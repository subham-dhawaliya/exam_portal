@extends('layouts.admin')

@section('title', 'Edit User - Admin')

@push('styles')
<style>
    .face-circle { 
        width: 150px; height: 190px; 
        border: 3px solid rgba(255,255,255,0.3); 
        border-radius: 50%; 
        position: relative;
    }
    .face-circle::before {
        content: ''; position: absolute; inset: -6px;
        border: 3px solid transparent; border-radius: 50%;
        border-top-color: #34C759;
        animation: none;
    }
    .face-circle.scanning::before { animation: spin 2s linear infinite; }
    @keyframes spin { to { transform: rotate(360deg); } }
    .progress-ring { transform: rotate(-90deg); }
    .progress-ring ellipse { fill: none; stroke-width: 4; stroke-linecap: round; transition: stroke-dashoffset 0.3s; }
    .direction-indicator {
        position: absolute; width: 28px; height: 28px;
        border-radius: 50%; display: flex; align-items: center; justify-content: center;
        font-size: 14px; transition: all 0.3s;
    }
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
            <h1 class="text-2xl font-bold text-gray-900">Edit User</h1>
            <p class="text-ios-gray mt-1">Update {{ $user->name }}'s account</p>
        </div>
    </div>

    <!-- Form -->
    <div class="ios-card p-6">
        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="ios-input">
                    @error('name')<p class="mt-1 text-sm text-ios-red">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="ios-input">
                    @error('email')<p class="mt-1 text-sm text-ios-red">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="ios-input">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                    <input type="password" name="password" class="ios-input" placeholder="Leave blank to keep">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="ios-input">
                    <option value="active" {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $user->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="suspended" {{ old('status', $user->status) === 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
            </div>

            <!-- Face Photo Update Section -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-ios-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Update Face Photo (Optional)
                    </span>
                </label>
                <p class="text-xs text-ios-gray mb-3">Leave unchanged to keep current face, or scan new face</p>

                <div class="grid grid-cols-2 gap-4">
                    <!-- Current Face -->
                    <div>
                        <p class="text-xs text-gray-500 mb-2">Current Face</p>
                        <div class="rounded-xl overflow-hidden bg-gray-100 aspect-square flex items-center justify-center">
                            @php
                                // Get latest face capture - try registration first, then login
                                $faceCapture = $user->faceCaptures()
                                    ->whereIn('capture_type', ['registration', 'login'])
                                    ->orderByRaw("CASE WHEN capture_type = 'registration' THEN 0 ELSE 1 END")
                                    ->latest()
                                    ->get()
                                    ->first(function($capture) {
                                        // Only return if file actually exists
                                        return \Illuminate\Support\Facades\Storage::disk('public')->exists($capture->image_path);
                                    });
                            @endphp
                            @if($faceCapture)
                                <img src="{{ url('/api/face-image/' . $faceCapture->id) }}" class="w-full h-full object-cover" alt="Current face">
                            @else
                                <div class="text-center text-gray-400">
                                    <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    <p class="text-xs">No face</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- New Face Scan -->
                    <div>
                        <p class="text-xs text-gray-500 mb-2">New Face (360° Scan)</p>
                        <div class="relative rounded-xl overflow-hidden bg-gray-900 aspect-square">
                            <video id="video" class="w-full h-full object-cover" autoplay playsinline muted></video>
                            <canvas id="canvas" class="hidden"></canvas>
                            
                            <!-- Face Guide -->
                            <div id="faceGuide" class="absolute inset-0 flex items-center justify-center pointer-events-none hidden">
                                <div class="relative">
                                    <svg class="progress-ring absolute -inset-2" width="180" height="220" viewBox="0 0 180 220">
                                        <ellipse cx="90" cy="110" rx="78" ry="98" stroke="rgba(255,255,255,0.2)" stroke-width="4" fill="none"/>
                                        <ellipse id="progressRing" cx="90" cy="110" rx="78" ry="98" stroke="#34C759" stroke-width="4" fill="none" stroke-dasharray="560" stroke-dashoffset="560"/>
                                    </svg>
                                    <div class="face-circle" id="faceCircle"></div>
                                    <div id="dirUp" class="direction-indicator pending" style="top:-20px;left:50%;transform:translateX(-50%)">↑</div>
                                    <div id="dirDown" class="direction-indicator pending" style="bottom:-20px;left:50%;transform:translateX(-50%)">↓</div>
                                    <div id="dirLeft" class="direction-indicator pending" style="left:-20px;top:50%;transform:translateY(-50%)">←</div>
                                    <div id="dirRight" class="direction-indicator pending" style="right:-20px;top:50%;transform:translateY(-50%)">→</div>
                                </div>
                            </div>
                            
                            <!-- Start Overlay -->
                            <div id="startOverlay" class="absolute inset-0 flex items-center justify-center bg-gradient-to-br from-ios-gray-6 to-white cursor-pointer" onclick="startFaceScan()">
                                <div class="text-center">
                                    <div class="w-12 h-12 mx-auto bg-gradient-to-br from-ios-green to-ios-teal rounded-xl flex items-center justify-center mb-2 shadow">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </div>
                                    <p class="text-gray-700 font-medium text-sm">Update Face</p>
                                    <p class="text-ios-gray text-xs">Click to scan</p>
                                </div>
                            </div>
                            
                            <!-- Status -->
                            <div id="scanStatus" class="absolute bottom-2 left-2 right-2 text-center hidden">
                                <div class="bg-black/70 rounded-lg px-2 py-1">
                                    <p id="statusText" class="text-white font-medium text-xs"></p>
                                </div>
                            </div>
                            
                            <!-- Loading -->
                            <div id="loadingOverlay" class="absolute inset-0 bg-black/80 flex items-center justify-center hidden">
                                <div class="text-center text-white">
                                    <svg class="w-8 h-8 mx-auto animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                </div>
                            </div>
                            
                            <!-- Success -->
                            <div id="successOverlay" class="absolute inset-0 bg-ios-green flex items-center justify-center hidden">
                                <div class="text-center text-white">
                                    <div class="w-14 h-14 mx-auto bg-white rounded-full flex items-center justify-center mb-2">
                                        <svg class="w-8 h-8 text-ios-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                    <p class="font-bold text-sm">Done!</p>
                                </div>
                            </div>
                        </div>
                        <button type="button" id="retakeBtn" class="mt-2 w-full px-3 py-1.5 bg-ios-gray-5 text-gray-700 rounded-lg text-xs font-medium hidden" onclick="retakeScan()">Retake</button>
                    </div>
                </div>
                <input type="hidden" name="face_image" id="faceImage">
            </div>

            <div class="flex items-center justify-between pt-4 border-t">
                <button type="button" onclick="if(confirm('Delete this user?')) document.getElementById('deleteForm').submit()" class="ios-btn bg-ios-red/10 text-ios-red hover:bg-ios-red/20">
                    Delete User
                </button>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.users.index') }}" class="ios-btn bg-ios-gray-6 text-gray-700">Cancel</a>
                    <button type="submit" class="ios-btn bg-ios-blue text-white">Update User</button>
                </div>
            </div>
        </form>
        
        <form id="deleteForm" action="{{ route('admin.users.destroy', $user) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
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
const secureScan = new SecureFaceScan('update');

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
    } catch(e) { alert('Camera access denied!'); }
}

async function detectFace() {
    if (!isScanning || scanComplete) return;
    const video = document.getElementById('video');
    try {
        // Detect ALL faces to check for multiple people
        const detections = await faceapi.detectAllFaces(video, new faceapi.TinyFaceDetectorOptions({ scoreThreshold: 0.3 })).withFaceLandmarks(true);
        
        // Check for multiple faces - BLOCK if more than 1 face
        if (detections.length > 1) {
            document.getElementById('statusText').textContent = '⚠️ Multiple faces! Only one person allowed';
            centerStableFrames = 0;
            requestAnimationFrame(detectFace);
            return;
        }
        
        if (detections.length === 1) {
            const detection = detections[0];
            const nose = detection.landmarks.getNose()[3];
            const centerX = video.videoWidth / 2, centerY = video.videoHeight / 2;
            const offsetX = (nose.x - centerX) / centerX, offsetY = (nose.y - centerY) / centerY;
            
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
                    document.getElementById('statusText').textContent = `👤 Hold... ${Math.min(100, Math.round(centerStableFrames / REQUIRED_CENTER_FRAMES * 100))}%`;
                    if (centerStableFrames >= REQUIRED_CENTER_FRAMES) {
                        completeScan(); return;
                    }
                } else {
                    centerStableFrames = Math.max(0, centerStableFrames - 2);
                    document.getElementById('statusText').textContent = '👤 Look straight';
                }
            }
        } else {
            document.getElementById('statusText').textContent = '🔍 Position face';
            centerStableFrames = 0;
        }
    } catch(e) {}
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
    document.getElementById('progressRing').style.strokeDashoffset = 560 - (done / 4) * 560 * 0.8;
}
function updateStatus() {
    const order = ['left', 'right', 'up', 'down'];
    const missing = order.filter(d => !directions[d]);
    if (missing.length > 0) {
        const msgs = { left: '← LEFT', right: '→ RIGHT', up: '↑ UP', down: '↓ DOWN' };
        document.getElementById('statusText').textContent = msgs[missing[0]];
    }
}

async function completeScan() {
    // Validate capture is allowed (server-side check)
    const validation = await secureScan.validateCapture();
    if (!validation.success) {
        document.getElementById('statusText').textContent = '⚠️ ' + validation.message;
        centerStableFrames = 0;
        if (validation.code === 'EXPIRED' || validation.code === 'NO_SESSION') {
            setTimeout(() => retakeScan(), 2000);
        }
        return;
    }
    
    isScanning = false; scanComplete = true;
    document.getElementById('faceCircle').classList.remove('scanning');
    document.getElementById('progressRing').style.strokeDashoffset = 0;
    const canvas = document.getElementById('canvas'), video = document.getElementById('video');
    canvas.width = video.videoWidth; canvas.height = video.videoHeight;
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
    document.getElementById('retakeBtn').classList.remove('hidden');
    setTimeout(() => { if (stream) stream.getTracks().forEach(t => t.stop()); }, 500);
}

async function retakeScan() {
    // Abort current session
    await secureScan.abort();
    
    scanComplete = false; allDirectionsDone = false;
    directions = { left: false, right: false, up: false, down: false };
    centerStableFrames = 0;
    document.getElementById('faceImage').value = '';
    document.getElementById('retakeBtn').classList.add('hidden');
    document.getElementById('successOverlay').classList.add('hidden');
    document.getElementById('startOverlay').classList.remove('hidden');
    document.getElementById('progressRing').style.strokeDashoffset = 560;
    ['dirUp','dirDown','dirLeft','dirRight'].forEach(id => {
        document.getElementById(id).classList.remove('done');
        document.getElementById(id).classList.add('pending');
    });
}

window.addEventListener('beforeunload', async () => { 
    await secureScan.abort();
    if (stream) stream.getTracks().forEach(t => t.stop()); 
});
</script>
@endsection
