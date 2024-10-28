// admin_manage_teachers.js

document.addEventListener('DOMContentLoaded', function() {
    // Initialize event listeners for the Add Teacher button
    const addTeacherBtn = document.getElementById('addTeacherBtn');
    if (addTeacherBtn) {
        addTeacherBtn.addEventListener('click', () => {
            window.location.href = '../../View/html/admin/add_new_teacher.php';
        });
    }

    // Initialize DataTable if present
    if ($.fn.DataTable) {
        $('#teachersTable').DataTable({
            responsive: true,
            order: [[0, 'asc']],
            language: {
                search: "Search teachers:",
                emptyTable: "No teachers found"
            }
        });
    }
});

// CRUD Operations
function viewTeacher(teacherId) {
    window.location.href = `../../View/html/admin/view_teacher.php?id=${teacherId}`;
}

function editTeacher(teacherId) {
    window.location.href = `../../View/html/admin/add_new_teacher.php?id=${teacherId}&mode=edit`;
}

function deleteTeacher(teacherId) {
    if (confirm('Are you sure you want to delete this teacher?')) {
        fetch('../../model/auth/admin_auth.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=delete_teacher&teacher_id=${teacherId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Refresh the table
                location.reload();
                showNotification('Success', 'Teacher deleted successfully', 'success');
            } else {
                showNotification('Error', data.message || 'Failed to delete teacher', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error', 'An unexpected error occurred', 'error');
        });
    }
}

// Form validation for add/edit teacher
function validateTeacherForm() {
    const form = document.getElementById('teacherForm');
    if (!form) return true;

    const requiredFields = [
        'firstName',
        'lastName',
        'email',
        'specialization'
    ];

    let isValid = true;
    requiredFields.forEach(field => {
        const input = form[field];
        if (!input.value.trim()) {
            markFieldAsInvalid(input);
            isValid = false;
        } else {
            markFieldAsValid(input);
        }
    });

    return isValid;
}

// Utility functions
function showNotification(title, message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        <strong>${title}:</strong> ${message}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    `;
    document.querySelector('.main-content').prepend(alertDiv);

    setTimeout(() => alertDiv.remove(), 3000);
}

function markFieldAsInvalid(input) {
    input.classList.add('is-invalid');
    const feedback = document.createElement('div');
    feedback.className = 'invalid-feedback';
    feedback.textContent = 'This field is required';
    input.parentNode.appendChild(feedback);
}

function markFieldAsValid(input) {
    input.classList.remove('is-invalid');
    const feedback = input.parentNode.querySelector('.invalid-feedback');
    if (feedback) feedback.remove();
}