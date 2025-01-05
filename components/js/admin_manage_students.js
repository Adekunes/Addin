// admin_manage_students.js

// Global functions need to be defined before DOMContentLoaded
function openEditModal(studentId) {
    const modal = document.getElementById('editStudentModal');
    fetch(`../../../model/auth/admin_auth.php?action=get_student&id=${studentId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('studentId').value = data.id;
            document.getElementById('studentName').value = data.name;
            document.getElementById('currentJuz').value = data.current_juz;
            document.getElementById('completedJuz').value = data.completed_juz;
            document.getElementById('status').value = data.status;
            
            modal.style.display = "block";
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error', 'Failed to load student data', 'error');
        });
}

function closeModal() {
    const modal = document.getElementById('editStudentModal');
    modal.style.display = "none";
}

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

document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('editStudentModal');
    const span = document.getElementsByClassName('close')[0];

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            closeModal();
        }
    }

    // Handle form submission
    document.getElementById('editStudentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const data = Object.fromEntries(formData);
        
        fetch('../../../model/auth/admin_auth.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'update_student',
                ...data
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Success', 'Student updated successfully', 'success');
                closeModal();
                location.reload();
            } else {
                showNotification('Error', data.message || 'Failed to update student', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error', 'An unexpected error occurred', 'error');
        });
    });
});