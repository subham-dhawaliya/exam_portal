@extends('layouts.admin')

@section('title', 'Exam Details - Admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.exams.index') }}" class="p-2 hover:bg-ios-gray-6 rounded-xl transition-colors">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $exam->title }}</h1>
                <p class="text-ios-gray mt-1">Created by {{ $exam->creator->name ?? 'Unknown' }}</p>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.exams.questions.index', $exam) }}" class="ios-btn bg-ios-purple text-white hover:bg-purple-600">
                Manage Questions
            </a>
            <a href="{{ route('admin.exams.edit', $exam) }}" class="ios-btn bg-ios-blue text-white hover:bg-blue-600">
                Edit Exam
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="ios-card p-4 text-center">
            <p class="text-2xl font-bold text-gray-900">{{ $exam->questions->count() }}</p>
            <p class="text-sm text-ios-gray">Questions</p>
        </div>
        <div class="ios-card p-4 text-center">
            <p class="text-2xl font-bold text-gray-900">{{ $exam->duration_minutes }}</p>
            <p class="text-sm text-ios-gray">Minutes</p>
        </div>
        <div class="ios-card p-4 text-center">
            <p class="text-2xl font-bold text-gray-900">{{ $exam->total_marks }}</p>
            <p class="text-sm text-ios-gray">Total Marks</p>
        </div>
        <div class="ios-card p-4 text-center">
            <p class="text-2xl font-bold text-gray-900">{{ $exam->passing_marks }}</p>
            <p class="text-sm text-ios-gray">Passing</p>
        </div>
        <div class="ios-card p-4 text-center">
            <p class="text-2xl font-bold text-gray-900">{{ $exam->attempts->count() }}</p>
            <p class="text-sm text-ios-gray">Attempts</p>
        </div>
    </div>

    <!-- Recent Attempts -->
    <div class="ios-card">
        <div class="p-6 border-b border-ios-gray-5 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Recent Attempts</h2>
            <a href="{{ route('admin.exams.results', $exam) }}" class="text-ios-blue text-sm font-medium hover:underline">View All</a>
        </div>
        <div class="divide-y divide-ios-gray-5">
            @forelse($exam->attempts->take(10) as $attempt)
                <div class="p-4 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-ios-blue to-ios-purple rounded-full flex items-center justify-center text-white font-semibold text-sm">
                            {{ substr($attempt->user->name ?? 'U', 0, 1) }}
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $attempt->user->name ?? 'Unknown' }}</p>
                            <p class="text-sm text-ios-gray">{{ $attempt->started_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        @if($attempt->status === 'completed')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $attempt->passed ? 'bg-ios-green/10 text-ios-green' : 'bg-ios-red/10 text-ios-red' }}">
                                {{ $attempt->score }}/{{ $attempt->total_marks }} ({{ $attempt->percentage }}%)
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-ios-orange/10 text-ios-orange">
                                {{ ucfirst($attempt->status) }}
                            </span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-ios-gray">No attempts yet</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
