document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('studentForm');
    
    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Basic form validation
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('error');
                } else {
                    field.classList.remove('error');
                }
            });
            
            if (!isValid) {
                alert('Please fill in all required fields');
                return;
            }

            try {
                const formData = new FormData(this);
                const response = await fetch('../../../model/db_requests/process_student.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                
                if (result.success) {
                    // Show success message
                    alert('Student added successfully!');
                    // Force redirect to manage_students.php
                    window.location.replace('manage_students.php');
                    return false;
                } else {
                    alert('Error: ' + (result.message || 'Failed to add student'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while adding the student');
            }
        });
    }
}); 