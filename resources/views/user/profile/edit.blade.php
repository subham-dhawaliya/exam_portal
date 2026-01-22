@extends('layouts.user')

@section('title', 'Edit Profile - Exam Portal')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center space-x-4">
        <a href="{{ route('user.profile.show') }}" class="p-2 hover:bg-ios-gray-6 rounded-xl transition-colors">
            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Profile</h1>
            <p class="text-ios-gray mt-1">Update your personal information</p>
        </div>
    </div>

    <!-- Profile Form -->
    <form method="POST" action="{{ route('user.profile.update') }}" enctype="multipart/form-data" class="ios-card p-6 space-y-6">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Profile Photo</label>
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 bg-gradient-to-br from-ios-blue to-ios-purple rounded-full flex items-center justify-center text-white text-xl font-bold overflow-hidden">
                    @if($user->profile_image)
                        <img src="{{ url('/api/profile-image/' . $user->id) }}" alt="Profile" class="w-full h-full object-cover">
                    @else
                        {{ substr($user->name, 0, 1) }}
                    @endif
                </div>
                <input type="file" name="profile_image" accept="image/*" class="ios-input flex-1">
            </div>
            @error('profile_image')<p class="mt-1 text-sm text-ios-red">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="ios-input">
            @error('name')<p class="mt-1 text-sm text-ios-red">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="ios-input">
            @error('email')<p class="mt-1 text-sm text-ios-red">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="ios-input" placeholder="Enter phone number">
            @error('phone')<p class="mt-1 text-sm text-ios-red">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
            <textarea name="address" rows="3" class="ios-input" placeholder="Enter your address">{{ old('address', $user->address) }}</textarea>
            @error('address')<p class="mt-1 text-sm text-ios-red">{{ $message }}</p>@enderror
        </div>

        <div class="flex items-center justify-end space-x-4 pt-4">
            <a href="{{ route('user.profile.show') }}" class="ios-btn bg-ios-gray-6 text-gray-700 hover:bg-ios-gray-5">Cancel</a>
            <button type="submit" class="ios-btn bg-ios-blue text-white hover:bg-blue-600">Save Changes</button>
        </div>
    </form>

    <!-- Password Form -->
    <form method="POST" action="{{ route('user.profile.password') }}" class="ios-card p-6 space-y-6">
        @csrf
        @method('PUT')

        <h2 class="text-lg font-semibold text-gray-900">Change Password</h2>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
            <input type="password" name="current_password" required class="ios-input">
            @error('current_password')<p class="mt-1 text-sm text-ios-red">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
            <input type="password" name="password" required class="ios-input" placeholder="Min 8 characters">
            @error('password')<p class="mt-1 text-sm text-ios-red">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
            <input type="password" name="password_confirmation" required class="ios-input">
        </div>

        <div class="flex items-center justify-end pt-4">
            <button type="submit" class="ios-btn bg-ios-orange text-white hover:bg-orange-600">Update Password</button>
        </div>
    </form>
</div>
@endsection
