@extends('layouts.admin')

@section('title', 'Face Captures - Admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Face Captures</h1>
            <p class="text-ios-gray mt-1">View all face verification logs</p>
        </div>
        <button type="button" id="bulkDeleteBtn" class="ios-btn bg-ios-red text-white hover:bg-red-600 hidden">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            Delete Selected (<span id="selectedCount">0</span>)
        </button>
    </div>

    @if(session('success'))
        {{-- Notification handled by layout --}}
    @endif

    <!-- Filters -->
    <div class="ios-card p-4">
        <form method="GET" class="flex flex-col sm:flex-row gap-4 items-end">
            <select name="capture_type" class="ios-input sm:w-40">
                <option value="">All Types</option>
                <option value="login" {{ request('capture_type') === 'login' ? 'selected' : '' }}>Login</option>
                <option value="exam_start" {{ request('capture_type') === 'exam_start' ? 'selected' : '' }}>Exam Start</option>
                <option value="registration" {{ request('capture_type') === 'registration' ? 'selected' : '' }}>Registration</option>
            </select>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="ios-input sm:w-40" placeholder="From">
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="ios-input sm:w-40" placeholder="To">
            <button type="submit" class="ios-btn bg-ios-gray-6 text-gray-700 hover:bg-ios-gray-5">Filter</button>
            <label class="flex items-center gap-2 cursor-pointer ml-auto">
                <input type="checkbox" id="selectAll" class="w-5 h-5 rounded border-gray-300 text-ios-blue focus:ring-ios-blue">
                <span class="text-sm text-gray-600">Select All</span>
            </label>
        </form>
    </div>

    <!-- Bulk Delete Form -->
    <form id="bulkDeleteForm" method="POST" action="{{ route('admin.face-captures.bulk-delete') }}" class="hidden">
        @csrf
    </form>

    <!-- Captures Grid -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
        @forelse($captures as $capture)
            <div class="ios-card overflow-hidden slide-in group relative">
                <!-- Checkbox -->
                <div class="absolute top-2 left-2 z-10">
                    <input type="checkbox" class="capture-checkbox w-5 h-5 rounded border-gray-300 text-ios-blue focus:ring-ios-blue bg-white/80" data-id="{{ $capture->id }}">
                </div>
                
                <!-- Delete Button -->
                <form method="POST" action="{{ route('admin.face-captures.destroy', $capture) }}" class="absolute top-2 right-2 z-10" onsubmit="return confirm('Are you sure you want to delete this face capture?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-8 h-8 bg-ios-red/90 hover:bg-ios-red text-white rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </form>

                <a href="{{ route('admin.face-captures.show', $capture) }}" class="block">
                    <div class="aspect-square bg-ios-gray-6">
                        @if($capture->image_path && Storage::disk('public')->exists($capture->image_path))
                            <img src="{{ url('/api/face-image/' . $capture->id) }}" alt="Face capture" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-ios-gray">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="p-4">
                        <p class="font-medium text-gray-900 truncate">{{ $capture->user->name ?? 'Unknown' }}</p>
                        <p class="text-sm text-ios-gray">{{ ucfirst(str_replace('_', ' ', $capture->capture_type)) }}</p>
                        <p class="text-xs text-ios-gray mt-1">{{ $capture->created_at->format('M d, Y H:i') }}</p>
                        @if($capture->liveness_verified)
                            <span class="inline-flex items-center mt-2 px-2 py-1 bg-ios-green/10 text-ios-green text-xs rounded-full">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Liveness
                            </span>
                        @endif
                    </div>
                </a>
            </div>
        @empty
            <div class="col-span-full ios-card p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-ios-gray-3 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                </svg>
                <p class="text-ios-gray">No face captures found</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="flex justify-center">
        {{ $captures->links() }}
    </div>
</div>

@push('scripts')
<script>
const checkboxes = document.querySelectorAll('.capture-checkbox');
const selectAllCheckbox = document.getElementById('selectAll');
const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
const bulkDeleteForm = document.getElementById('bulkDeleteForm');
const selectedCountSpan = document.getElementById('selectedCount');

function updateBulkDeleteButton() {
    const checkedBoxes = document.querySelectorAll('.capture-checkbox:checked');
    const count = checkedBoxes.length;
    
    if (count > 0) {
        bulkDeleteBtn.classList.remove('hidden');
        selectedCountSpan.textContent = count;
    } else {
        bulkDeleteBtn.classList.add('hidden');
    }
}

checkboxes.forEach(checkbox => {
    checkbox.addEventListener('change', updateBulkDeleteButton);
});

selectAllCheckbox.addEventListener('change', function() {
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    updateBulkDeleteButton();
});

bulkDeleteBtn.addEventListener('click', function() {
    if (!confirm('Are you sure you want to delete the selected face captures?')) {
        return;
    }
    
    // Clear existing inputs
    bulkDeleteForm.querySelectorAll('input[name="ids[]"]').forEach(el => el.remove());
    
    // Add selected IDs
    document.querySelectorAll('.capture-checkbox:checked').forEach(checkbox => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'ids[]';
        input.value = checkbox.dataset.id;
        bulkDeleteForm.appendChild(input);
    });
    
    bulkDeleteForm.submit();
});
</script>
@endpush
@endsection
