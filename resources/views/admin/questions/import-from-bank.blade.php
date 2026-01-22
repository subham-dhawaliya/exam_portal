@extends('layouts.admin')

@section('title', 'Import from Question Bank - Admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.exams.questions.index', $exam) }}" class="p-2 hover:bg-ios-gray-6 rounded-xl transition-colors">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Import from Question Bank</h1>
                <p class="text-ios-gray mt-1">{{ $exam->title }} • Select questions to import</p>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="ios-card p-4">
        <form method="GET" class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Search questions..." 
                       class="ios-input w-full">
            </div>
            <select name="section" class="ios-input w-auto">
                <option value="">All Sections</option>
                @foreach($sections as $section)
                    <option value="{{ $section->id }}" {{ request('section') == $section->id ? 'selected' : '' }}>
                        {{ $section->icon }} {{ $section->name }}
                    </option>
                @endforeach
            </select>
            <select name="type" class="ios-input w-auto">
                <option value="">All Types</option>
                @foreach($questionTypes as $key => $label)
                    <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <select name="difficulty" class="ios-input w-auto">
                <option value="">All Difficulty</option>
                @foreach($difficulties as $key => $diff)
                    <option value="{{ $key }}" {{ request('difficulty') == $key ? 'selected' : '' }}>{{ $diff['label'] }}</option>
                @endforeach
            </select>
            <button type="submit" class="ios-btn bg-ios-blue text-white">Filter</button>
            @if(request()->hasAny(['search', 'section', 'type', 'difficulty']))
                <a href="{{ route('admin.exams.questions.import-from-bank', $exam) }}" class="ios-btn bg-gray-200 text-gray-700">Clear</a>
            @endif
        </form>
    </div>

    <!-- Questions List -->
    <form method="POST" action="{{ route('admin.exams.questions.store-from-bank', $exam) }}" id="importForm">
        @csrf
        
        <!-- Selection Bar -->
        <div class="ios-card p-4 flex items-center justify-between sticky top-0 z-10 bg-white/95 backdrop-blur">
            <div class="flex items-center gap-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="selectAll" class="rounded text-ios-blue w-5 h-5">
                    <span class="text-sm font-medium">Select All</span>
                </label>
                <span class="text-sm text-ios-gray">
                    <span id="selectedCount">0</span> selected
                </span>
            </div>
            <button type="submit" class="ios-btn bg-ios-green text-white" id="importBtn" disabled>
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Import Selected
            </button>
        </div>

        <!-- Questions -->
        <div class="space-y-3 mt-4">
            @forelse($questions as $question)
                @php
                    $isAdded = in_array($question->id, $addedBankIds);
                @endphp
                <div class="ios-card p-4 {{ $isAdded ? 'opacity-60' : '' }}">
                    <div class="flex items-start gap-4">
                        <div class="pt-1">
                            @if($isAdded)
                                <span class="w-5 h-5 rounded bg-ios-green text-white flex items-center justify-center text-xs">✓</span>
                            @else
                                <input type="checkbox" name="question_ids[]" value="{{ $question->id }}" 
                                       class="question-checkbox rounded text-ios-blue w-5 h-5">
                            @endif
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-xs font-medium px-2 py-1 bg-ios-blue/10 text-ios-blue rounded">
                                    {{ $questionTypes[$question->question_type] ?? $question->question_type }}
                                </span>
                                <span class="text-xs px-2 py-1 rounded {{ $question->difficulty === 'easy' ? 'bg-green-100 text-green-700' : ($question->difficulty === 'hard' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                    {{ ucfirst($question->difficulty) }}
                                </span>
                                <span class="text-xs text-ios-gray">{{ $question->marks }} marks</span>
                                @if($question->section)
                                    <span class="text-xs text-ios-gray">• {{ $question->section->icon }} {{ $question->section->name }}</span>
                                @endif
                                @if($isAdded)
                                    <span class="text-xs text-ios-green font-medium ml-auto">Already Added</span>
                                @endif
                            </div>
                            <p class="text-gray-900">{{ Str::limit($question->question_text, 200) }}</p>
                            
                            @if($question->question_type === 'mcq' && $question->options->count())
                                <div class="mt-3 grid grid-cols-2 gap-2">
                                    @foreach($question->options as $opt)
                                        <div class="text-sm p-2 rounded {{ $opt->is_correct ? 'bg-green-50 text-green-700' : 'bg-gray-50 text-gray-600' }}">
                                            {{ chr(65 + $opt->order) }}. {{ $opt->option_text }}
                                            @if($opt->is_correct) <span class="text-xs">✓</span> @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="ios-card p-12 text-center">
                    <svg class="w-16 h-16 mx-auto text-ios-gray-3 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                    <p class="text-ios-gray mb-4">No questions found in question bank</p>
                    <a href="{{ route('admin.question-bank.create') }}" class="ios-btn bg-ios-blue text-white">Create Questions</a>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($questions->hasPages())
            <div class="mt-6">
                {{ $questions->links() }}
            </div>
        @endif
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.question-checkbox');
    const selectedCount = document.getElementById('selectedCount');
    const importBtn = document.getElementById('importBtn');

    function updateCount() {
        const checked = document.querySelectorAll('.question-checkbox:checked').length;
        selectedCount.textContent = checked;
        importBtn.disabled = checked === 0;
    }

    selectAll.addEventListener('change', function() {
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateCount();
    });

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateCount);
    });

    updateCount();
});
</script>
@endpush
@endsection
