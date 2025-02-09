// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Load dashboard stats
    loadDashboardStats();

    // Only attach event listeners if elements exist
    const btnPrimary = document.querySelector('.btn-primary');
    const btnSecondary = document.querySelector('.btn-secondary');

    if (btnPrimary) {
        btnPrimary.addEventListener('click', function() {
            window.location.href = '/View/html/admin/manage_teachers.php';
        });
    }

    if (btnSecondary) {
        btnSecondary.addEventListener('click', function() {
            window.location.href = '/View/html/manage_students.php';
        });
    }
});

async function loadDashboardStats() {
    try {
        const response = await fetch('../../../model/db_requests/process_dashboard.php', {
            method: 'GET',
            credentials: 'include',
            headers: {
                'Accept': 'application/json'
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const result = await response.json();
        console.log('Dashboard stats response:', result);
        
        if (result.success && result.data) {
            const stats = result.data;
            
            // Update stats only if elements exist
            const elements = {
                'totalStudents': stats.students,
                'totalTeachers': stats.teachers,
                'todayAttendance': stats.attendance,
                'activeClasses': stats.classes
            };

            // Update each element if it exists
            Object.entries(elements).forEach(([id, value]) => {
                const element = document.getElementById(id);
                if (element) {
                    element.textContent = value || '0';
                    element.classList.add('updated');
                } else {
                    console.warn(`Element with id '${id}' not found`);
                }
            });
        } else {
            console.error('Failed to load stats:', result.message);
            // Show error message in stats if elements exist
            ['totalStudents', 'totalTeachers', 'todayAttendance', 'activeClasses'].forEach(id => {
                const element = document.getElementById(id);
                if (element) {
                    element.textContent = 'Error';
                }
            });
        }
    } catch (error) {
        console.error('Error loading dashboard stats:', error);
        // Show error message in stats if elements exist
        ['totalStudents', 'totalTeachers', 'todayAttendance', 'activeClasses'].forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.textContent = 'Error';
            }
        });
    }
}