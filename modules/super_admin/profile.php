<?php
/**
 * SevaNest — Super Admin Profile Page
 * File     : modules/super_admin/profile.php
 * Version  : 1.0
 */

require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../config/database.php';

// Require Super Admin login
require_login();
require_role('Super Admin');

$base_path = '../../';
$page_title = 'Super Admin Profile | SevaNest';

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'super_admin';
$currentPage   = 'profile.php';
$sn_asset_root = "../../assets";
include '../../includes/sidebar.php';
?>

<main id="sn-main-content" role="main" aria-label="Super Admin profile content" class="p-4 flex-grow-1">
    <div class="container-fluid">
        
        <!-- Header Strip -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h3 class="fw-bold mb-0 text-dark">Super Admin Profile</h3>
                <small class="text-muted">Manage your administrator details, security credentials, and control access permissions.</small>
            </div>
            <button class="btn btn-primary" id="editProfileBtn"><i class="bi bi-pencil-square me-2"></i>Edit Profile</button>
        </div>

        <!-- Two Column Layout -->
        <div class="row g-4">
            
            <!-- Left Side · Profile Card -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-3 bg-white text-center p-4">
                    <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold mx-auto mb-3" style="width: 100px; height: 100px; font-size: 2.5rem;">
                        RS
                    </div>
                    <h5 class="fw-bold mb-1 text-dark">Rajesh Sharma</h5>
                    <p class="text-muted small mb-4">Super Administrator</p>
                    <hr class="border-light w-100 my-3">
                    <div class="text-start">
                        <div class="mb-3">
                            <span class="text-muted d-block small"><i class="bi bi-envelope me-1"></i>Email Address</span>
                            <span class="text-dark fw-semibold">superadmin@sevanest.com</span>
                        </div>
                        <div class="mb-3">
                            <span class="text-muted d-block small"><i class="bi bi-telephone me-1"></i>Contact Number</span>
                            <span class="text-dark fw-semibold">+91 98765 43210</span>
                        </div>
                        <div class="mb-1">
                            <span class="text-muted d-block small"><i class="bi bi-shield-check me-1"></i>Access Clearance</span>
                            <span class="badge bg-danger-subtle text-danger mt-1">Full Root Authority</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side · Logs & Preferences -->
            <div class="col-lg-8">
                
                <!-- System Privileges -->
                <div class="card border-0 shadow-sm rounded-3 bg-white mb-4 p-4">
                    <h5 class="fw-bold mb-1 text-dark">System Privileges &amp; Settings</h5>
                    <p class="text-muted small mb-4">View your clearance levels and security shortcuts.</p>
                    <div class="row g-3 fs-6">
                        <div class="col-md-6">
                            <span class="text-muted small d-block">Default Role Access</span>
                            <span class="text-dark fw-semibold">All Module Access (Admin, Doctor, Caretaker, Family, Donor)</span>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted small d-block">Security Configurations</span>
                            <span class="text-dark fw-semibold">Database backup, user role assignments, log audits.</span>
                        </div>
                        <div class="col-12 mt-3 pt-3 border-top d-flex gap-2">
                            <a href="settings/settings.php" class="btn btn-sm btn-outline-primary fw-semibold"><i class="bi bi-gear me-1"></i>System Settings</a>
                            <a href="user_management/user_management.php" class="btn btn-sm btn-outline-secondary fw-semibold"><i class="bi bi-people me-1"></i>User Directory</a>
                        </div>
                    </div>
                </div>

                <!-- Recent Activities -->
                <div class="card border-0 shadow-sm rounded-3 bg-white">
                    <div class="card-header bg-white border-bottom border-light p-3">
                        <h5 class="card-title fw-bold mb-0 text-dark">Recent Root Audit Trails</h5>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush" style="font-size: 0.875rem;">
                            <li class="list-group-item bg-transparent border-light py-3 px-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1 fw-semibold text-dark"><i class="bi bi-shield-check text-success me-2"></i>Database Schema Restored</h6>
                                        <small class="text-muted">Executed seed data mapping and security logs reset.</small>
                                    </div>
                                    <span class="text-muted small">1 hour ago</span>
                                </div>
                            </li>
                            <li class="list-group-item bg-transparent border-light py-3 px-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1 fw-semibold text-dark"><i class="bi bi-person-gear text-primary me-2"></i>Updated Role Access Rules</h6>
                                        <small class="text-muted">Modified permissions for role Doctor.</small>
                                    </div>
                                    <span class="text-muted small">Yesterday</span>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>

        </div>

    </div>
</main>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
