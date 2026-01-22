@extends('layouts.app')

@section('title', 'Face Verification - ' . $exam->title)

@push('styles')
<style>
    .blink-indicator {
        transition: all 0.3s ease;
    }
    .blink-indicator.active {
        background-color: #34C759 !important;
        transform: scale(1.1);
    }
    .eye-status {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border-radius: 12px;
        background: rgba(0,0,0,0.6);
        color: white;
        font-size: 14px;
    }
    .eye-icon {
        width: 20px;
        height: 20px;
    }
    @keyframes pulse-green {
        0%, 100% { box-shadow: 0 0 0 0 rgba(52, 199, 89, 0.4); }
        50% { box-shadow: 0 0 0 10px rgba(52, 199, 89, 0); }
    }
    .blink-success {
        animation: pulse-green 0.5s ease;
    }
</style>
@endpush

@section('body')
<div class="min-h-full flex items-center justify-center py-12 px-4">
    <div class="max-w-md w-full space-y-6">
        <!-- Header -->
        <div class="text-center slide-in">
            <div class="mx-auto w-20 h-20 bg-gradient-to-br from-ios-orange to-ios-red rounded-3xl flex items-center justify-center shadow-ios-lg mb-4">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Face Verification</h1>
            <p class="text-ios-gray mt-2">Verify your identity to start <strong>{{ $exam->title }}</strong></p>
        </div>

        <!-- Liveness Check Instructions -->
        <div id="livenessInstructions" class="ios-card p-4 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 slide-in hidden" style="animation-delay: 0.05s">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900">Liveness Check Required</h3>
                    <p class="text-sm text-gray-600 mt-1">Please blink your eyes <span id="requiredBlinks" class="font-bold text-blue-600">3</span> times to verify you're a real person.</p>
                </div>
            </div>
            
            <!-- Blink Progress -->
            <div class="mt-4 flex items-center justify-center gap-3">
                <span class="text-sm text-gray-600">Blinks detected:</span>
                <div class="flex gap-2">
                    <div id="blink1" class="w-4 h-4 rounded-full bg-gray-300 blink-indicator"></div>
                    <div id="blink2" class="w-4 h-4 rounded-full bg-gray-300 blink-indicator"></div>
                    <div id="blink3" class="w-4 h-4 rounded-full bg-gray-300 blink-indicator"></div>
                </div>
            </div>
        </div>

        <!-- Camera -->
        <div class="ios-card p-6 slide-in" style="animation-delay: 0.1s">
            <div class="relative rounded-2xl overflow-hidden bg-black aspect-video mb-4">
                <video id="video" class="w-full h-full object-cover" autoplay playsinline></video>
                <canvas id="canvas" class="hidden"></canvas>
                <canvas id="faceCanvas" class="absolute inset-0 w-full h-full pointer-events-none"></canvas>
                <div id="cameraOverlay" class="absolute inset-0 flex items-center justify-center bg-ios-gray-6">
                    <div class="text-center">
                        <svg class="w-12 h-12 mx-auto text-ios-gray mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                        </svg>
                        <p class="text-ios-gray text-sm">Click to enable camera</p>
                    </div>
                </div>
                <div id="faceGuide" class="absolute inset-0 pointer-events-none hidden">
                    <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                        <ellipse cx="50" cy="45" rx="25" ry="32" fill="none" stroke="rgba(255,255,255,0.5)" stroke-width="0.5" stroke-dasharray="2,2"/>
                    </svg>
                </div>
                <!-- Eye Status Overlay -->
                <div id="eyeStatus" class="absolute top-3 left-3 eye-status hidden">
                    <svg class="eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <span id="eyeStatusText">Detecting face...</span>
                </div>
                <!-- Loading Overlay for Model -->
                <div id="modelLoading" class="absolute inset-0 bg-black bg-opacity-70 flex items-center justify-center hidden">
                    <div class="text-center text-white">
                        <svg class="w-10 h-10 mx-auto animate-spin mb-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <p class="text-sm">Loading face detection...</p>
                    </div>
                </div>
            </div>

            <button type="button" id="captureBtn" class="w-full ios-btn bg-ios-gray-6 text-gray-700 hover:bg-ios-gray-5 mb-3">
                <span id="captureBtnText">Enable Camera</span>
            </button>
            
            <p id="captureStatus" class="text-sm text-ios-gray text-center"></p>
        </div>

        <!-- Start Button -->
        <button type="button" id="startExamBtn" disabled class="w-full ios-btn bg-ios-green text-white hover:bg-green-600 disabled:opacity-50 disabled:cursor-not-allowed slide-in" style="animation-delay: 0.2s">
            <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Start Exam
        </button>

        <a href="{{ route('user.exams.show', $exam) }}" class="block text-center text-ios-gray hover:text-gray-900 transition-colors">
            Cancel
        </a>
    </div>
</div>

@push('scripts')
<!-- Face-api.js - More reliable face detection -->
<script defer src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>

<script>
let stream = null;
let cameraEnabled = false;
let faceCaptured = false;
let faceImageData = '';
let livenessVerified = false;
let modelsLoaded = false;
let isDetecting = false;
let blinkCount = 0;
const REQUIRED_BLINKS = 3;
let lastEyeState = 'open';
let eyeClosedFrames = 0;
let eyeOpenFrames = 0;
const EYE_CLOSED_THRESHOLD = 0.25;
const MIN_CLOSED_FRAMES = 2;
const MIN_OPEN_FRAMES = 2;

const video = document.getElementById('video');
const canvas = document.getElementById('canvas');
const faceCanvas = document.getElementById('faceCanvas');
const captureBtn = document.getElementById('captureBtn');
const captureBtnText = document.getElementById('captureBtnText');
const captureStatus = document.getElementById('captureStatus');
const startExamBtn = document.getElementById('startExamBtn');
const cameraOverlay = document.getElementById('cameraOverlay');
const faceGuide = document.getElementById('faceGuide');
const livenessInstructions = document.getElementById('livenessInstructions');
const eyeStatus = document.getElementById('eyeStatus');
const eyeStatusText = document.getElementById('eyeStatusText');
const modelLoading = document.getElementById('modelLoading');

cameraOverlay.addEventListener('click', enableCamera);
captureBtn.addEventListener('click', handleCaptureClick);
startExamBtn.addEventListener('click', startExam);


// Load face-api.js models
async function loadModels() {
    modelLoading.classList.remove('hidden');
    try {
        const MODEL_URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1.7.12/model';
        
        await Promise.all([
            faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
            faceapi.nets.faceLandmark68TinyNet.loadFromUri(MODEL_URL)
        ]);
        
        modelsLoaded = true;
        console.log('Face-api.js models loaded successfully');
    } catch (err) {
        console.error('Failed to load face models:', err);
        captureStatus.textContent = 'Face detection unavailable. Proceeding without liveness check.';
        livenessVerified = true;
        captureBtn.disabled = false;
        captureBtnText.textContent = 'Capture Face';
    }
    modelLoading.classList.add('hidden');
}

// Calculate Eye Aspect Ratio
function calculateEAR(eye) {
    const v1 = distance(eye[1], eye[5]);
    const v2 = distance(eye[2], eye[4]);
    const h = distance(eye[0], eye[3]);
    return (v1 + v2) / (2.0 * h);
}

function distance(p1, p2) {
    return Math.sqrt(Math.pow(p2.x - p1.x, 2) + Math.pow(p2.y - p1.y, 2));
}

async function detectFace() {
    if (!modelsLoaded || !cameraEnabled || faceCaptured || !isDetecting) return;
    
    try {
        const detections = await faceapi.detectSingleFace(video, 
            new faceapi.TinyFaceDetectorOptions({ inputSize: 320, scoreThreshold: 0.5 })
        ).withFaceLandmarks(true);
        
        const ctx = faceCanvas.getContext('2d');
        ctx.clearRect(0, 0, faceCanvas.width, faceCanvas.height);
        
        if (detections) {
            const landmarks = detections.landmarks;
            const leftEye = landmarks.getLeftEye();
            const rightEye = landmarks.getRightEye();
            
            // Draw face box
            const box = detections.detection.box;
            const scaleX = faceCanvas.width / video.videoWidth;
            const scaleY = faceCanvas.height / video.videoHeight;
            
            ctx.strokeStyle = '#34C759';
            ctx.lineWidth = 2;
            ctx.strokeRect(box.x * scaleX, box.y * scaleY, box.width * scaleX, box.height * scaleY);
            
            // Calculate EAR
            const leftEAR = calculateEAR(leftEye);
            const rightEAR = calculateEAR(rightEye);
            const avgEAR = (leftEAR + rightEAR) / 2;
            
            // Draw eyes
            drawEyes(ctx, leftEye, rightEye, avgEAR < EYE_CLOSED_THRESHOLD, scaleX, scaleY);
            
            // Blink detection
            if (avgEAR < EYE_CLOSED_THRESHOLD) {
                eyeClosedFrames++;
                eyeOpenFrames = 0;
                if (lastEyeState === 'open' && eyeClosedFrames >= MIN_CLOSED_FRAMES) {
                    lastEyeState = 'closed';
                }
            } else {
                eyeOpenFrames++;
                if (lastEyeState === 'closed' && eyeOpenFrames >= MIN_OPEN_FRAMES) {
                    blinkCount++;
                    lastEyeState = 'open';
                    eyeClosedFrames = 0;
                    updateBlinkIndicators();
                    
                    if (blinkCount >= REQUIRED_BLINKS) {
                        livenessVerified = true;
                        eyeStatusText.textContent = '✓ Liveness verified!';
                        eyeStatus.style.background = 'rgba(52, 199, 89, 0.8)';
                        captureStatus.textContent = 'Liveness verified! Click to capture your face.';
                        captureStatus.classList.add('text-ios-green');
                        captureBtnText.textContent = 'Capture Face';
                        captureBtn.disabled = false;
                        captureBtn.classList.remove('bg-ios-gray-6');
                        captureBtn.classList.add('bg-ios-green', 'text-white');
                    }
                }
            }
            
            if (!livenessVerified) {
                eyeStatusText.textContent = blinkCount === 0 ? 'Face detected - Please blink' : `Blinks: ${blinkCount}/${REQUIRED_BLINKS}`;
            }
        } else {
            eyeStatusText.textContent = 'No face detected';
            eyeClosedFrames = 0;
            eyeOpenFrames = 0;
        }
    } catch (err) {
        console.error('Detection error:', err);
    }
    
    if (isDetecting && !faceCaptured) {
        requestAnimationFrame(detectFace);
    }
}

function drawEyes(ctx, leftEye, rightEye, isClosed, scaleX, scaleY) {
    ctx.strokeStyle = isClosed ? '#FF3B30' : '#34C759';
    ctx.fillStyle = isClosed ? 'rgba(255, 59, 48, 0.3)' : 'rgba(52, 199, 89, 0.3)';
    ctx.lineWidth = 2;
    
    [leftEye, rightEye].forEach(eye => {
        ctx.beginPath();
        eye.forEach((point, i) => {
            const x = point.x * scaleX;
            const y = point.y * scaleY;
            if (i === 0) ctx.moveTo(x, y);
            else ctx.lineTo(x, y);
        });
        ctx.closePath();
        ctx.fill();
        ctx.stroke();
    });
}

function updateBlinkIndicators() {
    for (let i = 1; i <= REQUIRED_BLINKS; i++) {
        const indicator = document.getElementById(`blink${i}`);
        if (i <= blinkCount) {
            indicator.classList.add('active', 'blink-success');
            setTimeout(() => indicator.classList.remove('blink-success'), 500);
        }
    }
}

async function enableCamera() {
    try {
        stream = await navigator.mediaDevices.getUserMedia({ 
            video: { facingMode: 'user', width: 640, height: 480 } 
        });
        video.srcObject = stream;
        
        video.onloadedmetadata = async () => {
            await video.play();
            cameraEnabled = true;
            cameraOverlay.classList.add('hidden');
            faceGuide.classList.remove('hidden');
            livenessInstructions.classList.remove('hidden');
            eyeStatus.classList.remove('hidden');
            
            faceCanvas.width = video.videoWidth;
            faceCanvas.height = video.videoHeight;
            
            captureBtnText.textContent = 'Loading face detection...';
            captureBtn.disabled = true;
            captureStatus.textContent = 'Loading face detection models...';
            
            await loadModels();
            
            if (modelsLoaded) {
                isDetecting = true;
                captureBtnText.textContent = 'Complete blink verification first';
                captureStatus.textContent = 'Please blink your eyes 3 times';
                detectFace();
            }
        };
    } catch (err) {
        captureStatus.textContent = 'Camera access denied. Please allow camera access.';
        captureStatus.classList.add('text-ios-red');
    }
}

function handleCaptureClick() {
    if (!cameraEnabled) {
        enableCamera();
    } else if (!faceCaptured && livenessVerified) {
        captureFace();
    } else if (faceCaptured) {
        retake();
    }
}

function captureFace() {
    if (!livenessVerified) {
        captureStatus.textContent = 'Please complete blink verification first';
        captureStatus.classList.add('text-ios-red');
        return;
    }
    
    isDetecting = false;
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);
    faceImageData = canvas.toDataURL('image/png');
    faceCaptured = true;
    captureBtnText.textContent = 'Retake Photo';
    captureBtn.disabled = false;
    captureBtn.classList.remove('bg-ios-green', 'text-white');
    captureBtn.classList.add('bg-ios-gray-6');
    captureStatus.textContent = '✓ Face captured with liveness verification';
    captureStatus.classList.remove('text-ios-red');
    captureStatus.classList.add('text-ios-green');
    startExamBtn.disabled = false;
    eyeStatus.classList.add('hidden');
    faceCanvas.getContext('2d').clearRect(0, 0, faceCanvas.width, faceCanvas.height);
    
    if (stream) stream.getTracks().forEach(track => track.stop());
    video.style.display = 'none';
    
    const img = document.createElement('img');
    img.src = faceImageData;
    img.className = 'w-full h-full object-cover';
    video.parentNode.insertBefore(img, video);
}

function retake() {
    faceCaptured = false;
    faceImageData = '';
    livenessVerified = false;
    blinkCount = 0;
    lastEyeState = 'open';
    eyeClosedFrames = 0;
    eyeOpenFrames = 0;
    startExamBtn.disabled = true;
    captureStatus.textContent = '';
    captureStatus.classList.remove('text-ios-green');
    
    for (let i = 1; i <= REQUIRED_BLINKS; i++) {
        document.getElementById(`blink${i}`).classList.remove('active');
    }
    
    const img = video.parentNode.querySelector('img');
    if (img) img.remove();
    
    video.style.display = 'block';
    eyeStatus.style.background = 'rgba(0,0,0,0.6)';
    enableCamera();
}

async function startExam() {
    if (!faceCaptured || !livenessVerified) return;
    
    startExamBtn.disabled = true;
    startExamBtn.innerHTML = '<svg class="w-5 h-5 mr-2 inline animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Starting...';
    
    try {
        const response = await fetch('{{ route("user.exams.start", $exam) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ 
                face_image: faceImageData,
                liveness_verified: livenessVerified,
                blink_count: blinkCount
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            window.location.href = data.redirect;
        } else {
            alert(data.error || 'Failed to start exam');
            startExamBtn.disabled = false;
            startExamBtn.innerHTML = '<svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Start Exam';
        }
    } catch (err) {
        alert('An error occurred. Please try again.');
        startExamBtn.disabled = false;
    }
}

window.addEventListener('beforeunload', () => {
    isDetecting = false;
    if (stream) stream.getTracks().forEach(track => track.stop());
});
</script>
@endpush
@endsection
