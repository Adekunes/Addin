// admin_manage_students.js

// Global functions need to be defined before DOMContentLoaded
window.editStudent = function(studentData) {
    console.log('Edit student called with:', studentData); // Debug log
    const modal = document.getElementById('editStudentModal');
    if (!modal) {
        console.error('Modal not found');
        return;
    }

    // Populate the form fields
    document.getElementById('studentId').value = studentData.id;
    document.getElementById('currentJuz').value = studentData.current_juz || '1';
    document.getElementById('completedJuz').value = studentData.completed_juz || '0';
    document.getElementById('lastCompletedSurah').value = studentData.last_completed_surah || '';
    document.getElementById('memorizationQuality').value = studentData.memorization_quality || 'average';
    document.getElementById('tajweedLevel').value = studentData.tajweed_level || 'beginner';
    document.getElementById('revisionStatus').value = studentData.revision_status || 'onTrack';
    document.getElementById('teacherNotes').value = studentData.teacher_notes || '';

    // Display the modal
    modal.style.display = 'block';
};

function closeModal() {
    const modal = document.getElementById('editStudentModal');
    if (modal) {
        modal.style.display = 'none';
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

// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Add click event listeners for edit and delete buttons
    document.addEventListener('click', async function(e) {
        if (e.target.closest('.btn-icon.edit')) {
            const button = e.target.closest('.btn-icon.edit');
            const studentId = button.dataset.studentId;
            openEditModal(studentId);
        }
        
        if (e.target.closest('.btn-icon.delete')) {
            const button = e.target.closest('.btn-icon.delete');
            const studentId = button.dataset.studentId;
            
            if (confirm('Are you sure you want to delete this student?')) {
                try {
                    const response = await fetch('../../../model/auth/process_student.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `action=delete_student&student_id=${studentId}`
                    });

                    const result = await response.json();
                    
                    if (result.success) {
                        // Remove the row from the table
                        button.closest('tr').remove();
                        showNotification('Success', 'Student deleted successfully', 'success');
                    } else {
                        showNotification('Error', result.message || 'Failed to delete student', 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showNotification('Error', 'An unexpected error occurred', 'error');
                }
            }
        }
    });

    // Get modal elements
    const modal = document.getElementById('editStudentModal');
    const closeBtn = document.querySelector('.modal .close');
    const form = document.getElementById('editStudentForm');

    // Close modal when clicking the X
    if (closeBtn) {
        closeBtn.onclick = function() {
            modal.style.display = 'none';
        }
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }

    // Handle form submission
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch('../../../model/auth/process_student.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Student updated successfully');
                    closeModal();
                    window.location.reload();
                } else {
                    alert('Failed to update student: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to update student');
            });
        });
    }
});

// Function to open modal and populate form
async function openEditModal(studentId) {
    try {
        console.log('Opening modal for student ID:', studentId);
        const modal = document.getElementById('editStudentModal');
        if (!modal) {
            console.error('Modal not found');
            return;
        }

        const response = await fetch(`../../../model/auth/process_student.php?action=get_student&id=${studentId}`);
        if (!response.ok) {
            throw new Error('Failed to fetch student data');
        }
        
        const studentData = await response.json();
        console.log('Fetched student data:', studentData);

        // Populate the form fields
        document.getElementById('studentId').value = studentData.id;
        document.getElementById('surahName').value = studentData.surah_name || '';
        document.getElementById('verseStart').value = studentData.verse_start || '';
        document.getElementById('verseEnd').value = studentData.verse_end || '';
        document.getElementById('linesMemorized').value = studentData.lines_memorized || '0';
        document.getElementById('lessonDate').value = studentData.lesson_date || new Date().toISOString().split('T')[0];
        document.getElementById('memorizationQuality').value = studentData.memorization_quality || 'average';
        document.getElementById('tajweedLevel').value = studentData.tajweed_level || 'beginner';
        document.getElementById('teacherNotes').value = studentData.teacher_notes || '';

        modal.style.display = 'block';
    } catch (error) {
        console.error('Error in openEditModal:', error);
        showNotification('Error', 'Failed to load student data', 'error');
    }
}

// Function to delete student
function deleteStudent(studentId) {
    if (confirm('Are you sure you want to delete this student?')) {
        fetch('../../../model/auth/process_student.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=delete_student&student_id=${studentId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Failed to delete student: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to delete student');
        });
    }
}