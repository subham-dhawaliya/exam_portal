@extends('layouts.admin')

@section('title', 'Preview Question')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.question-bank.index') }}" class="p-2 hover:bg-gray-100 rounded-lg transition">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Question Preview</h1>
                <p class="text-gray-600">How students will see this question</p>
            </div>
        </div>
        <a href="{{ route('admin.question-bank.edit', $questionBank) }}" class="ios-btn bg-ios-blue text-white">
            Edit Question
        </a>
    </div>

    <!-- Question Card -->
    <div class="glass-card p-6">
        <!-- Meta Info -->
        <div class="flex flex-wrap items-center gap-3 mb-4 pb-4 border-b">
            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-sm font-medium"
                  style="background-color: {{ $questionBank->section->color }}20; color: {{ $questionBank->section->color }}">
                {{ $questionBank->section->icon }} {{ $questionBank->section->name }}
            </span>
            <span class="px-3 py-1 bg-gray-100 rounded-lg text-sm text-gray-600">
                {{ \App\Models\QuestionBank::getQuestionTypes()[$questionBank->question_type] ?? $questionBank->question_type }}
            </span>
            @php $diff = \App\Models\QuestionBank::getDifficultyLevels()[$questionBank->difficulty]; @endphp
            <span class="px-3 py-1 rounded-lg text-sm font-medium
                {{ $diff['color'] === 'green' ? 'bg-green-100 text-green-700' : '' }}
                {{ $diff['color'] === 'yellow' ? 'bg-yellow-100 text-yellow-700' : '' }}
                {{ $diff['color'] === 'red' ? 'bg-red-100 text-red-700' : '' }}">
                {{ $diff['label'] }}
            </span>
            <span class="px-3 py-1 bg-ios-blue/10 text-ios-blue rounded-lg text-sm font-medium">
                {{ $questionBank->marks }} marks
                @if($questionBank->negative_marks > 0)
                    <span class="text-ios-red">(-{{ $questionBank->negative_marks }})</span>
                @endif
            </span>
        </div>

        <!-- Question Text -->
        <div class="mb-6">
            <h2 class="text-lg font-medium text-gray-900 leading-relaxed">{{ $questionBank->question_text }}</h2>
            @if($questionBank->question_image)
                <img src="{{ Storage::url($questionBank->question_image) }}" class="mt-4 max-h-64 rounded-lg">
            @endif
        </div>

        <!-- Answer Section based on type -->
        @switch($questionBank->question_type)
            @case('mcq')
                <div class="space-y-3">
                    @foreach($questionBank->options as $index => $option)
                    <label class="flex items-center gap-3 p-4 border rounded-xl cursor-pointer hover:bg-gray-50 transition {{ $option->is_correct ? 'border-ios-green bg-green-50' : '' }}">
                        <input type="radio" name="answer" class="text-ios-blue" {{ $option->is_correct ? 'checked' : '' }}>
                        <span class="flex-1">{{ $option->option_text }}</span>
                        @if($option->is_correct)
                            <span class="text-ios-green text-sm font-medium">✓ Correct</span>
                        @endif
                    </label>
                    @endforeach
                </div>
                @break

            @case('multiple_select')
                <div class="space-y-3">
                    @foreach($questionBank->options as $option)
                    <label class="flex items-center gap-3 p-4 border rounded-xl cursor-pointer hover:bg-gray-50 transition {{ $option->is_correct ? 'border-ios-green bg-green-50' : '' }}">
                        <input type="checkbox" class="rounded text-ios-blue" {{ $option->is_correct ? 'checked' : '' }}>
                        <span class="flex-1">{{ $option->option_text }}</span>
                        @if($option->is_correct)
                            <span class="text-ios-green text-sm font-medium">✓ Correct</span>
                        @endif
                    </label>
                    @endforeach
                </div>
                @break

            @case('true_false')
                <div class="flex gap-4">
                    @foreach($questionBank->options as $option)
                    <label class="flex items-center gap-3 p-4 border rounded-xl cursor-pointer hover:bg-gray-50 transition flex-1 {{ $option->is_correct ? 'border-ios-green bg-green-50' : '' }}">
                        <input type="radio" name="answer" class="text-ios-blue" {{ $option->is_correct ? 'checked' : '' }}>
                        <span class="font-medium">{{ $option->option_text }}</span>
                        @if($option->is_correct)
                            <span class="text-ios-green text-sm">✓</span>
                        @endif
                    </label>
                    @endforeach
                </div>
                @break

            @case('fill_blank')
            @case('short_answer')
                <div>
                    <input type="text" class="ios-input" placeholder="Type your answer here...">
                    <div class="mt-4 p-4 bg-green-50 rounded-xl">
                        <p class="text-sm font-medium text-green-800 mb-2">Acceptable Answers:</p>
                        <ul class="list-disc list-inside text-green-700 text-sm">
                            @foreach($questionBank->answers as $answer)
                                <li>{{ $answer->answer_text }}</li>
                            @endforeach
                        </ul>
                        @php $firstAnswer = $questionBank->answers->first(); @endphp
                        @if($firstAnswer)
                            <p class="text-xs text-green-600 mt-2">
                                {{ $firstAnswer->is_case_sensitive ? 'Case sensitive' : 'Case insensitive' }} •
                                {{ $firstAnswer->allow_partial_match ? 'Partial match allowed' : 'Exact match required' }}
                            </p>
                        @endif
                    </div>
                </div>
                @break

            @case('numerical')
                @php $answer = $questionBank->answers->first(); @endphp
                <div>
                    <input type="number" class="ios-input" placeholder="Enter number...">
                    <div class="mt-4 p-4 bg-green-50 rounded-xl">
                        <p class="text-sm font-medium text-green-800">Correct Answer: {{ $answer->answer_text ?? 'N/A' }}</p>
                        @if($answer && $answer->tolerance > 0)
                            <p class="text-xs text-green-600 mt-1">Tolerance: ± {{ $answer->tolerance }}</p>
                        @endif
                    </div>
                </div>
                @break

            @case('match_following')
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <p class="text-sm font-medium text-gray-700 mb-2">Column A</p>
                        @foreach($questionBank->matchPairs as $index => $pair)
                        <div class="p-3 bg-gray-100 rounded-lg">{{ $index + 1 }}. {{ $pair->left_side }}</div>
                        @endforeach
                    </div>
                    <div class="space-y-2">
                        <p class="text-sm font-medium text-gray-700 mb-2">Column B (Correct Matches)</p>
                        @foreach($questionBank->matchPairs as $index => $pair)
                        <div class="p-3 bg-green-100 rounded-lg text-green-800">{{ $index + 1 }}. {{ $pair->right_side }}</div>
                        @endforeach
                    </div>
                </div>
                @break

            @case('ordering')
                <div class="space-y-2">
                    <p class="text-sm text-gray-500 mb-3">Correct order (students will see shuffled):</p>
                    @foreach($questionBank->orderingItems as $item)
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                        <span class="w-8 h-8 bg-ios-blue text-white rounded-lg flex items-center justify-center text-sm font-medium">
                            {{ $item->correct_position }}
                        </span>
                        <span>{{ $item->item_text }}</span>
                    </div>
                    @endforeach
                </div>
                @break

            @case('essay')
                <div>
                    <textarea class="ios-input" rows="6" placeholder="Write your essay here..."></textarea>
                    <p class="text-sm text-yellow-600 mt-2">⚠️ This question requires manual grading</p>
                </div>
                @break
        @endswitch

        <!-- Explanation -->
        @if($questionBank->explanation)
        <div class="mt-6 p-4 bg-blue-50 rounded-xl">
            <p class="text-sm font-medium text-blue-800 mb-1">Explanation:</p>
            <p class="text-blue-700 text-sm">{{ $questionBank->explanation }}</p>
        </div>
        @endif

        <!-- Tags -->
        @if($questionBank->tags && count($questionBank->tags) > 0)
        <div class="mt-4 flex flex-wrap gap-2">
            @foreach($questionBank->tags as $tag)
            <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded text-xs">#{{ $tag }}</span>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection
