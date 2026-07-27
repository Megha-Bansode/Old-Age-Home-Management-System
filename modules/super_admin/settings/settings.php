<?php
/**
 * SevaNest — Super Admin System Settings
 * 
 * Provides configuration interfaces for General Organization Details, Security & Authentication Policies,
 * Appearance & Notifications, and System Maintenance (Backup, Restore, and Logs).
 */

$base_path = '../../../';
$page_title = 'System Settings';
$active_page = 'settings';
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
        
        <!-- Page Header Strip -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4 p-4 bg-white rounded-4 shadow-sm border-start border-4 border-dark">
            <div>
                <h2 class="fw-bold text-dark mb-0">System Settings & Platform Configuration</h2>
                <p class="text-muted small mb-0 mt-1">Manage organization details, security policies, theme preferences, backups, and system logs.</p>
            </div>
            <button class="btn btn-primary d-flex align-items-center gap-2 px-4 py-2 rounded-3" onclick="alert('System settings saved successfully!');">
                <i class="bi bi-save-fill"></i>
                <span>Save All Changes</span>
            </button>
        </div>

        <div class="row g-4">
            
            <!-- Left Navigation Menu Tabs -->
            <div class="col-12 col-md-4 col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 bg-white p-3">
                    <div class="nav flex-column nav-pills" id="settingsTabs" role="tablist" aria-orientation="vertical">
                        
                        <button class="nav-link active text-start fw-semibold py-3 px-3 rounded-3 mb-1" id="tab-general" data-bs-toggle="pill" data-bs-target="#content-general" type="button" role="tab">
                            <i class="bi bi-building-gear me-2"></i>General Details
                        </button>
                        
                        <button class="nav-link text-start fw-semibold py-3 px-3 rounded-3 mb-1" id="tab-security" data-bs-toggle="pill" data-bs-target="#content-security" type="button" role="tab">
                            <i class="bi bi-shield-lock-fill me-2"></i>Security & Access
                        </button>
                        
                        <button class="nav-link text-start fw-semibold py-3 px-3 rounded-3 mb-1" id="tab-appearance" data-bs-toggle="pill" data-bs-target="#content-appearance" type="button" role="tab">
                            <i class="bi bi-palette-fill me-2"></i>Appearance & Theme
                        </button>
                        
                        <button class="nav-link text-start fw-semibold py-3 px-3 rounded-3 mb-1" id="tab-system" data-bs-toggle="pill" data-bs-target="#content-system" type="button" role="tab">
                            <i class="bi bi-database-gear me-2"></i>Backup & Maintenance
                        </button>
                        
                    </div>
                </div>
            </div>

            <!-- Right Settings Form Content Area -->
            <div class="col-12 col-md-8 col-lg-9">
                <div class="tab-content" id="settingsTabsContent">
                    
                    <!-- 1. GENERAL SETTINGS -->
                    <div class="tab-pane fade show active" id="content-general" role="tabpanel" aria-labelledby="tab-general">
                        <div class="card border-0 shadow-sm rounded-4 bg-white">
                            <div class="card-header bg-transparent border-bottom p-4">
                                <h4 class="fw-bold text-dark mb-1"><i class="bi bi-building-gear me-2 text-primary"></i>Organization Information</h4>
                                <p class="text-muted small mb-0">Update the primary organization contact details displayed across reports and receipts.</p>
                            </div>
                            <div class="card-body p-4">
                                <form id="formGeneralSettings">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label for="orgName" class="form-label fw-semibold">Organization Name</label>
                                            <input type="text" class="form-control rounded-3" id="orgName" value="SevaNest Old Age Home Management System">
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <label for="orgEmail" class="form-label fw-semibold">Official Contact Email</label>
                                            <input type="email" class="form-control rounded-3" id="orgEmail" value="care@sevanest.in">
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <label for="orgPhone" class="form-label fw-semibold">Contact Phone Number</label>
                                            <input type="text" class="form-control rounded-3" id="orgPhone" value="+91 98765 43210">
                                        </div>

                                        <div class="col-12">
                                            <label for="orgAddress" class="form-label fw-semibold">Headquarters Address</label>
                                            <textarea class="form-control rounded-3" id="orgAddress" rows="3">12, Shanti Nagar, Pune — 411001, Maharashtra, India</textarea>
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <label for="orgWebsite" class="form-label fw-semibold">Official Website URL</label>
                                            <input type="url" class="form-control rounded-3" id="orgWebsite" value="https://www.sevanest.in">
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <label for="orgRegNo" class="form-label fw-semibold">Registration / Trust Registration No.</label>
                                            <input type="text" class="form-control rounded-3" id="orgRegNo" value="REG-MH-2023-88912">
                                        </div>
                                    </div>
                                    <hr class="my-4">
                                    <div class="text-end">
                                        <button type="button" class="btn btn-primary rounded-3 px-4" onclick="alert('General settings updated.');">
                                            Save General Settings
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- 2. SECURITY SETTINGS -->
                    <div class="tab-pane fade" id="content-security" role="tabpanel" aria-labelledby="tab-security">
                        <div class="card border-0 shadow-sm rounded-4 bg-white">
                            <div class="card-header bg-transparent border-bottom p-4">
                                <h4 class="fw-bold text-dark mb-1"><i class="bi bi-shield-lock-fill me-2 text-danger"></i>Security & Session Management</h4>
                                <p class="text-muted small mb-0">Configure authentication security policies, password changes, and idle session timeouts.</p>
                            </div>
                            <div class="card-body p-4">
                                <form id="formSecuritySettings">
                                    <h5 class="fw-bold text-dark mb-3">Change Administrator Password</h5>
                                    <div class="row g-3 mb-4">
                                        <div class="col-12 col-md-4">
                                            <label for="currentPassword" class="form-label fw-semibold">Current Password</label>
                                            <input type="password" class="form-control rounded-3" id="currentPassword" placeholder="••••••••">
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <label for="newPassword" class="form-label fw-semibold">New Password</label>
                                            <input type="password" class="form-control rounded-3" id="newPassword" placeholder="Minimum 8 characters">
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <label for="confirmPassword" class="form-label fw-semibold">Confirm New Password</label>
                                            <input type="password" class="form-control rounded-3" id="confirmPassword" placeholder="••••••••">
                                        </div>
                                    </div>

                                    <hr class="my-4">

                                    <h5 class="fw-bold text-dark mb-3">Session & Login Controls</h5>
                                    <div class="row g-3">
                                        <div class="col-12 col-md-6">
                                            <label for="sessionTimeout" class="form-label fw-semibold">Idle Session Timeout</label>
                                            <select class="form-select rounded-3" id="sessionTimeout">
                                                <option>15 Minutes</option>
                                                <option selected>30 Minutes (Recommended)</option>
                                                <option>60 Minutes</option>
                                                <option>Disabled (Never expire)</option>
                                            </select>
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <label for="maxLoginAttempts" class="form-label fw-semibold">Max Failed Login Attempts</label>
                                            <select class="form-select rounded-3" id="maxLoginAttempts">
                                                <option>3 Attempts</option>
                                                <option selected>5 Attempts</option>
                                                <option>10 Attempts</option>
                                            </select>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-check form-switch mt-2">
                                                <input class="form-check-input" type="checkbox" id="enable2FA" checked>
                                                <label class="form-check-label fw-semibold" for="enable2FA">
                                                    Require Two-Factor Authentication (2FA) for Super Admin Logins
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="my-4">
                                    <div class="text-end">
                                        <button type="button" class="btn btn-primary rounded-3 px-4" onclick="alert('Security preferences updated.');">
                                            Update Security Settings
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- 3. APPEARANCE SETTINGS -->
                    <div class="tab-pane fade" id="content-appearance" role="tabpanel" aria-labelledby="tab-appearance">
                        <div class="card border-0 shadow-sm rounded-4 bg-white">
                            <div class="card-header bg-transparent border-bottom p-4">
                                <h4 class="fw-bold text-dark mb-1"><i class="bi bi-palette-fill me-2 text-warning"></i>Appearance & Theme</h4>
                                <p class="text-muted small mb-0">Customize visual theme colors, layout density, and notification alerts.</p>
                            </div>
                            <div class="card-body p-4">
                                <form id="formAppearanceSettings">
                                    <h5 class="fw-bold text-dark mb-3">System Color Palette Theme</h5>
                                    <div class="row g-3 mb-4">
                                        <div class="col-6 col-md-3">
                                            <div class="border rounded-4 p-3 text-center bg-light border-primary border-2 shadow-sm">
                                                <div class="d-flex justify-content-center gap-1 mb-2">
                                                    <span class="d-inline-block rounded-circle" style="width:16px; height:16px; background:#6B9080;"></span>
                                                    <span class="d-inline-block rounded-circle" style="width:16px; height:16px; background:#D4A373;"></span>
                                                    <span class="d-inline-block rounded-circle" style="width:16px; height:16px; background:#F6F4EC;"></span>
                                                </div>
                                                <div class="fw-bold small">SevaNest Standard (Soft Green & Gold)</div>
                                                <span class="badge bg-primary mt-2">Active</span>
                                            </div>
                                        </div>
                                    </div>

                                    <h5 class="fw-bold text-dark mb-3">System Notification Alerts</h5>
                                    <div class="d-flex flex-column gap-3 mb-4">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="notifEmail" checked>
                                            <label class="form-check-label fw-semibold" for="notifEmail">
                                                Send Email Notifications for Emergency Resident Medical Alerts
                                            </label>
                                        </div>

                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="notifDonation" checked>
                                            <label class="form-check-label fw-semibold" for="notifDonation">
                                                Notify Super Admin when new high-value donations are received
                                            </label>
                                        </div>

                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="notifUserAdd" checked>
                                            <label class="form-check-label fw-semibold" for="notifUserAdd">
                                                Notify when new staff or user accounts are created
                                            </label>
                                        </div>
                                    </div>

                                    <hr class="my-4">
                                    <div class="text-end">
                                        <button type="button" class="btn btn-primary rounded-3 px-4" onclick="alert('Appearance settings saved.');">
                                            Save Appearance Settings
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- 4. SYSTEM BACKUP & RESTORE -->
                    <div class="tab-pane fade" id="content-system" role="tabpanel" aria-labelledby="tab-system">
                        <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                            <div class="card-header bg-transparent border-bottom p-4">
                                <h4 class="fw-bold text-dark mb-1"><i class="bi bi-database-gear me-2 text-info"></i>Database Backup & Restore</h4>
                                <p class="text-muted small mb-0">Create database snapshots, restore from previous SQL backups, or view system logs.</p>
                            </div>
                            <div class="card-body p-4">
                                
                                <div class="row g-4 mb-4">
                                    <div class="col-12 col-md-6">
                                        <div class="p-4 bg-light rounded-4 border h-100">
                                            <h5 class="fw-bold text-dark mb-2"><i class="bi bi-download me-2 text-success"></i>Create System Backup</h5>
                                            <p class="text-muted small mb-3">
                                                Generate a full SQL snapshot of the database including users, residents, medical logs, and donation receipts.
                                            </p>
                                            <button class="btn btn-success rounded-3 w-100 d-flex align-items-center justify-content-center gap-2 py-2" onclick="alert('Initiating database backup... Snapshot generated: sevanest_backup_20260725.sql');">
                                                <i class="bi bi-cloud-arrow-down-fill"></i>
                                                <span>Generate & Download Backup</span>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <div class="p-4 bg-light rounded-4 border h-100">
                                            <h5 class="fw-bold text-dark mb-2"><i class="bi bi-upload me-2 text-warning"></i>Restore Database</h5>
                                            <p class="text-muted small mb-2">
                                                Upload a valid `.sql` backup file to restore database records to a previous state.
                                            </p>
                                            <input type="file" class="form-control rounded-3 mb-3" accept=".sql,.gz">
                                            <button class="btn btn-warning text-dark rounded-3 w-100 d-flex align-items-center justify-content-center gap-2 py-2" onclick="alert('Restore snapshot upload triggered (Demo).');">
                                                <i class="bi bi-arrow-counterclockwise"></i>
                                                <span>Restore Backup File</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-journal-text me-2 text-primary"></i>Recent System Activity Logs</h5>
                                <div class="table-responsive bg-light rounded-3 border p-2">
                                    <table class="table table-sm table-borderless align-middle mb-0" style="font-size: 0.85rem;">
                                        <thead>
                                            <tr class="border-bottom">
                                                <th>Timestamp</th>
                                                <th>Event Type</th>
                                                <th>Operator</th>
                                                <th>IP Address</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="text-muted">2026-07-25 03:00:01</td>
                                                <td><span class="badge bg-secondary-subtle text-dark">Automated Cron</span></td>
                                                <td>System Job</td>
                                                <td>127.0.0.1</td>
                                                <td><span class="text-success fw-semibold">Backup Success</span></td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">2026-07-25 02:45:12</td>
                                                <td><span class="badge bg-primary-subtle text-primary">User Login</span></td>
                                                <td>Super Admin</td>
                                                <td>192.168.1.10</td>
                                                <td><span class="text-success fw-semibold">Authenticated</span></td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">2026-07-24 18:30:00</td>
                                                <td><span class="badge bg-info-subtle text-info">Permission Update</span></td>
                                                <td>Super Admin</td>
                                                <td>192.168.1.10</td>
                                                <td><span class="text-success fw-semibold">Saved</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>

    </main>

</div>

<?php require_once $base_path . 'includes/footer.php'; ?>
