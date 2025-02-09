// admin_manage_students.js
// This file is used to manage the front end of the manage students page


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
                    const response = await fetch(`../../model/db_requests/process_student.php?action=get_surahs_by_juz&juz=${juzNumber}`);
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
                    const response = await fetch('../../model/db_requests/process_student.php', {
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
            
            fetch('../../model/db_requests/process_student.php', {
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
                
                const response = await fetch('../../model/db_requests/process_student.php', {
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

    // Get search elements
    const searchInput = document.getElementById('searchInput');
    const clearSearchBtn = document.getElementById('clearSearch');
    const studentsTable = document.getElementById('studentsTable');
    
    if (searchInput && studentsTable) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            const tableBody = studentsTable.querySelector('tbody');
            const rows = tableBody.querySelectorAll('tr');
            
            // Show/hide clear button
            if (clearSearchBtn) {
                clearSearchBtn.style.display = searchTerm ? 'block' : 'none';
            }

            rows.forEach(row => {
                // Get text content from name and other relevant columns
                const studentName = row.querySelector('td:nth-child(1)');
                const guardianName = row.querySelector('td:nth-child(2)');
                const currentJuz = row.querySelector('td:nth-child(3)');
                
                if (studentName && guardianName && currentJuz) {
                    const searchableText = [
                        studentName.textContent,
                        guardianName.textContent,
                        currentJuz.textContent
                    ].join(' ').toLowerCase();

                    // Show/hide based on search term
                    if (searchTerm === '' || searchableText.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            });

            // Handle no results message
            const visibleRows = Array.from(rows).filter(row => row.style.display !== 'none');
            let noResultsRow = tableBody.querySelector('.no-results');
            
            if (visibleRows.length === 0) {
                if (!noResultsRow) {
                    noResultsRow = document.createElement('tr');
                    noResultsRow.className = 'no-results';
                    noResultsRow.innerHTML = `
                        <td colspan="6" style="text-align: center; padding: 1rem;">
                            No students found matching "${searchTerm}"
                        </td>`;
                    tableBody.appendChild(noResultsRow);
                }
            } else if (noResultsRow) {
                noResultsRow.remove();
            }
        });

        // Clear search functionality
        if (clearSearchBtn) {
            clearSearchBtn.addEventListener('click', function() {
                searchInput.value = '';
                // Trigger the input event to update the table
                searchInput.dispatchEvent(new Event('input'));
                this.style.display = 'none';
            });
        }
    } else {
        console.error('Search input or students table not found');
        console.log({
            searchInput: !!searchInput,
            studentsTable: !!studentsTable
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
                const studentId = document.getElementById('studentId').value;
                
                if (!studentId) {
                    throw new Error('Missing required field: student_id');
                }
                
                // Add required fields with correct action
                formData.set('student_id', studentId);
                formData.set('action', 'update_progress');
                
                // Log form data for debugging
                const formDataObj = {};
                formData.forEach((value, key) => {
                    formDataObj[key] = value;
                });
                console.log('Submitting form data:', formDataObj);
                
                const response = await fetch('../../model/db_requests/process_student.php', {
                    method: 'POST',
                    body: formData
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const result = await response.json();
                console.log('Server response:', result);
                
                if (result.success) {
                    showNotification('Success', 'Progress updated successfully', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    throw new Error(result.message || 'Failed to update progress');
                }
            } catch (error) {
                console.error('Error saving changes:', error);
                showNotification('Error', error.message || 'Failed to update progress', 'error');
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

    // Add this to your existing JavaScript
    const sabaqParaForm = document.getElementById('sabaqParaForm');
    if (sabaqParaForm) {
        sabaqParaForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            try {
                const formData = new FormData(this);
                
                // Debug log the form data
                const formDataObj = {};
                formData.forEach((value, key) => {
                    formDataObj[key] = value;
                });
                console.log('Submitting Sabaq Para form data:', formDataObj);
                
                const response = await fetch('../../model/db_requests/process_student.php', {
                    method: 'POST',
                    body: formData
                });
                
                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Server error response:', errorText);
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const result = await response.json();
                console.log('Server response:', result);
                
                if (result.success) {
                    showNotification('Success', 'Sabaq Para updated successfully', 'success');
                    if (typeof loadSabaqParaHistory === 'function') {
                        loadSabaqParaHistory(formData.get('student_id'));
                    }
                } else {
                    throw new Error(result.message || 'Failed to update Sabaq Para');
                }
            } catch (error) {
                console.error('Error saving Sabaq Para:', error);
                showNotification('Error', error.message, 'error');
            }
        });
    }
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

        // Update fetch paths to use shared location
        const response = await fetch(`../../model/db_requests/process_student.php?action=get_student&id=${studentId}`);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const contentType = response.headers.get("content-type");
        if (!contentType || !contentType.includes("application/json")) {
            throw new Error("Received non-JSON response from server");
        }

        const data = await response.json();
        console.log('Received student data:', data);

        if (!data.success) {
            throw new Error(data.message || 'Failed to fetch student data');
        }

        // Get the latest progress data
        const progressResponse = await fetch(`../../model/db_requests/process_student.php?action=get_latest_progress&student_id=${studentId}`);
        if (!progressResponse.ok) {
            throw new Error(`HTTP error! status: ${progressResponse.status}`);
        }
        
        const progressData = await progressResponse.json();
        console.log('Received progress data:', progressData);

        // Set student ID in all forms
        const studentIdInputs = document.querySelectorAll('input[name="student_id"]');
        studentIdInputs.forEach(input => {
            input.value = studentId;
        });

        // Set current juz and trigger change event
        const currentJuzSelect = document.getElementById('currentJuz');
        const sabaqJuzSelect = document.getElementById('sabaqJuz');
        
        if (currentJuzSelect && progressData.success && progressData.progress) {
            const currentJuz = progressData.progress.current_juz;
            currentJuzSelect.value = currentJuz;
            
            // Sync the juz selection
            syncJuzSelection();
            
            // Trigger change event
            const event = new Event('change');
            currentJuzSelect.dispatchEvent(event);
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
        await loadHistory(studentId);

    } catch (error) {
        console.error('Error in openEditModal:', error);
        showNotification('Error', error.message || 'Failed to load student data', 'error');
        closeModal();
    }
}

// Function to delete student
function deleteStudent(studentId) {
    if (confirm('Are you sure you want to delete this student?')) {
        fetch('../../model/db_requests/process_student.php', {
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

async function loadHistory(studentId) {
    try {
        const response = await fetch(`../../model/db_requests/process_student.php?action=get_student_history&student_id=${studentId}`);
        const data = await response.json();
        
        if (data.success && data.revisions) {
            // Group revisions by type
            const groupedRevisions = {
                progress: [],
                revision: [],
                sabaq_para: []
            };
            
            data.revisions.forEach(entry => {
                if (groupedRevisions[entry.source]) {
                    groupedRevisions[entry.source].push(entry);
                }
            });
            
            // Create HTML for each section
            const sectionsHtml = `
                <div class="history-sections">
                    <div class="history-section">
                        <h3>Daily Progress</h3>
                        <div class="history-table-container">
                            <table class="history-table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Juz</th>
                                        <th>Surah</th>
                                        <th>Ayat Range</th>
                                        <th>Verses Memorized</th>
                                        <th>Quality</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${groupedRevisions.progress.map(entry => {
                                        const versesMemorized = entry.end_ayat && entry.start_ayat ? 
                                            (parseInt(entry.end_ayat) - parseInt(entry.start_ayat) + 1) : 
                                            '-';
                                        const ayatRange = entry.start_ayat && entry.end_ayat ? 
                                            `${entry.start_ayat} - ${entry.end_ayat}` : 
                                            '-';
                                        return `
                                            <tr>
                                                <td>${entry.revision_date}</td>
                                                <td>Juz ${entry.juz_revised}</td>
                                                <td>${entry.current_surah || '-'}</td>
                                                <td>${ayatRange}</td>
                                                <td>${versesMemorized}</td>
                                                <td>${getQualityBadgeHtml(entry.quality_rating)}</td>
                                                <td>${entry.teacher_notes || '-'}</td>
                                            </tr>
                                        `;
                                    }).join('') || '<tr><td colspan="7" class="no-data">No progress records found</td></tr>'}
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="history-section">
                        <h3>Sabaq Para Records</h3>
                        <div class="history-table-container">
                            <table class="history-table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Juz</th>
                                        <th>Quality</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${groupedRevisions.sabaq_para.map(entry => `
                                        <tr>
                                            <td>${entry.revision_date}</td>
                                            <td>Juz ${entry.juz_revised}</td>
                                            <td><span class="quality-badge ${entry.quality_rating.toLowerCase()}">${entry.quality_rating}</span></td>
                                            <td>${entry.teacher_notes || '-'}</td>
                                        </tr>
                                    `).join('') || '<tr><td colspan="4" class="no-data">No sabaq para records found</td></tr>'}
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="history-section">
                        <h3>Revision Records</h3>
                        <div class="history-table-container">
                            <table class="history-table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Juz</th>
                                        <th>Quality</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${groupedRevisions.revision.map(entry => `
                                        <tr>
                                            <td>${entry.revision_date}</td>
                                            <td>Juz ${entry.juz_revised}</td>
                                            <td>${getQualityBadgeHtml(entry.quality_rating)}</td>
                                            <td>${entry.teacher_notes || '-'}</td>
                                        </tr>
                                    `).join('') || '<tr><td colspan="4" class="no-data">No revision records found</td></tr>'}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            `;
            
            const historyTab = document.querySelector('.tab-content[data-tab="history"]');
            if (historyTab) {
                historyTab.innerHTML = sectionsHtml;
            }
        }
    } catch (error) {
        console.error('Error loading history:', error);
        showNotification('Error', 'Failed to load history', 'error');
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
    fetch(`../../model/db_requests/process_student.php?action=get_student&id=${studentId}`)
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
    fetch(`../../model/db_requests/process_student.php?action=get_student&id=${studentId}`)
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
    
    fetch('../../model/db_requests/process_student.php', {
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

// Make sure we have all required elements
document.addEventListener('DOMContentLoaded', function() {
    const currentSurahSelect = document.getElementById('currentSurah');
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
});

// Function to load Sabaq Para history
async function loadSabaqParaHistory(studentId) {
    try {
        const response = await fetch(`../../model/db_requests/process_student.php?action=get_sabaq_para_history&student_id=${studentId}`);
        const data = await response.json();
        
        if (data.success) {
            const historyHtml = data.history.map(entry => `
                <tr>
                    <td>${entry.revision_date}</td>
                    <td>Juz ${entry.juz_number}</td>
                    <td>${entry.quarters_revised.replace('_', ' ')}</td>
                    <td>${entry.quality_rating}</td>
                    <td>${entry.teacher_notes || ''}</td>
                </tr>
            `).join('');
            
            // Add this section to your history tab
            const historySection = document.createElement('div');
            historySection.className = 'history-section';
            historySection.innerHTML = `
                <h3>Sabaq Para History</h3>
                <div class="history-table-container">
                    <table class="history-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Juz</th>
                                <th>Quarters</th>
                                <th>Quality</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${historyHtml}
                        </tbody>
                    </table>
                </div>
            `;
            
            // Find the history tab content and append this section
            const historyTab = document.querySelector('.tab-content[data-tab="history"]');
            if (historyTab) {
                historyTab.appendChild(historySection);
            }
        }
    } catch (error) {
        console.error('Error loading Sabaq Para history:', error);
    }
}

// Add this function to sync the juz selection
function syncJuzSelection() {
    const currentJuzSelect = document.getElementById('currentJuz');
    const sabaqJuzSelect = document.getElementById('sabaqJuz');
    
    if (currentJuzSelect && sabaqJuzSelect) {
        const currentJuzValue = currentJuzSelect.value;
        if (currentJuzValue) {
            sabaqJuzSelect.value = currentJuzValue;
            console.log('Synced Sabaq Para juz to:', currentJuzValue);
        }
    }
}

// Add tab switching logic with juz sync
document.querySelectorAll('.tab-button').forEach(button => {
    button.addEventListener('click', function() {
        const tab = this.dataset.tab;
        
        // If switching to sabaq-para tab, sync the juz
        if (tab === 'sabaq-para') {
            syncJuzSelection();
        }
        
        // Existing tab switching logic
        document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
        
        this.classList.add('active');
        document.querySelector(`.tab-content[data-tab="${tab}"]`).classList.add('active');
    });
});

// Update the quality rating mapping in loadHistory function
function getQualityBadgeHtml(quality) {
    if (!quality) {
        return '<span class="quality-badge not-rated">Not Rated</span>';
    }
    return `<span class="quality-badge ${quality.toLowerCase()}">${quality}</span>`;
}

function updateStudent(studentId) {
    const form = document.getElementById('editStudentForm');
    const formData = new FormData(form);
    
    // Debug log
    console.log('Updating student with data:', Object.fromEntries(formData));

    fetch('../../model/db_requests/process_student.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        console.log('Server response:', data); // Debug log
        
        if (data.success) {
            alert('Student updated successfully!');
            // Reload the page to show updated data
            window.location.reload();
        } else {
            throw new Error(data.message || 'Failed to update student');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to update student: ' + error.message);
    });
}