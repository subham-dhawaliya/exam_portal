<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Exam;
use App\Models\Question;
use App\Models\QuestionBank;
use App\Models\Section;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index(Exam $exam)
    {
        $questions = $exam->questions()->orderBy('order')->get();
        return view('admin.questions.index', compact('exam', 'questions'));
    }

    public function create(Exam $exam)
    {
        return view('admin.questions.create', compact('exam'));
    }

    public function store(Request $request, Exam $exam)
    {
        $request->validate([
            'type' => 'required|in:mcq,descriptive',
            'question' => 'required|string',
            'marks' => 'required|integer|min:1',
            'options' => 'required_if:type,mcq|array|min:2',
            'options.*' => 'required_if:type,mcq|string',
            'correct_answer' => 'required_if:type,mcq|string',
            'explanation' => 'nullable|string',
        ]);

        $maxOrder = $exam->questions()->max('order') ?? 0;

        $question = $exam->questions()->create([
            'type' => $request->type,
            'question' => $request->question,
            'options' => $request->type === 'mcq' ? $request->options : null,
            'correct_answer' => $request->correct_answer,
            'marks' => $request->marks,
            'order' => $maxOrder + 1,
            'explanation' => $request->explanation,
        ]);

        ActivityLog::log('question_created', "Added question to exam: {$exam->title}", $question);

        if ($request->add_another) {
            return redirect()->route('admin.exams.questions.create', $exam)->with('success', 'Question added. Add another.');
        }

        return redirect()->route('admin.exams.questions.index', $exam)->with('success', 'Question added successfully.');
    }

    public function edit(Exam $exam, Question $question)
    {
        return view('admin.questions.edit', compact('exam', 'question'));
    }

    public function update(Request $request, Exam $exam, Question $question)
    {
        $request->validate([
            'type' => 'required|in:mcq,descriptive',
            'question' => 'required|string',
            'marks' => 'required|integer|min:1',
            'options' => 'required_if:type,mcq|array|min:2',
            'options.*' => 'required_if:type,mcq|string',
            'correct_answer' => 'required_if:type,mcq|string',
            'explanation' => 'nullable|string',
        ]);

        $question->update([
            'type' => $request->type,
            'question' => $request->question,
            'options' => $request->type === 'mcq' ? $request->options : null,
            'correct_answer' => $request->correct_answer,
            'marks' => $request->marks,
            'explanation' => $request->explanation,
        ]);

        ActivityLog::log('question_updated', "Updated question in exam: {$exam->title}", $question);

        return redirect()->route('admin.exams.questions.index', $exam)->with('success', 'Question updated successfully.');
    }

    public function destroy(Exam $exam, Question $question)
    {
        ActivityLog::log('question_deleted', "Deleted question from exam: {$exam->title}", $question);
        $question->delete();

        return redirect()->route('admin.exams.questions.index', $exam)->with('success', 'Question deleted successfully.');
    }

    public function reorder(Request $request, Exam $exam)
    {
        $request->validate([
            'questions' => 'required|array',
            'questions.*' => 'exists:questions,id',
        ]);

        foreach ($request->questions as $index => $questionId) {
            Question::where('id', $questionId)->update(['order' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Show question bank import page
     */
    public function importFromBank(Exam $exam, Request $request)
    {
        $query = QuestionBank::with('section')->where('is_active', true);

        // Filters
        if ($request->filled('section')) {
            $query->where('section_id', $request->section);
        }
        if ($request->filled('type')) {
            $query->where('question_type', $request->type);
        }
        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }
        if ($request->filled('search')) {
            $query->where('question_text', 'like', '%' . $request->search . '%');
        }

        $questions = $query->latest()->paginate(20)->withQueryString();
        $sections = Section::active()->ordered()->get();
        $questionTypes = QuestionBank::getQuestionTypes();
        $difficulties = QuestionBank::getDifficultyLevels();

        // Get already added question bank IDs
        $addedBankIds = $exam->questionBankLinks()->pluck('question_bank_id')->toArray();

        return view('admin.questions.import-from-bank', compact(
            'exam', 'questions', 'sections', 'questionTypes', 'difficulties', 'addedBankIds'
        ));
    }

    /**
     * Import selected questions from bank to exam
     */
    public function storeFromBank(Request $request, Exam $exam)
    {
        $request->validate([
            'question_ids' => 'required|array|min:1',
            'question_ids.*' => 'exists:question_bank,id',
        ]);

        $maxOrder = $exam->questions()->max('order') ?? 0;
        $imported = 0;

        foreach ($request->question_ids as $bankId) {
            $bankQuestion = QuestionBank::with(['options', 'answers', 'matchPairs', 'orderingItems'])->find($bankId);
            
            if (!$bankQuestion) continue;

            // Check if already linked
            if ($exam->questionBankLinks()->where('question_bank_id', $bankId)->exists()) {
                continue;
            }

            // Convert question bank to exam question
            $questionData = $this->convertBankToExamQuestion($bankQuestion);
            $questionData['order'] = ++$maxOrder;

            $question = $exam->questions()->create($questionData);

            // Link to question bank for reference
            $exam->questionBankLinks()->create([
                'question_bank_id' => $bankId,
                'order' => $maxOrder,
                'marks_override' => null,
            ]);

            $imported++;
        }

        ActivityLog::log('questions_imported', "Imported {$imported} questions from bank to exam: {$exam->title}", $exam);

        return redirect()->route('admin.exams.questions.index', $exam)
            ->with('success', "{$imported} questions imported successfully!");
    }

    /**
     * Convert question bank format to exam question format
     */
    protected function convertBankToExamQuestion(QuestionBank $bankQuestion): array
    {
        $type = 'mcq'; // Default
        $options = null;
        $correctAnswer = null;

        switch ($bankQuestion->question_type) {
            case 'mcq':
                $type = 'mcq';
                $options = [];
                foreach ($bankQuestion->options as $opt) {
                    $key = chr(65 + $opt->order); // A, B, C, D...
                    $options[$key] = $opt->option_text;
                    if ($opt->is_correct) {
                        $correctAnswer = $key;
                    }
                }
                break;

            case 'multiple_select':
                $type = 'mcq'; // Treat as MCQ for now
                $options = [];
                $correctAnswers = [];
                foreach ($bankQuestion->options as $opt) {
                    $key = chr(65 + $opt->order);
                    $options[$key] = $opt->option_text;
                    if ($opt->is_correct) {
                        $correctAnswers[] = $key;
                    }
                }
                $correctAnswer = implode(',', $correctAnswers);
                break;

            case 'true_false':
                $type = 'mcq';
                $options = ['A' => 'True', 'B' => 'False'];
                $trueOption = $bankQuestion->options->firstWhere('is_correct', true);
                $correctAnswer = $trueOption && $trueOption->option_text === 'True' ? 'A' : 'B';
                break;

            case 'fill_blank':
            case 'short_answer':
            case 'numerical':
                $type = 'descriptive';
                $answer = $bankQuestion->answers->first();
                $correctAnswer = $answer ? $answer->answer_text : '';
                break;

            default:
                $type = 'descriptive';
                $correctAnswer = '';
        }

        return [
            'type' => $type,
            'question' => $bankQuestion->question_text,
            'options' => $options,
            'correct_answer' => $correctAnswer,
            'marks' => (int) $bankQuestion->marks,
            'explanation' => $bankQuestion->explanation,
        ];
    }
}
