<?php
/**
 * SevaNest — Admin Dashboard
 * File     : modules/admin/dashboard.php
 * Version  : 1.1
 */

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/functions.php';

// Require Admin login
require_login();
require_role(['Admin', 'Old Age Home Admin']);

$base_path = '../../';
$page_title = 'Admin Dashboard | SevaNest';

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'admin';
$currentPage   = 'dashboard.php';
$sn_asset_root = "../../assets";
include '../../includes/sidebar.php';

// Fetch Mock Data
$admissions = sn_recent_admissions();
?>

<main id="sn-main-content" role="main" aria-label="Admin Dashboard Content" class="p-4 flex-grow-1">
    <div class="container-fluid">
        
        <!-- Emergency Alerts Card (If any critical status) -->
        <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 p-3 d-flex align-items-center justify-content-between animate-fade-in" role="alert">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                </div>
                <div>
                    <h6 class="alert-heading fw-bold mb-1 text-danger-emphasis">Emergency Alert: Power Failure in Wing C</h6>
                    <small class="text-danger-emphasis">Backup generator is active. Maintenance staff has been dispatched.</small>
                </div>
            </div>
            <span class="badge bg-danger px-2.5 py-1.5 rounded-pill text-uppercase font-monospace small">Critical</span>
        </div>

        <!-- Welcome Strip -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" 
             style="background: linear-gradient(135deg, var(--color-primary) 0%, #4E7466 100%); color: #FFFFFF;">
            <div class="card-body p-4 p-lg-5">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <span class="badge bg-white text-dark mb-2 px-3 py-2 rounded-pill fw-semibold" style="font-size: 0.8rem;">
                            <i class="bi bi-shield-check text-success me-1"></i> Admin Portal
                        </span>
                        <h1 class="display-6 fw-bold mb-2">Welcome back, <?php echo sn_e($_SESSION['full_name'] ?? 'Admin'); ?>!</h1>
                        <p class="lead opacity-90 mb-0" style="font-size: 1rem;">
                            Track residents, manage admissions, view staff allocation, and oversee daily operations at SevaNest.
                        </p>
                    </div>
                    <div class="col-lg-4 text-end d-none d-lg-block">
                        <i class="bi bi-speedometer2 display-1 opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 8 KPI Stat Cards Grid (Total Residents, Active, Staff, Visitors, Admissions, Discharges, Rooms, Donations) -->
        <div class="row g-3 mb-4">
            <!-- 1. Total Residents -->
            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 h-100 p-3 bg-white border-start border-4 border-primary">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em; font-size: 0.75rem;">Total Residents</span>
                        <div class="bg-light p-2 rounded-3 text-primary">
                            <i class="bi bi-people fs-5"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-0 text-dark">96</h3>
                    <small class="text-success fw-semibold"><i class="bi bi-arrow-up-short"></i>+4 this month</small>
                </div>
            </div>

            <!-- 2. Active Residents -->
            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 h-100 p-3 bg-white border-start border-4 border-success">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em; font-size: 0.75rem;">Active Residents</span>
                        <div class="bg-light p-2 rounded-3 text-success">
                            <i class="bi bi-person-check fs-5"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-0 text-dark">86</h3>
                    <small class="text-muted small">10 currently on temporary leave</small>
                </div>
            </div>

            <!-- 3. Admissions Today -->
            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 h-100 p-3 bg-white border-start border-4 border-info">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em; font-size: 0.75rem;">Admissions Today</span>
                        <div class="bg-light p-2 rounded-3 text-info">
                            <i class="bi bi-clipboard-plus fs-5"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-0 text-dark">2</h3>
                    <small class="text-info fw-semibold">3 requests pending review</small>
                </div>
            </div>

            <!-- 4. Pending Discharges -->
            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 h-100 p-3 bg-white border-start border-4 border-warning">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em; font-size: 0.75rem;">Pending Discharges</span>
                        <div class="bg-light p-2 rounded-3 text-warning">
                            <i class="bi bi-box-arrow-right fs-5"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-0 text-dark">1</h3>
                    <small class="text-warning fw-semibold">Scheduled for 05:00 PM</small>
                </div>
            </div>

            <!-- 5. Staff Available -->
            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 h-100 p-3 bg-white border-start border-4 border-primary">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em; font-size: 0.75rem;">Staff Available</span>
                        <div class="bg-light p-2 rounded-3 text-primary">
                            <i class="bi bi-shield-check fs-5"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-0 text-dark">24 / 28</h3>
                    <small class="text-muted small">4 staff members on leave</small>
                </div>
            </div>

            <!-- 6. Visitors Today -->
            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 h-100 p-3 bg-white border-start border-4 border-success">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em; font-size: 0.75rem;">Visitors Today</span>
                        <div class="bg-light p-2 rounded-3 text-success">
                            <i class="bi bi-person-badge fs-5"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-0 text-dark">12</h3>
                    <small class="text-success fw-semibold"><i class="bi bi-arrow-up-short"></i>+3 vs yesterday</small>
                </div>
            </div>

            <!-- 7. Room Occupancy -->
            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 h-100 p-3 bg-white border-start border-4 border-dark">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em; font-size: 0.75rem;">Room Occupancy</span>
                        <div class="bg-light p-2 rounded-3 text-dark">
                            <i class="bi bi-house-door fs-5"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-0 text-dark">86%</h3>
                    <small class="text-muted small">86 of 100 rooms occupied</small>
                </div>
            </div>

            <!-- 8. Donation Summary -->
            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 h-100 p-3 bg-white border-start border-4 border-danger">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em; font-size: 0.75rem;">Donations (Monthly)</span>
                        <div class="bg-light p-2 rounded-3 text-danger">
                            <i class="bi bi-gift fs-5"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-0 text-dark">₹4,25,000</h3>
                    <small class="text-success fw-semibold"><i class="bi bi-arrow-up-short"></i>+12% vs last month</small>
                </div>
            </div>
        </div>

        <!-- Dashboard Layout columns -->
        <div class="row g-4 mb-4">
            
            <!-- Left Side Column: Recent Admissions & Activities -->
            <div class="col-lg-8">
                <!-- Recent Admissions Table -->
                <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
                    <div class="card-header bg-white border-bottom border-light p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="card-title fw-bold mb-0 text-dark">Recent Admissions</h5>
                            <small class="text-muted">Latest residents registered or awaiting approval</small>
                        </div>
                        <a href="admission_management.php" class="btn btn-sm btn-outline-primary fw-semibold">View all</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                                <thead class="table-light text-muted" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em;">
                                    <tr>
                                        <th class="ps-3">Resident</th>
                                        <th>Room</th>
                                        <th>Date</th>
                                        <th>Guardian</th>
                                        <th class="pe-3">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($admissions as $a): ?>
                                        <?php 
                                            $badgeCls = ($a['status'] === 'Admitted' || $a['status'] === 'Approved') ? 'success' : 'warning';
                                            $initials = strtoupper($a['name'][0]);
                                        ?>
                                        <tr>
                                            <td class="ps-3">
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center fw-bold text-primary" style="width: 40px; height: 40px; font-size: 0.95rem;">
                                                        <?php echo sn_e($initials); ?>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold text-dark"><?php echo sn_e($a['name']); ?></div>
                                                        <small class="text-muted">Age <?php echo (int)$a['age']; ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="font-monospace text-dark"><?php echo sn_e($a['room']); ?></span></td>
                                            <td><span class="text-dark"><?php echo sn_e($a['date'] ?? $a['requested']); ?></span></td>
                                            <td><span class="text-dark"><?php echo sn_e($a['guardian']); ?></span></td>
                                            <td class="pe-3">
                                                <span class="badge bg-<?php echo $badgeCls; ?>-subtle text-<?php echo $badgeCls; ?> rounded-pill px-2.5 py-1">
                                                    <?php echo sn_e($a['status']); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Recent Activities -->
                <div class="card border-0 shadow-sm rounded-3 bg-white">
                    <div class="card-header bg-white border-bottom border-light p-3">
                        <h5 class="card-title fw-bold mb-0 text-dark">Recent Activities</h5>
                        <small class="text-muted">Log of latest operational activities across the facility</small>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush" style="font-size: 0.875rem;">
                            <li class="list-group-item bg-transparent border-light py-3 px-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1 fw-semibold text-dark"><i class="bi bi-person-plus text-primary me-2"></i>Kamala Devi Admitted</h6>
                                        <small class="text-muted">Room assigned: A-102. Caretaker Meena Patil assigned.</small>
                                    </div>
                                    <span class="text-muted small">2 hours ago</span>
                                </div>
                            </li>
                            <li class="list-group-item bg-transparent border-light py-3 px-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1 fw-semibold text-dark"><i class="bi bi-calendar2-check text-success me-2"></i>Dr. Priya Nair Visited</h6>
                                        <small class="text-muted">Completed routine medical inspection for Wing B residents.</small>
                                    </div>
                                    <span class="text-muted small">4 hours ago</span>
                                </div>
                            </li>
                            <li class="list-group-item bg-transparent border-light py-3 px-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1 fw-semibold text-dark"><i class="bi bi-box-arrow-right text-warning me-2"></i>Discharge Roster Scheduled</h6>
                                        <small class="text-muted">Initiated discharge processes for Ram Sharan.</small>
                                    </div>
                                    <span class="text-muted small">1 day ago</span>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Right Side Column: Actions & Notifications -->
            <div class="col-lg-4 d-flex flex-column gap-4">
                <!-- Quick Actions -->
                <div class="card border-0 shadow-sm rounded-3 bg-white">
                    <div class="card-header bg-white border-bottom border-light p-3">
                        <h5 class="card-title fw-bold mb-0 text-dark">Quick Actions</h5>
                    </div>
                    <div class="card-body d-flex flex-column gap-2">
                        <a href="resident_registration.php" class="btn btn-primary w-100 justify-content-start py-2.5">
                            <i class="bi bi-person-plus me-2"></i> Register Resident
                        </a>
                        <a href="admission_management.php" class="btn btn-outline-primary w-100 justify-content-start py-2.5">
                            <i class="bi bi-clipboard-check me-2"></i> View Admissions
                        </a>
                        <a href="visitor_management.php" class="btn btn-outline-primary w-100 justify-content-start py-2.5">
                            <i class="bi bi-person-bounding-box me-2"></i> Log a Visitor Check-in
                        </a>
                        <a href="staff_management.php" class="btn btn-outline-primary w-100 justify-content-start py-2.5">
                            <i class="bi bi-people me-2"></i> Roster Shifts
                        </a>
                    </div>
                </div>

                <!-- Notifications Panel -->
                <div class="card border-0 shadow-sm rounded-3 bg-white">
                    <div class="card-header bg-white border-bottom border-light p-3 d-flex align-items-center justify-content-between">
                        <h5 class="card-title fw-bold mb-0 text-dark">Notifications</h5>
                        <span class="badge bg-danger rounded-pill px-2 py-1" style="font-size: 0.75rem;">3 New</span>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush" style="font-size: 0.825rem;">
                            <li class="list-group-item bg-transparent border-light py-2.5 px-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="pe-2">
                                        <div class="fw-semibold text-danger"><i class="bi bi-exclamation-circle-fill me-1"></i>Low Inventory Warning</div>
                                        <div class="text-muted small">Vitamin C capsules and Low-sodium salt stocks are under 10%.</div>
                                    </div>
                                    <span class="text-muted small font-monospace" style="font-size: 0.7rem;">10m ago</span>
                                </div>
                            </li>
                            <li class="list-group-item bg-transparent border-light py-2.5 px-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="pe-2">
                                        <div class="fw-semibold text-primary"><i class="bi bi-info-circle-fill me-1"></i>New Admission Request</div>
                                        <div class="text-muted small">Harish Mehta's guardian uploaded intake records.</div>
                                    </div>
                                    <span class="text-muted small font-monospace" style="font-size: 0.7rem;">1h ago</span>
                                </div>
                            </li>
                            <li class="list-group-item bg-transparent border-light py-2.5 px-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="pe-2">
                                        <div class="fw-semibold text-warning"><i class="bi bi-envelope-open-fill me-1"></i>Visitor Verification Request</div>
                                        <div class="text-muted small">Ravi Devi requested checkout clearance verification.</div>
                                    </div>
                                    <span class="text-muted small font-monospace" style="font-size: 0.7rem;">2h ago</span>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="card-footer bg-white border-top border-light text-center py-2.5">
                        <a href="notifications.php" class="text-decoration-none small fw-semibold">View all alerts</a>
                    </div>
                </div>
            </div>

        </div>

    </div>
</main>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
