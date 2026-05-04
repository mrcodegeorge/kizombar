<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="mb-4">
    <h2 class="fw-bold fs-3 mb-1">Shift Attendance</h2>
    <p class="text-muted small fw-500">Verify your presence to start work</p>
</div>

<?php if (isset($_GET['msg']) && $_GET['msg'] === 'clocked_out'): ?>
    <div class="alert alert-success py-2 rounded-3 text-center fw-500 mb-4">
        <i class="fas fa-check-circle me-1"></i> Clock-out successful! See you next time.
    </div>
<?php endif; ?>

<div id="statusCard" class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4 text-center">
        <?php if (!$status || $status['type'] === 'clock_out'): ?>
            <div class="mb-4">
                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                    <i class="fas fa-user-clock text-brand fs-1"></i>
                </div>
                <h4 class="fw-bold">Ready to Clock-in?</h4>
                <p class="text-muted">You are currently clocked out.</p>
            </div>
            <button type="button" id="startVerification" class="btn btn-primary w-100 py-3 fs-5 rounded-pill shadow-sm">
                <i class="fas fa-video me-2"></i> Start Liveness Verification
            </button>
        <?php else: ?>
            <div class="mb-4">
                <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                    <i class="fas fa-check-circle text-success fs-1"></i>
                </div>
                <h4 class="fw-bold">Currently Clocked-in</h4>
                <p class="text-muted">Started at: <?php echo date('H:i', strtotime($status['timestamp'])); ?></p>
                <div class="badge bg-<?php echo $status['verification_status'] === 'approved' ? 'success' : 'warning'; ?> rounded-pill px-3 py-2">
                    Verification: <?php echo ucfirst($status['verification_status']); ?>
                </div>
            </div>
            <form action="index.php?action=handle_clock_out" method="POST">
                <button type="submit" class="btn btn-outline-danger w-100 py-3 fs-5 rounded-pill">
                    <i class="fas fa-sign-out-alt me-2"></i> Clock-out
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>

<!-- Verification Overlay (Hidden by default) -->
<div id="verificationOverlay" class="fixed-top w-100 h-100 bg-white d-none" style="z-index: 9999; overflow-y: auto;">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold m-0">Liveness Check</h3>
            <button type="button" id="cancelVerification" class="btn-close"></button>
        </div>

        <!-- Video Preview -->
        <div class="position-relative mx-auto mb-4" style="max-width: 400px; aspect-ratio: 3/4; background: #000; border-radius: 24px; overflow: hidden; border: 4px solid #f3f4f6;">
            <video id="videoPreview" autoplay muted playsinline class="w-100 h-100" style="object-fit: cover;"></video>
            
            <div id="recIndicator" class="position-absolute top-0 end-0 m-3 d-none align-items-center bg-dark bg-opacity-50 rounded-pill px-2 py-1">
                <div class="bg-danger rounded-circle me-1 blink" style="width: 8px; height: 8px;"></div>
                <span class="text-white fw-bold" style="font-size: 0.7rem;">REC</span>
            </div>

            <!-- Instructions Overlay -->
            <div id="promptOverlay" class="position-absolute bottom-0 start-0 w-100 p-4 text-center text-white" style="background: linear-gradient(transparent, rgba(0,0,0,0.8));">
                <h4 id="currentPrompt" class="fw-bold mb-2">Initialize Camera...</h4>
                <div class="progress mb-2" style="height: 6px; background: rgba(255,255,255,0.2);">
                    <div id="stepProgress" class="progress-bar bg-brand" style="width: 0%"></div>
                </div>
                <p id="subPrompt" class="small mb-0 opacity-75">Please stay in the frame</p>
            </div>

            <!-- Face Frame Guide -->
            <div class="position-absolute top-50 start-50 translate-middle w-75 h-75 border border-white border-2 rounded-circle opacity-25" style="border-style: dashed !important;"></div>
        </div>

        <!-- Start/Next Buttons -->
        <div id="verificationActions" class="text-center d-none">
            <p class="text-muted small mb-3"><i class="fas fa-info-circle me-1"></i> We'll ask you to perform 3 actions and record a voice clip.</p>
            <button type="button" id="startRecording" class="btn btn-brand btn-lg px-5 py-3 rounded-pill fw-bold shadow">
                I'm Ready
            </button>
        </div>

        </div>
    </div>
</div>

<style>
.v-bar { transition: height 0.1s; border-radius: 2px; }
.btn-brand { background: #1a6b3c; color: white; border: none; }
.btn-brand:hover { background: #14522e; color: white; }
.text-brand { color: #1a6b3c; }
.blink { animation: blinker 1s linear infinite; }
@keyframes blinker { 50% { opacity: 0; } }
</style>

<script>
let mediaStream = null;
let mediaRecorder = null;
let audioRecorder = null;
let videoChunks = [];
let audioChunks = [];
let currentStep = 0;

const prompts = [
    { main: "Turn Head Left", sub: "Slowly rotate your head to the left" },
    { main: "Turn Head Right", sub: "Now rotate your head to the right" },
    { main: "Blink 3 Times", sub: "Close and open your eyes clearly" }
];

document.getElementById('startVerification').addEventListener('click', async () => {
    try {
        mediaStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: true });
        document.getElementById('videoPreview').srcObject = mediaStream;
        document.getElementById('verificationOverlay').classList.remove('d-none');
        document.getElementById('currentPrompt').innerText = "Camera Ready";
        document.getElementById('verificationActions').classList.remove('d-none');
    } catch (err) {
        alert("Camera and Microphone access is required for clock-in.");
    }
});

document.getElementById('cancelVerification').addEventListener('click', () => {
    if (mediaStream) mediaStream.getTracks().forEach(t => t.stop());
    document.getElementById('verificationOverlay').classList.add('d-none');
});

document.getElementById('startRecording').addEventListener('click', () => {
    document.getElementById('verificationActions').classList.add('d-none');
    startStepFlow();
});

function getSupportedMimeType(type) {
    const videoTypes = [
        'video/webm;codecs=vp8,opus',
        'video/webm;codecs=h264,opus',
        'video/webm',
        'video/mp4;codecs=avc1',
        'video/mp4'
    ];
    const audioTypes = [
        'audio/webm;codecs=opus',
        'audio/webm',
        'audio/mp4',
        'audio/aac',
        'audio/wav'
    ];
    const types = type === 'video' ? videoTypes : audioTypes;
    for (let t of types) {
        if (MediaRecorder.isTypeSupported(t)) {
            console.log('Using supported ' + type + ' type: ' + t);
            return t;
        }
    }
    return '';
}

function startStepFlow() {
    console.log("startStepFlow triggered");
    videoChunks = [];
    currentStep = 0; 
    
    const videoMime = getSupportedMimeType('video');

    try {
        document.getElementById('recIndicator').classList.remove('d-none');
        
        const videoOptions = videoMime ? { mimeType: videoMime } : {};
        mediaRecorder = new MediaRecorder(mediaStream, videoOptions);
        
        mediaRecorder.ondataavailable = e => { 
            if(e.data && e.data.size > 0) {
                videoChunks.push(e.data);
                console.log("Chunk captured:", e.data.size);
            }
        };

        // Start recording with a 500ms slice to ensure data is collected
        mediaRecorder.start(500); 
        console.log("MediaRecorder started successfully");
        
        // Start the prompt sequence
        runNextPrompt();
    } catch (err) {
        console.error("Critical Recorder Error:", err);
        alert("Camera Error: " + err.message);
    }
}

function runNextPrompt() {
    console.log("Prompt Cycle:", currentStep);
    
    if (currentStep < prompts.length) {
        const p = prompts[currentStep];
        
        // Update UI
        document.getElementById('currentPrompt').innerText = p.main;
        document.getElementById('subPrompt').innerText = p.sub;
        
        const progress = ((currentStep + 1) / prompts.length * 100);
        document.getElementById('stepProgress').style.width = progress + '%';
        
        console.log("Displaying prompt:", p.main);

        setTimeout(() => {
            currentStep++;
            runNextPrompt();
        }, 4000); 
    } else {
        console.log("Prompts finished. Moving to completion.");
        showToast("Recording finished. Finalizing...");
        finishVerification();
    }
}

function finishVerification() {
    console.log("Stopping recorder...");
    
    // Immediately close the overlay and stop tracks
    document.getElementById('cancelVerification').click();
    
    showToast("Processing verification... please wait.");

    if (mediaRecorder && mediaRecorder.state !== 'inactive') {
        mediaRecorder.onstop = async () => {
            console.log("onstop fired. Chunks count:", videoChunks.length);
            if (videoChunks.length === 0) {
                alert("Recording failed. Please try again.");
                return;
            }

            try {
                const videoBlob = new Blob(videoChunks, { type: mediaRecorder.mimeType });
                const formData = new FormData();
                formData.append('video', videoBlob, 'recording.webm');

                console.log("Sending fetch request...");
                const resp = await fetch('index.php?action=handle_clock_in', {
                    method: 'POST',
                    body: formData
                });
                
                const rawText = await resp.text();
                console.log("Raw Server Response:", rawText);

                try {
                    const res = JSON.parse(rawText);
                    if (res.success) {
                        showToast("Verification sent! Pending review.");
                        setTimeout(() => {
                            location.reload();
                        }, 3000);
                    } else {
                        alert("Upload failed: " + res.message);
                    }
                } catch (parseErr) {
                    console.error("JSON Parse Error:", parseErr);
                    alert("Server error during verification upload.");
                }
            } catch (err) {
                console.error("Processing error:", err);
            }
        };
        mediaRecorder.stop();
    }
}
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
