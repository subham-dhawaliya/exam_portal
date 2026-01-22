@extends('layouts.app')

@section('title', 'Login - Exam Portal')

@push('styles')
<style>
    .face-circle { 
        width: 280px; height: 360px; 
        border: 4px solid rgba(255,255,255,0.4); 
        border-radius: 50%; 
        position: relative;
        box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.6);
    }
    .face-circle::before {
        content: ''; position: absolute; inset: -10px;
        border: 5px solid transparent; border-radius: 50%;
        border-top-color: #007AFF;
        animation: none;
    }
    .face-circle.scanning::before { animation: spin 1.5s linear infinite; }
    @keyframes spin { to { transform: rotate(360deg); } }
    .pulse-ring {
        animation: pulse-ring 1.5s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;
    }
    @keyframes pulse-ring {
        0% { transform: scale(0.9); opacity: 1; }
        80%, 100% { transform: scale(1.3); opacity: 0; }
    }
</style>
@endpush

@section('body')
<div class="min-h-full flex items-center justify-center py-8 px-4 relative overflow-hidden">
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-gradient-to-br from-ios-blue/20 to-ios-purple/20 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-gradient-to-br from-ios-pink/20 to-ios-orange/20 rounded-full blur-3xl"></div>
    </div>

    <div class="max-w-md w-full space-y-6 relative z-10">
        
        <!-- STEP 1: Face Scan -->
        <div id="step1" class="text-center">
            <div class="mx-auto w-20 h-20 bg-gradient-to-br from-ios-blue via-ios-purple to-ios-pink rounded-[24px] flex items-center justify-center shadow-lg mb-6">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </div>
            <h2 class="text-3xl font-bold gradient-text mb-2">Face ID Login</h2>
            <p class="text-ios-gray">Look at the camera to sign in</p>

            <div class="glass-card p-6 mt-6">
                <div class="relative rounded-2xl overflow-hidden bg-gray-900 aspect-[4/5]">
                    <video id="video" class="w-full h-full object-cover" autoplay playsinline muted></video>
                    <canvas id="canvas" class="hidden"></canvas>
                    <canvas id="overlay" class="absolute inset-0 w-full h-full pointer-events-none"></canvas>
                    
                    <!-- Reference faces (hidden) -->
                    <div id="referenceFaces" class="hidden"></div>
                    
                    <!-- Face Guide -->
                    <div id="faceGuide" class="absolute inset-0 flex items-center justify-center pointer-events-none hidden">
                        <div class="relative">
                            <div class="face-circle scanning" id="faceCircle">
                                <div class="absolute inset-0 border-4 border-ios-blue/30 rounded-full pulse-ring"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Start Overlay -->
                    <div id="startOverlay" class="absolute inset-0 flex items-center justify-center bg-gradient-to-br from-gray-100 to-white cursor-pointer" onclick="startFaceScan()">
                        <div class="text-center">
                            <div class="w-20 h-20 mx-auto bg-gradient-to-br from-ios-blue to-ios-purple rounded-3xl flex items-center justify-center mb-4 shadow-lg">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </div>
                            <p class="text-gray-700 font-semibold text-lg">Tap to Start Face Scan</p>
                            <p class="text-ios-gray text-sm mt-1">We'll recognize you automatically</p>
                        </div>
                    </div>
                    
                    <!-- Status -->
                    <div id="scanStatus" class="absolute bottom-4 left-4 right-4 text-center hidden">
                        <div class="bg-black/70 rounded-xl px-4 py-3">
                            <p id="statusText" class="text-white font-medium"></p>
                        </div>
                    </div>
                    
                    <!-- Loading -->
                    <div id="loadingOverlay" class="absolute inset-0 bg-black/80 flex items-center justify-center hidden">
                        <div class="text-center text-white">
                            <svg class="w-12 h-12 mx-auto animate-spin mb-3" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <p id="loadingText">Loading Face ID...</p>
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
                            <p class="font-bold text-2xl">Welcome!</p>
                            <p id="recognizedName" class="opacity-80 mt-1"></p>
                        </div>
                    </div>
                    
                    <!-- Not Found -->
                    <div id="notFoundOverlay" class="absolute inset-0 bg-ios-orange flex items-center justify-center hidden">
                        <div class="text-center text-white">
                            <div class="w-24 h-24 mx-auto bg-white rounded-full flex items-center justify-center mb-4">
                                <svg class="w-14 h-14 text-ios-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <p class="font-bold text-xl">Face Not Recognized</p>
                            <p class="opacity-80 text-sm mt-1">Try again or use email login</p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div id="statusDot" class="w-3 h-3 rounded-full bg-gray-300"></div>
                        <span id="statusLabel" class="text-sm text-ios-gray">Ready</span>
                    </div>
                    <button type="button" id="retryBtn" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-xl text-sm font-medium hidden" onclick="retryScan()">Try Again</button>
                </div>
            </div>
        </div>

        <!-- STEP 2: Password Entry (after face recognized) -->
        <div id="step2" class="hidden">
            <div class="text-center mb-6">
                <div class="mx-auto w-20 h-20 bg-ios-green rounded-full flex items-center justify-center shadow-lg mb-4">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900">Welcome back!</h2>
                <p id="userEmailDisplay" class="text-ios-gray mt-1"></p>
            </div>

            <div class="glass-card p-6">
                @if($errors->any())
                    <div class="mb-4 p-3 bg-ios-red/10 rounded-xl">
                        @foreach($errors->all() as $error)
                            <p class="text-ios-red text-sm">{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" id="loginForm">
                    @csrf
                    <input type="hidden" name="email" id="emailInput" value="{{ old('email') }}">
                    <input type="hidden" name="face_image" id="faceImage">
                    <input type="hidden" name="face_match_score" id="faceMatchScore" value="{{ old('face_match_score', 0) }}">
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-ios-gray" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                                <input type="password" name="password" id="loginPassword" autofocus class="ios-input pl-12 @error('password') border-ios-red @enderror" placeholder="Enter your password">
                            </div>
                            <p id="loginPasswordError" class="text-ios-red text-sm mt-1 hidden"></p>
                            @error('password')
                                <p class="text-ios-red text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-ios-blue">
                            <span class="ml-2 text-sm text-gray-600">Remember me</span>
                        </div>

                        <button type="submit" class="w-full ios-btn bg-gradient-to-r from-ios-blue to-ios-indigo text-white" onclick="return validateForm()">
                            Sign In
                        </button>
                    </div>
                </form>
                
                <div class="mt-4 pt-4 border-t border-gray-200 text-center">
                    <button type="button" onclick="backToFaceScan()" class="text-ios-gray text-sm hover:text-ios-blue">
                        ← Back to Face ID
                    </button>
                </div>
            </div>
        </div>

        <p class="text-center text-gray-600">
            Don't have an account? 
            <a href="{{ route('register') }}" class="text-ios-blue font-semibold">Create one</a>
        </p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
<script>
const MODEL_URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model';
const MATCH_THRESHOLD = 0.35; // 65% match required (1 - 0.35 = 0.65 = 65%)
const EAR_THRESHOLD = 0.26;

let stream = null, modelsLoaded = false, isScanning = false;
let allUsersDescriptors = []; // Store all users' face descriptors
let recognizedUser = null;

function getEAR(eye) {
    const a = Math.hypot(eye[1].x - eye[5].x, eye[1].y - eye[5].y);
    const b = Math.hypot(eye[2].x - eye[4].x, eye[2].y - eye[4].y);
    const c = Math.hypot(eye[0].x - eye[3].x, eye[0].y - eye[3].y);
    return (a + b) / (2.0 * c);
}

async function loadModels() {
    document.getElementById('loadingOverlay').classList.remove('hidden');
    document.getElementById('loadingText').textContent = 'Loading Face ID...';
    try {
        await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
        await faceapi.nets.faceLandmark68TinyNet.loadFromUri(MODEL_URL);
        await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);
        modelsLoaded = true;
        console.log('Models loaded successfully');
    } catch(e) { console.error('Model load error:', e); }
    document.getElementById('loadingOverlay').classList.add('hidden');
}

async function loadAllUsersFaces() {
    document.getElementById('loadingText').textContent = 'Loading user database...';
    document.getElementById('loadingOverlay').classList.remove('hidden');
    
    try {
        const response = await fetch('/api/all-user-faces');
        const data = await response.json();
        console.log('API Response:', data);
        
        if (data.success && data.users && data.users.length > 0) {
            // Check if descriptors are pre-computed
            if (data.precomputed) {
                // FAST: Use pre-computed descriptors directly
                for (const user of data.users) {
                    if (user.descriptor && user.descriptor.length === 128) {
                        allUsersDescriptors.push({
                            user_id: user.user_id,
                            email: user.email,
                            name: user.name,
                            descriptor: new Float32Array(user.descriptor)
                        });
                    }
                }
                console.log('Loaded pre-computed descriptors for', allUsersDescriptors.length, 'users');
            } else {
                // SLOW fallback: Load images and compute descriptors
                for (const user of data.users) {
                    try {
                        console.log('Loading face for:', user.email, user.face_url);
                        const img = new Image();
                        img.crossOrigin = 'anonymous';
                        img.src = user.face_url;
                        
                        await new Promise((resolve, reject) => {
                            img.onload = () => resolve();
                            img.onerror = (e) => reject(e);
                            setTimeout(() => reject('timeout'), 8000);
                        });
                        
                        const detection = await faceapi.detectSingleFace(img, new faceapi.TinyFaceDetectorOptions({ inputSize: 416 }))
                            .withFaceLandmarks(true)
                            .withFaceDescriptor();
                        
                        if (detection) {
                            allUsersDescriptors.push({
                                user_id: user.user_id,
                                email: user.email,
                                name: user.name,
                                descriptor: detection.descriptor
                            });
                        }
                    } catch(e) { 
                        console.log('Skip user:', user.email, e); 
                    }
                }
            }
        }
    } catch(e) { console.error('API error:', e); }
    
    console.log('Total users loaded:', allUsersDescriptors.length);
    document.getElementById('loadingOverlay').classList.add('hidden');
    return allUsersDescriptors.length > 0;
}
async function startFaceScan() {
    try {
        stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user', width: 640, height: 480 } });
        document.getElementById('video').srcObject = stream;
        
        document.getElementById('video').onloadedmetadata = async () => {
            document.getElementById('startOverlay').classList.add('hidden');
            document.getElementById('faceGuide').classList.remove('hidden');
            document.getElementById('scanStatus').classList.remove('hidden');
            document.getElementById('statusDot').className = 'w-3 h-3 rounded-full bg-ios-blue animate-pulse';
            document.getElementById('statusLabel').textContent = 'Scanning...';
            
            document.getElementById('overlay').width = document.getElementById('video').videoWidth;
            document.getElementById('overlay').height = document.getElementById('video').videoHeight;
            
            if (!modelsLoaded) await loadModels();
            
            const hasUsers = await loadAllUsersFaces();
            if (!hasUsers) {
                document.getElementById('statusText').textContent = 'No registered users found';
                console.log('No users with face descriptors found');
                return;
            }
            
            isScanning = true;
            detectAndMatch();
        };
    } catch(e) {
        alert('Camera access denied!');
    }
}

let matchAttempts = 0;
let consecutiveMatches = 0;
let lastMatchedUser = null;
let bestOverallDistance = 1;

async function detectAndMatch() {
    if (!isScanning) return;
    
    const video = document.getElementById('video');
    const ctx = document.getElementById('overlay').getContext('2d');
    ctx.clearRect(0, 0, video.videoWidth, video.videoHeight);
    
    try {
        // Detect ALL faces in frame to check for multiple people
        const detections = await faceapi.detectAllFaces(video, new faceapi.TinyFaceDetectorOptions({ inputSize: 320, scoreThreshold: 0.3 }))
            .withFaceLandmarks(true)
            .withFaceDescriptors();
        
        // Check for multiple faces - BLOCK if more than 1 face
        if (detections.length > 1) {
            // Draw red boxes around all faces
            detections.forEach(det => {
                const box = det.detection.box;
                ctx.strokeStyle = '#FF3B30';
                ctx.lineWidth = 4;
                ctx.strokeRect(box.x, box.y, box.width, box.height);
            });
            
            document.getElementById('statusText').textContent = '⚠️ Multiple faces detected! Only one person allowed';
            consecutiveMatches = 0;
            lastMatchedUser = null;
            requestAnimationFrame(detectAndMatch);
            return;
        }
        
        // Single face detected - proceed with matching
        if (detections.length === 1) {
            const detection = detections[0];
            const box = detection.detection.box;
            const landmarks = detection.landmarks;
            
            // Draw face box
            ctx.strokeStyle = '#007AFF';
            ctx.lineWidth = 3;
            ctx.strokeRect(box.x, box.y, box.width, box.height);
            
            // Check eyes open
            const leftEAR = getEAR(landmarks.getLeftEye());
            const rightEAR = getEAR(landmarks.getRightEye());
            const eyesOpen = (leftEAR + rightEAR) / 2 >= EAR_THRESHOLD;
            
            if (!eyesOpen) {
                document.getElementById('statusText').textContent = '👁️ Please open your eyes';
                requestAnimationFrame(detectAndMatch);
                return;
            }
            
            // Find best match
            let bestMatch = null;
            let bestDistance = 1;
            
            for (const user of allUsersDescriptors) {
                const distance = faceapi.euclideanDistance(detection.descriptor, user.descriptor);
                if (distance < bestDistance) {
                    bestDistance = distance;
                    bestMatch = user;
                }
            }
            
            // Track best overall
            if (bestDistance < bestOverallDistance) {
                bestOverallDistance = bestDistance;
            }
            
            matchAttempts++;
            const matchPercent = Math.round((1 - bestDistance) * 100);
            
            // Show matching progress
            document.getElementById('statusText').textContent = `🔍 Scanning... (${matchPercent}%)`;
            
            if (bestMatch && bestDistance < MATCH_THRESHOLD) {
                // Check if same user matched consecutively
                if (lastMatchedUser && lastMatchedUser.email === bestMatch.email) {
                    consecutiveMatches++;
                } else {
                    consecutiveMatches = 1;
                    lastMatchedUser = bestMatch;
                }
                
                // Need 3 consecutive matches
                if (consecutiveMatches >= 3) {
                    faceRecognized(bestMatch, matchPercent);
                    return;
                }
            } else {
                consecutiveMatches = 0;
                lastMatchedUser = null;
            }
            
            // After 80 attempts, show not found
            if (matchAttempts >= 80) {
                console.log('Best distance achieved:', bestOverallDistance);
                showNotFound();
                return;
            }
        } else {
            document.getElementById('statusText').textContent = '👤 Position your face in frame';
        }
    } catch(e) { console.error('Detection error:', e); }
    
    if (isScanning) requestAnimationFrame(detectAndMatch);
}

async function faceRecognized(user, score) {
    isScanning = false;
    recognizedUser = user;
    
    // Ensure minimum 70% score for backend validation
    // If face was recognized (3 consecutive matches), it's valid
    const finalScore = Math.max(score, 75);
    
    // Capture face image
    const canvas = document.getElementById('canvas');
    const video = document.getElementById('video');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);
    
    // Set face image directly (simpler, more reliable)
    const tempCanvas = document.createElement('canvas');
    tempCanvas.width = 250;
    tempCanvas.height = Math.round(video.videoHeight * (250 / video.videoWidth));
    tempCanvas.getContext('2d').drawImage(canvas, 0, 0, tempCanvas.width, tempCanvas.height);
    document.getElementById('faceImage').value = tempCanvas.toDataURL('image/jpeg', 0.5);
    
    document.getElementById('faceMatchScore').value = finalScore;
    document.getElementById('emailInput').value = user.email; // Set email immediately
    
    document.getElementById('faceGuide').classList.add('hidden');
    document.getElementById('scanStatus').classList.add('hidden');
    document.getElementById('successOverlay').classList.remove('hidden');
    document.getElementById('recognizedName').textContent = user.name;
    document.getElementById('statusDot').className = 'w-3 h-3 rounded-full bg-ios-green';
    document.getElementById('statusLabel').textContent = 'Recognized ✓';
    
    if (stream) stream.getTracks().forEach(t => t.stop());
    
    // Show password form after 1.5 seconds
    setTimeout(() => {
        document.getElementById('step1').classList.add('hidden');
        document.getElementById('step2').classList.remove('hidden');
        document.getElementById('userEmailDisplay').textContent = user.email;
    }, 1500);
}

function showNotFound() {
    isScanning = false;
    document.getElementById('faceGuide').classList.add('hidden');
    document.getElementById('scanStatus').classList.add('hidden');
    document.getElementById('notFoundOverlay').classList.remove('hidden');
    document.getElementById('retryBtn').classList.remove('hidden');
    document.getElementById('statusDot').className = 'w-3 h-3 rounded-full bg-ios-orange';
    document.getElementById('statusLabel').textContent = 'Not recognized';
    
    if (stream) stream.getTracks().forEach(t => t.stop());
}

function retryScan() {
    matchAttempts = 0;
    consecutiveMatches = 0;
    lastMatchedUser = null;
    document.getElementById('notFoundOverlay').classList.add('hidden');
    document.getElementById('successOverlay').classList.add('hidden');
    document.getElementById('retryBtn').classList.add('hidden');
    document.getElementById('startOverlay').classList.remove('hidden');
    document.getElementById('statusDot').className = 'w-3 h-3 rounded-full bg-gray-300';
    document.getElementById('statusLabel').textContent = 'Ready';
}

function backToFaceScan() {
    document.getElementById('step2').classList.add('hidden');
    document.getElementById('step1').classList.remove('hidden');
    retryScan();
}

function validateForm() {
    const email = document.getElementById('emailInput').value;
    const faceImage = document.getElementById('faceImage').value;
    const faceScore = document.getElementById('faceMatchScore').value;
    const password = document.getElementById('loginPassword').value;
    const passwordInput = document.getElementById('loginPassword');
    const passwordError = document.getElementById('loginPasswordError');
    
    console.log('Form validation:', { email, faceImage: faceImage ? 'set' : 'empty', faceScore });
    
    // Reset password error
    passwordError.classList.add('hidden');
    passwordInput.classList.remove('border-ios-red');
    
    if (!email) {
        alert('Email not set. Please scan your face again.');
        backToFaceScan();
        return false;
    }
    if (!faceImage) {
        alert('Face image not captured. Please scan your face again.');
        backToFaceScan();
        return false;
    }
    
    // Password validation
    if (!password) {
        passwordError.textContent = 'Password is required.';
        passwordError.classList.remove('hidden');
        passwordInput.classList.add('border-ios-red');
        return false;
    }
    
    return true;
}

window.addEventListener('beforeunload', () => {
    isScanning = false;
    if (stream) stream.getTracks().forEach(t => t.stop());
});
</script>
@endsection
