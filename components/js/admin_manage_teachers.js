document.addEventListener('DOMContentLoaded', function() {
    const addTeacherBtn = document.getElementById('addTeacherBtn');
    const teachersTable = document.getElementById('teachersTable');

    function loadTeachers() {
        // Fetch teachers from server and populate the table
        // Example:
        // fetch('/api/admin/teachers')
        //     .then(response => response.json())
        //     .then(teachers => {
        //         const tbody = teachersTable.querySelector('tbody');
        //         tbody.innerHTML = '';
        //         teachers.forEach(teacher => {
        //             const row = `
        //                 <tr>
        //                     <td>${teacher.name}</td>
        //                     <td>${teacher.email}</td>
        //                     <td>${teacher.hireDate}</td>
        //                     <td>
        //                         <button class="btn-secondary edit-teacher" data-id="${teacher.id}">Edit</button>
        //                         <button class="btn-secondary delete-teacher" data-id="${teacher.id}">Delete</button>
        //                     </td>
        //                 </tr>
        //             `;
        //             tbody.innerHTML += row;
        //         });
        //     });
    }

    addTeacherBtn.addEventListener('click', function() {
        // Implement add teacher functionality
        // This could open a modal or navigate to a new page
    });

    teachersTable.addEventListener('click', function(e) {
        if (e.target.classList.contains('edit-teacher')) {
            const teacherId = e.target.getAttribute('data-id');
            // Implement edit teacher functionality
        } else if (e.target.classList.contains('delete-teacher')) {
            const teacherId = e.target.getAttribute('data-id');
            // Implement delete teacher functionality
        }
    });

    // Initial load of teachers
    loadTeachers();
});