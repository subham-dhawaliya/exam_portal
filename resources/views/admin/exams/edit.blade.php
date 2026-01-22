@extends('layouts.admin')

@section('title', 'Edit Exam - Admin')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center space-x-4">
        <a href="{{ route('admin.exams.index') }}" class="p-2 hover:bg-ios-gray-6 rounded-xl transition-colors">
            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Exam</h1>
            <p class="text-ios-gray mt-1">{{ $exam->title }}</p>
        </div>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('admin.exams.update', $exam) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="ios-card p-6 space-y-6">
            <h2 class="text-lg font-semibold text-gray-900">Basic Information</h2>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Exam Title</label>
                <input type="text" name="title" value="{{ old('title', $exam->title) }}" required class="ios-input">
                @error('title')<p class="mt-1 text-sm text-ios-red">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="3" class="ios-input">{{ old('description', $exam->description) }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Duration (minutes)</label>
                    <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $exam->duration_minutes) }}" required min="1" class="ios-input">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Max Attempts</label>
                    <input type="number" name="max_attempts" value="{{ old('max_attempts', $exam->max_attempts) }}" required min="1" class="ios-input">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Total Marks</label>
                    <input type="number" name="total_marks" value="{{ old('total_marks', $exam->total_marks) }}" required min="1" class="ios-input">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Passing Marks</label>
                    <input type="number" name="passing_marks" value="{{ old('passing_marks', $exam->passing_marks) }}" required min="0" class="ios-input">
                </div>
            </div>
        </div>

        <div class="ios-card p-6 space-y-6">
            <h2 class="text-lg font-semibold text-gray-900">Schedule</h2>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Start Time</label>
                    <input type="datetime-local" name="start_time" value="{{ old('start_time', $exam->start_time?->format('Y-m-d\TH:i')) }}" class="ios-input">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">End Time</label>
                    <input type="datetime-local" name="end_time" value="{{ old('end_time', $exam->end_time?->format('Y-m-d\TH:i')) }}" class="ios-input">
                </div>
            </div>
        </div>

        <div class="ios-card p-6 space-y-6">
            <h2 class="text-lg font-semibold text-gray-900">Settings</h2>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="ios-input">
                    <option value="draft" {{ old('status', $exam->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ old('status', $exam->status) === 'published' ? 'selected' : '' }}>Published</option>
                    <option value="completed" {{ old('status', $exam->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="archived" {{ old('status', $exam->status) === 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
            </div>

            <div class="space-y-4">
                <label class="flex items-center space-x-3 cursor-pointer">
                    <input type="checkbox" name="shuffle_questions" value="1" {{ old('shuffle_questions', $exam->shuffle_questions) ? 'checked' : '' }} class="w-5 h-5 rounded-lg border-ios-gray-4 text-ios-blue focus:ring-ios-blue">
                    <span class="text-gray-700">Shuffle questions for each attempt</span>
                </label>

                <label class="flex items-center space-x-3 cursor-pointer">
                    <input type="checkbox" name="show_results" value="1" {{ old('show_results', $exam->show_results) ? 'checked' : '' }} class="w-5 h-5 rounded-lg border-ios-gray-4 text-ios-blue focus:ring-ios-blue">
                    <span class="text-gray-700">Show results to students after completion</span>
                </label>

                <label class="flex items-center space-x-3 cursor-pointer">
                    <input type="checkbox" name="face_verification_required" value="1" {{ old('face_verification_required', $exam->face_verification_required) ? 'checked' : '' }} class="w-5 h-5 rounded-lg border-ios-gray-4 text-ios-blue focus:ring-ios-blue">
                    <span class="text-gray-700">Require face verification before exam</span>
                </label>
            </div>
        </div>

        <div class="flex items-center justify-between">
            <button type="button" onclick="confirmDelete()" class="ios-btn bg-ios-red/10 text-ios-red hover:bg-ios-red/20">Delete Exam</button>
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.exams.index') }}" class="ios-btn bg-ios-gray-6 text-gray-700 hover:bg-ios-gray-5">Cancel</a>
                <button type="submit" class="ios-btn bg-ios-blue text-white hover:bg-blue-600">Update Exam</button>
            </div>
        </div>
    </form>

    <!-- Delete Form (outside main form) -->
    <form id="deleteForm" action="{{ route('admin.exams.destroy', $exam) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
</div>

@push('scripts')
<script>
function confirmDelete() {
    if (confirm('Are you sure? This will delete all questions and attempts.')) {
        document.getElementById('deleteForm').submit();
    }
}
</script>
@endpush
@endsection
