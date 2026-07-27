<?php
/**
 * SevaNest — Admin Settings
 * File     : modules/admin/settings.php
 * Version  : 1.0
 */

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/functions.php';

// Require Admin login
require_login();
require_role(['Admin', 'Old Age Home Admin']);

$base_path = '../../';
$page_title = 'Admin Settings | SevaNest';

// Handle Form Submission Mock
$formSuccess = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formSuccess = 'System settings updated successfully!';
}

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'admin';
$currentPage   = 'settings.php';
$sn_asset_root = "../../assets";
include '../../includes/sidebar.php';
?>

<main id="sn-main-content" role="main" aria-label="Admin Settings Content" class="p-4 flex-grow-1">
    <div class="container-fluid">

        <?php if ($formSuccess): ?>
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> <?php echo sn_e($formSuccess); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Page Header -->
        <div class="mb-4">
            <h3 class="fw-bold mb-0 text-dark">System Settings</h3>
            <small class="text-muted">Configure facility options, alert notifications, and security defaults</small>
        </div>

        <div class="row g-4">
            <!-- Left Side: Navigation Quick Tabs -->
            <div class="col-lg-3">
                <div class="card border-0 shadow-sm rounded-3 bg-white p-3">
                    <div class="nav flex-column nav-pills" id="settings-tab" role="tablist" aria-orientation="vertical">
                        <button class="nav-link active text-start py-2.5 px-3 mb-1" id="tab-general" data-bs-toggle="pill" data-bs-target="#panel-general" type="button" role="tab"><i class="bi bi-sliders me-2"></i>General Config</button>
                        <button class="nav-link text-start py-2.5 px-3 mb-1" id="tab-security" data-bs-toggle="pill" data-bs-target="#panel-security" type="button" role="tab"><i class="bi bi-shield-lock me-2"></i>Security</button>
                        <button class="nav-link text-start py-2.5 px-3" id="tab-notifications" data-bs-toggle="pill" data-bs-target="#panel-notifications" type="button" role="tab"><i class="bi bi-bell me-2"></i>Notifications</button>
                    </div>
                </div>
            </div>

            <!-- Right Side: Configuration Panels -->
            <div class="col-lg-9">
                <div class="tab-content" id="settings-tabContent">
                    
                    <!-- Panel 1: General Config -->
                    <div class="tab-pane fade show active" id="panel-general" role="tabpanel" aria-labelledby="tab-general">
                        <div class="card border-0 shadow-sm rounded-3 bg-white p-4">
                            <h5 class="fw-bold mb-1 text-dark">General Facility Configuration</h5>
                            <p class="text-muted small mb-4">Set up global system behaviors and wing occupancy levels.</p>
                            
                            <form method="POST" action="settings.php">
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label for="home_name" class="form-label fw-semibold text-dark small">Old Age Home Name</label>
                                        <input type="text" id="home_name" class="form-control" value="SevaNest Old Age Home Office">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="max_occupancy" class="form-label fw-semibold text-dark small">Maximum Bed Occupancy</label>
                                        <input type="number" id="max_occupancy" class="form-control" value="100">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="session_timeout" class="form-label fw-semibold text-dark small">Automatic Session Timeout</label>
                                        <select id="session_timeout" class="form-select">
                                            <option>15 Minutes</option>
                                            <option selected>30 Minutes</option>
                                            <option>1 Hour</option>
                                            <option>2 Hours</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="timezone" class="form-label fw-semibold text-dark small">System Timezone</label>
                                        <select id="timezone" class="form-select">
                                            <option>UTC (Coordinated Universal Time)</option>
                                            <option selected>IST (Indian Standard Time)</option>
                                            <option>EST (Eastern Standard Time)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end gap-2 border-top pt-3">
                                    <button type="submit" class="btn btn-sm btn-primary fw-semibold">Save Settings</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Panel 2: Security -->
                    <div class="tab-pane fade" id="panel-security" role="tabpanel" aria-labelledby="tab-security">
                        <div class="card border-0 shadow-sm rounded-3 bg-white p-4">
                            <h5 class="fw-bold mb-1 text-dark">Security and Account Access</h5>
                            <p class="text-muted small mb-4">Update your administrative credentials and toggle multi-factor settings.</p>
                            
                            <form method="POST" action="settings.php">
                                <div class="row g-3 mb-4">
                                    <div class="col-md-12">
                                        <label for="current_password" class="form-label fw-semibold text-dark small">Current Password</label>
                                        <input type="password" id="current_password" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="new_password" class="form-label fw-semibold text-dark small">New Password</label>
                                        <input type="password" id="new_password" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="confirm_password" class="form-label fw-semibold text-dark small">Confirm New Password</label>
                                        <input type="password" id="confirm_password" class="form-control" required>
                                    </div>
                                    <div class="col-12 mt-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="mfa_switch" checked style="cursor: pointer;">
                                            <label class="form-check-label fw-semibold text-dark small" for="mfa_switch" style="cursor: pointer;">Enable Multi-Factor OTP on Login</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end gap-2 border-top pt-3">
                                    <button type="submit" class="btn btn-sm btn-primary fw-semibold">Change Password</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Panel 3: Notifications -->
                    <div class="tab-pane fade" id="panel-notifications" role="tabpanel" aria-labelledby="tab-notifications">
                        <div class="card border-0 shadow-sm rounded-3 bg-white p-4">
                            <h5 class="fw-bold mb-1 text-dark">Alert and Notification Routing</h5>
                            <p class="text-muted small mb-4">Select which notification logs are enabled or dispatched to emails.</p>
                            
                            <form method="POST" action="settings.php">
                                <div class="d-flex flex-column gap-3 mb-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="notify_admissions" checked style="cursor: pointer;">
                                        <label class="form-check-label text-dark small fw-semibold" for="notify_admissions" style="cursor: pointer;">New Admission Request Alerts</label>
                                        <span class="text-muted d-block small">Get real-time notification alerts when families submit an intake booking.</span>
                                    </div>
                                    <hr class="border-light my-1">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="notify_visitors" checked style="cursor: pointer;">
                                        <label class="form-check-label text-dark small fw-semibold" for="notify_visitors" style="cursor: pointer;">Visitor Entry/Exit Alerts</label>
                                        <span class="text-muted d-block small">Get logging notifications when visitors check in or out.</span>
                                    </div>
                                    <hr class="border-light my-1">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="notify_meds" style="cursor: pointer;">
                                        <label class="form-check-label text-dark small fw-semibold" for="notify_meds" style="cursor: pointer;">Critical Medical Logs</label>
                                        <span class="text-muted d-block small">Forward critical health status updates from doctor portal to administrator mail.</span>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end gap-2 border-top pt-3">
                                    <button type="submit" class="btn btn-sm btn-primary fw-semibold">Save Routing Settings</button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</main>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
