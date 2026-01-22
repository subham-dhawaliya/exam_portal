@extends('layouts.user')

@section('title', 'Dashboard - Exam Portal')

@section('content')
<div class="space-y-8">
    <!-- Welcome -->
    <div class="slide-up">
        <h1 class="text-3xl font-bold text-gray-900">Welcome back! 👋</h1>
        <p class="text-ios-gray mt-1 text-lg">Ready to ace your exams, <span class="text-ios-blue font-medium">{{ auth()->user()->name }}</span>?</p>
    </div>

    <!-- In Progress Alert -->
    @if($inProgressAttempt)
        <div class="glass-card p-6 bg-gradient-to-r from-ios-orange/90 to-ios-red/90 text-white overflow-hidden relative slide-up">
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
            <div class="flex items-center justify-between relative z-10">
                <div class="flex items-center space-x-4">
                    <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-7 h-7 pulse-ring" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold text-lg">Exam in Progress</p>
                        <p class="text-white/80">{{ $inProgressAttempt->exam->title }}</p>
                    </div>
                </div>
                <a href="{{ route('user.exams.attempt', $inProgressAttempt) }}" class="ios-btn bg-white text-ios-orange hover:bg-white/90 shadow-xl">
                    Continue Exam →
                </a>
            </div>
        </div>
    @endif

    <!-- Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ route('user.exams.history') }}" class="glass-card p-5 slide-up group hover:scale-105 transition-transform cursor-pointer" style="animation-delay: 0.05s">
            <div class="flex items-center space-x-4">
                <div class="w-14 h-14 bg-gradient-to-br from-ios-blue to-ios-indigo rounded-2xl flex items-center justify-center shadow-glow-blue group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div>
                    <p class="text-3xl font-bold text-gray-900 group-hover:text-ios-blue transition-colors">{{ $stats['total_exams_taken'] }}</p>
                    <p class="text-sm text-ios-gray">Exams Taken</p>
                </div>
            </div>
        </a>

        <a href="{{ route('user.exams.history') }}?filter=passed" class="glass-card p-5 slide-up group hover:scale-105 transition-transform cursor-pointer" style="animation-delay: 0.1s">
            <div class="flex items-center space-x-4">
                <div class="w-14 h-14 bg-gradient-to-br from-ios-green to-ios-teal rounded-2xl flex items-center justify-center shadow-glow-green group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-3xl font-bold text-gray-900 group-hover:text-ios-green transition-colors">{{ $stats['passed_exams'] }}</p>
                    <p class="text-sm text-ios-gray">Passed</p>
                </div>
            </div>
        </a>

        <a href="{{ route('user.exams.history') }}" class="glass-card p-5 slide-up group hover:scale-105 transition-transform cursor-pointer" style="animation-delay: 0.15s">
            <div class="flex items-center space-x-4">
                <div class="w-14 h-14 bg-gradient-to-br from-ios-purple to-ios-pink rounded-2xl flex items-center justify-center shadow-glow-purple group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-3xl font-bold text-gray-900 group-hover:text-ios-purple transition-colors">{{ number_format($stats['average_score'], 0) }}%</p>
                    <p class="text-sm text-ios-gray">Avg Score</p>
                </div>
            </div>
        </a>

        <a href="{{ route('user.exams.history') }}?filter=completed" class="glass-card p-5 slide-up group hover:scale-105 transition-transform cursor-pointer" style="animation-delay: 0.2s">
            <div class="flex items-center space-x-4">
                <div class="w-14 h-14 bg-gradient-to-br from-ios-orange to-ios-yellow rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-3xl font-bold text-gray-900 group-hover:text-ios-orange transition-colors">{{ $stats['completed_exams'] }}</p>
                    <p class="text-sm text-ios-gray">Completed</p>
                </div>
            </div>
        </a>
    </div>

    <!-- Available Exams -->
    <div class="slide-up" style="animation-delay: 0.25s">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">Available Exams</h2>
            <a href="{{ route('user.exams.index') }}" class="text-ios-blue font-semibold hover:text-ios-indigo transition-colors flex items-center space-x-1">
                <span>View All</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse($availableExams as $exam)
                <a href="{{ route('user.exams.show', $exam) }}" class="glass-card p-6 group">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-ios-blue via-ios-purple to-ios-pink rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <svg class="w-5 h-5 text-ios-gray-3 group-hover:text-ios-blue group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 text-lg mb-2 group-hover:text-ios-blue transition-colors">{{ $exam->title }}</h3>
                    <div class="flex items-center space-x-4 text-sm text-ios-gray">
                        <span class="flex items-center space-x-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>{{ $exam->questions_count }} Q</span>
                        </span>
                        <span class="flex items-center space-x-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>{{ $exam->duration_minutes }} min</span>
                        </span>
                        <span class="flex items-center space-x-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                            </svg>
                            <span>{{ $exam->total_marks }}</span>
                        </span>
                    </div>
                </a>
            @empty
                <div class="col-span-full glass-card p-12 text-center">
                    <div class="w-20 h-20 mx-auto bg-ios-gray-6 rounded-3xl flex items-center justify-center mb-4">
                        <svg class="w-10 h-10 text-ios-gray-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <p class="text-ios-gray text-lg">No exams available right now</p>
                    <p class="text-ios-gray-2 text-sm mt-1">Check back later for new exams</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Recent Attempts -->
    @if($recentAttempts->count() > 0)
        <div class="slide-up" style="animation-delay: 0.3s">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900">Recent Attempts</h2>
                <a href="{{ route('user.exams.history') }}" class="text-ios-blue font-semibold hover:text-ios-indigo transition-colors flex items-center space-x-1">
                    <span>View All</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
            
            <div class="glass-card divide-y divide-ios-gray-5/50 overflow-hidden">
                @foreach($recentAttempts as $attempt)
                    <a href="{{ route('user.exams.result', $attempt) }}" class="p-5 flex items-center justify-between hover:bg-ios-gray-6/50 transition-colors block">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center shadow-lg
                                {{ $attempt->status === 'completed' && $attempt->passed ? 'bg-gradient-to-br from-ios-green to-ios-teal' : '' }}
                                {{ $attempt->status === 'completed' && !$attempt->passed ? 'bg-gradient-to-br from-ios-red to-ios-pink' : '' }}
                                {{ $attempt->status !== 'completed' ? 'bg-gradient-to-br from-ios-orange to-ios-yellow' : '' }}">
                                @if($attempt->status === 'completed' && $attempt->passed)
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                @elseif($attempt->status === 'completed')
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                @else
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                @endif
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ $attempt->exam->title ?? 'Unknown' }}</p>
                                <p class="text-sm text-ios-gray">{{ $attempt->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        @if($attempt->status === 'completed')
                            <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-bold {{ $attempt->passed ? 'bg-ios-green/10 text-ios-green' : 'bg-ios-red/10 text-ios-red' }}">
                                {{ $attempt->percentage }}%
                            </span>
                        @else
                            <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-bold bg-ios-orange/10 text-ios-orange">
                                {{ ucfirst($attempt->status) }}
                            </span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
