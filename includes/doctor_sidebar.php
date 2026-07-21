<?php
/**
 * SevaNest - Doctor Sidebar Component
 * Included in all Doctor Module pages.
 */
// Determine the current page for active state highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="doctor-sidebar shadow-sm">
    <div class="doctor-sidebar-header">
        <h4>Doctor Portal</h4>
    </div>
    <div class="doctor-sidebar-menu">
        <a href="doc_dashboard.php" class="doctor-sidebar-link <?= ($current_page == 'doc_dashboard.php') ? 'active' : '' ?>">
            <i class="bi bi-grid-1x2"></i> Dashboard
        </a>
        <a href="doc_medical_history.php" class="doctor-sidebar-link <?= ($current_page == 'doc_medical_history.php') ? 'active' : '' ?>">
            <i class="bi bi-journal-medical"></i> Medical History
        </a>
        <a href="doc_health_checkup.php" class="doctor-sidebar-link <?= ($current_page == 'doc_health_checkup.php') ? 'active' : '' ?>">
            <i class="bi bi-heart-pulse"></i> Health Check-up
        </a>
        <a href="doc_prescriptions.php" class="doctor-sidebar-link <?= ($current_page == 'doc_prescriptions.php') ? 'active' : '' ?>">
            <i class="bi bi-capsule"></i> Prescriptions
        </a>
        <a href="doc_treatment_records.php" class="doctor-sidebar-link <?= ($current_page == 'doc_treatment_records.php') ? 'active' : '' ?>">
            <i class="bi bi-clipboard2-pulse"></i> Treatment Records
        </a>
        <a href="doc_followup_schedule.php" class="doctor-sidebar-link <?= ($current_page == 'doc_followup_schedule.php') ? 'active' : '' ?>">
            <i class="bi bi-calendar-check"></i> Follow-up Schedule
        </a>
    </div>
</div>
