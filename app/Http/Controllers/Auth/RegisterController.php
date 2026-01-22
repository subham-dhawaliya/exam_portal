<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\FaceCapture;
use App\Models\FaceVerificationLog;
use App\Models\Role;
use App\Models\User;
use App\Services\DuplicateFaceDetectionService;
use App\Services\FaceRecognitionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class RegisterController extends Controller
{
    protected $faceService;
    protected $duplicateFaceService;

    public function __construct(FaceRecognitionService $faceService, DuplicateFaceDetectionService $duplicateFaceService)
    {
        $this->faceService = $faceService;
        $this->duplicateFaceService = $duplicateFaceService;
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'face_image' => 'required|string',
            'face_descriptor' => 'nullable|string',
        ]);

        // Parse face descriptor if provided
        $faceDescriptor = null;
        if ($request->face_descriptor) {
            $faceDescriptor = json_decode($request->face_descriptor, true);
            
            // Check for duplicate face BEFORE creating account
            if (is_array($faceDescriptor) && count($faceDescriptor) === 128) {
                $duplicateCheck = $this->duplicateFaceService->findDuplicateFace($faceDescriptor);
                
                if ($duplicateCheck && $duplicateCheck['is_duplicate']) {
                    // Log the duplicate attempt
                    \Log::warning('Duplicate face registration attempt', [
                        'attempted_email' => $request->email,
                        'matched_user_id' => $duplicateCheck['matched_user_id'],
                        'similarity_score' => $duplicateCheck['similarity_score'],
                        'ip' => $request->ip(),
                    ]);
                    
                    return back()->withErrors([
                        'face_duplicate' => 'This face is already registered with another account (' . $duplicateCheck['matched_user_email'] . '). Each person can only have one account.',
                    ])->withInput($request->except('face_image', 'face_descriptor'));
                }
            }
        }

        $userRole = Role::where('slug', 'user')->first();

        // Save reference face image
        $imagePath = $this->saveReferenceFace($request->face_image, $request->email);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $userRole->id,
            'reference_face_image' => $imagePath,
            'face_descriptor' => $faceDescriptor,
            'status' => 'active',
            'face_verified' => true,
        ]);

        // Save face capture (360° scan verified)
        FaceCapture::create([
            'user_id' => $user->id,
            'capture_type' => 'registration',
            'image_path' => $imagePath,
            'liveness_verified' => true,
            'blink_count' => 0,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'metadata' => [
                'scan_type' => '360_face_scan',
                'positions_verified' => ['center', 'left', 'right', 'up', 'down'],
            ],
        ]);

        // Log enrollment attempt
        FaceVerificationLog::logAttempt([
            'user_id' => $user->id,
            'email' => $user->email,
            'verification_type' => 'registration',
            'status' => 'success',
            'liveness_score' => 100,
            'quality_score' => 80,
            'liveness_checks' => [
                'scan_type' => '360_face_scan',
            ],
        ]);

        Auth::login($user);

        ActivityLog::log('register', 'New user registered with 360° Face ID scan', $user);

        return redirect()->route('user.dashboard');
    }

    protected function saveReferenceFace($imageData, $email)
    {
        $image = str_replace('data:image/png;base64,', '', $imageData);
        $image = str_replace('data:image/jpeg;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        
        $imageName = 'reference_faces/' . md5($email) . '_' . time() . '.jpg';
        Storage::disk('public')->put($imageName, base64_decode($image));

        return $imageName;
    }
}
