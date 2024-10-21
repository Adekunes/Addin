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
});