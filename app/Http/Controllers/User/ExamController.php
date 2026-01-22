<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Answer;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\FaceCapture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExamController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Get user's attempt counts per exam
        $userAttemptCounts = $user->examAttempts()
            ->selectRaw('exam_id, COUNT(*) as attempt_count')
            ->groupBy('exam_id')
            ->pluck('attempt_count', 'exam_id')
            ->toArray();

        $query = Exam::where('status', 'published')
            ->where(function ($q) {
                $q->whereNull('start_time')->orWhere('start_time', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_time')->orWhere('end_time', '>=', now());
            })
            ->withCount('questions');

        if ($request->search) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        $allExams = $query->latest()->get();
        
        // Filter out exams where user has reached max attempts
        $filteredExams = $allExams->filter(function ($exam) use ($userAttemptCounts) {
            $attemptCount = $userAttemptCounts[$exam->id] ?? 0;
            return $attemptCount < $exam->max_attempts;
        });

        // Manual pagination for filtered collection
        $page = $request->get('page', 1);
        $perPage = 12;
        $exams = new \Illuminate\Pagination\LengthAwarePaginator(
            $filteredExams->forPage($page, $perPage),
            $filteredExams->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('user.exams.index', compact('exams'));
    }

    public function show(Exam $exam)
    {
        $user = auth()->user();
        $attemptCount = $exam->userAttemptCount($user);
        $canAttempt = $exam->canUserAttempt($user);
        $lastAttempt = $user->examAttempts()->where('exam_id', $exam->id)->latest()->first();

        return view('user.exams.show', compact('exam', 'attemptCount', 'canAttempt', 'lastAttempt'));
    }

    public function startVerification(Exam $exam)
    {
        if (!$exam->canUserAttempt(auth()->user())) {
            return redirect()->route('user.exams.show', $exam)->with('error', 'You cannot attempt this exam.');
        }

        return view('user.exams.verify', compact('exam'));
    }

    public function start(Request $request, Exam $exam)
    {
        $user = auth()->user();

        if (!$exam->canUserAttempt($user)) {
            return response()->json(['error' => 'You cannot attempt this exam.'], 403);
        }

        // Validate face capture and liveness verification if required
        if ($exam->face_verification_required) {
            $request->validate([
                'face_image' => 'required|string',
                'liveness_verified' => 'required|boolean',
                'blink_count' => 'required|integer|min:0'
            ]);
            
            // Check if liveness was verified (eye blink detection)
            if (!$request->liveness_verified || $request->blink_count < 3) {
                return response()->json([
                    'error' => 'Liveness verification failed. Please complete the eye blink verification.'
                ], 422);
            }
            
            $this->saveFaceCapture($user, $request->face_image, 'exam_start', null, [
                'liveness_verified' => true,
                'blink_count' => $request->blink_count
            ]);
        }

        // Create exam attempt
        $attempt = ExamAttempt::create([
            'user_id' => $user->id,
            'exam_id' => $exam->id,
            'started_at' => now(),
            'status' => 'in_progress',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        ActivityLog::log('exam_started', "Started exam: {$exam->title} (Liveness verified)", $attempt);

        return response()->json([
            'success' => true,
            'redirect' => route('user.exams.attempt', $attempt),
        ]);
    }

    public function attempt(ExamAttempt $attempt)
    {
        if ($attempt->user_id !== auth()->id()) {
            abort(403);
        }

        if (!$attempt->isInProgress()) {
            return redirect()->route('user.exams.result', $attempt);
        }

        $exam = $attempt->exam;
        $questions = $exam->shuffle_questions 
            ? $exam->questions->shuffle() 
            : $exam->questions;

        $answers = $attempt->answers->keyBy('question_id');
        $remainingTime = $attempt->getRemainingTime();

        if ($remainingTime <= 0) {
            $this->submitExam($attempt);
            return redirect()->route('user.exams.result', $attempt);
        }

        return view('user.exams.attempt', compact('attempt', 'exam', 'questions', 'answers', 'remainingTime'));
    }

    public function saveAnswer(Request $request, ExamAttempt $attempt)
    {
        if ($attempt->user_id !== auth()->id() || !$attempt->isInProgress()) {
            return response()->json(['error' => 'Invalid attempt'], 403);
        }

        $request->validate([
            'question_id' => 'required|exists:questions,id',
            'answer' => 'nullable|string',
        ]);

        $question = $attempt->exam->questions()->findOrFail($request->question_id);

        Answer::updateOrCreate(
            [
                'exam_attempt_id' => $attempt->id,
                'question_id' => $request->question_id,
            ],
            [
                'answer' => $request->answer,
                'is_correct' => $question->isMcq() ? $question->checkAnswer($request->answer ?? '') : null,
                'marks_obtained' => $question->isMcq() && $question->checkAnswer($request->answer ?? '') ? $question->marks : 0,
            ]
        );

        return response()->json(['success' => true]);
    }

    public function submit(Request $request, ExamAttempt $attempt)
    {
        if ($attempt->user_id !== auth()->id() || !$attempt->isInProgress()) {
            return response()->json(['error' => 'Invalid attempt'], 403);
        }

        $this->submitExam($attempt);

        ActivityLog::log('exam_submitted', "Submitted exam: {$attempt->exam->title}", $attempt);

        return response()->json([
            'success' => true,
            'redirect' => route('user.exams.result', $attempt),
        ]);
    }

    public function result(ExamAttempt $attempt)
    {
        if ($attempt->user_id !== auth()->id()) {
            abort(403);
        }

        if ($attempt->isInProgress()) {
            return redirect()->route('user.exams.attempt', $attempt);
        }

        $attempt->load(['exam', 'answers.question']);
        
        // Check if exam exists
        if (!$attempt->exam) {
            return redirect()->route('user.dashboard')->with('error', 'Exam not found.');
        }

        return view('user.exams.result', compact('attempt'));
    }

    public function history(Request $request)
    {
        $query = auth()->user()->examAttempts()->with('exam');
        
        // Apply filters
        if ($request->filter === 'passed') {
            $query->where('passed', true);
        } elseif ($request->filter === 'failed') {
            $query->where('passed', false)->where('status', 'completed');
        } elseif ($request->filter === 'completed') {
            $query->where('status', 'completed');
        } elseif ($request->filter === 'in_progress') {
            $query->where('status', 'in_progress');
        }
        
        $attempts = $query->latest()->paginate(15);
        $filter = $request->filter;

        return view('user.exams.history', compact('attempts', 'filter'));
    }

    public function logTabSwitch(Request $request, ExamAttempt $attempt)
    {
        if ($attempt->user_id !== auth()->id() || !$attempt->isInProgress()) {
            return response()->json(['error' => 'Invalid attempt'], 403);
        }

        $attempt->increment('tab_switch_count');

        ActivityLog::log('tab_switch', "Tab switch detected during exam", $attempt);

        return response()->json(['success' => true, 'count' => $attempt->tab_switch_count]);
    }

    public function logProctoring(Request $request, ExamAttempt $attempt)
    {
        if ($attempt->user_id !== auth()->id() || !$attempt->isInProgress()) {
            return response()->json(['error' => 'Invalid attempt'], 403);
        }

        $request->validate([
            'event_type' => 'required|string|in:gaze_warning,no_face_warning,multiple_faces',
            'data' => 'nullable|array',
        ]);

        ActivityLog::log('proctoring_' . $request->event_type, "Proctoring event: {$request->event_type}", $attempt);

        return response()->json(['success' => true]);
    }

    protected function submitExam(ExamAttempt $attempt)
    {
        $attempt->calculateScore();
        $attempt->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    protected function saveFaceCapture($user, $imageData, $type, $attemptId, $metadata = [])
    {
        $image = str_replace('data:image/png;base64,', '', $imageData);
        $image = str_replace('data:image/jpeg;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        
        $imageName = 'face_captures/' . $user->id . '/' . $type . '_' . time() . '.png';
        Storage::disk('public')->put($imageName, base64_decode($image));

        FaceCapture::create([
            'user_id' => $user->id,
            'exam_attempt_id' => $attemptId,
            'capture_type' => $type,
            'image_path' => $imageName,
            'liveness_verified' => $metadata['liveness_verified'] ?? false,
            'blink_count' => $metadata['blink_count'] ?? null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => !empty($metadata) ? $metadata : null,
        ]);
    }
}
