@extends('layouts.user')

@section('title', 'Exam History - Exam Portal')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Exam History</h1>
            <p class="text-ios-gray mt-1">View all your past exam attempts</p>
        </div>
        
        <!-- Filter Tabs -->
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('user.exams.history') }}" 
               class="px-4 py-2 rounded-xl text-sm font-medium transition-all {{ !isset($filter) || !$filter ? 'bg-ios-blue text-white' : 'bg-ios-gray-6 text-gray-700 hover:bg-ios-gray-5' }}">
                All
            </a>
            <a href="{{ route('user.exams.history', ['filter' => 'completed']) }}" 
               class="px-4 py-2 rounded-xl text-sm font-medium transition-all {{ ($filter ?? '') === 'completed' ? 'bg-ios-orange text-white' : 'bg-ios-gray-6 text-gray-700 hover:bg-ios-gray-5' }}">
                Completed
            </a>
            <a href="{{ route('user.exams.history', ['filter' => 'passed']) }}" 
               class="px-4 py-2 rounded-xl text-sm font-medium transition-all {{ ($filter ?? '') === 'passed' ? 'bg-ios-green text-white' : 'bg-ios-gray-6 text-gray-700 hover:bg-ios-gray-5' }}">
                Passed
            </a>
            <a href="{{ route('user.exams.history', ['filter' => 'failed']) }}" 
               class="px-4 py-2 rounded-xl text-sm font-medium transition-all {{ ($filter ?? '') === 'failed' ? 'bg-ios-red text-white' : 'bg-ios-gray-6 text-gray-700 hover:bg-ios-gray-5' }}">
                Failed
            </a>
            <a href="{{ route('user.exams.history', ['filter' => 'in_progress']) }}" 
               class="px-4 py-2 rounded-xl text-sm font-medium transition-all {{ ($filter ?? '') === 'in_progress' ? 'bg-ios-purple text-white' : 'bg-ios-gray-6 text-gray-700 hover:bg-ios-gray-5' }}">
                In Progress
            </a>
        </div>
    </div>

    <!-- History List -->
    <div class="ios-card divide-y divide-ios-gray-5">
        @forelse($attempts as $attempt)
            <a href="{{ route('user.exams.result', $attempt) }}" class="p-4 flex items-center justify-between hover:bg-ios-gray-6/50 transition-colors">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0
                        {{ $attempt->status === 'completed' && $attempt->passed ? 'bg-ios-green/10' : '' }}
                        {{ $attempt->status === 'completed' && !$attempt->passed ? 'bg-ios-red/10' : '' }}
                        {{ $attempt->status !== 'completed' ? 'bg-ios-orange/10' : '' }}">
                        @if($attempt->status === 'completed' && $attempt->passed)
                            <svg class="w-6 h-6 text-ios-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        @elseif($attempt->status === 'completed')
                            <svg class="w-6 h-6 text-ios-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        @else
                            <svg class="w-6 h-6 text-ios-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        @endif
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">{{ $attempt->exam->title ?? 'Unknown Exam' }}</p>
                        <p class="text-sm text-ios-gray">{{ $attempt->started_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    @if($attempt->status === 'completed')
                        <div class="text-right">
                            <p class="text-lg font-bold {{ $attempt->passed ? 'text-ios-green' : 'text-ios-red' }}">{{ $attempt->percentage }}%</p>
                            <p class="text-xs text-ios-gray">{{ $attempt->score }}/{{ $attempt->total_marks }}</p>
                        </div>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-ios-orange/10 text-ios-orange">
                            {{ ucfirst(str_replace('_', ' ', $attempt->status)) }}
                        </span>
                    @endif
                    <svg class="w-5 h-5 text-ios-gray-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </a>
        @empty
            <div class="p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-ios-gray-3 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-ios-gray mb-4">No exam attempts yet</p>
                <a href="{{ route('user.exams.index') }}" class="ios-btn bg-ios-blue text-white hover:bg-blue-600">Browse Exams</a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="flex justify-center">
        {{ $attempts->appends(['filter' => $filter ?? null])->links() }}
    </div>
</div>
@endsection
