<!-- Create Student Modal -->
<div class="modal fade" id="create-student-modal" tabindex="-1" aria-labelledby="create-student-modal-label"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="create-student-modal-label">Create Student Record</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="close"></button>
            </div>
            <div class="modal-body">

                {{-- 🔹 Show error message for duplicate student_id or RFID --}}
                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('process-create-student-record') }}" method="POST" enctype="multipart/form-data">

                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="last-name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="last-name" name="last_name" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="first-name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="first-name" name="first_name" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="middle-name" class="form-label">Middle Name</label>
                            <input type="text" class="form-control" id="middle-name" name="middle_name">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="student-id" class="form-label">Student ID</label>
                            <input type="text" class="form-control @error('student_id') is-invalid @enderror"
                                id="student-id" name="student_id" pattern="\d+" title="Only numbers are allowed"
                                required value="{{ old('student_id') }}">
                            @error('student_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="student-department" class="form-label">Department</label>
                                <select name="department_id" id="student-department" class="form-control">
                                    <option value="" disabled selected>Select Department</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->department_id }}">
                                            {{ $department->department_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="student-rfid" class="form-label">RFID</label>
                                <input type="text" class="form-control @error('rfid') is-invalid @enderror"
                                    id="student-rfid" name="rfid" pattern="\d+" title="Only numbers are allowed"
                                    value="{{ old('rfid') }}">
                                @error('rfid')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="student-program" class="form-label">Program</label>
                                <select name="program_id" id="student-program" class="form-control">
                                    <option value="" disabled selected>Select Program</option>
                                    <!-- options loaded dynamically via AJAX -->
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="student-year-level" class="form-label">Year Level</label>
                                <select name="year_level_id" id="student-year-level" class="form-control">
                                    <option value="" disabled selected>Select Year Level</option>
                                    <!-- options loaded dynamically via AJAX -->
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="student-section" class="form-label">Section <span
                                        class="text-muted">(Optional)</span></label>
                                <select name="section_id" id="student-section" class="form-control">
                                    <option value="" disabled selected>Select Section</option>
                                    <!-- options loaded dynamically via AJAX -->
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="school-year" class="form-label">School Year</label>
                                <select name="school_year_id" id="school-year" class="form-control">
                                    <option value="" disabled selected>Select School Year</option>
                                    @foreach($schoolyears as $schoolyear)
                                        <option value="{{ $schoolyear->school_year_id }}">
                                            {{ $schoolyear->school_year_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Face Image</label>

                            <!-- Camera container with face mesh overlay -->
                            <div style="position: relative; border-radius: 8px; overflow: hidden; background: #000;">
                                <video id="camera" width="100%" autoplay muted playsinline
                                    style="display: block;"></video>
                                <canvas id="faceMeshCanvas"
                                    style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 10;"></canvas>
                            </div>

                            <div class="d-flex align-items-center mt-2">
                                <button type="button" class="btn btn-primary" id="capture-btn">
                                    📷 Capture Photo
                                </button>
                                <span class="ms-3 text-muted" id="face-status">Loading face detection...</span>
                            </div>

                            <canvas id="snapshot" style="display:none;"></canvas>
                            <input type="hidden" name="face_image" id="face_image_base64">

                            <!-- Captured preview -->
                            <div id="captured-preview-container" style="margin-top:10px; border:2px dashed #ccc; border-radius:8px; 
                    height:150px; display:flex; align-items:center; justify-content:center; 
                    color:#888; font-style:italic; overflow:hidden; background: #f8f9fa;">
                                No photo captured yet
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary float-end ms-5"
                        onclick="if(!faceImageInput.value){alert('Please capture photo'); return false;}">
                        Create
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function () {

        // Prevent Enter in RFID input from submitting form
        $('#student-rfid').on('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                return false;
            }
        });

        // Store all sections from Laravel
        let allSections = @json($sections); // Make sure $sections contains program_id and year_level_id
        console.log('All sections loaded:', allSections);

        // Function to sort sections numerically by section_name
        function sortSectionsNumerically(sectionsArray) {
            return sectionsArray.sort((a, b) => {
                const aNum = parseInt(a.section_name.replace(/\D/g, '')) || 0;
                const bNum = parseInt(b.section_name.replace(/\D/g, '')) || 0;
                return aNum - bNum;
            });
        }

        // Render sections based on selected program and year level
        function renderSections() {
            const programId = $('#student-program').val();
            const yearLevelId = $('#student-year-level').val();

            console.log('renderSections called - Program ID:', programId, 'Year Level ID:', yearLevelId);

            // If no program is selected, clear sections
            if (!programId) {
                $('#student-section').html('<option value="" disabled selected>Select Section</option>');
                return;
            }

            // Filter by program first (required)
            let filteredSections = allSections.filter(s => s.program_id === programId);
            console.log('Filtered by program:', filteredSections);

            // If year level is also selected, filter by it too (optional additional filter)
            if (yearLevelId) {
                filteredSections = filteredSections.filter(s => s.year_level_id === yearLevelId);
                console.log('Filtered by year level:', filteredSections);
            }

            filteredSections = sortSectionsNumerically(filteredSections);

            let options = '<option value="" selected>None (Irregular Student)</option>';
            if (filteredSections.length > 0) {
                filteredSections.forEach(s => {
                    options += `<option value="${s.section_id}">${s.section_name}</option>`;
                });
            } else {
                options += '<option disabled>No sections available</option>';
            }

            $('#student-section').html(options);
        }


        // Department change -> Load programs and year levels
        $('#student-department').on('change', function () {
            let departmentId = $(this).val();
            if (!departmentId) return;

            // Programs
            $('#student-program').html('<option disabled selected>Loading programs...</option>');
            $.get("{{ route('get-programs', '') }}/" + departmentId, function (data) {
                let options = '<option disabled selected>Select Program</option>';
                if (data && data.length) data.forEach(p => { options += `<option value="${p.program_id}">${p.program_name}</option>`; });
                else options += '<option disabled>No programs found</option>';
                $('#student-program').html(options);
            }).fail(function () { $('#student-program').html('<option disabled selected>Error loading programs</option>'); });

            // Year Levels
            $('#student-year-level').html('<option disabled selected>Loading year levels...</option>');
            $.get("{{ route('get-year-levels', '') }}/" + departmentId, function (data) {
                let options = '<option disabled selected>Select Year Level</option>';
                if (data && data.length) {
                    data.sort((a, b) => parseInt(a.year_level_name) - parseInt(b.year_level_name));
                    data.forEach(y => { options += `<option value="${y.year_level_id}">${y.year_level_name}</option>`; });
                } else options += '<option disabled>No year levels found</option>';
                $('#student-year-level').html(options);
            }).fail(function () { $('#student-year-level').html('<option disabled selected>Error loading year levels</option>'); });

            // Reset sections
            $('#student-section').html('<option disabled selected>Select Section</option>');
        });

        // Program or Year Level change -> render sections
        $('#student-program, #student-year-level').on('change', renderSections);

        // Camera and Face Detection setup
        let video = document.getElementById('camera');
        let canvas = document.getElementById('snapshot');
        let faceMeshCanvas = document.getElementById('faceMeshCanvas');
        let captureBtn = document.getElementById('capture-btn');
        let faceImageInput = document.getElementById('face_image_base64');
        let faceStatus = document.getElementById('face-status');
        let stream = null;
        let detectionInterval = null;

        // Initialize camera when modal opens
        $('#create-student-modal').on('shown.bs.modal', async function () {
            try {
                // Check if faceapi is available
                if (typeof faceapi === 'undefined') {
                    console.error('face-api.js not loaded!');
                    faceStatus.textContent = '❌ Face detection library not loaded';
                    faceStatus.style.color = '#dc3545';
                    // Still start camera even without face detection
                    stream = await navigator.mediaDevices.getUserMedia({ video: { width: 640, height: 480 } });
                    video.srcObject = stream;
                    await video.play();
                    return;
                }

                faceStatus.textContent = '📷 Starting camera...';
                stream = await navigator.mediaDevices.getUserMedia({
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
                faceMeshCanvas.width = video.videoWidth;
                faceMeshCanvas.height = video.videoHeight;

                // Load face detection models
                faceStatus.textContent = '⏳ Loading face detection models...';
                const MODEL_URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model';

                console.log('Loading TinyFaceDetector model...');
                await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
                console.log('Loading FaceLandmark68Net model...');
                await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
                console.log('Models loaded successfully');

                faceStatus.textContent = '✅ Face detection ready';
                faceStatus.style.color = '#28a745';

                // Start detection loop with setInterval (slightly slower to reduce CPU load)
                detectionInterval = setInterval(detectFaces, 150);

            } catch (err) {
                console.error('Camera/Face detection error:', err);
                faceStatus.textContent = '❌ Error: ' + err.message;
                faceStatus.style.color = '#dc3545';
            }
        });

        // Store last detection to prevent blinking
        let lastDetections = null;
        let noFaceCount = 0;

        // Face detection function (same as machine page)
        async function detectFaces() {
            if (!video || video.paused || video.ended) return;

            // Match canvas to displayed video size
            const displaySize = { width: video.offsetWidth, height: video.offsetHeight };

            if (faceMeshCanvas.width !== displaySize.width || faceMeshCanvas.height !== displaySize.height) {
                faceMeshCanvas.width = displaySize.width;
                faceMeshCanvas.height = displaySize.height;
            }

            const ctx = faceMeshCanvas.getContext('2d');

            try {
                // Detect faces with landmarks
                const detections = await faceapi
                    .detectAllFaces(video, new faceapi.TinyFaceDetectorOptions())
                    .withFaceLandmarks();

                // Resize results to match display size
                const resizedDetections = faceapi.resizeResults(detections, displaySize);

                if (resizedDetections.length > 0) {
                    // Face found - draw it
                    ctx.clearRect(0, 0, faceMeshCanvas.width, faceMeshCanvas.height);
                    resizedDetections.forEach(det => {
                        drawWhiteMesh(ctx, det.landmarks.positions);
                    });
                    lastDetections = resizedDetections;
                    noFaceCount = 0;
                    faceStatus.textContent = '✅ Face detected - Ready to capture!';
                    faceStatus.style.color = '#28a745';
                } else {
                    noFaceCount++;
                    // Only clear after 5 consecutive frames with no face (prevents flickering)
                    if (noFaceCount > 5) {
                        ctx.clearRect(0, 0, faceMeshCanvas.width, faceMeshCanvas.height);
                        lastDetections = null;
                        faceStatus.textContent = '👤 Position your face in the camera';
                        faceStatus.style.color = '#6c757d';
                    } else if (lastDetections) {
                        // Keep showing last detection to prevent blink
                        ctx.clearRect(0, 0, faceMeshCanvas.width, faceMeshCanvas.height);
                        lastDetections.forEach(det => {
                            drawWhiteMesh(ctx, det.landmarks.positions);
                        });
                    }
                }
            } catch (err) {
                console.error('Detection error:', err);
            }
        }

        // Draw white triangulated mesh (same as machine page)
        function drawWhiteMesh(ctx, points) {
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

        // Capture photo
        if (captureBtn) {
            captureBtn.addEventListener('click', function () {
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
                let dataUrl = canvas.toDataURL('image/png');
                faceImageInput.value = dataUrl;

                document.getElementById('captured-preview-container').innerHTML =
                    `<img src="${dataUrl}" alt="Captured Image" style="display:block; max-width:100%; max-height:150px; border-radius:5px;" />`;

                faceStatus.textContent = '📸 Photo captured!';
                faceStatus.style.color = '#28a745';
            });
        }

        // Stop camera when modal closes
        $('#create-student-modal').on('hidden.bs.modal', function () {
            // Stop detection interval
            if (detectionInterval) {
                clearInterval(detectionInterval);
                detectionInterval = null;
            }
            // Stop camera stream
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }
            // Clear canvas
            if (faceMeshCanvas) {
                const ctx = faceMeshCanvas.getContext('2d');
                ctx.clearRect(0, 0, faceMeshCanvas.width, faceMeshCanvas.height);
            }
            // Reset UI
            if (faceImageInput) faceImageInput.value = '';
            document.getElementById('captured-preview-container').innerHTML = 'No photo captured yet';
            if (faceStatus) {
                faceStatus.textContent = 'Loading face detection...';
                faceStatus.style.color = '';
            }
        });

    });
</script>