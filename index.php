<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MUBAS | Attendance System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
    <script src="js/supabase-auth.js"></script>
    <link href="style/index.css" rel="stylesheet">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <h1><i class="image"><img src = "mubas-logo-full.png"></i> MUBAS-<span>ATTEND</span></h1>
        </div>
        <ul class="nav-links">
            <li><a href="index.php" class="active"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
            <li><a href="session.php"><i class="fas fa-calendar-alt"></i> <span>Sessions</span></a></li>
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
            <h2><i class="fas fa-home"></i> Dashboard</h2>
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

        <!-- Dashboard Cards -->
        <div class="dashboard-cards">
            <div class="card card-1">
                <div class="card-icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <h3 id="totalSessions">Loading...</h3>
                <p>Total Sessions</p>
            </div>
            <div class="card card-2">
                <div class="card-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3 id="totalParticipants">Loading...</h3>
                <p>Registered Students</p>
            </div>
            <div class="card card-3">
                <div class="card-icon">
                    <i class="fas fa-user-check"></i>
                </div>
                <h3 id="attendanceRate">Loading...</h3>
                <p>Average Attendance</p>
            </div>
            <div class="card card-4">
                <div class="card-icon">
                    <i class="fas fa-running"></i>
                </div>
                <h3 id="activeSessions">Loading...</h3>
                <p>Active Sessions</p>
            </div>
        </div>

        <!-- Charts - FIXED SIZE -->
        <div class="charts">
            <div class="chart-container">
                <div class="chart-header">
                    <h3>Attendance Overview</h3>
                    <select id="timeRange">
                        <option>Last 7 Days</option>
                        <option>Last 30 Days</option>
                        <option>Last 90 Days</option>
                    </select>
                </div>
                <div class="chart-wrapper">
                    <canvas id="attendanceChart"></canvas>
                </div>
            </div>
            <div class="chart-container">
                <div class="chart-header">
                    <h3>Attendance by Status</h3>
                </div>
                <div class="chart-wrapper">
                    <canvas id="attendancePie"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Sessions Section -->
        <h3 class="section-title"><i class="fas fa-calendar-alt"></i> Recent Sessions</h3>
        
        <div class="session-controls">
            <button class="btn btn-primary" id="createSessionBtn">
                <i class="fas fa-plus-circle"></i> Create New Session
            </button>
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="sessionSearch" placeholder="Search sessions...">
            </div>
        </div>

        <!-- Recent Sessions Table -->
        <div class="sessions-table">
            <table id="sessionsTable">
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Session Name</th>
                        <th>Date & Time</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="sessionsBody">
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 30px; color: var(--gray);">
                            <i class="fas fa-spinner fa-spin" style="font-size: 24px; margin-bottom: 10px; display: block;"></i>
                            Loading sessions...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Create Session Modal -->
        <div class="modal" id="sessionModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Create New Session</h3>
                    <button class="close-modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="courseSelect">Course</label>
                        <select id="courseSelect">
                            <option value="">Select Course</option>
                            <option value="COE321">Computer Organization and Design (COE321)</option>
                            <option value="DSP401">Digital Signal Processing (DSP401)</option>
                            <option value="ECE201">Electrical Circuits (ECE201)</option>
                            <option value="LE301">Linear Electronics (LE301)</option>
                            <option value="SE401">Software Engineering (SE401)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="sessionName">Session Name</label>
                        <input type="text" id="sessionName" placeholder="Enter session name">
                    </div>
                    <div class="form-group">
                        <label for="sessionDate">Date & Time</label>
                        <input type="datetime-local" id="sessionDate">
                    </div>
                    <div class="form-group">
                        <label for="sessionType">Session Type</label>
                        <select id="sessionType">
                            <option value="lecture">Lecture</option>
                            <option value="lab">Laboratory</option>
                            <option value="tutorial">Tutorial</option>
                            <option value="workshop">Workshop</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="sessionLocation">Location</label>
                        <input type="text" id="sessionLocation" placeholder="Enter location">
                    </div>
                    <div class="form-group">
                        <label for="sessionDescription">Description</label>
                        <textarea id="sessionDescription" rows="3" placeholder="Enter session description"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline close-modal">Cancel</button>
                    <button class="btn btn-primary" id="saveSessionBtn">Create Session</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // State variables
        let dashboardData = {
            sessions: [],
            students: [],
            attendance: []
        };

        let attendanceChartInstance = null;
        let pieChartInstance = null;

        // DOM Elements
        const sessionModal = document.getElementById('sessionModal');
        const createSessionBtn = document.getElementById('createSessionBtn');
        const saveSessionBtn = document.getElementById('saveSessionBtn');
        const sessionSearch = document.getElementById('sessionSearch');
        const sessionsBody = document.getElementById('sessionsBody');
        const closeModalBtns = document.querySelectorAll('.close-modal');

        // Initialize the application
        document.addEventListener('DOMContentLoaded', async function() {
            // Initialize authentication
            const isAuthenticated = await SupabaseAuth.initAuth();
            if (!isAuthenticated) return;
            
            // Load all dashboard data
            await loadDashboardData();
            
            // Initialize charts
            initCharts();
            
            // Update dashboard stats
            updateDashboardStats();
            
            // Populate recent sessions
            renderRecentSessions();
            
            // Event listeners
            setupEventListeners();
        });

        // Load all data from database
        async function loadDashboardData() {
            try {
                const userData = SupabaseAuth.getCurrentUser();
                if (!userData) {
                    throw new Error('User not authenticated');
                }

                // Load sessions
                const { data: sessions, error: sessionsError } = await SupabaseAuth.supabase
                    .from('sessions')
                    .select('*')
                    .eq('lecturer_id', userData.id)
                    .order('created_at', { ascending: false });

                if (sessionsError) throw sessionsError;
                dashboardData.sessions = sessions || [];

                // Load students
                const { data: students, error: studentsError } = await SupabaseAuth.supabase
                    .from('students')
                    .select('*')
                    .order('created_at', { ascending: false });

                if (studentsError) throw studentsError;
                dashboardData.students = students || [];

                // Load attendance data for charts
                const { data: attendance, error: attendanceError } = await SupabaseAuth.supabase
                    .from('attendance')
                    .select('*')
                    .order('created_at', { ascending: false });

                if (attendanceError) {
                    console.warn('Attendance table not found or empty:', attendanceError);
                    dashboardData.attendance = [];
                } else {
                    dashboardData.attendance = attendance || [];
                }

                console.log('Dashboard data loaded:', {
                    sessions: dashboardData.sessions.length,
                    students: dashboardData.students.length,
                    attendance: dashboardData.attendance.length
                });

            } catch (error) {
                console.error('Failed to load dashboard data:', error);
                
                // Show error message
                document.getElementById('totalSessions').textContent = 'Error';
                document.getElementById('totalParticipants').textContent = 'Error';
                document.getElementById('attendanceRate').textContent = 'Error';
                document.getElementById('activeSessions').textContent = 'Error';
                
                sessionsBody.innerHTML = `
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 30px; color: red;">
                            <i class="fas fa-exclamation-triangle" style="font-size: 24px; margin-bottom: 10px; display: block;"></i>
                            Error loading data: ${error.message}
                        </td>
                    </tr>
                `;
            }
        }

        // Update dashboard statistics
        function updateDashboardStats() {
            // Total Sessions
            document.getElementById('totalSessions').textContent = dashboardData.sessions.length;
            
            // Total Students
            document.getElementById('totalParticipants').textContent = dashboardData.students.length;
            
            // Active Sessions
            const activeSessions = dashboardData.sessions.filter(session => session.status === 'active').length;
            document.getElementById('activeSessions').textContent = activeSessions;
            
            // Calculate attendance rate
            let attendanceRate = 0;
            if (dashboardData.attendance.length > 0) {
                const presentCount = dashboardData.attendance.filter(record => 
                    record.status === 'present' || record.status === 'exited'
                ).length;
                const totalCount = dashboardData.attendance.length;
                attendanceRate = totalCount > 0 ? Math.round((presentCount / totalCount) * 100) : 0;
            }
            document.getElementById('attendanceRate').textContent = `${attendanceRate}%`;
        }

        // Render recent sessions
        function renderRecentSessions() {
            if (dashboardData.sessions.length === 0) {
                sessionsBody.innerHTML = `
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 30px; color: var(--gray);">
                            <i class="fas fa-calendar-plus" style="font-size: 24px; margin-bottom: 10px; display: block;"></i>
                            No sessions found. Create your first session to get started.
                        </td>
                    </tr>
                `;
                return;
            }

            sessionsBody.innerHTML = '';
            
            // Show only recent 10 sessions
            const recentSessions = dashboardData.sessions.slice(0, 10);
            
            recentSessions.forEach(session => {
                const row = document.createElement('tr');
                
                // Format date
                const sessionDate = session.start_time ? new Date(session.start_time) : null;
                const formattedDate = sessionDate ? sessionDate.toLocaleDateString('en-US', {
                    month: 'short',
                    day: 'numeric',
                    year: 'numeric',
                    hour: 'numeric',
                    minute: '2-digit'
                }) : 'Not scheduled';
                
                // Status display
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
                
                row.innerHTML = `
                    <td>${session.course || 'N/A'}</td>
                    <td>${session.session_title || session.name || 'Untitled'}</td>
                    <td>${formattedDate}</td>
                    <td>${session.location || 'TBD'}</td>
                    <td><span class="status ${statusClass}">${statusText}</span></td>
                    <td>
                        <button class="action-btn edit-session" data-id="${session.session_id}" title="Edit"><i class="fas fa-edit"></i></button>
                        <button class="action-btn view-session" data-id="${session.session_id}" title="View Details"><i class="fas fa-eye"></i></button>
                        ${session.status === 'completed' ? 
                            `<button class="action-btn view-report" data-id="${session.session_id}" title="View Report"><i class="fas fa-chart-bar"></i></button>` : 
                            ''
                        }
                    </td>
                `;
                
                sessionsBody.appendChild(row);
            });
            
            // Add event listeners to action buttons
            addActionListeners();
        }

        // Add event listeners to action buttons
        function addActionListeners() {
            document.querySelectorAll('.edit-session').forEach(btn => {
                btn.addEventListener('click', function() {
                    const sessionId = this.getAttribute('data-id');
                    window.location.href = `session.php?edit=${sessionId}`;
                });
            });
            
            document.querySelectorAll('.view-session').forEach(btn => {
                btn.addEventListener('click', function() {
                    const sessionId = this.getAttribute('data-id');
                    viewSessionDetails(sessionId);
                });
            });
            
            document.querySelectorAll('.view-report').forEach(btn => {
                btn.addEventListener('click', function() {
                    const sessionId = this.getAttribute('data-id');
                    window.location.href = `reporty.php?session=${sessionId}`;
                });
            });
        }

        // View session details
        function viewSessionDetails(sessionId) {
            const session = dashboardData.sessions.find(s => s.session_id === sessionId);
            if (!session) return;

            const sessionDate = session.start_time ? new Date(session.start_time) : null;
            const endDate = session.end_time ? new Date(session.end_time) : null;
            
            const formattedStart = sessionDate ? sessionDate.toLocaleString() : 'Not scheduled';
            const formattedEnd = endDate ? endDate.toLocaleString() : 'Not scheduled';

            Swal.fire({
                title: session.session_title || 'Session Details',
                html: `
                    <div style="text-align: left; margin: 20px 0;">
                        <p><strong>Course:</strong> ${session.course || 'N/A'}</p>
                        <p><strong>Type:</strong> ${session.session_type || 'N/A'}</p>
                        <p><strong>Location:</strong> ${session.location || 'TBD'}</p>
                        <p><strong>Start Time:</strong> ${formattedStart}</p>
                        <p><strong>End Time:</strong> ${formattedEnd}</p>
                        <p><strong>Status:</strong> ${session.status || 'scheduled'}</p>
                        <p><strong>Description:</strong> ${session.description || 'No description'}</p>
                    </div>
                `,
                confirmButtonText: 'Close',
                width: 600
            });
        }

        // Initialize charts with real data
        function initCharts() {
            initAttendanceChart();
            initPieChart();
        }

        // Initialize attendance line chart
        function initAttendanceChart() {
            const ctx = document.getElementById('attendanceChart').getContext('2d');
            
            // Process attendance data for the last 7 days
            const last7Days = [];
            const attendanceRates = [];
            
            for (let i = 6; i >= 0; i--) {
                const date = new Date();
                date.setDate(date.getDate() - i);
                const dateStr = date.toDateString();
                
                last7Days.push(date.toLocaleDateString('en-US', { weekday: 'short' }));
                
                // Calculate attendance rate for this day
                const dayAttendance = dashboardData.attendance.filter(record => {
                    if (!record.created_at) return false;
                    const recordDate = new Date(record.created_at);
                    return recordDate.toDateString() === dateStr;
                });
                
                let rate = 0;
                if (dayAttendance.length > 0) {
                    const presentCount = dayAttendance.filter(r => r.status === 'present' || r.status === 'exited').length;
                    rate = Math.round((presentCount / dayAttendance.length) * 100);
                }
                
                attendanceRates.push(rate);
            }
            
            if (attendanceChartInstance) {
                attendanceChartInstance.destroy();
            }
            
            attendanceChartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: last7Days,
                    datasets: [{
                        label: 'Attendance Rate (%)',
                        data: attendanceRates,
                        borderColor: '#4cc9f0',
                        backgroundColor: 'rgba(76, 201, 240, 0.1)',
                        borderWidth: 3,
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            grid: {
                                color: 'rgba(255, 255, 255, 0.05)'
                            },
                            ticks: {
                                color: '#8d99ae',
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#8d99ae'
                            }
                        }
                    }
                }
            });
        }

        // Initialize pie chart
        function initPieChart() {
            const ctx = document.getElementById('attendancePie').getContext('2d');
            
            // Calculate attendance status distribution
            let presentCount = 0;
            let exitedCount = 0;
            let absentCount = 0;
            
            dashboardData.attendance.forEach(record => {
                switch(record.status) {
                    case 'present':
                        presentCount++;
                        break;
                    case 'exited':
                        exitedCount++;
                        break;
                    case 'absent':
                        absentCount++;
                        break;
                }
            });
            
            // If no attendance data, show placeholder
            if (presentCount + exitedCount + absentCount === 0) {
                presentCount = 1;
                absentCount = 1;
            }
            
            if (pieChartInstance) {
                pieChartInstance.destroy();
            }
            
            pieChartInstance = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Present', 'Exited', 'Absent'],
                    datasets: [{
                        data: [presentCount, exitedCount, absentCount],
                        backgroundColor: [
                            '#4cc9f0',
                            '#4361ee', 
                            '#f72585'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#f8f9fa',
                                padding: 20,
                                font: {
                                    size: 14
                                }
                            }
                        }
                    },
                    cutout: '65%'
                }
            });
        }

        // Setup event listeners
        function setupEventListeners() {
            // Modal handling
            createSessionBtn.addEventListener('click', () => {
                window.location.href = 'session.php';
            });
            
            closeModalBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    sessionModal.style.display = 'none';
                });
            });
            
            window.addEventListener('click', (e) => {
                if (e.target === sessionModal) {
                    sessionModal.style.display = 'none';
                }
            });
            
            // Session search
            sessionSearch.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                filterSessions(searchTerm);
            });
            
            // Time range change for chart
            document.getElementById('timeRange').addEventListener('change', function() {
                // Re-initialize chart with new time range
                initAttendanceChart();
            });
        }

        // Filter sessions based on search term
        function filterSessions(searchTerm) {
            if (searchTerm === '') {
                renderRecentSessions();
                return;
            }
            
            const filteredSessions = dashboardData.sessions.filter(session => 
                (session.session_title && session.session_title.toLowerCase().includes(searchTerm)) ||
                (session.course && session.course.toLowerCase().includes(searchTerm)) ||
                (session.location && session.location.toLowerCase().includes(searchTerm))
            );
            
            if (filteredSessions.length === 0) {
                sessionsBody.innerHTML = `
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 30px; color: var(--gray);">
                            <i class="fas fa-search" style="font-size: 24px; margin-bottom: 10px; display: block;"></i>
                            No sessions found matching "${searchTerm}"
                        </td>
                    </tr>
                `;
                return;
            }
            
            sessionsBody.innerHTML = '';
            
            filteredSessions.slice(0, 10).forEach(session => {
                const row = document.createElement('tr');
                
                const sessionDate = session.start_time ? new Date(session.start_time) : null;
                const formattedDate = sessionDate ? sessionDate.toLocaleDateString('en-US', {
                    month: 'short',
                    day: 'numeric',
                    year: 'numeric',
                    hour: 'numeric',
                    minute: '2-digit'
                }) : 'Not scheduled';
                
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
                
                row.innerHTML = `
                    <td>${session.course || 'N/A'}</td>
                    <td>${session.session_title || 'Untitled'}</td>
                    <td>${formattedDate}</td>
                    <td>${session.location || 'TBD'}</td>
                    <td><span class="status ${statusClass}">${statusText}</span></td>
                    <td>
                        <button class="action-btn edit-session" data-id="${session.session_id}" title="Edit"><i class="fas fa-edit"></i></button>
                        <button class="action-btn view-session" data-id="${session.session_id}" title="View Details"><i class="fas fa-eye"></i></button>
                    </td>
                `;
                
                sessionsBody.appendChild(row);
            });
            
            addActionListeners();
        }

        // Refresh dashboard data
        async function refreshDashboard() {
            document.getElementById('totalSessions').textContent = 'Loading...';
            document.getElementById('totalParticipants').textContent = 'Loading...';
            document.getElementById('attendanceRate').textContent = 'Loading...';
            document.getElementById('activeSessions').textContent = 'Loading...';
            
            await loadDashboardData();
            updateDashboardStats();
            renderRecentSessions();
            initCharts();
        }

        // Auto-refresh dashboard every 30 seconds
        setInterval(() => {
            if (document.visibilityState === 'visible') {
                refreshDashboard();
            }
        }, 30000);
    </script>
</body>
</html>