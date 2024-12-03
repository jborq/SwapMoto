document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('rentForm');
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');
    const startTime = document.getElementById('start_time');
    const endTime = document.getElementById('end_time');

    // Create error elements
    const startDateError = document.createElement('div');
    const endDateError = document.createElement('div');
    const startTimeError = document.createElement('div');
    const endTimeError = document.createElement('div');

    // Add classes to error elements
    [startDateError, endDateError, startTimeError, endTimeError].forEach(el => {
        el.classList.add('validation-error');
    });

    // Add error elements after inputs
    startDate.parentNode.appendChild(startDateError);
    endDate.parentNode.appendChild(endDateError);
    startTime.parentNode.appendChild(startTimeError);
    endTime.parentNode.appendChild(endTimeError);

    const errorStyle = 'color: red; font-size: 0.8em; margin-top: 5px;';
    [startDateError, endDateError, startTimeError, endTimeError].forEach(el => {
        el.style.cssText = errorStyle;
    });

    function setMinDates() {
        const today = new Date();
        const tomorrow = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);
        
        // Format dates for input
        const todayStr = today.toISOString().split('T')[0];
        startDate.min = todayStr;
        endDate.min = todayStr;
    }

    function validateDates() {
        const start = new Date(startDate.value);
        const end = new Date(endDate.value);
        const now = new Date();

        // Validate start date
        if (start < now.setHours(0,0,0,0)) {
            startDateError.textContent = 'Start date cannot be in the past';
            return false;
        } else {
            startDateError.textContent = '';
        }

        // Validate end date
        if (end < start) {
            endDateError.textContent = 'End date must be after start date';
            return false;
        } else {
            endDateError.textContent = '';
        }

        return true;
    }

    function validateTimes() {
        const start = new Date(startDate.value + 'T' + startTime.value);
        const end = new Date(endDate.value + 'T' + endTime.value);
        const now = new Date();
        const minTime = new Date(now.getTime() + 10 * 60000); // Current time + 10 minutes

        // Validate start time
        if (startDate.value === now.toISOString().split('T')[0] && start < minTime) {
            startTimeError.textContent = 'Pickup time must be at least 10 minutes from now';
            return false;
        } else {
            startTimeError.textContent = '';
        }

        // Validate end time
        if (startDate.value === endDate.value && end <= start) {
            endTimeError.textContent = 'Return time must be after pickup time';
            return false;
        } else {
            endTimeError.textContent = '';
        }

        // Validate working hours (assuming 8:00-20:00)
        const startHour = parseInt(startTime.value.split(':')[0]);
        const endHour = parseInt(endTime.value.split(':')[0]);
        
        if (startHour < 8 || startHour > 18) {
            startTimeError.textContent = 'Pickup time must be between 8:00 and 18:00';
            return false;
        }
        
        if (endHour < 8 || endHour > 18) {
            endTimeError.textContent = 'Return time must be between 8:00 and 18:00';
            return false;
        }

        return true;
    }

    // Set min dates on load
    setMinDates();

    // Add event listeners
    startDate.addEventListener('change', function() {
        validateDates();
        endDate.min = startDate.value;
    });

    endDate.addEventListener('change', validateDates);
    startTime.addEventListener('change', validateTimes);
    endTime.addEventListener('change', validateTimes);

    // Form submission
    form.addEventListener('submit', function(event) {
        if (!validateDates() || !validateTimes()) {
            event.preventDefault();
        }
    });
});