@extends('layouts.admin')

@section('title', 'Create Exam - Admin')

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
            <h1 class="text-2xl font-bold text-gray-900">Create Exam</h1>
            <p class="text-ios-gray mt-1">Set up a new examination</p>
        </div>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('admin.exams.store') }}" class="space-y-6">
        @csrf

        <div class="ios-card p-6 space-y-6">
            <h2 class="text-lg font-semibold text-gray-900">Basic Information</h2>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Exam Title</label>
                <input type="text" name="title" value="{{ old('title') }}" required class="ios-input" placeholder="Enter exam title">
                @error('title')<p class="mt-1 text-sm text-ios-red">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="3" class="ios-input" placeholder="Enter exam description">{{ old('description') }}</textarea>
                @error('description')<p class="mt-1 text-sm text-ios-red">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Duration (minutes)</label>
                    <input type="number" name="duration_minutes" value="{{ old('duration_minutes', 60) }}" required min="1" class="ios-input">
                    @error('duration_minutes')<p class="mt-1 text-sm text-ios-red">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Max Attempts</label>
                    <input type="number" name="max_attempts" value="{{ old('max_attempts', 1) }}" required min="1" class="ios-input">
                    @error('max_attempts')<p class="mt-1 text-sm text-ios-red">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Total Marks</label>
                    <input type="number" name="total_marks" value="{{ old('total_marks', 100) }}" required min="1" class="ios-input">
                    @error('total_marks')<p class="mt-1 text-sm text-ios-red">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Passing Marks</label>
                    <input type="number" name="passing_marks" value="{{ old('passing_marks', 40) }}" required min="0" class="ios-input">
                    @error('passing_marks')<p class="mt-1 text-sm text-ios-red">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="ios-card p-6 space-y-6">
            <h2 class="text-lg font-semibold text-gray-900">Schedule (Optional)</h2>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Start Time</label>
                    <input type="datetime-local" name="start_time" value="{{ old('start_time') }}" class="ios-input">
                    @error('start_time')<p class="mt-1 text-sm text-ios-red">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">End Time</label>
                    <input type="datetime-local" name="end_time" value="{{ old('end_time') }}" class="ios-input">
                    @error('end_time')<p class="mt-1 text-sm text-ios-red">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="ios-card p-6 space-y-6">
            <h2 class="text-lg font-semibold text-gray-900">Settings</h2>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="ios-input">
                    <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Published</option>
                </select>
            </div>

            <div class="space-y-4">
                <label class="flex items-center space-x-3 cursor-pointer">
                    <input type="checkbox" name="shuffle_questions" value="1" {{ old('shuffle_questions') ? 'checked' : '' }} class="w-5 h-5 rounded-lg border-ios-gray-4 text-ios-blue focus:ring-ios-blue">
                    <span class="text-gray-700">Shuffle questions for each attempt</span>
                </label>

                <label class="flex items-center space-x-3 cursor-pointer">
                    <input type="checkbox" name="show_results" value="1" {{ old('show_results', true) ? 'checked' : '' }} class="w-5 h-5 rounded-lg border-ios-gray-4 text-ios-blue focus:ring-ios-blue">
                    <span class="text-gray-700">Show results to students after completion</span>
                </label>

                <label class="flex items-center space-x-3 cursor-pointer">
                    <input type="checkbox" name="face_verification_required" value="1" {{ old('face_verification_required', true) ? 'checked' : '' }} class="w-5 h-5 rounded-lg border-ios-gray-4 text-ios-blue focus:ring-ios-blue">
                    <span class="text-gray-700">Require face verification before exam</span>
                </label>
            </div>
        </div>

        <div class="flex items-center justify-end space-x-4">
            <a href="{{ route('admin.exams.index') }}" class="ios-btn bg-ios-gray-6 text-gray-700 hover:bg-ios-gray-5">Cancel</a>
            <button type="submit" class="ios-btn bg-ios-blue text-white hover:bg-blue-600">Create & Add Questions</button>
        </div>
    </form>
</div>
@endsection
