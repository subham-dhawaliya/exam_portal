@extends('layouts.admin')

@section('title', 'Face Capture Details - Admin')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center space-x-4">
        <a href="{{ route('admin.face-captures.index') }}" class="p-2 hover:bg-ios-gray-6 rounded-xl transition-colors">
            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Face Capture Details</h1>
            <p class="text-ios-gray mt-1">{{ $faceCapture->created_at->format('M d, Y H:i:s') }}</p>
        </div>
    </div>

    <!-- Image -->
    <div class="ios-card overflow-hidden">
        <img src="{{ url('/api/face-image/' . $faceCapture->id) }}" alt="Face capture" class="w-full">
    </div>

    <!-- Details -->
    <div class="ios-card p-6 space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-ios-gray">User</p>
                <p class="font-medium text-gray-900">{{ $faceCapture->user->name ?? 'Unknown' }}</p>
            </div>
            <div>
                <p class="text-sm text-ios-gray">Capture Type</p>
                <p class="font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $faceCapture->capture_type)) }}</p>
            </div>
            <div>
                <p class="text-sm text-ios-gray">IP Address</p>
                <p class="font-medium text-gray-900">{{ $faceCapture->ip_address ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-ios-gray">Device</p>
                <p class="font-medium text-gray-900">{{ $faceCapture->device_info ?? 'N/A' }}</p>
            </div>
        </div>
        @if($faceCapture->examAttempt)
            <div class="pt-4 border-t border-ios-gray-5">
                <p class="text-sm text-ios-gray">Related Exam</p>
                <p class="font-medium text-gray-900">{{ $faceCapture->examAttempt->exam->title ?? 'Unknown' }}</p>
            </div>
        @endif
    </div>
</div>
@endsection
