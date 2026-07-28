<?php
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../config/database.php';

// Require Doctor login
require_login();
require_role('Doctor');

$pdo = get_db_connection();
$user_id = $_SESSION['user_id'] ?? 3; // Default to Dr. Priya Nair

// Query appointments
$stmt = $pdo->prepare("SELECT a.*, r.full_name, r.room_number FROM appointments a JOIN residents r ON a.resident_id = r.resident_id WHERE a.doctor_id = ? AND a.status != 'Cancelled'");
$stmt->execute([$user_id]);
$appointments = $stmt->fetchAll();

// Map appointments into day & hour structure
$grid = [
    '09:00 AM' => [1=>[], 2=>[], 3=>[], 4=>[], 5=>[], 6=>[], 7=>[]],
    '11:00 AM' => [1=>[], 2=>[], 3=>[], 4=>[], 5=>[], 6=>[], 7=>[]],
    '02:00 PM' => [1=>[], 2=>[], 3=>[], 4=>[], 5=>[], 6=>[], 7=>[]],
    '04:00 PM' => [1=>[], 2=>[], 3=>[], 4=>[], 5=>[], 6=>[], 7=>[]]
];

foreach ($appointments as $appt) {
    $time = strtotime($appt['appointment_date']);
    $day_index = (int)date('N', $time); // 1 (Mon) - 7 (Sun)
    $hour = (int)date('H', $time);
    
    $slot = '';
    if ($hour >= 9 && $hour < 11) $slot = '09:00 AM';
    elseif ($hour >= 11 && $hour < 14) $slot = '11:00 AM';
    elseif ($hour >= 14 && $hour < 16) $slot = '02:00 PM';
    elseif ($hour >= 16 && $hour < 18) $slot = '04:00 PM';
    
    if ($slot !== '') {
        $grid[$slot][$day_index][] = $appt;
    }
}

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
$base_path = '../../';
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
                <?php for ($d = 1; $d <= 7; $d++): ?>
                    <div class="schedule-cell">
                        <?php if (!empty($grid['09:00 AM'][$d])): ?>
                            <?php foreach ($grid['09:00 AM'][$d] as $appt): ?>
                                <div class="schedule-event opd">
                                    <?php echo sn_e($appt['reason']); ?>: <?php echo sn_e($appt['full_name']); ?> (Rm <?php echo sn_e($appt['room_number']); ?>)
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <?php if ($d === 1 || $d === 3 || $d === 5): ?>
                                <div class="schedule-event opd">OPD Duty</div>
                            <?php elseif ($d === 6): ?>
                                <span style="color: var(--color-text-muted-team); font-size: var(--font-size-xs); font-style: italic; padding: 10px; display: block;">Leave Day</span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                <?php endfor; ?>

                <!-- 11:00 AM Row -->
                <div class="schedule-time-label">11:00 AM</div>
                <?php for ($d = 1; $d <= 7; $d++): ?>
                    <div class="schedule-cell">
                        <?php if (!empty($grid['11:00 AM'][$d])): ?>
                            <?php foreach ($grid['11:00 AM'][$d] as $appt): ?>
                                <div class="schedule-event">
                                    <?php echo sn_e($appt['reason']); ?>: <?php echo sn_e($appt['full_name']); ?> (Rm <?php echo sn_e($appt['room_number']); ?>)
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <?php if ($d === 2): ?>
                                <div class="schedule-event">Home Visit: Rm 204</div>
                            <?php elseif ($d === 4): ?>
                                <div class="schedule-event">Home Visit: Rm 118</div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                <?php endfor; ?>

                <!-- 02:00 PM Row -->
                <div class="schedule-time-label">02:00 PM</div>
                <?php for ($d = 1; $d <= 7; $d++): ?>
                    <div class="schedule-cell">
                        <?php if (!empty($grid['02:00 PM'][$d])): ?>
                            <?php foreach ($grid['02:00 PM'][$d] as $appt): ?>
                                <div class="schedule-event">
                                    <?php echo sn_e($appt['reason']); ?>: <?php echo sn_e($appt['full_name']); ?> (Rm <?php echo sn_e($appt['room_number']); ?>)
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <?php if ($d === 1): ?>
                                <div class="schedule-event">BP Monitoring Check</div>
                            <?php elseif ($d === 3): ?>
                                <div class="schedule-event emergency">Emergency Duty</div>
                            <?php elseif ($d === 5): ?>
                                <div class="schedule-event">Home Visit: Rm 301</div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                <?php endfor; ?>

                <!-- 04:00 PM Row -->
                <div class="schedule-time-label">04:00 PM</div>
                <?php for ($d = 1; $d <= 7; $d++): ?>
                    <div class="schedule-cell">
                        <?php if (!empty($grid['04:00 PM'][$d])): ?>
                            <?php foreach ($grid['04:00 PM'][$d] as $appt): ?>
                                <div class="schedule-event opd">
                                    <?php echo sn_e($appt['reason']); ?>: <?php echo sn_e($appt['full_name']); ?> (Rm <?php echo sn_e($appt['room_number']); ?>)
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <?php if ($d === 2 || $d === 4): ?>
                                <div class="schedule-event opd">OPD Consultations</div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                <?php endfor; ?>
            </div>
        </div>

    </div>
</main>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
