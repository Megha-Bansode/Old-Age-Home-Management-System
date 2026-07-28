<?php
/**
 * SevaNest – Doctor Dashboard
 * File     : modules/doctor/dashboard.php
 * Version  : 1.1
 */

require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/database.php';

require_login();
require_role('Doctor');

$pdo = get_db_connection();
$user_id = $_SESSION['user_id'] ?? 3; // Default to Dr. Priya Nair (3)

// 1. Today's Appointments Count
$stmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE doctor_id = ? AND DATE(appointment_date) = CURDATE()");
$stmt->execute([$user_id]);
$todays_appointments_cnt = (int)$stmt->fetchColumn();

// 2. Emergency Cases Count
$stmt = $pdo->prepare("SELECT COUNT(*) FROM emergency_cases WHERE status = 'Active'");
$stmt->execute();
$emergency_cases_cnt = (int)$stmt->fetchColumn();

// 3. Active Patients Count
$stmt = $pdo->prepare("SELECT COUNT(DISTINCT resident_id) FROM patient_assignments WHERE caretaker_id = ? AND status = 'Active'");
$stmt->execute([$user_id]);
$active_patients_cnt = (int)$stmt->fetchColumn();

// 4. Pending Rx Count
$stmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE doctor_id = ? AND appointment_id NOT IN (SELECT DISTINCT appointment_id FROM prescriptions WHERE appointment_id IS NOT NULL)");
$stmt->execute([$user_id]);
$pending_rx_cnt = (int)$stmt->fetchColumn();

// 5. Today's Appointments List
$stmt = $pdo->prepare("SELECT a.*, r.full_name AS resident_name, r.room_number FROM appointments a JOIN residents r ON a.resident_id = r.resident_id WHERE a.doctor_id = ? AND DATE(a.appointment_date) = CURDATE() ORDER BY a.appointment_date ASC");
$stmt->execute([$user_id]);
$today_appointments = $stmt->fetchAll();

// 6. Active Emergencies List
$stmt = $pdo->prepare("SELECT ec.*, r.full_name AS resident_name, r.room_number FROM emergency_cases ec JOIN residents r ON ec.resident_id = r.resident_id WHERE ec.status = 'Active' ORDER BY ec.case_id DESC");
$stmt->execute();
$active_emergencies = $stmt->fetchAll();

// 7. Upcoming Visits List
$stmt = $pdo->prepare("SELECT a.*, r.full_name AS resident_name FROM appointments a JOIN residents r ON a.resident_id = r.resident_id WHERE a.doctor_id = ? AND a.appointment_date > NOW() ORDER BY a.appointment_date ASC LIMIT 3");
$stmt->execute([$user_id]);
$upcoming_visits = $stmt->fetchAll();

// 8. Pending Prescriptions List (appointments without prescription)
$stmt = $pdo->prepare("SELECT a.*, r.full_name AS resident_name FROM appointments a JOIN residents r ON a.resident_id = r.resident_id WHERE a.doctor_id = ? AND a.appointment_id NOT IN (SELECT DISTINCT appointment_id FROM prescriptions WHERE appointment_id IS NOT NULL) ORDER BY a.appointment_date DESC LIMIT 3");
$stmt->execute([$user_id]);
$pending_prescriptions_list = $stmt->fetchAll();

// 9. Recent Medical Activities
$stmt = $pdo->prepare("SELECT p.*, r.full_name AS resident_name FROM prescriptions p JOIN residents r ON p.resident_id = r.resident_id WHERE p.doctor_id = ? ORDER BY p.prescription_id DESC LIMIT 4");
$stmt->execute([$user_id]);
$recent_activities = $stmt->fetchAll();

$base_path = '../../';
$page_title = 'Doctor Dashboard | SevaNest';

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'doctor';
$currentPage   = 'dashboard.php';
$sn_asset_root = "../../assets";
include '../../includes/sidebar.php';
?>

<main id="sn-main-content" role="main" aria-label="Doctor dashboard content" class="p-4 flex-grow-1">
    <div class="container-fluid">
        
        <!-- Welcome Strip -->
        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
            <div>
                <h3 class="fw-bold mb-0 text-dark">Welcome Back, <?php echo sn_e($_SESSION['user_full_name'] ?? 'Dr. Robert Watson'); ?> 🩺</h3>
                <small class="text-muted">Medical Department | Senior Resident Medical Officer</small>
            </div>
            <div class="d-flex gap-2">
                <a href="schedule.php" class="btn btn-outline-primary fw-semibold btn-sm"><i class="bi bi-calendar3 me-2"></i>My Schedule</a>
                <a href="appointments.php" class="btn btn-primary fw-semibold btn-sm"><i class="bi bi-plus-circle me-2"></i>New Appointment</a>
            </div>
        </div>

        <!-- Vital stats overview -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-4 border-primary h-100">
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Today's Appts</span>
                    <h4 class="fw-bold mb-0 text-dark mt-1"><?php echo $todays_appointments_cnt; ?> Scheduled</h4>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-4 border-danger h-100">
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Emergency Cases</span>
                    <h4 class="fw-bold mb-0 text-danger mt-1"><?php echo $emergency_cases_cnt; ?> Active</h4>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-4 border-success h-100">
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Active Patients</span>
                    <h4 class="fw-bold mb-0 text-success mt-1"><?php echo $active_patients_cnt; ?> Assigned</h4>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-4 border-warning h-100">
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Pending Rx</span>
                    <h4 class="fw-bold mb-0 text-warning mt-1"><?php echo $pending_rx_cnt; ?> Required</h4>
                </div>
            </div>
        </div>

        <!-- Two Column Main Grid -->
        <div class="row g-4">
            
            <!-- Left Column -->
            <div class="col-lg-8 d-flex flex-column gap-4">
                
                <!-- Today's Appointments -->
                <div class="card border-0 shadow-sm rounded-3 bg-white">
                    <div class="card-header bg-white border-bottom border-light p-3 d-flex align-items-center justify-content-between">
                        <h6 class="fw-bold mb-0 text-dark">Today's Appointments</h6>
                        <a href="appointments.php" class="btn btn-sm btn-link text-primary p-0 text-decoration-none fw-semibold">View All</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                            <thead class="table-light text-muted text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.05em;">
                                <tr>
                                    <th class="ps-3">Resident</th>
                                    <th>Time</th>
                                    <th>Type</th>
                                    <th class="pe-3 text-end">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($today_appointments)): ?>
                                    <tr><td colspan="4" class="text-center text-muted py-3">No appointments scheduled for today.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($today_appointments as $appt): ?>
                                        <?php 
                                            $badge_cls = 'primary';
                                            if ($appt['status'] === 'Completed') $badge_cls = 'success';
                                            elseif ($appt['status'] === 'Cancelled') $badge_cls = 'danger';
                                            elseif ($appt['status'] === 'Scheduled') $badge_cls = 'warning';
                                        ?>
                                        <tr>
                                            <td class="ps-3">
                                                <strong class="text-dark"><?php echo sn_e($appt['resident_name']); ?></strong> 
                                                <span class="text-muted font-monospace small">(Room <?php echo sn_e($appt['room_number'] ?? 'N/A'); ?>)</span>
                                            </td>
                                            <td><?php echo date('h:i A', strtotime($appt['appointment_date'])); ?></td>
                                            <td><?php echo sn_e($appt['reason']); ?></td>
                                            <td class="pe-3 text-end"><span class="badge bg-<?php echo $badge_cls; ?>-subtle text-<?php echo $badge_cls; ?> rounded-pill px-2.5 py-1"><?php echo sn_e($appt['status']); ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Emergency Cases -->
                <div class="card border-0 shadow-sm rounded-3 bg-white">
                    <div class="card-header bg-white border-bottom border-light p-3 d-flex align-items-center justify-content-between">
                        <h6 class="fw-bold mb-0 text-dark">Emergency Cases</h6>
                        <a href="emergency_cases.php" class="btn btn-sm btn-link text-primary p-0 text-decoration-none fw-semibold">Alert Board</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                            <thead class="table-light text-muted text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.05em;">
                                <tr>
                                    <th class="ps-3">Resident</th>
                                    <th>Severity</th>
                                    <th>Reported Time</th>
                                    <th class="pe-3 text-end">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($active_emergencies)): ?>
                                    <tr><td colspan="4" class="text-center text-muted py-3">No active emergency cases.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($active_emergencies as $ec): ?>
                                        <tr>
                                            <td class="ps-3">
                                                <strong class="text-dark"><?php echo sn_e($ec['resident_name']); ?></strong> 
                                                <span class="text-muted font-monospace small">(Room <?php echo sn_e($ec['room_number'] ?? 'N/A'); ?>)</span>
                                            </td>
                                            <td><span class="badge bg-danger-subtle text-danger rounded-pill px-2.5 py-1">Critical</span></td>
                                            <td><?php echo date('h:i A', strtotime($ec['created_at'])); ?></td>
                                            <td class="pe-3 text-end"><span class="text-dark"><?php echo sn_e($ec['status']); ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card border-0 shadow-sm rounded-3 bg-white p-4">
                    <h6 class="fw-bold text-dark mb-3">Quick Actions</h6>
                    <div class="row g-2">
                        <div class="col-6 col-sm-4">
                            <a href="prescriptions.php" class="btn btn-outline-light border text-dark w-100 py-3 d-flex flex-column align-items-center gap-2">
                                <span class="fs-4">💊</span>
                                <span class="small fw-semibold">Write Prescription</span>
                            </a>
                        </div>
                        <div class="col-6 col-sm-4">
                            <a href="medical_records.php" class="btn btn-outline-light border text-dark w-100 py-3 d-flex flex-column align-items-center gap-2">
                                <span class="fs-4">📝</span>
                                <span class="small fw-semibold">Add Medical Log</span>
                            </a>
                        </div>
                        <div class="col-6 col-sm-4">
                            <a href="emergency_cases.php" class="btn btn-outline-light border text-dark w-100 py-3 d-flex flex-column align-items-center gap-2">
                                <span class="fs-4">🚨</span>
                                <span class="small fw-semibold">Report Emergency</span>
                            </a>
                        </div>
                        <div class="col-6 col-sm-4">
                            <a href="health_reports.php" class="btn btn-outline-light border text-dark w-100 py-3 d-flex flex-column align-items-center gap-2">
                                <span class="fs-4">📊</span>
                                <span class="small fw-semibold">Generate Reports</span>
                            </a>
                        </div>
                        <div class="col-6 col-sm-4">
                            <a href="residents.php" class="btn btn-outline-light border text-dark w-100 py-3 d-flex flex-column align-items-center gap-2">
                                <span class="fs-4">👥</span>
                                <span class="small fw-semibold">Resident Profiles</span>
                            </a>
                        </div>
                        <div class="col-6 col-sm-4">
                            <a href="schedule.php" class="btn btn-outline-light border text-dark w-100 py-3 d-flex flex-column align-items-center gap-2">
                                <span class="fs-4">📅</span>
                                <span class="small fw-semibold">Set Leaves</span>
                            </a>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Column -->
            <div class="col-lg-4 d-flex flex-column gap-4">
                
                <!-- Upcoming Visits -->
                <div class="card border-0 shadow-sm rounded-3 bg-white p-3">
                    <h6 class="fw-bold text-dark mb-3">Upcoming Visits</h6>
                    <div class="d-flex flex-column gap-3">
                        <?php if (empty($upcoming_visits)): ?>
                            <div class="text-muted small">No upcoming visits scheduled.</div>
                        <?php else: ?>
                            <?php foreach ($upcoming_visits as $uv): ?>
                                <div class="d-flex gap-3 align-items-start">
                                    <div class="bg-primary rounded-circle" style="width: 8px; height: 8px; margin-top: 6px; flex-shrink: 0;"></div>
                                    <div>
                                        <strong class="d-block text-dark small" style="line-height: 1.3;"><?php echo sn_e($uv['reason']); ?></strong>
                                        <small class="text-muted d-block mt-0.5"><?php echo sn_e($uv['resident_name']); ?></small>
                                        <small class="text-muted font-monospace" style="font-size: 0.75rem;"><?php echo date('D, M d · h:i A', strtotime($uv['appointment_date'])); ?></small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Pending Prescriptions -->
                <div class="card border-0 shadow-sm rounded-3 bg-white p-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold text-dark mb-0">Pending Prescriptions</h6>
                        <a href="prescriptions.php" class="btn btn-sm btn-link text-primary p-0 text-decoration-none fw-semibold">Manage Rx</a>
                    </div>
                    <div class="d-flex flex-column gap-3">
                        <?php if (empty($pending_prescriptions_list)): ?>
                            <div class="text-muted small">No pending prescriptions.</div>
                        <?php else: ?>
                            <?php foreach ($pending_prescriptions_list as $pr): ?>
                                <div class="d-flex gap-3 align-items-start">
                                    <div class="bg-warning rounded-circle" style="width: 8px; height: 8px; margin-top: 6px; flex-shrink: 0;"></div>
                                    <div>
                                        <strong class="d-block text-dark small" style="line-height: 1.3;"><?php echo sn_e($pr['resident_name']); ?>:</strong>
                                        <span class="text-muted small">Prescription needed for checkup: <?php echo sn_e($pr['reason']); ?>.</span>
                                        <small class="text-muted d-block mt-1 font-monospace" style="font-size: 0.7rem;"><?php echo date('h:i A', strtotime($pr['appointment_date'])); ?></small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Recent Medical Activities -->
                <div class="card border-0 shadow-sm rounded-3 bg-white p-3">
                    <h6 class="fw-bold text-dark mb-3">Recent Medical Activities</h6>
                    <ul class="list-group list-group-flush fs-6" style="font-size: 0.82rem;">
                        <?php if (empty($recent_activities)): ?>
                            <li class="list-group-item bg-transparent border-light py-2 px-0 text-muted small">No recent medical activities logged.</li>
                        <?php else: ?>
                            <?php foreach ($recent_activities as $act): ?>
                                <li class="list-group-item bg-transparent border-light py-2 px-0 d-flex gap-2">
                                    <strong class="font-monospace text-primary"><?php echo date('H:i', strtotime($act['prescription_date'] ?? $act['created_at'])); ?></strong>
                                    <span class="text-dark">Prescription written for <?php echo sn_e($act['resident_name']); ?>.</span>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>

            </div>
        </div>

    </div>
</main>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
