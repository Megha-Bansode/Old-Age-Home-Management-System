<?php
/**
 * SevaNest – Doctor Dashboard
 * File     : modules/doctor/dashboard.php
 * Version  : 1.1
 */

require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../config/database.php';

// Require Doctor login
require_login();
require_role('Doctor');

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
                <h3 class="fw-bold mb-0 text-dark">Welcome Back, Dr. Robert Watson 🩺</h3>
                <small class="text-muted">Cardiology Department | Senior Resident Medical Officer</small>
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
                    <h4 class="fw-bold mb-0 text-dark mt-1">8 Scheduled</h4>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-4 border-danger h-100">
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Emergency Cases</span>
                    <h4 class="fw-bold mb-0 text-danger mt-1">2 Active</h4>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-4 border-success h-100">
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Active Patients</span>
                    <h4 class="fw-bold mb-0 text-success mt-1">42 Assigned</h4>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-4 border-warning h-100">
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Pending Rx</span>
                    <h4 class="fw-bold mb-0 text-warning mt-1">6 Required</h4>
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
                                <tr>
                                    <td class="ps-3"><strong class="text-dark">Arun Kapoor</strong> <span class="text-muted font-monospace small">(Room 204)</span></td>
                                    <td>10:00 AM</td>
                                    <td>Routine Checkup</td>
                                    <td class="pe-3 text-end"><span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1">Confirmed</span></td>
                                </tr>
                                <tr>
                                    <td class="ps-3"><strong class="text-dark">Meera Iyer</strong> <span class="text-muted font-monospace small">(Room 118)</span></td>
                                    <td>11:30 AM</td>
                                    <td>Diabetic Review</td>
                                    <td class="pe-3 text-end"><span class="badge bg-warning-subtle text-warning rounded-pill px-2.5 py-1">Pending</span></td>
                                </tr>
                                <tr>
                                    <td class="ps-3"><strong class="text-dark">Vijay Verma</strong> <span class="text-muted font-monospace small">(Room 301)</span></td>
                                    <td>02:15 PM</td>
                                    <td>BP Monitoring</td>
                                    <td class="pe-3 text-end"><span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 py-1">Completed</span></td>
                                </tr>
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
                                <tr>
                                    <td class="ps-3"><strong class="text-dark">Arun Kapoor</strong> <span class="text-muted font-monospace small">(Room 204)</span></td>
                                    <td><span class="badge bg-danger-subtle text-danger rounded-pill px-2.5 py-1">Critical</span></td>
                                    <td>14:05 PM</td>
                                    <td class="pe-3 text-end"><span class="text-dark">Caretaker Assigned</span></td>
                                </tr>
                                <tr>
                                    <td class="ps-3"><strong class="text-dark">Latha Rao</strong> <span class="text-muted font-monospace small">(Room 302)</span></td>
                                    <td><span class="badge bg-warning-subtle text-warning rounded-pill px-2.5 py-1">High</span></td>
                                    <td>12:30 PM</td>
                                    <td class="pe-3 text-end"><span class="text-dark">Monitoring</span></td>
                                </tr>
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
                        <div class="d-flex gap-3 align-items-start">
                            <div class="bg-primary rounded-circle" style="width: 8px; height: 8px; margin-top: 6px; flex-shrink: 0;"></div>
                            <div>
                                <strong class="d-block text-dark small" style="line-height: 1.3;">Dr. Watson OPD Visit</strong>
                                <small class="text-muted font-monospace" style="font-size: 0.75rem;">Tomorrow · 10:00 AM</small>
                            </div>
                        </div>
                        <div class="d-flex gap-3 align-items-start">
                            <div class="bg-warning rounded-circle" style="width: 8px; height: 8px; margin-top: 6px; flex-shrink: 0;"></div>
                            <div>
                                <strong class="d-block text-dark small" style="line-height: 1.3;">Routine Cardiologist Check</strong>
                                <small class="text-muted font-monospace" style="font-size: 0.75rem;">Wednesday · 09:30 AM</small>
                            </div>
                        </div>
                        <div class="d-flex gap-3 align-items-start">
                            <div class="bg-info rounded-circle" style="width: 8px; height: 8px; margin-top: 6px; flex-shrink: 0;"></div>
                            <div>
                                <strong class="d-block text-dark small" style="line-height: 1.3;">Family Consultation (Mr. Kapoor)</strong>
                                <small class="text-muted font-monospace" style="font-size: 0.75rem;">Friday · 04:00 PM</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pending Prescriptions -->
                <div class="card border-0 shadow-sm rounded-3 bg-white p-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold text-dark mb-0">Pending Prescriptions</h6>
                        <a href="prescriptions.php" class="btn btn-sm btn-link text-primary p-0 text-decoration-none fw-semibold">Manage Rx</a>
                    </div>
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex gap-3 align-items-start">
                            <div class="bg-warning rounded-circle" style="width: 8px; height: 8px; margin-top: 6px; flex-shrink: 0;"></div>
                            <div>
                                <strong class="d-block text-dark small" style="line-height: 1.3;">Mr. Verma:</strong>
                                <span class="text-muted small">Low-salt medication review needed.</span>
                                <small class="text-muted d-block mt-1 font-monospace" style="font-size: 0.7rem;">2h ago</small>
                            </div>
                        </div>
                        <div class="d-flex gap-3 align-items-start">
                            <div class="bg-warning rounded-circle" style="width: 8px; height: 8px; margin-top: 6px; flex-shrink: 0;"></div>
                            <div>
                                <strong class="d-block text-dark small" style="line-height: 1.3;">Mrs. Iyer:</strong>
                                <span class="text-muted small">Insulin dosage verification due.</span>
                                <small class="text-muted d-block mt-1 font-monospace" style="font-size: 0.7rem;">3h ago</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Medical Activities -->
                <div class="card border-0 shadow-sm rounded-3 bg-white p-3">
                    <h6 class="fw-bold text-dark mb-3">Recent Medical Activities</h6>
                    <ul class="list-group list-group-flush fs-6" style="font-size: 0.82rem;">
                        <li class="list-group-item bg-transparent border-light py-2 px-0 d-flex gap-2">
                            <strong class="font-monospace text-primary">08:30</strong>
                            <span class="text-dark">Routine sugar check logged for Mrs. Iyer.</span>
                        </li>
                        <li class="list-group-item bg-transparent border-light py-2 px-0 d-flex gap-2">
                            <strong class="font-monospace text-primary">09:15</strong>
                            <span class="text-dark">ECG report scanned and uploaded for Mr. Nair.</span>
                        </li>
                        <li class="list-group-item bg-transparent border-light py-2 px-0 d-flex gap-2">
                            <strong class="font-monospace text-primary">10:45</strong>
                            <span class="text-dark">Physiotherapy schedule confirmed for Wing A.</span>
                        </li>
                        <li class="list-group-item bg-transparent border-light py-2 px-0 d-flex gap-2">
                            <strong class="font-monospace text-primary">12:00</strong>
                            <span class="text-dark">Blood pressure checklist completed for Room 301.</span>
                        </li>
                    </ul>
                </div>

            </div>
        </div>

    </div>
</main>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
