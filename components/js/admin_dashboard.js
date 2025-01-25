document.addEventListener('DOMContentLoaded', function() {
    // Function to load system overview
    function loadSystemOverview() {
        // Fetch system data from server and update the DOM
    }

    // Initialize dashboard
    loadSystemOverview();

    // Event listeners for quick action buttons
    document.querySelector('.btn-primary').addEventListener('click', function() {
        window.location.href = '/View/html/admin/manage_teachers.html';
    });

    document.querySelector('.btn-secondary').addEventListener('click', function() {
        window.location.href = '/View/html/admin/manage_students.html';
    });

    loadDashboardStats();
});

async function loadDashboardStats() {
    try {
        const response = await fetch('../../../model/auth/process_dashboard.php');
        const result = await response.json();
        
        console.log('Raw response:', response);
        console.log('Parsed result:', result);
        console.log('Stats data:', result.data);
        
        if (result.success) {
            const stats = result.data;
            
            // Only update stats that are available
            if ('total_students' in stats) {
                document.getElementById('totalStudents').textContent = stats.total_students;
                document.getElementById('totalStudents').classList.add('updated');
            } else {
                document.getElementById('totalStudents').textContent = 'N/A';
            }

            if ('total_teachers' in stats) {
                document.getElementById('totalTeachers').textContent = stats.total_teachers;
                document.getElementById('totalTeachers').classList.add('updated');
            } else {
                document.getElementById('totalTeachers').textContent = 'N/A';
            }

            if ('today_attendance' in stats) {
                document.getElementById('todayAttendance').textContent = stats.today_attendance;
                document.getElementById('todayAttendance').classList.add('updated');
            } else {
                document.getElementById('todayAttendance').textContent = 'N/A';
            }

            if ('active_classes' in stats) {
                document.getElementById('activeClasses').textContent = stats.active_classes;
                document.getElementById('activeClasses').classList.add('updated');
            } else {
                document.getElementById('activeClasses').textContent = 'N/A';
            }
        } else {
            console.error('Failed to load stats:', result.message);
            document.querySelectorAll('.stat-number').forEach(el => {
                el.textContent = 'Error';
            });
        }
    } catch (error) {
        console.error('Error:', error);
        document.querySelectorAll('.stat-number').forEach(el => {
            el.textContent = 'Error';
        });
    }
}