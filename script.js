/* ============================================
   HOSPITAL MANAGEMENT WEBSITE - JAVASCRIPT
   Dynamic functionality, form handling, and data loading
   ============================================ */

// ==================== CONSTANTS ====================
const STORAGE_KEY = 'hospitalAppointments';
const DOCTORS_DATA_FILE = './data/doctors.json';

// ==================== DOM ELEMENTS ====================
let hamburger, navbarMenu, navbarLinks, body;

// ==================== INITIALIZATION ====================
document.addEventListener('DOMContentLoaded', () => {
    initializeElements();
    setupEventListeners();
    loadDoctorsData();
    setActiveNavLink();
});

/**
 * Initialize DOM elements
 */
function initializeElements() {
    hamburger = document.querySelector('.hamburger');
    navbarMenu = document.querySelector('.navbar-menu');
    navbarLinks = document.querySelectorAll('.navbar-link');
    body = document.body;
}

/**
 * Setup all event listeners
 */
function setupEventListeners() {
    // Mobile menu toggle
    if (hamburger) {
        hamburger.addEventListener('click', toggleMobileMenu);
    }
    
    // Close mobile menu when link is clicked
    navbarLinks.forEach(link => {
        link.addEventListener('click', () => {
            closeMobileMenu();
            setActiveNavLink();
        });
    });
    
    // Form submission
    const appointmentForm = document.querySelector('#appointmentForm');
    if (appointmentForm) {
        appointmentForm.addEventListener('submit', handleAppointmentSubmit);
    }
    
    const contactForm = document.querySelector('#contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', handleContactSubmit);
    }
    
    // Emergency call button
    const emergencyBtn = document.querySelector('.btn-emergency');
    if (emergencyBtn) {
        emergencyBtn.addEventListener('click', handleEmergencyCall);
    }
}

/**
 * Toggle mobile menu visibility
 */
function toggleMobileMenu() {
    if (hamburger && navbarMenu) {
        hamburger.classList.toggle('active');
        navbarMenu.classList.toggle('active');
    }
}

/**
 * Close mobile menu
 */
function closeMobileMenu() {
    if (hamburger && navbarMenu) {
        hamburger.classList.remove('active');
        navbarMenu.classList.remove('active');
    }
}

/**
 * Set active nav link based on current page
 */
function setActiveNavLink() {
    const currentPage = getCurrentPage();
    
    navbarLinks.forEach(link => {
        link.classList.remove('active');
        
        const href = link.getAttribute('href');
        if (href === currentPage || (currentPage === 'index.html' && href === './') || (currentPage === 'index.html' && href === 'index.html')) {
            link.classList.add('active');
        }
    });
}

/**
 * Get current page filename
 */
function getCurrentPage() {
    const path = window.location.pathname;
    const page = path.split('/').pop() || 'index.html';
    return page === '' ? 'index.html' : page;
}

// ==================== FORM VALIDATION ====================

/**
 * Validate email format
 */
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

/**
 * Validate phone number (basic validation)
 */
function isValidPhone(phone) {
    const phoneRegex = /^[\d\s\-\+\(\)]{10,}$/;
    return phoneRegex.test(phone.replace(/\s/g, ''));
}

/**
 * Clear form error message
 */
function clearFormError(field) {
    const errorElement = field.nextElementSibling;
    if (errorElement && errorElement.classList.contains('form-error')) {
        errorElement.remove();
    }
    field.classList.remove('is-invalid');
}

/**
 * Show form error message
 */
function showFormError(field, message) {
    clearFormError(field);
    field.classList.add('is-invalid');
    const errorElement = document.createElement('div');
    errorElement.className = 'form-error';
    errorElement.textContent = message;
    field.insertAdjacentElement('afterend', errorElement);
}

/**
 * Show success message
 */
function showSuccessMessage(container, message) {
    const alert = document.createElement('div');
    alert.className = 'alert alert-success';
    alert.innerHTML = `<strong>Success!</strong> ${message}`;
    container.insertAdjacentElement('afterbegin', alert);
    
    setTimeout(() => {
        alert.remove();
    }, 5000);
}

/**
 * Show error message
 */
function showErrorMessage(container, message) {
    const alert = document.createElement('div');
    alert.className = 'alert alert-error';
    alert.innerHTML = `<strong>Error!</strong> ${message}`;
    container.insertAdjacentElement('afterbegin', alert);
    
    setTimeout(() => {
        alert.remove();
    }, 5000);
}

// ==================== APPOINTMENT FORM HANDLING ====================

/**
 * Handle appointment form submission
 */
function handleAppointmentSubmit(e) {
    e.preventDefault();
    
    const form = e.target;
    const formContainer = form.closest('.container') || form.parentElement;
    
    // Get form fields
    const patientName = form.querySelector('[name="patient-name"]');
    const patientEmail = form.querySelector('[name="patient-email"]');
    const patientPhone = form.querySelector('[name="patient-phone"]');
    const doctorSelect = form.querySelector('[name="doctor"]');
    const appointmentDate = form.querySelector('[name="appointment-date"]');
    const appointmentTime = form.querySelector('[name="appointment-time"]');
    const message = form.querySelector('[name="message"]');
    
    let isValid = true;
    
    // Clear previous errors
    [patientName, patientEmail, patientPhone, doctorSelect, appointmentDate, appointmentTime].forEach(field => {
        if (field) clearFormError(field);
    });
    
    // Validate patient name
    if (!patientName.value.trim()) {
        showFormError(patientName, 'Please enter your name');
        isValid = false;
    } else if (patientName.value.trim().length < 2) {
        showFormError(patientName, 'Name must be at least 2 characters');
        isValid = false;
    }
    
    // Validate email
    if (!patientEmail.value.trim()) {
        showFormError(patientEmail, 'Please enter your email');
        isValid = false;
    } else if (!isValidEmail(patientEmail.value)) {
        showFormError(patientEmail, 'Please enter a valid email address');
        isValid = false;
    }
    
    // Validate phone
    if (!patientPhone.value.trim()) {
        showFormError(patientPhone, 'Please enter your phone number');
        isValid = false;
    } else if (!isValidPhone(patientPhone.value)) {
        showFormError(patientPhone, 'Please enter a valid phone number');
        isValid = false;
    }
    
    // Validate doctor selection
    if (!doctorSelect.value) {
        showFormError(doctorSelect, 'Please select a doctor');
        isValid = false;
    }
    
    // Validate date
    if (!appointmentDate.value) {
        showFormError(appointmentDate, 'Please select a date');
        isValid = false;
    } else {
        const selectedDate = new Date(appointmentDate.value);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        if (selectedDate < today) {
            showFormError(appointmentDate, 'Please select a future date');
            isValid = false;
        }
    }
    
    // Validate time
    if (!appointmentTime.value) {
        showFormError(appointmentTime, 'Please select a time');
        isValid = false;
    }
    
    if (!isValid) return;
    
    // Create appointment object
    const appointment = {
        id: Date.now(),
        patientName: patientName.value.trim(),
        patientEmail: patientEmail.value.trim(),
        patientPhone: patientPhone.value.trim(),
        doctor: doctorSelect.value,
        date: appointmentDate.value,
        time: appointmentTime.value,
        message: message.value.trim(),
        submittedAt: new Date().toISOString()
    };
    
    // Save to localStorage
    const appointments = getAppointmentsFromStorage();
    appointments.push(appointment);
    localStorage.setItem(STORAGE_KEY, JSON.stringify(appointments));
    
    // Show success message
    showSuccessMessage(formContainer, 'Your appointment has been booked successfully! We will contact you soon.');
    
    // Reset form
    form.reset();
    
    // Log appointment (in production, send to server)
    console.log('Appointment booked:', appointment);
}

/**
 * Handle contact form submission
 */
function handleContactSubmit(e) {
    e.preventDefault();
    
    const form = e.target;
    const formContainer = form.closest('.container') || form.parentElement;
    
    // Get form fields
    const fullName = form.querySelector('[name="full-name"]');
    const email = form.querySelector('[name="email"]');
    const phone = form.querySelector('[name="phone"]');
    const subject = form.querySelector('[name="subject"]');
    const message = form.querySelector('[name="message"]');
    
    let isValid = true;
    
    // Clear previous errors
    [fullName, email, phone, subject, message].forEach(field => {
        if (field) clearFormError(field);
    });
    
    // Validate full name
    if (!fullName.value.trim()) {
        showFormError(fullName, 'Please enter your name');
        isValid = false;
    } else if (fullName.value.trim().length < 2) {
        showFormError(fullName, 'Name must be at least 2 characters');
        isValid = false;
    }
    
    // Validate email
    if (!email.value.trim()) {
        showFormError(email, 'Please enter your email');
        isValid = false;
    } else if (!isValidEmail(email.value)) {
        showFormError(email, 'Please enter a valid email address');
        isValid = false;
    }
    
    // Validate phone
    if (!phone.value.trim()) {
        showFormError(phone, 'Please enter your phone number');
        isValid = false;
    } else if (!isValidPhone(phone.value)) {
        showFormError(phone, 'Please enter a valid phone number');
        isValid = false;
    }
    
    // Validate subject
    if (!subject.value.trim()) {
        showFormError(subject, 'Please enter a subject');
        isValid = false;
    } else if (subject.value.trim().length < 5) {
        showFormError(subject, 'Subject must be at least 5 characters');
        isValid = false;
    }
    
    // Validate message
    if (!message.value.trim()) {
        showFormError(message, 'Please enter your message');
        isValid = false;
    } else if (message.value.trim().length < 10) {
        showFormError(message, 'Message must be at least 10 characters');
        isValid = false;
    }
    
    if (!isValid) return;
    
    // Create contact message object
    const contactMessage = {
        id: Date.now(),
        fullName: fullName.value.trim(),
        email: email.value.trim(),
        phone: phone.value.trim(),
        subject: subject.value.trim(),
        message: message.value.trim(),
        submittedAt: new Date().toISOString()
    };
    
    // Show success message (in production, send to server)
    showSuccessMessage(formContainer, 'Thank you for your message! We will get back to you shortly.');
    
    // Reset form
    form.reset();
    
    // Log message
    console.log('Contact message submitted:', contactMessage);
}

// ==================== DOCTOR DATA LOADING ====================

/**
 * Load doctors data from JSON file
 */
function loadDoctorsData() {
    const doctorsContainer = document.querySelector('#doctorsContainer');
    const doctorSelect = document.querySelector('[name="doctor"]');
    
    if (!doctorsContainer && !doctorSelect) return;
    
    fetch(DOCTORS_DATA_FILE)
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to load doctors data');
            }
            return response.json();
        })
        .then(data => {
            // Populate doctor grid
            if (doctorsContainer) {
                populateDoctorGrid(data.doctors, doctorsContainer);
            }
            
            // Populate doctor select dropdown
            if (doctorSelect) {
                populateDoctorSelect(data.doctors, doctorSelect);
            }
        })
        .catch(error => {
            console.error('Error loading doctors data:', error);
            if (doctorsContainer) {
                doctorsContainer.innerHTML = '<p class="text-error">Failed to load doctors data. Please refresh the page.</p>';
            }
        });
}

/**
 * Populate doctor grid with doctor cards
 */
function populateDoctorGrid(doctors, container) {
    container.innerHTML = '';
    
    doctors.forEach(doctor => {
        const doctorCard = createDoctorCard(doctor);
        container.appendChild(doctorCard);
    });
}

/**
 * Create a doctor card element
 */
function createDoctorCard(doctor) {
    const card = document.createElement('div');
    card.className = 'card doctor-card';
    
    // Create a placeholder image (using a neutral placeholder)
    const imageHtml = `<div class="doctor-image-wrapper">
        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='300' height='300'%3E%3Crect fill='%231a1f2e' width='300' height='300'/%3E%3Ccircle cx='150' cy='100' r='40' fill='%2300a99d'/%3E%3Cpath d='M130 150c0-20 20-35 40-35s40 15 40 35v30H130z' fill='%2300a99d'/%3E%3C/svg%3E" alt="${doctor.name}">
    </div>`;
    
    card.innerHTML = `
        ${imageHtml}
        <h3>${doctor.name}</h3>
        <p class="doctor-specialty">${doctor.specialty}</p>
        <p class="doctor-experience">
            <i class="icon">★</i> ${doctor.experience} years experience
        </p>
        <p>${doctor.bio}</p>
        <div class="doctor-contact">
            <button class="doctor-contact-btn" title="Call" onclick="window.location.href='tel:${doctor.phone}'">
                ☎️
            </button>
            <button class="doctor-contact-btn" title="Email" onclick="window.location.href='mailto:${doctor.email}'">
                ✉️
            </button>
        </div>
    `;
    
    return card;
}

/**
 * Populate doctor select dropdown
 */
function populateDoctorSelect(doctors, select) {
    // Clear existing options except placeholder
    select.innerHTML = '<option value="">-- Select a Doctor --</option>';
    
    doctors.forEach(doctor => {
        const option = document.createElement('option');
        option.value = doctor.id;
        option.textContent = `${doctor.name} - ${doctor.specialty}`;
        select.appendChild(option);
    });
}

// ==================== LOCALSTORAGE HELPERS ====================

/**
 * Get appointments from localStorage
 */
function getAppointmentsFromStorage() {
    const data = localStorage.getItem(STORAGE_KEY);
    return data ? JSON.parse(data) : [];
}

/**
 * Get a specific appointment by ID
 */
function getAppointmentById(id) {
    const appointments = getAppointmentsFromStorage();
    return appointments.find(apt => apt.id === id);
}

/**
 * Delete an appointment
 */
function deleteAppointment(id) {
    const appointments = getAppointmentsFromStorage();
    const filtered = appointments.filter(apt => apt.id !== id);
    localStorage.setItem(STORAGE_KEY, JSON.stringify(filtered));
}

// ==================== EMERGENCY FUNCTIONS ====================

/**
 * Handle emergency call button
 */
function handleEmergencyCall() {
    const emergencyNumber = '911';
    
    // Try to initiate call
    window.location.href = `tel:${emergencyNumber}`;
    
    // Show alert (for web browsers that can't make calls)
    alert(`Emergency Number: ${emergencyNumber}\n\nPlease call immediately for emergency assistance.`);
}

// ==================== UTILITY FUNCTIONS ====================

/**
 * Format date to readable format
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

/**
 * Format time to readable format
 */
function formatTime(timeString) {
    const [hours, minutes] = timeString.split(':');
    const hour = parseInt(hours, 10);
    const ampm = hour >= 12 ? 'PM' : 'AM';
    const displayHour = hour % 12 || 12;
    return `${displayHour}:${minutes} ${ampm}`;
}

/**
 * Get minimum date for date input (today)
 */
function setMinDateForAppointment() {
    const dateInput = document.querySelector('[name="appointment-date"]');
    if (dateInput) {
        const today = new Date().toISOString().split('T')[0];
        dateInput.setAttribute('min', today);
    }
}

// Call function on page load
document.addEventListener('DOMContentLoaded', setMinDateForAppointment);

// ==================== SCROLL ANIMATIONS ====================

/**
 * Add scroll animation on elements
 */
function addScrollAnimations() {
    const cards = document.querySelectorAll('.card, .service-card');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    });
    
    cards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'all 0.6s ease-out';
        observer.observe(card);
    });
}

// Initialize scroll animations
document.addEventListener('DOMContentLoaded', addScrollAnimations);
