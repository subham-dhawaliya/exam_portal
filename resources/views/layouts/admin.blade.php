@extends('layouts.app')

@section('body')
<div class="min-h-full flex">
    <!-- Sidebar -->
    <aside class="hidden lg:flex lg:flex-col lg:w-72 lg:fixed lg:inset-y-0 bg-white/80 backdrop-blur-xl border-r border-white/50 z-30">
        <div class="flex flex-col flex-1 min-h-0">
            <!-- Logo -->
            <div class="flex items-center h-20 px-6">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-ios-blue via-ios-purple to-ios-pink rounded-2xl flex items-center justify-center shadow-glow-blue">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-xl font-bold gradient-text">Exam Portal</span>
                        <p class="text-xs text-ios-gray">Admin Panel</p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3.5 rounded-2xl {{ request()->routeIs('admin.dashboard') ? 'bg-gradient-to-r from-ios-blue to-ios-indigo text-white shadow-glow-blue' : 'text-gray-600 hover:bg-ios-gray-6' }} transition-all duration-200 group">
                    <div class="{{ request()->routeIs('admin.dashboard') ? 'bg-white/20' : 'bg-ios-blue/10 group-hover:bg-ios-blue/20' }} w-10 h-10 rounded-xl flex items-center justify-center mr-3 transition-colors">
                        <svg class="w-5 h-5 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-ios-blue' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                    </div>
                    <span class="font-medium">Dashboard</span>
                </a>

                <a href="{{ route('admin.users.index') }}" class="flex items-center px-4 py-3.5 rounded-2xl {{ request()->routeIs('admin.users.*') ? 'bg-gradient-to-r from-ios-purple to-ios-pink text-white shadow-glow-purple' : 'text-gray-600 hover:bg-ios-gray-6' }} transition-all duration-200 group">
                    <div class="{{ request()->routeIs('admin.users.*') ? 'bg-white/20' : 'bg-ios-purple/10 group-hover:bg-ios-purple/20' }} w-10 h-10 rounded-xl flex items-center justify-center mr-3 transition-colors">
                        <svg class="w-5 h-5 {{ request()->routeIs('admin.users.*') ? 'text-white' : 'text-ios-purple' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <span class="font-medium">Users</span>
                </a>

                <a href="{{ route('admin.exams.index') }}" class="flex items-center px-4 py-3.5 rounded-2xl {{ request()->routeIs('admin.exams.*') || request()->routeIs('admin.*.questions.*') ? 'bg-gradient-to-r from-ios-green to-ios-teal text-white shadow-glow-green' : 'text-gray-600 hover:bg-ios-gray-6' }} transition-all duration-200 group">
                    <div class="{{ request()->routeIs('admin.exams.*') ? 'bg-white/20' : 'bg-ios-green/10 group-hover:bg-ios-green/20' }} w-10 h-10 rounded-xl flex items-center justify-center mr-3 transition-colors">
                        <svg class="w-5 h-5 {{ request()->routeIs('admin.exams.*') ? 'text-white' : 'text-ios-green' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                    </div>
                    <span class="font-medium">Exams</span>
                </a>

                <a href="{{ route('admin.question-bank.index') }}" class="flex items-center px-4 py-3.5 rounded-2xl {{ request()->routeIs('admin.question-bank.*') || request()->routeIs('admin.sections.*') ? 'bg-gradient-to-r from-ios-blue to-ios-indigo text-white' : 'text-gray-600 hover:bg-ios-gray-6' }} transition-all duration-200 group">
                    <div class="{{ request()->routeIs('admin.question-bank.*') || request()->routeIs('admin.sections.*') ? 'bg-white/20' : 'bg-ios-blue/10 group-hover:bg-ios-blue/20' }} w-10 h-10 rounded-xl flex items-center justify-center mr-3 transition-colors">
                        <svg class="w-5 h-5 {{ request()->routeIs('admin.question-bank.*') || request()->routeIs('admin.sections.*') ? 'text-white' : 'text-ios-blue' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="font-medium">Question Bank</span>
                </a>

                <a href="{{ route('admin.face-captures.index') }}" class="flex items-center px-4 py-3.5 rounded-2xl {{ request()->routeIs('admin.face-captures.*') ? 'bg-gradient-to-r from-ios-orange to-ios-yellow text-white' : 'text-gray-600 hover:bg-ios-gray-6' }} transition-all duration-200 group">
                    <div class="{{ request()->routeIs('admin.face-captures.*') ? 'bg-white/20' : 'bg-ios-orange/10 group-hover:bg-ios-orange/20' }} w-10 h-10 rounded-xl flex items-center justify-center mr-3 transition-colors">
                        <svg class="w-5 h-5 {{ request()->routeIs('admin.face-captures.*') ? 'text-white' : 'text-ios-orange' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <span class="font-medium">Face Captures</span>
                </a>

                <a href="{{ route('admin.face-verification-logs.index') }}" class="flex items-center px-4 py-3.5 rounded-2xl {{ request()->routeIs('admin.face-verification-logs.*') ? 'bg-gradient-to-r from-ios-teal to-ios-green text-white' : 'text-gray-600 hover:bg-ios-gray-6' }} transition-all duration-200 group">
                    <div class="{{ request()->routeIs('admin.face-verification-logs.*') ? 'bg-white/20' : 'bg-ios-teal/10 group-hover:bg-ios-teal/20' }} w-10 h-10 rounded-xl flex items-center justify-center mr-3 transition-colors">
                        <svg class="w-5 h-5 {{ request()->routeIs('admin.face-verification-logs.*') ? 'text-white' : 'text-ios-teal' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <span class="font-medium">Verification Logs</span>
                </a>

                <a href="{{ route('admin.activity-logs.index') }}" class="flex items-center px-4 py-3.5 rounded-2xl {{ request()->routeIs('admin.activity-logs.*') ? 'bg-gradient-to-r from-ios-indigo to-ios-purple text-white' : 'text-gray-600 hover:bg-ios-gray-6' }} transition-all duration-200 group">
                    <div class="{{ request()->routeIs('admin.activity-logs.*') ? 'bg-white/20' : 'bg-ios-indigo/10 group-hover:bg-ios-indigo/20' }} w-10 h-10 rounded-xl flex items-center justify-center mr-3 transition-colors">
                        <svg class="w-5 h-5 {{ request()->routeIs('admin.activity-logs.*') ? 'text-white' : 'text-ios-indigo' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <span class="font-medium">Activity Logs</span>
                </a>
            </nav>

            <!-- User Info -->
            <div class="p-4 border-t border-ios-gray-5">
                <div class="flex items-center space-x-3 p-4 rounded-2xl bg-gradient-to-r from-ios-gray-6 to-white">
                    <div class="w-12 h-12 bg-gradient-to-br from-ios-purple to-ios-pink rounded-2xl flex items-center justify-center text-white font-bold text-lg shadow-lg">
                        {{ substr(Auth::guard('admin')->user()->name, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-900 truncate">{{ Auth::guard('admin')->user()->name }}</p>
                        <p class="text-xs text-ios-gray">Administrator</p>
                    </div>
                    <form action="{{ route('admin.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="p-2.5 text-ios-gray hover:text-ios-red hover:bg-ios-red/10 rounded-xl transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    <!-- Mobile Header -->
    <div class="lg:hidden fixed top-0 left-0 right-0 z-40 bg-white/80 backdrop-blur-xl border-b border-white/50">
        <div class="flex items-center justify-between h-16 px-4">
            <span class="text-lg font-bold gradient-text">Exam Portal</span>
            <button id="mobile-menu-btn" class="p-2 rounded-xl hover:bg-ios-gray-6 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 lg:pl-72">
        <div class="py-6 lg:py-8 px-4 lg:px-8 mt-16 lg:mt-0">
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
