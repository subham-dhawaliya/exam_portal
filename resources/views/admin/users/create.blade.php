@extends('layouts.admin')

@section('title', 'Create User - Admin')

@push('styles')
<style>
    .face-circle { 
        width: 180px; height: 230px; 
        border: 4px solid rgba(255,255,255,0.3); 
        border-radius: 50%; 
        position: relative;
    }
    .face-circle::before {
        content: ''; position: absolute; inset: -8px;
        border: 4px solid transparent; border-radius: 50%;
        border-top-color: #34C759;
        animation: none;
    }
    .face-circle.scanning::before { animation: spin 2s linear infinite; }
    @keyframes spin { to { transform: rotate(360deg); } }
    
    .progress-ring { transform: rotate(-90deg); }
    .progress-ring circle, .progress-ring ellipse { 
        fill: none; stroke-width: 5;
        stroke-linecap: round; transition: stroke-dashoffset 0.3s;
    }
    .direction-indicator {
        position: absolute; width: 32px; height: 32px;
        border-radius: 50%; display: flex; align-items: center; justify-content: center;
        font-size: 16px; transition: all 0.3s;
    }
    .direction-indicator.active { background: #34C759; transform: scale(1.2); }
    .direction-indicator.done { background: #34C759; color: white; }
    .direction-indicator.pending { background: rgba(255,255,255,0.2); color: white; }
</style>
@endpush

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center space-x-4">
        <a href="{{ route('admin.users.index') }}" class="p-2 hover:bg-ios-gray-6 rounded-xl transition-colors">
            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Create User</h1>
            <p class="text-ios-gray mt-1">Add a new student account with Face ID enrollment</p>
        </div>
    </div>

    <!-- Form -->
    <div class="ios-card p-6">
        <form method="POST" action="{{ route('admin.users.store') }}" id="createUserForm" class="space-y-6">
            @csrf

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="ios-input" placeholder="Enter full name">
                    @error('name')<p class="mt-1 text-sm text-ios-red">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="ios-input" placeholder="Enter email">
                    @error('email')<p class="mt-1 text-sm text-ios-red">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input type="password" name="password" required class="ios-input" placeholder="Min 8 characters">
                    @error('password')<p class="mt-1 text-sm text-ios-red">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                    <input type="password" name="password_confirmation" required class="ios-input" placeholder="Confirm password">
                </div>
            </div>

            <!-- 360° Face ID Scan Section -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-ios-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        Face ID Scan (Required)
                    </span>
                </label>
                <p class="text-xs text-ios-gray mb-3">User must move head in all directions for secure enrollment</p>

                <div class="relative rounded-2xl overflow-hidden bg-gray-900 aspect-video">
                    <video id="video" class="w-full h-full object-cover" autoplay playsinline muted></video>
                    <canvas id="canvas" class="hidden"></canvas>
                    
                    <!-- Face Guide Overlay -->
                    <div id="faceGuide" class="absolute inset-0 flex items-center justify-center pointer-events-none hidden">
                        <div class="relative">
                            <!-- Progress Ring -->
                            <svg class="progress-ring absolute -inset-3" width="220" height="270" viewBox="0 0 220 270">
                                <ellipse cx="110" cy="135" rx="95" ry="120" stroke="rgba(255,255,255,0.2)" stroke-width="5" fill="none"/>
                                <ellipse id="progressRing" cx="110" cy="135" rx="95" ry="120" stroke="#34C759" stroke-width="5" fill="none"
                                    stroke-dasharray="680" stroke-dashoffset="680"/>
                            </svg>
                            <div class="face-circle" id="faceCircle"></div>
                            
                            <!-- Direction Indicators -->
                            <div id="dirUp" class="direction-indicator pending" style="top:-25px;left:50%;transform:translateX(-50%)">↑</div>
                            <div id="dirDown" class="direction-indicator pending" style="bottom:-25px;left:50%;transform:translateX(-50%)">↓</div>
                            <div id="dirLeft" class="direction-indicator pending" style="left:-25px;top:50%;transform:translateY(-50%)">←</div>
                            <div id="dirRight" class="direction-indicator pending" style="right:-25px;top:50%;transform:translateY(-50%)">→</div>
                        </div>
                    </div>
                    
                    <!-- Start Overlay -->
                    <div id="startOverlay" class="absolute inset-0 flex items-center justify-center bg-gradient-to-br from-ios-gray-6 to-white cursor-pointer" onclick="startFaceScan()">
                        <div class="text-center">
                            <div class="w-16 h-16 mx-auto bg-gradient-to-br from-ios-green to-ios-teal rounded-2xl flex items-center justify-center mb-3 shadow-lg">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </div>
                            <p class="text-gray-700 font-semibold">Start Face ID Scan</p>
                            <p class="text-ios-gray text-xs mt-1">Move head in all directions</p>
                        </div>
                    </div>
                    
                    <!-- Status -->
                    <div id="scanStatus" class="absolute bottom-3 left-3 right-3 text-center hidden">
                        <div class="bg-black/70 rounded-xl px-3 py-2">
                            <p id="statusText" class="text-white font-medium text-sm"></p>
                            <p id="progressText" class="text-white/70 text-xs mt-1"></p>
                        </div>
                    </div>
                    
                    <!-- Loading -->
                    <div id="loadingOverlay" class="absolute inset-0 bg-black/80 flex items-center justify-center hidden">
                        <div class="text-center text-white">
                            <svg class="w-10 h-10 mx-auto animate-spin mb-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <p class="text-sm">Loading Face ID...</p>
                        </div>
                    </div>
                    
                    <!-- Success -->
                    <div id="successOverlay" class="absolute inset-0 bg-ios-green flex items-center justify-center hidden">
                        <div class="text-center text-white">
                            <div class="w-20 h-20 mx-auto bg-white rounded-full flex items-center justify-center mb-3">
                                <svg class="w-12 h-12 text-ios-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <p class="font-bold text-xl">Face ID Complete!</p>
                            <p class="opacity-80 text-sm mt-1">Face enrolled successfully</p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-3 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div id="statusDot" class="w-3 h-3 rounded-full bg-gray-300"></div>
                        <span id="statusLabel" class="text-sm text-ios-gray">Ready to scan</span>
                    </div>
                    <button type="button" id="retakeBtn" class="px-4 py-2 bg-ios-gray-5 text-gray-700 rounded-xl text-sm font-medium hidden" onclick="retakeScan()">Retake</button>
                </div>
                <input type="hidden" name="face_image" id="faceImage">
                @error('face_image')<p class="mt-1 text-sm text-ios-red">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="ios-input">
                    <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="suspended" {{ old('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
            </div>

            <div class="flex items-center justify-end space-x-4 pt-4">
                <a href="{{ route('admin.users.index') }}" class="ios-btn bg-ios-gray-6 text-gray-700 hover:bg-ios-gray-5">Cancel</a>
                <button type="submit" id="submitBtn" disabled class="ios-btn bg-ios-blue text-white hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed">Create User</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
<script src="/js/secure-face-scan.js"></script>
<script>
const MODEL_URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model';
let stream = null, modelsLoaded = false, isScanning = false, scanComplete = false;
let directions = { left: false, right: false, up: false, down: false };
let allDirectionsDone = false;
let centerStableFrames = 0;
const REQUIRED_CENTER_FRAMES = 15;

// Secure scan session
const secureScan = new SecureFaceScan('registration');

async function loadModels() {
    document.getElementById('loadingOverlay').classList.remove('hidden');
    try {
        await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
        await faceapi.nets.faceLandmark68TinyNet.loadFromUri(MODEL_URL);
        modelsLoaded = true;
    } catch(e) { console.error(e); }
    document.getElementById('loadingOverlay').classList.add('hidden');
}

async function startFaceScan() {
    try {
        // Start secure session first
        const sessionResult = await secureScan.start();
        if (!sessionResult.success) {
            alert(sessionResult.message);
            return;
        }
        
        stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user', width: 640, height: 480 } });
        document.getElementById('video').srcObject = stream;
        document.getElementById('video').onloadedmetadata = async () => {
            document.getElementById('startOverlay').classList.add('hidden');
            document.getElementById('faceGuide').classList.remove('hidden');
            document.getElementById('scanStatus').classList.remove('hidden');
            document.getElementById('faceCircle').classList.add('scanning');
            if (!modelsLoaded) await loadModels();
            if (modelsLoaded) { isScanning = true; detectFace(); }
        };
    } catch(e) {
        alert('Camera access denied!');
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
            
            // Phase 1: Scan all 4 directions first
            if (!allDirectionsDone) {
                if (offsetX < -0.20 && !directions.left) { directions.left = true; markDirection('dirLeft'); updateProgress(); }
                if (offsetX > 0.20 && !directions.right) { directions.right = true; markDirection('dirRight'); updateProgress(); }
                if (offsetY < -0.15 && !directions.up) { directions.up = true; markDirection('dirUp'); updateProgress(); }
                if (offsetY > 0.15 && !directions.down) { directions.down = true; markDirection('dirDown'); updateProgress(); }
                
                if (directions.left && directions.right && directions.up && directions.down) {
                    allDirectionsDone = true;
                }
                updateStatus();
            } 
            // Phase 2: Wait for face to be centered, then capture
            else {
                const isCentered = Math.abs(offsetX) < 0.12 && Math.abs(offsetY) < 0.12;
                if (isCentered) {
                    centerStableFrames++;
                    document.getElementById('statusText').textContent = `👤 Hold still... ${Math.min(100, Math.round(centerStableFrames / REQUIRED_CENTER_FRAMES * 100))}%`;
                    document.getElementById('progressText').textContent = 'Capturing center photo';
                    
                    if (centerStableFrames >= REQUIRED_CENTER_FRAMES) {
                        completeScan();
                        return;
                    }
                } else {
                    centerStableFrames = Math.max(0, centerStableFrames - 2);
                    document.getElementById('statusText').textContent = '👤 Now look straight at camera';
                    document.getElementById('progressText').textContent = 'Center your face to capture';
                }
            }
        } else {
            document.getElementById('statusText').textContent = '🔍 Position face in frame';
            centerStableFrames = 0;
        }
    } catch(e) { console.error(e); }
    if (isScanning && !scanComplete) requestAnimationFrame(detectFace);
}

function markDirection(id) {
    document.getElementById(id).classList.remove('pending');
    document.getElementById(id).classList.add('done');
    // Sync progress to server
    const completedDirs = Object.entries(directions).filter(([k,v]) => v).map(([k]) => k);
    secureScan.updateProgress(completedDirs);
}

function updateProgress() {
    const done = Object.values(directions).filter(v => v).length;
    const progress = (done / 4) * 680 * 0.8;
    document.getElementById('progressRing').style.strokeDashoffset = 680 - progress;
    document.getElementById('progressText').textContent = `${done}/4 directions scanned`;
}

function updateStatus() {
    const order = ['left', 'right', 'up', 'down'];
    const missing = order.filter(d => !directions[d]);
    
    if (missing.length > 0) {
        const next = missing[0];
        const msgs = { left: '← Turn LEFT', right: '→ Turn RIGHT', up: '↑ Tilt UP', down: '↓ Tilt DOWN' };
        document.getElementById('statusText').textContent = msgs[next];
    }
}

async function completeScan() {
    // Validate capture is allowed (server-side check)
    const validation = await secureScan.validateCapture();
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
    scanComplete = true;
    document.getElementById('faceCircle').classList.remove('scanning');
    document.getElementById('progressRing').style.strokeDashoffset = 0;
    
    const canvas = document.getElementById('canvas');
    const video = document.getElementById('video');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);
    
    const img = new Image();
    img.onload = async () => {
        const c = document.createElement('canvas');
        c.width = 300; c.height = img.height * (300/img.width);
        c.getContext('2d').drawImage(img, 0, 0, c.width, c.height);
        document.getElementById('faceImage').value = c.toDataURL('image/jpeg', 0.6);
        
        // Complete the secure session
        await secureScan.complete();
    };
    img.src = canvas.toDataURL('image/jpeg', 0.8);
    
    document.getElementById('faceGuide').classList.add('hidden');
    document.getElementById('scanStatus').classList.add('hidden');
    document.getElementById('successOverlay').classList.remove('hidden');
    document.getElementById('submitBtn').disabled = false;
    document.getElementById('retakeBtn').classList.remove('hidden');
    document.getElementById('statusDot').className = 'w-3 h-3 rounded-full bg-ios-green';
    document.getElementById('statusLabel').textContent = 'Face ID enrolled ✓';
    
    setTimeout(() => { if (stream) stream.getTracks().forEach(t => t.stop()); }, 500);
}

async function retakeScan() {
    // Abort current session
    await secureScan.abort();
    
    scanComplete = false;
    allDirectionsDone = false;
    directions = { left: false, right: false, up: false, down: false };
    centerStableFrames = 0;
    document.getElementById('faceImage').value = '';
    document.getElementById('submitBtn').disabled = true;
    document.getElementById('retakeBtn').classList.add('hidden');
    document.getElementById('successOverlay').classList.add('hidden');
    document.getElementById('startOverlay').classList.remove('hidden');
    document.getElementById('progressRing').style.strokeDashoffset = 680;
    ['dirUp','dirDown','dirLeft','dirRight'].forEach(id => {
        document.getElementById(id).classList.remove('done');
        document.getElementById(id).classList.add('pending');
    });
    document.getElementById('statusDot').className = 'w-3 h-3 rounded-full bg-gray-300';
    document.getElementById('statusLabel').textContent = 'Ready to scan';
}

window.addEventListener('beforeunload', async () => { 
    await secureScan.abort();
    if (stream) stream.getTracks().forEach(t => t.stop()); 
});
</script>
@endsection
