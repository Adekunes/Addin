// admin_manage_students.js

// Global functions need to be defined before DOMContentLoaded
window.editStudent = function(studentId) {
    if (!studentId) {
        console.error('No student ID provided to editStudent function');
        return;
    }
    
    console.log('editStudent called with ID:', studentId);
    openEditModal(studentId);
};

// Close modal function
window.closeModal = function() {
    const modal = document.getElementById('editStudentModal');
    if (modal) {
        modal.style.display = 'none';
        window.isEditingStudent = false;
    }
};

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('editStudentModal');
    if (event.target === modal) {
        closeModal();
    }
};

function showNotification(title, message, type = 'info') {
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
    console.log('Setting up event listeners');
    
    const currentJuzSelect = document.getElementById('currentJuz');
    const currentSurahSelect = document.getElementById('currentSurah');
    
    if (currentJuzSelect) {
        currentJuzSelect.addEventListener('change', async function() {
            console.log('Juz changed to:', this.value);
            const juzNumber = this.value;
            const surahSelect = document.getElementById('currentSurah');
            
            // Clear existing options
            surahSelect.innerHTML = '<option value="">Select Surah</option>';
            document.getElementById('startAyat').innerHTML = '<option value="">Select Starting Ayat</option>';
            document.getElementById('endAyat').innerHTML = '<option value="">Select Ending Ayat</option>';
            
            if (juzNumber) {
                try {
                    console.log('Fetching surahs for juz:', juzNumber);
                    const response = await fetch(`../../../model/auth/process_student.php?action=get_surahs_by_juz&juz=${juzNumber}`);
                    const data = await response.json();
                    console.log('Received surahs:', data);
                    
                    if (data.success && data.surahs) {
                        data.surahs.forEach(surah => {
                            const option = document.createElement('option');
                            option.value = surah.surah_id;
                            option.dataset.startAyat = surah.start_ayat;
                            option.dataset.endAyat = surah.end_ayat;
                            option.textContent = `${surah.name} (Ayat ${surah.start_ayat}-${surah.end_ayat})`;
                            surahSelect.appendChild(option);
                        });
                        console.log('Added surah options:', surahSelect.options.length);
                    }
                } catch (error) {
                    console.error('Error fetching surahs:', error);
                }
            }
        });
    } else {
        console.error('Current Juz select not found');
    }
    
    if (currentSurahSelect) {
        const startAyatSelect = document.getElementById('startAyat');
        const endAyatSelect = document.getElementById('endAyat');
        
        console.log('Elements found:', {
            currentSurahSelect: !!currentSurahSelect,
            startAyatSelect: !!startAyatSelect,
            endAyatSelect: !!endAyatSelect
        });
        
        if (currentSurahSelect && startAyatSelect && endAyatSelect) {
            // Add click event listeners to ayat selects
            startAyatSelect.addEventListener('click', function(e) {
                console.log('Start ayat clicked');
                console.log('Current options:', this.options.length);
                if (this.options.length <= 1) {
                    populateAyatOptions(currentSurahSelect, startAyatSelect, endAyatSelect);
                }
            });
            
            endAyatSelect.addEventListener('click', function(e) {
                console.log('End ayat clicked');
                console.log('Current options:', this.options.length);
                if (this.options.length <= 1) {
                    populateAyatOptions(currentSurahSelect, startAyatSelect, endAyatSelect);
                }
            });
            
            // Also add change event to surah select to update ayat ranges
            currentSurahSelect.addEventListener('change', function() {
                console.log('Surah changed to:', this.value);
                startAyatSelect.innerHTML = '<option value="">Select Starting Ayat</option>';
                endAyatSelect.innerHTML = '<option value="">Select Ending Ayat</option>';
            });
        } else {
            console.error('One or more required elements not found');
        }
    } else {
        console.error('Current Surah select not found');
    }

    // Initialize DataTable
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

    // Add click event listeners for edit and delete buttons
    document.addEventListener('click', async function(e) {
        const editButton = e.target.closest('.btn-icon.edit');
        if (editButton) {
            const studentId = editButton.getAttribute('data-student-id');
            console.log('Edit button clicked, student ID:', studentId);
            if (studentId) {
                editStudent(studentId);
            } else {
                console.error('No student ID found on edit button');
            }
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
            closeModal();
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

    // Handle tab switching
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const tab = button.dataset.tab;
            
            // Update active states
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));
            
            button.classList.add('active');
            document.querySelector(`.tab-content[data-tab="${tab}"]`).classList.add('active');
        });
    });

    // Handle form submission
    const memorizationForm = document.getElementById('memorizationForm');
    if (memorizationForm) {
        memorizationForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            try {
                const formData = new FormData(this);
                formData.append('action', 'update_student');
                
                // Add start and end ayat to form data
                const startAyat = document.getElementById('startAyat').value;
                const endAyat = document.getElementById('endAyat').value;
                formData.append('start_ayat', startAyat);
                formData.append('end_ayat', endAyat);
                
                // Log form data for debugging
                const formDataObj = {};
                formData.forEach((value, key) => {
                    formDataObj[key] = value;
                });
                console.log('Submitting form data:', formDataObj);
                
                const response = await fetch('../../../model/auth/process_student.php', {
                    method: 'POST',
                    body: formData
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const result = await response.json();
                console.log('Server response:', result);
                
                if (result.success) {
                    showNotification('Success', 'Student updated successfully', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    throw new Error(result.message || 'Failed to update student');
                }
            } catch (error) {
                console.error('Error saving changes:', error);
                showNotification('Error', error.message || 'Failed to update student', 'error');
            }
        });
    }

    // Add this to your existing JavaScript
    document.getElementById('currentSurah').addEventListener('change', function() {
        const surahSelect = this;
        const startAyatSelect = document.getElementById('startAyat');
        const endAyatSelect = document.getElementById('endAyat');
        
        // Clear existing options
        startAyatSelect.innerHTML = '<option value="">Select Starting Ayat</option>';
        endAyatSelect.innerHTML = '<option value="">Select Ending Ayat</option>';
        
        if (surahSelect.value) {
            const totalAyat = parseInt(surahSelect.selectedOptions[0].dataset.ayat);
            
            // Populate ayat options
            for (let i = 1; i <= totalAyat; i++) {
                const startOption = document.createElement('option');
                startOption.value = i;
                startOption.textContent = `Ayat ${i}`;
                startAyatSelect.appendChild(startOption);
                
                const endOption = document.createElement('option');
                endOption.value = i;
                endOption.textContent = `Ayat ${i}`;
                endAyatSelect.appendChild(endOption);
            }
        }
    });

    // Add validation for ayat selection
    document.getElementById('startAyat').addEventListener('change', function() {
        const startAyat = parseInt(this.value);
        const endAyatSelect = document.getElementById('endAyat');
        const options = endAyatSelect.options;
        
        // Disable options before start ayat
        for (let i = 0; i < options.length; i++) {
            const optionValue = parseInt(options[i].value);
            if (optionValue && optionValue < startAyat) {
                options[i].disabled = true;
            } else {
                options[i].disabled = false;
            }
        }
    });
});

// Function to open modal and populate form
async function openEditModal(studentId) {
    try {
        if (!studentId) {
            throw new Error('Student ID is required');
        }
        
        console.log('Opening modal for student ID:', studentId);
        
        // Show the modal first
        const modal = document.getElementById('editStudentModal');
        if (!modal) {
            throw new Error('Modal element not found');
        }
        modal.style.display = 'block';

        // Fetch student data
        const response = await fetch(`../../../model/auth/process_student.php?action=get_student&id=${studentId}`);
        const data = await response.json();
        
        console.log('Received student data:', data);

        if (!data.success) {
            throw new Error(data.message || 'Failed to fetch student data');
        }

        // Get the latest progress data
        const progressResponse = await fetch(`../../../model/auth/process_student.php?action=get_latest_progress&student_id=${studentId}`);
        const progressData = await progressResponse.json();
        
        console.log('Received progress data:', progressData);

        // Set student ID in form
        const studentIdInputs = document.querySelectorAll('input[name="student_id"]');
        studentIdInputs.forEach(input => {
            input.value = studentId;
        });

        // Set current juz and trigger change event
        const currentJuzSelect = document.getElementById('currentJuz');
        if (currentJuzSelect && progressData.success && progressData.progress) {
            currentJuzSelect.value = progressData.progress.current_juz;
            // Trigger the change event manually
            const event = new Event('change');
            currentJuzSelect.dispatchEvent(event);
            console.log('Triggered juz change event for:', progressData.progress.current_juz);
        }

        // Set memorization quality
        const memorizationQuality = document.getElementById('memorizationQuality');
        if (memorizationQuality) {
            const quality = progressData.success ? progressData.progress.memorization_quality : 'average';
            memorizationQuality.value = quality;
            console.log('Set memorizationQuality to:', quality);
        }

        // Set teacher notes
        const teacherNotes = document.getElementById('teacherNotes');
        if (teacherNotes) {
            teacherNotes.value = progressData.success ? (progressData.progress.teacher_notes || '') : '';
            console.log('Set teacherNotes');
        }

        // Set current surah and ayat if available
        if (progressData.success && progressData.progress) {
            const currentSurah = document.getElementById('currentSurah');
            if (currentSurah && progressData.progress.current_surah) {
                currentSurah.value = progressData.progress.current_surah;
                currentSurah.dispatchEvent(new Event('change')); // Trigger ayat population
                
                // Set start and end ayat if available
                if (progressData.progress.start_ayat) {
                    document.getElementById('startAyat').value = progressData.progress.start_ayat;
                    document.getElementById('startAyat').dispatchEvent(new Event('change'));
                }
                if (progressData.progress.end_ayat) {
                    document.getElementById('endAyat').value = progressData.progress.end_ayat;
                }
            }
        }

        // Load revision history
        await loadStudentHistory(studentId);

    } catch (error) {
        console.error('Error in openEditModal:', error);
        alert(error.message || 'Failed to load student data');
        closeModal();
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
        console.log('Loading history for student:', studentId);
        
        const response = await fetch(`../../../model/auth/process_student.php?action=get_student_history&student_id=${studentId}`);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('Received history data:', data);

        const historyBody = document.getElementById('revisionHistoryBody');
        if (!historyBody) {
            console.error('History table body element not found');
            return;
        }

        if (data.success && data.revisions && data.revisions.length > 0) {
            historyBody.innerHTML = data.revisions.map(revision => {
                const quality = revision.quality_rating || 'average';
                return `
                    <tr>
                        <td>${new Date(revision.revision_date).toLocaleDateString()}</td>
                        <td>Juz ${revision.juz_revised}</td>
                        <td>
                            <span class="quality-indicator quality-${quality.toLowerCase()}">
                                ${quality}
                            </span>
                        </td>
                        <td>${revision.teacher_notes || '-'}</td>
                    </tr>
                `;
            }).join('');
        } else {
            historyBody.innerHTML = '<tr><td colspan="4" class="text-center">No revision history available</td></tr>';
        }

    } catch (error) {
        console.error('Error loading student history:', error);
        const historyBody = document.getElementById('revisionHistoryBody');
        if (historyBody) {
            historyBody.innerHTML = '<tr><td colspan="4" class="text-center text-error">Failed to load revision history</td></tr>';
        }
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

function viewStudent(studentId) {
    // Open the modal in view mode
    const modal = document.getElementById('editStudentModal');
    modal.style.display = 'block';
    
    // Set active tab to memorization by default
    document.querySelector('.tab-button[data-tab="memorization"]').click();
    
    // Fetch student data
    fetch(`../../../model/auth/process_student.php?action=get_student&id=${studentId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateStudentData(data.student);
                // Disable form inputs in view mode
                document.querySelectorAll('#memorizationForm input, #memorizationForm select, #memorizationForm textarea')
                    .forEach(input => input.disabled = true);
            } else {
                showNotification('error', data.message || 'Failed to load student data');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('error', 'Failed to load student data');
        });
}

function editStudent(studentId) {
    fetch(`../../../model/auth/process_student.php?action=get_student&id=${studentId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const student = data.data;
                // Show the modal
                const modal = document.getElementById('editStudentModal');
                modal.style.display = 'block';
                
                // Set the current juz value
                const currentJuzSelect = document.getElementById('currentJuz');
                if (currentJuzSelect) {
                    currentJuzSelect.value = student.current_juz || '1';
                }
                
                // Set other form values as needed
                document.getElementById('studentId').value = student.id;
                document.getElementById('currentJuz').value = student.current_juz || '1';
                document.getElementById('completedJuz').value = student.completed_juz || '0';
                document.getElementById('lastCompletedSurah').value = student.last_completed_surah || '';
                document.getElementById('memorizationQuality').value = student.memorization_quality || 'average';
                document.getElementById('tajweedLevel').value = student.tajweed_level || 'beginner';
                document.getElementById('revisionStatus').value = student.revision_status || 'onTrack';
                document.getElementById('teacherNotes').value = student.teacher_notes || '';

                // Load history if available
                if (student.revisions) {
                    populateRevisionHistory(student.revisions);
                }
            } else {
                showNotification('error', data.message || 'Failed to load student data');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('error', 'Failed to load student data');
        });
}

function populateStudentData(student) {
    console.log('Populating student data:', student); // Debug log
    
    // Set hidden student ID
    const studentIdInput = document.getElementById('studentId');
    if (studentIdInput) {
        studentIdInput.value = student.id;
    } else {
        console.error('Student ID input not found');
    }

    // Populate form fields - removed masteryLevel since it's no longer in the form
    const fields = {
        'currentJuz': student.current_juz || '1',
        'memorizationQuality': student.memorization_quality || 'average',
        'teacherNotes': student.teacher_notes || ''
    };

    Object.entries(fields).forEach(([fieldId, value]) => {
        const element = document.getElementById(fieldId);
        if (element) {
            element.value = value;
        } else {
            console.warn(`Element with id ${fieldId} not found`);
        }
    });
}

function populateRevisionHistory(revisions) {
    const tbody = document.getElementById('revisionHistoryBody');
    tbody.innerHTML = '';
    
    revisions.forEach(revision => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${new Date(revision.revision_date).toLocaleDateString()}</td>
            <td>${revision.juz_revised}</td>
            <td>
                <span class="quality-badge ${revision.quality_rating.toLowerCase()}">
                    ${revision.quality_rating}
                </span>
            </td>
            <td>${revision.teacher_notes || '-'}</td>
        `;
        tbody.appendChild(row);
    });
}

function toggleStudentStatus(studentId, currentStatus) {
    const newStatus = currentStatus.toLowerCase() === 'active' ? 'inactive' : 'active';
    
    fetch('../../../model/auth/process_student.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=toggle_status&student_id=${studentId}&status=${newStatus}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('success', `Student status updated to ${newStatus}`);
            // Reload the page to reflect changes
            setTimeout(() => location.reload(), 1500);
        } else {
            showNotification('error', data.message || 'Failed to update student status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('error', 'Failed to update student status');
    });
}

function populateAyatOptions(surahSelect, startAyatSelect, endAyatSelect) {
    console.log('populateAyatOptions called');
    console.log('surahSelect value:', surahSelect.value);
    console.log('surahSelect selected option:', surahSelect.selectedOptions[0]);
    
    // Clear existing options
    startAyatSelect.innerHTML = '<option value="">Select Starting Ayat</option>';
    endAyatSelect.innerHTML = '<option value="">Select Ending Ayat</option>';
    
    if (surahSelect.value) {
        const selectedOption = surahSelect.selectedOptions[0];
        console.log('Selected option dataset:', selectedOption.dataset);
        
        const endAyat = parseInt(selectedOption.dataset.endAyat);
        const startAyat = parseInt(selectedOption.dataset.startAyat);
        
        console.log('Parsed ayat range:', startAyat, 'to', endAyat);
        
        if (isNaN(startAyat) || isNaN(endAyat)) {
            console.error('Invalid ayat range:', selectedOption.dataset);
            return;
        }
        
        // Populate ayat options
        for (let i = startAyat; i <= endAyat; i++) {
            // Create option for start ayat
            const startOption = document.createElement('option');
            startOption.value = i;
            startOption.textContent = `Ayat ${i}`;
            startAyatSelect.appendChild(startOption.cloneNode(true));
            
            // Create option for end ayat
            const endOption = document.createElement('option');
            endOption.value = i;
            endOption.textContent = `Ayat ${i}`;
            endAyatSelect.appendChild(endOption);
        }
        
        console.log('Added ayat options. Start options:', startAyatSelect.options.length - 1);
        console.log('End options:', endAyatSelect.options.length - 1);
    } else {
        console.warn('No surah selected');
    }
}