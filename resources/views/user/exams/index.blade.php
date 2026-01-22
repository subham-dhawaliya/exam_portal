@extends('layouts.user')

@section('title', 'Available Exams - Exam Portal')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Available Exams</h1>
        <p class="text-ios-gray mt-1">Choose an exam to start</p>
    </div>

    <!-- Search -->
    <div class="ios-card p-4">
        <form method="GET" class="flex gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search exams..." class="ios-input flex-1">
            <button type="submit" class="ios-btn bg-ios-blue text-white hover:bg-blue-600">Search</button>
        </form>
    </div>

    <!-- Exams Grid -->
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($exams as $exam)
            <a href="{{ route('user.exams.show', $exam) }}" class="ios-card overflow-hidden hover:shadow-ios-lg transition-all duration-200 slide-in">
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-ios-blue to-ios-purple rounded-2xl flex items-center justify-center">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        @if($exam->face_verification_required)
                            <span class="inline-flex items-center px-2 py-1 rounded-lg bg-ios-orange/10 text-ios-orange text-xs font-medium">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                </svg>
                                Face ID
                            </span>
                        @endif
                    </div>
                    
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $exam->title }}</h3>
                    <p class="text-sm text-ios-gray mb-4 line-clamp-2">{{ $exam->description ?? 'No description available' }}</p>
                    
                    <div class="grid grid-cols-3 gap-3 text-center py-4 border-t border-ios-gray-5">
                        <div>
                            <p class="text-lg font-bold text-gray-900">{{ $exam->questions_count }}</p>
                            <p class="text-xs text-ios-gray">Questions</p>
                        </div>
                        <div>
                            <p class="text-lg font-bold text-gray-900">{{ $exam->duration_minutes }}</p>
                            <p class="text-xs text-ios-gray">Minutes</p>
                        </div>
                        <div>
                            <p class="text-lg font-bold text-gray-900">{{ $exam->total_marks }}</p>
                            <p class="text-xs text-ios-gray">Marks</p>
                        </div>
                    </div>
                </div>
                
                <div class="px-6 py-4 bg-ios-gray-6/50 flex items-center justify-between">
                    <span class="text-sm text-ios-gray">Pass: {{ $exam->passing_marks }} marks</span>
                    <span class="text-ios-blue font-medium text-sm flex items-center">
                        Start Exam
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </span>
                </div>
            </a>
        @empty
            <div class="col-span-full ios-card p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-ios-gray-3 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p class="text-ios-gray">No exams available at the moment</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="flex justify-center">
        {{ $exams->links() }}
    </div>
</div>
@endsection
