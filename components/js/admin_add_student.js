document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('studentForm');
    
    if (form) {
        form.addEventListener('submit', function(e) {
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
            
            if (isValid) {
                // Submit the form
                form.submit();
            } else {
                alert('Please fill in all required fields');
            }
        });
    }
}); 