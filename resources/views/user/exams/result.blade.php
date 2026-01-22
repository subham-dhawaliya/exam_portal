@extends('layouts.user')

@section('title', 'Exam Result - Exam Portal')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Result Card -->
    <div class="ios-card overflow-hidden slide-in">
        <div class="p-8 text-center {{ $attempt->passed ? 'bg-gradient-to-br from-ios-green to-green-600' : 'bg-gradient-to-br from-ios-red to-red-600' }} text-white">
            <div class="w-24 h-24 mx-auto bg-white/20 rounded-full flex items-center justify-center mb-4">
                @if($attempt->passed)
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                @else
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                @endif
            </div>
            <h1 class="text-3xl font-bold mb-2">{{ $attempt->passed ? 'Congratulations!' : 'Better Luck Next Time' }}</h1>
            <p class="text-white/80">{{ $attempt->exam->title ?? 'Exam' }}</p>
        </div>

        <div class="p-6">
            <!-- Score -->
            <div class="text-center py-6 border-b border-ios-gray-5">
                <p class="text-6xl font-bold {{ $attempt->passed ? 'text-ios-green' : 'text-ios-red' }}">{{ $attempt->percentage }}%</p>
                <p class="text-ios-gray mt-2">{{ $attempt->score }} out of {{ $attempt->total_marks }} marks</p>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-3 divide-x divide-ios-gray-5 py-6">
                <div class="text-center">
                    <p class="text-2xl font-bold text-gray-900">{{ $attempt->answers->where('is_correct', true)->count() }}</p>
                    <p class="text-sm text-ios-gray">Correct</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-gray-900">{{ $attempt->answers->where('is_correct', false)->count() }}</p>
                    <p class="text-sm text-ios-gray">Incorrect</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-gray-900">{{ $attempt->tab_switch_count }}</p>
                    <p class="text-sm text-ios-gray">Tab Switches</p>
                </div>
            </div>

            <!-- Time Info -->
            <div class="flex items-center justify-between p-4 bg-ios-gray-6 rounded-xl">
                <div>
                    <p class="text-sm text-ios-gray">Started</p>
                    <p class="font-medium text-gray-900">{{ $attempt->started_at->format('M d, Y H:i') }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-ios-gray">Completed</p>
                    <p class="font-medium text-gray-900">{{ $attempt->completed_at->format('M d, Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Answer Review -->
    @if($attempt->exam && $attempt->exam->show_results)
        <div class="ios-card slide-in" style="animation-delay: 0.1s">
            <div class="p-6 border-b border-ios-gray-5">
                <h2 class="text-lg font-semibold text-gray-900">Answer Review</h2>
            </div>
            <div class="divide-y divide-ios-gray-5">
                @foreach($attempt->answers as $answer)
                    <div class="p-6">
                        <div class="flex items-start space-x-4">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 {{ $answer->is_correct ? 'bg-ios-green/10 text-ios-green' : 'bg-ios-red/10 text-ios-red' }}">
                                @if($answer->is_correct)
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                @endif
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-900 mb-2">{{ $answer->question->question }}</p>
                                
                                @if($answer->question->isMcq())
                                    <div class="space-y-2">
                                        <p class="text-sm">
                                            <span class="text-ios-gray">Your answer:</span>
                                            <span class="{{ $answer->is_correct ? 'text-ios-green' : 'text-ios-red' }} font-medium">
                                                {{ $answer->answer }} - {{ $answer->question->options[$answer->answer] ?? 'N/A' }}
                                            </span>
                                        </p>
                                        @if(!$answer->is_correct)
                                            <p class="text-sm">
                                                <span class="text-ios-gray">Correct answer:</span>
                                                <span class="text-ios-green font-medium">
                                                    {{ $answer->question->correct_answer }} - {{ $answer->question->options[$answer->question->correct_answer] ?? 'N/A' }}
                                                </span>
                                            </p>
                                        @endif
                                    </div>
                                @else
                                    <div class="p-3 bg-ios-gray-6 rounded-xl text-sm text-gray-700">
                                        {{ $answer->answer ?? 'No answer provided' }}
                                    </div>
                                @endif

                                @if($answer->question->explanation)
                                    <div class="mt-3 p-3 bg-ios-blue/5 rounded-xl">
                                        <p class="text-sm text-ios-blue"><strong>Explanation:</strong> {{ $answer->question->explanation }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Actions -->
    <div class="flex items-center justify-center space-x-4 slide-in" style="animation-delay: 0.2s">
        <a href="{{ route('user.exams.index') }}" class="ios-btn bg-ios-gray-6 text-gray-700 hover:bg-ios-gray-5">
            Browse Exams
        </a>
        <a href="{{ route('user.dashboard') }}" class="ios-btn bg-ios-blue text-white hover:bg-blue-600">
            Go to Dashboard
        </a>
    </div>
</div>
@endsection
