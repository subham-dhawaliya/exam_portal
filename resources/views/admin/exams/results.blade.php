@extends('layouts.admin')

@section('title', 'Exam Results - Admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center space-x-4">
        <a href="{{ route('admin.exams.show', $exam) }}" class="p-2 hover:bg-ios-gray-6 rounded-xl transition-colors">
            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $exam->title }} - Results</h1>
            <p class="text-ios-gray mt-1">{{ $attempts->total() }} completed attempts</p>
        </div>
    </div>

    <!-- Results Table -->
    <div class="ios-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-ios-gray-6">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Rank</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Student</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Score</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Percentage</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Completed</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ios-gray-5">
                    @forelse($attempts as $index => $attempt)
                        <tr class="hover:bg-ios-gray-6/50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full {{ $index < 3 ? 'bg-ios-yellow text-white' : 'bg-ios-gray-6 text-gray-600' }} font-semibold text-sm">
                                    {{ ($attempts->currentPage() - 1) * $attempts->perPage() + $index + 1 }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-ios-blue to-ios-purple rounded-full flex items-center justify-center text-white font-semibold">
                                        {{ substr($attempt->user->name ?? 'U', 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $attempt->user->name ?? 'Unknown' }}</p>
                                        <p class="text-sm text-ios-gray">{{ $attempt->user->email ?? '' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-semibold text-gray-900">{{ $attempt->score }}/{{ $attempt->total_marks }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <div class="w-24 h-2 bg-ios-gray-5 rounded-full overflow-hidden">
                                        <div class="h-full {{ $attempt->passed ? 'bg-ios-green' : 'bg-ios-red' }} rounded-full" style="width: {{ $attempt->percentage }}%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">{{ $attempt->percentage }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $attempt->passed ? 'bg-ios-green/10 text-ios-green' : 'bg-ios-red/10 text-ios-red' }}">
                                    {{ $attempt->passed ? 'Passed' : 'Failed' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $attempt->completed_at->format('M d, Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-ios-gray">No completed attempts</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="flex justify-center">
        {{ $attempts->links() }}
    </div>
</div>
@endsection
