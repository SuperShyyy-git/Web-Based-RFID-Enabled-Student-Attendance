@extends('layout.app')

@section('title', 'Attendance Scanner')

@section('content')
<style>
    .scanner-container {
        max-width: 900px;
        margin: 20px auto;
        padding: 15px;
    }
    .video-container {
        position: relative;
        width: 100%;
        background: #000;
        border-radius: 10px;
        overflow: hidden;
    }
    .video-container video {
        width: 100%;
        display: block;
    }
    .video-container canvas {
        position: absolute;
        top: 0;
        left: 0;
    }
    #status {
        padding: 15px;
        text-align: center;
        font-size: 16px;
        background: #f8f9fa;
        border-radius: 8px;
        margin-top: 15px;
    }
    #rfidInput {
        width: 100%;
        padding: 15px;
        font-size: 18px;
        text-align: center;
        border: 2px solid #007bff;
        border-radius: 8px;
        margin-top: 15px;
    }
    #rfidInput:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(0,123,255,0.25);
    }
</style>

<div class="scanner-container">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">📷 Attendance Scanner</h5>
        </div>
        <div class="card-body p-2">
            <div class="video-container" id="videoContainer">
                <video id="video" autoplay muted playsinline></video>
                <canvas id="canvas"></canvas>
            </div>
        </div>
    </div>
    
    <div id="status">⏳ Loading...</div>
    
    <input type="text" id="rfidInput" placeholder="Tap your RFID card" autocomplete="off">
</div>

<canvas id="captureCanvas" style="display:none;"></canvas>

<!-- Load face-api.js -->
<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>

<script>
const video = document.getElementById('video');
const canvas = document.getElementById('canvas');
const status = document.getElementById('status');
const rfidInput = document.getElementById('rfidInput');

// Focus RFID
rfidInput.focus();
document.body.addEventListener('click', () => rfidInput.focus());

async function start() {
    try {
        // 1. Start camera
        status.textContent = '📷 Starting camera...';
        const stream = await navigator.mediaDevices.getUserMedia({
            video: { width: 640, height: 480, facingMode: 'user' }
        });
        video.srcObject = stream;
        
        await new Promise(resolve => {
            video.onloadedmetadata = () => {
                video.play();
                resolve();
            };
        });
        
        // Set canvas size to match video
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        
        status.textContent = '⏳ Loading face detection models...';
        
        // 2. Load models
        const MODEL_URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model';
        await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
        await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
        
        status.textContent = '✅ Ready! Face detection active.';
        status.style.background = '#d4edda';
        status.style.color = '#155724';
        
        // 3. Start detection loop
        setInterval(detectFaces, 100);
        
    } catch (err) {
        console.error('Error:', err);
        status.textContent = '❌ Error: ' + err.message;
        status.style.background = '#f8d7da';
        status.style.color = '#721c24';
    }
}

async function detectFaces() {
    // Match canvas to displayed video size
    const displaySize = { width: video.offsetWidth, height: video.offsetHeight };
    
    if (canvas.width !== displaySize.width || canvas.height !== displaySize.height) {
        canvas.width = displaySize.width;
        canvas.height = displaySize.height;
    }
    
    // Detect faces with landmarks
    const detections = await faceapi
        .detectAllFaces(video, new faceapi.TinyFaceDetectorOptions())
        .withFaceLandmarks();
    
    // Resize results to match display size
    const resizedDetections = faceapi.resizeResults(detections, displaySize);
    
    // Clear canvas
    const ctx = canvas.getContext('2d');
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    
    if (resizedDetections.length > 0) {
        resizedDetections.forEach(det => {
            drawWhiteMesh(ctx, det.landmarks.positions);
        });
        status.textContent = '✅ Face detected! Ready to scan RFID.';
    } else {
        status.textContent = '👤 Please look at the camera...';
    }
}

// Draw white triangulated mesh like reference image
function drawWhiteMesh(ctx, points) {
    // Triangulation pattern for face mesh
    const triangles = [
        // Forehead/eyebrows
        [17, 18, 37], [18, 19, 38], [19, 20, 39], [20, 21, 39],
        [22, 23, 42], [23, 24, 43], [24, 25, 44], [25, 26, 45],
        [17, 36, 37], [26, 45, 16],
        // Between eyebrows
        [21, 22, 27], [21, 27, 39], [22, 27, 42],
        // Nose bridge to eyes
        [27, 28, 39], [27, 28, 42],
        [28, 29, 39], [28, 29, 42],
        [39, 28, 40], [42, 28, 47],
        // Nose
        [29, 30, 31], [29, 30, 35],
        [30, 31, 32], [30, 32, 33], [30, 33, 34], [30, 34, 35],
        // Eyes
        [36, 37, 41], [37, 38, 40], [38, 39, 40], [40, 41, 37],
        [42, 43, 47], [43, 44, 46], [44, 45, 46], [46, 47, 43],
        // Cheeks - left side
        [0, 1, 36], [1, 2, 41], [2, 3, 31], [3, 4, 48],
        [4, 5, 48], [5, 6, 59], [6, 7, 58], [7, 8, 57],
        // Cheeks - right side
        [16, 15, 45], [15, 14, 46], [14, 13, 35], [13, 12, 54],
        [12, 11, 54], [11, 10, 55], [10, 9, 56], [9, 8, 57],
        // Nose to cheeks
        [31, 40, 41], [31, 41, 2], [35, 47, 46], [35, 46, 14],
        [31, 32, 48], [32, 33, 51], [33, 34, 51], [34, 35, 54],
        [48, 49, 31], [49, 50, 32], [50, 51, 33], [51, 52, 34], [52, 53, 35], [53, 54, 35],
        // Mouth area
        [48, 59, 60], [59, 58, 67], [58, 57, 66], [57, 56, 65],
        [54, 55, 64], [55, 56, 65],
        // Inner mouth connections
        [60, 61, 67], [61, 62, 66], [62, 63, 65], [63, 64, 65],
    ];

    // Style - white with glow effect
    ctx.strokeStyle = 'rgba(255, 255, 255, 0.8)';
    ctx.lineWidth = 1;
    ctx.shadowColor = 'white';
    ctx.shadowBlur = 3;
    
    // Draw all triangle lines
    triangles.forEach(tri => {
        const [a, b, c] = tri;
        if (points[a] && points[b] && points[c]) {
            ctx.beginPath();
            ctx.moveTo(points[a].x, points[a].y);
            ctx.lineTo(points[b].x, points[b].y);
            ctx.lineTo(points[c].x, points[c].y);
            ctx.closePath();
            ctx.stroke();
        }
    });
    
    // Draw bright white dots at key landmark points
    ctx.shadowBlur = 8;
    ctx.fillStyle = 'white';
    
    // Key points: jawline, eyebrows, eyes, nose, mouth
    const dotPoints = [
        0, 2, 4, 6, 8, 10, 12, 14, 16,  // Jawline
        17, 19, 21, 22, 24, 26,          // Eyebrows
        36, 37, 38, 39, 40, 41,          // Left eye
        42, 43, 44, 45, 46, 47,          // Right eye
        27, 28, 29, 30, 31, 33, 35,      // Nose
        48, 51, 54, 57, 60, 62, 64, 66   // Mouth
    ];
    
    dotPoints.forEach(i => {
        if (points[i]) {
            ctx.beginPath();
            ctx.arc(points[i].x, points[i].y, 3, 0, Math.PI * 2);
            ctx.fill();
        }
    });
    
    // Reset shadow
    ctx.shadowBlur = 0;
}

// RFID handling
rfidInput.addEventListener('keydown', async function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        const rfid = this.value.trim();
        if (!rfid) return;
        
        status.textContent = '⏳ Processing...';
        
        // Capture image
        const captureCanvas = document.getElementById('captureCanvas');
        captureCanvas.width = video.videoWidth;
        captureCanvas.height = video.videoHeight;
        captureCanvas.getContext('2d').drawImage(video, 0, 0);
        const imageData = captureCanvas.toDataURL('image/png');
        
        try {
            const response = await fetch('/process/login/rfid', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ rfid, image: imageData })
            });
            
            const data = await response.json();
            
            Swal.fire({
                icon: data.success ? 'success' : 'error',
                title: data.success ? 'Success!' : 'Error',
                text: data.message,
                timer: 2500,
                showConfirmButton: false
            });
            
            status.textContent = '✅ Ready! Face detection active.';
            
        } catch (err) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Request failed: ' + err.message,
                timer: 2500
            });
            status.textContent = '✅ Ready! Face detection active.';
        }
        
        this.value = '';
        this.focus();
    }
});

// Shortcut to home
let keys = {};
document.addEventListener('keydown', e => keys[e.key.toLowerCase()] = true);
document.addEventListener('keyup', e => keys[e.key.toLowerCase()] = false);
setInterval(() => { if (keys['r'] && keys['c'] && keys['i']) location.href = '/'; }, 100);

// Start everything
start();
</script>
@endsection
