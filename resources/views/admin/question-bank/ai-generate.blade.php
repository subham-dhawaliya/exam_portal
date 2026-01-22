@extends('layouts.admin')

@section('title', 'AI Question Generator')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.question-bank.index') }}" class="p-2 hover:bg-gray-100 rounded-lg transition">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <span class="text-2xl">✨</span> AI Question Generator
            </h1>
            <p class="text-gray-600">Generate questions automatically using {{ ucfirst($provider) }} AI</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Generator Form -->
        <div class="lg:col-span-1">
            <div class="glass-card p-6 sticky top-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Generation Settings</h2>
                
                <form id="generateForm" class="space-y-4">
                    @csrf
                    
                    <!-- Section -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Section *</label>
                        <select name="section_id" id="sectionId" required class="ios-input">
                            <option value="">Select Section</option>
                            @foreach($sections as $section)
                                <option value="{{ $section->id }}">{{ $section->icon }} {{ $section->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Question Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Question Type *</label>
                        <select name="question_type" id="questionType" required class="ios-input">
                            @foreach($questionTypes as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Difficulty -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Difficulty *</label>
                        <select name="difficulty" id="difficulty" required class="ios-input">
                            @foreach($difficulties as $key => $diff)
                                <option value="{{ $key }}" {{ $key === 'medium' ? 'selected' : '' }}>{{ $diff['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Topic -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Topic / Subject *</label>
                        <input type="text" name="topic" id="topic" required class="ios-input" 
                               placeholder="e.g., Photosynthesis, World War II, Algebra">
                        <p class="text-xs text-gray-500 mt-1">Be specific for better results</p>
                    </div>

                    <!-- Count -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Number of Questions</label>
                        <input type="number" name="count" id="count" value="5" min="1" max="20" class="ios-input">
                    </div>

                    <!-- Additional Instructions -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Additional Instructions</label>
                        <textarea name="instructions" id="instructions" rows="2" class="ios-input" 
                                  placeholder="Any specific requirements..."></textarea>
                    </div>

                    <!-- Marks Settings -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Marks per Q</label>
                            <input type="number" name="marks" id="marks" value="1" step="0.5" min="0" class="ios-input">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Negative</label>
                            <input type="number" name="negative_marks" id="negativeMarks" value="0" step="0.25" min="0" class="ios-input">
                        </div>
                    </div>

                    <button type="submit" id="generateBtn" class="w-full ios-btn bg-gradient-to-r from-ios-purple to-ios-pink text-white">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Generate Questions
                    </button>
                </form>
            </div>
        </div>

        <!-- Generated Questions Preview -->
        <div class="lg:col-span-2">
            <!-- Loading State -->
            <div id="loadingState" class="hidden">
                <div class="glass-card p-12 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 relative">
                        <div class="absolute inset-0 border-4 border-ios-purple/20 rounded-full"></div>
                        <div class="absolute inset-0 border-4 border-ios-purple border-t-transparent rounded-full animate-spin"></div>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Generating Questions...</h3>
                    <p class="text-gray-500 mt-1">AI is creating your questions. This may take a moment.</p>
                </div>
            </div>

            <!-- Empty State -->
            <div id="emptyState" class="glass-card p-12 text-center">
                <div class="w-20 h-20 bg-gradient-to-br from-ios-purple/20 to-ios-pink/20 rounded-3xl flex items-center justify-center mx-auto mb-4">
                    <span class="text-4xl">🤖</span>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Ready to Generate</h3>
                <p class="text-gray-500 mt-1 max-w-sm mx-auto">Fill in the settings and click "Generate Questions" to create questions using AI.</p>
            </div>

            <!-- Questions Preview -->
            <div id="questionsPreview" class="hidden space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">
                        Generated Questions (<span id="questionCount">0</span>)
                    </h2>
                    <div class="flex items-center gap-2">
                        <button onclick="selectAll()" class="text-sm text-ios-blue hover:underline">Select All</button>
                        <span class="text-gray-300">|</span>
                        <button onclick="deselectAll()" class="text-sm text-ios-blue hover:underline">Deselect All</button>
                    </div>
                </div>

                <div id="questionsList" class="space-y-4">
                    <!-- Questions will be inserted here -->
                </div>

                <!-- Save Button -->
                <div class="glass-card p-4 flex items-center justify-between sticky bottom-4">
                    <div>
                        <span class="text-sm text-gray-600">Selected: <strong id="selectedCount">0</strong> questions</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <button onclick="regenerate()" class="ios-btn bg-gray-200 text-gray-700">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Regenerate
                        </button>
                        <button onclick="saveQuestions()" id="saveBtn" class="ios-btn bg-ios-green text-white">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Save to Question Bank
                        </button>
                    </div>
                </div>
            </div>

            <!-- Error State -->
            <div id="errorState" class="hidden glass-card p-8 text-center border-2 border-ios-red/20">
                <div class="w-16 h-16 bg-ios-red/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-ios-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Generation Failed</h3>
                <p class="text-gray-500 mt-1" id="errorMessage">Something went wrong. Please try again.</p>
                <button onclick="hideError()" class="ios-btn bg-gray-200 text-gray-700 mt-4">Try Again</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let generatedQuestions = [];
const questionTypes = @json($questionTypes);

document.getElementById('generateForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    await generateQuestions();
});

async function generateQuestions() {
    const form = document.getElementById('generateForm');
    const formData = new FormData(form);
    
    // Show loading
    document.getElementById('emptyState').classList.add('hidden');
    document.getElementById('questionsPreview').classList.add('hidden');
    document.getElementById('errorState').classList.add('hidden');
    document.getElementById('loadingState').classList.remove('hidden');
    document.getElementById('generateBtn').disabled = true;
    
    try {
        const response = await fetch('{{ route("admin.question-bank.ai-generate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                section_id: formData.get('section_id'),
                question_type: formData.get('question_type'),
                difficulty: formData.get('difficulty'),
                topic: formData.get('topic'),
                count: formData.get('count'),
                instructions: formData.get('instructions'),
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            generatedQuestions = data.questions;
            renderQuestions();
        } else {
            showError(data.message || 'Failed to generate questions');
        }
    } catch (error) {
        showError(error.message || 'Network error. Please try again.');
    } finally {
        document.getElementById('loadingState').classList.add('hidden');
        document.getElementById('generateBtn').disabled = false;
    }
}

function renderQuestions() {
    const container = document.getElementById('questionsList');
    container.innerHTML = '';
    
    generatedQuestions.forEach((q, index) => {
        const card = createQuestionCard(q, index);
        container.appendChild(card);
    });
    
    document.getElementById('questionCount').textContent = generatedQuestions.length;
    document.getElementById('questionsPreview').classList.remove('hidden');
    updateSelectedCount();
}

function createQuestionCard(question, index) {
    const div = document.createElement('div');
    div.className = 'glass-card p-5 question-card';
    div.dataset.index = index;
    
    const typeLabel = questionTypes[question.question_type] || question.question_type;
    
    let answerHtml = '';
    switch(question.question_type) {
        case 'mcq':
            answerHtml = question.options.map((opt, i) => `
                <div class="flex items-center gap-2 p-2 rounded ${i === question.correct_index ? 'bg-green-50 text-green-700' : ''}">
                    <span class="w-6 h-6 rounded-full border flex items-center justify-center text-xs ${i === question.correct_index ? 'bg-green-500 text-white border-green-500' : 'border-gray-300'}">
                        ${String.fromCharCode(65 + i)}
                    </span>
                    <span>${opt}</span>
                    ${i === question.correct_index ? '<span class="ml-auto text-xs font-medium">✓ Correct</span>' : ''}
                </div>
            `).join('');
            break;
        case 'multiple_select':
            answerHtml = question.options.map((opt, i) => `
                <div class="flex items-center gap-2 p-2 rounded ${question.correct_indices.includes(i) ? 'bg-green-50 text-green-700' : ''}">
                    <span class="w-5 h-5 rounded border flex items-center justify-center text-xs ${question.correct_indices.includes(i) ? 'bg-green-500 text-white border-green-500' : 'border-gray-300'}">
                        ${question.correct_indices.includes(i) ? '✓' : ''}
                    </span>
                    <span>${opt}</span>
                </div>
            `).join('');
            break;
        case 'true_false':
            answerHtml = `<p class="text-green-600 font-medium">Answer: ${question.correct_answer ? 'True' : 'False'}</p>`;
            break;
        case 'fill_blank':
        case 'short_answer':
            answerHtml = `<p class="text-green-600"><strong>Answers:</strong> ${question.answers.join(', ')}</p>`;
            break;
        case 'numerical':
            answerHtml = `<p class="text-green-600"><strong>Answer:</strong> ${question.answer} (±${question.tolerance})</p>`;
            break;
        case 'match_following':
            answerHtml = question.pairs.map(p => `
                <div class="flex items-center gap-2 p-2 bg-gray-50 rounded">
                    <span class="flex-1">${p.left}</span>
                    <span class="text-gray-400">→</span>
                    <span class="flex-1 text-green-600">${p.right}</span>
                </div>
            `).join('');
            break;
        case 'ordering':
            answerHtml = question.items.map((item, i) => `
                <div class="flex items-center gap-2 p-2 bg-gray-50 rounded">
                    <span class="w-6 h-6 bg-ios-blue text-white rounded flex items-center justify-center text-xs">${i + 1}</span>
                    <span>${item}</span>
                </div>
            `).join('');
            break;
        case 'essay':
            answerHtml = `
                <div class="space-y-2">
                    <p class="text-sm text-gray-600"><strong>Word Limit:</strong> ${question.word_limit || 500} words</p>
                    <p class="text-sm font-medium text-gray-700">Key Points to Cover:</p>
                    ${(question.key_points || []).map((point, i) => `
                        <div class="flex items-start gap-2 p-2 bg-green-50 rounded">
                            <span class="w-5 h-5 bg-green-500 text-white rounded-full flex items-center justify-center text-xs">${i + 1}</span>
                            <span class="text-green-700">${point}</span>
                        </div>
                    `).join('')}
                </div>
            `;
            break;
    }
    
    div.innerHTML = `
        <div class="flex items-start gap-4">
            <label class="flex items-center mt-1">
                <input type="checkbox" class="question-select rounded text-ios-green w-5 h-5" checked onchange="updateSelectedCount()">
            </label>
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-xs font-medium px-2 py-1 bg-ios-blue/10 text-ios-blue rounded">${typeLabel}</span>
                    <span class="text-xs text-gray-400">Q${index + 1}</span>
                </div>
                <p class="text-gray-900 font-medium mb-3">${question.question_text}</p>
                <div class="space-y-1 text-sm">
                    ${answerHtml}
                </div>
                ${question.explanation ? `
                <div class="mt-3 p-3 bg-blue-50 rounded-lg">
                    <p class="text-xs font-medium text-blue-800 mb-1">Explanation:</p>
                    <p class="text-sm text-blue-700">${question.explanation}</p>
                </div>
                ` : ''}
            </div>
            <button onclick="removeQuestion(${index})" class="p-2 text-gray-400 hover:text-ios-red hover:bg-red-50 rounded-lg transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    `;
    
    return div;
}

function removeQuestion(index) {
    generatedQuestions.splice(index, 1);
    renderQuestions();
}

function selectAll() {
    document.querySelectorAll('.question-select').forEach(cb => cb.checked = true);
    updateSelectedCount();
}

function deselectAll() {
    document.querySelectorAll('.question-select').forEach(cb => cb.checked = false);
    updateSelectedCount();
}

function updateSelectedCount() {
    const count = document.querySelectorAll('.question-select:checked').length;
    document.getElementById('selectedCount').textContent = count;
}

function regenerate() {
    generateQuestions();
}

async function saveQuestions() {
    const selectedIndices = [];
    document.querySelectorAll('.question-select').forEach((cb, i) => {
        if (cb.checked) selectedIndices.push(i);
    });
    
    if (selectedIndices.length === 0) {
        alert('Please select at least one question to save.');
        return;
    }
    
    const questionsToSave = selectedIndices.map(i => ({
        ...generatedQuestions[i],
        selected: true
    }));
    
    document.getElementById('saveBtn').disabled = true;
    document.getElementById('saveBtn').innerHTML = '<svg class="w-4 h-4 mr-1 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Saving...';
    
    try {
        const response = await fetch('{{ route("admin.question-bank.ai-save") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                questions: questionsToSave,
                section_id: document.getElementById('sectionId').value,
                difficulty: document.getElementById('difficulty').value,
                marks: document.getElementById('marks').value,
                negative_marks: document.getElementById('negativeMarks').value,
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            window.location.href = data.redirect;
        } else {
            alert(data.message || 'Failed to save questions');
        }
    } catch (error) {
        alert('Network error. Please try again.');
    } finally {
        document.getElementById('saveBtn').disabled = false;
        document.getElementById('saveBtn').innerHTML = '<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Save to Question Bank';
    }
}

function showError(message) {
    document.getElementById('errorMessage').textContent = message;
    document.getElementById('errorState').classList.remove('hidden');
}

function hideError() {
    document.getElementById('errorState').classList.add('hidden');
    document.getElementById('emptyState').classList.remove('hidden');
}
</script>
@endpush
@endsection
