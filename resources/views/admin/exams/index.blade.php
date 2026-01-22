@extends('layouts.admin')

@section('title', 'Exams - Admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Exams</h1>
            <p class="text-ios-gray mt-1">Create and manage exams</p>
        </div>
        <a href="{{ route('admin.exams.create') }}" class="ios-btn bg-ios-blue text-white hover:bg-blue-600 inline-flex items-center justify-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Create Exam
        </a>
    </div>

    <!-- Filters -->
    <div class="ios-card p-4">
        <form method="GET" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search exams..." class="ios-input">
            </div>
            <select name="status" class="ios-input sm:w-40">
                <option value="">All Status</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
            </select>
            <button type="submit" class="ios-btn bg-ios-gray-6 text-gray-700 hover:bg-ios-gray-5">Filter</button>
        </form>
    </div>

    <!-- Exams Grid -->
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($exams as $exam)
            <div class="ios-card overflow-hidden slide-in">
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-ios-purple to-ios-pink rounded-2xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $exam->status === 'published' ? 'bg-ios-green/10 text-ios-green' : '' }}
                            {{ $exam->status === 'draft' ? 'bg-ios-orange/10 text-ios-orange' : '' }}
                            {{ $exam->status === 'completed' ? 'bg-ios-blue/10 text-ios-blue' : '' }}
                            {{ $exam->status === 'archived' ? 'bg-ios-gray/10 text-ios-gray' : '' }}">
                            {{ ucfirst($exam->status) }}
                        </span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $exam->title }}</h3>
                    <p class="text-sm text-ios-gray mb-4 line-clamp-2">{{ $exam->description ?? 'No description' }}</p>
                    
                    <div class="grid grid-cols-3 gap-4 text-center py-4 border-t border-b border-ios-gray-5">
                        <div>
                            <p class="text-lg font-bold text-gray-900">{{ $exam->questions_count }}</p>
                            <p class="text-xs text-ios-gray">Questions</p>
                        </div>
                        <div>
                            <p class="text-lg font-bold text-gray-900">{{ $exam->duration_minutes }}</p>
                            <p class="text-xs text-ios-gray">Minutes</p>
                        </div>
                        <div>
                            <p class="text-lg font-bold text-gray-900">{{ $exam->attempts_count }}</p>
                            <p class="text-xs text-ios-gray">Attempts</p>
                        </div>
                    </div>
                </div>
                
                <div class="px-6 py-4 bg-ios-gray-6/50 flex items-center justify-between">
                    <a href="{{ route('admin.exams.questions.index', $exam) }}" class="text-sm font-medium text-ios-blue hover:underline">
                        Manage Questions
                    </a>
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('admin.exams.results', $exam) }}" class="p-2 text-ios-gray hover:text-ios-green hover:bg-white rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </a>
                        <a href="{{ route('admin.exams.edit', $exam) }}" class="p-2 text-ios-gray hover:text-ios-orange hover:bg-white rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full ios-card p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-ios-gray-3 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p class="text-ios-gray mb-4">No exams found</p>
                <a href="{{ route('admin.exams.create') }}" class="ios-btn bg-ios-blue text-white hover:bg-blue-600">Create First Exam</a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="flex justify-center">
        {{ $exams->links() }}
    </div>
</div>
@endsection
