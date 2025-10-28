<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MUBAS | Lecturer Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
    <link rel="stylesheet" href="style/login.css">
    
</head>
<body>
    <!-- Floating Background Elements -->
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <!-- Loading Screen -->
    <div class="loading" id="loadingScreen">
        <div class="loader"></div>
    </div>

    <div class="container">
        <div class="auth-wrapper">
            <!-- Brand Section -->
            <div class="brand-section">
                <div class="logo-container">
                    <div class="logo-image">
                        <img src="mubas-logo-full.png" alt="MUBAS Logo" style="width: 150px; height: 150px; background: white; border-radius: 50%;">
                    </div>
                    <h1 class="brand-title">MUBAS-ATTEND</h1>
                    <p class="brand-subtitle">Lecturer Portal</p>
                    <div class="welcome-text">
                        <p>Welcome to the MUBAS Attendance Management System. Access your dashboard to manage classes, track attendance, and monitor student progress efficiently.</p>
                    </div>
                </div>
            </div>

            <!-- Forms Section -->
            <div class="forms-section">
                <div class="form-container">
                    <div class="form-wrapper" id="formWrapper">
                        <!-- Login Form -->
                        <div class="form login">
                            <h2 class="form-title">
                                <i class="fas fa-sign-in-alt"></i>
                                Lecturer Login
                            </h2>

                            <form id="loginForm">
                                <div class="form-group">
                                    <label for="loginEmail">Email Address</label>
                                    <div class="input-group">
                                        <i class="fas fa-envelope"></i>
                                        <input type="email" id="loginEmail" name="email" required placeholder="Enter your email address">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="loginPassword">Password</label>
                                    <div class="input-group">
                                        <i class="fas fa-lock"></i>
                                        <input type="password" id="loginPassword" name="password" required placeholder="Enter your password">
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-sign-in-alt"></i> Sign In
                                </button>
                            </form>

                            <div class="switch-text">
                                Don't have an account? 
                                <span class="switch-link" id="showRegister">Sign up here</span>
                            </div>
                        </div>

                        <!-- Register Form -->
                        <div class="form register">
                            <h2 class="form-title">
                                <i class="fas fa-user-plus"></i>
                                Lecturer Registration
                            </h2>

                            <form id="registerForm">
                                <div class="form-group">
                                    <label for="registerName">Full Name</label>
                                    <div class="input-group">
                                        <i class="fas fa-user"></i>
                                        <input type="text" id="registerName" name="fullName" required placeholder="Enter your full name">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="registerEmail">Email Address</label>
                                    <div class="input-group">
                                        <i class="fas fa-envelope"></i>
                                        <input type="email" id="registerEmail" name="email" required placeholder="Enter your email address">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="registerDepartment">School</label>
                                    <div class="input-group">
                                        <i class="fas fa-building"></i>
                                        <select id="registerDepartment" name="School" required>
                                            <option value="">Select School</option>
                                            <option value="education">School of Education and media studies</option>
                                            <option value="engineering">School of Engineering</option>
                                            <option value="applied-sciences">School of Applied Sciences</option>
                                            <option value="business">School of Business and Economic Sciences</option>
                                            <option value="humanities">School of Humanities and Social Sciences</option>
                                            <option value="law">School of Law</option>
                                            <option value="Built-Environment">School of Built Environment</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="registerPassword">Password</label>
                                    <div class="input-group">
                                        <i class="fas fa-lock"></i>
                                        <input type="password" id="registerPassword" name="password" required placeholder="Create a strong password">
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-user-plus"></i> Create Account
                                </button>
                            </form>

                            <div class="switch-text">
                                Already have an account? 
                                <span class="switch-link" id="showLogin">Sign in here</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Supabase Configuration
        const SUPABASE_URL = 'https://jzxmzmaszmdjftyhilgu.supabase.co'; // Replace with your Supabase URL
        const SUPABASE_ANON_KEY = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Imp6eG16bWFzem1kamZ0eWhpbGd1Iiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTcyNTQwNDAsImV4cCI6MjA3MjgzMDA0MH0.uASNA1MhRzwbd9BR3ox8vDqvpJMZojChchu8Lc01kN4'; // Replace with your Supabase anon key
        
        const supabase = window.supabase.createClient(SUPABASE_URL, SUPABASE_ANON_KEY);

        // DOM Elements
        const formWrapper = document.getElementById('formWrapper');
        const showRegisterBtn = document.getElementById('showRegister');
        const showLoginBtn = document.getElementById('showLogin');
        const loginForm = document.getElementById('loginForm');
        const registerForm = document.getElementById('registerForm');
        const loadingScreen = document.getElementById('loadingScreen');

        // Initialize the application
        document.addEventListener('DOMContentLoaded', function() {
            setupEventListeners();
            checkExistingSession();
        });

        // Check if user is already logged in
        async function checkExistingSession() {
            const { data: { session } } = await supabase.auth.getSession();
            if (session) {
                // User is already logged in, redirect to dashboard
                window.location.href = 'index.php';
            }
        }

        // Setup event listeners
        function setupEventListeners() {
            // Form switching
            showRegisterBtn.addEventListener('click', () => switchForm('register'));
            showLoginBtn.addEventListener('click', () => switchForm('login'));

            // Form submissions
            loginForm.addEventListener('submit', handleLogin);
            registerForm.addEventListener('submit', handleRegister);

            // Input animations
            const inputs = document.querySelectorAll('input, select');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'translateY(-2px)';
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'translateY(0)';
                });
            });
        }

        // Switch between login and register forms
        function switchForm(formType) {
            if (formType === 'register') {
                formWrapper.classList.add('flip');
            } else {
                formWrapper.classList.remove('flip');
            }
        }

        // Handle login form submission
        async function handleLogin(e) {
            e.preventDefault();
            
            const email = document.getElementById('loginEmail').value;
            const password = document.getElementById('loginPassword').value;

            if (!email || !password) {
                showAlert('error', 'Missing Information', 'Please fill in all fields.');
                return;
            }

            showLoading();

            try {
                // Sign in with Supabase Auth
                const { data, error } = await supabase.auth.signInWithPassword({
                    email: email,
                    password: password,
                });

                if (error) {
                    hideLoading();
                    let errorMessage = 'Invalid email or password. Please check your credentials and try again.';
                    showAlert('error', 'Login Failed', errorMessage);
                    return;
                }

                // Check if user profile exists in lecturers table
                const { data: lecturerData, error: lecturerError } = await supabase
                    .from('lecturers')
                    .select('*')
                    .eq('email', email)
                    .single();

                if (lecturerError) {
                    // If no record found, sign out the user and show error
                    if (lecturerError.code === 'PGRST116') {
                        await supabase.auth.signOut();
                        hideLoading();
                        showAlert('error', 'Access Denied', 'No lecturer profile found for this email. Please contact administrator.');
                        return;
                    }
                    throw lecturerError;
                }

                if (!lecturerData) {
                    await supabase.auth.signOut();
                    hideLoading();
                    showAlert('error', 'Access Denied', 'No lecturer profile found for this email. Please contact administrator.');
                    return;
                }

                // Store user session data
                localStorage.setItem('lecturer_data', JSON.stringify({
                    id: lecturerData.id,
                    full_name: lecturerData.full_name,
                    email: lecturerData.email,
                    department: lecturerData.department
                }));

                hideLoading();
                showAlert('success', 'Login Successful!', 'Welcome back! Redirecting to your dashboard...').then(() => {
                    window.location.href = 'index.php';
                });

            } catch (error) {
                hideLoading();
                showAlert('error', 'Connection Error', 'Unable to connect to the server. Please try again later.');
                console.error('Login Error:', error);
            }
        }

        // Handle register form submission
        async function handleRegister(e) {
            e.preventDefault();
            
            const fullName = document.getElementById('registerName').value;
            const email = document.getElementById('registerEmail').value;
            const department = document.getElementById('registerDepartment').value;
            const password = document.getElementById('registerPassword').value;

            if (!fullName || !email || !department || !password) {
                showAlert('error', 'Missing Information', 'Please fill in all fields.');
                return;
            }

            // Validate password strength
            if (password.length < 8) {
                showAlert('error', 'Weak Password', 'Password must be at least 8 characters long.');
                return;
            }

            showLoading();

            try {
                // Check if email already exists in lecturers table
                const { data: existingLecturer, error: checkError } = await supabase
                    .from('lecturers')
                    .select('email')
                    .eq('email', email)
                    .single();

                if (checkError && checkError.code !== 'PGRST116') {
                    throw checkError;
                }

                if (existingLecturer) {
                    hideLoading();
                    showAlert('error', 'Email Already Registered', 'This email address is already registered.');
                    return;
                }

                // Sign up with Supabase Auth (disable email confirmation)
                const { data: authData, error: authError } = await supabase.auth.signUp({
                    email: email,
                    password: password,
                    options: {
                        emailRedirectTo: undefined,
                        data: {
                            full_name: fullName,
                            department: department
                        }
                    }
                });

                if (authError) {
                    hideLoading();
                    let errorMessage = 'Unable to create account. Please try again later.';
                    if (authError.message.includes('already registered')) {
                        errorMessage = 'This email address is already registered.';
                    } else if (authError.message.includes('weak password')) {
                        errorMessage = 'Password is too weak. Please use a stronger password.';
                    }
                    showAlert('error', 'Registration Failed', errorMessage);
                    return;
                }

                // Insert lecturer data into lecturers table
                const { error: insertError } = await supabase
                    .from('lecturers')
                    .insert([
                        {
                            id: authData.user.id,
                            full_name: fullName,
                            email: email,
                            department: department,
                            created_at: new Date().toISOString(),
                            status: 'active'
                        }
                    ]);

                if (insertError) {
                    hideLoading();
                    showAlert('error', 'Registration Failed', 'Unable to create lecturer profile. Please try again.');
                    return;
                }

                hideLoading();
                
                // Auto-login the user after successful registration
                showAlert('success', 'Registration Successful!', 'Your account has been created successfully! Logging you in...').then(async () => {
                    // Store user session data
                    localStorage.setItem('lecturer_data', JSON.stringify({
                        id: authData.user.id,
                        full_name: fullName,
                        email: email,
                        department: department
                    }));
                    
                    // Redirect to dashboard
                    window.location.href = 'index.php';
                });

            } catch (error) {
                hideLoading();
                let errorMessage = 'Unable to create account. Please try again later.';
                if (error.message.includes('already registered')) {
                    errorMessage = 'This email address is already registered.';
                }
                showAlert('error', 'Registration Failed', errorMessage);
                console.error('Registration Error:', error);
            }
        }

        // Utility Functions
        function showAlert(type, title, text) {
            return Swal.fire({
                icon: type,
                title: title,
                text: text,
                confirmButtonColor: '#4361ee',
                timer: type === 'success' ? 2000 : undefined,
                showConfirmButton: type !== 'success'
            });
        }

        function showLoading() {
            loadingScreen.style.display = 'flex';
        }

        function hideLoading() {
            loadingScreen.style.display = 'none';
        }

        // Add some interactive animations
        document.addEventListener('mousemove', function(e) {
            const shapes = document.querySelectorAll('.shape');
            const x = e.clientX / window.innerWidth;
            const y = e.clientY / window.innerHeight;
            
            shapes.forEach((shape, index) => {
                const speed = (index + 1) * 0.02;
                const xOffset = (x - 0.5) * 20 * speed;
                const yOffset = (y - 0.5) * 20 * speed;
                
                shape.style.transform = `translate(${xOffset}px, ${yOffset}px) rotate(${xOffset}deg)`;
            });
        });
    </script>
</body>
</html>