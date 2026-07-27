<?php
/**
 * SevaNest – Doctor Schedule Page
 * File     : modules/doctor/schedule.php
 * Version  : 1.0
 */

require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../config/database.php';

// Require Doctor login
require_login();

$base_path = '../../';
$page_title = 'My Schedule | SevaNest';
$extra_css = [
    'assets/css/doctor.css'
];

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'doctor';
$currentPage   = 'schedule.php';
$sn_asset_root = "../../assets";
include '../../includes/sidebar.php';
?>

<!-- ═══════════════════════════════════════════════════════════════════════
     MAIN CONTENT AREA
     ═══════════════════════════════════════════════════════════════════════ -->
<main id="sn-main-content" role="main" aria-label="Doctor schedule content">
    <div class="doctor-page-wrapper">
        
        <!-- Header Strip -->
        <div class="dr-header-strip animate-fade-in">
            <div>
                <h2 class="dr-header-strip__title">Weekly Duty Schedule</h2>
                <p style="color: var(--color-text-muted-team); margin: 4px 0 0;">Manage your outpatient department (OPD) slots, resident home visits, and planned leaves.</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary"><i class="bi bi-chevron-left"></i> Previous Week</button>
                <button class="btn btn-outline-primary">Next Week <i class="bi bi-chevron-right"></i></button>
            </div>
        </div>

        <!-- Schedule Container -->
        <div class="schedule-container animate-fade-in">
            <div class="schedule-header">
                <h3 style="font-size: 1.2rem; font-weight: 700; margin: 0; color: var(--color-text);">July 2026 (Week 4)</h3>
                <div class="d-flex gap-3" style="font-size: var(--font-size-xs); font-weight: 600;">
                    <div class="d-flex align-items-center gap-1"><span class="badge" style="background: var(--color-primary-soft-team); border-left: 3px solid var(--color-primary); color: var(--color-primary);">Home Visit</span></div>
                    <div class="d-flex align-items-center gap-1"><span class="badge" style="background: var(--color-accent-soft-team); border-left: 3px solid var(--color-accent); color: var(--color-accent);">OPD Session</span></div>
                    <div class="d-flex align-items-center gap-1"><span class="badge" style="background: rgba(231, 111, 81, 0.1); border-left: 3px solid var(--color-danger); color: var(--color-danger);">Emergency Duty</span></div>
                </div>
            </div>

            <!-- Schedule Calendar Grid -->
            <div class="schedule-grid">
                <!-- Header row -->
                <div class="schedule-time-label" style="background-color: var(--color-primary); border-bottom: 1px solid var(--color-border); border-right: 1px solid var(--color-border);"></div>
                <div class="schedule-header-cell">Mon (20)</div>
                <div class="schedule-header-cell">Tue (21)</div>
                <div class="schedule-header-cell">Wed (22)</div>
                <div class="schedule-header-cell">Thu (23)</div>
                <div class="schedule-header-cell">Fri (24)</div>
                <div class="schedule-header-cell">Sat (25)</div>
                <div class="schedule-header-cell">Sun (26)</div>

                <!-- 09:00 AM Row -->
                <div class="schedule-time-label">09:00 AM</div>
                <div class="schedule-cell"><div class="schedule-event opd">OPD Duty</div></div>
                <div class="schedule-cell"></div>
                <div class="schedule-cell"><div class="schedule-event opd">OPD Duty</div></div>
                <div class="schedule-cell"></div>
                <div class="schedule-cell"><div class="schedule-event opd">OPD Duty</div></div>
                <div class="schedule-cell"><span style="color: var(--color-text-muted-team); font-size: var(--font-size-xs); font-style: italic; padding: 10px; display: block;">Leave Day</span></div>
                <div class="schedule-cell"></div>

                <!-- 11:00 AM Row -->
                <div class="schedule-time-label">11:00 AM</div>
                <div class="schedule-cell"></div>
                <div class="schedule-cell"><div class="schedule-event">Home Visit: Rm 204</div></div>
                <div class="schedule-cell"></div>
                <div class="schedule-cell"><div class="schedule-event">Home Visit: Rm 118</div></div>
                <div class="schedule-cell"></div>
                <div class="schedule-cell" style="background-color: #fafbfb;"></div>
                <div class="schedule-cell"></div>

                <!-- 02:00 PM Row -->
                <div class="schedule-time-label">02:00 PM</div>
                <div class="schedule-cell"><div class="schedule-event">BP Monitoring Check</div></div>
                <div class="schedule-cell"></div>
                <div class="schedule-cell"><div class="schedule-event emergency">Emergency Duty</div></div>
                <div class="schedule-cell"></div>
                <div class="schedule-cell"><div class="schedule-event">Home Visit: Rm 301</div></div>
                <div class="schedule-cell" style="background-color: #fafbfb;"></div>
                <div class="schedule-cell"></div>

                <!-- 04:00 PM Row -->
                <div class="schedule-time-label">04:00 PM</div>
                <div class="schedule-cell"></div>
                <div class="schedule-cell"><div class="schedule-event opd">OPD Consultations</div></div>
                <div class="schedule-cell"></div>
                <div class="schedule-cell"><div class="schedule-event opd">OPD Consultations</div></div>
                <div class="schedule-cell"></div>
                <div class="schedule-cell" style="background-color: #fafbfb;"></div>
                <div class="schedule-cell"></div>
            </div>
        </div>

    </div>
</main>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
