@extends('layouts.admin')

@section('title', 'User Details - Admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.users.index') }}" class="p-2 hover:bg-ios-gray-6 rounded-xl transition-colors">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
                <p class="text-ios-gray mt-1">{{ $user->email }}</p>
            </div>
        </div>
        <a href="{{ route('admin.users.edit', $user) }}" class="ios-btn bg-ios-blue text-white hover:bg-blue-600">
            Edit User
        </a>
    </div>

    <!-- User Info Card -->
    <div class="ios-card p-6">
        <div class="flex items-start space-x-6">
            <div class="w-20 h-20 bg-gradient-to-br from-ios-blue to-ios-purple rounded-2xl flex items-center justify-center text-white text-2xl font-bold">
                {{ substr($user->name, 0, 1) }}
            </div>
            <div class="flex-1 grid grid-cols-2 lg:grid-cols-4 gap-6">
                <div>
                    <p class="text-sm text-ios-gray">Status</p>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-1
                        {{ $user->status === 'active' ? 'bg-ios-green/10 text-ios-green' : '' }}
                        {{ $user->status === 'inactive' ? 'bg-ios-gray/10 text-ios-gray' : '' }}
                        {{ $user->status === 'suspended' ? 'bg-ios-red/10 text-ios-red' : '' }}">
                        {{ ucfirst($user->status) }}
                    </span>
                </div>
                <div>
                    <p class="text-sm text-ios-gray">Phone</p>
                    <p class="font-medium text-gray-900 mt-1">{{ $user->phone ?? 'Not provided' }}</p>
                </div>
                <div>
                    <p class="text-sm text-ios-gray">Joined</p>
                    <p class="font-medium text-gray-900 mt-1">{{ $user->created_at->format('M d, Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-ios-gray">Last Login</p>
                    <p class="font-medium text-gray-900 mt-1">{{ $user->last_login_at?->diffForHumans() ?? 'Never' }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        <!-- Exam Attempts -->
        <div class="ios-card">
            <div class="p-6 border-b border-ios-gray-5">
                <h2 class="text-lg font-semibold text-gray-900">Exam Attempts</h2>
            </div>
            <div class="divide-y divide-ios-gray-5 max-h-96 overflow-y-auto">
                @forelse($user->examAttempts as $attempt)
                    <div class="p-4 flex items-center justify-between">
                        <div>
                            <p class="font-medium text-gray-900">{{ $attempt->exam->title ?? 'Unknown' }}</p>
                            <p class="text-sm text-ios-gray">{{ $attempt->created_at->format('M d, Y H:i') }}</p>
                        </div>
                        <div class="text-right">
                            @if($attempt->status === 'completed')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $attempt->passed ? 'bg-ios-green/10 text-ios-green' : 'bg-ios-red/10 text-ios-red' }}">
                                    {{ $attempt->percentage }}%
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-ios-orange/10 text-ios-orange">
                                    {{ ucfirst($attempt->status) }}
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-ios-gray">No exam attempts</div>
                @endforelse
            </div>
        </div>

        <!-- Face Captures -->
        <div class="ios-card">
            <div class="p-6 border-b border-ios-gray-5">
                <h2 class="text-lg font-semibold text-gray-900">Face Captures</h2>
            </div>
            <div class="divide-y divide-ios-gray-5 max-h-96 overflow-y-auto">
                @forelse($user->faceCaptures->take(10) as $capture)
                    <div class="p-4 flex items-center space-x-4">
                        <img src="{{ url('/api/face-image/' . $capture->id) }}" alt="Face capture" class="w-12 h-12 rounded-xl object-cover bg-ios-gray-6">
                        <div class="flex-1">
                            <p class="font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $capture->capture_type)) }}</p>
                            <p class="text-sm text-ios-gray">{{ $capture->created_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-ios-gray">No face captures</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
