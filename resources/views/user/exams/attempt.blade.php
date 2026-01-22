@extends('layouts.app')

@section('title', 'Exam - ' . $exam->title)

@section('body')
<div class="min-h-full bg-ios-gray-6">
    <!-- Fixed Header -->
    <header class="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-xl border-b border-ios-gray-5">
        <div class="max-w-4xl mx-auto px-4 py-3">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="font-semibold text-gray-900 truncate">{{ $exam->title }}</h1>
                    <p class="text-sm text-ios-gray">Question <span id="currentQ">1</span> of {{ $questions->count() }}</p>
                </div>
                <div class="flex items-center space-x-4">
                    <!-- Proctoring Status -->
                    <div id="proctoringStatus" class="flex items-center space-x-2 px-3 py-1.5 bg-ios-green/10 text-ios-green rounded-xl text-sm font-medium">
                        <div class="w-2 h-2 bg-ios-green rounded-full animate-pulse"></div>
                        <span>Proctoring Active</span>
                    </div>
                    <div id="timer" class="flex items-center space-x-2 px-4 py-2 bg-ios-red/10 text-ios-red rounded-xl font-mono font-semibold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span id="timerDisplay">--:--</span>
                    </div>
                    <button type="button" onclick="submitExam()" class="ios-btn bg-ios-green text-white hover:bg-green-600 text-sm">
                        Submit
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Proctoring Camera (Fixed Position) -->
    <div id="proctorCamera" class="fixed top-20 right-4 z-40 w-48 rounded-2xl overflow-hidden shadow-ios-lg border-2 border-ios-green bg-black">
        <video id="proctorVideo" class="w-full h-36 object-cover" autoplay playsinline muted></video>
        <canvas id="proctorCanvas" class="hidden"></canvas>
        <div id="cameraStatus" class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent p-2">
            <div class="flex items-center justify-between text-white text-xs">
                <span id="faceStatus" class="flex items-center space-x-1">
                    <span class="w-2 h-2 bg-ios-green rounded-full"></span>
                    <span>Face OK</span>
                </span>
                <span id="gazeStatus" class="flex items-center space-x-1">
                    <span class="w-2 h-2 bg-ios-green rounded-full"></span>
                    <span>Gaze OK</span>
                </span>
            </div>
        </div>
        <!-- Warning Overlay -->
        <div id="cameraWarning" class="absolute inset-0 bg-ios-red/90 flex items-center justify-center hidden">
            <div class="text-center text-white p-2">
                <svg class="w-8 h-8 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <p class="text-xs font-semibold" id="warningText">Look at screen!</p>
            </div>
        </div>
    </div>

    <!-- Tab Switch Warning -->
    <div id="tabWarning" class="fixed top-20 left-4 right-4 z-40 hidden">
        <div class="max-w-4xl mx-auto p-4 bg-ios-red text-white rounded-xl shadow-ios-lg flex items-center space-x-3">
            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div>
                <p class="font-semibold">Tab Switch Detected!</p>
                <p class="text-sm text-white/80">Warning count: <span id="tabSwitchCount">0</span></p>
            </div>
        </div>
    </div>

    <!-- Gaze Warning Toast -->
    <div id="gazeWarning" class="fixed top-20 left-4 z-40 hidden transition-all duration-300">
        <div class="p-4 bg-ios-orange text-white rounded-xl shadow-ios-lg flex items-center space-x-3">
            <svg class="w-6 h-6 flex-shrink-0 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            <div>
                <p class="font-semibold">Please look at the screen!</p>
                <p class="text-sm text-white/80">Gaze warnings: <span id="gazeWarningCount">0</span></p>
            </div>
        </div>
    </div>

    <!-- No Face Warning Toast -->
    <div id="noFaceWarning" class="fixed top-20 left-4 z-40 hidden transition-all duration-300">
        <div class="p-4 bg-ios-red text-white rounded-xl shadow-ios-lg flex items-center space-x-3">
            <svg class="w-6 h-6 flex-shrink-0 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <p class="font-semibold">Face not detected!</p>
                <p class="text-sm text-white/80">Please stay in front of camera</p>
            </div>
        </div>
    </div>

    <!-- Questions -->
    <main class="pt-24 pb-32 px-4">
        <div class="max-w-4xl mx-auto space-y-6">
            @foreach($questions as $index => $question)
                <div class="ios-card p-6 question-card {{ $index === 0 ? '' : 'hidden' }}" data-question="{{ $index }}">
                    <div class="flex items-start space-x-4 mb-6">
                        <div class="w-10 h-10 bg-ios-blue rounded-xl flex items-center justify-center text-white font-semibold flex-shrink-0">
                            {{ $index + 1 }}
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $question->type === 'mcq' ? 'bg-ios-blue/10 text-ios-blue' : 'bg-ios-purple/10 text-ios-purple' }}">
                                    {{ strtoupper($question->type) }}
                                </span>
                                <span class="text-sm text-ios-gray">{{ $question->marks }} marks</span>
                            </div>
                            <p class="text-lg text-gray-900">{{ $question->question }}</p>
                        </div>
                    </div>

                    @if($question->isMcq())
                        <div class="space-y-3 ml-14">
                            @foreach($question->options as $key => $option)
                                <label class="block cursor-pointer">
                                    <input type="radio" name="answer_{{ $question->id }}" value="{{ $key }}" 
                                        {{ isset($answers[$question->id]) && $answers[$question->id]->answer === $key ? 'checked' : '' }}
                                        class="sr-only peer" onchange="saveAnswer({{ $question->id }}, '{{ $key }}')">
                                    <div class="p-4 rounded-xl border-2 border-ios-gray-5 peer-checked:border-ios-blue peer-checked:bg-ios-blue/5 transition-all flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-full border-2 border-ios-gray-4 peer-checked:border-ios-blue peer-checked:bg-ios-blue flex items-center justify-center font-semibold text-sm transition-all">
                                            {{ $key }}
                                        </div>
                                        <span class="text-gray-700">{{ $option }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <div class="ml-14">
                            <textarea name="answer_{{ $question->id }}" rows="6" 
                                class="ios-input" placeholder="Type your answer here..."
                                onblur="saveAnswer({{ $question->id }}, this.value)">{{ $answers[$question->id]->answer ?? '' }}</textarea>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </main>

    <!-- Fixed Footer Navigation -->
    <footer class="fixed bottom-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-xl border-t border-ios-gray-5">
        <div class="max-w-4xl mx-auto px-4 py-4">
            <!-- Question Navigator -->
            <div class="flex items-center justify-center space-x-2 mb-4 overflow-x-auto pb-2">
                @foreach($questions as $index => $question)
                    <button type="button" onclick="goToQuestion({{ $index }})" 
                        class="question-nav w-10 h-10 rounded-xl flex items-center justify-center font-semibold text-sm transition-all
                        {{ $index === 0 ? 'bg-ios-blue text-white' : 'bg-ios-gray-6 text-gray-600 hover:bg-ios-gray-5' }}"
                        data-nav="{{ $index }}"
                        data-answered="{{ isset($answers[$question->id]) ? 'true' : 'false' }}">
                        {{ $index + 1 }}
                    </button>
                @endforeach
            </div>
            
            <!-- Prev/Next -->
            <div class="flex items-center justify-between">
                <button type="button" onclick="prevQuestion()" id="prevBtn" disabled
                    class="ios-btn bg-ios-gray-6 text-gray-700 hover:bg-ios-gray-5 disabled:opacity-50">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Previous
                </button>
                <button type="button" onclick="nextQuestion()" id="nextBtn"
                    class="ios-btn bg-ios-blue text-white hover:bg-blue-600">
                    Next
                    <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </div>
    </footer>
</div>

@push('scripts')
<!-- Face-api.js for proctoring -->
<script defer src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>

<script>
const totalQuestions = {{ $questions->count() }};
let currentQuestion = 0;
let remainingTime = {{ $remainingTime }};
let tabSwitchCount = {{ $attempt->tab_switch_count }};

// Proctoring variables
let proctorStream = null;
let proctorModel = null;
let isProctoring = false;
let gazeWarningCount = 0;
let noFaceWarningCount = 0;
let lastFaceDetected = true;
let lastGazeOk = true;
let consecutiveNoFace = 0;
let consecutiveBadGaze = 0;
const MAX_CONSECUTIVE_WARNINGS = 5;

// Timer
function updateTimer() {
    if (remainingTime <= 0) {
        submitExam();
        return;
    }
    
    const minutes = Math.floor(remainingTime / 60);
    const seconds = remainingTime % 60;
    document.getElementById('timerDisplay').textContent = 
        `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    
    if (remainingTime <= 300) {
        document.getElementById('timer').classList.add('animate-pulse');
    }
    
    remainingTime--;
}

setInterval(updateTimer, 1000);
updateTimer();

// Initialize Proctoring
async function initProctoring() {
    try {
        // Start camera
        proctorStream = await navigator.mediaDevices.getUserMedia({ 
            video: { facingMode: 'user', width: 320, height: 240 } 
        });
        
        const video = document.getElementById('proctorVideo');
        video.srcObject = proctorStream;
        
        // Load face-api models
        const MODEL_URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1.7.12/model';
        await Promise.all([
            faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
            faceapi.nets.faceLandmark68TinyNet.loadFromUri(MODEL_URL)
        ]);
        
        console.log('Proctoring initialized');
        isProctoring = true;
        startProctoring();
        
    } catch (err) {
        console.error('Proctoring init failed:', err);
        document.getElementById('proctoringStatus').innerHTML = `
            <div class="w-2 h-2 bg-ios-red rounded-full"></div>
            <span>Camera Error</span>
        `;
        document.getElementById('proctoringStatus').classList.remove('bg-ios-green/10', 'text-ios-green');
        document.getElementById('proctoringStatus').classList.add('bg-ios-red/10', 'text-ios-red');
    }
}

// Proctoring Detection Loop
async function startProctoring() {
    if (!isProctoring) return;
    
    const video = document.getElementById('proctorVideo');
    
    try {
        const detection = await faceapi.detectSingleFace(video, 
            new faceapi.TinyFaceDetectorOptions({ inputSize: 224, scoreThreshold: 0.4 })
        ).withFaceLandmarks(true);
        
        if (detection) {
            consecutiveNoFace = 0;
            updateFaceStatus(true);
            
            // Check gaze direction using face landmarks
            const landmarks = detection.landmarks;
            const leftEye = landmarks.getLeftEye();
            const rightEye = landmarks.getRightEye();
            const nose = landmarks.getNose();
            const jawOutline = landmarks.getJawOutline();
            const mouth = landmarks.getMouth();
            
            // Get face bounding box
            const box = detection.detection.box;
            const faceWidth = box.width;
            const faceHeight = box.height;
            const faceCenterX = box.x + faceWidth / 2;
            const faceCenterY = box.y + faceHeight / 2;
            
            // === LEFT/RIGHT DETECTION ===
            const noseTip = nose[6]; // Bottom of nose
            const noseTop = nose[0]; // Top of nose (between eyes)
            const noseOffsetX = noseTip.x - faceCenterX;
            const horizontalRatio = Math.abs(noseOffsetX) / (faceWidth / 2);
            
            // Jaw-based horizontal detection
            const leftJaw = jawOutline[0];
            const rightJaw = jawOutline[16];
            const jawWidth = rightJaw.x - leftJaw.x;
            const jawCenter = (leftJaw.x + rightJaw.x) / 2;
            const jawNoseOffset = Math.abs(noseTip.x - jawCenter) / jawWidth;
            
            // === UP/DOWN DETECTION ===
            // Calculate eye center
            const leftEyeCenter = {
                x: leftEye.reduce((sum, p) => sum + p.x, 0) / leftEye.length,
                y: leftEye.reduce((sum, p) => sum + p.y, 0) / leftEye.length
            };
            const rightEyeCenter = {
                x: rightEye.reduce((sum, p) => sum + p.x, 0) / rightEye.length,
                y: rightEye.reduce((sum, p) => sum + p.y, 0) / rightEye.length
            };
            const eyesCenterY = (leftEyeCenter.y + rightEyeCenter.y) / 2;
            
            // Mouth center for vertical head pose
            const mouthCenter = {
                x: mouth.reduce((sum, p) => sum + p.x, 0) / mouth.length,
                y: mouth.reduce((sum, p) => sum + p.y, 0) / mouth.length
            };
            
            // Nose-to-eye vertical ratio (changes when looking up/down)
            const noseToEyeDistance = noseTip.y - eyesCenterY;
            const eyeToMouthDistance = mouthCenter.y - eyesCenterY;
            const verticalRatio = noseToEyeDistance / eyeToMouthDistance;
            
            // Face position in frame (looking down = face higher, looking up = face lower)
            const faceVerticalPosition = (box.y + faceHeight / 2) / video.videoHeight;
            
            // Nose tip to nose top angle (vertical head tilt)
            const noseVerticalAngle = (noseTip.y - noseTop.y) / faceHeight;
            
            // Chin position relative to face
            const chinPoint = jawOutline[8]; // Bottom of chin
            const chinToNoseRatio = (chinPoint.y - noseTip.y) / faceHeight;
            
            // Eye tilt detection (head rotation left/right)
            const eyeTilt = Math.abs(leftEyeCenter.y - rightEyeCenter.y) / faceWidth;
            
            // === DETECTION THRESHOLDS ===
            // Looking left/right
            const isLookingLeftRight = horizontalRatio > 0.25 || jawNoseOffset > 0.15;
            
            // Looking up: nose closer to eyes, chin more visible, face lower in frame
            // Need at least 3 indicators to trigger (more strict)
            const upIndicators = [
                verticalRatio < 0.30,           // Nose much closer to eyes
                chinToNoseRatio > 0.38,         // Much more chin visible
                faceVerticalPosition > 0.65,    // Face much lower in frame
                noseVerticalAngle < 0.28        // Nose angle much smaller
            ].filter(Boolean).length;
            const isLookingUp = upIndicators >= 3;
            
            // Looking down: nose further from eyes, less chin visible, face higher in frame
            // Need at least 3 indicators to trigger (more strict)
            const downIndicators = [
                verticalRatio > 0.58,           // Nose much further from eyes
                chinToNoseRatio < 0.12,         // Much less chin visible
                faceVerticalPosition < 0.28,    // Face much higher in frame
                noseVerticalAngle > 0.50        // Nose angle much larger
            ].filter(Boolean).length;
            const isLookingDown = downIndicators >= 3;
            
            // Head tilted sideways
            const isHeadTilted = eyeTilt > 0.10;
            
            // Face at edge of frame (only extreme edges)
            const isFaceAtEdge = box.x < 5 || (box.x + box.width) > (video.videoWidth - 5) ||
                                 box.y < 5 || (box.y + box.height) > (video.videoHeight - 5);
            
            // Combined detection
            const isLookingAway = isLookingLeftRight || isLookingUp || isLookingDown || isHeadTilted || isFaceAtEdge;
            
            // Determine direction for better feedback
            let lookDirection = '';
            if (isLookingLeftRight) lookDirection = 'left/right';
            else if (isLookingUp) lookDirection = 'up';
            else if (isLookingDown) lookDirection = 'down';
            else if (isHeadTilted) lookDirection = 'tilted';
            else if (isFaceAtEdge) lookDirection = 'edge';
            
            // Debug info
            console.log('Gaze:', { 
                horizontal: horizontalRatio.toFixed(2), 
                vertical: verticalRatio.toFixed(2),
                chinRatio: chinToNoseRatio.toFixed(2),
                facePos: faceVerticalPosition.toFixed(2),
                noseAngle: noseVerticalAngle.toFixed(2),
                upIndicators: upIndicators,
                downIndicators: downIndicators,
                direction: lookDirection,
                isLookingAway 
            });
            
            if (isLookingAway) {
                consecutiveBadGaze++;
                if (consecutiveBadGaze >= 4) { // Need 4 consecutive detections
                    updateGazeStatus(false, lookDirection);
                    showGazeWarning(lookDirection);
                }
            } else {
                consecutiveBadGaze = 0;
                updateGazeStatus(true);
                hideGazeWarning();
            }
            
        } else {
            consecutiveNoFace++;
            if (consecutiveNoFace >= MAX_CONSECUTIVE_WARNINGS) {
                updateFaceStatus(false);
                showNoFaceWarning();
            }
        }
        
    } catch (err) {
        console.error('Proctoring detection error:', err);
    }
    
    // Continue loop - faster detection
    if (isProctoring) {
        setTimeout(startProctoring, 300); // Check every 300ms for faster response
    }
}

function updateFaceStatus(detected) {
    const faceStatus = document.getElementById('faceStatus');
    const cameraWarning = document.getElementById('cameraWarning');
    const proctorCamera = document.getElementById('proctorCamera');
    
    if (detected) {
        faceStatus.innerHTML = '<span class="w-2 h-2 bg-ios-green rounded-full"></span><span>Face OK</span>';
        proctorCamera.classList.remove('border-ios-red');
        proctorCamera.classList.add('border-ios-green');
        hideNoFaceWarning();
    } else {
        faceStatus.innerHTML = '<span class="w-2 h-2 bg-ios-red rounded-full animate-pulse"></span><span>No Face!</span>';
        proctorCamera.classList.remove('border-ios-green');
        proctorCamera.classList.add('border-ios-red');
    }
}

function updateGazeStatus(ok, direction = '') {
    const gazeStatus = document.getElementById('gazeStatus');
    
    if (ok) {
        gazeStatus.innerHTML = '<span class="w-2 h-2 bg-ios-green rounded-full"></span><span>Gaze OK</span>';
    } else {
        let statusText = 'Look here!';
        if (direction === 'up') statusText = 'Looking Up!';
        else if (direction === 'down') statusText = 'Looking Down!';
        else if (direction === 'left/right') statusText = 'Looking Away!';
        else if (direction === 'tilted') statusText = 'Head Tilted!';
        else if (direction === 'edge') statusText = 'Move Center!';
        
        gazeStatus.innerHTML = `<span class="w-2 h-2 bg-ios-orange rounded-full animate-pulse"></span><span>${statusText}</span>`;
    }
}

function showGazeWarning(direction = '') {
    if (!lastGazeOk) return;
    lastGazeOk = false;
    gazeWarningCount++;
    
    // Direction-specific warning messages
    let warningMessage = 'Please look at the screen!';
    let cameraWarningText = 'Look at screen!';
    
    if (direction === 'up') {
        warningMessage = 'You are looking UP!';
        cameraWarningText = 'Looking Up!';
    } else if (direction === 'down') {
        warningMessage = 'You are looking DOWN!';
        cameraWarningText = 'Looking Down!';
    } else if (direction === 'left/right') {
        warningMessage = 'You are looking AWAY!';
        cameraWarningText = 'Looking Away!';
    } else if (direction === 'tilted') {
        warningMessage = 'Your head is TILTED!';
        cameraWarningText = 'Head Tilted!';
    } else if (direction === 'edge') {
        warningMessage = 'Move to CENTER of camera!';
        cameraWarningText = 'Move Center!';
    }
    
    document.getElementById('gazeWarningCount').textContent = gazeWarningCount;
    document.getElementById('gazeWarning').querySelector('.font-semibold').textContent = warningMessage;
    document.getElementById('gazeWarning').classList.remove('hidden');
    document.getElementById('cameraWarning').classList.remove('hidden');
    document.getElementById('warningText').textContent = cameraWarningText;
    
    // Log warning with direction
    logProctoringEvent('gaze_warning', { count: gazeWarningCount, direction: direction });
    
    // Play warning sound
    playWarningSound();
}

function hideGazeWarning() {
    lastGazeOk = true;
    document.getElementById('gazeWarning').classList.add('hidden');
    document.getElementById('cameraWarning').classList.add('hidden');
}

function showNoFaceWarning() {
    if (!lastFaceDetected) return;
    lastFaceDetected = false;
    noFaceWarningCount++;
    
    document.getElementById('noFaceWarning').classList.remove('hidden');
    document.getElementById('cameraWarning').classList.remove('hidden');
    document.getElementById('warningText').textContent = 'Face not visible!';
    
    // Log warning
    logProctoringEvent('no_face_warning', { count: noFaceWarningCount });
    
    // Play warning sound
    playWarningSound();
}

function hideNoFaceWarning() {
    lastFaceDetected = true;
    document.getElementById('noFaceWarning').classList.add('hidden');
    if (lastGazeOk) {
        document.getElementById('cameraWarning').classList.add('hidden');
    }
}

function playWarningSound() {
    // Create a simple beep sound
    try {
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();
        
        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);
        
        oscillator.frequency.value = 800;
        oscillator.type = 'sine';
        gainNode.gain.value = 0.3;
        
        oscillator.start();
        setTimeout(() => oscillator.stop(), 200);
    } catch (e) {
        console.log('Audio not supported');
    }
}

async function logProctoringEvent(eventType, data) {
    try {
        await fetch('{{ route("user.exams.proctor-log", $attempt) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ event_type: eventType, data: data })
        });
    } catch (err) {
        console.error('Failed to log proctoring event');
    }
}

// Navigation
function goToQuestion(index) {
    document.querySelectorAll('.question-card').forEach((card, i) => {
        card.classList.toggle('hidden', i !== index);
    });
    
    document.querySelectorAll('.question-nav').forEach((nav, i) => {
        nav.classList.toggle('bg-ios-blue', i === index);
        nav.classList.toggle('text-white', i === index);
        nav.classList.toggle('bg-ios-gray-6', i !== index);
        nav.classList.toggle('text-gray-600', i !== index);
    });
    
    currentQuestion = index;
    document.getElementById('currentQ').textContent = index + 1;
    document.getElementById('prevBtn').disabled = index === 0;
    document.getElementById('nextBtn').textContent = index === totalQuestions - 1 ? 'Finish' : 'Next';
}

function prevQuestion() {
    if (currentQuestion > 0) goToQuestion(currentQuestion - 1);
}

function nextQuestion() {
    if (currentQuestion < totalQuestions - 1) {
        goToQuestion(currentQuestion + 1);
    }
}

// Save Answer
async function saveAnswer(questionId, answer) {
    try {
        await fetch('{{ route("user.exams.answer", $attempt) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ question_id: questionId, answer: answer })
        });
        
        // Mark as answered
        const navBtn = document.querySelector(`.question-nav[data-nav="${currentQuestion}"]`);
        if (navBtn) {
            navBtn.dataset.answered = 'true';
            navBtn.classList.add('ring-2', 'ring-ios-green', 'ring-offset-2');
        }
    } catch (err) {
        console.error('Failed to save answer');
    }
}

// Submit Exam
async function submitExam() {
    if (!confirm('Are you sure you want to submit the exam?')) return;
    
    // Stop proctoring
    isProctoring = false;
    if (proctorStream) {
        proctorStream.getTracks().forEach(track => track.stop());
    }
    
    try {
        const response = await fetch('{{ route("user.exams.submit", $attempt) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                gaze_warnings: gazeWarningCount,
                no_face_warnings: noFaceWarningCount
            })
        });
        
        const data = await response.json();
        if (data.success) {
            window.location.href = data.redirect;
        }
    } catch (err) {
        alert('Failed to submit exam. Please try again.');
    }
}

// Tab Switch Detection
document.addEventListener('visibilitychange', async function() {
    if (document.hidden) {
        tabSwitchCount++;
        document.getElementById('tabSwitchCount').textContent = tabSwitchCount;
        document.getElementById('tabWarning').classList.remove('hidden');
        
        setTimeout(() => {
            document.getElementById('tabWarning').classList.add('hidden');
        }, 5000);
        
        await fetch('{{ route("user.exams.tab-switch", $attempt) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
    }
});

// Prevent right-click
document.addEventListener('contextmenu', e => e.preventDefault());

// Prevent keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey && (e.key === 'c' || e.key === 'v' || e.key === 'u' || e.key === 'p')) {
        e.preventDefault();
    }
    if (e.key === 'F12' || (e.ctrlKey && e.shiftKey && e.key === 'I')) {
        e.preventDefault();
    }
});

// Initialize proctoring when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Wait for face-api to load
    const checkFaceApi = setInterval(() => {
        if (typeof faceapi !== 'undefined') {
            clearInterval(checkFaceApi);
            initProctoring();
        }
    }, 100);
});

// Cleanup on page unload
window.addEventListener('beforeunload', () => {
    isProctoring = false;
    if (proctorStream) {
        proctorStream.getTracks().forEach(track => track.stop());
    }
});
</script>
@endpush
@endsection
