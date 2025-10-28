// Supabase Authentication Utility Functions
// This file provides centralized authentication management for the MUBAS attendance system

// Supabase Configuration
const SUPABASE_URL = 'https://jzxmzmaszmdjftyhilgu.supabase.co';
const SUPABASE_ANON_KEY = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Imp6eG16bWFzem1kamZ0eWhpbGd1Iiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTcyNTQwNDAsImV4cCI6MjA3MjgzMDA0MH0.uASNA1MhRzwbd9BR3ox8vDqvpJMZojChchu8Lc01kN4';

// Initialize Supabase client
const supabase = window.supabase.createClient(SUPABASE_URL, SUPABASE_ANON_KEY);

// Authentication utility functions
const SupabaseAuth = {
    // Expose supabase client for direct database operations
    supabase: supabase,
    
    // Check if user is authenticated
    async isAuthenticated() {
        try {
            const { data: { session } } = await supabase.auth.getSession();
            return session !== null;
        } catch (error) {
            console.error('Error checking authentication:', error);
            return false;
        }
    },

    // Get current user session
    async getCurrentSession() {
        try {
            const { data: { session } } = await supabase.auth.getSession();
            return session;
        } catch (error) {
            console.error('Error getting session:', error);
            return null;
        }
    },

    // Get current user data from localStorage
    getCurrentUser() {
        try {
            const lecturerData = localStorage.getItem('lecturer_data');
            return lecturerData ? JSON.parse(lecturerData) : null;
        } catch (error) {
            console.error('Error getting user data:', error);
            return null;
        }
    },

    // Get lecturer data from Supabase database
    async getLecturerData(email) {
        try {
            const { data, error } = await supabase
                .from('lecturers')
                .select('*')
                .eq('email', email)
                .single();

            if (error) {
                console.error('Error fetching lecturer data:', error);
                return null;
            }

            return data;
        } catch (error) {
            console.error('Error in getLecturerData:', error);
            return null;
        }
    },

    // Redirect to login if not authenticated
    async requireAuth() {
        const isAuth = await this.isAuthenticated();
        const userData = this.getCurrentUser();
        
        if (!isAuth || !userData) {
            // Clear any stale data
            localStorage.removeItem('lecturer_data');
            window.location.href = 'login.php';
            return false;
        }
        return true;
    },

    // Sign out user
    async signOut() {
        try {
            // Sign out from Supabase
            const { error } = await supabase.auth.signOut();
            
            if (error) {
                console.error('Error signing out:', error);
            }

            // Clear localStorage
            localStorage.removeItem('lecturer_data');
            
            // Redirect to login
            window.location.href = 'login.php';
        } catch (error) {
            console.error('Error in signOut:', error);
            // Force redirect even if there's an error
            localStorage.removeItem('lecturer_data');
            window.location.href = 'login.php';
        }
    },

    // Update user display in header
    updateUserDisplay() {
        const userData = this.getCurrentUser();
        if (!userData) return;

        // Update user name
        const userNameElements = document.querySelectorAll('.user-menu h4, .header .user-menu h4');
        userNameElements.forEach(element => {
            if (element) {
                element.textContent = userData.full_name;
            }
        });

        // Update department
        const departmentElements = document.querySelectorAll('.user-menu p, .header .user-menu p');
        departmentElements.forEach(element => {
            if (element) {
                element.textContent = userData.department.toUpperCase();
            }
        });
    },

    // Initialize authentication for a page
    async initAuth() {
        // Check authentication
        const isAuthenticated = await this.requireAuth();
        if (!isAuthenticated) return false;

        // Update user display
        this.updateUserDisplay();

        // Set up logout handlers
        this.setupLogoutHandlers();

        return true;
    },

    // Setup logout button handlers
    setupLogoutHandlers() {
        const logoutLinks = document.querySelectorAll('a[href="logout.php"]');
        logoutLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                this.signOut();
            });
        });
    },

    // Refresh user data from Supabase
    async refreshUserData() {
        try {
            const session = await this.getCurrentSession();
            if (!session) {
                await this.signOut();
                return null;
            }

            const lecturerData = await this.getLecturerData(session.user.email);
            if (!lecturerData) {
                await this.signOut();
                return null;
            }

            // Update localStorage with fresh data
            const userData = {
                id: lecturerData.id,
                full_name: lecturerData.full_name,
                email: lecturerData.email,
                department: lecturerData.department
            };

            localStorage.setItem('lecturer_data', JSON.stringify(userData));
            this.updateUserDisplay();

            return userData;
        } catch (error) {
            console.error('Error refreshing user data:', error);
            return null;
        }
    },

    // Listen for auth state changes
    setupAuthListener() {
        supabase.auth.onAuthStateChange((event, session) => {
            if (event === 'SIGNED_OUT' || !session) {
                localStorage.removeItem('lecturer_data');
                if (window.location.pathname !== '/login.php') {
                    window.location.href = 'login.php';
                }
            }
        });
    }
};

// Initialize auth listener when the script loads
document.addEventListener('DOMContentLoaded', () => {
    SupabaseAuth.setupAuthListener();
});

// Export for use in other scripts
window.SupabaseAuth = SupabaseAuth;
