document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'timeGridWeek,timeGridDay'
        },
        slotMinTime: '06:00:00',
        slotMaxTime: '22:00:00',
        allDaySlot: false,
        slotDuration: '00:30:00',
        events: function(info, successCallback, failureCallback) {
            fetch(`../../../model/auth/process_schedule.php?action=get_schedule&teacher_id=${teacherId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        successCallback(data.events);
                    } else {
                        failureCallback(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    failureCallback(error);
                });
        },
        eventClick: function(info) {
            // Handle event click
            showEventDetails(info.event);
        }
    });
    calendar.render();

    // Generate time slots for the modal
    generateTimeSlots();
});

function generateTimeSlots() {
    const container = document.getElementById('timeSlots');
    const startTime = 6; // 6 AM
    const endTime = 22; // 10 PM
    
    for (let hour = startTime; hour < endTime; hour++) {
        for (let minute of ['00', '30']) {
            const time = `${hour.toString().padStart(2, '0')}:${minute}`;
            const slot = document.createElement('div');
            slot.className = 'time-slot';
            slot.innerHTML = `
                <input type="checkbox" name="times[]" value="${time}">
                <label>${formatTime(time)}</label>
            `;
            container.appendChild(slot);
        }
    }
}

function formatTime(time) {
    const [hours, minutes] = time.split(':');
    const hour = parseInt(hours);
    const ampm = hour >= 12 ? 'PM' : 'AM';
    const formattedHour = hour % 12 || 12;
    return `${formattedHour}:${minutes} ${ampm}`;
}

function openAddScheduleModal() {
    document.getElementById('scheduleModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('scheduleModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('scheduleModal');
    if (event.target === modal) {
        closeModal();
    }
}

// Handle form submission
document.getElementById('scheduleForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    try {
        const formData = new FormData(this);
        const response = await fetch('../../../model/auth/process_schedule.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();
        
        if (result.success) {
            alert('Schedule added successfully!');
            closeModal();
            calendar.refetchEvents();
        } else {
            alert('Error: ' + (result.message || 'Failed to add schedule'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while adding the schedule');
    }
}); 