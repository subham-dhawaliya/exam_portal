<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\FaceEmbedding;
use App\Models\FaceVerificationLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('role')->whereHas('role', fn($q) => $q->where('slug', 'user'));

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $users = $query->latest()->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'status' => 'required|in:active,inactive,suspended',
            'face_image' => 'required|string',
        ]);

        $userRole = Role::where('slug', 'user')->first();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $userRole->id,
            'phone' => $request->phone,
            'status' => $request->status,
        ]);

        // Handle face photo from camera capture
        if ($request->face_image && strlen($request->face_image) > 100) {
            $this->saveFacePhoto($user, $request->face_image);
        }

        ActivityLog::log('user_created', "Created user: {$user->name}", $user);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    protected function saveFacePhoto($user, $imageData)
    {
        \Log::info('saveFacePhoto called for user: ' . $user->id);
        \Log::info('Image data length: ' . strlen($imageData));
        
        // Remove base64 header
        $image = str_replace('data:image/png;base64,', '', $imageData);
        $image = str_replace('data:image/jpeg;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        
        // Ensure directory exists
        $directory = 'face_captures/' . $user->id;
        if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($directory)) {
            \Illuminate\Support\Facades\Storage::disk('public')->makeDirectory($directory);
            \Log::info('Created directory: ' . $directory);
        }
        
        $imageName = $directory . '/registration_' . time() . '.jpg';
        
        // Decode and store the file
        $decodedImage = base64_decode($image);
        if (!$decodedImage) {
            \Log::error('Failed to decode base64 image for user: ' . $user->id);
            return false;
        }
        
        \Log::info('Decoded image size: ' . strlen($decodedImage) . ' bytes');
        
        $stored = \Illuminate\Support\Facades\Storage::disk('public')->put($imageName, $decodedImage);
        
        if (!$stored) {
            \Log::error('Failed to store face image for user: ' . $user->id);
            return false;
        }
        
        \Log::info('Image stored at: ' . $imageName);
        
        // Create face capture record
        $faceCapture = \App\Models\FaceCapture::create([
            'user_id' => $user->id,
            'capture_type' => 'registration',
            'image_path' => $imageName,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'liveness_verified' => true,
            'metadata' => ['uploaded_by' => 'admin', 'admin_id' => \Illuminate\Support\Facades\Auth::guard('admin')->id()],
        ]);

        \Log::info('FaceCapture record created with ID: ' . $faceCapture->id);

        // Mark user as face verified
        $user->update([
            'face_verified' => true,
            'face_enrolled_at' => now(),
            'face_enrollment_count' => $user->face_enrollment_count + 1,
        ]);
        
        \Log::info('Face photo saved successfully for user: ' . $user->id . ' at ' . $imageName);
        
        return true;
    }

    public function show(User $user)
    {
        $user->load(['examAttempts.exam', 'faceCaptures', 'activityLogs', 'faceEmbeddings', 'faceVerificationLogs']);
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        \Log::info('User update called for user ID: ' . $user->id);
        \Log::info('Has face_image: ' . ($request->has('face_image') ? 'yes' : 'no'));
        \Log::info('face_image length: ' . ($request->face_image ? strlen($request->face_image) : 0));
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive,suspended',
            'face_image' => 'nullable|string',
        ]);

        $oldValues = $user->toArray();

        $user->update($request->only(['name', 'email', 'phone', 'status']));

        if ($request->password) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        // Handle face photo update
        if ($request->face_image && strlen($request->face_image) > 100) {
            \Log::info('Calling saveFacePhoto for user: ' . $user->id);
            $this->saveFacePhoto($user, $request->face_image);
        } else {
            \Log::info('No face_image to save or too short');
        }

        ActivityLog::log('user_updated', "Updated user: {$user->name}", $user, $oldValues, $user->toArray());

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        ActivityLog::log('user_deleted', "Deleted user: {$user->name}", $user);
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    public function toggleStatus(User $user)
    {
        $newStatus = $user->status === 'active' ? 'suspended' : 'active';
        $user->update(['status' => $newStatus]);

        ActivityLog::log('user_status_changed', "Changed user status to: {$newStatus}", $user);

        return back()->with('success', 'User status updated.');
    }

    public function reEnrollFace(User $user)
    {
        // Delete existing face embeddings
        FaceEmbedding::where('user_id', $user->id)->delete();
        
        // Reset face verification status
        $user->update([
            'face_verified' => false,
            'face_enrollment_count' => 0,
            'face_enrolled_at' => null,
            'failed_face_attempts' => 0,
            'face_locked_until' => null,
        ]);

        // Log the action
        FaceVerificationLog::logAttempt([
            'user_id' => $user->id,
            'email' => $user->email,
            'verification_type' => 're_enrollment',
            'status' => 'success',
            'failure_reason' => 'Admin initiated re-enrollment',
        ]);

        ActivityLog::log('face_re_enrollment', "Reset face data for user: {$user->name}", $user);

        return back()->with('success', 'Face data cleared. User must re-enroll their face on next login.');
    }
}
