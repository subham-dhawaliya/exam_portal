<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FaceCapture;
use App\Models\FaceVerificationLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FaceCaptureController extends Controller
{
    public function index(Request $request)
    {
        $query = FaceCapture::with('user');

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->capture_type) {
            $query->where('capture_type', $request->capture_type);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $captures = $query->latest()->paginate(20);

        return view('admin.face-captures.index', compact('captures'));
    }

    public function show(FaceCapture $faceCapture)
    {
        $faceCapture->load(['user', 'examAttempt.exam']);
        return view('admin.face-captures.show', compact('faceCapture'));
    }

    public function destroy(FaceCapture $faceCapture)
    {
        // Delete the image file from storage
        if ($faceCapture->image_path && Storage::disk('public')->exists($faceCapture->image_path)) {
            Storage::disk('public')->delete($faceCapture->image_path);
        }

        $faceCapture->delete();

        return redirect()->route('admin.face-captures.index')
            ->with('success', 'Face capture deleted successfully.');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:face_captures,id'
        ]);

        $captures = FaceCapture::whereIn('id', $request->ids)->get();

        foreach ($captures as $capture) {
            if ($capture->image_path && Storage::disk('public')->exists($capture->image_path)) {
                Storage::disk('public')->delete($capture->image_path);
            }
            $capture->delete();
        }

        return redirect()->route('admin.face-captures.index')
            ->with('success', count($request->ids) . ' face captures deleted successfully.');
    }

    public function verificationLogs(Request $request)
    {
        $query = FaceVerificationLog::with('user');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->verification_type) {
            $query->where('verification_type', $request->verification_type);
        }

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->latest()->paginate(20);

        // Stats
        $stats = [
            'total' => FaceVerificationLog::count(),
            'success' => FaceVerificationLog::where('status', 'success')->count(),
            'failed' => FaceVerificationLog::where('status', 'failed')->count(),
            'blocked' => FaceVerificationLog::where('status', 'blocked')->count(),
            'spoof_detected' => FaceVerificationLog::where('status', 'spoof_detected')->count(),
        ];

        return view('admin.face-verification-logs.index', compact('logs', 'stats'));
    }
}
