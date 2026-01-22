<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $stats = [
            'total_exams_taken' => $user->examAttempts()->count(),
            'completed_exams' => $user->examAttempts()->where('status', 'completed')->count(),
            'passed_exams' => $user->examAttempts()->where('passed', true)->count(),
            'average_score' => $user->examAttempts()->where('status', 'completed')->avg('percentage') ?? 0,
        ];

        // Get user's attempt counts per exam
        $userAttemptCounts = $user->examAttempts()
            ->selectRaw('exam_id, COUNT(*) as attempt_count')
            ->groupBy('exam_id')
            ->pluck('attempt_count', 'exam_id')
            ->toArray();

        $availableExams = Exam::where('status', 'published')
            ->where(function ($q) {
                $q->whereNull('start_time')->orWhere('start_time', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_time')->orWhere('end_time', '>=', now());
            })
            ->withCount('questions')
            ->latest()
            ->get()
            ->filter(function ($exam) use ($userAttemptCounts) {
                // Filter out exams where user has reached max attempts
                $attemptCount = $userAttemptCounts[$exam->id] ?? 0;
                return $attemptCount < $exam->max_attempts;
            })
            ->take(6);

        $recentAttempts = $user->examAttempts()
            ->with('exam')
            ->latest()
            ->take(5)
            ->get();

        $inProgressAttempt = $user->examAttempts()
            ->with('exam')
            ->where('status', 'in_progress')
            ->first();

        return view('user.dashboard', compact('stats', 'availableExams', 'recentAttempts', 'inProgressAttempt'));
    }
}
