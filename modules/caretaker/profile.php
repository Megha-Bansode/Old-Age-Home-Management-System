<?php
/**
 * SevaNest — Caretaker Profile Page
 * File     : modules/caretaker/profile.php
 * Version  : 1.0
 */

require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../config/database.php';

// Require Caretaker login
require_login();
require_role('Caretaker');

$base_path = '../../';
$page_title = 'Caretaker Profile | SevaNest';

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'caretaker';
$currentPage   = 'profile.php';
$sn_asset_root = "../../assets";
include '../../includes/sidebar.php';
?>

<main id="sn-main-content" role="main" aria-label="Caretaker profile content" class="p-4 flex-grow-1">
    <div class="container-fluid">
        
        <!-- Header Strip -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h3 class="fw-bold mb-0 text-dark">My Profile</h3>
                <small class="text-muted">Manage your caregiver contact details, wing assignments, and shifts roster.</small>
            </div>
            <button class="btn btn-primary" id="editProfileBtn"><i class="bi bi-pencil-square me-2"></i>Edit Profile</button>
        </div>

        <!-- Two Column Layout -->
        <div class="row g-4">
            
            <!-- Left Side · Profile Card -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-3 bg-white text-center p-4">
                    <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold mx-auto mb-3" style="width: 100px; height: 100px; font-size: 2.5rem;">
                        SK
                    </div>
                    <h5 class="fw-bold mb-1 text-dark">Suresh Kumar</h5>
                    <p class="text-muted small mb-4">Head Caregiver (Wing B)</p>
                    <hr class="border-light w-100 my-3">
                    <div class="text-start">
                        <div class="mb-3">
                            <span class="text-muted d-block small"><i class="bi bi-envelope me-1"></i>Email Address</span>
                            <span class="text-dark fw-semibold">caretaker@sevanest.com</span>
                        </div>
                        <div class="mb-3">
                            <span class="text-muted d-block small"><i class="bi bi-telephone me-1"></i>Contact Number</span>
                            <span class="text-dark fw-semibold">+91 98765 43212</span>
                        </div>
                        <div class="mb-1">
                            <span class="text-muted d-block small"><i class="bi bi-calendar-event me-1"></i>Shift Timing</span>
                            <span class="badge bg-success-subtle text-success mt-1">Evening (02:00 PM - 10:00 PM)</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side · Details & Tasks -->
            <div class="col-lg-8">
                
                <!-- Caretaker Responsibilities -->
                <div class="card border-0 shadow-sm rounded-3 bg-white mb-4 p-4">
                    <h5 class="fw-bold mb-1 text-dark">Assigned Duties &amp; Wing Info</h5>
                    <p class="text-muted small mb-4">Summary of caretaking wing and assigned resident wings.</p>
                    <div class="row g-3 fs-6">
                        <div class="col-md-6">
                            <span class="text-muted small d-block">Wing Assignment</span>
                            <span class="text-dark fw-semibold">Wing B (Rooms B-101 to B-120)</span>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted small d-block">Report Manager</span>
                            <span class="text-dark fw-semibold">Anita Verma (Admin)</span>
                        </div>
                        <div class="col-12 mt-3 pt-3 border-top d-flex gap-2">
                            <a href="attendance.php" class="btn btn-sm btn-outline-primary fw-semibold"><i class="bi bi-check-circle me-1"></i>Resident Attendance</a>
                            <a href="meals.php" class="btn btn-sm btn-outline-secondary fw-semibold"><i class="bi bi-egg-fried me-1"></i>Meals Roster</a>
                        </div>
                    </div>
                </div>

                <!-- Recent Logs -->
                <div class="card border-0 shadow-sm rounded-3 bg-white">
                    <div class="card-header bg-white border-bottom border-light p-3">
                        <h5 class="card-title fw-bold mb-0 text-dark">My Activity Logs</h5>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush" style="font-size: 0.875rem;">
                            <li class="list-group-item bg-transparent border-light py-3 px-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1 fw-semibold text-dark"><i class="bi bi-clipboard-plus text-primary me-2"></i>Logged Wing B Daily Attendance</h6>
                                        <small class="text-muted">Recorded check-ins for 18 residents.</small>
                                    </div>
                                    <span class="text-muted small">3 hours ago</span>
                                </div>
                            </li>
                            <li class="list-group-item bg-transparent border-light py-3 px-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1 fw-semibold text-dark"><i class="bi bi-egg-fried text-success me-2"></i>Meals Distribution Verified</h6>
                                        <small class="text-muted">Low-sodium dietary lunch served to Wing B patients.</small>
                                    </div>
                                    <span class="text-muted small">5 hours ago</span>
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
