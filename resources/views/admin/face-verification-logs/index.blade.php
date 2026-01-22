@extends('layouts.admin')

@section('title', 'Face Verification Logs')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Face Verification Logs</h1>
            <p class="text-ios-gray mt-1">Monitor all face verification attempts</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="glass-card p-4 text-center">
            <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['total']) }}</div>
            <div class="text-xs text-ios-gray">Total Attempts</div>
        </div>
        <div class="glass-card p-4 text-center">
            <div class="text-2xl font-bold text-ios-green">{{ number_format($stats['success']) }}</div>
            <div class="text-xs text-ios-gray">Successful</div>
        </div>
        <div class="glass-card p-4 text-center">
            <div class="text-2xl font-bold text-ios-orange">{{ number_format($stats['failed']) }}</div>
            <div class="text-xs text-ios-gray">Failed</div>
        </div>
        <div class="glass-card p-4 text-center">
            <div class="text-2xl font-bold text-ios-red">{{ number_format($stats['blocked']) }}</div>
            <div class="text-xs text-ios-gray">Blocked</div>
        </div>
        <div class="glass-card p-4 text-center">
            <div class="text-2xl font-bold text-ios-purple">{{ number_format($stats['spoof_detected']) }}</div>
            <div class="text-xs text-ios-gray">Spoof Detected</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="glass-card p-4">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <select name="status" class="ios-input text-sm">
                <option value="">All Status</option>
                <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Success</option>
                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                <option value="blocked" {{ request('status') == 'blocked' ? 'selected' : '' }}>Blocked</option>
                <option value="no_face" {{ request('status') == 'no_face' ? 'selected' : '' }}>No Face</option>
                <option value="spoof_detected" {{ request('status') == 'spoof_detected' ? 'selected' : '' }}>Spoof Detected</option>
                <option value="low_quality" {{ request('status') == 'low_quality' ? 'selected' : '' }}>Low Quality</option>
            </select>
            <select name="verification_type" class="ios-input text-sm">
                <option value="">All Types</option>
                <option value="login" {{ request('verification_type') == 'login' ? 'selected' : '' }}>Login</option>
                <option value="registration" {{ request('verification_type') == 'registration' ? 'selected' : '' }}>Registration</option>
                <option value="exam_start" {{ request('verification_type') == 'exam_start' ? 'selected' : '' }}>Exam Start</option>
                <option value="re_enrollment" {{ request('verification_type') == 're_enrollment' ? 'selected' : '' }}>Re-enrollment</option>
            </select>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="ios-input text-sm" placeholder="From Date">
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="ios-input text-sm" placeholder="To Date">
            <button type="submit" class="ios-btn bg-ios-blue text-white text-sm">Filter</button>
        </form>
    </div>

    <!-- Logs Table -->
    <div class="glass-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-ios-gray-6">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">User</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Match Score</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Liveness</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Quality</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">IP Address</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ios-gray-5">
                    @forelse($logs as $log)
                    <tr class="hover:bg-ios-gray-6/50 transition-colors">
                        <td class="px-4 py-3">
                            @if($log->user)
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-gradient-to-br from-ios-blue to-ios-purple rounded-full flex items-center justify-center text-white text-xs font-bold">
                                        {{ strtoupper(substr($log->user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900 text-sm">{{ $log->user->name }}</div>
                                        <div class="text-xs text-ios-gray">{{ $log->email }}</div>
                                    </div>
                                </div>
                            @else
                                <span class="text-ios-gray text-sm">{{ $log->email ?? 'Unknown' }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs rounded-full 
                                @if($log->verification_type == 'login') bg-ios-blue/10 text-ios-blue
                                @elseif($log->verification_type == 'registration') bg-ios-green/10 text-ios-green
                                @elseif($log->verification_type == 'exam_start') bg-ios-orange/10 text-ios-orange
                                @else bg-ios-purple/10 text-ios-purple @endif">
                                {{ ucfirst(str_replace('_', ' ', $log->verification_type)) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs rounded-full font-medium
                                @if($log->status == 'success') bg-ios-green/10 text-ios-green
                                @elseif($log->status == 'failed') bg-ios-orange/10 text-ios-orange
                                @elseif($log->status == 'blocked') bg-ios-red/10 text-ios-red
                                @elseif($log->status == 'spoof_detected') bg-ios-purple/10 text-ios-purple
                                @else bg-ios-gray/10 text-ios-gray @endif">
                                {{ ucfirst(str_replace('_', ' ', $log->status)) }}
                            </span>
                            @if($log->failure_reason)
                                <div class="text-xs text-ios-red mt-1">{{ Str::limit($log->failure_reason, 30) }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($log->match_score !== null)
                                <div class="flex items-center space-x-2">
                                    <div class="w-16 h-2 bg-ios-gray-5 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full @if($log->match_score >= 70) bg-ios-green @elseif($log->match_score >= 50) bg-ios-orange @else bg-ios-red @endif" 
                                             style="width: {{ $log->match_score }}%"></div>
                                    </div>
                                    <span class="text-xs font-medium">{{ number_format($log->match_score, 1) }}%</span>
                                </div>
                            @else
                                <span class="text-ios-gray text-xs">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($log->liveness_score !== null)
                                <span class="text-sm @if($log->liveness_score >= 60) text-ios-green @else text-ios-red @endif">
                                    {{ number_format($log->liveness_score, 0) }}%
                                </span>
                            @else
                                <span class="text-ios-gray text-xs">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($log->quality_score !== null)
                                <span class="text-sm @if($log->quality_score >= 50) text-ios-green @else text-ios-orange @endif">
                                    {{ number_format($log->quality_score, 0) }}%
                                </span>
                            @else
                                <span class="text-ios-gray text-xs">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-xs text-ios-gray font-mono">{{ $log->ip_address }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-sm text-gray-900">{{ $log->created_at->format('M d, Y') }}</div>
                            <div class="text-xs text-ios-gray">{{ $log->created_at->format('h:i A') }}</div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center">
                            <div class="w-16 h-16 mx-auto bg-ios-gray-6 rounded-2xl flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-ios-gray" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                            <p class="text-ios-gray">No verification logs found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($logs->hasPages())
        <div class="px-4 py-3 border-t border-ios-gray-5">
            {{ $logs->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
