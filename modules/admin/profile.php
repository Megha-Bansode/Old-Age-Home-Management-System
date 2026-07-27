<?php
/**
 * SevaNest — Admin Profile
 * File     : modules/admin/profile.php
 * Version  : 1.0
 */

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/functions.php';

// Require Admin login
require_login();
require_role(['Admin', 'Old Age Home Admin']);

$base_path = '../../';
$page_title = 'Admin Profile | SevaNest';

// Handle Form Submission Mock
$formSuccess = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formSuccess = 'Profile details updated successfully!';
}

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'admin';
$currentPage   = 'profile.php';
$sn_asset_root = "../../assets";
include '../../includes/sidebar.php';

$adminName = $_SESSION['full_name'] ?? 'Anita Verma';
$adminEmail = $_SESSION['email'] ?? 'admin@sevanest.com';
?>

<main id="sn-main-content" role="main" aria-label="Admin Profile Content" class="p-4 flex-grow-1">
    <div class="container-fluid">

        <?php if ($formSuccess): ?>
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> <?php echo sn_e($formSuccess); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h3 class="fw-bold mb-0 text-dark">My Profile</h3>
                <small class="text-muted">Manage your administrator details and system preferences</small>
            </div>
        </div>

        <!-- Layout Grid -->
        <div class="row g-4">
            
            <!-- Left Side: Profile Summary Card -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-3 bg-white text-center p-4">
                    <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold mx-auto mb-3" style="width: 100px; height: 100px; font-size: 2.5rem;">
                        <?php echo sn_e(strtoupper($adminName[0])); ?>
                    </div>
                    <h5 class="fw-bold mb-1 text-dark"><?php echo sn_e($adminName); ?></h5>
                    <p class="text-muted small mb-4">System Administrator</p>
                    <hr class="border-light w-100 my-3">
                    <div class="text-start">
                        <div class="mb-3">
                            <span class="text-muted d-block small">Role Permissions</span>
                            <span class="badge bg-primary-subtle text-primary mt-1">Full Admin Operations</span>
                        </div>
                        <div class="mb-3">
                            <span class="text-muted d-block small">Assigned Facility</span>
                            <span class="text-dark fw-semibold">Administrative Wing, SevaNest Home</span>
                        </div>
                        <div class="mb-1">
                            <span class="text-muted d-block small">Joined Date</span>
                            <span class="text-dark fw-semibold">12 May 2024</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Edit Form and Preferences -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
                    <div class="card-header bg-white border-bottom border-light p-3">
                        <h5 class="card-title fw-bold mb-0 text-dark">Personal Information</h5>
                        <small class="text-muted">Modify name, contact number, and email</small>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" action="profile.php">
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="admin_fullname" class="form-label fw-semibold text-dark small">Full Name</label>
                                    <input type="text" id="admin_fullname" name="name" class="form-control" value="<?php echo sn_e($adminName); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="admin_email" class="form-label fw-semibold text-dark small">Email Address</label>
                                    <input type="email" id="admin_email" name="email" class="form-control" value="<?php echo sn_e($adminEmail); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="admin_phone" class="form-label fw-semibold text-dark small">Phone Number</label>
                                    <input type="tel" id="admin_phone" name="phone" class="form-control" value="+91 98765 43211">
                                </div>
                                <div class="col-md-6">
                                    <label for="admin_office" class="form-label fw-semibold text-dark small">Office Extension</label>
                                    <input type="text" id="admin_office" name="office" class="form-control" value="EXT-201">
                                </div>
                            </div>
                            <div class="d-flex justify-content-end gap-2 border-top pt-3">
                                <button type="reset" class="btn btn-sm btn-secondary fw-semibold">Reset</button>
                                <button type="submit" class="btn btn-sm btn-primary fw-semibold">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Admin Action Log -->
                <div class="card border-0 shadow-sm rounded-3 bg-white">
                    <div class="card-header bg-white border-bottom border-light p-3">
                        <h5 class="card-title fw-bold mb-0 text-dark">Recent Admin Activities</h5>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush" style="font-size: 0.875rem;">
                            <li class="list-group-item bg-transparent border-light py-3 px-4">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h6 class="mb-1 fw-semibold text-dark"><i class="bi bi-person-plus text-primary me-2"></i>Registered Kamala Devi</h6>
                                        <small class="text-muted">Assigned to Room A-102</small>
                                    </div>
                                    <span class="text-muted small">2 hours ago</span>
                                </div>
                            </li>
                            <li class="list-group-item bg-transparent border-light py-3 px-4">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h6 class="mb-1 fw-semibold text-dark"><i class="bi bi-clipboard-check text-success me-2"></i>Approved Admission Request</h6>
                                        <small class="text-muted">Applicant: Harish Mehta (REQ-1002)</small>
                                    </div>
                                    <span class="text-muted small">Yesterday</span>
                                </div>
                            </li>
                            <li class="list-group-item bg-transparent border-light py-3 px-4">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h6 class="mb-1 fw-semibold text-dark"><i class="bi bi-shield-lock text-warning me-2"></i>Changed Admin Security Settings</h6>
                                        <small class="text-muted">Updated session expiry to 1 hour</small>
                                    </div>
                                    <span class="text-muted small">3 days ago</span>
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
