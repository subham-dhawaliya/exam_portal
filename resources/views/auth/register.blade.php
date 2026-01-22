@extends('layouts.app')

@section('title', 'Register - Exam Portal')

@push('styles')
<style>
    .face-scan-container {
        position: relative;
        width: 280px;
        height: 360px;
    }
    .face-oval {
        width: 100%;
        height: 100%;
        border: 3px solid rgba(255,255,255,0.3);
        border-radius: 50%;
        position: relative;
        box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.6);
    }
    /* iPhone style progress ring */
    .progress-ring-svg {
        position: absolute;
        top: -10px;
        left: -10px;
        width: 300px;
        height: 380px;
        transform: rotate(-90deg);
    }
    .progress-ring-bg {
        fill: none;
        stroke: rgba(255,255,255,0.2);
        stroke-width: 10;
    }
    .progress-ring-fill {
        fill: none;
        stroke: #34C759;
        stroke-width: 10;
        stroke-linecap: round;
        transition: stroke-dashoffset 0.5s ease-out;
        filter: drop-shadow(0 0 8px rgba(52, 199, 89, 0.7));
    }
    /* Direction arrow indicator */
    .direction-arrow {
        position: absolute;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #34C759;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
        box-shadow: 0 4px 15px rgba(52, 199, 89, 0.5);
        transition: all 0.3s ease;
        animation: pulse-arrow 1.5s ease-in-out infinite;
    }
    .direction-arrow.hidden { display: none; }
    .direction-arrow.completed {
        background: #34C759;
        animation: none;
        transform: scale(1.1);
    }
    @keyframes pulse-arrow {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.15); opacity: 0.8; }
    }
    /* Percentage display */
    .scan-percentage {
        position: absolute;
        bottom: -50px;
        left: 50%;
        transform: translateX(-50%);
        font-size: 32px;
        font-weight: 700;
        color: #34C759;
        text-shadow: 0 2px 10px rgba(52, 199, 89, 0.4);
    }
    /* Checkmarks for completed directions */
    .direction-checks {
        position: absolute;
        bottom: -90px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        gap: 12px;
    }
    .check-item {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        transition: all 0.3s ease;
    }
    .check-item.pending {
        background: rgba(255,255,255,0.15);
        color: rgba(255,255,255,0.4);
    }
    .check-item.done {
        background: #34C759;
        color: white;
        animation: check-pop 0.3s ease;
    }
    @keyframes check-pop {
        0% { transform: scale(0.5); }
        50% { transform: scale(1.2); }
        100% { transform: scale(1); }
    }
    /* Scanning pulse effect */
    .scan-pulse {
        position: absolute;
        inset: -20px;
        border: 2px solid #34C759;
        border-radius: 50%;
        opacity: 0;
        animation: scan-pulse 2s ease-out infinite;
    }
    @keyframes scan-pulse {
        0% { transform: scale(0.9); opacity: 0.8; }
        100% { transform: scale(1.1); opacity: 0; }
    }
</style>
@endpush

@section('body')
<div class="min-h-full flex items-center justify-center py-8 px-4">
    <div class="max-w-md w-full space-y-6">
        
        <!-- STEP 1: Face ID Scan First -->
        <div id="step1">
            <div class="text-center">
                <div class="mx-auto w-20 h-20 bg-gradient-to-br from-ios-green to-ios-teal rounded-[24px] flex items-center justify-center shadow-lg mb-6">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-gray-900">Create Account</h2>
                <p class="mt-2 text-ios-gray">First, let's scan your face</p>
            </div>

            <div class="glass-card p-6 mt-6">
                <div class="relative rounded-2xl overflow-hidden bg-gray-900 aspect-[4/5]">
                    <video id="video" class="w-full h-full object-cover" autoplay playsinline muted></video>
                    <canvas id="canvas" class="hidden"></canvas>
                    
                    <!-- Face Guide Overlay - iPhone Style -->
                    <div id="faceGuide" class="absolute inset-0 flex items-center justify-center pointer-events-none hidden">
                        <div class="face-scan-container">
                            <!-- Pulse effect -->
                            <div class="scan-pulse" id="scanPulse"></div>
                            
                            <!-- Progress Ring SVG -->
                            <svg class="progress-ring-svg" viewBox="0 0 300 380">
                                <ellipse class="progress-ring-bg" cx="150" cy="190" rx="140" ry="180"/>
                                <ellipse id="progressRing" class="progress-ring-fill" cx="150" cy="190" rx="140" ry="180"
                                    stroke-dasharray="1005" stroke-dashoffset="1005"/>
                            </svg>
                            
                            <!-- Face oval guide -->
                            <div class="face-oval" id="faceOval"></div>
                            
                            <!-- Direction Arrow (shows current direction to turn) -->
                            <div id="directionArrow" class="direction-arrow hidden" style="top: -30px; left: 50%; transform: translateX(-50%);">
                                <span id="arrowIcon">↑</span>
                            </div>
                            
                            <!-- Percentage Display -->
                            <div class="scan-percentage" id="scanPercentage">0%</div>
                            
                            <!-- Direction Checkmarks -->
                            <div class="direction-checks">
                                <div id="checkLeft" class="check-item pending" title="Left">←</div>
                                <div id="checkRight" class="check-item pending" title="Right">→</div>
                                <div id="checkUp" class="check-item pending" title="Up">↑</div>
                                <div id="checkDown" class="check-item pending" title="Down">↓</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Start Overlay -->
                    <div id="startOverlay" class="absolute inset-0 flex items-center justify-center bg-gradient-to-br from-gray-100 to-white cursor-pointer" onclick="startFaceScan()">
                        <div class="text-center">
                            <div class="w-20 h-20 mx-auto bg-gradient-to-br from-ios-green to-ios-teal rounded-3xl flex items-center justify-center mb-4 shadow-lg">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </div>
                            <p class="text-gray-700 font-semibold text-lg">Tap to Start Face Scan</p>
                            <p class="text-ios-gray text-sm mt-1">Move your head in all directions</p>
                        </div>
                    </div>
                    
                    <!-- Status -->
                    <div id="scanStatus" class="absolute bottom-4 left-4 right-4 text-center hidden">
                        <div class="bg-black/70 rounded-xl px-4 py-3">
                            <p id="statusText" class="text-white font-medium"></p>
                            <p id="progressText" class="text-white/70 text-sm mt-1"></p>
                        </div>
                    </div>
                    
                    <!-- Loading -->
                    <div id="loadingOverlay" class="absolute inset-0 bg-black/80 flex items-center justify-center hidden">
                        <div class="text-center text-white">
                            <svg class="w-12 h-12 mx-auto animate-spin mb-3" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <p>Loading Face ID...</p>
                        </div>
                    </div>
                    
                    <!-- Success -->
                    <div id="successOverlay" class="absolute inset-0 bg-ios-green flex items-center justify-center hidden">
                        <div class="text-center text-white">
                            <div class="w-24 h-24 mx-auto bg-white rounded-full flex items-center justify-center mb-4">
                                <svg class="w-14 h-14 text-ios-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <p class="font-bold text-2xl">Face Captured!</p>
                            <p class="opacity-80 mt-1">Now fill in your details</p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div id="statusDot" class="w-3 h-3 rounded-full bg-gray-300"></div>
                        <span id="statusLabel" class="text-sm text-ios-gray">Ready to scan</span>
                    </div>
                    <button type="button" id="retakeBtn" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-xl text-sm font-medium hidden" onclick="retakeScan()">Retake</button>
                </div>
            </div>
        </div>

        <!-- STEP 2: Details Form (after face scan) -->
        <div id="step2" class="hidden">
            <div class="text-center mb-6">
                <div class="mx-auto w-20 h-20 bg-ios-green rounded-full flex items-center justify-center shadow-lg mb-4">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900">Almost Done!</h2>
                <p class="text-ios-gray mt-1">Fill in your details to complete</p>
            </div>

            <div class="glass-card p-6">
                @if($errors->has('face_duplicate'))
                    <div class="mb-4 p-4 bg-ios-red/10 rounded-xl border border-ios-red/30">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-ios-red rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-ios-red font-semibold">Face Already Registered!</p>
                                <p class="text-ios-red/80 text-sm mt-1">{{ $errors->first('face_duplicate') }}</p>
                                <a href="{{ route('login') }}" class="inline-flex items-center gap-1 mt-2 text-ios-blue text-sm font-medium">
                                    <span>Login to your existing account</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @elseif($errors->any())
                    <div class="mb-4 p-3 bg-ios-red/10 rounded-xl">
                        @foreach($errors->all() as $error)
                            <p class="text-ios-red text-sm">{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" id="registerForm">
                    @csrf
                    <input type="hidden" name="face_image" id="faceImage">
                    <input type="hidden" name="face_descriptor" id="faceDescriptor">
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                            <input type="text" name="name" value="{{ old('name') }}" required class="ios-input" placeholder="Your name">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" required class="ios-input" placeholder="you@example.com">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                                <input type="password" name="password" id="password" class="ios-input @error('password') border-ios-red @enderror" placeholder="••••••••">
                                <p id="passwordError" class="text-ios-red text-sm mt-1 hidden"></p>
                                @error('password')
                                    <p class="text-ios-red text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Confirm</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="ios-input" placeholder="••••••••">
                                <p id="confirmError" class="text-ios-red text-sm mt-1 hidden"></p>
                            </div>
                        </div>

                        <!-- Face Preview -->
                        <div class="flex items-center gap-3 p-3 bg-ios-green/10 rounded-xl">
                            <div class="w-12 h-12 rounded-xl overflow-hidden bg-gray-200">
                                <img id="facePreview" class="w-full h-full object-cover" alt="Your face">
                            </div>
                            <div class="flex-1">
                                <p class="text-ios-green font-medium text-sm">Face ID Enrolled ✓</p>
                                <p class="text-xs text-ios-gray">Your face has been captured</p>
                            </div>
                            <button type="button" onclick="backToFaceScan()" class="text-ios-blue text-sm font-medium">Change</button>
                        </div>

                        <button type="submit" class="w-full ios-btn bg-gradient-to-r from-ios-green to-ios-teal text-white" onclick="return validateRegisterForm()">
                            Create Account
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <p class="text-center text-gray-600">Already have an account? <a href="{{ route('login') }}" class="text-ios-blue font-semibold">Sign in</a></p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
<script>
const MODEL_URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model';
let stream = null, modelsLoaded = false, isScanning = false, scanComplete = false;
let directions = { left: false, right: false, up: false, down: false };
let allDirectionsDone = false;
let centerStableFrames = 0;
const REQUIRED_CENTER_FRAMES = 15;
const TOTAL_PROGRESS = 1005; // Ellipse circumference (updated for larger size)

// Direction sequence for iPhone-style scanning
const directionSequence = ['left', 'right', 'up', 'down'];
let currentDirectionIndex = 0;

// Secure scan session
let scanToken = null;
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content;

async function loadModels() {
    document.getElementById('loadingOverlay').classList.remove('hidden');
    try {
        await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
        await faceapi.nets.faceLandmark68TinyNet.loadFromUri(MODEL_URL);
        modelsLoaded = true;
    } catch(e) { console.error(e); }
    document.getElementById('loadingOverlay').classList.add('hidden');
}

// Start secure scan session
async function startSecureScan() {
    try {
        const response = await fetch('/api/face-scan/start', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
            body: JSON.stringify({ scan_type: 'registration' })
        });
        const data = await response.json();
        if (data.success) {
            scanToken = data.token;
            return true;
        } else {
            alert(data.message || 'Could not start scan session');
            return false;
        }
    } catch(e) {
        console.error('Failed to start secure scan:', e);
        return false;
    }
}

// Update scan progress on server
async function updateScanProgress() {
    if (!scanToken) return;
    const completedDirs = Object.entries(directions).filter(([k,v]) => v).map(([k]) => k);
    try {
        await fetch('/api/face-scan/progress', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
            body: JSON.stringify({ token: scanToken, directions: completedDirs })
        });
    } catch(e) { console.error('Progress update failed:', e); }
}

// Validate capture is allowed
async function validateCapture() {
    if (!scanToken) return { success: false, message: 'No scan session' };
    try {
        const response = await fetch('/api/face-scan/validate-capture', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
            body: JSON.stringify({ token: scanToken })
        });
        return await response.json();
    } catch(e) {
        return { success: false, message: 'Validation failed' };
    }
}

// Complete scan session
async function completeScanSession() {
    if (!scanToken) return;
    try {
        await fetch('/api/face-scan/complete', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
            body: JSON.stringify({ token: scanToken })
        });
        scanToken = null;
    } catch(e) { console.error('Complete scan failed:', e); }
}

// Abort scan session
async function abortScanSession() {
    if (!scanToken) return;
    try {
        await fetch('/api/face-scan/abort', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
            body: JSON.stringify({ token: scanToken })
        });
        scanToken = null;
    } catch(e) { console.error('Abort scan failed:', e); }
}

// Check for duplicate face in database
async function checkDuplicateFace(descriptor) {
    try {
        const response = await fetch('/api/face-scan/check-duplicate', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
            body: JSON.stringify({ face_descriptor: descriptor })
        });
        const data = await response.json();
        return {
            is_duplicate: data.is_duplicate || false,
            matched_email: data.matched_email || '',
            similarity_score: data.similarity_score || 0
        };
    } catch(e) {
        console.error('Duplicate check failed:', e);
        return { is_duplicate: false };
    }
}

// Update progress ring and percentage - iPhone style
function updateProgressUI() {
    const completedCount = Object.values(directions).filter(v => v).length;
    let percentage = (completedCount / 4) * 80; // 80% for directions
    
    if (allDirectionsDone) {
        percentage = 80 + (centerStableFrames / REQUIRED_CENTER_FRAMES) * 20; // Last 20% for centering
    }
    
    percentage = Math.min(100, Math.round(percentage));
    
    // Update percentage text
    document.getElementById('scanPercentage').textContent = percentage + '%';
    
    // Update progress ring
    const offset = TOTAL_PROGRESS - (TOTAL_PROGRESS * percentage / 100);
    document.getElementById('progressRing').style.strokeDashoffset = offset;
    
    // Update progress text
    if (!allDirectionsDone) {
        document.getElementById('progressText').textContent = `${completedCount}/4 directions scanned`;
    } else {
        document.getElementById('progressText').textContent = 'Capturing face...';
    }
}

// Show direction arrow at correct position
function showDirectionArrow(direction) {
    const arrow = document.getElementById('directionArrow');
    const icon = document.getElementById('arrowIcon');
    
    arrow.classList.remove('hidden');
    
    // Reset all positions
    arrow.style.top = 'auto';
    arrow.style.bottom = 'auto';
    arrow.style.left = 'auto';
    arrow.style.right = 'auto';
    
    if (direction === 'left') {
        arrow.style.top = '50%';
        arrow.style.left = '-30px';
        arrow.style.transform = 'translateY(-50%)';
        icon.textContent = '←';
    } else if (direction === 'right') {
        arrow.style.top = '50%';
        arrow.style.right = '-30px';
        arrow.style.transform = 'translateY(-50%)';
        icon.textContent = '→';
    } else if (direction === 'up') {
        arrow.style.top = '-30px';
        arrow.style.left = '50%';
        arrow.style.transform = 'translateX(-50%)';
        icon.textContent = '↑';
    } else if (direction === 'down') {
        arrow.style.bottom = '-30px';
        arrow.style.left = '50%';
        arrow.style.transform = 'translateX(-50%)';
        icon.textContent = '↓';
    }
}

// Mark direction as complete
function markDirectionComplete(direction) {
    const checkId = 'check' + direction.charAt(0).toUpperCase() + direction.slice(1);
    const checkEl = document.getElementById(checkId);
    checkEl.classList.remove('pending');
    checkEl.classList.add('done');
    checkEl.innerHTML = '✓';
    
    // Update server progress
    updateScanProgress();
    
    // Update UI
    updateProgressUI();
    
    // Move to next direction
    currentDirectionIndex++;
    if (currentDirectionIndex < directionSequence.length) {
        showDirectionArrow(directionSequence[currentDirectionIndex]);
    } else {
        document.getElementById('directionArrow').classList.add('hidden');
    }
}

async function startFaceScan() {
    try {
        // Start secure session first
        const sessionStarted = await startSecureScan();
        if (!sessionStarted) return;
        
        stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user', width: 640, height: 480 } });
        document.getElementById('video').srcObject = stream;
        document.getElementById('video').onloadedmetadata = async () => {
            document.getElementById('startOverlay').classList.add('hidden');
            document.getElementById('faceGuide').classList.remove('hidden');
            document.getElementById('scanStatus').classList.remove('hidden');
            document.getElementById('statusDot').className = 'w-3 h-3 rounded-full bg-ios-green animate-pulse';
            document.getElementById('statusLabel').textContent = 'Scanning...';
            
            // Show first direction arrow
            showDirectionArrow(directionSequence[0]);
            
            if (!modelsLoaded) await loadModels();
            if (modelsLoaded) { isScanning = true; detectFace(); }
        };
    } catch(e) {
        alert('Camera access denied!');
        await abortScanSession();
    }
}

async function detectFace() {
    if (!isScanning || scanComplete) return;
    const video = document.getElementById('video');
    try {
        // Detect ALL faces to check for multiple people
        const detections = await faceapi.detectAllFaces(video, new faceapi.TinyFaceDetectorOptions({ scoreThreshold: 0.3 })).withFaceLandmarks(true);
        
        // Check for multiple faces - BLOCK if more than 1 face
        if (detections.length > 1) {
            document.getElementById('statusText').textContent = '⚠️ Multiple faces detected!';
            document.getElementById('progressText').textContent = 'Only one person allowed';
            centerStableFrames = 0;
            requestAnimationFrame(detectFace);
            return;
        }
        
        if (detections.length === 1) {
            const detection = detections[0];
            const nose = detection.landmarks.getNose()[3];
            const centerX = video.videoWidth / 2;
            const centerY = video.videoHeight / 2;
            const offsetX = (nose.x - centerX) / centerX;
            const offsetY = (nose.y - centerY) / centerY;
            
            // Phase 1: Scan all 4 directions in sequence
            if (!allDirectionsDone) {
                const currentDir = directionSequence[currentDirectionIndex];
                
                // Check if current direction is achieved
                let directionAchieved = false;
                if (currentDir === 'left' && offsetX < -0.20) directionAchieved = true;
                if (currentDir === 'right' && offsetX > 0.20) directionAchieved = true;
                if (currentDir === 'up' && offsetY < -0.15) directionAchieved = true;
                if (currentDir === 'down' && offsetY > 0.15) directionAchieved = true;
                
                if (directionAchieved && !directions[currentDir]) {
                    directions[currentDir] = true;
                    markDirectionComplete(currentDir);
                }
                
                // Check if all directions done
                if (directions.left && directions.right && directions.up && directions.down) {
                    allDirectionsDone = true;
                }
                
                // Update status text
                const msgs = { 
                    left: '← Turn head LEFT', 
                    right: '→ Turn head RIGHT', 
                    up: '↑ Tilt head UP', 
                    down: '↓ Tilt head DOWN' 
                };
                if (!allDirectionsDone && currentDirectionIndex < directionSequence.length) {
                    document.getElementById('statusText').textContent = msgs[directionSequence[currentDirectionIndex]];
                }
            } 
            // Phase 2: Wait for face to be centered, then capture
            else {
                const isCentered = Math.abs(offsetX) < 0.12 && Math.abs(offsetY) < 0.12;
                if (isCentered) {
                    centerStableFrames++;
                    updateProgressUI();
                    document.getElementById('statusText').textContent = `👤 Hold still...`;
                    
                    if (centerStableFrames >= REQUIRED_CENTER_FRAMES) {
                        completeScan();
                        return;
                    }
                } else {
                    centerStableFrames = Math.max(0, centerStableFrames - 2);
                    updateProgressUI();
                    document.getElementById('statusText').textContent = '👤 Now look straight at camera';
                    document.getElementById('progressText').textContent = 'Center your face to capture';
                }
            }
        } else {
            document.getElementById('statusText').textContent = '🔍 Position your face in the frame';
            centerStableFrames = 0;
        }
    } catch(e) { console.error(e); }
    if (isScanning && !scanComplete) requestAnimationFrame(detectFace);
}

function markDirection(id) {
    // This function is no longer used - replaced by markDirectionComplete
}

function updateProgress() {
    // This function is no longer used - replaced by updateProgressUI
}

function updateStatus() {
    // This function is no longer used - status is updated in detectFace
}

async function completeScan() {
    // Validate capture is allowed (server-side check)
    const validation = await validateCapture();
    if (!validation.success) {
        document.getElementById('statusText').textContent = '⚠️ ' + validation.message;
        document.getElementById('progressText').textContent = 'Please try again';
        centerStableFrames = 0;
        if (validation.code === 'EXPIRED' || validation.code === 'NO_SESSION') {
            setTimeout(() => retakeScan(), 2000);
        }
        return;
    }
    
    isScanning = false;
    
    // Set to 100%
    document.getElementById('scanPercentage').textContent = '100%';
    document.getElementById('progressRing').style.strokeDashoffset = 0;
    
    const canvas = document.getElementById('canvas');
    const video = document.getElementById('video');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);
    
    // Generate face descriptor for fast login later
    document.getElementById('statusText').textContent = '🔐 Generating Face ID...';
    document.getElementById('progressText').textContent = 'Checking face uniqueness...';
    
    let descriptorArray = null;
    
    try {
        // Load face recognition model if not loaded
        if (!faceapi.nets.faceRecognitionNet.isLoaded) {
            await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);
        }
        
        const detection = await faceapi.detectSingleFace(canvas, new faceapi.TinyFaceDetectorOptions({ inputSize: 416 }))
            .withFaceLandmarks(true)
            .withFaceDescriptor();
        
        if (detection) {
            // Convert Float32Array to regular array for JSON
            descriptorArray = Array.from(detection.descriptor);
            console.log('Face descriptor generated:', descriptorArray.length, 'dimensions');
            
            // CHECK FOR DUPLICATE FACE before proceeding
            document.getElementById('statusText').textContent = '🔍 Checking if face exists...';
            
            const duplicateCheck = await checkDuplicateFace(descriptorArray);
            
            if (duplicateCheck.is_duplicate) {
                // DUPLICATE FOUND - Show error and reset
                document.getElementById('statusText').textContent = '⚠️ Face Already Registered!';
                document.getElementById('progressText').textContent = duplicateCheck.matched_email;
                document.getElementById('scanPercentage').textContent = '❌';
                document.getElementById('progressRing').style.stroke = '#FF3B30';
                
                // Show alert with details
                setTimeout(() => {
                    alert('This face is already registered with account: ' + duplicateCheck.matched_email + '\n\nEach person can only have one account. Please login instead.');
                    retakeScan();
                }, 1000);
                
                return;
            }
            
            // Face is unique - proceed
            document.getElementById('faceDescriptor').value = JSON.stringify(descriptorArray);
        }
    } catch(e) {
        console.error('Face descriptor generation failed:', e);
    }
    
    // Mark scan as complete only after duplicate check passes
    scanComplete = true;
    
    // Compress and save
    const fullImage = canvas.toDataURL('image/jpeg', 0.8);
    const img = new Image();
    img.onload = async () => {
        const c = document.createElement('canvas');
        c.width = 300; c.height = img.height * (300/img.width);
        c.getContext('2d').drawImage(img, 0, 0, c.width, c.height);
        const compressed = c.toDataURL('image/jpeg', 0.6);
        document.getElementById('faceImage').value = compressed;
        document.getElementById('facePreview').src = compressed;
        
        // Complete the secure session
        await completeScanSession();
    };
    img.src = fullImage;
    
    document.getElementById('faceGuide').classList.add('hidden');
    document.getElementById('scanStatus').classList.add('hidden');
    document.getElementById('successOverlay').classList.remove('hidden');
    document.getElementById('retakeBtn').classList.remove('hidden');
    document.getElementById('statusDot').className = 'w-3 h-3 rounded-full bg-ios-green';
    document.getElementById('statusLabel').textContent = 'Face captured ✓';
    
    if (stream) stream.getTracks().forEach(t => t.stop());
    
    // Show details form after 1.5 seconds
    setTimeout(() => {
        document.getElementById('step1').classList.add('hidden');
        document.getElementById('step2').classList.remove('hidden');
    }, 1500);
}

async function retakeScan() {
    // Abort current session
    await abortScanSession();
    
    scanComplete = false;
    allDirectionsDone = false;
    directions = { left: false, right: false, up: false, down: false };
    centerStableFrames = 0;
    currentDirectionIndex = 0;
    
    document.getElementById('faceImage').value = '';
    document.getElementById('faceDescriptor').value = '';
    document.getElementById('retakeBtn').classList.add('hidden');
    document.getElementById('successOverlay').classList.add('hidden');
    document.getElementById('startOverlay').classList.remove('hidden');
    document.getElementById('progressRing').style.strokeDashoffset = TOTAL_PROGRESS;
    document.getElementById('progressRing').style.stroke = '#34C759'; // Reset to green
    document.getElementById('scanPercentage').textContent = '0%';
    document.getElementById('directionArrow').classList.add('hidden');
    
    // Reset checkmarks
    ['Left','Right','Up','Down'].forEach(dir => {
        const el = document.getElementById('check' + dir);
        el.classList.remove('done');
        el.classList.add('pending');
        el.innerHTML = dir === 'Left' ? '←' : dir === 'Right' ? '→' : dir === 'Up' ? '↑' : '↓';
    });
    
    document.getElementById('statusDot').className = 'w-3 h-3 rounded-full bg-gray-300';
    document.getElementById('statusLabel').textContent = 'Ready to scan';
}

async function backToFaceScan() {
    await abortScanSession();
    document.getElementById('step2').classList.add('hidden');
    document.getElementById('step1').classList.remove('hidden');
    retakeScan();
}

window.addEventListener('beforeunload', async () => { 
    await abortScanSession();
    if (stream) stream.getTracks().forEach(t => t.stop()); 
});

// Password validation before form submit
function validateRegisterForm() {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('password_confirmation').value;
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('password_confirmation');
    const passwordError = document.getElementById('passwordError');
    const confirmError = document.getElementById('confirmError');
    
    // Reset errors
    passwordError.classList.add('hidden');
    confirmError.classList.add('hidden');
    passwordInput.classList.remove('border-ios-red');
    confirmInput.classList.remove('border-ios-red');
    
    let isValid = true;
    
    // Check if password is empty
    if (!password) {
        passwordError.textContent = 'Password is required.';
        passwordError.classList.remove('hidden');
        passwordInput.classList.add('border-ios-red');
        isValid = false;
    }
    // Check minimum length (8 characters)
    else if (password.length < 8) {
        passwordError.textContent = 'Password must be at least 8 characters.';
        passwordError.classList.remove('hidden');
        passwordInput.classList.add('border-ios-red');
        isValid = false;
    }
    
    // Check if confirm password is empty
    if (!confirmPassword) {
        confirmError.textContent = 'Please confirm your password.';
        confirmError.classList.remove('hidden');
        confirmInput.classList.add('border-ios-red');
        isValid = false;
    }
    // Check if passwords match
    else if (password !== confirmPassword) {
        confirmError.textContent = 'Passwords do not match.';
        confirmError.classList.remove('hidden');
        confirmInput.classList.add('border-ios-red');
        isValid = false;
    }
    
    return isValid;
}
</script>
@endsection
