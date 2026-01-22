@extends('layouts.admin')

@section('title', 'Add Question - Admin')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center space-x-4">
        <a href="{{ route('admin.exams.questions.index', $exam) }}" class="p-2 hover:bg-ios-gray-6 rounded-xl transition-colors">
            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Add Question</h1>
            <p class="text-ios-gray mt-1">{{ $exam->title }}</p>
        </div>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('admin.exams.questions.store', $exam) }}" class="space-y-6" id="questionForm">
        @csrf

        <div class="ios-card p-6 space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Question Type</label>
                <div class="flex space-x-4">
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="type" value="mcq" {{ old('type', 'mcq') === 'mcq' ? 'checked' : '' }} class="sr-only peer" onchange="toggleOptions()">
                        <div class="p-4 rounded-xl border-2 border-ios-gray-5 peer-checked:border-ios-blue peer-checked:bg-ios-blue/5 transition-all">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-ios-blue/10 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-ios-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">Multiple Choice</p>
                                    <p class="text-sm text-ios-gray">Auto-graded</p>
                                </div>
                            </div>
                        </div>
                    </label>
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="type" value="descriptive" {{ old('type') === 'descriptive' ? 'checked' : '' }} class="sr-only peer" onchange="toggleOptions()">
                        <div class="p-4 rounded-xl border-2 border-ios-gray-5 peer-checked:border-ios-purple peer-checked:bg-ios-purple/5 transition-all">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-ios-purple/10 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-ios-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">Descriptive</p>
                                    <p class="text-sm text-ios-gray">Manual grading</p>
                                </div>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Question</label>
                <textarea name="question" rows="3" required class="ios-input" placeholder="Enter your question">{{ old('question') }}</textarea>
                @error('question')<p class="mt-1 text-sm text-ios-red">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Marks</label>
                <input type="number" name="marks" value="{{ old('marks', 1) }}" required min="1" class="ios-input w-32">
                @error('marks')<p class="mt-1 text-sm text-ios-red">{{ $message }}</p>@enderror
            </div>
        </div>

        <!-- MCQ Options -->
        <div class="ios-card p-6 space-y-6" id="mcqOptions">
            <h2 class="text-lg font-semibold text-gray-900">Answer Options</h2>
            
            <div class="space-y-4" id="optionsContainer">
                @foreach(['A', 'B', 'C', 'D'] as $key)
                    <div class="flex items-center space-x-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="correct_answer" value="{{ $key }}" {{ old('correct_answer') === $key ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-10 h-10 rounded-full border-2 border-ios-gray-4 peer-checked:border-ios-green peer-checked:bg-ios-green peer-checked:text-white flex items-center justify-center font-semibold transition-all">
                                {{ $key }}
                            </div>
                        </label>
                        <input type="text" name="options[{{ $key }}]" value="{{ old("options.$key") }}" class="ios-input flex-1" placeholder="Option {{ $key }}">
                    </div>
                @endforeach
            </div>
            <p class="text-sm text-ios-gray">Click the letter to mark as correct answer</p>
            @error('correct_answer')<p class="text-sm text-ios-red">{{ $message }}</p>@enderror
        </div>

        <!-- Explanation -->
        <div class="ios-card p-6 space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Explanation (Optional)</label>
                <textarea name="explanation" rows="2" class="ios-input" placeholder="Explain the correct answer">{{ old('explanation') }}</textarea>
            </div>
        </div>

        <div class="flex items-center justify-between">
            <label class="flex items-center space-x-2 cursor-pointer">
                <input type="checkbox" name="add_another" value="1" class="w-5 h-5 rounded-lg border-ios-gray-4 text-ios-blue focus:ring-ios-blue">
                <span class="text-gray-700">Add another question</span>
            </label>
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.exams.questions.index', $exam) }}" class="ios-btn bg-ios-gray-6 text-gray-700 hover:bg-ios-gray-5">Cancel</a>
                <button type="submit" class="ios-btn bg-ios-blue text-white hover:bg-blue-600">Add Question</button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function toggleOptions() {
    const type = document.querySelector('input[name="type"]:checked').value;
    const mcqOptions = document.getElementById('mcqOptions');
    mcqOptions.style.display = type === 'mcq' ? 'block' : 'none';
}
toggleOptions();
</script>
@endpush
@endsection
