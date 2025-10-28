<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MUBAS | Student Registration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
    <script src="js/supabase-auth.js"></script>
    <link rel="stylesheet" href="style/register.css">
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
            <li><a href="participants.php"><i class="fas fa-users"></i> <span>Participants</span></a></li>
            <li><a href="register.php" class="active"><i class="fas fa-user"></i> <span>Registration</span></a></li>
            <li><a href="reporty.php"><i class="fas fa-chart-bar"></i> <span>Reports</span></a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <h2><i class="fas fa-user-plus"></i> Student Registration</h2>
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

        <!-- Registration Form -->
        <div class="registration-container">
            <h3 class="form-title"><i class="fas fa-user-graduate"></i> Student Information</h3>
            
            <form id="studentRegistrationForm">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="fullName">Full Name *</label>
                        <input type="text" id="fullName" name="fullName" required placeholder="Enter student's full name">
                    </div>
                    
                    <div class="form-group">
                        <label for="studentId">Student ID *</label>
                        <input type="text" id="studentId" name="studentId" required placeholder="Enter student ID">
                    </div>
                    
                    <div class="form-group">
                        <label for="program">Program *</label>
                        <select id="program" name="program" required>
                            <option value="">Select Program</option>
                            <option value="BScIT">Bachelor of Electronics and Computer Engineering</option>
                            <option value="BIS">Bachelor of Information Management System</option>
                            <option value="BScCS">Bachelor of Science in Computer Science</option>
                            <option value="BIT">Bachelor of Information Technology</option>
                            <option value="BAF">Bachelor of Banking and Finance (Commerce)</option>
                            <option value="BBA">Bachelor of Business Administration</option>
                            <option value="BETE">Bachelor of Electronics and Telecommunications Engineering </option>
                            <option value="BEEE">Bachelor of Electrical and Electronic Engineering</option>
                            <option value="BSE">Bachelor of Software Engineering</option>
                            <option value="BCE">Bachelor of Civil Engineering</option>
                            <option value="BME">Bachelor of Mechanical Engineering</option>
                            <option value="EBS">Bachelor of Business and Education Studies</option>
                            <option value="BBC">Bachelor of Business Commucation</option>
                            <option value="BAE">Bachelor of Automobile Engineering</option>
                            <option value="BEd">Bachelor of Education and Media Studies</option>
                            <option value="LLB">Bachelor of Laws</option>
                            <option value="BCME">Bachelor of Enteprenuership</option>
                            <option value="BHRM">Bachelor of Hotel and Restaurant Management</option>
                            <option value="BLQ">Bachelor of Land Surveying </option>
                            <option value="BAJ">Bachelor of Arts in Journalism</option>
                            <option value="BCT">Bachelor of Tourism</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="year">Year of Study *</label>
                        <select id="year" name="year" required>
                            <option value="">Select Year</option>
                            <option value="1">Year 1</option>
                            <option value="2">Year 2</option>
                            <option value="3">Year 3</option>
                            <option value="4">Year 4</option>
                            <option value="5">Year 5</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" required placeholder="Enter email address">
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone Number *</label>
                        <input type="tel" id="phone" name="phone" required placeholder="Enter phone number">
                    </div>
                </div>
                
                <!-- Scanner API Configuration -->
                <div class="form-group">
                    <label for="scannerIp">Barcode Scanner IP Address *</label>
                    <input type="text" id="scannerIp" name="scannerIp" required placeholder="192.168.1.198" value="192.168.1.198">
                    <small>Enter the IP address of the PC running the barcode scanner API</small>
                </div>
                
                <!-- Biometric Section -->
                <div class="biometric-section">
                    <h3 class="biometric-title"><i class="fas fa-fingerprint"></i> Biometric Registration</h3>
                    
                    <div class="scanner-container">
                        <div class="scanner-box">
                            <div class="scanner-icon">
                                <i class="fas fa-fingerprint"></i>
                            </div>
                            <h4 class="scanner-title">Fingerprint Registration</h4>
                            <button type="button" class="scanner-btn" id="scanFingerprintBtn">
                                <i class="fas fa-hand-point-up"></i> Scan Fingerprint
                            </button>
                            <div class="scanner-status" id="fingerprintStatus">Not scanned yet</div>
                        </div>
                        
                        <div class="scanner-box">
                            <div class="scanner-icon">
                                <i class="fas fa-qrcode"></i>
                            </div>
                            <h4 class="scanner-title">Barcode/QR Registration</h4>
                            <button type="button" class="scanner-btn" id="scanBarcodeBtn">
                                <i class="fas fa-camera"></i> Scan Barcode
                            </button>
                            <div class="scanner-status" id="barcodeStatus">Not scanned yet</div>
                        </div>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="reset" class="btn btn-outline">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Register Student
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // DOM Elements
        const registrationForm = document.getElementById('studentRegistrationForm');
        const scanFingerprintBtn = document.getElementById('scanFingerprintBtn');
        const scanBarcodeBtn = document.getElementById('scanBarcodeBtn');
        const fingerprintStatus = document.getElementById('fingerprintStatus');
        const barcodeStatus = document.getElementById('barcodeStatus');
        
        // Fingerprint and Barcode data
        let fingerprintData = null;
        let barcodeData = null;
        
        // Initialize the application
        document.addEventListener('DOMContentLoaded', async function() {
            // Initialize authentication
            const isAuthenticated = await SupabaseAuth.initAuth();
            if (!isAuthenticated) return;
            
            // Event listeners
            setupEventListeners();
        });
        
        // Setup event listeners
        function setupEventListeners() {
            // Form submission
            registrationForm.addEventListener('submit', function(e) {
                e.preventDefault();
                registerStudent();
            });
            
            // Fingerprint scan button
            scanFingerprintBtn.addEventListener('click', scanFingerprint);
            
            // Barcode scan button
            scanBarcodeBtn.addEventListener('click', scanBarcode);
        }
        
	        // Utility: fetch with timeout (to avoid hanging during barcode scan)
	        async function fetchWithTimeout(resource, options = {}) {
			const { timeout = 60000 } = options; // default 60s
			const controller = new AbortController();
			const id = setTimeout(() => controller.abort(), timeout);
			try {
				const response = await fetch(resource, { ...options, signal: controller.signal });
				return response;
			} finally {
				clearTimeout(id);
			}
		}

		// Utility: delay
		function delay(ms) {
			return new Promise(resolve => setTimeout(resolve, ms));
		}

	        // Fingerprint enrollment using Flask API
        async function scanFingerprint() {
            const scannerIp = document.getElementById('scannerIp').value;
            const studentId = document.getElementById('studentId').value;
            
            if (!scannerIp) {
                Swal.fire({
                    icon: 'error',
                    title: 'Missing IP Address',
                    text: 'Please enter the scanner API IP address.',
                    timer: 3000
                });
                return;
            }
            
            if (!studentId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Student ID Required',
                    text: 'Please enter the Student ID before enrolling fingerprint.',
                    timer: 3000
                });
                return;
            }
            
            // Deterministically map studentId to a sensor slot (1-127)
            const computedId = (Array.from(studentId).reduce((acc, ch) => acc + ch.charCodeAt(0), 0) % 127) + 1;
            
            // Show enrolling status
            fingerprintStatus.textContent = `Connecting to sensor...`;
            fingerprintStatus.className = "scanner-status";
            
            try {
                const response = await fetch(`http://${scannerIp}:5002/finger/enroll`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id: computedId })
                });
                
                if (!response.ok) {
                    const text = await response.text().catch(() => '');
                    throw new Error(`HTTP ${response.status}: ${text || 'Enrollment request failed'}`);
                }
                
                const data = await response.json();
                
                if (data && data.enrolled) {
                    // Store the sensor slot id as fingerprint data
                    fingerprintData = String(data.id ?? computedId);
                    fingerprintStatus.textContent = `Fingerprint enrolled. ID: ${fingerprintData}`;
                    fingerprintStatus.className = "scanner-status status-success";
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Fingerprint Enrolled',
                        html: `Fingerprint successfully enrolled on sensor.<br/>Assigned ID: <strong>${fingerprintData}</strong>`,
                        timer: 2500,
                        showConfirmButton: false
                    });
                } else {
                    throw new Error((data && data.error) || 'Enrollment failed on sensor');
                }
                
            } catch (error) {
                console.error('Fingerprint enrollment error:', error);
                fingerprintStatus.textContent = "Connection or enrollment failed. Check IP or try again.";
                fingerprintStatus.className = "scanner-status status-error";
                
                Swal.fire({
                    icon: 'error',
                    title: 'Enrollment Failed',
                    text: `Could not enroll fingerprint via ${scannerIp}:5002. ${error.message || 'Please ensure the fingerprint API is running and the sensor is connected.'}`,
                    timer: 6000
                });
            }
        }
        
	        // Barcode scanning using Flask API
	        async function scanBarcode() {
            const scannerIp = document.getElementById('scannerIp').value;
            
            if (!scannerIp) {
                Swal.fire({
                    icon: 'error',
                    title: 'Missing IP Address',
                    text: 'Please enter the barcode scanner IP address.',
                    timer: 3000
                });
                return;
            }
            
			// Show scanning status
			barcodeStatus.textContent = "Preparing scanner...";
			barcodeStatus.className = "scanner-status";
            
	            try {
				// Detect API capabilities
				let supportsMode = false;
				try {
					const ping = await fetchWithTimeout(`http://${scannerIp}:5002/status`, { timeout: 4000, cache: 'no-store' });
					if (ping.ok) {
						const modeJson = await ping.json().catch(() => ({}));
						supportsMode = typeof modeJson.barcode_enabled !== 'undefined';
					}
				} catch (_) {
					// If /status not available, fall back to simple /scan polling
					supportsMode = false;
				}

				if (supportsMode) {
					// Ensure fingerprint is disabled and barcode is enabled (newer server)
					const enableResp = await fetchWithTimeout(`http://${scannerIp}:5002/barcode/enable`, {
						method: 'POST',
						headers: { 'Content-Type': 'application/json' },
						body: JSON.stringify({}),
						timeout: 10000
					});
					if (!enableResp.ok) {
						const txt = await enableResp.text().catch(() => '');
						throw new Error(`Failed to enable barcode mode (HTTP ${enableResp.status}): ${txt}`);
					}
				}

				// Wait for barcode using either blocking /scan or polling
				barcodeStatus.textContent = "Waiting for barcode...";
				let barcode = '';
				const startedAt = Date.now();
				const timeoutMs = 120000; // 2 minutes
				while (Date.now() - startedAt < timeoutMs) {
					const resp = await fetchWithTimeout(`http://${scannerIp}:5002/scan`, { method: 'GET', timeout: 10000, cache: 'no-store' });
					if (!resp.ok) {
						const txt = await resp.text().catch(() => '');
						throw new Error(`Scan failed (HTTP ${resp.status}): ${txt}`);
					}
					const data = await resp.json().catch(() => ({}));
					barcode = (data && data.barcode) ? String(data.barcode).trim() : '';
					if (barcode) break;
					await delay(1000);
				}

				if (barcode) {
					barcodeData = barcode;
					barcodeStatus.textContent = `Barcode: ${barcodeData}`;
					barcodeStatus.className = "scanner-status status-success";

					// If mode control supported, disable barcode to re-enable fingerprint
					if (supportsMode) {
						fetchWithTimeout(`http://${scannerIp}:5002/barcode/disable`, { method: 'POST', timeout: 8000 }).catch(() => {});
					}

					Swal.fire({
						icon: 'success',
						title: 'Barcode Scanned',
						text: `Barcode ${barcodeData} has been successfully scanned.`,
						timer: 2000,
						showConfirmButton: false
					});
				} else {
					throw new Error('No barcode data received from scanner');
				}
				
			} catch (error) {
				console.error('Barcode scan error:', error);
				barcodeStatus.textContent = (error.name === 'AbortError') ? "Timed out waiting for barcode" : "Scan failed. Check scanner mode or IP.";
				barcodeStatus.className = "scanner-status status-error";
				
				// Attempt to re-enable fingerprint mode to keep system usable (if supported)
				try { await fetchWithTimeout(`http://${scannerIp}:5002/finger/enable`, { method: 'POST', timeout: 5000 }); } catch (_) {}
				
				Swal.fire({
					icon: 'error',
					title: 'Scanner Error',
					html: `${(error && error.message ? error.message : 'Unexpected error during barcode scan.')}
						<br/><br/>
						<strong>Troubleshoot:</strong>
					<ul style="text-align:left;">
						<li>Open <code>http://${scannerIp}:5002</code> (and <code>/status</code> if available) in this browser.</li>
							<li>Verify <code>app2.py</code> is running with host <code>0.0.0.0</code>.</li>
							<li>Allow inbound TCP 5002 in Windows Firewall.</li>
							<li>Confirm the IP is correct for the scanner PC.</li>
						</ul>`,
					footer: `Tried http://${scannerIp}:5002`,
					timer: 6000
				});
			}
        }
        
        
        
        // Generate random barcode data for simulation
        function generateRandomBarcodeData() {
            return 'MUB' + Math.floor(1000000 + Math.random() * 9000000);
        }
        
        // Register student in Supabase
        async function registerStudent() {
            // Validate form
            const fullName = document.getElementById('fullName').value;
            const studentId = document.getElementById('studentId').value;
            const program = document.getElementById('program').value;
            const year = document.getElementById('year').value;
            const email = document.getElementById('email').value;
            const phone = document.getElementById('phone').value;
            
            if (!fullName || !studentId || !program || !year || !email || !phone) {
                Swal.fire({
                    icon: 'error',
                    title: 'Missing Information',
                    text: 'Please fill in all required fields.',
                    timer: 3000
                });
                return;
            }
            
            // Check if biometric data is captured
            if (!fingerprintData) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Fingerprint Required',
                    text: 'Please scan the student\'s fingerprint before registration.',
                    timer: 3000
                });
                return;
            }
            
            if (!barcodeData) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Barcode Required',
                    text: 'Please scan the student\'s barcode before registration.',
                    timer: 3000
                });
                return;
            }
            
            // Show registration progress
            Swal.fire({
                title: 'Registering Student...',
                text: 'Please wait while we save the student information',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading()
                }
            });
            
            try {
                // Get current user data
                const userData = SupabaseAuth.getCurrentUser();
                
                if (!userData) {
                    throw new Error('No user data found. Please log in again.');
                }
                
                // Prepare student data
                const studentData = {
                    student_id: studentId,
                    full_name: fullName,
                    email: email,
                    phone: phone,
                    program: program,
                    year_of_study: parseInt(year),
                    fingerprint_data: fingerprintData,
                    barcode_data: barcodeData,
                    registered_by: userData.id
                };
                
                // Insert student into Supabase
                const { data, error } = await SupabaseAuth.supabase
                    .from('students')
                    .insert([studentData])
                    .select();
                
                if (error) {
                    throw error;
                }
                
                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Registration Successful!',
                    html: `
                        <p>Student <strong>${fullName}</strong> has been successfully registered.</p>
                        <p><strong>Student ID:</strong> ${studentId}</p>
                        <p><strong>Program:</strong> ${program} Year ${year}</p>
                        <p><strong>Barcode:</strong> ${barcodeData}</p>
                    `,
                    confirmButtonText: 'Register Another Student'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Reset form
                        resetForm();
                    }
                });
                
            } catch (error) {
                console.error('Registration error:', error);
                console.error('Error details:', {
                    message: error.message,
                    code: error.code,
                    details: error.details,
                    hint: error.hint
                });
                
                let errorMessage = 'An error occurred during registration.';
                
                if (error.message && error.message.includes('No user data found')) {
                    errorMessage = 'Please log in again to register students.';
                } else if (error.code === '23505') {
                    if (error.message.includes('student_id')) {
                        errorMessage = 'A student with this ID already exists.';
                    } else if (error.message.includes('email')) {
                        errorMessage = 'A student with this email already exists.';
                    }
                } else if (error.code === '42P01') {
                    errorMessage = 'Students table not found. Please contact administrator.';
                } else if (error.code === '42501') {
                    errorMessage = 'Permission denied. Please contact administrator.';
                } else if (error.message) {
                    errorMessage = `Database error: ${error.message}`;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Registration Failed',
                    text: errorMessage,
                    footer: 'Check browser console for detailed error information',
                    timer: 8000
                });
            }
        }
        
        // Reset form function
        function resetForm() {
            registrationForm.reset();
            fingerprintData = null;
            barcodeData = null;
            fingerprintStatus.textContent = "Not scanned yet";
            fingerprintStatus.className = "scanner-status";
            barcodeStatus.textContent = "Not scanned yet";
            barcodeStatus.className = "scanner-status";
			document.getElementById('scannerIp').value = "192.168.1.198";
        }
    </script>
</body>
</html>