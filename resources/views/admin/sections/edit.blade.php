@extends('layouts.admin')

@section('title', 'Edit Section')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Header -->
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.sections.index') }}" class="p-2 hover:bg-gray-100 rounded-lg transition">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Section</h1>
            <p class="text-gray-600">Update section details</p>
        </div>
    </div>

    <div class="glass-card p-6">
        <form action="{{ route('admin.sections.update', $section) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="space-y-5">
                <!-- Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Section Name *</label>
                    <input type="text" name="name" value="{{ old('name', $section->name) }}" required
                           class="ios-input @error('name') border-ios-red @enderror"
                           placeholder="e.g., Mathematics, General Knowledge">
                    @error('name')
                        <p class="text-ios-red text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3" class="ios-input"
                              placeholder="Brief description of this section">{{ old('description', $section->description) }}</textarea>
                </div>

                <!-- Icon & Color -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Icon (Emoji)</label>
                        <input type="text" name="icon" value="{{ old('icon', $section->icon ?? '📚') }}" 
                               class="ios-input text-center text-2xl" maxlength="5">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                        <div class="flex items-center gap-2">
                            <input type="color" name="color" value="{{ old('color', $section->color) }}" 
                                   class="w-12 h-10 rounded-lg border-0 cursor-pointer">
                            <input type="text" value="{{ old('color', $section->color) }}" 
                                   class="ios-input flex-1" readonly id="colorText">
                        </div>
                    </div>
                </div>

                <!-- Status -->
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                    <div>
                        <p class="font-medium text-gray-900">Active Status</p>
                        <p class="text-sm text-gray-500">Section will be visible for question creation</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ $section->is_active ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-300 peer-focus:ring-2 peer-focus:ring-ios-blue/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-ios-green"></div>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t">
                <a href="{{ route('admin.sections.index') }}" class="ios-btn bg-gray-200 text-gray-700">Cancel</a>
                <button type="submit" class="ios-btn bg-ios-blue text-white">Update Section</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.querySelector('input[name="color"]').addEventListener('input', function() {
    document.getElementById('colorText').value = this.value;
});
</script>
@endpush
@endsection
