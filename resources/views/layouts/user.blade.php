@extends('layouts.app')

@section('body')
<div class="min-h-full flex flex-col">
    <!-- Header -->
    <header class="fixed top-0 left-0 right-0 z-40 bg-white/80 backdrop-blur-xl border-b border-white/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="{{ route('user.dashboard') }}" class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-ios-blue via-ios-purple to-ios-pink rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="text-lg font-bold gradient-text hidden sm:block">Exam Portal</span>
                </a>

                <nav class="hidden md:flex items-center space-x-1">
                    <a href="{{ route('user.dashboard') }}" class="px-4 py-2 rounded-xl text-sm font-medium {{ request()->routeIs('user.dashboard') ? 'bg-gradient-to-r from-ios-blue to-ios-indigo text-white shadow-lg' : 'text-gray-600 hover:bg-ios-gray-6' }} transition-all duration-200">
                        Dashboard
                    </a>
                    <a href="{{ route('user.exams.index') }}" class="px-4 py-2 rounded-xl text-sm font-medium {{ request()->routeIs('user.exams.index') || request()->routeIs('user.exams.show') ? 'bg-gradient-to-r from-ios-blue to-ios-indigo text-white shadow-lg' : 'text-gray-600 hover:bg-ios-gray-6' }} transition-all duration-200">
                        Exams
                    </a>
                    <a href="{{ route('user.exams.history') }}" class="px-4 py-2 rounded-xl text-sm font-medium {{ request()->routeIs('user.exams.history') ? 'bg-gradient-to-r from-ios-blue to-ios-indigo text-white shadow-lg' : 'text-gray-600 hover:bg-ios-gray-6' }} transition-all duration-200">
                        History
                    </a>
                </nav>

                <div class="flex items-center space-x-3">
                    <a href="{{ route('user.profile.show') }}" class="flex items-center space-x-2 p-2 rounded-xl hover:bg-ios-gray-6 transition-all duration-200">
                        <div class="w-9 h-9 bg-gradient-to-br from-ios-orange to-ios-pink rounded-xl flex items-center justify-center text-white text-sm font-bold shadow-lg">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <span class="text-sm font-medium text-gray-700 hidden sm:block">{{ auth()->user()->name }}</span>
                    </a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="p-2.5 text-ios-gray hover:text-ios-red hover:bg-ios-red/10 rounded-xl transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Mobile Bottom Navigation -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/90 backdrop-blur-xl border-t border-white/50 safe-area-inset-bottom">
        <div class="flex items-center justify-around h-20 pb-2">
            <a href="{{ route('user.dashboard') }}" class="flex flex-col items-center justify-center flex-1 py-2 {{ request()->routeIs('user.dashboard') ? 'text-ios-blue' : 'text-ios-gray' }} transition-colors">
                <div class="{{ request()->routeIs('user.dashboard') ? 'bg-ios-blue/10' : '' }} p-2 rounded-xl transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
                <span class="text-xs font-medium mt-1">Home</span>
            </a>
            <a href="{{ route('user.exams.index') }}" class="flex flex-col items-center justify-center flex-1 py-2 {{ request()->routeIs('user.exams.index') ? 'text-ios-blue' : 'text-ios-gray' }} transition-colors">
                <div class="{{ request()->routeIs('user.exams.index') ? 'bg-ios-blue/10' : '' }} p-2 rounded-xl transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <span class="text-xs font-medium mt-1">Exams</span>
            </a>
            <a href="{{ route('user.exams.history') }}" class="flex flex-col items-center justify-center flex-1 py-2 {{ request()->routeIs('user.exams.history') ? 'text-ios-blue' : 'text-ios-gray' }} transition-colors">
                <div class="{{ request()->routeIs('user.exams.history') ? 'bg-ios-blue/10' : '' }} p-2 rounded-xl transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-xs font-medium mt-1">History</span>
            </a>
            <a href="{{ route('user.profile.show') }}" class="flex flex-col items-center justify-center flex-1 py-2 {{ request()->routeIs('user.profile.*') ? 'text-ios-blue' : 'text-ios-gray' }} transition-colors">
                <div class="{{ request()->routeIs('user.profile.*') ? 'bg-ios-blue/10' : '' }} p-2 rounded-xl transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <span class="text-xs font-medium mt-1">Profile</span>
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-1 pt-20 pb-24 md:pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            @if(session('success'))
                <div class="mb-6 p-4 bg-ios-green/10 border border-ios-green/20 rounded-2xl flex items-center space-x-3 scale-in">
                    <div class="w-10 h-10 bg-ios-green/20 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-ios-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <span class="text-ios-green font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-ios-red/10 border border-ios-red/20 rounded-2xl flex items-center space-x-3 scale-in">
                    <div class="w-10 h-10 bg-ios-red/20 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-ios-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <span class="text-ios-red font-medium">{{ session('error') }}</span>
                </div>
            @endif

            @yield('content')
        </div>
    </main>
</div>
@endsection
