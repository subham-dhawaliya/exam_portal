<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuestionBank;
use App\Models\QuestionOption;
use App\Models\QuestionAnswer;
use App\Models\QuestionMatchPair;
use App\Models\QuestionOrderingItem;
use App\Models\Section;
use App\Services\AIQuestionGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class QuestionBankController extends Controller
{
    protected AIQuestionGeneratorService $aiService;

    public function __construct(AIQuestionGeneratorService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function index(Request $request)
    {
        $query = QuestionBank::with('section');

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
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }
        if ($request->filled('search')) {
            $query->where('question_text', 'like', '%' . $request->search . '%');
        }

        $questions = $query->latest()->paginate(20)->withQueryString();
        $sections = Section::ordered()->get();
        $questionTypes = QuestionBank::getQuestionTypes();
        $difficulties = QuestionBank::getDifficultyLevels();

        return view('admin.question-bank.index', compact(
            'questions', 'sections', 'questionTypes', 'difficulties'
        ));
    }

    public function create()
    {
        $sections = Section::active()->ordered()->get();
        $questionTypes = QuestionBank::getQuestionTypes();
        $difficulties = QuestionBank::getDifficultyLevels();

        return view('admin.question-bank.create', compact(
            'sections', 'questionTypes', 'difficulties'
        ));
    }

    public function store(Request $request)
    {
        $validated = $this->validateQuestion($request);

        DB::beginTransaction();
        try {
            // Handle image upload
            if ($request->hasFile('question_image')) {
                $validated['question_image'] = $request->file('question_image')
                    ->store('question_images', 'public');
            }

            $validated['created_by'] = auth('admin')->id() ?? auth()->id();
            $validated['tags'] = $request->tags ? array_map('trim', explode(',', $request->tags)) : null;

            $question = QuestionBank::create($validated);

            // Save type-specific data
            $this->saveQuestionTypeData($question, $request);

            DB::commit();

            return redirect()->route('admin.question-bank.index')
                ->with('success', 'Question created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create question: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(QuestionBank $questionBank)
    {
        $questionBank->load(['section', 'options', 'answers', 'matchPairs', 'orderingItems']);
        
        return view('admin.question-bank.show', compact('questionBank'));
    }

    public function edit(QuestionBank $questionBank)
    {
        $questionBank->load(['options', 'answers', 'matchPairs', 'orderingItems']);
        $sections = Section::active()->ordered()->get();
        $questionTypes = QuestionBank::getQuestionTypes();
        $difficulties = QuestionBank::getDifficultyLevels();

        return view('admin.question-bank.edit', compact(
            'questionBank', 'sections', 'questionTypes', 'difficulties'
        ));
    }

    public function update(Request $request, QuestionBank $questionBank)
    {
        $validated = $this->validateQuestion($request, $questionBank->id);

        DB::beginTransaction();
        try {
            // Handle image upload
            if ($request->hasFile('question_image')) {
                // Delete old image
                if ($questionBank->question_image) {
                    Storage::disk('public')->delete($questionBank->question_image);
                }
                $validated['question_image'] = $request->file('question_image')
                    ->store('question_images', 'public');
            }

            $validated['tags'] = $request->tags ? array_map('trim', explode(',', $request->tags)) : null;

            $questionBank->update($validated);

            // Clear old type-specific data and save new
            $questionBank->options()->delete();
            $questionBank->answers()->delete();
            $questionBank->matchPairs()->delete();
            $questionBank->orderingItems()->delete();

            $this->saveQuestionTypeData($questionBank, $request);

            DB::commit();

            return redirect()->route('admin.question-bank.index')
                ->with('success', 'Question updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update question: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(QuestionBank $questionBank)
    {
        if ($questionBank->question_image) {
            Storage::disk('public')->delete($questionBank->question_image);
        }

        $questionBank->delete();

        return redirect()->route('admin.question-bank.index')
            ->with('success', 'Question deleted successfully!');
    }

    public function duplicate(QuestionBank $questionBank)
    {
        DB::beginTransaction();
        try {
            $newQuestion = $questionBank->replicate();
            $newQuestion->question_text = '[COPY] ' . $newQuestion->question_text;
            $newQuestion->save();

            // Duplicate options
            foreach ($questionBank->options as $option) {
                $newOption = $option->replicate();
                $newOption->question_bank_id = $newQuestion->id;
                $newOption->save();
            }

            // Duplicate answers
            foreach ($questionBank->answers as $answer) {
                $newAnswer = $answer->replicate();
                $newAnswer->question_bank_id = $newQuestion->id;
                $newAnswer->save();
            }

            // Duplicate match pairs
            foreach ($questionBank->matchPairs as $pair) {
                $newPair = $pair->replicate();
                $newPair->question_bank_id = $newQuestion->id;
                $newPair->save();
            }

            // Duplicate ordering items
            foreach ($questionBank->orderingItems as $item) {
                $newItem = $item->replicate();
                $newItem->question_bank_id = $newQuestion->id;
                $newItem->save();
            }

            DB::commit();

            return redirect()->route('admin.question-bank.edit', $newQuestion)
                ->with('success', 'Question duplicated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to duplicate question: ' . $e->getMessage());
        }
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:question_bank,id',
        ]);

        $questions = QuestionBank::whereIn('id', $request->ids)->get();
        
        foreach ($questions as $question) {
            if ($question->question_image) {
                Storage::disk('public')->delete($question->question_image);
            }
            $question->delete();
        }

        return response()->json([
            'success' => true,
            'message' => count($request->ids) . ' questions deleted successfully!',
        ]);
    }

    public function toggleStatus(QuestionBank $questionBank)
    {
        $questionBank->update(['is_active' => !$questionBank->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $questionBank->is_active,
        ]);
    }

    public function preview(QuestionBank $questionBank)
    {
        $questionBank->load(['options', 'answers', 'matchPairs', 'orderingItems']);
        
        return view('admin.question-bank.preview', compact('questionBank'));
    }

    protected function validateQuestion(Request $request, $id = null)
    {
        return $request->validate([
            'section_id' => 'required|exists:sections,id',
            'question_type' => 'required|in:' . implode(',', array_keys(QuestionBank::getQuestionTypes())),
            'question_text' => 'required|string',
            'question_image' => 'nullable|image|max:2048',
            'difficulty' => 'required|in:easy,medium,hard',
            'marks' => 'required|numeric|min:0',
            'negative_marks' => 'nullable|numeric|min:0',
            'time_limit' => 'nullable|integer|min:0',
            'explanation' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
    }

    protected function saveQuestionTypeData(QuestionBank $question, Request $request)
    {
        switch ($question->question_type) {
            case QuestionBank::TYPE_MCQ:
            case QuestionBank::TYPE_MULTIPLE_SELECT:
                $this->saveOptions($question, $request);
                break;

            case QuestionBank::TYPE_TRUE_FALSE:
                $this->saveTrueFalseOptions($question, $request);
                break;

            case QuestionBank::TYPE_FILL_BLANK:
            case QuestionBank::TYPE_SHORT_ANSWER:
                $this->saveTextAnswers($question, $request);
                break;

            case QuestionBank::TYPE_NUMERICAL:
                $this->saveNumericalAnswer($question, $request);
                break;

            case QuestionBank::TYPE_MATCH_FOLLOWING:
                $this->saveMatchPairs($question, $request);
                break;

            case QuestionBank::TYPE_ORDERING:
                $this->saveOrderingItems($question, $request);
                break;
        }
    }

    protected function saveOptions(QuestionBank $question, Request $request)
    {
        $options = $request->input('options', []);
        $correctOptions = $request->input('correct_options', []);

        foreach ($options as $index => $optionText) {
            if (empty(trim($optionText))) continue;

            QuestionOption::create([
                'question_bank_id' => $question->id,
                'option_text' => $optionText,
                'is_correct' => in_array($index, $correctOptions),
                'order' => $index,
            ]);
        }
    }

    protected function saveTrueFalseOptions(QuestionBank $question, Request $request)
    {
        $correctAnswer = $request->input('correct_answer', 'true');

        QuestionOption::create([
            'question_bank_id' => $question->id,
            'option_text' => 'True',
            'is_correct' => $correctAnswer === 'true',
            'order' => 0,
        ]);

        QuestionOption::create([
            'question_bank_id' => $question->id,
            'option_text' => 'False',
            'is_correct' => $correctAnswer === 'false',
            'order' => 1,
        ]);
    }

    protected function saveTextAnswers(QuestionBank $question, Request $request)
    {
        $answers = $request->input('answers', []);
        $caseSensitive = $request->boolean('case_sensitive', false);
        $partialMatch = $request->boolean('partial_match', false);

        foreach ($answers as $index => $answerText) {
            if (empty(trim($answerText))) continue;

            QuestionAnswer::create([
                'question_bank_id' => $question->id,
                'answer_text' => $answerText,
                'is_case_sensitive' => $caseSensitive,
                'allow_partial_match' => $partialMatch,
                'order' => $index,
            ]);
        }
    }

    protected function saveNumericalAnswer(QuestionBank $question, Request $request)
    {
        QuestionAnswer::create([
            'question_bank_id' => $question->id,
            'answer_text' => $request->input('numerical_answer'),
            'tolerance' => $request->input('tolerance', 0),
            'order' => 0,
        ]);
    }

    protected function saveMatchPairs(QuestionBank $question, Request $request)
    {
        $leftSides = $request->input('left_sides', []);
        $rightSides = $request->input('right_sides', []);

        foreach ($leftSides as $index => $leftText) {
            if (empty(trim($leftText)) || empty(trim($rightSides[$index] ?? ''))) continue;

            QuestionMatchPair::create([
                'question_bank_id' => $question->id,
                'left_side' => $leftText,
                'right_side' => $rightSides[$index],
                'order' => $index,
            ]);
        }
    }

    protected function saveOrderingItems(QuestionBank $question, Request $request)
    {
        $items = $request->input('ordering_items', []);

        foreach ($items as $index => $itemText) {
            if (empty(trim($itemText))) continue;

            QuestionOrderingItem::create([
                'question_bank_id' => $question->id,
                'item_text' => $itemText,
                'correct_position' => $index + 1,
            ]);
        }
    }

    // ==================== AI QUESTION GENERATION ====================

    /**
     * Show AI question generator form
     */
    public function aiGenerateForm()
    {
        if (!$this->aiService->isConfigured()) {
            return redirect()->route('admin.question-bank.index')
                ->with('error', 'AI service is not configured. Please add AI_API_KEY to your .env file.');
        }

        $sections = Section::active()->ordered()->get();
        $questionTypes = QuestionBank::getQuestionTypes();
        $difficulties = QuestionBank::getDifficultyLevels();
        $provider = $this->aiService->getProvider();

        return view('admin.question-bank.ai-generate', compact(
            'sections', 'questionTypes', 'difficulties', 'provider'
        ));
    }

    /**
     * Generate questions using AI
     */
    public function aiGenerate(Request $request)
    {
        $request->validate([
            'section_id' => 'required|exists:sections,id',
            'question_type' => 'required|in:' . implode(',', array_keys(QuestionBank::getQuestionTypes())),
            'difficulty' => 'required|in:easy,medium,hard',
            'topic' => 'required|string|max:500',
            'count' => 'required|integer|min:1|max:20',
            'instructions' => 'nullable|string|max:1000',
        ]);

        try {
            $questions = $this->aiService->generateQuestions([
                'topic' => $request->topic,
                'question_type' => $request->question_type,
                'difficulty' => $request->difficulty,
                'count' => $request->count,
                'instructions' => $request->instructions,
            ]);

            // Store in session for review
            session(['ai_generated_questions' => $questions]);
            session(['ai_generation_params' => $request->only(['section_id', 'question_type', 'difficulty', 'topic'])]);

            return response()->json([
                'success' => true,
                'questions' => $questions,
                'count' => count($questions),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Save AI generated questions to database
     */
    public function aiSave(Request $request)
    {
        $request->validate([
            'questions' => 'required|array',
            'questions.*.question_text' => 'required|string',
            'section_id' => 'required|exists:sections,id',
            'difficulty' => 'required|in:easy,medium,hard',
            'marks' => 'required|numeric|min:0',
            'negative_marks' => 'nullable|numeric|min:0',
        ]);

        $savedCount = 0;

        DB::beginTransaction();
        try {
            foreach ($request->questions as $q) {
                if (empty($q['selected'] ?? true)) continue; // Skip unselected

                $question = QuestionBank::create([
                    'section_id' => $request->section_id,
                    'question_type' => $q['question_type'],
                    'question_text' => $q['question_text'],
                    'difficulty' => $request->difficulty,
                    'marks' => $request->marks,
                    'negative_marks' => $request->negative_marks ?? 0,
                    'explanation' => $q['explanation'] ?? null,
                    'is_active' => true,
                    'created_by' => auth('admin')->id() ?? auth()->id(),
                    'tags' => ['ai-generated'],
                ]);

                // Save type-specific data
                $this->saveAIQuestionData($question, $q);
                $savedCount++;
            }

            DB::commit();

            // Clear session
            session()->forget(['ai_generated_questions', 'ai_generation_params']);

            return response()->json([
                'success' => true,
                'message' => "{$savedCount} questions saved successfully!",
                'redirect' => route('admin.question-bank.index'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to save questions: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Save AI generated question type-specific data
     */
    protected function saveAIQuestionData(QuestionBank $question, array $data)
    {
        switch ($question->question_type) {
            case 'mcq':
                foreach ($data['options'] ?? [] as $index => $optionText) {
                    QuestionOption::create([
                        'question_bank_id' => $question->id,
                        'option_text' => $optionText,
                        'is_correct' => $index === ($data['correct_index'] ?? 0),
                        'order' => $index,
                    ]);
                }
                break;

            case 'multiple_select':
                $correctIndices = $data['correct_indices'] ?? [];
                foreach ($data['options'] ?? [] as $index => $optionText) {
                    QuestionOption::create([
                        'question_bank_id' => $question->id,
                        'option_text' => $optionText,
                        'is_correct' => in_array($index, $correctIndices),
                        'order' => $index,
                    ]);
                }
                break;

            case 'true_false':
                $correct = $data['correct_answer'] ?? true;
                QuestionOption::create([
                    'question_bank_id' => $question->id,
                    'option_text' => 'True',
                    'is_correct' => $correct === true,
                    'order' => 0,
                ]);
                QuestionOption::create([
                    'question_bank_id' => $question->id,
                    'option_text' => 'False',
                    'is_correct' => $correct === false,
                    'order' => 1,
                ]);
                break;

            case 'fill_blank':
            case 'short_answer':
                foreach ($data['answers'] ?? [] as $index => $answer) {
                    QuestionAnswer::create([
                        'question_bank_id' => $question->id,
                        'answer_text' => $answer,
                        'is_case_sensitive' => false,
                        'allow_partial_match' => $question->question_type === 'short_answer',
                        'order' => $index,
                    ]);
                }
                break;

            case 'numerical':
                QuestionAnswer::create([
                    'question_bank_id' => $question->id,
                    'answer_text' => $data['answer'] ?? 0,
                    'tolerance' => $data['tolerance'] ?? 0,
                    'order' => 0,
                ]);
                break;

            case 'match_following':
                foreach ($data['pairs'] ?? [] as $index => $pair) {
                    QuestionMatchPair::create([
                        'question_bank_id' => $question->id,
                        'left_side' => $pair['left'] ?? '',
                        'right_side' => $pair['right'] ?? '',
                        'order' => $index,
                    ]);
                }
                break;

            case 'ordering':
                foreach ($data['items'] ?? [] as $index => $item) {
                    QuestionOrderingItem::create([
                        'question_bank_id' => $question->id,
                        'item_text' => $item,
                        'correct_position' => $index + 1,
                    ]);
                }
                break;
        }
    }
}
