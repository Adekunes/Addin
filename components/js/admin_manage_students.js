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

    // Handle revision form submission
    const revisionForm = document.getElementById('revisionForm');
    if (revisionForm) {
        revisionForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            try {
                const formData = new FormData(this);
                // Debug log the form data
                const formDataObj = {};
                formData.forEach((value, key) => {
                    formDataObj[key] = value;
                });
                console.log('Form data being submitted:', formDataObj);
                
                const response = await fetch('../../../model/auth/process_student.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                console.log('Server response:', result);
                
                if (result.success) {
                    alert('Revision recorded successfully!');
                    window.location.reload();
                } else {
                    alert('Error: ' + (result.message || 'Failed to record revision'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while recording the revision');
            }
        });
    }

    // Search functionality
    const searchInput = document.getElementById('studentSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const tableRows = document.querySelectorAll('table tbody tr:not(.no-results)');
            const noResultsRow = document.querySelector('.no-results');
            
            tableRows.forEach(row => {
                const name = row.querySelector('.user-name')?.textContent.toLowerCase() || '';
                const guardian = row.querySelector('.user-details')?.textContent.toLowerCase() || '';
                const juz = row.querySelector('.current-juz')?.textContent.toLowerCase() || '';
                const quality = row.querySelector('.quality-badge')?.textContent.toLowerCase() || '';
                const tajweed = row.querySelector('td:nth-child(8)')?.textContent.toLowerCase() || '';
                
                const matchesSearch = 
                    name.includes(searchTerm) || 
                    guardian.includes(searchTerm) || 
                    juz.includes(searchTerm) || 
                    quality.includes(searchTerm) ||
                    tajweed.includes(searchTerm);
                
                row.style.display = matchesSearch ? '' : 'none';
            });

            // Show "No matching students" message only when searching with no results
            if (noResultsRow) {
                const visibleRows = Array.from(tableRows).filter(row => row.style.display !== 'none');
                noResultsRow.style.display = searchTerm && visibleRows.length === 0 ? '' : 'none';
            }
        });

        // Add clear search button
        const searchContainer = searchInput.parentElement;
        const clearButton = document.createElement('button');
        clearButton.className = 'clear-search';
        clearButton.innerHTML = '&times;';
        clearButton.style.display = 'none';
        searchContainer.appendChild(clearButton);

        clearButton.addEventListener('click', () => {
            searchInput.value = '';
            searchInput.dispatchEvent(new Event('input'));
            clearButton.style.display = 'none';
        });

        searchInput.addEventListener('input', () => {
            clearButton.style.display = searchInput.value ? 'block' : 'none';
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
        
        const result = await response.json();
        console.log('Fetched student data:', result);

        if (!result.success) {
            throw new Error(result.message || 'Failed to fetch student data');
        }

        const studentData = result.data;

        // Populate both forms with student ID
        document.querySelectorAll('#studentId').forEach(input => {
            input.value = studentId;
        });

        // Populate the form fields
        if (document.getElementById('currentJuz')) {
            document.getElementById('currentJuz').value = studentData.current_juz || '1';
        }
        if (document.getElementById('masteryLevel')) {
            document.getElementById('masteryLevel').value = studentData.mastery_level || 'not_started';
        }
        document.getElementById('verseStart').value = studentData.verse_start || '';
        document.getElementById('verseEnd').value = studentData.verse_end || '';
        document.getElementById('linesMemorized').value = studentData.lines_memorized || '0';
        document.getElementById('memorizationQuality').value = studentData.memorization_quality || 'average';
        document.getElementById('tajweedLevel').value = studentData.tajweed_level || 'beginner';
        document.getElementById('teacherNotes').value = studentData.teacher_notes || '';

        // Load history when modal opens
        await loadStudentHistory(studentId);

        modal.style.display = 'block';
    } catch (error) {
        console.error('Error in openEditModal:', error);
        alert('Error: ' + error.message);
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

async function loadStudentHistory(studentId) {
    try {
        const response = await fetch(`../../../model/auth/process_student.php?action=get_history&id=${studentId}`);
        if (!response.ok) throw new Error('Failed to fetch history');
        
        const data = await response.json();
        if (!data.success) throw new Error(data.message);
        
        // Populate revision history
        const revisionBody = document.getElementById('revisionHistoryBody');
        revisionBody.innerHTML = data.data.revisions.length ? 
            data.data.revisions.map(rev => `
                <tr>
                    <td>${new Date(rev.revision_date).toLocaleDateString()}</td>
                    <td>Juz ${rev.juz_revised}</td>
                    <td><span class="quality-indicator quality-${rev.quality_rating.toLowerCase()}">${rev.quality_rating}</span></td>
                    <td>${rev.teacher_notes || '-'}</td>
                </tr>
            `).join('') :
            '<tr><td colspan="4" class="text-center">No revision history available</td></tr>';

        // Populate memorization progress
        const memorizationBody = document.getElementById('memorizationHistoryBody');
        memorizationBody.innerHTML = data.data.progress.map(prog => `
            <tr>
                <td>Juz ${prog.juz_number}</td>
                <td>${formatMasteryLevel(prog.mastery_level)}</td>
                <td>${prog.last_revision_date ? new Date(prog.last_revision_date).toLocaleDateString() : 'Never'}</td>
                <td>${prog.total_revisions}</td>
            </tr>
        `).join('');
    } catch (error) {
        console.error('Error loading history:', error);
        alert('Failed to load student history');
    }
}

function formatMasteryLevel(level) {
    const levels = {
        'not_started': 'Not Started',
        'in_progress': 'In Progress',
        'memorized': 'Memorized',
        'mastered': 'Mastered'
    };
    return levels[level] || level;
}