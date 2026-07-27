<?php
/**
 * SevaNest — Super Admin Dashboard
 * 
 * Provides global overview, stat counters, recent system activities, quick actions,
 * and system health monitoring for Super Administrators.
 */

$base_path = '../../';
$page_title = 'Super Admin Dashboard';
$active_page = 'dashboard';
$module_name = 'Super Admin Module';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_name'])) {
    $_SESSION['user_name'] = 'Super Admin';
    $_SESSION['user_role'] = 'Super Admin';
}

require_once $base_path . 'includes/header.php';
require_once $base_path . 'includes/navbar.php';
?>

<div class="d-flex min-vh-100 position-relative">
    
    <!-- Sidebar Include -->
    <?php require_once $base_path . 'includes/sidebar.php'; ?>

    <!-- Main Content Body -->
    <main class="main-content flex-grow-1 bg-light p-4">
        
        <!-- Welcome Hero Banner -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" 
             style="background: linear-gradient(135deg, var(--color-primary) 0%, #4E7466 100%); color: #FFFFFF;">
            <div class="card-body p-4 p-lg-5 position-relative">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <span class="badge bg-white text-dark mb-2 px-3 py-2 rounded-pill fw-semibold" style="font-size: 0.8rem;">
                            <i class="bi bi-shield-fill-check me-1 text-warning"></i> Super Admin Control Center
                        </span>
                        <h1 class="display-6 fw-bold mb-2">Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
                        <p class="lead opacity-90 mb-0" style="font-size: 1rem; max-width: 650px;">
                            Monitor system activity, manage access roles, generate system-wide reports, and oversee all elder care administration across SevaNest.
                        </p>
                    </div>
                    <div class="col-lg-4 text-end d-none d-lg-block">
                        <i class="bi bi-building-gear display-1 opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 7 Stat Cards Grid -->
        <div class="row g-3 mb-4">
            
            <!-- 1. Total Users -->
            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 h-100 p-3 bg-white border-start border-4 border-primary">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small fw-semibold text-uppercase">Total Users</div>
                            <div class="fs-3 fw-bold text-dark mt-1">128</div>
                            <small class="text-success fw-medium"><i class="bi bi-arrow-up-right me-1"></i>+12 this month</small>
                        </div>
                        <div class="rounded-3 p-3 bg-primary-subtle text-primary">
                            <i class="bi bi-people-fill fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Total Residents -->
            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 h-100 p-3 bg-white border-start border-4 border-success">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small fw-semibold text-uppercase">Total Residents</div>
                            <div class="fs-3 fw-bold text-dark mt-1">245</div>
                            <small class="text-success fw-medium"><i class="bi bi-check-circle me-1"></i>Active Care</small>
                        </div>
                        <div class="rounded-3 p-3 bg-success-subtle text-success">
                            <i class="bi bi-heart-pulse-fill fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Total Staff -->
            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 h-100 p-3 bg-white border-start border-4 border-warning">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small fw-semibold text-uppercase">Total Staff</div>
                            <div class="fs-3 fw-bold text-dark mt-1">42</div>
                            <small class="text-muted fw-medium">On Duty: 18</small>
                        </div>
                        <div class="rounded-3 p-3 bg-warning-subtle text-warning">
                            <i class="bi bi-person-badge-fill fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Total Doctors -->
            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 h-100 p-3 bg-white border-start border-4 border-info">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small fw-semibold text-uppercase">Total Doctors</div>
                            <div class="fs-3 fw-bold text-dark mt-1">14</div>
                            <small class="text-info fw-medium"><i class="bi bi-activity me-1"></i>5 On Call Today</small>
                        </div>
                        <div class="rounded-3 p-3 bg-info-subtle text-info">
                            <i class="bi bi-hospital-fill fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 5. Total Caretakers -->
            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 h-100 p-3 bg-white border-start border-4 border-secondary">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small fw-semibold text-uppercase">Total Caretakers</div>
                            <div class="fs-3 fw-bold text-dark mt-1">28</div>
                            <small class="text-muted fw-medium">24/7 Shift Cover</small>
                        </div>
                        <div class="rounded-3 p-3 bg-secondary-subtle text-dark">
                            <i class="bi bi-person-heart fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 6. Total Family Members -->
            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 h-100 p-3 bg-white border-start border-4 border-purple" style="border-left-color: #6f42c1 !important;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small fw-semibold text-uppercase">Family Members</div>
                            <div class="fs-3 fw-bold text-dark mt-1">186</div>
                            <small class="text-muted fw-medium">Connected</small>
                        </div>
                        <div class="rounded-3 p-3 text-purple" style="background: rgba(111, 66, 193, 0.1); color: #6f42c1;">
                            <i class="bi bi-house-heart-fill fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 7. Total Donors -->
            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 h-100 p-3 bg-white border-start border-4 border-danger">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small fw-semibold text-uppercase">Total Donors</div>
                            <div class="fs-3 fw-bold text-dark mt-1">94</div>
                            <small class="text-danger fw-medium">₹25.4L Contributed</small>
                        </div>
                        <div class="rounded-3 p-3 bg-danger-subtle text-danger">
                            <i class="bi bi-cash-coin fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Health Quick Card -->
            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 h-100 p-3 bg-white border-start border-4 border-dark">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small fw-semibold text-uppercase">System Health</div>
                            <div class="fs-3 fw-bold text-success mt-1">99.9%</div>
                            <small class="text-success fw-medium"><i class="bi bi-shield-check me-1"></i>All Services Normal</small>
                        </div>
                        <div class="rounded-3 p-3 bg-dark-subtle text-dark">
                            <i class="bi bi-cpu-fill fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Main Grid Row: Activities & Quick Actions -->
        <div class="row g-4 mb-4">
            
            <!-- Left: Recent System Activities Table -->
            <div class="col-12 col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 bg-white h-100">
                    <div class="card-header bg-transparent border-bottom p-3 d-flex align-items-center justify-content-between">
                        <h5 class="fw-bold text-dark mb-0">
                            <i class="bi bi-clock-history me-2 text-primary"></i>Recent System Activities
                        </h5>
                        <a href="reports/reports.php" class="btn btn-sm btn-outline-primary rounded-pill">View All Logs</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">User</th>
                                        <th>Role</th>
                                        <th>Action</th>
                                        <th>Timestamp</th>
                                        <th class="pe-3">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="ps-3 fw-semibold">Dr. Rajesh Kumar</td>
                                        <td><span class="badge bg-info-subtle text-info">Doctor</span></td>
                                        <td>Updated medical prescription for Resident #104</td>
                                        <td class="text-muted small">10 mins ago</td>
                                        <td class="pe-3"><span class="badge bg-success-subtle text-success">Completed</span></td>
                                    </tr>
                                    <tr>
                                        <td class="ps-3 fw-semibold">Ananya Verma</td>
                                        <td><span class="badge bg-warning-subtle text-warning">Caretaker</span></td>
                                        <td>Logged daily vital signs checkup</td>
                                        <td class="text-muted small">25 mins ago</td>
                                        <td class="pe-3"><span class="badge bg-success-subtle text-success">Completed</span></td>
                                    </tr>
                                    <tr>
                                        <td class="ps-3 fw-semibold">Suresh Patel</td>
                                        <td><span class="badge bg-danger-subtle text-danger">Donor</span></td>
                                        <td>Contributed ₹15,000 via online donation portal</td>
                                        <td class="text-muted small">1 hour ago</td>
                                        <td class="pe-3"><span class="badge bg-success-subtle text-success">Verified</span></td>
                                    </tr>
                                    <tr>
                                        <td class="ps-3 fw-semibold">Super Admin</td>
                                        <td><span class="badge bg-primary-subtle text-primary">Super Admin</span></td>
                                        <td>Modified role permissions for Caretakers</td>
                                        <td class="text-muted small">3 hours ago</td>
                                        <td class="pe-3"><span class="badge bg-success-subtle text-success">Saved</span></td>
                                    </tr>
                                    <tr>
                                        <td class="ps-3 fw-semibold">Meera Sharma</td>
                                        <td><span class="badge bg-secondary-subtle text-dark">Family Member</span></td>
                                        <td>Booked weekend visit request for Resident #88</td>
                                        <td class="text-muted small">5 hours ago</td>
                                        <td class="pe-3"><span class="badge bg-warning-subtle text-warning">Pending</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Quick Actions & System Status -->
            <div class="col-12 col-lg-4 d-flex flex-column gap-4">
                
                <!-- Quick Actions Card -->
                <div class="card border-0 shadow-sm rounded-4 bg-white">
                    <div class="card-header bg-transparent border-bottom p-3">
                        <h5 class="fw-bold text-dark mb-0">
                            <i class="bi bi-lightning-charge-fill me-2 text-warning"></i>Quick Actions
                        </h5>
                    </div>
                    <div class="card-body p-3 d-flex flex-column gap-2">
                        <a href="user_management/user_management.php" class="btn btn-primary w-100 d-flex align-items-center justify-content-between p-3 rounded-3 text-white text-decoration-none">
                            <span class="fw-semibold"><i class="bi bi-person-plus-fill me-2"></i>Add New System User</span>
                            <i class="bi bi-chevron-right"></i>
                        </a>
                        <a href="role_management/role_management.php" class="btn btn-outline-primary w-100 d-flex align-items-center justify-content-between p-3 rounded-3 text-decoration-none">
                            <span class="fw-semibold"><i class="bi bi-shield-lock me-2"></i>Configure Role Access</span>
                            <i class="bi bi-chevron-right"></i>
                        </a>
                        <a href="reports/reports.php" class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-between p-3 rounded-3 text-decoration-none text-dark">
                            <span class="fw-semibold"><i class="bi bi-file-earmark-text me-2"></i>Generate Reports</span>
                            <i class="bi bi-chevron-right"></i>
                        </a>
                        <a href="settings/settings.php" class="btn btn-outline-dark w-100 d-flex align-items-center justify-content-between p-3 rounded-3 text-decoration-none">
                            <span class="fw-semibold"><i class="bi bi-database-gear me-2"></i>System Backup & Settings</span>
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </div>
                </div>

                <!-- System Status Panel -->
                <div class="card border-0 shadow-sm rounded-4 bg-white">
                    <div class="card-header bg-transparent border-bottom p-3">
                        <h5 class="fw-bold text-dark mb-0">
                            <i class="bi bi-activity me-2 text-success"></i>System Health & Infrastructure
                        </h5>
                    </div>
                    <div class="card-body p-3">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1 small fw-semibold">
                                <span>Database Capacity</span>
                                <span class="text-success">28% Used</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: 28%"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1 small fw-semibold">
                                <span>Server Load</span>
                                <span class="text-primary">14% Normal</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: 14%"></div>
                            </div>
                        </div>

                        <div>
                            <div class="d-flex justify-content-between mb-1 small fw-semibold">
                                <span>Storage Allocation</span>
                                <span class="text-warning">45% Used</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: 45%"></div>
                            </div>
                        </div>

                        <hr class="my-3 opacity-25">
                        
                        <div class="d-flex align-items-center justify-content-between text-muted small">
                            <span><i class="bi bi-hdd-network me-1"></i>Automated Backup:</span>
                            <strong class="text-dark">Today, 03:00 AM</strong>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </main>

</div>

<?php require_once $base_path . 'includes/footer.php'; ?>
