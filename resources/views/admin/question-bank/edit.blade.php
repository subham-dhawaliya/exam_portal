@extends('layouts.admin')

@section('title', 'Edit Question')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.question-bank.index') }}" class="p-2 hover:bg-gray-100 rounded-lg transition">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Question</h1>
            <p class="text-gray-600">Update question details</p>
        </div>
    </div>

    <form action="{{ route('admin.question-bank.update', $questionBank) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="space-y-6">
            <!-- Basic Info -->
            <div class="glass-card p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Section *</label>
                        <select name="section_id" required class="ios-input">
                            @foreach($sections as $section)
                                <option value="{{ $section->id }}" {{ $questionBank->section_id == $section->id ? 'selected' : '' }}>
                                    {{ $section->icon }} {{ $section->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Question Type *</label>
                        <select name="question_type" id="questionType" required class="ios-input">
                            @foreach($questionTypes as $key => $label)
                                <option value="{{ $key }}" {{ $questionBank->question_type == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Difficulty *</label>
                        <select name="difficulty" required class="ios-input">
                            @foreach($difficulties as $key => $diff)
                                <option value="{{ $key }}" {{ $questionBank->difficulty == $key ? 'selected' : '' }}>
                                    {{ $diff['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Marks *</label>
                            <input type="number" name="marks" value="{{ $questionBank->marks }}" step="0.5" min="0" required class="ios-input">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Negative Marks</label>
                            <input type="number" name="negative_marks" value="{{ $questionBank->negative_marks }}" step="0.25" min="0" class="ios-input">
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Question Text *</label>
                    <textarea name="question_text" rows="4" required class="ios-input">{{ $questionBank->question_text }}</textarea>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Question Image</label>
                    @if($questionBank->question_image)
                        <div class="mb-2">
                            <img src="{{ Storage::url($questionBank->question_image) }}" class="h-32 rounded-lg">
                        </div>
                    @endif
                    <input type="file" name="question_image" accept="image/*" class="ios-input">
                </div>
            </div>

            <!-- MCQ Options -->
            <div class="glass-card p-6 type-section {{ $questionBank->question_type == 'mcq' ? '' : 'hidden' }}" id="mcqSection">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Answer Options</h2>
                <div id="mcqOptions" class="space-y-3">
                    @foreach($questionBank->options as $index => $option)
                    <div class="flex items-center gap-3 option-row">
                        <input type="radio" name="correct_options[]" value="{{ $index }}" {{ $option->is_correct ? 'checked' : '' }} class="text-ios-green">
                        <input type="text" name="options[]" value="{{ $option->option_text }}" class="ios-input flex-1">
                        <button type="button" onclick="removeOption(this)" class="p-2 text-gray-400 hover:text-ios-red">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    @endforeach
                </div>
                <button type="button" onclick="addMcqOption()" class="mt-3 text-ios-blue text-sm font-medium flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Option
                </button>
            </div>

            <!-- Multiple Select -->
            <div class="glass-card p-6 type-section {{ $questionBank->question_type == 'multiple_select' ? '' : 'hidden' }}" id="multipleSelectSection">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Answer Options</h2>
                <div id="multiSelectOptions" class="space-y-3">
                    @if($questionBank->question_type == 'multiple_select')
                        @foreach($questionBank->options as $index => $option)
                        <div class="flex items-center gap-3 option-row">
                            <input type="checkbox" name="correct_options[]" value="{{ $index }}" {{ $option->is_correct ? 'checked' : '' }} class="rounded text-ios-green">
                            <input type="text" name="options[]" value="{{ $option->option_text }}" class="ios-input flex-1">
                            <button type="button" onclick="removeOption(this)" class="p-2 text-gray-400 hover:text-ios-red">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        @endforeach
                    @endif
                </div>
                <button type="button" onclick="addMultiSelectOption()" class="mt-3 text-ios-blue text-sm font-medium flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Option
                </button>
            </div>

            <!-- True/False -->
            <div class="glass-card p-6 type-section {{ $questionBank->question_type == 'true_false' ? '' : 'hidden' }}" id="trueFalseSection">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Correct Answer</h2>
                @php $correctTF = $questionBank->options->where('is_correct', true)->first(); @endphp
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 p-4 border rounded-xl cursor-pointer hover:bg-gray-50 flex-1">
                        <input type="radio" name="correct_answer" value="true" {{ $correctTF && $correctTF->option_text == 'True' ? 'checked' : '' }} class="text-ios-green">
                        <span class="font-medium">True</span>
                    </label>
                    <label class="flex items-center gap-2 p-4 border rounded-xl cursor-pointer hover:bg-gray-50 flex-1">
                        <input type="radio" name="correct_answer" value="false" {{ $correctTF && $correctTF->option_text == 'False' ? 'checked' : '' }} class="text-ios-green">
                        <span class="font-medium">False</span>
                    </label>
                </div>
            </div>

            <!-- Fill Blank / Short Answer -->
            <div class="glass-card p-6 type-section {{ in_array($questionBank->question_type, ['fill_blank', 'short_answer']) ? '' : 'hidden' }}" id="fillBlankSection">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Acceptable Answers</h2>
                <div id="fillBlankAnswers" class="space-y-3">
                    @foreach($questionBank->answers as $answer)
                    <div class="flex items-center gap-3">
                        <input type="text" name="answers[]" value="{{ $answer->answer_text }}" class="ios-input flex-1">
                        <button type="button" onclick="removeAnswer(this)" class="p-2 text-gray-400 hover:text-ios-red">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    @endforeach
                </div>
                <button type="button" onclick="addFillBlankAnswer()" class="mt-3 text-ios-blue text-sm font-medium flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Alternative Answer
                </button>
                @php $firstAnswer = $questionBank->answers->first(); @endphp
                <div class="mt-4 flex items-center gap-6">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="case_sensitive" value="1" {{ $firstAnswer && $firstAnswer->is_case_sensitive ? 'checked' : '' }} class="rounded text-ios-blue">
                        <span class="text-sm text-gray-700">Case Sensitive</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="partial_match" value="1" {{ $firstAnswer && $firstAnswer->allow_partial_match ? 'checked' : '' }} class="rounded text-ios-blue">
                        <span class="text-sm text-gray-700">Allow Partial Match</span>
                    </label>
                </div>
            </div>

            <!-- Numerical -->
            <div class="glass-card p-6 type-section {{ $questionBank->question_type == 'numerical' ? '' : 'hidden' }}" id="numericalSection">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Numerical Answer</h2>
                @php $numAnswer = $questionBank->answers->first(); @endphp
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Correct Answer *</label>
                        <input type="number" name="numerical_answer" value="{{ $numAnswer->answer_text ?? '' }}" step="any" class="ios-input">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tolerance (±)</label>
                        <input type="number" name="tolerance" value="{{ $numAnswer->tolerance ?? 0 }}" step="any" class="ios-input">
                    </div>
                </div>
            </div>

            <!-- Match Following -->
            <div class="glass-card p-6 type-section {{ $questionBank->question_type == 'match_following' ? '' : 'hidden' }}" id="matchSection">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Matching Pairs</h2>
                <div id="matchPairs" class="space-y-3">
                    @foreach($questionBank->matchPairs as $pair)
                    <div class="flex items-center gap-3 match-row">
                        <input type="text" name="left_sides[]" value="{{ $pair->left_side }}" class="ios-input flex-1">
                        <span class="text-gray-400">→</span>
                        <input type="text" name="right_sides[]" value="{{ $pair->right_side }}" class="ios-input flex-1">
                        <button type="button" onclick="removeMatchPair(this)" class="p-2 text-gray-400 hover:text-ios-red">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    @endforeach
                </div>
                <button type="button" onclick="addMatchPair()" class="mt-3 text-ios-blue text-sm font-medium flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Pair
                </button>
            </div>

            <!-- Ordering -->
            <div class="glass-card p-6 type-section {{ $questionBank->question_type == 'ordering' ? '' : 'hidden' }}" id="orderingSection">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Items to Order</h2>
                <div id="orderingItems" class="space-y-3">
                    @foreach($questionBank->orderingItems as $index => $item)
                    <div class="flex items-center gap-3 ordering-row">
                        <span class="w-8 h-8 bg-ios-blue text-white rounded-lg flex items-center justify-center text-sm font-medium">{{ $index + 1 }}</span>
                        <input type="text" name="ordering_items[]" value="{{ $item->item_text }}" class="ios-input flex-1">
                        <button type="button" onclick="removeOrderingItem(this)" class="p-2 text-gray-400 hover:text-ios-red">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    @endforeach
                </div>
                <button type="button" onclick="addOrderingItem()" class="mt-3 text-ios-blue text-sm font-medium flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Item
                </button>
            </div>

            <!-- Essay -->
            <div class="glass-card p-6 type-section {{ $questionBank->question_type == 'essay' ? '' : 'hidden' }}" id="essaySection">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Essay Question</h2>
                <div class="p-4 bg-yellow-50 rounded-xl">
                    <p class="text-yellow-800 text-sm">Essay questions require manual grading.</p>
                </div>
            </div>

            <!-- Explanation -->
            <div class="glass-card p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Explanation</h2>
                <textarea name="explanation" rows="3" class="ios-input">{{ $questionBank->explanation }}</textarea>
            </div>

            <!-- Tags & Status -->
            <div class="glass-card p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tags</label>
                        <input type="text" name="tags" value="{{ is_array($questionBank->tags) ? implode(', ', $questionBank->tags) : '' }}" class="ios-input">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Time Limit (seconds)</label>
                        <input type="number" name="time_limit" value="{{ $questionBank->time_limit }}" min="0" class="ios-input">
                    </div>
                </div>
                
                <div class="mt-4 flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                    <div>
                        <p class="font-medium text-gray-900">Active Status</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ $questionBank->is_active ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-300 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-ios-green"></div>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('admin.question-bank.index') }}" class="ios-btn bg-gray-200 text-gray-700">Cancel</a>
                <button type="submit" class="ios-btn bg-ios-blue text-white">Update Question</button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
const questionType = document.getElementById('questionType');
let mcqOptionCount = {{ $questionBank->options->count() }};
let multiSelectOptionCount = {{ $questionBank->question_type == 'multiple_select' ? $questionBank->options->count() : 0 }};
let orderingItemCount = {{ $questionBank->orderingItems->count() }};

questionType.addEventListener('change', function() {
    document.querySelectorAll('.type-section').forEach(s => s.classList.add('hidden'));
    const sectionMap = {
        'mcq': 'mcqSection',
        'multiple_select': 'multipleSelectSection',
        'true_false': 'trueFalseSection',
        'fill_blank': 'fillBlankSection',
        'short_answer': 'fillBlankSection',
        'numerical': 'numericalSection',
        'match_following': 'matchSection',
        'ordering': 'orderingSection',
        'essay': 'essaySection'
    };
    if (sectionMap[this.value]) {
        document.getElementById(sectionMap[this.value]).classList.remove('hidden');
    }
});

function addMcqOption() {
    const container = document.getElementById('mcqOptions');
    const div = document.createElement('div');
    div.className = 'flex items-center gap-3 option-row';
    div.innerHTML = `
        <input type="radio" name="correct_options[]" value="${mcqOptionCount}" class="text-ios-green">
        <input type="text" name="options[]" class="ios-input flex-1" placeholder="Option ${String.fromCharCode(65 + mcqOptionCount)}">
        <button type="button" onclick="removeOption(this)" class="p-2 text-gray-400 hover:text-ios-red">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    `;
    container.appendChild(div);
    mcqOptionCount++;
}

function addMultiSelectOption() {
    const container = document.getElementById('multiSelectOptions');
    const div = document.createElement('div');
    div.className = 'flex items-center gap-3 option-row';
    div.innerHTML = `
        <input type="checkbox" name="correct_options[]" value="${multiSelectOptionCount}" class="rounded text-ios-green">
        <input type="text" name="options[]" class="ios-input flex-1" placeholder="Option">
        <button type="button" onclick="removeOption(this)" class="p-2 text-gray-400 hover:text-ios-red">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    `;
    container.appendChild(div);
    multiSelectOptionCount++;
}

function removeOption(btn) { btn.closest('.option-row').remove(); }

function addFillBlankAnswer() {
    const container = document.getElementById('fillBlankAnswers');
    const div = document.createElement('div');
    div.className = 'flex items-center gap-3';
    div.innerHTML = `
        <input type="text" name="answers[]" class="ios-input flex-1" placeholder="Alternative answer">
        <button type="button" onclick="removeAnswer(this)" class="p-2 text-gray-400 hover:text-ios-red">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    `;
    container.appendChild(div);
}

function removeAnswer(btn) { btn.closest('div').remove(); }

function addMatchPair() {
    const container = document.getElementById('matchPairs');
    const div = document.createElement('div');
    div.className = 'flex items-center gap-3 match-row';
    div.innerHTML = `
        <input type="text" name="left_sides[]" class="ios-input flex-1" placeholder="Left item">
        <span class="text-gray-400">→</span>
        <input type="text" name="right_sides[]" class="ios-input flex-1" placeholder="Right item">
        <button type="button" onclick="removeMatchPair(this)" class="p-2 text-gray-400 hover:text-ios-red">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    `;
    container.appendChild(div);
}

function removeMatchPair(btn) { btn.closest('.match-row').remove(); }

function addOrderingItem() {
    orderingItemCount++;
    const container = document.getElementById('orderingItems');
    const div = document.createElement('div');
    div.className = 'flex items-center gap-3 ordering-row';
    div.innerHTML = `
        <span class="w-8 h-8 bg-ios-blue text-white rounded-lg flex items-center justify-center text-sm font-medium">${orderingItemCount}</span>
        <input type="text" name="ordering_items[]" class="ios-input flex-1" placeholder="Item">
        <button type="button" onclick="removeOrderingItem(this)" class="p-2 text-gray-400 hover:text-ios-red">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    `;
    container.appendChild(div);
    updateOrderingNumbers();
}

function removeOrderingItem(btn) {
    btn.closest('.ordering-row').remove();
    updateOrderingNumbers();
}

function updateOrderingNumbers() {
    document.querySelectorAll('.ordering-row').forEach((row, index) => {
        row.querySelector('span').textContent = index + 1;
    });
    orderingItemCount = document.querySelectorAll('.ordering-row').length;
}
</script>
@endpush
@endsection
