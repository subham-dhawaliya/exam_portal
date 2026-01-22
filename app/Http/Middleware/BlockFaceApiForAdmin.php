<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class BlockFaceApiForAdmin
{
    /**
     * Block face LOGIN APIs for admin, but allow face SCAN APIs for user management
     * 
     * Admin should NOT be able to:
     * - Use face recognition to login
     * - Access /api/user-face or /api/all-user-faces for their own login
     * 
     * Admin SHOULD be able to:
     * - Use face scan APIs to capture/update user face photos
     * - Access /api/face-scan/* endpoints for user management
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Allow face scan APIs for admin (needed for user face photo management)
        // These are used when admin creates/edits user face photos
        $path = $request->path();
        
        // Allow face-scan APIs (used for 360° scan when admin manages users)
        if (str_starts_with($path, 'api/face-scan')) {
            return $next($request);
        }
        
        // Block face LOGIN APIs for admin
        if (Auth::guard('admin')->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Face recognition login is not available for admin accounts.',
                'code' => 'ADMIN_BLOCKED',
            ], 403);
        }

        return $next($request);
    }
}
