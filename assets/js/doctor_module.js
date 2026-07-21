/**
 * SevaNest - Doctor Module JavaScript
 * Handles basic interactions, modal populated with dummy data for the prototype.
 */

document.addEventListener("DOMContentLoaded", function() {
    
    // Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });

    // Dummy functionality for viewing Medical History
    const historyButtons = document.querySelectorAll('.view-history-btn');
    historyButtons.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            const residentId = e.currentTarget.getAttribute('data-id');
            const residentName = e.currentTarget.getAttribute('data-name');
            
            // In a real app, you would fetch this via AJAX
            document.getElementById('historyModalLabel').textContent = "Medical History: " + residentName;
            
            // Populate timeline with dummy data
            const timeline = document.getElementById('historyTimelineContent');
            if(timeline) {
                timeline.innerHTML = `
                    <div class="timeline-item">
                        <div class="timeline-marker"></div>
                        <div class="timeline-content">
                            <div class="d-flex justify-content-between mb-2">
                                <strong>Regular Check-up</strong>
                                <small class="text-muted">Today</small>
                            </div>
                            <p class="mb-0 text-sm">Blood pressure normal (120/80). Prescribed standard vitamins.</p>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-marker"></div>
                        <div class="timeline-content">
                            <div class="d-flex justify-content-between mb-2">
                                <strong>Follow-up: Joint Pain</strong>
                                <small class="text-muted">2 weeks ago</small>
                            </div>
                            <p class="mb-0 text-sm">Reported mild knee pain. Advised physical therapy twice a week.</p>
                        </div>
                    </div>
                `;
            }
        });
    });

    // Mobile sidebar toggle
    const toggleBtn = document.getElementById('sidebarToggleBtn');
    if(toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            const sidebar = document.querySelector('.doctor-sidebar');
            if(sidebar.style.display === 'none' || sidebar.style.display === '') {
                sidebar.style.display = 'block';
            } else {
                sidebar.style.display = 'none';
            }
        });
    }
});
