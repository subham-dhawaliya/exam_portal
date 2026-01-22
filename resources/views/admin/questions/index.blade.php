@extends('layouts.admin')

@section('title', 'Questions - Admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.exams.index') }}" class="p-2 hover:bg-ios-gray-6 rounded-xl transition-colors">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $exam->title }}</h1>
                <p class="text-ios-gray mt-1">{{ $questions->count() }} questions • {{ $exam->total_marks }} total marks</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.exams.questions.import-from-bank', $exam) }}" class="ios-btn bg-ios-purple text-white hover:bg-purple-600 inline-flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Import from Bank
            </a>
            <a href="{{ route('admin.exams.questions.create', $exam) }}" class="ios-btn bg-ios-blue text-white hover:bg-blue-600 inline-flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Add Question
            </a>
        </div>
    </div>

    <!-- Questions List -->
    <div class="space-y-4">
        @forelse($questions as $index => $question)
            <div class="ios-card p-6 slide-in" style="animation-delay: {{ $index * 0.05 }}s">
                <div class="flex items-start justify-between">
                    <div class="flex items-start space-x-4 flex-1">
                        <div class="w-10 h-10 bg-ios-gray-6 rounded-xl flex items-center justify-center text-gray-600 font-semibold flex-shrink-0">
                            {{ $index + 1 }}
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $question->type === 'mcq' ? 'bg-ios-blue/10 text-ios-blue' : 'bg-ios-purple/10 text-ios-purple' }}">
                                    {{ strtoupper($question->type) }}
                                </span>
                                <span class="text-sm text-ios-gray">{{ $question->marks }} marks</span>
                            </div>
                            <p class="text-gray-900 font-medium mb-3">{{ $question->question }}</p>
                            
                            @if($question->isMcq() && $question->options)
                                <div class="grid grid-cols-2 gap-2">
                                    @foreach($question->options as $key => $option)
                                        <div class="flex items-center space-x-2 p-2 rounded-lg {{ $question->correct_answer === $key ? 'bg-ios-green/10' : 'bg-ios-gray-6' }}">
                                            <span class="w-6 h-6 rounded-full {{ $question->correct_answer === $key ? 'bg-ios-green text-white' : 'bg-white text-gray-600' }} flex items-center justify-center text-xs font-semibold">
                                                {{ $key }}
                                            </span>
                                            <span class="text-sm {{ $question->correct_answer === $key ? 'text-ios-green font-medium' : 'text-gray-600' }}">{{ $option }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center space-x-2 ml-4">
                        <a href="{{ route('admin.exams.questions.edit', [$exam, $question]) }}" class="p-2 text-ios-gray hover:text-ios-orange hover:bg-ios-gray-6 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </a>
                        <form action="{{ route('admin.exams.questions.destroy', [$exam, $question]) }}" method="POST" onsubmit="return confirm('Delete this question?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-ios-gray hover:text-ios-red hover:bg-ios-gray-6 rounded-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="ios-card p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-ios-gray-3 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-ios-gray mb-4">No questions added yet</p>
                <a href="{{ route('admin.exams.questions.create', $exam) }}" class="ios-btn bg-ios-blue text-white hover:bg-blue-600">Add First Question</a>
            </div>
        @endforelse
    </div>
</div>
@endsection
