@extends('layouts.user')

@section('title', $exam->title . ' - Exam Portal')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Back -->
    <a href="{{ route('user.exams.index') }}" class="inline-flex items-center text-ios-gray hover:text-gray-900 transition-colors">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Exams
    </a>

    <!-- Exam Card -->
    <div class="ios-card overflow-hidden">
        <div class="p-8 text-center border-b border-ios-gray-5">
            <div class="w-20 h-20 mx-auto bg-gradient-to-br from-ios-blue to-ios-purple rounded-3xl flex items-center justify-center mb-4">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $exam->title }}</h1>
            <p class="text-ios-gray">{{ $exam->description ?? 'No description available' }}</p>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 divide-x divide-ios-gray-5 border-b border-ios-gray-5">
            <div class="p-4 text-center">
                <p class="text-2xl font-bold text-gray-900">{{ $exam->questions->count() }}</p>
                <p class="text-sm text-ios-gray">Questions</p>
            </div>
            <div class="p-4 text-center">
                <p class="text-2xl font-bold text-gray-900">{{ $exam->duration_minutes }}</p>
                <p class="text-sm text-ios-gray">Minutes</p>
            </div>
            <div class="p-4 text-center">
                <p class="text-2xl font-bold text-gray-900">{{ $exam->total_marks }}</p>
                <p class="text-sm text-ios-gray">Total Marks</p>
            </div>
            <div class="p-4 text-center">
                <p class="text-2xl font-bold text-gray-900">{{ $exam->passing_marks }}</p>
                <p class="text-sm text-ios-gray">Pass Marks</p>
            </div>
        </div>

        <div class="p-6 space-y-4">
            <!-- Attempt Info -->
            <div class="flex items-center justify-between p-4 bg-ios-gray-6 rounded-xl">
                <div>
                    <p class="font-medium text-gray-900">Attempts</p>
                    <p class="text-sm text-ios-gray">{{ $attemptCount }} of {{ $exam->max_attempts }} used</p>
                </div>
                @if($canAttempt)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-ios-green/10 text-ios-green">
                        Available
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-ios-red/10 text-ios-red">
                        No attempts left
                    </span>
                @endif
            </div>

            <!-- Requirements -->
            @if($exam->face_verification_required)
                <div class="flex items-center space-x-3 p-4 bg-ios-orange/10 rounded-xl">
                    <svg class="w-6 h-6 text-ios-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                    </svg>
                    <div>
                        <p class="font-medium text-ios-orange">Face Verification Required</p>
                        <p class="text-sm text-ios-orange/80">You'll need to verify your identity before starting</p>
                    </div>
                </div>
            @endif

            <!-- Last Attempt -->
            @if($lastAttempt && $lastAttempt->status === 'completed')
                <div class="flex items-center justify-between p-4 bg-ios-gray-6 rounded-xl">
                    <div>
                        <p class="font-medium text-gray-900">Last Attempt</p>
                        <p class="text-sm text-ios-gray">{{ $lastAttempt->completed_at->diffForHumans() }}</p>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $lastAttempt->passed ? 'bg-ios-green/10 text-ios-green' : 'bg-ios-red/10 text-ios-red' }}">
                        {{ $lastAttempt->percentage }}%
                    </span>
                </div>
            @endif

            <!-- Start Button -->
            @if($canAttempt)
                <a href="{{ route('user.exams.verify', $exam) }}" class="block w-full ios-btn bg-ios-blue text-white hover:bg-blue-600 text-center">
                    Start Exam
                </a>
            @else
                <button disabled class="block w-full ios-btn bg-ios-gray-4 text-white cursor-not-allowed">
                    No Attempts Remaining
                </button>
            @endif
        </div>
    </div>

    <!-- Instructions -->
    <div class="ios-card p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Instructions</h2>
        <ul class="space-y-3 text-ios-gray">
            <li class="flex items-start space-x-3">
                <svg class="w-5 h-5 text-ios-blue flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Ensure you have a stable internet connection</span>
            </li>
            <li class="flex items-start space-x-3">
                <svg class="w-5 h-5 text-ios-blue flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Do not switch tabs or leave the exam window</span>
            </li>
            <li class="flex items-start space-x-3">
                <svg class="w-5 h-5 text-ios-blue flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Answers are auto-saved as you progress</span>
            </li>
            <li class="flex items-start space-x-3">
                <svg class="w-5 h-5 text-ios-blue flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>The exam will auto-submit when time runs out</span>
            </li>
        </ul>
    </div>
</div>
@endsection
