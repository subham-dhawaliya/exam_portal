<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ExamController as AdminExamController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\FaceCaptureController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\User\DashboardController as UserDashboard;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\ExamController as UserExamController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

// Public routes
Route::get('/', fn() => redirect()->route('login'));

// API route for getting user's reference face
// EXCLUDES admin role users - admins cannot use face login
Route::get('/api/user-face', function (Request $request) {
    $email = $request->query('email');
    if (!$email) {
        return response()->json(['success' => false, 'message' => 'Email required']);
    }
    
    $user = \App\Models\User::with('role')->where('email', $email)->first();
    if (!$user) {
        return response()->json(['success' => false, 'message' => 'User not found']);
    }
    
    // Block admin users from face login
    if ($user->role && $user->role->slug === 'admin') {
        return response()->json(['success' => false, 'message' => 'Admin users must use /admin/login']);
    }
    
    // Get the latest face capture (registration first, then login)
    $faceCapture = \App\Models\FaceCapture::where('user_id', $user->id)
        ->where('capture_type', 'registration')
        ->latest()
        ->first();
    
    // If no registration capture, try login capture
    if (!$faceCapture) {
        $faceCapture = \App\Models\FaceCapture::where('user_id', $user->id)
            ->where('capture_type', 'login')
            ->latest()
            ->first();
    }
    
    if (!$faceCapture || !Storage::disk('public')->exists($faceCapture->image_path)) {
        return response()->json(['success' => false, 'message' => 'No face data found']);
    }
    
    return response()->json([
        'success' => true,
        'face_url' => url('/api/face-image/' . $faceCapture->id)
    ]);
});

// API route for getting ALL users' faces (for face recognition login)
// EXCLUDES admin role users - admins must use /admin/login
Route::get('/api/all-user-faces', function () {
    // Get users with pre-computed face descriptors (FAST!)
    $users = \App\Models\User::with('role')
        ->whereNotNull('face_descriptor')
        ->whereHas('role', function($q) {
            $q->where('slug', '!=', 'admin');
        })
        ->where('status', 'active')
        ->get();
    
    $result = [];
    
    foreach ($users as $user) {
        // Skip admin users
        if ($user->role && $user->role->slug === 'admin') {
            continue;
        }
        $result[] = [
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'descriptor' => $user->face_descriptor // Pre-computed descriptor
        ];
    }
    
    return response()->json([
        'success' => true,
        'users' => $result,
        'count' => count($result),
        'precomputed' => true
    ]);
});

// Direct image serving route (bypasses symlink issues)
Route::get('/api/face-image/{id}', function ($id) {
    $capture = \App\Models\FaceCapture::find($id);
    
    if (!$capture || !Storage::disk('public')->exists($capture->image_path)) {
        abort(404);
    }
    
    $file = Storage::disk('public')->get($capture->image_path);
    $mimeType = Storage::disk('public')->mimeType($capture->image_path);
    
    return response($file, 200)->header('Content-Type', $mimeType);
});

// Direct user reference face image serving route
Route::get('/api/user-reference-face/{userId}', function ($userId) {
    $user = \App\Models\User::find($userId);
    
    if (!$user || !$user->reference_face_image || !Storage::disk('public')->exists($user->reference_face_image)) {
        abort(404);
    }
    
    $file = Storage::disk('public')->get($user->reference_face_image);
    $mimeType = Storage::disk('public')->mimeType($user->reference_face_image);
    
    return response($file, 200)->header('Content-Type', $mimeType);
});

// Direct profile image serving route
Route::get('/api/profile-image/{userId}', function ($userId) {
    $user = \App\Models\User::find($userId);
    
    if (!$user || !$user->profile_image || !Storage::disk('public')->exists($user->profile_image)) {
        abort(404);
    }
    
    $file = Storage::disk('public')->get($user->profile_image);
    $mimeType = Storage::disk('public')->mimeType($user->profile_image);
    
    return response($file, 200)->header('Content-Type', $mimeType);
});

// Face Scan Lock API Routes (for 360° scan security)
// Face Scan Lock API Routes (for 360° scan security)
// These APIs are used for 360° face scanning during registration and user management
// Admin can use these to capture user face photos, but cannot use face LOGIN
Route::prefix('api/face-scan')->group(function () {
    Route::post('/start', [\App\Http\Controllers\Api\FaceScanController::class, 'startScan']);
    Route::post('/progress', [\App\Http\Controllers\Api\FaceScanController::class, 'updateProgress']);
    Route::post('/validate-capture', [\App\Http\Controllers\Api\FaceScanController::class, 'validateCapture']);
    Route::post('/complete', [\App\Http\Controllers\Api\FaceScanController::class, 'completeScan']);
    Route::post('/abort', [\App\Http\Controllers\Api\FaceScanController::class, 'abortScan']);
    Route::get('/status', [\App\Http\Controllers\Api\FaceScanController::class, 'getStatus']);
    Route::post('/check-duplicate', [\App\Http\Controllers\Api\FaceScanController::class, 'checkDuplicateFace']);
});

// User Auth routes (with face recognition)
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
    Route::post('verify-face', [LoginController::class, 'verifyFaceAjax'])->name('verify-face');
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);
});

Route::post('logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Admin Auth routes (email/password only - NO face recognition)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('admin.guest')->group(function () {
        Route::get('login', [\App\Http\Controllers\Admin\Auth\AdminLoginController::class, 'showLoginForm'])->name('login');
        Route::post('login', [\App\Http\Controllers\Admin\Auth\AdminLoginController::class, 'login'])->name('login.submit');
    });
    Route::post('logout', [\App\Http\Controllers\Admin\Auth\AdminLoginController::class, 'logout'])->name('logout')->middleware('admin.auth');
});

// Admin protected routes
Route::prefix('admin')->name('admin.')->middleware('admin.auth')->group(function () {
    Route::get('dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
    
    // Users
    Route::resource('users', UserController::class);
    Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    
    // Sections (Question Bank Categories)
    Route::resource('sections', \App\Http\Controllers\Admin\SectionController::class);
    Route::post('sections/reorder', [\App\Http\Controllers\Admin\SectionController::class, 'reorder'])->name('sections.reorder');
    Route::post('sections/{section}/toggle-status', [\App\Http\Controllers\Admin\SectionController::class, 'toggleStatus'])->name('sections.toggle-status');
    
    // Question Bank
    Route::resource('question-bank', \App\Http\Controllers\Admin\QuestionBankController::class);
    Route::post('question-bank/{questionBank}/duplicate', [\App\Http\Controllers\Admin\QuestionBankController::class, 'duplicate'])->name('question-bank.duplicate');
    Route::post('question-bank/{questionBank}/toggle-status', [\App\Http\Controllers\Admin\QuestionBankController::class, 'toggleStatus'])->name('question-bank.toggle-status');
    Route::post('question-bank/bulk-delete', [\App\Http\Controllers\Admin\QuestionBankController::class, 'bulkDelete'])->name('question-bank.bulk-delete');
    Route::get('question-bank/{questionBank}/preview', [\App\Http\Controllers\Admin\QuestionBankController::class, 'preview'])->name('question-bank.preview');
    
    // AI Question Generator
    Route::get('question-bank-ai/generate', [\App\Http\Controllers\Admin\QuestionBankController::class, 'aiGenerateForm'])->name('question-bank.ai-form');
    Route::post('question-bank-ai/generate', [\App\Http\Controllers\Admin\QuestionBankController::class, 'aiGenerate'])->name('question-bank.ai-generate');
    Route::post('question-bank-ai/save', [\App\Http\Controllers\Admin\QuestionBankController::class, 'aiSave'])->name('question-bank.ai-save');
    
    // Exams
    Route::resource('exams', AdminExamController::class);
    Route::get('exams/{exam}/results', [AdminExamController::class, 'results'])->name('exams.results');
    
    // Questions
    Route::resource('exams.questions', QuestionController::class)->except(['show']);
    Route::post('exams/{exam}/questions/reorder', [QuestionController::class, 'reorder'])->name('exams.questions.reorder');
    Route::get('exams/{exam}/questions/import-from-bank', [QuestionController::class, 'importFromBank'])->name('exams.questions.import-from-bank');
    Route::post('exams/{exam}/questions/import-from-bank', [QuestionController::class, 'storeFromBank'])->name('exams.questions.store-from-bank');
    
    // Face Captures
    Route::get('face-captures', [FaceCaptureController::class, 'index'])->name('face-captures.index');
    Route::get('face-captures/{faceCapture}', [FaceCaptureController::class, 'show'])->name('face-captures.show');
    Route::delete('face-captures/{faceCapture}', [FaceCaptureController::class, 'destroy'])->name('face-captures.destroy');
    Route::post('face-captures/bulk-delete', [FaceCaptureController::class, 'bulkDelete'])->name('face-captures.bulk-delete');
    
    // Face Verification Logs
    Route::get('face-verification-logs', [FaceCaptureController::class, 'verificationLogs'])->name('face-verification-logs.index');
    Route::post('users/{user}/re-enroll-face', [UserController::class, 'reEnrollFace'])->name('users.re-enroll-face');
    
    // Activity Logs
    Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
});

// User routes
Route::prefix('user')->name('user.')->middleware(['auth', 'user', 'status'])->group(function () {
    Route::get('dashboard', [UserDashboard::class, 'index'])->name('dashboard');
    
    // Profile
    Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('profile/face', [ProfileController::class, 'updateFace'])->name('profile.face');
    
    // Exams
    Route::get('exams', [UserExamController::class, 'index'])->name('exams.index');
    Route::get('exams/history', [UserExamController::class, 'history'])->name('exams.history');
    Route::get('exams/{exam}', [UserExamController::class, 'show'])->name('exams.show');
    Route::get('exams/{exam}/verify', [UserExamController::class, 'startVerification'])->name('exams.verify');
    Route::post('exams/{exam}/start', [UserExamController::class, 'start'])->name('exams.start');
    Route::get('attempts/{attempt}', [UserExamController::class, 'attempt'])->name('exams.attempt');
    Route::post('attempts/{attempt}/answer', [UserExamController::class, 'saveAnswer'])->name('exams.answer');
    Route::post('attempts/{attempt}/submit', [UserExamController::class, 'submit'])->name('exams.submit');
    Route::get('attempts/{attempt}/result', [UserExamController::class, 'result'])->name('exams.result');
    Route::post('attempts/{attempt}/tab-switch', [UserExamController::class, 'logTabSwitch'])->name('exams.tab-switch');
    Route::post('attempts/{attempt}/proctor-log', [UserExamController::class, 'logProctoring'])->name('exams.proctor-log');
});
