<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MUBAS | Session Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
    <script src="js/supabase-auth.js"></script>
    <link rel="stylesheet" href="style/session.css">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <h1><i class="image"><img src="mubas-logo-full.png"></i> MUBAS-<span>ATTEND</span></h1>
        </div>
        <ul class="nav-links">
            <li><a href="index.php"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
            <li><a href="session.php" class="active"><i class="fas fa-calendar-alt"></i> <span>Sessions</span></a></li>
            <li><a href="participants.php"><i class="fas fa-users"></i> <span>Participants</span></a></li>
            <li><a href="register.php"><i class="fas fa-user"></i> <span>Registration</span></a></li>
            <li><a href="reporty.php"><i class="fas fa-chart-bar"></i> <span>Reports</span></a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <h2><i class="fas fa-calendar-alt"></i> Session Management</h2>
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

        <!-- Session Creation Form -->
        <div class="session-container">
            <h3 class="form-title"><i class="fas fa-plus-circle"></i> Create New Session</h3>
            
            <form id="sessionCreationForm">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="courseSelect">Course *</label>
                        <select id="courseSelect" name="course" required>
                            <option value="">Select Course</option>
                            <option value="COE321">Computer Organization and Design (COD511)</option>
                            <option value="DSP401">Digital Signal Processing (DSP401)</option>
                            <option value="ECE201">Electrical Circuits (ECE201)</option>
                            <option value="LE301">Linear Electronics (LE301)</option>
                            <option value="SE401">Software Engineering (SE401)</option>
                            <option value="CIV101">Civil Engineering Basics (CIV101)</option>
                            <option value="MEC201">Mechanics of Materials (MEC201)</option>
                            <option value="ENV301">Environmental Science (ENV301)</option>
                            <option value="MAT101">Calculus I (MAT101)</option>
                            <option value="PHY101">Physics I (PHY101)</option>
                            <option value="CHE101">Chemistry I (CHE101)</option>
                            <option value="STA201">Statistics for Engineers (STA201)</option>
                            <option value="ECO101">Microeconomics (ECO101)</option>
                            <option value="ACC201">Financial Accounting (ACC201)</option>
                            <option value="MGT301">Principles of Management (MGT301)</option>
                            <option value="MKT401">Marketing Fundamentals (MKT401)</option>
                            <option value="LAW101">Business Law (LAW101)</option>
                            <option value="HRM201">Human Resource Management (HRM201)</option>
                            <option value="COM101">Introduction to Communication (COM101)</option>
                            <option value="PSY201">Organizational Behavior (PSY201)</option>
                            <option value="SOC301">Sociology for Engineers (SOC301)</option>
                            <option value="ENG101">English for Academic Purposes (ENG101)</option>
                            <option value="HIS201">History of Technology (HIS201)</option>
                            <option value="PHI301">Ethics in Engineering (PHI301)</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="sessionTitle">Session Title *</label>
                        <input type="text" id="sessionTitle" name="sessionTitle" required placeholder="Enter session title">
                    </div>
                    
                    <div class="form-group">
                        <label for="startTime">Start Time *</label>
                        <input type="datetime-local" id="startTime" name="startTime" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="endTime">End Time *</label>
                        <input type="datetime-local" id="endTime" name="endTime" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="location">Location *</label>
                        <input type="text" id="location" name="location" required placeholder="Enter classroom/lab location">
                    </div>
                    
                    <div class="form-group">
                        <label for="sessionType">Session Type</label>
                        <select id="sessionType" name="sessionType">
                            <option value="lecture">Lecture</option>
                            <option value="lab">Laboratory</option>
                            <option value="tutorial">Tutorial</option>
                            <option value="workshop">Workshop</option>
                            <option value="Examination">Examination</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="sessionDescription">Description</label>
                    <textarea id="sessionDescription" name="sessionDescription" rows="3" placeholder="Enter session description (optional)"></textarea>
                </div>
                

                
                <div class="form-actions">
                    <button type="reset" class="btn btn-outline">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Session
                    </button>
                </div>
            </form>
        </div>

        <!-- Active Sessions Section -->
        <div class="active-sessions">
            <h3 class="section-title"><i class="fas fa-list-alt"></i> Active Sessions</h3>
            
            <div class="sessions-table">
                <table>
                    <thead>
                        <tr>
                            <th>Course</th>
                            <th>Session Title</th>
                            <th>Date & Time</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="sessionsTableBody">
                        <!-- Sessions will be loaded dynamically from database -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // DOM Elements
        const sessionForm = document.getElementById('sessionCreationForm');
        const sessionsTableBody = document.getElementById('sessionsTableBody');
        
        // Sample courses data (would come from database in real application)
        const courses = [
            { id: 'COE321', name: 'Computer Organization and Design', lecturer: 'Dr. James Phiri' },
            { id: 'DSP401', name: 'Digital Signal Processing', lecturer: 'Prof. Alice Banda' },
            { id: 'ECE201', name: 'Electrical Circuits', lecturer: 'Dr. Robert Chiwala' },
            { id: 'LE301', name: 'Linear Electronics', lecturer: 'Dr. Sarah Kambewa' },
            { id: 'SE401', name: 'Software Engineering', lecturer: 'Prof. Mike Jere' }
        ];
        
        // Initialize the application
        document.addEventListener('DOMContentLoaded', async function() {
            // Initialize authentication
            const isAuthenticated = await SupabaseAuth.initAuth();
            if (!isAuthenticated) return;
            
            // Set default times
            const now = new Date();
            const startTime = new Date(now.getTime() + 30 * 60000); // 30 minutes from now
            const endTime = new Date(now.getTime() + 90 * 60000); // 90 minutes from now
            
            document.getElementById('startTime').value = formatDateTimeLocal(startTime);
            document.getElementById('endTime').value = formatDateTimeLocal(endTime);
            
            // Event listeners
            setupEventListeners();
            
            // Load existing sessions
            loadSessions();
        });
        
        // Format date for datetime-local input
        function formatDateTimeLocal(date) {
            const year = date.getFullYear();
            const month = (date.getMonth() + 1).toString().padStart(2, '0');
            const day = date.getDate().toString().padStart(2, '0');
            const hours = date.getHours().toString().padStart(2, '0');
            const minutes = date.getMinutes().toString().padStart(2, '0');
            
            return `${year}-${month}-${day}T${hours}:${minutes}`;
        }
        
        // Setup event listeners
        function setupEventListeners() {
            // Form submission
            sessionForm.addEventListener('submit', function(e) {
                e.preventDefault();
                createSession();
            });
        }
        
        // Create a new session
        async function createSession() {
            const courseSelect = document.getElementById('courseSelect');
            const sessionTitle = document.getElementById('sessionTitle').value;
            const startTime = document.getElementById('startTime').value;
            const endTime = document.getElementById('endTime').value;
            const location = document.getElementById('location').value;
            const sessionType = document.getElementById('sessionType').value;
            const sessionDescription = document.getElementById('sessionDescription').value;
            
            if (!courseSelect.value || !sessionTitle || !startTime || !endTime || !location) {
                Swal.fire({
                    icon: 'error',
                    title: 'Missing Information',
                    text: 'Please fill in all required fields.',
                    timer: 3000
                });
                return;
            }
            
            if (new Date(startTime) >= new Date(endTime)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Time',
                    text: 'End time must be after start time.',
                    timer: 3000
                });
                return;
            }
            
            // Show loading
            Swal.fire({
                title: 'Creating Session...',
                text: 'Please wait while we create your session',
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
                
                // Generate session ID
                const sessionId = 'S' + Date.now() + Math.floor(Math.random() * 1000);
                
                // Prepare session data
                const sessionData = {
                    session_id: sessionId,
                    lecturer_id: userData.id,
                    course: courseSelect.value,
                    session_title: sessionTitle,
                    start_time: startTime,
                    end_time: endTime,
                    location: location,
                    session_type: sessionType,
                    description: sessionDescription,
                    status: 'scheduled',
                    created_at: new Date().toISOString()
                };
                
                // Insert session into Supabase
                const { data, error } = await SupabaseAuth.supabase
                    .from('sessions')
                    .insert([sessionData])
                    .select();
                
                if (error) {
                    throw error;
                }
                
                // Reset form
                document.getElementById('sessionCreationForm').reset();
                
                // Reload sessions to show the new one
                await loadSessions();
                
                // Show success message
                const course = courses.find(c => c.id === courseSelect.value);
                const startDate = new Date(startTime);
                const formattedDate = startDate.toLocaleDateString('en-US', {
                    month: 'short',
                    day: 'numeric',
                    year: 'numeric',
                    hour: 'numeric',
                    minute: '2-digit'
                });
                
                Swal.fire({
                    icon: 'success',
                    title: 'Session Created!',
                    html: `
                        <p>Session <strong>${sessionTitle}</strong> has been successfully created.</p>
                        <p><strong>Course:</strong> ${course ? course.name : courseSelect.value}</p>
                        <p><strong>Time:</strong> ${formattedDate}</p>
                    `,
                    confirmButtonText: 'Continue'
                });
                
            } catch (error) {
                console.error('Session creation error:', error);
                
                let errorMessage = 'An error occurred while creating the session.';
                
                if (error.message && error.message.includes('No user data found')) {
                    errorMessage = 'Please log in again to create sessions.';
                    window.location.href = 'login.php';
                    return;
                } else if (error.code === '23505') {
                    errorMessage = 'A session with this ID already exists. Please try again.';
                } else if (error.code === '42P01') {
                    errorMessage = 'Sessions table not found. Please contact administrator.';
                } else if (error.code === '42501') {
                    errorMessage = 'Permission denied. Please contact administrator.';
                } else if (error.message) {
                    errorMessage = `Database error: ${error.message}`;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Session Creation Failed',
                    text: errorMessage,
                    timer: 5000
                });
            }
        }
        

        
        // Edit session
        function editSession(sessionId) {
            Swal.fire({
                title: 'Edit Session',
                text: 'Edit functionality would open here...',
                icon: 'info',
                confirmButtonText: 'OK'
            });
        }
        
        // Load sessions from database
        async function loadSessions() {
            try {
                // Get current user data
                const userData = SupabaseAuth.getCurrentUser();
                
                if (!userData) {
                    console.error('User not authenticated');
                    return;
                }
                
                // Fetch sessions from Supabase
                const { data: sessions, error } = await SupabaseAuth.supabase
                    .from('sessions')
                    .select('*')
                    .eq('lecturer_id', userData.id)
                    .order('start_time', { ascending: false });
                
                if (error) {
                    throw error;
                }
                
                // Clear existing table content
                sessionsTableBody.innerHTML = '';
                
                // Add sessions to table
                if (sessions && sessions.length > 0) {
                    sessions.forEach(session => {
                        const startDate = new Date(session.start_time);
                        const formattedDate = startDate.toLocaleDateString('en-US', {
                            month: 'short',
                            day: 'numeric',
                            year: 'numeric',
                            hour: 'numeric',
                            minute: '2-digit'
                        });
                        
                        const course = courses.find(c => c.id === session.course);
                        const courseName = course ? course.name : session.course;
                        
                        let statusClass = 'pending';
                        let statusText = 'Scheduled';
                        
                        switch(session.status) {
                            case 'active':
                                statusClass = 'active';
                                statusText = 'Active';
                                break;
                            case 'completed':
                                statusClass = 'completed';
                                statusText = 'Completed';
                                break;
                            case 'cancelled':
                                statusClass = 'cancelled';
                                statusText = 'Cancelled';
                                break;
                        }
                        
                        const sessionRow = `
                            <tr>
                                <td>${courseName} (${session.course})</td>
                                <td>${session.session_title}</td>
                                <td>${formattedDate}</td>
                                <td>${session.location}</td>
                                <td><span class="status ${statusClass}">${statusText}</span></td>
                                <td>
                                    ${session.status === 'scheduled' ? `
                                        <button class="action-btn" title="Edit" onclick="editSession('${session.session_id}')"><i class="fas fa-edit"></i></button>
                                        <button class="action-btn" title="Cancel Session" onclick="cancelSession('${session.session_id}')"><i class="fas fa-times"></i></button>
                                    ` : session.status === 'active' ? `
                                        <button class="action-btn" title="Close Session" onclick="closeSession('${session.session_id}')"><i class="fas fa-lock"></i></button>
                                    ` : session.status === 'completed' ? `
                                        <button class="action-btn" title="View Report"><i class="fas fa-chart-bar"></i></button>
                                        <button class="action-btn" title="Delete" onclick="deleteSession('${session.session_id}')"><i class="fas fa-trash"></i></button>
                                    ` : ''}
                                </td>
                            </tr>
                        `;
                        
                        sessionsTableBody.innerHTML += sessionRow;
                    });
                } else {
                    sessionsTableBody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 20px;">No sessions found. Create your first session above.</td></tr>';
                }
                
            } catch (error) {
                console.error('Error loading sessions:', error);
                
                let errorMessage = 'Failed to load sessions.';
                if (error.code === '42P01') {
                    errorMessage = 'Sessions table not found. Please contact administrator.';
                } else if (error.code === '42501') {
                    errorMessage = 'Permission denied. Please contact administrator.';
                }
                
                sessionsTableBody.innerHTML = `<tr><td colspan="6" style="text-align: center; padding: 20px; color: red;">Error: ${errorMessage}</td></tr>`;
            }
        }
        
        // Cancel session
        async function cancelSession(sessionId) {
            const userData = SupabaseAuth.getCurrentUser();
            if (!userData) {
                Swal.fire({
                    icon: 'error',
                    title: 'Authentication Error',
                    text: 'Please log in again.',
                    timer: 3000
                });
                return;
            }
            
            Swal.fire({
                title: 'Cancel Session',
                text: 'Are you sure you want to cancel this session?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, cancel it',
                cancelButtonText: 'No, keep it'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        // Update session status in Supabase
                        const { error } = await SupabaseAuth.supabase
                            .from('sessions')
                            .update({ status: 'cancelled' })
                            .eq('session_id', sessionId)
                            .eq('lecturer_id', userData.id);
                        
                        if (error) {
                            throw error;
                        }
                        
                        Swal.fire(
                            'Cancelled!',
                            'Your session has been cancelled.',
                            'success'
                        );
                        
                        // Reload sessions to update the display
                        await loadSessions();
                        
                    } catch (error) {
                        console.error('Error cancelling session:', error);
                        Swal.fire(
                            'Error!',
                            error.message || 'An error occurred while cancelling the session',
                            'error'
                        );
                    }
                }
            });
        }
        
        // Close session (mark as completed)
        async function closeSession(sessionId) {
            const userData = SupabaseAuth.getCurrentUser();
            if (!userData) {
                Swal.fire({
                    icon: 'error',
                    title: 'Authentication Error',
                    text: 'Please log in again.',
                    timer: 3000
                });
                return;
            }
            
            Swal.fire({
                title: 'Close Session',
                text: 'Are you sure you want to close this session?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, close it',
                cancelButtonText: 'No, keep it open'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        // Update session status in Supabase
                        const { error } = await SupabaseAuth.supabase
                            .from('sessions')
                            .update({ status: 'completed' })
                            .eq('session_id', sessionId)
                            .eq('lecturer_id', userData.id);
                        
                        if (error) {
                            throw error;
                        }
                        
                        Swal.fire(
                            'Closed!',
                            'Your session has been closed.',
                            'success'
                        );
                        
                        // Reload sessions to update the display
                        await loadSessions();
                        
                    } catch (error) {
                        console.error('Error closing session:', error);
                        Swal.fire(
                            'Error!',
                            error.message || 'An error occurred while closing the session',
                            'error'
                        );
                    }
                }
            });
        }
        
        // Delete session
        async function deleteSession(sessionId) {
            const userData = SupabaseAuth.getCurrentUser();
            if (!userData) {
                Swal.fire({
                    icon: 'error',
                    title: 'Authentication Error',
                    text: 'Please log in again.',
                    timer: 3000
                });
                return;
            }
            
            Swal.fire({
                title: 'Delete Session',
                text: 'Are you sure you want to permanently delete this session? This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'No, keep it',
                confirmButtonColor: '#d33'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        // Delete session from Supabase
                        const { error } = await SupabaseAuth.supabase
                            .from('sessions')
                            .delete()
                            .eq('session_id', sessionId)
                            .eq('lecturer_id', userData.id);
                        
                        if (error) {
                            throw error;
                        }
                        
                        Swal.fire(
                            'Deleted!',
                            'The session has been permanently deleted.',
                            'success'
                        );
                        
                        // Reload sessions to update the display
                        await loadSessions();
                        
                    } catch (error) {
                        console.error('Error deleting session:', error);
                        Swal.fire(
                            'Error!',
                            error.message || 'An error occurred while deleting the session',
                            'error'
                        );
                    }
                }
            });
        }
    </script>
</body>
</html>