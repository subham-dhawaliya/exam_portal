<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::whereHas('role', fn($q) => $q->where('slug', 'user'))->count(),
            'active_users' => User::where('status', 'active')->whereHas('role', fn($q) => $q->where('slug', 'user'))->count(),
            'total_exams' => Exam::count(),
            'active_exams' => Exam::where('status', 'published')->count(),
            'total_attempts' => ExamAttempt::count(),
            'completed_attempts' => ExamAttempt::where('status', 'completed')->count(),
        ];

        $recentAttempts = ExamAttempt::with(['user', 'exam'])
            ->latest()
            ->take(10)
            ->get();

        $recentUsers = User::with('role')
            ->whereHas('role', fn($q) => $q->where('slug', 'user'))
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentAttempts', 'recentUsers'));
    }
}
