/**
 * SevaNest – Visit Schedule JavaScript
 * File     : assets/js/visitors.js
 * Version  : 1.0
 * Author   : SevaNest Dev Team
 *
 * Features
 * --------
 * 1. Dynamic Month Calendar rendering with navigation and legend indicators.
 * 2. Status Tracker dynamic styling based on PHP/database state values.
 * 3. Calendar date selection linking to "Upcoming Visit" detail and status tracker.
 * 4. Request Visit Form mockup (submitting adds a requested dot to the calendar).
 * 5. Visit History search, filter, and pagination.
 */

'use strict';

// Mock DB for visits (can be updated dynamically on the frontend)
const mockVisits = [
    { id: 1, date: '2026-07-10', time: '10:00 AM', visitor: 'Kirti Bansode', purpose: 'Regular Visit', status: 'Completed', remarks: 'Loved one was happy. Brought sweets.' },
    { id: 2, date: '2026-07-18', time: '02:30 PM', visitor: 'Kirti Bansode', purpose: 'Medical Check-in', status: 'Completed', remarks: 'Checked BP and sugar. Reports stable.' },
    { id: 3, date: '2026-07-24', time: '04:00 PM', visitor: 'Kirti Bansode', purpose: 'Regular Visit', status: 'Scheduled', remarks: 'Pending arrival. Bringing clothes.' },
    { id: 4, date: '2026-07-28', time: '11:00 AM', visitor: 'Amit Bansode', purpose: 'Festival Celebration', status: 'Requested', remarks: 'Will bring fruits and sweets.' },
    { id: 5, date: '2026-07-02', time: '03:00 PM', visitor: 'Kirti Bansode', purpose: 'Regular Visit', status: 'Cancelled', remarks: 'Cancelled due to caretaker availability.' }
];

// Current calendar date view state
let currentYear = 2026;
let currentMonth = 6; // July (0-indexed: January = 0)

document.addEventListener('DOMContentLoaded', function () {
    // Initialise elements
    initCalendar();
    initStatusTracker('Scheduled'); // Default load status is Scheduled
    initFormRequest();
    initHistoryTable();
    initRippleEffect();
});

/* ─────────────────────────────────────────────────────────────────────────
 * 1. Dynamic Calendar Rendering
 * ───────────────────────────────────────────────────────────────────────── */
function initCalendar() {
    const monthNames = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ];

    const monthYearTitle = document.getElementById('vs-month-year');
    const prevBtn = document.getElementById('vs-prev-month');
    const nextBtn = document.getElementById('vs-next-month');
    const gridBody = document.getElementById('vs-calendar-days');

    if (!monthYearTitle || !prevBtn || !nextBtn || !gridBody) return;

    function renderCalendar(year, month) {
        // Clear previous days
        gridBody.innerHTML = '';

        // Update Title
        monthYearTitle.textContent = `${monthNames[month]} ${year}`;

        // Get first day of the month (0 = Sun, 6 = Sat)
        const firstDayIndex = new Date(year, month, 1).getDay();

        // Get total days in current month
        const totalDays = new Date(year, month + 1, 0).getDate();

        // Get total days in previous month
        const prevTotalDays = new Date(year, month, 0).getDate();

        // Render previous month blank days
        for (let i = firstDayIndex; i > 0; i--) {
            const dayNum = prevTotalDays - i + 1;
            const cell = document.createElement('div');
            cell.className = 'vs-calendar-cell vs-calendar-cell--outside';
            cell.innerHTML = `<span class="vs-calendar-cell-num">${dayNum}</span>`;
            gridBody.appendChild(cell);
        }

        // Render current month days
        const today = new Date();
        const isCurrentMonthYear = today.getFullYear() === year && today.getMonth() === month;

        for (let day = 1; day <= totalDays; day++) {
            const cell = document.createElement('div');
            cell.className = 'vs-calendar-cell';
            
            // Generate full date string YYYY-MM-DD
            const formattedMonth = String(month + 1).padStart(2, '0');
            const formattedDay = String(day).padStart(2, '0');
            const dateStr = `${year}-${formattedMonth}-${formattedDay}`;

            cell.dataset.date = dateStr;

            // Highlight today
            if (isCurrentMonthYear && today.getDate() === day) {
                cell.classList.add('vs-calendar-cell--current-day');
            }

            // Cell number
            let cellHTML = `<span class="vs-calendar-cell-num">${day}</span>`;

            // Look up visits on this date
            const dayVisits = mockVisits.filter(v => v.date === dateStr);
            if (dayVisits.length > 0) {
                let dotHTML = '<div class="vs-calendar-indicator-wrap">';
                dayVisits.forEach(v => {
                    let dotClass = '';
                    if (v.status.toLowerCase() === 'scheduled') {
                        dotClass = 'vs-calendar-dot--scheduled';
                    } else if (v.status.toLowerCase() === 'requested' || v.status.toLowerCase() === 'approved') {
                        dotClass = 'vs-calendar-dot--requested';
                    } else {
                        dotClass = 'vs-calendar-dot--completed'; // completed / cancelled as grey
                    }
                    dotHTML += `<span class="vs-calendar-dot ${dotClass}" title="${v.status}: ${v.purpose}"></span>`;
                });
                dotHTML += '</div>';
                cellHTML += dotHTML;
            }

            cell.innerHTML = cellHTML;

            // Click interaction
            cell.addEventListener('click', function () {
                selectDate(dateStr);
            });

            gridBody.appendChild(cell);
        }

        // Render next month placeholder cells to complete grid (multiples of 7)
        const totalCellsRendered = firstDayIndex + totalDays;
        const remainingCells = (7 - (totalCellsRendered % 7)) % 7;
        for (let i = 1; i <= remainingCells; i++) {
            const cell = document.createElement('div');
            cell.className = 'vs-calendar-cell vs-calendar-cell--outside';
            cell.innerHTML = `<span class="vs-calendar-cell-num">${i}</span>`;
            gridBody.appendChild(cell);
        }
    }

    // Nav listeners
    prevBtn.addEventListener('click', function () {
        currentMonth--;
        if (currentMonth < 0) {
            currentMonth = 11;
            currentYear--;
        }
        renderCalendar(currentYear, currentMonth);
    });

    nextBtn.addEventListener('click', function () {
        currentMonth++;
        if (currentMonth > 11) {
            currentMonth = 0;
            currentYear++;
        }
        renderCalendar(currentYear, currentMonth);
    });

    // Initial render
    renderCalendar(currentYear, currentMonth);
}

/* ─────────────────────────────────────────────────────────────────────────
 * 2. Visit Status Tracker Updates
 * ───────────────────────────────────────────────────────────────────────── */
function initStatusTracker(status) {
    const trackerContainer = document.getElementById('vs-status-tracker');
    const fillLine = document.getElementById('vs-tracker-line-fill');
    const displayVal = document.getElementById('vs-status-val');
    const displayCard = document.getElementById('vs-status-display-card');

    if (!trackerContainer || !fillLine || !displayVal || !displayCard) return;

    const steps = trackerContainer.querySelectorAll('.vs-tracker-step');
    
    // Status states index map
    const states = ['requested', 'approved', 'scheduled', 'completed'];
    const lowerStatus = status.toLowerCase();
    const statusIndex = states.indexOf(lowerStatus);

    // Reset classes
    trackerContainer.classList.remove('vs-tracker--cancelled');
    displayCard.classList.remove('vs-tracker-status--cancelled');
    steps.forEach(step => {
        step.classList.remove('vs-tracker-step--completed', 'vs-tracker-step--active');
        const icon = step.querySelector('.vs-tracker-circle i');
        if (icon) {
            // Restore default numbers/checks
            const stepNum = step.dataset.step;
            icon.className = `bi bi-${stepNum}-circle`;
        }
    });

    if (lowerStatus === 'cancelled') {
        trackerContainer.classList.add('vs-tracker--cancelled');
        displayCard.classList.add('vs-tracker-status--cancelled');
        displayVal.textContent = 'Cancelled';
        fillLine.style.width = '100%';
        
        steps.forEach(step => {
            step.classList.add('vs-tracker-step--completed');
            const icon = step.querySelector('.vs-tracker-circle i');
            if (icon) icon.className = 'bi bi-x-circle-fill';
        });
        return;
    }

    displayVal.textContent = status;

    // Calculate line width fill percent
    let fillPercent = 0;
    if (statusIndex >= 0) {
        fillPercent = (statusIndex / (states.length - 1)) * 100;
    }
    fillLine.style.width = `${fillPercent}%`;

    // Highlight stages
    steps.forEach((step, i) => {
        const stepNum = i + 1;
        const icon = step.querySelector('.vs-tracker-circle i');

        if (i < statusIndex) {
            step.classList.add('vs-tracker-step--completed');
            if (icon) icon.className = 'bi bi-check-circle-fill';
        } else if (i === statusIndex) {
            step.classList.add('vs-tracker-step--active');
            if (icon) icon.className = 'bi bi-arrow-right-circle-fill';
        } else {
            if (icon) icon.className = `bi bi-${stepNum}-circle`;
        }
    });
}

/* ─────────────────────────────────────────────────────────────────────────
 * 3. Calendar Selection Details linking
 * ───────────────────────────────────────────────────────────────────────── */
function selectDate(dateStr) {
    const upcomingDate = document.getElementById('vs-up-date');
    const upcomingTime = document.getElementById('vs-up-time');
    const upcomingVisitor = document.getElementById('vs-up-visitor');
    const upcomingPurpose = document.getElementById('vs-up-purpose');
    const upcomingStatus = document.getElementById('vs-up-status');
    const actionBtns = document.getElementById('vs-upcoming-actions');

    if (!upcomingDate || !upcomingTime || !upcomingVisitor || !upcomingPurpose || !upcomingStatus) return;

    // Search for visit matching chosen date
    const visit = mockVisits.find(v => v.date === dateStr);

    // Fade effect on selection update
    const leftCard = upcomingDate.closest('.vs-card');
    if (leftCard) {
        leftCard.style.opacity = 0.5;
        leftCard.style.transform = 'translateY(5px)';
        setTimeout(() => {
            leftCard.style.opacity = 1;
            leftCard.style.transform = 'none';
        }, 150);
    }

    if (visit) {
        // Format Date beautifully
        const dateObj = new Date(dateStr);
        const options = { day: 'numeric', month: 'long', year: 'numeric' };
        upcomingDate.textContent = dateObj.toLocaleDateString('en-US', options);

        upcomingTime.textContent = visit.time;
        upcomingVisitor.textContent = visit.visitor;
        upcomingPurpose.textContent = visit.purpose;
        upcomingStatus.textContent = visit.status;

        // Apply dynamic color class to upcomingStatus display badge text
        upcomingStatus.className = 'vs-info-value';
        if (visit.status === 'Cancelled') {
            upcomingStatus.style.color = 'var(--vs-danger)';
            actionBtns.style.display = 'none'; // Hide edit controls on Cancelled
        } else {
            upcomingStatus.style.color = 'var(--vs-primary)';
            actionBtns.style.display = 'flex';
        }

        // Update right tracker card
        initStatusTracker(visit.status);
    } else {
        // Fallback displays when clicking a date with no visit scheduled
        const dateObj = new Date(dateStr);
        const options = { day: 'numeric', month: 'long', year: 'numeric' };
        upcomingDate.textContent = dateObj.toLocaleDateString('en-US', options);

        upcomingTime.textContent = '-- : --';
        upcomingVisitor.textContent = 'None';
        upcomingPurpose.textContent = 'No visit scheduled';
        upcomingStatus.textContent = 'None';
        upcomingStatus.style.color = 'var(--vs-grey)';
        actionBtns.style.display = 'none';

        // Clear tracker to 0 width
        initStatusTracker('Requested');
        // Override displays
        document.getElementById('vs-status-val').textContent = 'No Visits Today';
    }
}

/* ─────────────────────────────────────────────────────────────────────────
 * 4. Request Visit Form Submission Mockup
 * ───────────────────────────────────────────────────────────────────────── */
function initFormRequest() {
    const form = document.getElementById('vs-request-form');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const dateInput = document.getElementById('vs-input-date').value;
        const timeInput = document.getElementById('vs-input-time').value;
        const purposeInput = document.getElementById('vs-input-purpose').value;
        const notesInput = document.getElementById('vs-input-notes').value;

        if (!dateInput || !timeInput || !purposeInput) {
            alert('Please fill in all required fields (Date, Time, Purpose).');
            return;
        }

        // Format military time to AM/PM format
        let formattedTime = timeInput;
        try {
            const [hours, minutes] = timeInput.split(':');
            const hrs = parseInt(hours);
            const ampm = hrs >= 12 ? 'PM' : 'AM';
            const displayHrs = hrs % 12 || 12;
            formattedTime = `${displayHrs}:${minutes} ${ampm}`;
        } catch(err) {}

        // Create new visit object
        const newVisit = {
            id: mockVisits.length + 1,
            date: dateInput,
            time: formattedTime,
            visitor: 'Kirti Bansode', // Assume logged-in member
            purpose: purposeInput,
            status: 'Requested',
            remarks: notesInput || 'Pending approval'
        };

        // Add to array
        mockVisits.push(newVisit);

        // Success Feedback banner
        showToast('Visit Request Submitted Successfully!', `Your visit request for ${dateInput} is pending approval.`);

        // Re-render Calendar and table
        initCalendar();
        initHistoryTable();

        // Focus selection on the new request
        selectDate(dateInput);

        // Reset form fields
        form.reset();
    });
}

/* ─────────────────────────────────────────────────────────────────────────
 * 5. Visit History Search, Filter & Pagination
 * ───────────────────────────────────────────────────────────────────────── */
function initHistoryTable() {
    const tbody = document.getElementById('vs-history-tbody');
    const searchInput = document.getElementById('vs-table-search');
    const filterSelect = document.getElementById('vs-table-filter');

    if (!tbody) return;

    function renderTableRows() {
        tbody.innerHTML = '';
        
        // Filter rows
        const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const filterVal = filterSelect ? filterSelect.value.toLowerCase() : '';

        // Sort descending by date
        const sortedVisits = [...mockVisits].sort((a, b) => new Date(b.date) - new Date(a.date));

        const filtered = sortedVisits.filter(v => {
            const matchQuery = v.purpose.toLowerCase().includes(query) || v.remarks.toLowerCase().includes(query) || v.visitor.toLowerCase().includes(query);
            const matchStatus = !filterVal || v.status.toLowerCase() === filterVal;
            return matchQuery && matchStatus;
        });

        if (filtered.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted">No visits found matching your criteria.</td></tr>';
            return;
        }

        filtered.forEach(v => {
            const row = document.createElement('tr');
            
            // Status Badge mapper
            let badgeClass = 'vs-badge--scheduled';
            if (v.status === 'Completed') badgeClass = 'vs-badge--completed';
            else if (v.status === 'Approved') badgeClass = 'vs-badge--approved';
            else if (v.status === 'Requested') badgeClass = 'vs-badge--requested';
            else if (v.status === 'Cancelled') badgeClass = 'vs-badge--cancelled';

            let badgeIcon = 'bi-clock-fill';
            if (v.status === 'Completed') badgeIcon = 'bi-check-circle-fill';
            else if (v.status === 'Approved') badgeIcon = 'bi-hand-thumbs-up-fill';
            else if (v.status === 'Cancelled') badgeIcon = 'bi-x-circle-fill';
            else if (v.status === 'Scheduled') badgeIcon = 'bi-calendar-check-fill';

            // Pretty format date
            const dateObj = new Date(v.date);
            const options = { day: 'numeric', month: 'short', year: 'numeric' };
            const prettyDate = dateObj.toLocaleDateString('en-US', options);

            row.innerHTML = `
                <td><strong>${prettyDate}</strong></td>
                <td>${v.time}</td>
                <td>${v.purpose}</td>
                <td>
                    <span class="vs-badge ${badgeClass}">
                        <i class="bi ${badgeIcon}"></i>
                        ${v.status}
                    </span>
                </td>
                <td><small class="text-muted">${v.remarks}</small></td>
            `;

            // Clicking history row also navigates calendar and updates tracker
            row.style.cursor = 'pointer';
            row.addEventListener('click', function() {
                // Parse year/month to sync calendar view
                const [y, m, d] = v.date.split('-');
                currentYear = parseInt(y);
                currentMonth = parseInt(m) - 1;
                
                // Re-render calendar showing that month
                const prevBtn = document.getElementById('vs-prev-month');
                if (prevBtn) {
                    // Triggers the calendar rendering functions
                    prevBtn.click();
                    document.getElementById('vs-next-month').click();
                }
                
                selectDate(v.date);
                
                // Scroll up smooth to see the calendar/details
                document.getElementById('sn-main-content').scrollIntoView({ behavior: 'smooth' });
            });

            tbody.appendChild(row);
        });
    }

    if (searchInput) searchInput.addEventListener('input', renderTableRows);
    if (filterSelect) filterSelect.addEventListener('change', renderTableRows);

    // Initial render
    renderTableRows();
}

/* ─────────────────────────────────────────────────────────────────────────
 * Helper Toast banner notification
 * ───────────────────────────────────────────────────────────────────────── */
function showToast(title, message) {
    // Create element on the fly
    const toast = document.createElement('div');
    toast.className = 'vs-toast animate-slide-in';
    toast.style.cssText = `
        position: fixed;
        bottom: 24px;
        right: 24px;
        background: #FFFFFF;
        border-left: 5px solid var(--vs-primary);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        padding: 16px 20px;
        border-radius: 10px;
        z-index: 10000;
        display: flex;
        align-items: center;
        gap: 14px;
        max-width: 360px;
        animation: vs-toast-in 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) both;
    `;

    toast.innerHTML = `
        <div style="background: rgba(107, 144, 128, 0.1); width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink:0;">
            <i class="bi bi-bell-fill" style="color: var(--vs-primary); font-size: 1.2rem;"></i>
        </div>
        <div>
            <h5 style="margin: 0; font-size: 0.95rem; font-weight: 700; color: var(--vs-heading);">${title}</h5>
            <p style="margin: 3px 0 0; font-size: 0.8rem; color: var(--vs-grey); line-height: 1.3;">${message}</p>
        </div>
    `;

    document.body.appendChild(toast);

    // Styles for toast transition animations
    const style = document.createElement('style');
    style.innerHTML = `
        @keyframes vs-toast-in {
            from { opacity: 0; transform: translateY(30px) scale(0.9); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        @keyframes vs-toast-out {
            from { opacity: 1; transform: translateY(0); }
            to { opacity: 0; transform: translateY(20px) scale(0.9); }
        }
    `;
    document.head.appendChild(style);

    // Dismiss toast after 4.5 seconds
    setTimeout(() => {
        toast.style.animation = 'vs-toast-out 0.35s ease both';
        setTimeout(() => {
            toast.remove();
        }, 350);
    }, 4500);
}

/* ─────────────────────────────────────────────────────────────────────────
 * Button Ripple effect initialization
 * ───────────────────────────────────────────────────────────────────────── */
function initRippleEffect() {
    const buttons = document.querySelectorAll('.vs-btn');
    buttons.forEach(btn => {
        btn.addEventListener('click', function (e) {
            const x = e.clientX - e.target.getBoundingClientRect().left;
            const y = e.clientY - e.target.getBoundingClientRect().top;
            
            const ripple = document.createElement('span');
            ripple.style.cssText = `
                position: absolute;
                top: ${y}px;
                left: ${x}px;
                width: 0px;
                height: 0px;
                background: rgba(255, 255, 255, 0.35);
                border-radius: 50%;
                transform: translate(-50%, -50%);
                pointer-events: none;
                animation: vs-ripple 0.6s ease-out;
            `;
            
            this.appendChild(ripple);
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });

    const style = document.createElement('style');
    style.innerHTML = `
        @keyframes vs-ripple {
            to {
                width: 250px;
                height: 250px;
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);
}
