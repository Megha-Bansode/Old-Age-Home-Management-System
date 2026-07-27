<?php
/**
 * SevaNest – Doctor Dashboard
 * File     : modules/doctor/dashboard.php
 * Version  : 1.0
 */

require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../config/database.php';

// Require Doctor login
require_login();

$base_path = '../../';
$page_title = 'Doctor Dashboard | SevaNest';
$extra_css = [
    'assets/css/doctor.css'
];

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'doctor';
$currentPage   = 'dashboard.php';
$sn_asset_root = "../../assets";
include '../../includes/sidebar.php';
?>

<!-- ═══════════════════════════════════════════════════════════════════════
     MAIN CONTENT AREA
     ═══════════════════════════════════════════════════════════════════════ -->
<main id="sn-main-content" role="main" aria-label="Doctor dashboard content">
    <div class="doctor-page-wrapper">
        
        <!-- Welcome Strip -->
        <div class="dr-header-strip animate-fade-in">
            <div>
                <h2 class="dr-header-strip__title">Welcome Back, Dr. Robert Watson 🩺</h2>
                <p style="color: var(--color-text-muted-team); margin: 4px 0 0;">Cardiology Department | Senior Resident Medical Officer</p>
            </div>
            <div class="d-flex gap-2">
                <a href="schedule.php" class="btn btn-outline-primary"><i class="bi bi-calendar3 me-2"></i>My Schedule</a>
                <a href="appointments.php" class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>New Appointment</a>
            </div>
        </div>

        <!-- Vital stats overview -->
        <div class="vitals-grid">
            <div class="vital-card">
                <div class="vital-icon"><i class="bi bi-calendar2-check"></i></div>
                <div class="vital-details">
                    <span>Today's Appts</span>
                    <h4>8 Scheduled</h4>
                </div>
            </div>
            <div class="vital-card">
                <div class="vital-icon red"><i class="bi bi-heart-pulse"></i></div>
                <div class="vital-details">
                    <span>Emergency Cases</span>
                    <h4>2 Active</h4>
                </div>
            </div>
            <div class="vital-card">
                <div class="vital-icon blue"><i class="bi bi-people"></i></div>
                <div class="vital-details">
                    <span>Active Patients</span>
                    <h4>42 Assigned</h4>
                </div>
            </div>
            <div class="vital-card">
                <div class="vital-icon gold"><i class="bi bi-capsule"></i></div>
                <div class="vital-details">
                    <span>Pending Rx</span>
                    <h4>6 Required</h4>
                </div>
            </div>
        </div>

        <!-- Two Column Main Grid -->
        <div class="grid two-col">
            <!-- Left Column -->
            <div class="d-flex flex-column gap-4">
                
                <!-- Today's Appointments -->
                <div class="card">
                    <div class="card-head">
                        <h3>Today's Appointments</h3>
                        <a href="appointments.php" class="link">View All</a>
                    </div>
                    <div class="table-wrap">
                        <table class="tbl">
                            <thead>
                                <tr>
                                    <th>Resident</th>
                                    <th>Time</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><b>Arun Kapoor</b> (Room 204)</td>
                                    <td>10:00 AM</td>
                                    <td>Routine Checkup</td>
                                    <td><span class="badge green">Confirmed</span></td>
                                </tr>
                                <tr>
                                    <td><b>Meera Iyer</b> (Room 118)</td>
                                    <td>11:30 AM</td>
                                    <td>Diabetic Review</td>
                                    <td><span class="badge amber">Pending</span></td>
                                </tr>
                                <tr>
                                    <td><b>Vijay Verma</b> (Room 301)</td>
                                    <td>02:15 PM</td>
                                    <td>BP Monitoring</td>
                                    <td><span class="badge blue">Completed</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Emergency Cases -->
                <div class="card">
                    <div class="card-head">
                        <h3>Emergency Cases</h3>
                        <a href="emergency_cases.php" class="link">Alert Board</a>
                    </div>
                    <div class="table-wrap">
                        <table class="tbl">
                            <thead>
                                <tr>
                                    <th>Resident</th>
                                    <th>Severity</th>
                                    <th>Reported Time</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><b>Arun Kapoor</b> (Room 204)</td>
                                    <td><span class="badge red">Critical</span></td>
                                    <td>14:05 PM</td>
                                    <td>Caretaker Assigned</td>
                                </tr>
                                <tr>
                                    <td><b>Latha Rao</b> (Room 302)</td>
                                    <td><span class="badge amber">High</span></td>
                                    <td>12:30 PM</td>
                                    <td>Monitoring</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-head"><h3>Quick Actions</h3></div>
                    <div class="quick-grid">
                        <a href="prescriptions.php" class="quick"><span>💊</span>Write Prescription</a>
                        <a href="medical_records.php" class="quick"><span>📝</span>Add Medical Log</a>
                        <a href="emergency_cases.php" class="quick"><span>🚨</span>Report Emergency</a>
                        <a href="health_reports.php" class="quick"><span>📊</span>Generate Reports</a>
                        <a href="residents.php" class="quick"><span>👥</span>Resident Profiles</a>
                        <a href="schedule.php" class="quick"><span>📅</span>Set Leaves</a>
                    </div>
                </div>

            </div>

            <!-- Right Column -->
            <div class="d-flex flex-column gap-4">
                
                <!-- Upcoming Visits -->
                <div class="card">
                    <div class="card-head">
                        <h3>Upcoming Visits</h3>
                    </div>
                    <ul class="timeline dense">
                        <li><span class="dot"></span><div><strong>Dr. Watson OPD Visit</strong><em>Tomorrow · 10:00 AM</em></div></li>
                        <li><span class="dot gold"></span><div><strong>Routine Cardiologist Check</strong><em>Wednesday · 09:30 AM</em></div></li>
                        <li><span class="dot pink"></span><div><strong>Family Consultation (Mr. Kapoor)</strong><em>Friday · 04:00 PM</em></div></li>
                    </ul>
                </div>

                <!-- Pending Prescriptions -->
                <div class="card">
                    <div class="card-head">
                        <h3>Pending Prescriptions</h3>
                        <a href="prescriptions.php" class="link">Manage Rx</a>
                    </div>
                    <ul class="notif-list">
                        <li><span class="dot gold"></span><div><strong>Mr. Verma:</strong> Low-salt medication review needed. <em>2h ago</em></div></li>
                        <li><span class="dot gold"></span><div><strong>Mrs. Iyer:</strong> Insulin dosage verification due. <em>3h ago</em></div></li>
                    </ul>
                </div>

                <!-- Recent Medical Activities -->
                <div class="card">
                    <div class="card-head">
                        <h3>Recent Medical Activities</h3>
                    </div>
                    <ul class="tl">
                        <li><b>08:30</b> Routine sugar check logged for Mrs. Iyer.</li>
                        <li><b>09:15</b> ECG report scanned and uploaded for Mr. Nair.</li>
                        <li><b>10:45</b> Physiotherapy schedule confirmed for Wing A.</li>
                        <li><b>12:00</b> Blood pressure checklist completed for Room 301.</li>
                    </ul>
                </div>

            </div>
        </div>

    </div>
</main>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
