// Authentication Manager
document.addEventListener('DOMContentLoaded', function() {
    console.log('Auth.js loaded successfully');
    updateNavbarAuth();
});

function updateNavbarAuth() {
    const patientLogin = localStorage.getItem('patientLogin');
    const adminLogin = localStorage.getItem('adminLogin');
    
    console.log('Checking login status:', { patientLogin: !!patientLogin, adminLogin: !!adminLogin });
    
    // Find the login button in navbar
    const loginBtn = document.querySelector('.navbar-container .btn-primary');
    
    if (loginBtn) {
        if (patientLogin || adminLogin) {
            // User is logged in
            loginBtn.textContent = 'Logout';
            loginBtn.onclick = function(e) {
                e.preventDefault();
                logoutUser();
            };
            loginBtn.href = '#';
        } else {
            // User is not logged in
            loginBtn.textContent = 'Login';
            loginBtn.href = './login.html';
            loginBtn.onclick = null;
        }
    }
}

function logoutUser() {
    if (confirm('Are you sure you want to logout?')) {
        localStorage.removeItem('patientLogin');
        localStorage.removeItem('adminLogin');
        console.log('User logged out');
        window.location.href = './login.html';
    }
}

function isPatientLoggedIn() {
    return !!localStorage.getItem('patientLogin');
}

function isAdminLoggedIn() {
    return !!localStorage.getItem('adminLogin');
}

function getPatientData() {
    const data = localStorage.getItem('patientLogin');
    return data ? JSON.parse(data) : null;
}

function getAdminData() {
    const data = localStorage.getItem('adminLogin');
    return data ? JSON.parse(data) : null;
}

// Redirect to login if not authenticated
function requirePatientLogin() {
    console.log('Checking patient login...');
    if (!isPatientLoggedIn()) {
        console.log('Patient not logged in, redirecting to login page');
        alert('Please login as a patient to access this page');
        window.location.href = './login.html';
        return false;
    }
    console.log('Patient logged in successfully');
    return true;
}

function requireAdminLogin() {
    console.log('Checking admin login...');
    if (!isAdminLoggedIn()) {
        console.log('Admin not logged in, redirecting to login page');
        alert('Please login as an administrator to access this page');
        window.location.href = './login.html';
        return false;
    }
    console.log('Admin logged in successfully');
    return true;
}
