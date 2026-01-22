@extends('layouts.user')

@section('title', 'Profile - Exam Portal')

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
    <!-- Profile Card -->
    <div class="ios-card overflow-hidden slide-in">
        <div class="p-8 text-center bg-gradient-to-br from-ios-blue to-ios-purple text-white">
            <div class="w-24 h-24 mx-auto bg-white/20 rounded-full flex items-center justify-center text-3xl font-bold mb-4">
                @if($user->profile_image)
                    <img src="{{ url('/api/profile-image/' . $user->id) }}" alt="Profile" class="w-full h-full rounded-full object-cover">
                @else
                    {{ substr($user->name, 0, 1) }}
                @endif
            </div>
            <h1 class="text-2xl font-bold">{{ $user->name }}</h1>
            <p class="text-white/80">{{ $user->email }}</p>
        </div>

        <div class="p-6 space-y-4">
            <div class="flex items-center justify-between p-4 bg-ios-gray-6 rounded-xl">
                <div>
                    <p class="text-sm text-ios-gray">Phone</p>
                    <p class="font-medium text-gray-900">{{ $user->phone ?? 'Not provided' }}</p>
                </div>
            </div>
            <div class="flex items-center justify-between p-4 bg-ios-gray-6 rounded-xl">
                <div>
                    <p class="text-sm text-ios-gray">Address</p>
                    <p class="font-medium text-gray-900">{{ $user->address ?? 'Not provided' }}</p>
                </div>
            </div>
            <div class="flex items-center justify-between p-4 bg-ios-gray-6 rounded-xl">
                <div>
                    <p class="text-sm text-ios-gray">Member Since</p>
                    <p class="font-medium text-gray-900">{{ $user->created_at->format('M d, Y') }}</p>
                </div>
            </div>
            <div class="flex items-center justify-between p-4 bg-ios-gray-6 rounded-xl">
                <div>
                    <p class="text-sm text-ios-gray">Last Login</p>
                    <p class="font-medium text-gray-900">{{ $user->last_login_at?->diffForHumans() ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="grid grid-cols-2 gap-4 slide-in" style="animation-delay: 0.1s">
        <a href="{{ route('user.profile.edit') }}" class="ios-card p-4 flex items-center space-x-3 hover:bg-ios-gray-6 transition-colors">
            <div class="w-10 h-10 bg-ios-blue/10 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-ios-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <span class="font-medium text-gray-900">Edit Profile</span>
        </a>
        <button type="button" onclick="openFaceModal()" class="ios-card p-4 flex items-center space-x-3 hover:bg-ios-gray-6 transition-colors text-left">
            <div class="w-10 h-10 bg-ios-orange/10 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-ios-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                </svg>
            </div>
            <span class="font-medium text-gray-900">Update Face</span>
        </button>
    </div>

    <!-- Reference Face -->
    <div class="ios-card p-6 slide-in" style="animation-delay: 0.15s">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Reference Face Image</h2>
        @php
            $faceCapture = $user->faceCaptures()
                ->whereIn('capture_type', ['registration', 'login'])
                ->orderByRaw("CASE WHEN capture_type = 'registration' THEN 0 ELSE 1 END")
                ->latest()
                ->get()
                ->first(function($capture) {
                    return \Illuminate\Support\Facades\Storage::disk('public')->exists($capture->image_path);
                });
        @endphp
        @if($faceCapture)
            <div class="w-32 h-32 rounded-2xl overflow-hidden bg-ios-gray-6">
                <img src="{{ url('/api/face-image/' . $faceCapture->id) }}" alt="Reference face" class="w-full h-full object-cover">
            </div>
            <p class="text-sm text-ios-gray mt-3">This image is used for identity verification during exams</p>
        @elseif($user->reference_face_image && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->reference_face_image))
            <div class="w-32 h-32 rounded-2xl overflow-hidden bg-ios-gray-6">
                <img src="{{ url('/api/user-reference-face/' . $user->id) }}" alt="Reference face" class="w-full h-full object-cover">
            </div>
            <p class="text-sm text-ios-gray mt-3">This image is used for identity verification during exams</p>
        @else
            <div class="w-32 h-32 rounded-2xl overflow-hidden bg-ios-gray-6 flex items-center justify-center">
                <div class="text-center text-gray-400">
                    <svg class="w-10 h-10 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <p class="text-xs">No face</p>
                </div>
            </div>
            <p class="text-sm text-ios-orange mt-3">Please update your face image for exam verification</p>
        @endif
    </div>
</div>


<!-- 360° Face Scan Modal -->
<div id="faceModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeFaceModal()"></div>
    <div class="absolute inset-4 md:inset-auto md:top-1/2 md:left-1/2 md:-translate-x-1/2 md:-translate-y-1/2 md:w-full md:max-w-lg bg-white rounded-3xl overflow-hidden shadow-2xl">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-900">Update Face Image</h2>
                <button onclick="closeFaceModal()" class="p-2 hover:bg-ios-gray-6 rounded-xl transition-colors">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <p class="text-sm text-ios-gray mb-4">Complete 360° face scan by looking in all 4 directions</p>
            <div class="relative rounded-2xl overflow-hidden bg-gray-900 aspect-video mb-4">
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
                        <div class="w-16 h-16 mx-auto bg-gradient-to-br from-ios-orange to-ios-yellow rounded-2xl flex items-center justify-center mb-3 shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <p class="text-gray-700 font-semibold">Start 360° Face Scan</p>
                        <p class="text-ios-gray text-sm">Click to begin</p>
                    </div>
                </div>
                <!-- Status -->
                <div id="scanStatus" class="absolute bottom-3 left-3 right-3 text-center hidden">
                    <div class="bg-black/70 rounded-xl px-4 py-2">
                        <p id="statusText" class="text-white font-medium text-sm"></p>
                    </div>
                </div>
                <!-- Loading -->
                <div id="loadingOverlay" class="absolute inset-0 bg-black/80 flex items-center justify-center hidden">
                    <div class="text-center text-white">
                        <svg class="w-10 h-10 mx-auto animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <p class="mt-2 text-sm">Loading models...</p>
                    </div>
                </div>
                <!-- Success -->
                <div id="successOverlay" class="absolute inset-0 bg-ios-green flex items-center justify-center hidden">
                    <div class="text-center text-white">
                        <div class="w-16 h-16 mx-auto bg-white rounded-full flex items-center justify-center mb-3">
                            <svg class="w-10 h-10 text-ios-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <p class="font-bold text-lg">Face Updated!</p>
                    </div>
                </div>
            </div>
            <button type="button" id="retakeBtn" class="w-full ios-btn bg-ios-gray-5 text-gray-700 hidden mb-3" onclick="retakeScan()">Retake Scan</button>
            <button type="button" onclick="closeFaceModal()" class="w-full ios-btn bg-ios-gray-6 text-gray-700 hover:bg-ios-gray-5">Cancel</button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
<script src="/js/secure-face-scan.js"></script>
<script>
const MODEL_URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model';
let stream = null, modelsLoaded = false, isScanning = false, scanComplete = false;
let directions = { left: false, right: false, up: false, down: false };
let allDirectionsDone = false, centerStableFrames = 0;
const REQUIRED_CENTER_FRAMES = 15;
const secureScan = new SecureFaceScan('profile_update');

function openFaceModal() { document.getElementById('faceModal').classList.remove('hidden'); }
function closeFaceModal() {
    document.getElementById('faceModal').classList.add('hidden');
    if (stream) { stream.getTracks().forEach(t => t.stop()); stream = null; }
    secureScan.abort();
    resetScanUI();
}
function resetScanUI() {
    scanComplete = false; isScanning = false; allDirectionsDone = false;
    directions = { left: false, right: false, up: false, down: false };
    centerStableFrames = 0;
    document.getElementById('startOverlay').classList.remove('hidden');
    document.getElementById('faceGuide').classList.add('hidden');
    document.getElementById('scanStatus').classList.add('hidden');
    document.getElementById('successOverlay').classList.add('hidden');
    document.getElementById('retakeBtn').classList.add('hidden');
    document.getElementById('progressRing').style.strokeDashoffset = 560;
    ['dirUp','dirDown','dirLeft','dirRight'].forEach(id => {
        document.getElementById(id).classList.remove('done');
        document.getElementById(id).classList.add('pending');
    });
}
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
        const sessionResult = await secureScan.start();
        if (!sessionResult.success) { alert(sessionResult.message); return; }
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
    } catch(e) { alert('Camera access denied!'); closeFaceModal(); }
}
async function detectFace() {
    if (!isScanning || scanComplete) return;
    const video = document.getElementById('video');
    try {
        const detections = await faceapi.detectAllFaces(video, new faceapi.TinyFaceDetectorOptions({ scoreThreshold: 0.3 })).withFaceLandmarks(true);
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
            if (!allDirectionsDone) {
                if (offsetX < -0.20 && !directions.left) { directions.left = true; markDirection('dirLeft'); updateProgress(); }
                if (offsetX > 0.20 && !directions.right) { directions.right = true; markDirection('dirRight'); updateProgress(); }
                if (offsetY < -0.15 && !directions.up) { directions.up = true; markDirection('dirUp'); updateProgress(); }
                if (offsetY > 0.15 && !directions.down) { directions.down = true; markDirection('dirDown'); updateProgress(); }
                if (directions.left && directions.right && directions.up && directions.down) allDirectionsDone = true;
                updateStatus();
            } else {
                const isCentered = Math.abs(offsetX) < 0.12 && Math.abs(offsetY) < 0.12;
                if (isCentered) {
                    centerStableFrames++;
                    document.getElementById('statusText').textContent = `👤 Hold... ${Math.min(100, Math.round(centerStableFrames / REQUIRED_CENTER_FRAMES * 100))}%`;
                    if (centerStableFrames >= REQUIRED_CENTER_FRAMES) { completeScan(); return; }
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
        const msgs = { left: '← Turn LEFT', right: '→ Turn RIGHT', up: '↑ Look UP', down: '↓ Look DOWN' };
        document.getElementById('statusText').textContent = msgs[missing[0]];
    }
}
async function completeScan() {
    const validation = await secureScan.validateCapture();
    if (!validation.success) {
        document.getElementById('statusText').textContent = '⚠️ ' + validation.message;
        centerStableFrames = 0;
        if (validation.code === 'EXPIRED' || validation.code === 'NO_SESSION') setTimeout(() => retakeScan(), 2000);
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
        const imageData = c.toDataURL('image/jpeg', 0.6);
        try {
            const response = await fetch('{{ route("user.profile.face") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ face_image: imageData })
            });
            const data = await response.json();
            if (data.success) {
                await secureScan.complete();
                document.getElementById('faceGuide').classList.add('hidden');
                document.getElementById('scanStatus').classList.add('hidden');
                document.getElementById('successOverlay').classList.remove('hidden');
                setTimeout(() => { if (stream) stream.getTracks().forEach(t => t.stop()); location.reload(); }, 1500);
            } else { alert(data.message || 'Failed to update face'); retakeScan(); }
        } catch (err) { alert('Failed to update face image'); retakeScan(); }
    };
    img.src = canvas.toDataURL('image/jpeg', 0.8);
}
async function retakeScan() {
    await secureScan.abort();
    if (stream) { stream.getTracks().forEach(t => t.stop()); stream = null; }
    resetScanUI();
}
window.addEventListener('beforeunload', async () => { await secureScan.abort(); if (stream) stream.getTracks().forEach(t => t.stop()); });
</script>
@endsection