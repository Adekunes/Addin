document.addEventListener('DOMContentLoaded', function() {
    const classSelect = document.getElementById('classSelect');
    const studentList = document.getElementById('studentList');
    const attendanceForm = document.getElementById('attendanceForm');

    function loadClasses() {
        fetch('/api/teacher/classes')
            .then(response => response.json())
            .then(classes => {
                classSelect.innerHTML = '<option value="">Select Class</option>';
                classes.forEach(cls => {
                    const option = document.createElement('option');
                    option.value = cls.id;
                    option.textContent = cls.name;
                    classSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error loading classes:', error);
                alert('Failed to load classes. Please try again.');
            });
    }

    function loadStudents(classId) {
        fetch(`/api/teacher/students?classId=${classId}`)
            .then(response => response.json())
            .then(students => {
                studentList.innerHTML = '';
                students.forEach(student => {
                    const div = document.createElement('div');
                    div.innerHTML = `
                        <label>
                            <input type="checkbox" name="attendance[]" value="${student.id}">
                            ${student.name}
                        </label>
                    `;
                    studentList.appendChild(div);
                });
            })
            .catch(error => {
                console.error('Error loading students:', error);
                alert('Failed to load students. Please try again.');
            });
    }

    classSelect.addEventListener('change', function() {
        if (this.value) {
            loadStudents(this.value);
        } else {
            studentList.innerHTML = '';
        }
    });

    attendanceForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch('/api/teacher/submit-attendance', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            alert('Attendance submitted successfully');
            this.reset();
            studentList.innerHTML = '';
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to submit attendance. Please try again.');
        });
    });

    // Initial load of classes
    loadClasses();
});