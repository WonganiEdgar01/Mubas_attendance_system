<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MUBAS | System Reports & Analytics</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="js/supabase-auth.js"></script>
    <link rel="stylesheet" href="style/reporty.css">
    <!-- Export libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/docx@8.4.0/build/index.umd.min.js"></script>
    <style>
        /* Enhanced styling for professional reports */
        .report-analytics {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .analytics-chart {
            background: var(--card-bg);
            border-radius: 15px;
            padding: 20px;
            border: 1px solid var(--border-color);
        }
        
        .chart-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .chart-container {
            height: 300px;
            position: relative;
        }
        
        .report-filters {
            background: var(--card-bg);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            border: 1px solid var(--border-color);
        }
        
        .filter-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            align-items: end;
        }
        
        .data-table {
            background: var(--card-bg);
            border-radius: 15px;
            overflow: hidden;
            border: 1px solid var(--border-color);
            margin-bottom: 30px;
        }
        
        .data-table table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .data-table th {
            background: var(--primary-gradient);
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }
        
        .data-table td {
            padding: 12px 15px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .data-table tbody tr:hover {
            background: rgba(76, 201, 240, 0.05);
        }
        
        .export-options {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        
        .metric-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            padding: 20px;
            color: white;
            text-align: center;
        }
        
        .metric-value {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .metric-label {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 10000;
            display: none;
        }
        
        .loading-content {
            background: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
        }
        
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #4cc9f0;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 15px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-content">
            <div class="spinner"></div>
            <h3>Generating Report...</h3>
            <p>Please wait while we process your data</p>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <h1><i class="image"><img src="mubas-logo-full.png"></i> MUBAS-<span>ATTEND</span></h1>
        </div>
        <ul class="nav-links">
            <li><a href="index.php"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
            <li><a href="session.php"><i class="fas fa-calendar-alt"></i> <span>Sessions</span></a></li>
            <li><a href="participants.php"><i class="fas fa-users"></i> <span>Participants</span></a></li>
            <li><a href="register.php"><i class="fas fa-user"></i> <span>Registration</span></a></li>
            <li><a href="reporty.php" class="active"><i class="fas fa-chart-bar"></i> <span>Reports</span></a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <h2><i class="fas fa-chart-line"></i> System Reports & Analytics</h2>
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

        <!-- Report Filters -->
        <div class="report-filters">
            <h3 class="filter-title"><i class="fas fa-filter"></i> Report Configuration</h3>
            <div class="filter-row">
                <div class="form-group">
                    <label for="reportType">Report Type</label>
                    <select id="reportType">
                        <option value="attendance_summary">Attendance Summary</option>
                        <option value="session_analysis">Session Analysis</option>
                        <option value="session_attendance">Session Attendance Details</option>
                        <option value="student_performance">Student Performance</option>
                        <option value="course_statistics">Course Statistics</option>
                        <option value="system_logs">System Activity Logs</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="dateRange">Date Range</label>
                    <select id="dateRange">
                        <option value="7">Last 7 Days</option>
                        <option value="30" selected>Last 30 Days</option>
                        <option value="90">Last 90 Days</option>
                        <option value="semester">Current Semester</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="courseFilter">Course Filter</label>
                    <select id="courseFilter">
                        <option value="">All Courses</option>
                        <!-- Populated dynamically -->
                    </select>
                </div>
                
                <div class="form-group">
                    <label>&nbsp;</label>
                    <button class="btn btn-primary" id="generateReportBtn">
                        <i class="fas fa-chart-bar"></i> Generate Report
                    </button>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-cards">
            <div class="stat-card stat-1">
                <div class="stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-value" id="totalSessions">0</div>
                <div class="stat-label">Total Sessions</div>
            </div>
            
            <div class="stat-card stat-2">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-value" id="totalStudents">0</div>
                <div class="stat-label">Registered Students</div>
            </div>
            
            <div class="stat-card stat-3">
                <div class="stat-icon">
                    <i class="fas fa-percentage"></i>
                </div>
                <div class="stat-value" id="avgAttendanceRate">0%</div>
                <div class="stat-label">Average Attendance</div>
            </div>
            
            <div class="stat-card stat-4">
                <div class="stat-icon">
                    <i class="fas fa-trophy"></i>
                </div>
                <div class="stat-value" id="bestPerformingCourse">N/A</div>
                <div class="stat-label">Best Course</div>
            </div>
        </div>

        <!-- Report Analytics Charts -->
        <div class="report-analytics">
            <div class="analytics-chart">
                <h3 class="chart-title">
                    <i class="fas fa-chart-line"></i>
                    Attendance Trends
                </h3>
                <div class="chart-container">
                    <canvas id="attendanceTrendChart"></canvas>
                </div>
            </div>
            
            <div class="analytics-chart">
                <h3 class="chart-title">
                    <i class="fas fa-chart-pie"></i>
                    Attendance Distribution
                </h3>
                <div class="chart-container">
                    <canvas id="attendanceDistributionChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Report Data Table -->
        <div class="data-table" id="reportTableContainer">
            <div style="padding: 20px;">
                <h3 style="margin-bottom: 15px; color: var(--primary-color);">
                    <i class="fas fa-table"></i> Report Data
                </h3>
                <div style="text-align: center; padding: 40px; color: var(--gray);">
                    <i class="fas fa-chart-bar" style="font-size: 48px; margin-bottom: 15px; display: block;"></i>
                    <p>Click "Generate Report" to view detailed analytics</p>
                </div>
            </div>
        </div>

        <!-- Recent System Logs -->
        <div class="system-logs">
            <div class="logs-header">
                <h3 class="section-title"><i class="fas fa-clipboard-list"></i> Recent System Activity</h3>
                <div class="logs-filter">
                    <select class="btn-sm" id="logTypeFilter">
                        <option value="">All Activities</option>
                        <option value="session">Session Events</option>
                        <option value="attendance">Attendance Events</option>
                        <option value="registration">Registration Events</option>
                        <option value="system">System Events</option>
                    </select>
                    <button class="btn btn-outline btn-sm" id="exportLogsBtn">
                        <i class="fas fa-download"></i> Export Logs
                    </button>
                </div>
            </div>
            
            <table class="logs-table">
                <thead>
                    <tr>
                        <th>Timestamp <i class="fas fa-sort"></i></th>
                        <th>Event Type</th>
                        <th>Description</th>
                        <th>User/Entity</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="systemLogsTable">
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 30px;">
                            <i class="fas fa-spinner fa-spin" style="font-size: 24px; margin-bottom: 10px; display: block;"></i>
                            Loading system activities...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Global variables
        let reportData = {
            sessions: [],
            students: [],
            attendance: [],
            courses: []
        };
        
        let attendanceTrendChart = null;
        let attendanceDistributionChart = null;
        let lastGeneratedData = null;
        let sessionAttendanceState = {
            sessionId: '',
            status: '',
            query: ''
        };

        // Initialize the application
        document.addEventListener('DOMContentLoaded', async function() {
            // Initialize authentication
            const isAuthenticated = await SupabaseAuth.initAuth();
            if (!isAuthenticated) return;
            
            // Load initial data
            await loadReportData();
            
            // Setup event listeners
            setupEventListeners();
            
            // Initialize default view
            updateStatistics();
            populateCourseFilter();
            loadSystemLogs();
        });

        // Load data from Supabase
        async function loadReportData() {
            try {
                const userData = SupabaseAuth.getCurrentUser();
                if (!userData) throw new Error('User not authenticated');

                showLoading('Loading report data...');

                // Load sessions
                const { data: sessions, error: sessionsError } = await SupabaseAuth.supabase
                    .from('sessions')
                    .select('*')
                    .eq('lecturer_id', userData.id)
                    .order('created_at', { ascending: false });

                if (sessionsError) throw sessionsError;
                reportData.sessions = sessions || [];

                // Load students
                const { data: students, error: studentsError } = await SupabaseAuth.supabase
                    .from('students')
                    .select('*')
                    .order('created_at', { ascending: false });

                if (studentsError) throw studentsError;
                reportData.students = students || [];

                // Load attendance data
                const { data: attendance, error: attendanceError } = await SupabaseAuth.supabase
                    .from('attendance')
                    .select('*')
                    .order('created_at', { ascending: false });

                if (attendanceError) {
                    console.warn('Attendance data not available:', attendanceError);
                    reportData.attendance = [];
                } else {
                    // Restrict attendance to only the sessions owned by this lecturer
                    const sessionIds = new Set((reportData.sessions || []).map(s => s.session_id));
                    reportData.attendance = (attendance || []).filter(a => sessionIds.has(a.session_id));
                }

                // Extract unique courses
                reportData.courses = [...new Set(reportData.sessions.map(s => s.course).filter(Boolean))];

                console.log('Report data loaded:', {
                    sessions: reportData.sessions.length,
                    students: reportData.students.length,
                    attendance: reportData.attendance.length,
                    courses: reportData.courses.length
                });

                hideLoading();

            } catch (error) {
                console.error('Failed to load report data:', error);
                hideLoading();
                
                Swal.fire({
                    icon: 'error',
                    title: 'Data Loading Failed',
                    text: 'Unable to load report data: ' + error.message,
                    timer: 5000
                });
            }
        }

        // Setup event listeners
        function setupEventListeners() {
            document.getElementById('generateReportBtn').addEventListener('click', generateReport);
            document.getElementById('exportLogsBtn').addEventListener('click', exportSystemLogs);
            document.getElementById('logTypeFilter').addEventListener('change', filterSystemLogs);
            
            // Auto-refresh every 5 minutes
            setInterval(loadSystemLogs, 300000);
        }

        // Update statistics cards
        function updateStatistics() {
            document.getElementById('totalSessions').textContent = reportData.sessions.length;
            document.getElementById('totalStudents').textContent = reportData.students.length;

            // Calculate average attendance rate
            let avgAttendance = 0;
            if (reportData.attendance.length > 0) {
                const presentCount = reportData.attendance.filter(a => 
                    a.status === 'present' || a.status === 'exited'
                ).length;
                avgAttendance = Math.round((presentCount / reportData.attendance.length) * 100);
            }
            document.getElementById('avgAttendanceRate').textContent = avgAttendance + '%';

            // Find best performing course
            let bestCourse = 'N/A';
            if (reportData.courses.length > 0) {
                const courseAttendance = {};
                reportData.attendance.forEach(record => {
                    if (!courseAttendance[record.course]) {
                        courseAttendance[record.course] = { present: 0, total: 0 };
                    }
                    courseAttendance[record.course].total++;
                    if (record.status === 'present' || record.status === 'exited') {
                        courseAttendance[record.course].present++;
                    }
                });

                let bestRate = 0;
                Object.keys(courseAttendance).forEach(course => {
                    const rate = courseAttendance[course].present / courseAttendance[course].total;
                    if (rate > bestRate) {
                        bestRate = rate;
                        bestCourse = course;
                    }
                });
            }
            document.getElementById('bestPerformingCourse').textContent = bestCourse;
        }

        // Populate course filter dropdown
        function populateCourseFilter() {
            const courseFilter = document.getElementById('courseFilter');
            courseFilter.innerHTML = '<option value="">All Courses</option>';
            
            reportData.courses.forEach(course => {
                const option = document.createElement('option');
                option.value = course;
                option.textContent = course;
                courseFilter.appendChild(option);
            });
        }

        // Generate report based on selected filters
        async function generateReport() {
            const reportType = document.getElementById('reportType').value;
            const dateRange = document.getElementById('dateRange').value;
            const courseFilter = document.getElementById('courseFilter').value;

            showLoading('Generating report...');

            try {
                // Filter data based on selected criteria
                const filteredData = filterReportData(dateRange, courseFilter);
                lastGeneratedData = filteredData;

                // Generate report based on type
                switch (reportType) {
                    case 'attendance_summary':
                        generateAttendanceSummary(filteredData);
                        break;
                    case 'session_analysis':
                        generateSessionAnalysis(filteredData);
                        break;
                    case 'session_attendance':
                        generateSessionAttendanceDetails(filteredData);
                        break;
                    case 'student_performance':
                        generateStudentPerformance(filteredData);
                        break;
                    case 'course_statistics':
                        generateCourseStatistics(filteredData);
                        break;
                    case 'system_logs':
                        generateSystemLogsReport(filteredData);
                        break;
                    default:
                        generateAttendanceSummary(filteredData);
                }

                // Update charts
                updateCharts(filteredData);

                hideLoading();

            } catch (error) {
                console.error('Report generation failed:', error);
                hideLoading();
                
                Swal.fire({
                    icon: 'error',
                    title: 'Report Generation Failed',
                    text: 'Unable to generate report: ' + error.message
                });
            }
        }

        // Filter data based on criteria
        function filterReportData(dateRange, courseFilter) {
            const now = new Date();
            let startDate = new Date();
            
            // Calculate date range
            switch(dateRange) {
                case '7':
                    startDate.setDate(now.getDate() - 7);
                    break;
                case '30':
                    startDate.setDate(now.getDate() - 30);
                    break;
                case '90':
                    startDate.setDate(now.getDate() - 90);
                    break;
                case 'semester':
                    startDate.setMonth(now.getMonth() - 6);
                    break;
            }

            const filtered = {
                sessions: reportData.sessions.filter(s => {
                    const sessionDate = new Date(s.created_at);
                    const inDateRange = sessionDate >= startDate;
                    const inCourseFilter = !courseFilter || s.course === courseFilter;
                    return inDateRange && inCourseFilter;
                }),
                attendance: reportData.attendance.filter(a => {
                    const attendanceDate = new Date(a.created_at);
                    const inDateRange = attendanceDate >= startDate;
                    const inCourseFilter = !courseFilter || a.course === courseFilter;
                    return inDateRange && inCourseFilter;
                }),
                students: reportData.students
            };

            return filtered;
        }

        // Generate attendance summary report
        function generateAttendanceSummary(data) {
            const tableContainer = document.getElementById('reportTableContainer');
            
            let reportHtml = `
                <div style="padding: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h3 style="color: var(--primary-color); margin: 0;">
                            <i class="fas fa-users"></i> Attendance Summary Report
                        </h3>
                        <div class="export-options">
                            <button class="btn btn-outline btn-sm" onclick="exportToPDF()">
                                <i class="fas fa-file-pdf"></i> PDF
                            </button>
                            <button class="btn btn-primary btn-sm" onclick="exportToWord()">
                                <i class="fas fa-file-word"></i> Word
                            </button>
                            <button class="btn btn-success btn-sm" onclick="exportToExcel()">
                                <i class="fas fa-file-excel"></i> Excel
                            </button>
                        </div>
                    </div>
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: var(--primary-gradient); color: white;">
                                <th style="padding: 12px; text-align: left;">Session</th>
                                <th style="padding: 12px; text-align: left;">Course</th>
                                <th style="padding: 12px; text-align: left;">Date</th>
                                <th style="padding: 12px; text-align: center;">Present</th>
                                <th style="padding: 12px; text-align: center;">Exited</th>
                                <th style="padding: 12px; text-align: center;">Absent</th>
                                <th style="padding: 12px; text-align: center;">Attendance Rate</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            if (data.sessions.length === 0) {
                reportHtml += `
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px; color: var(--gray);">
                            No sessions found for the selected criteria
                        </td>
                    </tr>
                `;
            } else {
                data.sessions.forEach(session => {
                    const sessionAttendance = data.attendance.filter(a => a.session_id === session.session_id);
                    const present = sessionAttendance.filter(a => a.status === 'present').length;
                    const exited = sessionAttendance.filter(a => a.status === 'exited').length;
                    const absent = sessionAttendance.filter(a => a.status === 'absent').length;
                    const total = sessionAttendance.length;
                    const rate = total > 0 ? Math.round(((present + exited) / total) * 100) : 0;

                    const sessionDate = new Date(session.start_time || session.created_at);
                    const formattedDate = sessionDate.toLocaleDateString();

                    reportHtml += `
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 12px;">${session.session_title || 'Untitled'}</td>
                            <td style="padding: 12px;">${session.course || 'N/A'}</td>
                            <td style="padding: 12px;">${formattedDate}</td>
                            <td style="padding: 12px; text-align: center; color: #4cc9f0;">${present}</td>
                            <td style="padding: 12px; text-align: center; color: #ffd60a;">${exited}</td>
                            <td style="padding: 12px; text-align: center; color: #f72585;">${absent}</td>
                            <td style="padding: 12px; text-align: center; font-weight: 600;">${rate}%</td>
                        </tr>
                    `;
                });
            }

            reportHtml += `
                        </tbody>
                    </table>
                </div>
            `;

            tableContainer.innerHTML = reportHtml;
        }

        // Session Attendance Details report
        function generateSessionAttendanceDetails(data) {
            const tableContainer = document.getElementById('reportTableContainer');

            // Ensure default session selection
            if (!sessionAttendanceState.sessionId) {
                sessionAttendanceState.sessionId = data.sessions[0]?.session_id || '';
            }

            const buildFiltersHtml = () => {
                const sessionsOptions = data.sessions.map(s => {
                    const d = new Date(s.start_time || s.created_at).toLocaleString();
                    const title = `${s.session_title || 'Untitled'} — ${s.course || 'N/A'} (${d})`;
                    const selected = String(sessionAttendanceState.sessionId) === String(s.session_id) ? 'selected' : '';
                    return `<option value="${s.session_id}" ${selected}>${title}</option>`;
                }).join('');
                const statusOptions = [
                    {v:'', t:'All Statuses'},
                    {v:'present', t:'Present'},
                    {v:'exited', t:'Exited'},
                    {v:'absent', t:'Absent'}
                ].map(o => `<option value="${o.v}" ${sessionAttendanceState.status===o.v?'selected':''}>${o.t}</option>`).join('');

                return `
                    <div style="display:flex; gap:12px; flex-wrap:wrap; align-items:end; margin-bottom: 16px;">
                        <div class="form-group" style="min-width:260px;">
                            <label>Session</label>
                            <select id="sessionAttendanceSession">${sessionsOptions}</select>
                        </div>
                        <div class="form-group" style="min-width:160px;">
                            <label>Status</label>
                            <select id="sessionAttendanceStatus">${statusOptions}</select>
                        </div>
                        <div class="form-group" style="min-width:220px;">
                            <label>Search Student</label>
                            <input id="sessionAttendanceQuery" type="text" placeholder="ID or name" value="${sessionAttendanceState.query || ''}"/>
                        </div>
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button class="btn btn-primary" id="sessionAttendanceApply"><i class="fas fa-filter"></i> Apply</button>
                        </div>
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button class="btn btn-outline" id="sessionAttendanceReset"><i class="fas fa-rotate"></i> Reset</button>
                        </div>
                        <div class="export-options" style="margin-left:auto;">
                            <button class="btn btn-outline btn-sm" onclick="exportToPDF()"><i class="fas fa-file-pdf"></i> PDF</button>
                            <button class="btn btn-primary btn-sm" onclick="exportToWord()"><i class="fas fa-file-word"></i> Word</button>
                            <button class="btn btn-success btn-sm" onclick="exportToExcel()"><i class="fas fa-file-excel"></i> Excel</button>
                        </div>
                    </div>
                `;
            };

            const buildTableHtml = (rows) => {
                return `
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: var(--primary-gradient); color: white;">
                                <th style="padding:12px; text-align:left;">Student ID</th>
                                <th style="padding:12px; text-align:left;">Name</th>
                                <th style="padding:12px; text-align:left;">Program</th>
                                <th style="padding:12px; text-align:left;">Course</th>
                                <th style="padding:12px; text-align:left;">Timestamp</th>
                                <th style="padding:12px; text-align:center;">Status</th>
                                <th style="padding:12px; text-align:left;">Entry Method</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${rows.map(r => `
                                <tr style=\"border-bottom:1px solid var(--border-color);\">`
                                + `
                                    <td style=\"padding:12px;\">${r.student_id || ''}</td>
                                    <td style=\"padding:12px;\">${r.full_name || ''}</td>
                                    <td style=\"padding:12px;\">${r.program || ''}</td>
                                    <td style=\"padding:12px;\">${r.course || ''}</td>
                                    <td style=\"padding:12px;\">${r.created_at ? new Date(r.created_at).toLocaleString() : ''}</td>
                                    <td style=\"padding:12px; text-align:center;\">${r.status || ''}</td>
                                    <td style=\"padding:12px;\">${r.entry_method || ''}</td>
                                `
                                + `</tr>
                            `).join('')}
                            ${rows.length === 0 ? `
                                <tr><td colspan=\"7\" style=\"text-align:center; padding:30px; color: var(--gray);\">No attendance found for the selected filters</td></tr>
                            ` : ''}
                        </tbody>
                    </table>
                `;
            };

            // Enrich attendance with student details if missing
            const studentMap = {};
            (data.students || []).forEach(st => { studentMap[st.student_id] = st; });

            const applyFilters = () => {
                const sid = sessionAttendanceState.sessionId;
                const st = (sessionAttendanceState.status || '').toLowerCase();
                const q = (sessionAttendanceState.query || '').toLowerCase();

                let rows = (data.attendance || []).filter(a => String(a.session_id) === String(sid));
                if (st) rows = rows.filter(a => String(a.status).toLowerCase() === st);
                if (q) rows = rows.filter(a => {
                    const s = studentMap[a.student_id] || {};
                    const name = (a.full_name || s.full_name || '').toLowerCase();
                    return String(a.student_id).toLowerCase().includes(q) || name.includes(q);
                });

                // fill missing columns from students table
                rows = rows.map(a => {
                    const s = studentMap[a.student_id] || {};
                    return {
                        ...a,
                        full_name: a.full_name || s.full_name || '',
                        program: a.program || s.program || ''
                    };
                });

                return rows;
            };

            const render = () => {
                const rows = applyFilters();
                const header = `
                    <div style=\"display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;\">`
                    + `
                        <h3 style=\"color: var(--primary-color); margin:0;\">`
                    + `
                            <i class=\"fas fa-list-check\"></i> Session Attendance Details
                        </h3>
                    </div>
                `;
                tableContainer.innerHTML = `
                    <div style=\"padding:20px;\">`
                    + `${header}`
                    + `${buildFiltersHtml()}`
                    + `${buildTableHtml(rows)}`
                    + `</div>`;

                // Attach listeners after render
                const sel = document.getElementById('sessionAttendanceSession');
                const stat = document.getElementById('sessionAttendanceStatus');
                const q = document.getElementById('sessionAttendanceQuery');
                const apply = document.getElementById('sessionAttendanceApply');
                const reset = document.getElementById('sessionAttendanceReset');

                const doApply = () => {
                    sessionAttendanceState.sessionId = sel.value;
                    sessionAttendanceState.status = stat.value;
                    sessionAttendanceState.query = q.value;
                    render();
                };
                apply.addEventListener('click', doApply);
                reset.addEventListener('click', () => {
                    sessionAttendanceState = { sessionId: data.sessions[0]?.session_id || '', status: '', query: '' };
                    render();
                });
                sel.addEventListener('change', doApply);
                stat.addEventListener('change', doApply);
                q.addEventListener('keyup', (e) => { if (e.key === 'Enter') doApply(); });
            };

            render();
        }

        // Update charts with filtered data
        function updateCharts(data) {
            updateAttendanceTrendChart(data);
            updateAttendanceDistributionChart(data);
        }

        // Update attendance trend chart
        function updateAttendanceTrendChart(data) {
            const ctx = document.getElementById('attendanceTrendChart').getContext('2d');
            
            // Process data for last 7 days
            const last7Days = [];
            const attendanceRates = [];
            
            for (let i = 6; i >= 0; i--) {
                const date = new Date();
                date.setDate(date.getDate() - i);
                const dateStr = date.toDateString();
                
                last7Days.push(date.toLocaleDateString('en-US', { weekday: 'short' }));
                
                const dayAttendance = data.attendance.filter(record => {
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
            
            if (attendanceTrendChart) {
                attendanceTrendChart.destroy();
            }
            
            attendanceTrendChart = new Chart(ctx, {
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
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        }
                    }
                }
            });
        }

        // Update attendance distribution chart
        function updateAttendanceDistributionChart(data) {
            const ctx = document.getElementById('attendanceDistributionChart').getContext('2d');
            
            const present = data.attendance.filter(a => a.status === 'present').length;
            const exited = data.attendance.filter(a => a.status === 'exited').length;
            const absent = data.attendance.filter(a => a.status === 'absent').length;
            
            if (attendanceDistributionChart) {
                attendanceDistributionChart.destroy();
            }
            
            attendanceDistributionChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Present', 'Exited', 'Absent'],
                    datasets: [{
                        data: [present, exited, absent],
                        backgroundColor: ['#4cc9f0', '#ffd60a', '#f72585'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    },
                    cutout: '65%'
                }
            });
        }

        // Load system logs
        async function loadSystemLogs() {
            try {
                // Generate sample system logs based on actual data
                const logs = generateSystemLogs();
                renderSystemLogs(logs);
            } catch (error) {
                console.error('Failed to load system logs:', error);
            }
        }

        // Generate system logs from database activities
        function generateSystemLogs() {
            const logs = [];
            
            // Add session-related logs
            reportData.sessions.slice(0, 10).forEach(session => {
                logs.push({
                    timestamp: new Date(session.created_at),
                    type: 'session',
                    description: `Session "${session.session_title}" created for ${session.course}`,
                    user: 'Lecturer',
                    status: 'success'
                });
                
                if (session.status === 'active') {
                    logs.push({
                        timestamp: new Date(session.updated_at || session.created_at),
                        type: 'session',
                        description: `Session "${session.session_title}" started`,
                        user: 'System',
                        status: 'info'
                    });
                }
            });

            // Add attendance logs
            reportData.attendance.slice(0, 15).forEach(record => {
                logs.push({
                    timestamp: new Date(record.created_at),
                    type: 'attendance',
                    description: `Student ${record.student_id} marked ${record.status} for ${record.course}`,
                    user: record.entry_method || 'Scanner',
                    status: record.status === 'present' ? 'success' : 'warning'
                });
            });

            // Add student registration logs
            reportData.students.slice(0, 5).forEach(student => {
                logs.push({
                    timestamp: new Date(student.created_at),
                    type: 'registration',
                    description: `New student registered: ${student.student_id} - ${student.full_name}`,
                    user: 'Admin',
                    status: 'success'
                });
            });

            // Sort by timestamp (most recent first)
            return logs.sort((a, b) => b.timestamp - a.timestamp).slice(0, 20);
        }

        // Generate ALL system logs (no truncation) for exports
        function generateAllSystemLogs() {
            const logs = [];
            // Session-related logs
            reportData.sessions.forEach(session => {
                logs.push({
                    timestamp: new Date(session.created_at),
                    type: 'session',
                    description: `Session "${session.session_title}" created for ${session.course}`,
                    user: 'Lecturer',
                    status: 'success'
                });
                if (session.status === 'active') {
                    logs.push({
                        timestamp: new Date(session.updated_at || session.created_at),
                        type: 'session',
                        description: `Session "${session.session_title}" started`,
                        user: 'System',
                        status: 'info'
                    });
                }
            });
            // Attendance logs
            reportData.attendance.forEach(record => {
                logs.push({
                    timestamp: new Date(record.created_at),
                    type: 'attendance',
                    description: `Student ${record.student_id} marked ${record.status} for ${record.course}`,
                    user: record.entry_method || 'Scanner',
                    status: record.status === 'present' ? 'success' : 'warning'
                });
            });
            // Registration logs
            reportData.students.forEach(student => {
                logs.push({
                    timestamp: new Date(student.created_at),
                    type: 'registration',
                    description: `New student registered: ${student.student_id} - ${student.full_name}`,
                    user: 'Admin',
                    status: 'success'
                });
            });
            return logs.sort((a, b) => b.timestamp - a.timestamp);
        }

        // Render system logs in table
        function renderSystemLogs(logs) {
            const tbody = document.getElementById('systemLogsTable');
            
            if (logs.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 30px;">
                            No system activities found
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = '';
            
            logs.forEach(log => {
                const row = document.createElement('tr');
                const statusClass = log.status === 'success' ? 'type-success' : 
                                  log.status === 'warning' ? 'type-warning' : 
                                  log.status === 'error' ? 'type-error' : 'type-info';
                
                row.innerHTML = `
                    <td>${log.timestamp.toLocaleString()}</td>
                    <td><span class="log-type ${statusClass}">${log.type}</span></td>
                    <td>${log.description}</td>
                    <td>${log.user}</td>
                    <td><span class="log-type ${statusClass}">${log.status}</span></td>
                `;
                
                tbody.appendChild(row);
            });
        }

        // Filter system logs
        function filterSystemLogs() {
            const filterValue = document.getElementById('logTypeFilter').value;
            const logs = generateSystemLogs();
            
            const filteredLogs = filterValue ? 
                logs.filter(log => log.type === filterValue) : logs;
            
            renderSystemLogs(filteredLogs);
        }

        // Generate other report types
        function generateSessionAnalysis(data) {
            const tableContainer = document.getElementById('reportTableContainer');
            
            let reportHtml = `
                <div style="padding: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h3 style="color: var(--primary-color); margin: 0;">
                            <i class="fas fa-calendar-alt"></i> Session Analysis Report
                        </h3>
                        <div class="export-options">
                            <button class="btn btn-outline btn-sm" onclick="exportToPDF()">
                                <i class="fas fa-file-pdf"></i> PDF
                            </button>
                            <button class="btn btn-primary btn-sm" onclick="exportToWord()">
                                <i class="fas fa-file-word"></i> Word
                            </button>
                            <button class="btn btn-success btn-sm" onclick="exportToExcel()">
                                <i class="fas fa-file-excel"></i> Excel
                            </button>
                        </div>
                    </div>
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: var(--primary-gradient); color: white;">
                                <th style="padding: 12px; text-align: left;">Session</th>
                                <th style="padding: 12px; text-align: left;">Course</th>
                                <th style="padding: 12px; text-align: left;">Type</th>
                                <th style="padding: 12px; text-align: left;">Duration</th>
                                <th style="padding: 12px; text-align: center;">Status</th>
                                <th style="padding: 12px; text-align: center;">Participants</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            data.sessions.forEach(session => {
                const sessionAttendance = data.attendance.filter(a => a.session_id === session.session_id);
                const participants = sessionAttendance.length;
                
                const startTime = session.start_time ? new Date(session.start_time) : null;
                const endTime = session.end_time ? new Date(session.end_time) : null;
                const duration = startTime && endTime ? 
                    Math.round((endTime - startTime) / (1000 * 60)) + ' minutes' : 'N/A';

                reportHtml += `
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 12px;">${session.session_title || 'Untitled'}</td>
                        <td style="padding: 12px;">${session.course || 'N/A'}</td>
                        <td style="padding: 12px;">${session.session_type || 'N/A'}</td>
                        <td style="padding: 12px;">${duration}</td>
                        <td style="padding: 12px; text-align: center;">
                            <span class="status ${session.status || 'scheduled'}">${session.status || 'scheduled'}</span>
                        </td>
                        <td style="padding: 12px; text-align: center;">${participants}</td>
                    </tr>
                `;
            });

            reportHtml += `
                        </tbody>
                    </table>
                </div>
            `;

            tableContainer.innerHTML = reportHtml;
        }

        function generateStudentPerformance(data) {
            const tableContainer = document.getElementById('reportTableContainer');
            
            // Calculate student performance metrics
            const studentPerformance = {};
            
            data.attendance.forEach(record => {
                if (!studentPerformance[record.student_id]) {
                    studentPerformance[record.student_id] = {
                        name: record.full_name,
                        program: record.program,
                        present: 0,
                        exited: 0,
                        absent: 0,
                        total: 0
                    };
                }
                
                studentPerformance[record.student_id].total++;
                studentPerformance[record.student_id][record.status]++;
            });

            let reportHtml = `
                <div style="padding: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h3 style="color: var(--primary-color); margin: 0;">
                            <i class="fas fa-user-graduate"></i> Student Performance Report
                        </h3>
                        <div class="export-options">
                            <button class="btn btn-outline btn-sm" onclick="exportToPDF()">
                                <i class="fas fa-file-pdf"></i> PDF
                            </button>
                            <button class="btn btn-primary btn-sm" onclick="exportToWord()">
                                <i class="fas fa-file-word"></i> Word
                            </button>
                            <button class="btn btn-success btn-sm" onclick="exportToExcel()">
                                <i class="fas fa-file-excel"></i> Excel
                            </button>
                        </div>
                    </div>
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: var(--primary-gradient); color: white;">
                                <th style="padding: 12px; text-align: left;">Student ID</th>
                                <th style="padding: 12px; text-align: left;">Name</th>
                                <th style="padding: 12px; text-align: left;">Program</th>
                                <th style="padding: 12px; text-align: center;">Sessions</th>
                                <th style="padding: 12px; text-align: center;">Present</th>
                                <th style="padding: 12px; text-align: center;">Attendance Rate</th>
                                <th style="padding: 12px; text-align: center;">Performance</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            Object.keys(studentPerformance).forEach(studentId => {
                const student = studentPerformance[studentId];
                const attendanceRate = student.total > 0 ? 
                    Math.round(((student.present + student.exited) / student.total) * 100) : 0;
                
                const performance = attendanceRate >= 90 ? 'Excellent' :
                                  attendanceRate >= 75 ? 'Good' :
                                  attendanceRate >= 60 ? 'Average' : 'Poor';
                
                const performanceColor = attendanceRate >= 90 ? '#4cc9f0' :
                                       attendanceRate >= 75 ? '#4361ee' :
                                       attendanceRate >= 60 ? '#ffd60a' : '#f72585';

                reportHtml += `
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 12px;">${studentId}</td>
                        <td style="padding: 12px;">${student.name}</td>
                        <td style="padding: 12px;">${student.program}</td>
                        <td style="padding: 12px; text-align: center;">${student.total}</td>
                        <td style="padding: 12px; text-align: center;">${student.present + student.exited}</td>
                        <td style="padding: 12px; text-align: center; font-weight: 600;">${attendanceRate}%</td>
                        <td style="padding: 12px; text-align: center; color: ${performanceColor}; font-weight: 600;">${performance}</td>
                    </tr>
                `;
            });

            reportHtml += `
                        </tbody>
                    </table>
                </div>
            `;

            tableContainer.innerHTML = reportHtml;
        }

        function generateCourseStatistics(data) {
            const tableContainer = document.getElementById('reportTableContainer');
            
            // Calculate course statistics
            const courseStats = {};
            
            data.attendance.forEach(record => {
                if (!courseStats[record.course]) {
                    courseStats[record.course] = {
                        sessions: new Set(),
                        totalAttendance: 0,
                        present: 0,
                        exited: 0,
                        absent: 0
                    };
                }
                
                courseStats[record.course].sessions.add(record.session_id);
                courseStats[record.course].totalAttendance++;
                courseStats[record.course][record.status]++;
            });

            let reportHtml = `
                <div style="padding: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h3 style="color: var(--primary-color); margin: 0;">
                            <i class="fas fa-book"></i> Course Statistics Report
                        </h3>
                        <div class="export-options">
                            <button class="btn btn-outline btn-sm" onclick="exportToPDF()">
                                <i class="fas fa-file-pdf"></i> PDF
                            </button>
                            <button class="btn btn-primary btn-sm" onclick="exportToWord()">
                                <i class="fas fa-file-word"></i> Word
                            </button>
                            <button class="btn btn-success btn-sm" onclick="exportToExcel()">
                                <i class="fas fa-file-excel"></i> Excel
                            </button>
                        </div>
                    </div>
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: var(--primary-gradient); color: white;">
                                <th style="padding: 12px; text-align: left;">Course</th>
                                <th style="padding: 12px; text-align: center;">Sessions</th>
                                <th style="padding: 12px; text-align: center;">Total Records</th>
                                <th style="padding: 12px; text-align: center;">Present</th>
                                <th style="padding: 12px; text-align: center;">Exited</th>
                                <th style="padding: 12px; text-align: center;">Absent</th>
                                <th style="padding: 12px; text-align: center;">Avg. Attendance</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            Object.keys(courseStats).forEach(course => {
                const stats = courseStats[course];
                const avgAttendance = stats.totalAttendance > 0 ? 
                    Math.round(((stats.present + stats.exited) / stats.totalAttendance) * 100) : 0;

                reportHtml += `
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 12px; font-weight: 600;">${course}</td>
                        <td style="padding: 12px; text-align: center;">${stats.sessions.size}</td>
                        <td style="padding: 12px; text-align: center;">${stats.totalAttendance}</td>
                        <td style="padding: 12px; text-align: center; color: #4cc9f0;">${stats.present}</td>
                        <td style="padding: 12px; text-align: center; color: #ffd60a;">${stats.exited}</td>
                        <td style="padding: 12px; text-align: center; color: #f72585;">${stats.absent}</td>
                        <td style="padding: 12px; text-align: center; font-weight: 600;">${avgAttendance}%</td>
                    </tr>
                `;
            });

            reportHtml += `
                        </tbody>
                    </table>
                </div>
            `;

            tableContainer.innerHTML = reportHtml;
        }

        function generateSystemLogsReport(data) {
            const tableContainer = document.getElementById('reportTableContainer');
            const logs = generateSystemLogs();

            let reportHtml = `
                <div style="padding: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h3 style="color: var(--primary-color); margin: 0;">
                            <i class="fas fa-clipboard-list"></i> System Activity Logs Report
                        </h3>
                        <div class="export-options">
                            <button class="btn btn-outline btn-sm" onclick="exportToPDF()">
                                <i class="fas fa-file-pdf"></i> PDF
                            </button>
                            <button class="btn btn-primary btn-sm" onclick="exportToWord()">
                                <i class="fas fa-file-word"></i> Word
                            </button>
                            <button class="btn btn-success btn-sm" onclick="exportToExcel()">
                                <i class="fas fa-file-excel"></i> Excel
                            </button>
                        </div>
                    </div>
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: var(--primary-gradient); color: white;">
                                <th style="padding: 12px; text-align: left;">Timestamp</th>
                                <th style="padding: 12px; text-align: left;">Event Type</th>
                                <th style="padding: 12px; text-align: left;">Description</th>
                                <th style="padding: 12px; text-align: left;">User/Entity</th>
                                <th style="padding: 12px; text-align: center;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            logs.forEach(log => {
                const statusColor = log.status === 'success' ? '#4cc9f0' :
                                  log.status === 'warning' ? '#ffd60a' :
                                  log.status === 'error' ? '#f72585' : '#8d99ae';

                reportHtml += `
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 12px;">${log.timestamp.toLocaleString()}</td>
                        <td style="padding: 12px;">${log.type}</td>
                        <td style="padding: 12px;">${log.description}</td>
                        <td style="padding: 12px;">${log.user}</td>
                        <td style="padding: 12px; text-align: center; color: ${statusColor}; font-weight: 600;">${log.status}</td>
                    </tr>
                `;
            });

            reportHtml += `
                        </tbody>
                    </table>
                </div>
            `;

            tableContainer.innerHTML = reportHtml;
        }

        // Export helpers
        function getCurrentReportTable() {
            const container = document.getElementById('reportTableContainer');
            if (!container) return null;
            // Prefer the first table inside the container
            const table = container.querySelector('table');
            return table;
        }

        function buildReportFileBaseName(prefix = 'report') {
            const type = (document.getElementById('reportType')?.value || 'report')
                .replace(/_/g, '-');
            const course = document.getElementById('courseFilter')?.value || 'all-courses';
            const sessionSel = document.getElementById('sessionAttendanceSession');
            const sessionPart = sessionSel && sessionSel.value ? `session-${sessionSel.value}` : '';
            const date = new Date();
            const stamp = `${date.getFullYear()}-${String(date.getMonth()+1).padStart(2,'0')}-${String(date.getDate()).padStart(2,'0')}_${String(date.getHours()).padStart(2,'0')}${String(date.getMinutes()).padStart(2,'0')}`;
            return `${prefix}-${type}-${course}${sessionPart?'-'+sessionPart:''}-${stamp}`;
        }

        function extractTableData(tableEl) {
            const data = [];
            const thead = tableEl.querySelector('thead');
            const tbody = tableEl.querySelector('tbody');
            if (thead) {
                const headerRow = Array.from(thead.querySelectorAll('tr'))[0];
                if (headerRow) {
                    const headers = Array.from(headerRow.querySelectorAll('th')).map(th => th.innerText.trim());
                    if (headers.length) data.push(headers);
                }
            }
            if (tbody) {
                Array.from(tbody.querySelectorAll('tr')).forEach(tr => {
                    const row = Array.from(tr.querySelectorAll('td, th')).map(td => td.innerText.trim());
                    if (row.length) data.push(row);
                });
            }
            return data;
        }

        function formatDateRangeLabel(value) {
            switch(String(value)) {
                case '7': return 'Last 7 Days';
                case '30': return 'Last 30 Days';
                case '90': return 'Last 90 Days';
                case 'semester': return 'Current Semester';
                default: return 'Custom';
            }
        }

        function computeKpis(data) {
            const totalSessions = data?.sessions?.length || 0;
            const totalStudents = new Set((data?.attendance||[]).map(r => r.student_id)).size || reportData.students.length;
            let avgAttendance = 0;
            if (data?.attendance?.length) {
                const present = data.attendance.filter(a => a.status === 'present' || a.status === 'exited').length;
                avgAttendance = Math.round((present / data.attendance.length) * 100);
            }
            // Best course
            let bestCourse = 'N/A';
            let bestRate = 0;
            if (data?.attendance?.length) {
                const courseAttendance = {};
                data.attendance.forEach(r => {
                    courseAttendance[r.course] ||= { present: 0, total: 0 };
                    courseAttendance[r.course].total++;
                    if (r.status === 'present' || r.status === 'exited') courseAttendance[r.course].present++;
                });
                Object.keys(courseAttendance).forEach(course => {
                    const stats = courseAttendance[course];
                    const rate = stats.total ? stats.present / stats.total : 0;
                    if (rate > bestRate) { bestRate = rate; bestCourse = course; }
                });
            }
            return { totalSessions, totalStudents, avgAttendance, bestCourse };
        }

        function buildInsights(data) {
            try {
                const insights = [];
                const k = computeKpis(data);
                if (k.avgAttendance < 60) {
                    insights.push('Average attendance is below 60%. Consider sending reminders or incentivizing attendance.');
                } else if (k.avgAttendance >= 90) {
                    insights.push('Excellent overall attendance. Keep the current engagement strategies.');
                }
                // Lowest performing course
                if (data?.attendance?.length) {
                    const courseStats = {};
                    data.attendance.forEach(r => {
                        courseStats[r.course] ||= { present: 0, total: 0 };
                        courseStats[r.course].total++;
                        if (r.status === 'present' || r.status === 'exited') courseStats[r.course].present++;
                    });
                    let worstCourse = null, worstRate = 2;
                    Object.keys(courseStats).forEach(c => {
                        const s = courseStats[c];
                        const rate = s.total ? (s.present / s.total) : 0;
                        if (rate < worstRate) { worstRate = rate; worstCourse = c; }
                    });
                    if (worstCourse && worstRate < 0.6) {
                        insights.push(`Low attendance observed in ${worstCourse}. Review scheduling, content difficulty, or communication for this course.`);
                    }
                }
                if (!insights.length) insights.push('Attendance metrics are stable with no critical issues detected.');
                return insights;
            } catch (e) { return ['Insights could not be generated.']; }
        }

        // Export functions
        async function exportToPDF() {
            try {
                // Ensure data exists and a table is rendered
                let data = lastGeneratedData;
                if (!data) {
                    const dateRange = document.getElementById('dateRange').value;
                    const courseFilter = document.getElementById('courseFilter').value;
                    data = filterReportData(dateRange, courseFilter);
                }
                const table = getCurrentReportTable();
                if (!table || !data) {
                    return Swal.fire({ icon: 'warning', title: 'No data to export', text: 'Please generate a report first.' });
                }

                // Minimal wrapper: header + the currently filtered & styled table
                const wrapper = document.createElement('div');
                wrapper.style.fontFamily = 'Poppins, Arial, sans-serif';
                wrapper.style.color = '#1b1d1f';
                wrapper.style.padding = '10px';

                // Header with current filter summary
                const h = document.createElement('div');
                h.style.margin = '0 0 8px 0';
                const reportTypeText = document.getElementById('reportType').selectedOptions[0]?.textContent || 'Report';
                const dateLabel = formatDateRangeLabel(document.getElementById('dateRange').value);
                const course = document.getElementById('courseFilter').value || 'All Courses';
                let extra = '';
                const sessionSel = document.getElementById('sessionAttendanceSession');
                if (sessionSel && sessionSel.value) extra = ` | Session: ${sessionSel.value}`;
                h.textContent = `${reportTypeText} | Range: ${dateLabel} | Course: ${course}${extra}`;
                h.style.fontWeight = '600';
                wrapper.appendChild(h);

                // Clone current table and keep basic borders for readability in PDF
                const tableClone = table.cloneNode(true);
                tableClone.style.width = '100%';
                tableClone.style.borderCollapse = 'collapse';
                tableClone.querySelectorAll('th, td').forEach(cell => {
                    cell.style.border = '1px solid #ddd';
                    cell.style.padding = '8px';
                });
                wrapper.appendChild(tableClone);

                const opt = {
                    margin:       [10, 10, 10, 10],
                    filename:     `${buildReportFileBaseName('mubas-report')}.pdf`,
                    image:        { type: 'jpeg', quality: 0.98 },
                    html2canvas:  { scale: 2, useCORS: true },
                    jsPDF:        { unit: 'mm', format: 'a4', orientation: 'landscape' }
                };

                await html2pdf().set(opt).from(wrapper).save();
            } catch (err) {
                console.error('PDF export failed:', err);
                Swal.fire({
                    icon: 'error',
                    title: 'PDF Export Failed',
                    text: err?.message || 'An error occurred while generating the PDF.'
                });
            }
        }

        function exportToExcel() {
            try {
                const table = getCurrentReportTable();
                if (!table) {
                    return Swal.fire({
                        icon: 'warning',
                        title: 'No data to export',
                        text: 'Please generate a report first.',
                    });
                }

                const aoa = extractTableData(table);
                if (!aoa || aoa.length === 0) {
                    return Swal.fire({
                        icon: 'warning',
                        title: 'No data found',
                        text: 'The generated report appears to be empty.'
                    });
                }

                const wb = XLSX.utils.book_new();
                const ws = XLSX.utils.aoa_to_sheet(aoa);
                XLSX.utils.book_append_sheet(wb, ws, 'Report');
                XLSX.writeFile(wb, `${buildReportFileBaseName('mubas-report')}.xlsx`);
            } catch (err) {
                console.error('Excel export failed:', err);
                Swal.fire({
                    icon: 'error',
                    title: 'Excel Export Failed',
                    text: err?.message || 'An error occurred while generating the Excel file.'
                });
            }
        }

        async function exportToWord() {
            try {
                if (!window.docx) throw new Error('DOCX library not loaded');
                const table = getCurrentReportTable();
                if (!table) {
                    return Swal.fire({
                        icon: 'warning',
                        title: 'No data to export',
                        text: 'Please generate a report first.',
                    });
                }
                const aoa = extractTableData(table);
                if (!aoa || aoa.length === 0) {
                    return Swal.fire({
                        icon: 'warning',
                        title: 'No data found',
                        text: 'The generated report appears to be empty.'
                    });
                }

                const { Document, Packer, Paragraph, TextRun, Table: DTable, TableRow, TableCell, HeadingLevel, WidthType, AlignmentType, BorderStyle, ShadingType } = docx;

                const header = new Paragraph({
                    text: 'MUBAS Attendance Report',
                    heading: HeadingLevel.HEADING_1,
                });

                const meta = new Paragraph({
                    children: [
                        new TextRun({ text: `Generated: ${new Date().toLocaleString()}`, size: 18, color: '555555' })
                    ]
                });

                const rows = [];
                aoa.forEach((rowData, rIdx) => {
                    const isHeader = rIdx === 0;
                    const cells = rowData.map(cellText => new TableCell({
                        children: [new Paragraph(String(cellText))],
                        shading: isHeader ? { type: ShadingType.CLEAR, fill: 'E8F1FF', color: 'auto' } : undefined,
                    }));
                    rows.push(new TableRow({ children: cells }));
                });

                const tableDocx = new DTable({
                    rows,
                    width: { size: 100, type: WidthType.PERCENTAGE },
                    borders: {
                        top: { style: BorderStyle.SINGLE, size: 1, color: 'CCCCCC' },
                        bottom: { style: BorderStyle.SINGLE, size: 1, color: 'CCCCCC' },
                        left: { style: BorderStyle.SINGLE, size: 1, color: 'CCCCCC' },
                        right: { style: BorderStyle.SINGLE, size: 1, color: 'CCCCCC' },
                        insideH: { style: BorderStyle.SINGLE, size: 1, color: 'DDDDDD' },
                        insideV: { style: BorderStyle.SINGLE, size: 1, color: 'DDDDDD' },
                    }
                });

                const doc = new Document({
                    sections: [{
                        properties: {},
                        children: [header, meta, new Paragraph({ text: ' ' }), tableDocx]
                    }]
                });

                const blob = await Packer.toBlob(doc);
                const filename = `${buildReportFileBaseName('mubas-report')}.docx`;
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = filename;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            } catch (err) {
                console.error('Word export failed:', err);
                Swal.fire({
                    icon: 'error',
                    title: 'Word Export Failed',
                    text: err?.message || 'An error occurred while generating the Word document.'
                });
            }
        }

        function exportSystemLogs() {
            try {
                const filterValue = document.getElementById('logTypeFilter')?.value || '';
                let logs = generateAllSystemLogs();
                if (filterValue) logs = logs.filter(l => l.type === filterValue);
                if (!logs || logs.length === 0) {
                    return Swal.fire({ icon: 'warning', title: 'No logs to export', text: 'No system activities found.' });
                }
                const aoa = [['Timestamp', 'Event Type', 'Description', 'User/Entity', 'Status']];
                logs.forEach(l => aoa.push([
                    l.timestamp.toLocaleString(), l.type, l.description, l.user, l.status
                ]));
                const wb = XLSX.utils.book_new();
                const ws = XLSX.utils.aoa_to_sheet(aoa);
                XLSX.utils.book_append_sheet(wb, ws, 'System Logs');
                const suffix = filterValue ? `-${filterValue}` : '';
                const filename = `mubas-system-logs${suffix}-${new Date().toISOString().slice(0,16).replace('T','_').replace(':','')}.xlsx`;
                XLSX.writeFile(wb, filename);
            } catch (err) {
                console.error('Logs export failed:', err);
                Swal.fire({
                    icon: 'error',
                    title: 'Logs Export Failed',
                    text: err?.message || 'An error occurred while exporting the logs.'
                });
            }
        }

        // Utility functions
        function showLoading(message = 'Loading...') {
            const overlay = document.getElementById('loadingOverlay');
            overlay.querySelector('h3').textContent = message;
            overlay.style.display = 'flex';
        }

        function hideLoading() {
            document.getElementById('loadingOverlay').style.display = 'none';
        }

        // Auto-refresh data every 5 minutes
        setInterval(async () => {
            if (document.visibilityState === 'visible') {
                await loadReportData();
                updateStatistics();
                loadSystemLogs();
            }
        }, 300000);
    </script>
</body>
</html>
