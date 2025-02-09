document.addEventListener('DOMContentLoaded', function() {
    const studentForm = document.getElementById('studentForm');
    const isEditMode = studentForm.querySelector('input[name="action"]').value === 'update_student';

    // Form validation and submission
    studentForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        if (!validateForm()) {
            return;
        }

        try {
            const formData = new FormData(this);
            const response = await fetch('../../../model/auth/admin_auth.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            
            if (result.success) {
                showNotification('Success', 'Student has been ' + (isEditMode ? 'updated' : 'added'), 'success');
                setTimeout(() => {
                    window.location.href = 'manage_students.php';
                }, 1500);
            } else {
                showNotification('Error', result.message, 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('Error', 'An unexpected error occurred', 'error');
        }
    });

    function validateForm() {
        let isValid = true;
        const requiredFields = studentForm.querySelectorAll('[required]');
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('invalid');
                showFieldError(field, 'This field is required');
            } else {
                field.classList.remove('invalid');
                removeFieldError(field);
            }
        });

        // Phone number validation
        const guardianPhone = document.getElementById('guardianPhone');
        if (guardianPhone.value && !validatePhone(guardianPhone.value)) {
            isValid = false;
            showFieldError(guardianPhone, 'Please enter a valid phone number');
        }

        // Email validation (if provided)
        const email = document.getElementById('email');
        if (email.value && !validateEmail(email.value)) {
            isValid = false;
            showFieldError(email, 'Please enter a valid email address');
        }

        return isValid;
    }

    // Utility functions
    function validatePhone(phone) {
        return /^\+?[\d\s-]{10,}$/.test(phone);
    }

    function validateEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    function showFieldError(field, message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'field-error';
        errorDiv.textContent = message;
        field.parentNode.appendChild(errorDiv);
    }

    function removeFieldError(field) {
        const errorDiv = field.parentNode.querySelector('.field-error');
        if (errorDiv) {
            errorDiv.remove();
        }
    }

    function showNotification(title, message, type) {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.innerHTML = `
            <strong>${title}:</strong> ${message}
            <button onclick="this.parentElement.remove()">&times;</button>
        `;
        document.body.appendChild(notification);
        setTimeout(() => notification.remove(), 5000);
    }
});