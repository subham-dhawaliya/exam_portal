@extends('layouts.admin')

@section('title', 'Admin Dashboard - Exam Portal')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="slide-up">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
        <p class="text-ios-gray mt-1">Welcome back, <span class="text-ios-blue font-medium">{{ Auth::guard('admin')->user()->name }}</span></p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
        <a href="{{ route('admin.users.index') }}" class="glass-card p-6 slide-up group hover:scale-105 transition-transform cursor-pointer" style="animation-delay: 0.05s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-ios-gray text-sm font-medium">Total Users</p>
                    <p class="text-4xl font-bold text-gray-900 mt-2 group-hover:text-ios-blue transition-colors">{{ $stats['total_users'] }}</p>
                    <p class="text-xs text-ios-green mt-2 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ $stats['active_users'] }} active
                    </p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-ios-blue to-ios-indigo rounded-2xl flex items-center justify-center shadow-glow-blue group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.exams.index') }}" class="glass-card p-6 slide-up group hover:scale-105 transition-transform cursor-pointer" style="animation-delay: 0.1s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-ios-gray text-sm font-medium">Total Exams</p>
                    <p class="text-4xl font-bold text-gray-900 mt-2 group-hover:text-ios-purple transition-colors">{{ $stats['total_exams'] }}</p>
                    <p class="text-xs text-ios-green mt-2 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ $stats['active_exams'] }} published
                    </p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-ios-purple to-ios-pink rounded-2xl flex items-center justify-center shadow-glow-purple group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.activity-logs.index') }}" class="glass-card p-6 slide-up group hover:scale-105 transition-transform cursor-pointer" style="animation-delay: 0.15s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-ios-gray text-sm font-medium">Exam Attempts</p>
                    <p class="text-4xl font-bold text-gray-900 mt-2 group-hover:text-ios-green transition-colors">{{ $stats['total_attempts'] }}</p>
                    <p class="text-xs text-ios-green mt-2 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ $stats['completed_attempts'] }} completed
                    </p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-ios-green to-ios-teal rounded-2xl flex items-center justify-center shadow-glow-green group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </a>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ route('admin.users.create') }}" class="glass-card p-5 flex items-center space-x-4 group slide-up" style="animation-delay: 0.2s">
            <div class="w-12 h-12 bg-gradient-to-br from-ios-blue to-ios-indigo rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
            </div>
            <div>
                <span class="font-semibold text-gray-900">Add User</span>
                <p class="text-xs text-ios-gray">Create new account</p>
            </div>
        </a>

        <a href="{{ route('admin.exams.create') }}" class="glass-card p-5 flex items-center space-x-4 group slide-up" style="animation-delay: 0.25s">
            <div class="w-12 h-12 bg-gradient-to-br from-ios-purple to-ios-pink rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
            </div>
            <div>
                <span class="font-semibold text-gray-900">Create Exam</span>
                <p class="text-xs text-ios-gray">New examination</p>
            </div>
        </a>

        <a href="{{ route('admin.face-captures.index') }}" class="glass-card p-5 flex items-center space-x-4 group slide-up" style="animation-delay: 0.3s">
            <div class="w-12 h-12 bg-gradient-to-br from-ios-orange to-ios-yellow rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                </svg>
            </div>
            <div>
                <span class="font-semibold text-gray-900">Face Logs</span>
                <p class="text-xs text-ios-gray">View captures</p>
            </div>
        </a>

        <a href="{{ route('admin.activity-logs.index') }}" class="glass-card p-5 flex items-center space-x-4 group slide-up" style="animation-delay: 0.35s">
            <div class="w-12 h-12 bg-gradient-to-br from-ios-green to-ios-teal rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div>
                <span class="font-semibold text-gray-900">Activity</span>
                <p class="text-xs text-ios-gray">Audit trail</p>
            </div>
        </a>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        <!-- Recent Attempts -->
        <div class="glass-card overflow-hidden slide-up" style="animation-delay: 0.4s">
            <div class="p-6 border-b border-ios-gray-5/50 flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-900">Recent Exam Attempts</h2>
                <a href="{{ route('admin.activity-logs.index') }}" class="text-ios-blue text-sm font-medium hover:text-ios-indigo transition-colors">View All →</a>
            </div>
            <div class="divide-y divide-ios-gray-5/50">
                @forelse($recentAttempts as $attempt)
                    <a href="{{ route('admin.users.show', $attempt->user_id) }}" class="p-4 flex items-center justify-between hover:bg-ios-gray-6/50 transition-colors block group cursor-pointer">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-ios-blue to-ios-purple rounded-2xl flex items-center justify-center text-white font-bold shadow-lg group-hover:scale-110 transition-transform">
                                {{ substr($attempt->user->name ?? 'U', 0, 1) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 group-hover:text-ios-blue transition-colors">{{ $attempt->user->name ?? 'Unknown' }}</p>
                                <p class="text-sm text-ios-gray">{{ $attempt->exam->title ?? 'Unknown Exam' }}</p>
                            </div>
                        </div>
                        <div class="text-right flex items-center space-x-3">
                            @if($attempt->status === 'completed')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $attempt->passed ? 'bg-ios-green/10 text-ios-green' : 'bg-ios-red/10 text-ios-red' }}">
                                    {{ $attempt->percentage }}%
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-ios-orange/10 text-ios-orange">
                                    {{ ucfirst($attempt->status) }}
                                </span>
                            @endif
                            <div class="text-right">
                                <p class="text-xs text-ios-gray">{{ $attempt->created_at->diffForHumans() }}</p>
                            </div>
                            <svg class="w-5 h-5 text-ios-gray-3 group-hover:text-ios-blue group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </a>
                @empty
                    <div class="p-8 text-center text-ios-gray">
                        <svg class="w-12 h-12 mx-auto text-ios-gray-3 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        No exam attempts yet
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Users -->
        <div class="glass-card overflow-hidden slide-up" style="animation-delay: 0.45s">
            <div class="p-6 border-b border-ios-gray-5/50">
                <h2 class="text-lg font-bold text-gray-900">Recent Users</h2>
            </div>
            <div class="divide-y divide-ios-gray-5/50">
                @forelse($recentUsers as $user)
                    <a href="{{ route('admin.users.show', $user) }}" class="p-4 flex items-center justify-between hover:bg-ios-gray-6/50 transition-colors block">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-ios-orange to-ios-pink rounded-2xl flex items-center justify-center text-white font-bold shadow-lg">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ $user->name }}</p>
                                <p class="text-sm text-ios-gray">{{ $user->email }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $user->status === 'active' ? 'bg-ios-green/10 text-ios-green' : 'bg-ios-red/10 text-ios-red' }}">
                                {{ ucfirst($user->status) }}
                            </span>
                            <p class="text-xs text-ios-gray mt-1">{{ $user->created_at->diffForHumans() }}</p>
                        </div>
                    </a>
                @empty
                    <div class="p-8 text-center text-ios-gray">
                        <svg class="w-12 h-12 mx-auto text-ios-gray-3 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        No users yet
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
