// admin_manage_students.js

document.addEventListener('DOMContentLoaded', function() {
    // Initialize event listeners for the Add Student button
    const addStudentBtn = document.getElementById('addStudentBtn');
    if (addStudentBtn) {
        addStudentBtn.addEventListener('click', () => {
            window.location.href = '../../View/html/admin/add_new_student.php';
        });
    }

    // Initialize DataTable if present
    if ($.fn.DataTable) {
        $('#studentsTable').DataTable({
            responsive: true,
            order: [[0, 'asc']],
            language: {
                search: "Search students:",
                emptyTable: "No students found"
            }
        });
    }
});

// CRUD Operations
function viewStudent(studentId) {
    window.location.href = `../../View/html/admin/view_student.php?id=${studentId}`;
}

function editStudent(studentId) {
    window.location.href = `../../View/html/admin/add_new_student.php?id=${studentId}&mode=edit`;
}

function deleteStudent(studentId) {
    if (confirm('Are you sure you want to delete this student?')) {
        fetch('../../model/auth/admin_auth.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=delete_student&student_id=${studentId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Refresh the table
                location.reload();
                showNotification('Success', 'Student deleted successfully', 'success');
            } else {
                showNotification('Error', data.message || 'Failed to delete student', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error', 'An unexpected error occurred', 'error');
        });
    }
}

// Utility functions
function showNotification(title, message, type) {
    // Add your preferred notification library here
    // Example using bootstrap toast or alert
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        <strong>${title}:</strong> ${message}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    `;
    document.querySelector('.main-content').prepend(alertDiv);

    // Auto dismiss after 3 seconds
    setTimeout(() => alertDiv.remove(), 3000);
}