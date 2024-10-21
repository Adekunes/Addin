document.addEventListener('DOMContentLoaded', function() {
    const addStudentBtn = document.getElementById('addStudentBtn');
    const studentsTable = document.getElementById('studentsTable');
    const studentModal = document.getElementById('studentModal');
    const studentForm = document.getElementById('studentForm');
    const closeModalBtn = document.querySelector('.close-modal');

    function loadStudents() {
        fetch('/api/admin/students')
            .then(response => response.json())
            .then(students => {
                const tbody = studentsTable.querySelector('tbody');
                tbody.innerHTML = '';
                students.forEach(student => {
                    const row = `
                        <tr>
                            <td>${student.name}</td>
                            <td>${student.class}</td>
                            <td>${student.enrollmentDate}</td>
                            <td>
                                <button class="btn-secondary edit-student" data-id="${student.id}">Edit</button>
                                <button class="btn-secondary delete-student" data-id="${student.id}">Delete</button>
                            </td>
                        </tr>
                    `;
                    tbody.innerHTML += row;
                });
            })
            .catch(error => {
                console.error('Error loading students:', error);
                alert('Failed to load students. Please try again.');
            });
    }

    function showModal(title, studentData = null) {
        studentModal.style.display = 'block';
        document.getElementById('modalTitle').textContent = title;
        if (studentData) {
            document.getElementById('studentId').value = studentData.id;
            document.getElementById('studentName').value = studentData.name;
            document.getElementById('studentClass').value = studentData.class;
            document.getElementById('enrollmentDate').value = studentData.enrollmentDate;
        } else {
            studentForm.reset();
            document.getElementById('studentId').value = '';
        }
    }

    function hideModal() {
        studentModal.style.display = 'none';
    }

    function handleFormSubmit(event) {
        event.preventDefault();
        const studentId = document.getElementById('studentId').value;
        const formData = new FormData(studentForm);
        const url = studentId ? `/api/admin/students/${studentId}` : '/api/admin/students';
        const method = studentId ? 'PUT' : 'POST';

        fetch(url, {
            method: method,
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            alert(studentId ? 'Student updated successfully' : 'Student added successfully');
            hideModal();
            loadStudents();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    }

    function deleteStudent(studentId) {
        if (confirm('Are you sure you want to delete this student?')) {
            fetch(`/api/admin/students/${studentId}`, {
                method: 'DELETE'
            })
            .then(response => response.json())
            .then(result => {
                alert('Student deleted successfully');
                loadStudents();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to delete student. Please try again.');
            });
        }
    }

    addStudentBtn.addEventListener('click', () => showModal('Add New Student'));

    closeModalBtn.addEventListener('click', hideModal);

    studentForm.addEventListener('submit', handleFormSubmit);

    studentsTable.addEventListener('click', function(e) {
        if (e.target.classList.contains('edit-student')) {
            const studentId = e.target.getAttribute('data-id');
            fetch(`/api/admin/students/${studentId}`)
                .then(response => response.json())
                .then(student => {
                    showModal('Edit Student', student);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to load student data. Please try again.');
                });
        } else if (e.target.classList.contains('delete-student')) {
            const studentId = e.target.getAttribute('data-id');
            deleteStudent(studentId);
        }
    });

    // Close modal if clicking outside of it
    window.addEventListener('click', function(e) {
        if (e.target == studentModal) {
            hideModal();
        }
    });

    // Initial load of students
    loadStudents();
});