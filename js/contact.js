/**
 * Contact Form Handler
 * Handles contact form submission
 */

document.addEventListener('DOMContentLoaded', function() {
    setupContactFormHandler();
});

/**
 * Setup contact form submission handler
 */
function setupContactFormHandler() {
    const contactForm = document.getElementById('contactForm');
    
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitContactForm();
        });
    }
}

/**
 * Submit contact form to API
 */
function submitContactForm() {
    const form = document.getElementById('contactForm');
    
    // Get form data
    const contactData = {
        'full-name': document.getElementById('full-name').value,
        'email': document.getElementById('email').value,
        'phone': document.getElementById('phone').value,
        'subject': document.getElementById('subject').value,
        'message': document.getElementById('message').value
    };
    
    // Validate data
    if (!contactData['full-name'] || !contactData['email'] || 
        !contactData['phone'] || !contactData['subject'] || 
        !contactData['message']) {
        showContactAlert('Please fill in all required fields', 'error');
        return;
    }
    
    // Show loading state
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.textContent = '⏳ Sending...';
    
    // Send to API
    fetch('./api/contact.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(contactData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showContactAlert('✅ ' + data.message, 'success');
            form.reset();
        } else {
            const errorMessage = data.errors ? data.errors.join(', ') : data.message || 'Failed to submit form';
            showContactAlert(errorMessage, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showContactAlert('An error occurred while submitting the form', 'error');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    });
}

/**
 * Show alert message
 */
function showContactAlert(message, type = 'info') {
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
    
    setTimeout(() => {
        alertDiv.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => alertDiv.remove(), 300);
    }, 5000);
}
