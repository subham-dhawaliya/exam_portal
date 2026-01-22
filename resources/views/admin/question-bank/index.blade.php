@extends('layouts.admin')

@section('title', 'Question Bank')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Question Bank</h1>
            <p class="text-gray-600 mt-1">{{ $questions->total() }} questions in database</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.sections.index') }}" class="ios-btn bg-gray-200 text-gray-700">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                Sections
            </a>
            <a href="{{ route('admin.question-bank.ai-form') }}" class="ios-btn bg-gradient-to-r from-ios-purple to-ios-pink text-white">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                AI Generate
            </a>
            <a href="{{ route('admin.question-bank.create') }}" class="ios-btn bg-ios-blue text-white">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Question
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="glass-card p-4">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       class="ios-input" placeholder="Search questions...">
            </div>
            <div class="w-40">
                <label class="block text-sm font-medium text-gray-700 mb-1">Section</label>
                <select name="section" class="ios-input">
                    <option value="">All Sections</option>
                    @foreach($sections as $section)
                        <option value="{{ $section->id }}" {{ request('section') == $section->id ? 'selected' : '' }}>
                            {{ $section->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="w-40">
                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select name="type" class="ios-input">
                    <option value="">All Types</option>
                    @foreach($questionTypes as $key => $label)
                        <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="w-32">
                <label class="block text-sm font-medium text-gray-700 mb-1">Difficulty</label>
                <select name="difficulty" class="ios-input">
                    <option value="">All</option>
                    @foreach($difficulties as $key => $diff)
                        <option value="{{ $key }}" {{ request('difficulty') == $key ? 'selected' : '' }}>
                            {{ $diff['label'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="w-32">
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="ios-input">
                    <option value="">All</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <button type="submit" class="ios-btn bg-gray-800 text-white">Filter</button>
            <a href="{{ route('admin.question-bank.index') }}" class="ios-btn bg-gray-200 text-gray-700">Reset</a>
        </form>
    </div>

    <!-- Bulk Actions -->
    <div class="flex items-center gap-4 hidden" id="bulkActions">
        <span class="text-sm text-gray-600"><span id="selectedCount">0</span> selected</span>
        <button onclick="bulkDelete()" class="ios-btn bg-ios-red text-white text-sm py-2">
            Delete Selected
        </button>
    </div>

    <!-- Questions Table -->
    <div class="glass-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left">
                            <input type="checkbox" id="selectAll" class="rounded border-gray-300">
                        </th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Question</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Section</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Type</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Difficulty</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Marks</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Status</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($questions as $question)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3">
                            <input type="checkbox" class="question-checkbox rounded border-gray-300" value="{{ $question->id }}">
                        </td>
                        <td class="px-4 py-3">
                            <div class="max-w-md">
                                <p class="text-gray-900 line-clamp-2">{{ Str::limit(strip_tags($question->question_text), 100) }}</p>
                                @if($question->question_image)
                                    <span class="text-xs text-ios-blue">📷 Has image</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-medium"
                                  style="background-color: {{ $question->section->color }}20; color: {{ $question->section->color }}">
                                {{ $question->section->icon ?? '📚' }} {{ $question->section->name }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm text-gray-600">{{ $questionTypes[$question->question_type] ?? $question->question_type }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @php $diff = $difficulties[$question->difficulty] ?? ['label' => $question->difficulty, 'color' => 'gray']; @endphp
                            <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium
                                {{ $diff['color'] === 'green' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $diff['color'] === 'yellow' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                {{ $diff['color'] === 'red' ? 'bg-red-100 text-red-700' : '' }}">
                                {{ $diff['label'] }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="font-medium text-gray-900">{{ $question->marks }}</span>
                            @if($question->negative_marks > 0)
                                <span class="text-xs text-ios-red">(-{{ $question->negative_marks }})</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <button onclick="toggleStatus({{ $question->id }})" 
                                    class="status-toggle {{ $question->is_active ? 'bg-ios-green' : 'bg-gray-300' }} relative inline-flex h-6 w-11 items-center rounded-full transition-colors"
                                    data-id="{{ $question->id }}">
                                <span class="{{ $question->is_active ? 'translate-x-6' : 'translate-x-1' }} inline-block h-4 w-4 transform rounded-full bg-white transition-transform"></span>
                            </button>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('admin.question-bank.preview', $question) }}" 
                                   class="p-2 text-gray-500 hover:text-ios-blue hover:bg-blue-50 rounded-lg transition" title="Preview">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <a href="{{ route('admin.question-bank.edit', $question) }}" 
                                   class="p-2 text-gray-500 hover:text-ios-blue hover:bg-blue-50 rounded-lg transition" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('admin.question-bank.duplicate', $question) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="p-2 text-gray-500 hover:text-ios-green hover:bg-green-50 rounded-lg transition" title="Duplicate">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                    </button>
                                </form>
                                <form action="{{ route('admin.question-bank.destroy', $question) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Delete this question?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-gray-500 hover:text-ios-red hover:bg-red-50 rounded-lg transition" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900">No questions found</h3>
                            <p class="text-gray-500 mt-1">Create your first question to get started</p>
                            <a href="{{ route('admin.question-bank.create') }}" class="ios-btn bg-ios-blue text-white mt-4 inline-flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Add Question
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($questions->hasPages())
        <div class="px-4 py-3 border-t bg-gray-50">
            {{ $questions->links() }}
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
const checkboxes = document.querySelectorAll('.question-checkbox');
const selectAll = document.getElementById('selectAll');
const bulkActions = document.getElementById('bulkActions');
const selectedCount = document.getElementById('selectedCount');

selectAll.addEventListener('change', function() {
    checkboxes.forEach(cb => cb.checked = this.checked);
    updateBulkActions();
});

checkboxes.forEach(cb => {
    cb.addEventListener('change', updateBulkActions);
});

function updateBulkActions() {
    const checked = document.querySelectorAll('.question-checkbox:checked');
    selectedCount.textContent = checked.length;
    bulkActions.classList.toggle('hidden', checked.length === 0);
}

function toggleStatus(id) {
    fetch(`/admin/question-bank/${id}/toggle-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(res => res.json())
    .then(data => {
        const btn = document.querySelector(`.status-toggle[data-id="${id}"]`);
        const span = btn.querySelector('span');
        if (data.is_active) {
            btn.classList.remove('bg-gray-300');
            btn.classList.add('bg-ios-green');
            span.classList.remove('translate-x-1');
            span.classList.add('translate-x-6');
        } else {
            btn.classList.remove('bg-ios-green');
            btn.classList.add('bg-gray-300');
            span.classList.remove('translate-x-6');
            span.classList.add('translate-x-1');
        }
    });
}

function bulkDelete() {
    if (!confirm('Delete selected questions? This cannot be undone.')) return;
    
    const ids = [...document.querySelectorAll('.question-checkbox:checked')].map(cb => cb.value);
    
    fetch('{{ route("admin.question-bank.bulk-delete") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ ids })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}
</script>
@endpush
@endsection
