document.addEventListener('DOMContentLoaded', function() {
    const teacherForm = document.getElementById('teacherForm');
    const isEditMode = teacherForm.querySelector('input[name="action"]').value === 'update_teacher';

    // Form validation and submission
    teacherForm.addEventListener('submit', async function(e) {
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
                showNotification('Success', 'Teacher has been ' + (isEditMode ? 'updated' : 'added'), 'success');
                setTimeout(() => {
                    window.location.href = 'manage_teachers.php';
                }, 1500);
            } else {
                showNotification('Error', result.message, 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('Error', 'An unexpected error occurred', 'error');
        }
    });

    // Specialization dependent fields
    const specializationSelect = document.getElementById('specialization');
    specializationSelect.addEventListener('change', function() {
        updateSpecializationFields(this.value);
    });

    function updateSpecializationFields(specialization) {
        const certificationsField = document.getElementById('certifications');
        const placeholder = getSpecializationPlaceholder(specialization);
        certificationsField.placeholder = placeholder;
    }

    function getSpecializationPlaceholder(specialization) {
        const placeholders = {
            'hifz': 'List Hifz teaching certifications...',
            'tajweed': 'List Tajweed certifications and credentials...',
            'qirat': 'List Qirat specializations and certificates...',
            '': 'List any relevant certifications...'
        };
        return placeholders[specialization] || placeholders[''];
    }

    function validateForm() {
        let isValid = true;
        const requiredFields = teacherForm.querySelectorAll('[required]');
        
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

        // Phone validation
        const phone = document.getElementById('phone');
        if (phone.value && !validatePhone(phone.value)) {
            isValid = false;
            showFieldError(phone, 'Please enter a valid phone number');
        }

        // Email validation
        const email = document.getElementById('email');
        if (!validateEmail(email.value)) {
            isValid = false;
            showFieldError(email, 'Please enter a valid email address');
        }

        // Check if at least one class is selected
        const classCheckboxes = document.querySelectorAll('input[name="classes[]"]:checked');
        if (classCheckboxes.length === 0) {
            isValid = false;
            showNotification('Error', 'Please assign at least one class', 'error');
        }

        return isValid;
    }

    // Utility functions (same as in admin_add_student.js)
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