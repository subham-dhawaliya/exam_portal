@extends('layouts.admin')

@section('title', 'Activity Logs - Admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Activity Logs</h1>
        <p class="text-ios-gray mt-1">System activity and audit trail</p>
    </div>

    <!-- Filters -->
    <div class="ios-card p-4">
        <form method="GET" class="flex flex-col sm:flex-row gap-4">
            <input type="text" name="action" value="{{ request('action') }}" placeholder="Search action..." class="ios-input flex-1">
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="ios-input sm:w-40">
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="ios-input sm:w-40">
            <button type="submit" class="ios-btn bg-ios-gray-6 text-gray-700 hover:bg-ios-gray-5">Filter</button>
        </form>
    </div>

    <!-- Logs List -->
    <div class="ios-card overflow-hidden">
        <div class="divide-y divide-ios-gray-5">
            @forelse($logs as $log)
                <div class="p-4 flex items-start space-x-4 hover:bg-ios-gray-6/50 transition-colors">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0
                        {{ str_contains($log->action, 'login') ? 'bg-ios-green/10 text-ios-green' : '' }}
                        {{ str_contains($log->action, 'logout') ? 'bg-ios-orange/10 text-ios-orange' : '' }}
                        {{ str_contains($log->action, 'created') ? 'bg-ios-blue/10 text-ios-blue' : '' }}
                        {{ str_contains($log->action, 'updated') ? 'bg-ios-purple/10 text-ios-purple' : '' }}
                        {{ str_contains($log->action, 'deleted') ? 'bg-ios-red/10 text-ios-red' : '' }}
                        {{ !str_contains($log->action, 'login') && !str_contains($log->action, 'logout') && !str_contains($log->action, 'created') && !str_contains($log->action, 'updated') && !str_contains($log->action, 'deleted') ? 'bg-ios-gray/10 text-ios-gray' : '' }}">
                        @if(str_contains($log->action, 'login'))
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                        @elseif(str_contains($log->action, 'logout'))
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        @else
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center space-x-2">
                            <span class="font-medium text-gray-900">{{ $log->user->name ?? 'System' }}</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-ios-gray-6 text-gray-600">
                                {{ str_replace('_', ' ', $log->action) }}
                            </span>
                        </div>
                        <p class="text-sm text-ios-gray mt-1">{{ $log->description }}</p>
                        <div class="flex items-center space-x-4 mt-2 text-xs text-ios-gray">
                            <span>{{ $log->created_at->format('M d, Y H:i:s') }}</span>
                            @if($log->ip_address)
                                <span>IP: {{ $log->ip_address }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center text-ios-gray">
                    No activity logs found
                </div>
            @endforelse
        </div>
    </div>

    <!-- Pagination -->
    <div class="flex justify-center">
        {{ $logs->links() }}
    </div>
</div>
@endsection
