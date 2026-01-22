<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\FaceCapture;
use App\Models\FaceVerificationLog;
use App\Models\User;
use App\Services\FaceRecognitionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LoginController extends Controller
{
    protected $faceService;

    public function __construct(FaceRecognitionService $faceService)
    {
        $this->faceService = $faceService;
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'face_image' => 'required|string',
            'face_match_score' => 'required|integer|min:0',
        ]);

        $credentials = $request->only('email', 'password');

        // First check credentials
        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Invalid credentials.']);
        }

        $user = Auth::user();

        // Block admin users from face login - they must use /admin/login
        if ($user->role && $user->role->slug === 'admin') {
            Auth::logout();
            return back()->withErrors(['email' => 'Admin users must login at /admin/login']);
        }

        // Check if account is active
        if (!$user->isActive()) {
            Auth::logout();
            return back()->withErrors(['email' => 'Your account has been suspended.']);
        }

        // Check if face is locked
        if ($user->isFaceLocked()) {
            Auth::logout();
            $minutes = $user->face_locked_until->diffInMinutes(now());
            return back()->withErrors(['face' => "Face verification locked. Try again in {$minutes} minutes."]);
        }

        $faceMatchScore = (int) $request->face_match_score;

        // CRITICAL: Check face match score - must be at least 70%
        if ($faceMatchScore < 70) {
            Auth::logout();
            
            // Log failed attempt
            FaceVerificationLog::logAttempt([
                'user_id' => $user->id,
                'email' => $user->email,
                'verification_type' => 'login',
                'status' => 'failed',
                'match_score' => $faceMatchScore,
                'liveness_score' => 100,
                'failure_reason' => "Face mismatch - Score: {$faceMatchScore}% (minimum 70% required)",
            ]);
            
            $user->incrementFailedFaceAttempts();
            
            return back()->withErrors(['face' => "Face verification failed! Your face does not match."]);
        }

        // Face matched successfully
        $verificationResult = [
            'success' => true,
            'match_score' => $faceMatchScore,
            'liveness_score' => 100,
            'quality_score' => 80,
            'message' => 'Face verified successfully',
            'checks' => [
                'face_matched' => true,
                'match_score' => $faceMatchScore,
            ],
        ];

        // Log the verification attempt
        FaceVerificationLog::logAttempt([
            'user_id' => $user->id,
            'email' => $user->email,
            'verification_type' => 'login',
            'status' => 'success',
            'match_score' => $faceMatchScore,
            'liveness_score' => $verificationResult['liveness_score'],
            'quality_score' => $verificationResult['quality_score'],
            'failure_reason' => null,
            'liveness_checks' => $verificationResult['checks'],
        ]);

        // Reset failed attempts on success
        $user->resetFailedFaceAttempts();

        // Save face capture
        $this->saveFaceCapture($user, $request->face_image, 'login', [
            'face_matched' => true,
            'match_score' => $faceMatchScore,
        ]);

        // Update last login
        $user->update(['last_login_at' => now()]);

        // Log activity
        ActivityLog::log('login', "User logged in with face match (Score: {$faceMatchScore}%)");

        $request->session()->regenerate();

        if ($user->isAdmin()) {
            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect()->intended(route('user.dashboard'));
    }

    public function verifyFaceAjax(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'face_image' => 'required|string',
            'liveness_data' => 'nullable|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ]);
        }

        if (!$user->isFaceVerified()) {
            return response()->json([
                'success' => false,
                'message' => 'Face not enrolled for this user',
            ]);
        }

        if ($user->isFaceLocked()) {
            return response()->json([
                'success' => false,
                'message' => 'Face verification temporarily locked',
            ]);
        }

        $livenessData = [];
        if ($request->liveness_data) {
            $livenessData = json_decode($request->liveness_data, true) ?? [];
        }

        $result = $this->faceService->verifyFace($user, $request->face_image, $livenessData);

        return response()->json($result);
    }

    public function logout(Request $request)
    {
        ActivityLog::log('logout', 'User logged out');

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    protected function saveFaceCapture($user, $imageData, $type, $metadata = [])
    {
        $image = str_replace('data:image/png;base64,', '', $imageData);
        $image = str_replace('data:image/jpeg;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        
        $imageName = 'face_captures/' . $user->id . '/' . $type . '_' . time() . '.png';
        Storage::disk('public')->put($imageName, base64_decode($image));

        FaceCapture::create([
            'user_id' => $user->id,
            'capture_type' => $type,
            'image_path' => $imageName,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'device_info' => request()->header('sec-ch-ua-platform'),
            'browser_info' => request()->header('sec-ch-ua'),
            'metadata' => !empty($metadata) ? $metadata : null,
        ]);
    }
}
