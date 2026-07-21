/**
 * OAHMS Header JavaScript File
 * Handles the live-updating digital clock in the header.
 */

document.addEventListener('DOMContentLoaded', () => {
    initLiveClock();
});

/**
 * Initializes and starts the live digital clock.
 */
function initLiveClock() {
    const clockElement = document.getElementById('oahms-live-clock');
    const clockElementMedium = document.getElementById('oahms-live-clock-medium');
    
    if (!clockElement && !clockElementMedium) {
        return;
    }

    // Days of the week in full English representation
    const daysOfWeek = [
        'Sunday', 'Monday', 'Tuesday', 'Wednesday', 
        'Thursday', 'Friday', 'Saturday'
    ];

    // Months of the year in full English representation
    const monthsOfYear = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ];

    function updateTime() {
        const now = new Date();

        // Retrieve components
        const dayName = daysOfWeek[now.getDay()];
        const dayOfMonth = now.getDate();
        const monthName = monthsOfYear[now.getMonth()];
        const year = now.getFullYear();

        let hours = now.getHours();
        let minutes = now.getMinutes();
        let seconds = now.getSeconds();
        const ampm = hours >= 12 ? 'PM' : 'AM';

        // Convert to 12-hour format
        hours = hours % 12;
        hours = hours ? hours : 12; // '0' is mapped to '12'

        // Pad minutes and seconds with leading zeros
        const formattedMinutes = minutes < 10 ? '0' + minutes : minutes;
        const formattedSeconds = seconds < 10 ? '0' + seconds : seconds;

        // Build the formatted string
        // Example output: Sunday, 19 July 2026 | 07:45:32 PM
        const dateString = `${dayName}, ${dayOfMonth} ${monthName} ${year}`;
        const timeString = `${hours}:${formattedMinutes}:${formattedSeconds} ${ampm}`;
        const clockContent = `${dateString} | ${timeString}`;
        
        if (clockElement) {
            clockElement.textContent = clockContent;
        }
        if (clockElementMedium) {
            clockElementMedium.textContent = clockContent;
        }
    }

    // Initial update and set interval for every second
    updateTime();
    setInterval(updateTime, 1000);
}
