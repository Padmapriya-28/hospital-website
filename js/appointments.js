/**
 * Appointments Form Handler
 * Handles appointment booking form submission and doctor loading
 */

// Load doctors when page loads
document.addEventListener('DOMContentLoaded', function() {
    loadDoctors();
    setupFormHandler();
});

/**
 * Load doctors from API
 */
function loadDoctors() {
    fetch('./api/doctors.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success' && data.data) {
                const doctorSelect = document.getElementById('doctor');
                doctorSelect.innerHTML = '<option value="">-- Select a Doctor --</option>';
                
                data.data.forEach(doctor => {
                    const option = document.createElement('option');
                    option.value = doctor.name;
                    option.textContent = `${doctor.name} - ${doctor.specialty}`;
                    doctorSelect.appendChild(option);
                });
            }
        })
        .catch(error => console.error('Error loading doctors:', error));
}

/**
 * Setup appointment form submission handler
 */
function setupFormHandler() {
    const appointmentForm = document.getElementById('appointmentForm');
    
    if (appointmentForm) {
        appointmentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitAppointment();
        });
    }
}

/**
 * Submit appointment form to API
 */
function submitAppointment() {
    const form = document.getElementById('appointmentForm');
    
    // Get form data
    const appointmentData = {
        patient_name: document.getElementById('patient-name').value,
        patient_email: document.getElementById('patient-email').value,
        patient_phone: document.getElementById('patient-phone').value,
        doctor: document.getElementById('doctor').value,
        appointment_date: document.getElementById('appointment-date').value,
        appointment_time: document.getElementById('appointment-time').value,
        message: document.getElementById('message').value
    };
    
    // Validate data
    if (!appointmentData.patient_name || !appointmentData.patient_email || 
        !appointmentData.patient_phone || !appointmentData.doctor || 
        !appointmentData.appointment_date || !appointmentData.appointment_time) {
        showAlert('Please fill in all required fields', 'error');
        return;
    }
    
    // Show loading state
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.textContent = '⏳ Processing...';
    
    // Send to API
    fetch('./api/appointments.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(appointmentData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Save to localStorage for backup
            const appointments = JSON.parse(localStorage.getItem('appointments')) || [];
            appointments.push({
                id: data.id,
                ...appointmentData,
                status: 'pending',
                created_at: new Date().toISOString()
            });
            localStorage.setItem('appointments', JSON.stringify(appointments));
            
            // Show success message
            showAlert(`✅ Appointment booked successfully! ID: ${data.id}. A confirmation email has been sent.`, 'success');
            
            // Reset form
            form.reset();
            loadDoctors();
        } else {
            const errorMessage = data.errors ? data.errors.join(', ') : data.message || 'Failed to book appointment';
            showAlert(errorMessage, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while booking the appointment', 'error');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    });
}

/**
 * Show alert message
 */
function showAlert(message, type = 'info') {
    // Create alert element
    const alertDiv = document.createElement('div');
    alertDiv.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        background-color: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
        color: white;
        font-weight: 500;
        z-index: 9999;
        max-width: 400px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        animation: slideIn 0.3s ease-out;
    `;
    
    alertDiv.textContent = message;
    document.body.appendChild(alertDiv);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        alertDiv.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => alertDiv.remove(), 300);
    }, 5000);
}

// Add animation styles
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
