document.addEventListener('DOMContentLoaded', function() {
    const studentSelect = document.getElementById('studentSelect');
    const progressForm = document.getElementById('progressForm');
    const currentSurah = document.getElementById('currentSurah');
    const versesMemorized = document.getElementById('versesMemorized');
    const lastRevisionDate = document.getElementById('lastRevisionDate');

    function loadStudents() {
        fetch('/api/teacher/all-students')
            .then(response => response.json())
            .then(students => {
                studentSelect.innerHTML = '<option value="">Select Student</option>';
                students.forEach(student => {
                    const option = document.createElement('option');
                    option.value = student.id;
                    option.textContent = student.name;
                    studentSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error loading students:', error);
                alert('Failed to load students. Please try again.');
            });
    }

    function loadStudentProgress(studentId) {
        fetch(`/api/teacher/student-progress?studentId=${studentId}`)
            .then(response => response.json())
            .then(progress => {
                currentSurah.value = progress.currentSurah;
                versesMemorized.value = progress.versesMemorized;
                lastRevisionDate.value = progress.lastRevisionDate;
            })
            .catch(error => {
                console.error('Error loading student progress:', error);
                alert('Failed to load student progress. Please try again.');
            });
    }

    studentSelect.addEventListener('change', function() {
        if (this.value) {
            loadStudentProgress(this.value);
        } else {
            progressForm.reset();
        }
    });

    progressForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch('/api/teacher/update-progress', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            alert('Progress updated successfully');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to update progress. Please try again.');
        });
    });

    // Initial load of students
    loadStudents();
});