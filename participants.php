<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MUBAS | Live Session Tracking</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
    <script src="js/supabase-auth.js"></script>
    <link rel="stylesheet" href="style/participants.css">
    
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <h1><i class="image"><img src="mubas-logo-full.png"></i> MUBAS-<span>ATTEND</span></h1>
        </div>
        <ul class="nav-links">
            <li><a href="index.php"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
            <li><a href="session.php"><i class="fas fa-calendar-alt"></i> <span>Sessions</span></a></li>
            <li><a href="participants.php" class="active"><i class="fas fa-users"></i> <span>Participants</span></a></li>
            <li><a href="register.php"><i class="fas fa-user"></i> <span>Registration</span></a></li>
            <li><a href="reporty.php"><i class="fas fa-chart-bar"></i> <span>Reports</span></a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <h2><i class="fas fa-users"></i> Live Session Tracking</h2>
            <div class="user-menu">
                <div class="notification">
                    <i class="fas fa-bell"></i>
                    <span class="badge">3</span>
                </div>
                <div class="image">
                    <img src="img/image.png" alt="User">
                    <div>
                        <h4 id="userName">Loading...</h4>
                        <p id="userDepartment">Loading...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Session Control -->
        <div class="session-control">
            <h3 class="control-title"><i class="fas fa-play-circle"></i> Session Control</h3>
            
            <div class="control-grid">
                <div class="form-group">
                    <label for="sessionSelect">Select Session</label>
                    <select id="sessionSelect">
                        <option value="">-- Select Session --</option>
                        <!-- Sessions will be populated from Supabase -->
                    </select>
                </div>
            </div>
            
            <div class="action-buttons">
                <button class="btn btn-success" id="startSessionBtn">
                    <i class="fas fa-play"></i> Start Session
                </button>
                <button class="btn btn-danger" id="stopSessionBtn" disabled>
                    <i class="fas fa-stop"></i> End Session
                </button>
                <button class="btn btn-outline" id="refreshBtn">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
                <button class="btn btn-primary" id="scanFingerprintBtn" style="display: none;">
                    <i class="fas fa-fingerprint"></i> Scan Fingerprint
                </button>
                <button class="btn btn-primary" id="saveAttendanceBtn" disabled style="display: none;">
                    <i class="fas fa-save"></i> Save Attendance
                </button>
            </div>
            
            <div class="session-status">
                <div class="status-indicator status-inactive" id="statusIndicator"></div>
                <div class="status-text" id="statusText">No active session. Please start a session to track attendance.</div>
                <div class="session-info" id="sessionInfo"></div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-cards">
            <div class="stat-card stat-1">
                <div class="stat-icon">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="stat-value" id="totalPresent">0</div>
                <div class="stat-label">Present Students</div>
            </div>
            
            <div class="stat-card stat-2">
                <div class="stat-icon">
                    <i class="fas fa-door-open"></i>
                </div>
                <div class="stat-value" id="totalExited">0</div>
                <div class="stat-label">Exited Students</div>
            </div>
            
            <div class="stat-card stat-3">
                <div class="stat-icon">
                    <i class="fas fa-user-times"></i>
                </div>
                <div class="stat-value" id="totalAbsent">0</div>
                <div class="stat-label">Absent Students</div>
            </div>
            
            <div class="stat-card stat-4">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-value" id="attendanceRate">0%</div>
                <div class="stat-label">Attendance Rate</div>
            </div>
        </div>

        <!-- Participants Table -->
        <h3 class="section-title"><i class="fas fa-list-ol"></i> Live Attendance</h3>
        
        <div class="participants-table">
            <table>
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Program</th>
                        <th>Entry Time</th>
                        <th>Exit Time</th>
                        <th>Authentication</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="participantsTableBody">
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 30px; color: var(--gray);">
                            <i class="fas fa-users" style="font-size: 40px; margin-bottom: 15px; display: block;"></i>
                            No active session. Start a session to view attendance data.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // DOM Elements
        const sessionSelect = document.getElementById('sessionSelect');
        const startSessionBtn = document.getElementById('startSessionBtn');
        const stopSessionBtn = document.getElementById('stopSessionBtn');
        const refreshBtn = document.getElementById('refreshBtn');
        const scanFingerprintBtn = document.getElementById('scanFingerprintBtn');
        const saveAttendanceBtn = document.getElementById('saveAttendanceBtn');
        const statusIndicator = document.getElementById('statusIndicator');
        const statusText = document.getElementById('statusText');
        const sessionInfo = document.getElementById('sessionInfo');
        const participantsTableBody = document.getElementById('participantsTableBody');
        
        // Stats elements
        const totalPresentEl = document.getElementById('totalPresent');
        const totalExitedEl = document.getElementById('totalExited');
        const totalAbsentEl = document.getElementById('totalAbsent');
        const attendanceRateEl = document.getElementById('attendanceRate');
        
        // State variables
        let currentSession = null;
        let sessionInterval = null;
        let sessionDuration = 0;
        let allSessions = [];
        let allStudents = [];
        let attendanceData = []; // Real attendance data
        let scannerIp = '192.168.1.198'; // Default scanner IP
        let scannerInterval = null; // Scanner monitoring interval
        let isScanning = false; // Prevent multiple simultaneous scans
        let isExitPhase = false; // If true, we're in fingerprint-only exit verification
        // Fast lookup maps for robust matching
        let studentIdIndex = new Map();
        let barcodeIndex = new Map();
        let fingerprintIndex = new Map();

        // Initialize the application
        document.addEventListener('DOMContentLoaded', async function() {
            // Initialize authentication
            const isAuthenticated = await SupabaseAuth.initAuth();
            if (!isAuthenticated) return;
            
            // Load sessions and students from Supabase
            await Promise.all([loadSessions(), loadStudents()]);
            
            // Event listeners
            setupEventListeners();
            
            // Show scanner configuration
            showScannerConfig();
        });

        // Scanner PC controls: enable/disable barcode or fingerprint modes
        async function enableBarcode() {
            try { 
                const response = await fetch(`http://${scannerIp}:5002/barcode/enable`, { 
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'}
                });
                if (response.ok) {
                    console.log('Barcode scanning enabled');
                } else {
                    console.log('Failed to enable barcode:', response.status);
                }
            } catch (e) { 
                console.log('Failed to enable barcode:', e.message); 
            }
        }
        
        async function disableBarcode() {
            try { 
                const response = await fetch(`http://${scannerIp}:5002/barcode/disable`, { 
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'}
                });
                if (response.ok) {
                    console.log('Barcode scanning disabled');
                } else {
                    console.log('Failed to disable barcode:', response.status);
                }
            } catch (e) { 
                console.log('Failed to disable barcode:', e.message); 
            }
        }
        
        async function enableFingerprint() {
            try { 
                const response = await fetch(`http://${scannerIp}:5002/finger/enable`, { 
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'}
                });
                if (response.ok) {
                    console.log('Fingerprint scanning enabled');
                } else {
                    console.log('Failed to enable fingerprint:', response.status);
                }
            } catch (e) { 
                console.log('Failed to enable fingerprint:', e.message); 
            }
        }
        
        async function disableFingerprint() {
            try { 
                const response = await fetch(`http://${scannerIp}:5002/finger/disable`, { 
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'}
                });
                if (response.ok) {
                    console.log('Fingerprint scanning disabled');
                } else {
                    console.log('Failed to disable fingerprint:', response.status);
                }
            } catch (e) { 
                console.log('Failed to disable fingerprint:', e.message); 
            }
        }
        
        // Setup event listeners
        function setupEventListeners() {
            // Start session button
            startSessionBtn.addEventListener('click', startSession);
            
            // Stop session button
            stopSessionBtn.addEventListener('click', stopSession);
            
            // Refresh button
            refreshBtn.addEventListener('click', refreshAttendance);
            
            // Manual fingerprint scan (exit phase)
            scanFingerprintBtn.addEventListener('click', scanFingerprintOnce);

            // Save attendance button
            saveAttendanceBtn.addEventListener('click', finalizeAndSaveAttendance);
            
            // Keyboard listener for manual student ID entry
            document.addEventListener('keypress', handleStudentIdEntry);
        }
        
        // Show scanner configuration dialog
        function showScannerConfig() {
            Swal.fire({
                title: 'Scanner Configuration',
                html: `
                    <div style="text-align: left;">
                        <p>Configure the scanner connection (barcode and fingerprint):</p>
                        <input type="text" id="scannerIpInput" class="swal2-input" placeholder="Scanner IP Address" value="${scannerIp}">
                        <p style="font-size: 12px; color: #666; margin-top: 10px;">
                            Enter the IP of the PC running the Flask APIs on port 5002. Default: 192.168.1.198
                        </p>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Save Configuration',
                cancelButtonText: 'Use Default',
                preConfirm: () => {
                    const ip = document.getElementById('scannerIpInput').value.trim();
                    if (ip) {
                        scannerIp = ip;
                        return ip;
                    }
                    return scannerIp;
                }
            }).then((result) => {
                if (result.isConfirmed || result.dismiss === Swal.DismissReason.cancel) {
                    console.log(`Scanner IP configured: ${scannerIp}`);
                }
            });
        }
        
        // Load sessions from Supabase
        async function loadSessions() {
            try {
                const userData = SupabaseAuth.getCurrentUser();
                if (!userData) return;
                
                // Fetch only scheduled sessions from Supabase
                const { data: sessions, error } = await SupabaseAuth.supabase
                    .from('sessions')
                    .select('*')
                    .eq('lecturer_id', userData.id)
                    .eq('status', 'scheduled')
                    .order('start_time', { ascending: false });
                
                if (error) throw error;
                
                allSessions = sessions || [];
                
                // Populate session dropdown
                sessionSelect.innerHTML = '<option value="">-- Select Session --</option>';
                
                allSessions.forEach(session => {
                    const startDate = session.start_time ? new Date(session.start_time) : null;
                    const labelDate = startDate ? startDate.toLocaleString([], { 
                        month: 'short', 
                        day: '2-digit', 
                        hour: '2-digit', 
                        minute: '2-digit' 
                    }) : '';
                    
                    const option = document.createElement('option');
                    option.value = session.session_id;
                    option.textContent = `${session.course} - ${session.session_title}${labelDate ? ' - ' + labelDate : ''}`;
                    option.dataset.session = JSON.stringify(session);
                    sessionSelect.appendChild(option);
                });
                
            } catch (e) {
                console.error('Failed to load sessions:', e);
                sessionSelect.innerHTML = '<option value="">-- Select Session --</option>';
                Swal.fire({
                    icon: 'error',
                    title: 'Failed to load sessions',
                    text: e.message || 'Database error',
                    timer: 4000
                });
            }
        }
        
        // Load all students from Supabase
        async function loadStudents() {
            try {
                const userData = SupabaseAuth.getCurrentUser();
                if (!userData) return;
                
                // Fetch all students from Supabase
                const { data: students, error } = await SupabaseAuth.supabase
                    .from('students')
                    .select('*')
                    .order('student_id', { ascending: true });
                
                if (error) throw error;
                
                allStudents = students || [];
                console.log(`Loaded ${allStudents.length} students from database`);
                buildLookupIndexes(allStudents);
                
            } catch (e) {
                console.error('Failed to load students:', e);
                Swal.fire({
                    icon: 'error',
                    title: 'Failed to load students',
                    text: e.message || 'Database error',
                    timer: 4000
                });
            }
        }
        
        // Normalize identifier tokens from scanner/DB for consistent matching
        function normalizeIdToken(value) {
            if (value === null || value === undefined) return '';
            let token = String(value).trim();
            if ((token.startsWith('"') && token.endsWith('"')) || (token.startsWith("'") && token.endsWith("'"))) {
                token = token.slice(1, -1);
            }
            token = token.replace(/\s+/g, '');
            token = token.replace(/^0+/, '');
            return token;
        }

        // Build fast lookup maps: studentId -> record, barcode -> studentId, fingerprint -> studentId
        function buildLookupIndexes(students) {
            studentIdIndex = new Map();
            barcodeIndex = new Map();
            fingerprintIndex = new Map();
            students.forEach(student => {
                const sid = normalizeIdToken(student.student_id);
                if (sid) studentIdIndex.set(sid, student);
                const b = normalizeIdToken(student.barcode_data);
                if (b) barcodeIndex.set(b, sid || student.student_id);
                const fpRaw = student.fingerprint_data;
                if (fpRaw !== null && fpRaw !== undefined) {
                    let tokens = [];
                    if (typeof fpRaw === 'string') {
                        tokens = fpRaw.split(/[;,\s]+/);
                    } else {
                        tokens = [String(fpRaw)];
                    }
                    tokens.map(normalizeIdToken).filter(Boolean).forEach(tok => {
                        if (!fingerprintIndex.has(tok)) {
                            fingerprintIndex.set(tok, sid || student.student_id);
                        }
                    });
                }
            });
            console.log(`Indexes built: ids=${studentIdIndex.size}, barcodes=${barcodeIndex.size}, fingerprints=${fingerprintIndex.size}`);
        }

        // Extract a fingerprint identifier from scanner response supporting multiple shapes
        function extractFingerprintId(data) {
            if (!data) return '';
            const candidates = [
                data.finger_id,
                data.fingerId,
                data.id,
                data.template_id,
                data.templateId,
                data.uid,
                data.user_id,
                data.userId
            ];
            for (const c of candidates) {
                const n = normalizeIdToken(c);
                if (n) return n;
            }
            if (data.match) {
                const n = normalizeIdToken(data.match.id || data.match.finger_id);
                if (n) return n;
            }
            return '';
        }
        
        // Start a new session
        async function startSession() {
            const sessionId = sessionSelect.value;
            
            if (!sessionId) {
                Swal.fire({
                    icon: 'error',
                    title: 'Missing Information',
                    text: 'Please select a session to start.',
                    timer: 3000
                });
                return;
            }
            
            // Get the selected session data
            const selectedOption = sessionSelect.options[sessionSelect.selectedIndex];
            const session = JSON.parse(selectedOption.dataset.session);
            
            // Check if session can be started
            if (session.status !== 'scheduled') {
                Swal.fire({
                    icon: 'error',
                    title: 'Cannot Start Session',
                    text: 'Only scheduled sessions can be started.',
                    timer: 3000
                });
                return;
            }
            
            Swal.fire({
                title: 'Start Session?',
                html: `
                    <p>Are you sure you want to start:</p>
                    <p><b>${session.course} - ${session.session_title}</b></p>
                    <hr>
                    <p><strong>Scanner IP:</strong> ${scannerIp}:5002</p>
                    <p><strong>Students:</strong> ${allStudents.length}</p>
                    <p><strong>Mode:</strong> Barcode scanning for entry</p>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Start Session',
                cancelButtonText: 'Cancel'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        // Calculate session duration in minutes from scheduled start/end
                        let duration = 60; // default fallback
                        try {
                            const startTime = session.start_time ? new Date(session.start_time) : null;
                            const endTime = session.end_time ? new Date(session.end_time) : null;
                            if (startTime && endTime && !isNaN(startTime) && !isNaN(endTime) && endTime > startTime) {
                                duration = Math.max(1, Math.floor((endTime - startTime) / 60000));
                            }
                        } catch (e) {
                            console.warn('Failed to compute duration from session times, using default 60m');
                        }

                        currentSession = {
                            session_id: sessionId,
                            course: session.course,
                            title: session.session_title,
                            startTime: new Date(),
                            duration: duration
                        };
                        
                        // We are in active session (entry only with barcode)
                        isExitPhase = false;
                        
                        // Initialize attendance data for all students
                        initializeAttendanceData();
                        
                        startSessionUI(currentSession);
                        
                        // Start scanner monitoring and activate barcode ONLY
                        await disableFingerprint(); // Ensure fingerprint is off
                        await enableBarcode(); // Enable barcode scanning
                        startScannerMonitoring();
                        
                        // Ensure Save button is hidden/disabled during active phase
                        saveAttendanceBtn.style.display = 'none';
                        saveAttendanceBtn.disabled = true;
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Session Started!',
                            html: `
                                <p>Attendance tracking is now active.</p>
                                <p><strong>Scanner:</strong> ${scannerIp}:5002</p>
                                <p><strong>Students:</strong> ${allStudents.length}</p>
                                <p><strong>Mode:</strong> Barcode scanning enabled for entry</p>
                                <p>Students can now scan their IDs to mark attendance.</p>
                            `,
                            timer: 4000,
                            showConfirmButton: false
                        });
                        
                    } catch (e) {
                        console.error('Session start error:', e);
                        
                        let errorMessage = 'An error occurred while starting the session.';
                        
                        if (e.message && e.message.includes('Please log in again')) {
                            errorMessage = 'Please log in again to start sessions.';
                            window.location.href = 'login.php';
                            return;
                        } else if (e.code === '23505') {
                            errorMessage = 'Session is already active.';
                        } else if (e.code === '42P01') {
                            errorMessage = 'Sessions table not found. Please contact administrator.';
                        } else if (e.code === '42501') {
                            errorMessage = 'Permission denied. Please contact administrator.';
                        } else if (e.message) {
                            errorMessage = `Database error: ${e.message}`;
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed to start session',
                            text: errorMessage,
                            timer: 5000
                        });
                    }
                }
            });
        }
        
        // Initialize attendance data for all students
        function initializeAttendanceData() {
            attendanceData = allStudents.map(student => ({
                student_id: student.student_id,
                full_name: student.full_name,
                email: student.email,
                phone: student.phone,
                program: student.program,
                year_of_study: student.year_of_study,
                fingerprint_data: student.fingerprint_data,
                barcode_data: student.barcode_data,
                entryTime: null,
                exitTime: null,
                status: 'absent',
                entryMethod: null,
                exitMethod: null
            }));
            
            updateAttendanceTable(attendanceData);
            updateStats(0, 0, allStudents.length);
        }
        
        // Stop the current session
        async function stopSession() {
            Swal.fire({
                title: 'End Session?',
                html: '<p>End the session and start <b>exit verification</b>? Only fingerprint scans will be accepted to confirm exits. You will save attendance after verification.</p>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Start Exit Verification',
                cancelButtonText: 'Cancel'
            }).then(async (result) => {
                if (!result.isConfirmed) return;
                
                // Switch to exit verification phase
                isExitPhase = true;
                
                // UI updates for exit phase
                statusIndicator.className = 'status-indicator status-scheduled';
                statusText.textContent = 'Exit Verification: Use fingerprint scanner to confirm exits';
                sessionInfo.textContent = `Mode: Fingerprint-only | Scanner: ${scannerIp}:5002`;
                
                // Disable start/stop while verifying, show Save button
                startSessionBtn.disabled = true;
                stopSessionBtn.disabled = true;
                saveAttendanceBtn.style.display = 'inline-block';
                saveAttendanceBtn.disabled = false;
                // Hide standalone fingerprint button; we'll use a modal-based flow
                scanFingerprintBtn.style.display = 'none';
                scanFingerprintBtn.disabled = true;
                
                // Keep session selection disabled
                sessionSelect.disabled = true;
                
                // Switch scanner modes: disable barcode, enable fingerprint
                await disableBarcode();
                await enableFingerprint();
                
                // Start auto monitoring for fingerprint scans during exit verification
                startScannerMonitoring();
                
                // Info message about automatic scanning
                Swal.fire({
                    icon: 'info',
                    title: 'Exit Verification Started',
                    html: `
                        <p>Fingerprint scanning is now active.</p>
                        <p>Students can place their finger to be marked as exited automatically.</p>
                        <p>Click "Save Attendance" when done.</p>
                    `,
                    timer: 3500,
                    showConfirmButton: false
                });
            });
        }
        
        // Create attendance table and save attendance data
        async function createAttendanceTable() {
            try {
                const userData = SupabaseAuth.getCurrentUser();
                if (!userData || !currentSession) return;
                
                // Create attendance table SQL
                const createTableSQL = `
                    CREATE TABLE IF NOT EXISTS attendance_${currentSession.session_id} (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        student_id VARCHAR(50) NOT NULL,
                        full_name VARCHAR(255) NOT NULL,
                        email VARCHAR(255) NOT NULL,
                        phone VARCHAR(20) NOT NULL,
                        program VARCHAR(20) NOT NULL,
                        year_of_study INT NOT NULL,
                        session_id VARCHAR(50) NOT NULL,
                        course VARCHAR(20) NOT NULL,
                        entry_time DATETIME NULL,
                        exit_time DATETIME NULL,
                        status ENUM('present', 'exited', 'absent') DEFAULT 'absent',
                        entry_method ENUM('fingerprint', 'barcode') NULL,
                        exit_method ENUM('fingerprint', 'barcode') NULL,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                        INDEX idx_student_id (student_id),
                        INDEX idx_session_id (session_id),
                        INDEX idx_status (status)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                `;
                
                // Save attendance data to Supabase (using a generic attendance table)
                const attendanceRecords = attendanceData.map(record => ({
                    student_id: record.student_id,
                    full_name: record.full_name,
                    email: record.email,
                    phone: record.phone,
                    program: record.program,
                    year_of_study: record.year_of_study,
                    session_id: currentSession.session_id,
                    course: currentSession.course,
                    entry_time: record.entryTime,
                    exit_time: record.exitTime,
                    status: record.status,
                    entry_method: record.entryMethod,
                    exit_method: record.exitMethod
                }));
                
                // Insert attendance data into Supabase
                const { error } = await SupabaseAuth.supabase
                    .from('attendance')
                    .insert(attendanceRecords);
                
                if (error) {
                    console.error('Failed to save attendance data:', error);
                    throw error;
                }
                
                console.log(`Attendance data saved for session ${currentSession.session_id}`);
                
                // Show success message with SQL
                Swal.fire({
                    icon: 'success',
                    title: 'Attendance Table Created!',
                    html: `
                        <p>Attendance data has been saved successfully.</p>
                        <p><strong>Session:</strong> ${currentSession.title}</p>
                        <p><strong>Students Present:</strong> ${attendanceData.filter(s => s.status === 'present' || s.status === 'exited').length}</p>
                        <p><strong>Students Absent:</strong> ${attendanceData.filter(s => s.status === 'absent').length}</p>
                        <hr>
                        <p><strong>SQL Command for Attendance Table:</strong></p>
                        <textarea readonly style="width: 100%; height: 100px; font-family: monospace; font-size: 10px;">${createTableSQL}</textarea>
                    `,
                    confirmButtonText: 'OK'
                });
                
            } catch (error) {
                console.error('Failed to create attendance table:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Failed to Save Attendance',
                    text: 'An error occurred while saving attendance data.',
                    timer: 5000
                });
            }
        }
        
        // Refresh attendance data
        function refreshAttendance() {
            if (!currentSession) {
                Swal.fire({
                    icon: 'info',
                    title: 'No Active Session',
                    text: 'There is no active session to refresh.',
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }
            
            // Update the attendance table with current data
            updateAttendanceTable(attendanceData);
            
            Swal.fire({
                icon: 'success',
                title: 'Attendance Updated',
                text: 'Attendance data has been refreshed.',
                timer: 1500,
                showConfirmButton: false
            });
        }

        // Finalize and save attendance, then reset UI back to idle
        async function finalizeAndSaveAttendance() {
            if (!currentSession) {
                Swal.fire({
                    icon: 'info',
                    title: 'No Session In Progress',
                    text: 'Start a session to track and save attendance.',
                    timer: 2500,
                    showConfirmButton: false
                });
                return;
            }

            const presentCount = attendanceData.filter(s => s.status === 'present').length;
            const exitedCount = attendanceData.filter(s => s.status === 'exited').length;
            const totalCount = attendanceData.length;

            const confirmResult = await Swal.fire({
                title: 'Save Attendance?',
                html: `
                    <p>You are about to save attendance for <b>${currentSession.title}</b>.</p>
                    <p><strong>Students Present:</strong> ${presentCount}</p>
                    <p><strong>Students Exited (verified):</strong> ${exitedCount}</p>
                    <p><strong>Total Students:</strong> ${totalCount}</p>
                    <hr>
                    <p>This will finalize the session and disable scanners.</p>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Save',
                cancelButtonText: 'Cancel'
            });

            if (!confirmResult.isConfirmed) return;

            try {
                // Stop polling and timers
                stopScannerMonitoring();
                if (sessionInterval) {
                    clearInterval(sessionInterval);
                    sessionInterval = null;
                }

                // Disable both scanner modes
                await disableBarcode();
                await disableFingerprint();

                // Persist attendance records
                await createAttendanceTable();

                // Reset state and UI
                currentSession = null;
                isExitPhase = false;
                sessionDuration = 0;

                // Buttons and controls
                startSessionBtn.disabled = false;
                stopSessionBtn.disabled = true;
                saveAttendanceBtn.disabled = true;
                saveAttendanceBtn.style.display = 'none';
                scanFingerprintBtn.style.display = 'none';
                sessionSelect.disabled = false;

                // Status and info
                statusIndicator.className = 'status-indicator status-inactive';
                statusText.textContent = 'No active session. Please start a session to track attendance.';
                sessionInfo.textContent = '';

                // Clear table and stats back to default view
                participantsTableBody.innerHTML = `
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 30px; color: var(--gray);">
                            <i class="fas fa-users" style="font-size: 40px; margin-bottom: 15px; display: block;"></i>
                            No active session. Start a session to view attendance data.
                        </td>
                    </tr>
                `;
                updateStats(0, 0, allStudents.length);

                await Swal.fire({
                    icon: 'success',
                    title: 'Attendance Saved',
                    text: 'Session has been finalized and attendance saved successfully.',
                    timer: 3000,
                    showConfirmButton: false
                });

            } catch (e) {
                console.error('Finalize/save attendance error:', e);
                Swal.fire({
                    icon: 'error',
                    title: 'Failed to Save Attendance',
                    text: e.message || 'Please try again.',
                    timer: 5000
                });
            }
        }
        
        // Start session UI updates
        function startSessionUI(session) {
            // Update UI
            statusIndicator.className = 'status-indicator status-active';
            statusText.textContent = `Session Active: ${session.title} (Entry via Barcode only)`;
            
            // Enable stop button, disable start button
            stopSessionBtn.disabled = false;
            startSessionBtn.disabled = true;
            
            // Disable session select
            sessionSelect.disabled = true;
            
            // Start session timer
            sessionDuration = 0;
            updateSessionInfo();
            
            if (sessionInterval) clearInterval(sessionInterval);
            sessionInterval = setInterval(() => {
                sessionDuration++;
                updateSessionInfo();
            }, 1000);
        }
        
        // Update session info display
        function updateSessionInfo() {
            const minutes = Math.floor(sessionDuration / 60);
            const seconds = sessionDuration % 60;
            
            if (currentSession) {
                const remaining = currentSession.duration - minutes;
                const mode = isExitPhase ? 'Fingerprint-only (Exit)' : 'Barcode-only (Entry)';
                sessionInfo.textContent = `Elapsed: ${minutes}m ${seconds}s | Remaining: ${remaining}m | Mode: ${mode} | Scanner: ${scannerIp}:5002`;
                
                // Auto-end session when time is up
                if (remaining <= 0) {
                    // Auto-switch to exit verification when time elapses
                    if (!isExitPhase) stopSession();
                }
            }
        }
        
        // Start scanner monitoring (randomized cadence during exit phase)
        function startScannerMonitoring() {
            console.log(`Starting scanner monitoring on ${scannerIp}:5002`);
            
            // Clear any existing timer
            if (scannerInterval) {
                clearTimeout(scannerInterval);
            }
            
            const poll = async () => {
                try {
                    if (currentSession && !isScanning) {
                        if (isExitPhase) {
                            await checkForFingerprintInput();
                        } else {
                            await checkForBarcodeInput();
                        }
                    }
                } finally {
                    // Random delay during exit phase; fixed during entry
                    const delay = isExitPhase ? (1000 + Math.floor(Math.random() * 1500)) : 2000;
                    scannerInterval = setTimeout(poll, delay);
                }
            };
            poll();
        }
        
        // Stop scanner monitoring
        function stopScannerMonitoring() {
            if (scannerInterval) {
                clearTimeout(scannerInterval);
                scannerInterval = null;
            }
            console.log('Scanner monitoring stopped');
        }
        
        // Manual, single fingerprint scan during exit verification
        async function scanFingerprintOnce() {
            if (!currentSession || !isExitPhase) {
                Swal.fire({
                    icon: 'info',
                    title: 'Not In Exit Verification',
                    text: 'End the session first to start fingerprint exit verification.',
                    timer: 2500,
                    showConfirmButton: false
                });
                return;
            }
            if (isScanning) return;
            try {
                isScanning = true;
                scanFingerprintBtn.disabled = true;
                const response = await fetch(`http://${scannerIp}:5002/finger/scan`, {
                    method: 'GET',
                    headers: { 'Content-Type': 'application/json' }
                });
                if (!response.ok) {
                    const txt = await response.text().catch(() => '');
                    throw new Error(`HTTP ${response.status}: ${txt || 'Scan failed'}`);
                }
                const data = await response.json();
                if (data && data.matched) {
                    const fingerId = extractFingerprintId(data);
                    if (fingerId) {
                        await processStudentScan(fingerId, 'fingerprint');
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Scanner Data Missing ID',
                            text: 'Matched but no identifier returned. Please try again.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Match',
                        text: 'No matching fingerprint found. Try again.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            } catch (e) {
                console.log('Manual fingerprint scan error:', e.message || e);
                Swal.fire({
                    icon: 'error',
                    title: 'Scanner Error',
                    text: e.message || 'Could not scan fingerprint. Ensure the API is reachable.',
                    timer: 4000
                });
            } finally {
                isScanning = false;
                scanFingerprintBtn.disabled = false;
            }
        }

        // Check for barcode input from Flask API (entry phase)
        async function checkForBarcodeInput() {
            try {
                isScanning = true;
                
                // Make request to Flask API
                const response = await fetch(`http://${scannerIp}:5002/scan`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    timeout: 1000 // 1 second timeout
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                
                if (data.barcode && data.barcode.trim() !== '') {
                    console.log(`Barcode scanner detected: ${data.barcode}`);
                    await processStudentScan(data.barcode, 'barcode');
                }
                
            } catch (error) {
                // Don't show error for timeout or connection issues during monitoring
                if (error.name !== 'AbortError' && !error.message.includes('timeout')) {
                    console.log(`Barcode scanner connection issue: ${error.message}`);
                }
            } finally {
                isScanning = false;
            }
        }

        // Check for fingerprint input from Flask API (exit phase)
        async function checkForFingerprintInput() {
            try {
                isScanning = true;
                
                const response = await fetch(`http://${scannerIp}:5002/finger/scan`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                
                if (data && data.matched) {
                    const fingerId = extractFingerprintId(data);
                    if (fingerId) {
                        console.log(`Fingerprint matched: ID ${fingerId} (confidence ${data.confidence})`);
                        await processStudentScan(fingerId, 'fingerprint');
                    } else {
                        console.log('Fingerprint matched but no ID field found in response.');
                    }
                }
            } catch (error) {
                if (error.name !== 'AbortError' && !error.message.includes('timeout')) {
                    console.log(`Fingerprint scanner issue: ${error.message}`);
                }
            } finally {
                isScanning = false;
            }
        }
        
        // Handle student ID entry (manual or scanner)
        function handleStudentIdEntry(event) {
            if (!currentSession) return;
            
            // Check if it's a student ID pattern (alphanumeric)
            if (event.key.match(/[a-zA-Z0-9]/)) {
                // This would be part of a student ID being scanned
                // In a real implementation, you'd buffer the input until complete
            }
        }
        
        // Process student ID scan
        async function processStudentScan(studentId, scanMethod = 'barcode') {
            if (!currentSession) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Active Session',
                    text: 'Please start a session before scanning student IDs.',
                    timer: 3000
                });
                return;
            }
            
            // Enforce scanning mode rules
            if (isExitPhase) {
                if (scanMethod !== 'fingerprint') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Fingerprint Required',
                        text: 'During exit verification, only fingerprint scans are accepted.',
                        timer: 2500,
                        showConfirmButton: false
                    });
                    return;
                }
            } else {
                // Active session: only barcode/manual allowed for entry; no exits allowed
                if (scanMethod === 'fingerprint') {
                    // Should not happen because fingerprint is not polled, but guard anyway
                    Swal.fire({
                        icon: 'info',
                        title: 'Fingerprint Disabled',
                        text: 'Fingerprint scanning is disabled during the session. Use barcode to enter.',
                        timer: 2500,
                        showConfirmButton: false
                    });
                    return;
                }
            }
            
            // Resolve student using indexes for robustness
            const token = normalizeIdToken(studentId);
            let resolvedStudent = null;
            if (scanMethod === 'barcode') {
                const sid = barcodeIndex.get(token) || token;
                resolvedStudent = studentIdIndex.get(sid) || allStudents.find(s => normalizeIdToken(s.student_id) === sid);
            } else if (scanMethod === 'fingerprint') {
                const sid = fingerprintIndex.get(token) || token;
                resolvedStudent = studentIdIndex.get(sid) || allStudents.find(s => normalizeIdToken(s.student_id) === sid);
            }
            const student = resolvedStudent;
            
            if (!student) {
                Swal.fire({
                    icon: 'error',
                    title: 'Student Not Found',
                    text: `Identifier "${studentId}" did not match any student.`,
                    timer: 3000
                });
                return;
            }
            
            // Find student in attendance data
            const attendanceRecord = attendanceData.find(a => a.student_id === student.student_id);
            
            if (!attendanceRecord) {
                Swal.fire({
                    icon: 'error',
                    title: 'Attendance Record Not Found',
                    text: 'Student not found in attendance list.',
                    timer: 3000
                });
                return;
            }
            
            const currentTime = new Date();
            
            // Determine action based on current status and phase
            if (!isExitPhase) {
                // Active session: allow only entry
                if (attendanceRecord.status === 'absent') {
                    attendanceRecord.entryTime = currentTime;
                    attendanceRecord.status = 'present';
                    attendanceRecord.entryMethod = scanMethod;
                    Swal.fire({
                        icon: 'success',
                        title: 'Student Entered',
                        html: `
                            <p><strong>Student:</strong> ${student.full_name}</p>
                            <p><strong>ID:</strong> ${student.student_id}</p>
                            <p><strong>Time:</strong> ${currentTime.toLocaleTimeString()}</p>
                            <p><strong>Method:</strong> ${scanMethod}</p>
                        `,
                        timer: 2500,
                        showConfirmButton: false
                    });
                } else if (attendanceRecord.status === 'present') {
                    // Ignore duplicate scans during active session; keep loop running without blocking modal
                    console.log(`Duplicate scan ignored for ${student.student_id} while session active.`);
                    return;
                } else if (attendanceRecord.status === 'exited') {
                    // If already exited from prior verification (edge), prevent changes
                    Swal.fire({
                        icon: 'info',
                        title: 'Already Exited',
                        text: 'This student has already been marked as exited.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    return;
                }
            } else {
                // Exit verification phase: fingerprint-only exits
                if (attendanceRecord.status === 'present') {
                    attendanceRecord.exitTime = currentTime;
                    attendanceRecord.status = 'exited';
                    attendanceRecord.exitMethod = scanMethod; // fingerprint
                    Swal.fire({
                        icon: 'info',
                        title: 'Student Exited (Verified)',
                        html: `
                            <p><strong>Student:</strong> ${student.full_name}</p>
                            <p><strong>ID:</strong> ${student.student_id}</p>
                            <p><strong>Exit Time:</strong> ${currentTime.toLocaleTimeString()}</p>
                            <p><strong>Method:</strong> ${scanMethod}</p>
                        `,
                        timer: 2500,
                        showConfirmButton: false
                    });
                } else if (attendanceRecord.status === 'absent') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Entry Recorded',
                        text: 'This student did not enter. Cannot record an exit.',
                        timer: 2500,
                        showConfirmButton: false
                    });
                    return;
                } else if (attendanceRecord.status === 'exited') {
                    Swal.fire({
                        icon: 'info',
                        title: 'Already Exited',
                        text: 'Exit already recorded for this student.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    return;
                }
            }
            
            // Update the UI
            updateAttendanceTable(attendanceData);
            
            // Update statistics
            const presentCount = attendanceData.filter(s => s.status === 'present').length;
            const exitedCount = attendanceData.filter(s => s.status === 'exited').length;
            const totalCount = attendanceData.length;
            
            updateStats(presentCount, exitedCount, totalCount);
        }
        
        // Update attendance table
        function updateAttendanceTable(data) {
            if (data.length === 0) {
                participantsTableBody.innerHTML = `
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 30px; color: var(--gray);">
                            <i class="fas fa-spinner fa-spin" style="font-size: 40px; margin-bottom: 15px; display: block;"></i>
                            Waiting for attendance data...
                        </td>
                    </tr>
                `;
                return;
            }
            
            participantsTableBody.innerHTML = '';
            
            data.forEach(student => {
                const row = document.createElement('tr');
                
                const entryTime = student.entryTime ? 
                    student.entryTime.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : '—';
                
                const exitTime = student.exitTime ? 
                    student.exitTime.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : '—';
                
                const entryIcon = student.entryMethod === 'fingerprint' ? 
                    '<i class="fas fa-fingerprint scanner-icon fingerprint-icon" title="Fingerprint Entry"></i>' : 
                    (student.entryMethod ? '<i class="fas fa-qrcode scanner-icon barcode-icon" title="Barcode Entry"></i>' : '—');
                
                const exitIcon = student.exitMethod === 'barcode' ? 
                    '<i class="fas fa-qrcode scanner-icon barcode-icon" title="Barcode Exit"></i>' : 
                    (student.exitMethod === 'fingerprint' ? '<i class="fas fa-fingerprint scanner-icon fingerprint-icon" title="Fingerprint Exit"></i>' : '—');
                
                row.innerHTML = `
                    <td>${student.student_id}</td>
                    <td>${student.full_name}</td>
                    <td>${student.program}</td>
                    <td>${entryTime}</td>
                    <td>${exitTime}</td>
                    <td>${entryIcon} ${exitIcon}</td>
                    <td><span class="status ${student.status}">${student.status.charAt(0).toUpperCase() + student.status.slice(1)}</span></td>
                `;
                
                participantsTableBody.appendChild(row);
            });
        }
        
        // Update statistics
        function updateStats(present, exited, total) {
            totalPresentEl.textContent = present;
            totalExitedEl.textContent = exited;
            totalAbsentEl.textContent = total - present;
            attendanceRateEl.textContent = total > 0 ? Math.round((present / total) * 100) + '%' : '0%';
        }
        
        // Function to simulate scanner input (for testing)
        function simulateScannerInput() {
            if (!currentSession) return;
            
            // Get a random student for testing
            const randomStudent = allStudents[Math.floor(Math.random() * allStudents.length)];
            if (randomStudent) {
                const method = isExitPhase ? 'fingerprint' : 'barcode';
                const id = isExitPhase ? randomStudent.fingerprint_data : randomStudent.barcode_data;
                processStudentScan(id || randomStudent.student_id, method);
            }
        }
        
        // Manual student ID entry function
        function manualStudentEntry() {
            if (!currentSession) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Active Session',
                    text: 'Please start a session before entering student IDs.',
                    timer: 3000
                });
                return;
            }
            
            const mode = isExitPhase ? 'fingerprint verification' : 'barcode entry';
            
            Swal.fire({
                title: 'Enter Student ID',
                input: 'text',
                inputPlaceholder: `Enter student ID for ${mode}`,
                showCancelButton: true,
                confirmButtonText: 'Mark Attendance',
                cancelButtonText: 'Cancel',
                preConfirm: (studentId) => {
                    if (!studentId || studentId.trim() === '') {
                        Swal.showValidationMessage('Please enter a student ID');
                        return false;
                    }
                    return studentId.trim();
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const method = isExitPhase ? 'fingerprint' : 'barcode';
                    processStudentScan(result.value, method);
                }
            });
        }
        
        // Expose functions for testing and manual use
        window.processStudentScan = processStudentScan;
        window.simulateScannerInput = simulateScannerInput;
        window.manualStudentEntry = manualStudentEntry;
        window.startScannerMonitoring = startScannerMonitoring;
        window.stopScannerMonitoring = stopScannerMonitoring;

        // Modal-based exit verification UI
        function openExitVerificationModal() {
            if (!currentSession || !isExitPhase) return;
            Swal.fire({
                title: 'Exit Verification',
                html: `
                    <div id="exitModalContent" style="text-align:left">
                        <p><strong>Mode:</strong> Fingerprint-only</p>
                        <p><strong>Scanner:</strong> ${scannerIp}:5002</p>
                        <div id="scanStatus" style="margin-top:10px;color:#edd899">Ready to scan.</div>
                        <div id="lastResult" style="margin-top:12px"></div>
                        <div style="margin-top:16px;display:flex;gap:10px;flex-wrap:wrap">
                            <button id="modalScanBtn" class="swal2-confirm swal2-styled" style="background:#4361ee"><i class="fas fa-fingerprint"></i> Scan Fingerprint</button>
                            <button id="modalScanAnotherBtn" class="swal2-confirm swal2-styled" style="display:none;background:#4361ee"><i class="fas fa-fingerprint"></i> Scan another student</button>
                            <button id="modalSaveBtn" class="swal2-deny swal2-styled" style="background:#39ffa3;color:#111"><i class="fas fa-save"></i> Save & Finish</button>
                        </div>
                    </div>
                `,
                showConfirmButton: false,
                showCancelButton: false,
                didOpen: () => {
                    const scanBtn = document.getElementById('modalScanBtn');
                    const scanAnotherBtn = document.getElementById('modalScanAnotherBtn');
                    const saveBtn = document.getElementById('modalSaveBtn');
                    const scanStatus = document.getElementById('scanStatus');
                    const lastResult = document.getElementById('lastResult');

                    async function doScan() {
                        if (isScanning) return;
                        try {
                            isScanning = true;
                            scanBtn.disabled = true;
                            scanAnotherBtn.disabled = true;
                            scanStatus.textContent = 'Scanning... Place finger on the reader.';

                            const response = await fetch(`http://${scannerIp}:5002/finger/scan`, {
                                method: 'GET',
                                headers: { 'Content-Type': 'application/json' }
                            });
                            if (!response.ok) {
                                const txt = await response.text().catch(() => '');
                                throw new Error(`HTTP ${response.status}: ${txt || 'Scan failed'}`);
                            }
                            const data = await response.json();
                            if (data && data.matched && typeof data.finger_id !== 'undefined') {
                                const fingerId = String(data.finger_id);
                                await processStudentScan(fingerId, 'fingerprint');
                                scanStatus.textContent = 'Scan successful.';
                                const now = new Date();
                                lastResult.innerHTML = `
                                    <div style="margin-top:8px">
                                        <i class="fas fa-check-circle" style="color:#39ffa3"></i>
                                        Exit recorded at ${now.toLocaleTimeString()}.
                                    </div>
                                `;
                                scanAnotherBtn.style.display = 'inline-block';
                            } else {
                                scanStatus.textContent = 'No match. Try again.';
                                lastResult.innerHTML = `
                                    <div style="margin-top:8px">
                                        <i class="fas fa-exclamation-circle" style="color:#f72585"></i>
                                        No matching fingerprint found.
                                    </div>
                                `;
                                scanAnotherBtn.style.display = 'none';
                            }
                        } catch (e) {
                            scanStatus.textContent = e.message || 'Scanner error.';
                            lastResult.innerHTML = '';
                        } finally {
                            isScanning = false;
                            scanBtn.disabled = false;
                            scanAnotherBtn.disabled = false;
                        }
                    }

                    scanBtn.addEventListener('click', doScan);
                    scanAnotherBtn.addEventListener('click', () => {
                        scanAnotherBtn.style.display = 'none';
                        lastResult.innerHTML = '';
                        scanStatus.textContent = 'Ready to scan.';
                        doScan();
                    });
                    saveBtn.addEventListener('click', async () => {
                        await finalizeAndSaveAttendance();
                        Swal.close();
                    });
                },
                allowOutsideClick: false,
                allowEscapeKey: false
            });
        }
    </script>
</body>
</html>