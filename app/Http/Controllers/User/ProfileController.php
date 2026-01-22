<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        return view('user.profile.show', compact('user'));
    }

    public function edit()
    {
        $user = auth()->user();
        return view('user.profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'profile_image' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['name', 'email', 'phone', 'address']);

        if ($request->hasFile('profile_image')) {
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }
            $data['profile_image'] = $request->file('profile_image')->store('profile_images', 'public');
        }

        $user->update($data);

        ActivityLog::log('profile_updated', 'User updated their profile');

        return redirect()->route('user.profile.show')->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        ActivityLog::log('password_changed', 'User changed their password');

        return back()->with('success', 'Password updated successfully.');
    }

    public function updateFace(Request $request)
    {
        $request->validate([
            'face_image' => 'required|string',
        ]);

        $user = auth()->user();

        // Remove base64 header
        $image = str_replace('data:image/png;base64,', '', $request->face_image);
        $image = str_replace('data:image/jpeg;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        
        // Ensure directory exists
        $directory = 'face_captures/' . $user->id;
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }
        
        $imageName = $directory . '/registration_' . time() . '.jpg';
        
        // Decode and store the file
        $decodedImage = base64_decode($image);
        if (!$decodedImage) {
            return response()->json(['success' => false, 'message' => 'Invalid image data']);
        }
        
        $stored = Storage::disk('public')->put($imageName, $decodedImage);
        
        if (!$stored) {
            return response()->json(['success' => false, 'message' => 'Failed to save image']);
        }
        
        // Create face capture record
        \App\Models\FaceCapture::create([
            'user_id' => $user->id,
            'capture_type' => 'registration',
            'image_path' => $imageName,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'liveness_verified' => true,
            'metadata' => ['uploaded_by' => 'user', 'source' => 'profile_update'],
        ]);

        // Mark user as face verified
        $user->update([
            'face_verified' => true,
            'face_enrolled_at' => now(),
            'face_enrollment_count' => $user->face_enrollment_count + 1,
        ]);

        ActivityLog::log('face_updated', 'User updated their reference face image');

        return response()->json(['success' => true, 'message' => 'Face image updated successfully.']);
    }
}
