<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DuplicateFaceDetectionService;
use App\Services\FaceScanLockService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FaceScanController extends Controller
{
    protected FaceScanLockService $lockService;
    protected DuplicateFaceDetectionService $duplicateFaceService;
    
    public function __construct(FaceScanLockService $lockService, DuplicateFaceDetectionService $duplicateFaceService)
    {
        $this->lockService = $lockService;
        $this->duplicateFaceService = $duplicateFaceService;
    }
    
    /**
     * Generate or get device ID for this session
     */
    protected function getDeviceId(Request $request): string
    {
        // Use combination of session ID and fingerprint for unique device identification
        $sessionId = $request->session()->getId();
        $fingerprint = $request->header('X-Device-Fingerprint', '');
        $ip = $request->ip();
        $userAgent = $request->userAgent();
        
        return hash('sha256', $sessionId . $fingerprint . $ip . $userAgent);
    }
    
    /**
     * Start a new 360° scan session
     */
    public function startScan(Request $request)
    {
        $request->validate([
            'scan_type' => 'required|in:registration,login,update,profile_update',
        ]);
        
        $deviceId = $this->getDeviceId($request);
        $result = $this->lockService->startScan($deviceId, $request->scan_type);
        
        if ($result['success']) {
            // Store token in session for additional security
            $request->session()->put('face_scan_token', $result['token']);
            $request->session()->put('face_scan_device', $deviceId);
        }
        
        return response()->json($result);
    }
    
    /**
     * Update scan progress (directions completed)
     */
    public function updateProgress(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'directions' => 'required|array',
            'directions.*' => 'in:left,right,up,down',
        ]);
        
        $deviceId = $this->getDeviceId($request);
        $sessionToken = $request->session()->get('face_scan_token');
        
        // Verify token matches session
        if ($sessionToken !== $request->token) {
            return response()->json([
                'success' => false,
                'message' => 'Token mismatch. Possible session hijacking attempt.',
                'code' => 'TOKEN_MISMATCH',
            ], 403);
        }
        
        $result = $this->lockService->updateProgress($deviceId, $request->token, $request->directions);
        
        return response()->json($result);
    }
    
    /**
     * Validate if final capture is allowed
     */
    public function validateCapture(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);
        
        $deviceId = $this->getDeviceId($request);
        $sessionToken = $request->session()->get('face_scan_token');
        
        // Verify token matches session
        if ($sessionToken !== $request->token) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized capture attempt detected.',
                'code' => 'UNAUTHORIZED',
            ], 403);
        }
        
        $result = $this->lockService->validateCapture($deviceId, $request->token);
        
        return response()->json($result);
    }
    
    /**
     * Complete the scan session
     */
    public function completeScan(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);
        
        $deviceId = $this->getDeviceId($request);
        $sessionToken = $request->session()->get('face_scan_token');
        
        if ($sessionToken !== $request->token) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid session',
            ], 403);
        }
        
        $result = $this->lockService->completeScan($deviceId, $request->token);
        
        if ($result['success']) {
            $request->session()->forget(['face_scan_token', 'face_scan_device']);
        }
        
        return response()->json($result);
    }
    
    /**
     * Abort/cancel the scan
     */
    public function abortScan(Request $request)
    {
        $deviceId = $this->getDeviceId($request);
        $token = $request->token ?? $request->session()->get('face_scan_token');
        
        if ($token) {
            $result = $this->lockService->abortScan($deviceId, $token);
            $request->session()->forget(['face_scan_token', 'face_scan_device']);
            return response()->json($result);
        }
        
        return response()->json(['success' => true, 'message' => 'No active scan']);
    }
    
    /**
     * Get current scan status
     */
    public function getStatus(Request $request)
    {
        $deviceId = $this->getDeviceId($request);
        $result = $this->lockService->getScanStatus($deviceId);
        
        return response()->json($result);
    }
    
    /**
     * Check if face descriptor matches any existing user (duplicate detection)
     */
    public function checkDuplicateFace(Request $request)
    {
        $request->validate([
            'face_descriptor' => 'required|array',
            'face_descriptor.*' => 'numeric',
        ]);
        
        $descriptor = $request->face_descriptor;
        
        // Validate descriptor length (face-api.js uses 128-dimensional vectors)
        if (count($descriptor) !== 128) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid face descriptor format',
            ], 400);
        }
        
        $duplicateCheck = $this->duplicateFaceService->findDuplicateFace($descriptor);
        
        if ($duplicateCheck && $duplicateCheck['is_duplicate']) {
            \Log::warning('Duplicate face detected via API', [
                'matched_user_id' => $duplicateCheck['matched_user_id'],
                'similarity_score' => $duplicateCheck['similarity_score'],
                'ip' => $request->ip(),
            ]);
            
            return response()->json([
                'success' => false,
                'is_duplicate' => true,
                'message' => 'This face is already registered with another account.',
                'matched_email' => $duplicateCheck['matched_user_email'],
                'similarity_score' => $duplicateCheck['similarity_score'],
            ]);
        }
        
        return response()->json([
            'success' => true,
            'is_duplicate' => false,
            'message' => 'Face is unique, registration allowed.',
        ]);
    }
}
