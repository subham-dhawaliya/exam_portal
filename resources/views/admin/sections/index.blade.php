@extends('layouts.admin')

@section('title', 'Sections - Question Bank')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Sections</h1>
            <p class="text-gray-600 mt-1">Organize your questions by category</p>
        </div>
        <a href="{{ route('admin.sections.create') }}" class="ios-btn bg-ios-blue text-white inline-flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Section
        </a>
    </div>

    <!-- Sections Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="sectionsGrid">
        @forelse($sections as $section)
        <div class="glass-card p-5 cursor-move section-card" data-id="{{ $section->id }}">
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white text-xl"
                         style="background-color: {{ $section->color }}">
                        {{ $section->icon ?? '📚' }}
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">{{ $section->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $section->questions_count }} questions</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <!-- Status Toggle -->
                    <button onclick="toggleStatus({{ $section->id }})" 
                            class="status-toggle {{ $section->is_active ? 'bg-ios-green' : 'bg-gray-300' }} relative inline-flex h-6 w-11 items-center rounded-full transition-colors"
                            data-id="{{ $section->id }}">
                        <span class="{{ $section->is_active ? 'translate-x-6' : 'translate-x-1' }} inline-block h-4 w-4 transform rounded-full bg-white transition-transform"></span>
                    </button>
                </div>
            </div>
            
            @if($section->description)
            <p class="text-sm text-gray-600 mt-3 line-clamp-2">{{ $section->description }}</p>
            @endif
            
            <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.question-bank.index', ['section' => $section->id]) }}" 
                   class="text-ios-blue text-sm font-medium hover:underline">
                    View Questions →
                </a>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.sections.edit', $section) }}" 
                       class="p-2 text-gray-500 hover:text-ios-blue hover:bg-gray-100 rounded-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </a>
                    <form action="{{ route('admin.sections.destroy', $section) }}" method="POST" class="inline"
                          onsubmit="return confirm('Delete this section? Questions will also be deleted!')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-2 text-gray-500 hover:text-ios-red hover:bg-red-50 rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900">No sections yet</h3>
            <p class="text-gray-500 mt-1">Create your first section to organize questions</p>
            <a href="{{ route('admin.sections.create') }}" class="ios-btn bg-ios-blue text-white mt-4 inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Create Section
            </a>
        </div>
        @endforelse
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
// Drag and drop reordering
new Sortable(document.getElementById('sectionsGrid'), {
    animation: 150,
    ghostClass: 'opacity-50',
    onEnd: function() {
        const ids = [...document.querySelectorAll('.section-card')].map(el => el.dataset.id);
        fetch('{{ route("admin.sections.reorder") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ sections: ids })
        });
    }
});

// Toggle status
function toggleStatus(id) {
    fetch(`/admin/sections/${id}/toggle-status`, {
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
</script>
@endpush
@endsection
