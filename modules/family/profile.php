<?php
/**
 * SevaNest — Family Member Profile Page
 * File     : modules/family/profile.php
 * Version  : 1.0
 */

require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../config/database.php';

// Require Family Member login
require_login();
require_role('Family Member');

$base_path = '../../';
$page_title = 'Family Profile | SevaNest';

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'family_member';
$currentPage   = 'profile.php';
$sn_asset_root = "../../assets";
include '../../includes/sidebar.php';
?>

<main id="sn-main-content" role="main" aria-label="Family Member profile content" class="p-4 flex-grow-1">
    <div class="container-fluid">
        
        <!-- Header Strip -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h3 class="fw-bold mb-0 text-dark">My Profile</h3>
                <small class="text-muted">Manage your family contact information, emergency numbers, and loved ones relationship profiles.</small>
            </div>
            <button class="btn btn-primary" id="editProfileBtn"><i class="bi bi-pencil-square me-2"></i>Edit Profile</button>
        </div>

        <!-- Two Column Layout -->
        <div class="row g-4">
            
            <!-- Left Side · Profile Card -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-3 bg-white text-center p-4">
                    <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold mx-auto mb-3" style="width: 100px; height: 100px; font-size: 2.5rem;">
                        SD
                    </div>
                    <h5 class="fw-bold mb-1 text-dark">Sunita Deshmukh</h5>
                    <p class="text-muted small mb-4">Family Representative</p>
                    <hr class="border-light w-100 my-3">
                    <div class="text-start">
                        <div class="mb-3">
                            <span class="text-muted d-block small"><i class="bi bi-envelope me-1"></i>Email Address</span>
                            <span class="text-dark fw-semibold">family@sevanest.com</span>
                        </div>
                        <div class="mb-3">
                            <span class="text-muted d-block small"><i class="bi bi-telephone me-1"></i>Contact Number</span>
                            <span class="text-dark fw-semibold">+91 98765 43215</span>
                        </div>
                        <div class="mb-1">
                            <span class="text-muted d-block small"><i class="bi bi-heart me-1"></i>Relationship</span>
                            <span class="badge bg-primary-subtle text-primary mt-1">Daughter (Kamala Devi)</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side · Details & Tasks -->
            <div class="col-lg-8">
                
                <!-- Resident Connections -->
                <div class="card border-0 shadow-sm rounded-3 bg-white mb-4 p-4">
                    <h5 class="fw-bold mb-1 text-dark">Loved One Status</h5>
                    <p class="text-muted small mb-4">Summary of the resident you represent at SevaNest.</p>
                    <div class="row g-3 fs-6">
                        <div class="col-md-6">
                            <span class="text-muted small d-block">Resident Represented</span>
                            <span class="text-dark fw-semibold">Kamala Devi (RES-2001)</span>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted small d-block">Room No.</span>
                            <span class="text-dark fw-semibold">Room A-102 (Single Suite)</span>
                        </div>
                        <div class="col-12 mt-3 pt-3 border-top d-flex gap-2">
                            <a href="resident-profile.php" class="btn btn-sm btn-outline-primary fw-semibold"><i class="bi bi-person me-1"></i>Loved One Profile</a>
                            <a href="health-updates.php" class="btn btn-sm btn-outline-secondary fw-semibold"><i class="bi bi-heart-pulse me-1"></i>Health Log</a>
                        </div>
                    </div>
                </div>

                <!-- Recent Activities -->
                <div class="card border-0 shadow-sm rounded-3 bg-white">
                    <div class="card-header bg-white border-bottom border-light p-3">
                        <h5 class="card-title fw-bold mb-0 text-dark">My Activity &amp; Visitation Logs</h5>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush" style="font-size: 0.875rem;">
                            <li class="list-group-item bg-transparent border-light py-3 px-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1 fw-semibold text-dark"><i class="bi bi-calendar-event text-primary me-2"></i>Scheduled Visit Request</h6>
                                        <small class="text-muted">Requested visiting permit for 30th July, 04:00 PM.</small>
                                    </div>
                                    <span class="text-muted small">Yesterday</span>
                                </div>
                            </li>
                            <li class="list-group-item bg-transparent border-light py-3 px-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1 fw-semibold text-dark"><i class="bi bi-credit-card text-success me-2"></i>Cleared Monthly Fees</h6>
                                        <small class="text-muted">Cleared invoice INV-9081 (Amount: ₹15,000).</small>
                                    </div>
                                    <span class="text-muted small">5 days ago</span>
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
