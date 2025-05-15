document.addEventListener('DOMContentLoaded', function() {
    // Initialize date picker
    flatpickr("#datePicker", {
        minDate: "today",
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "F j, Y",
    });
    
    // Initialize time picker
    flatpickr("#timePicker", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true
    });
    
    // Handle reminder type change
    const reminderTypeRadios = document.querySelectorAll('input[name="reminderType"]');
    const contactInfoLabel = document.querySelector('label[for="contactInfo"]');
    const contactInfoInput = document.getElementById('contactInfo');
    
    reminderTypeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'SMS') {
                contactInfoLabel.textContent = 'Phone Number:';
                contactInfoInput.placeholder = 'Enter your phone number';
                contactInfoInput.type = 'tel';
            } else if (this.value === 'Email') {
                contactInfoLabel.textContent = 'Email Address:';
                contactInfoInput.placeholder = 'Enter your email address';
                contactInfoInput.type = 'email';
            }
        });
    });
    
    // Handle form submission
    const reminderForm = document.getElementById('reminderForm');
    const notification = document.getElementById('notification');
    
    reminderForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get form values
        const date = document.getElementById('datePicker').value;
        const time = document.getElementById('timePicker').value;
        const message = document.getElementById('message').value;
        const reminderType = document.querySelector('input[name="reminderType"]:checked').value;
        const contactInfo = document.getElementById('contactInfo').value;
        
        // Create reminder object
        const reminderData = {
            date: date,
            time: time,
            message: message,
            reminderType: reminderType,
            contactInfo: contactInfo
        };
        
        // Send data to API
        fetch('api/create_reminder.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(reminderData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('success', 'Reminder set successfully!');
                reminderForm.reset();
            } else {
                showNotification('error', data.error || 'Failed to set reminder. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('error', 'An unexpected error occurred. Please try again.');
        });
    });
    
    // Function to show notification
    function showNotification(type, message) {
        notification.textContent = message;
        notification.className = `notification ${type}`;
        
        // Auto hide after 5 seconds
        setTimeout(() => {
            notification.classList.add('hidden');
        }, 5000);
    }
    
    // Close notification on click
    notification.addEventListener('click', function() {
        this.classList.add('hidden');
    });
});