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

$pdo = get_db_connection();
$user_id = $_SESSION['user_id'] ?? 5;

$formSuccess = '';
$formError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    
    if ($full_name && $email) {
        $stmt_upd = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, address = ? WHERE id = ?");
        $stmt_upd->execute([$full_name, $email, $phone, $address, $user_id]);
        $formSuccess = 'Profile details updated successfully!';
    } else {
        $formError = 'Full Name and Email Address are required.';
    }
}

// Fetch family user details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$family_user = $stmt->fetch();

$family_name = $family_user['full_name'] ?? 'Sunita Deshmukh';
$family_email = $family_user['email'] ?? 'family@sevanest.com';
$family_phone = $family_user['phone'] ?? '+91 98765 43215';
$family_address = $family_user['address'] ?? '';

// Init initials for avatar
$name_parts = explode(' ', $family_name);
$initials = '';
if (!empty($name_parts)) {
    $initials .= strtoupper(substr($name_parts[0], 0, 1));
    if (count($name_parts) > 1) {
        $initials .= strtoupper(substr($name_parts[count($name_parts) - 1], 0, 1));
    }
} else {
    $initials = 'FM';
}

// Fetch associated resident
$stmt = $pdo->prepare("SELECT * FROM residents WHERE family_member_id = ? AND status = 'Active' LIMIT 1");
$stmt->execute([$user_id]);
$resident = $stmt->fetch();

$resident_name = $resident['full_name'] ?? 'Devendra Joshi';
$resident_room = $resident['room_number'] ?? 'Room 104 (A Wing)';
$resident_id_code = 'RES-' . (1000 + ($resident ? (int)$resident['resident_id'] : 1));

// Fetch recent visitation log (recent 2 visit requests)
$stmt = $pdo->prepare("SELECT * FROM visit_requests WHERE family_member_id = ? ORDER BY visit_date DESC LIMIT 2");
$stmt->execute([$user_id]);
$recent_activities = $stmt->fetchAll();

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'family_member';
$currentPage   = 'profile.php';
$sn_asset_root = "../../assets";
include '../../includes/sidebar.php';
?>

<main id="sn-main-content" role="main" aria-label="Family Member profile content" class="p-4 flex-grow-1">
    <div class="container-fluid">
        
        <?php if ($formSuccess): ?>
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> <?php echo htmlspecialchars($formSuccess); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if ($formError): ?>
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo htmlspecialchars($formError); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

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
                        <?php echo htmlspecialchars($initials); ?>
                    </div>
                    <h5 class="fw-bold mb-1 text-dark"><?php echo htmlspecialchars($family_name); ?></h5>
                    <p class="text-muted small mb-4">Family Representative</p>
                    <hr class="border-light w-100 my-3">
                    <div class="text-start">
                        <div class="mb-3">
                            <span class="text-muted d-block small"><i class="bi bi-envelope me-1"></i>Email Address</span>
                            <span class="text-dark fw-semibold"><?php echo htmlspecialchars($family_email); ?></span>
                        </div>
                        <div class="mb-3">
                            <span class="text-muted d-block small"><i class="bi bi-telephone me-1"></i>Contact Number</span>
                            <span class="text-dark fw-semibold"><?php echo htmlspecialchars($family_phone); ?></span>
                        </div>
                        <div class="mb-1">
                            <span class="text-muted d-block small"><i class="bi bi-heart me-1"></i>Relationship</span>
                            <span class="badge bg-primary-subtle text-primary mt-1">Guardian (<?php echo htmlspecialchars($resident_name); ?>)</span>
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
                            <span class="text-dark fw-semibold"><?php echo htmlspecialchars($resident_name); ?> (<?php echo htmlspecialchars($resident_id_code); ?>)</span>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted small d-block">Room No.</span>
                            <span class="text-dark fw-semibold"><?php echo htmlspecialchars($resident_room); ?></span>
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
                            <?php if (!empty($recent_activities)): ?>
                                <?php foreach ($recent_activities as $act): 
                                    $act_ts = strtotime($act['visit_date']);
                                    $formatted_date = date('jS F, h:i A', $act_ts);
                                    $time_diff = time() - $act_ts;
                                    
                                    $time_label = 'Some time ago';
                                    if ($time_diff < 86400) {
                                        $time_label = 'Today';
                                    } elseif ($time_diff < 172800) {
                                        $time_label = 'Yesterday';
                                    } else {
                                        $time_label = floor($time_diff / 86400) . ' days ago';
                                    }
                                ?>
                                <li class="list-group-item bg-transparent border-light py-3 px-4">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1 fw-semibold text-dark"><i class="bi bi-calendar-event text-primary me-2"></i>Visit Request Status: <?php echo htmlspecialchars($act['status']); ?></h6>
                                            <small class="text-muted">Requested visiting permit for <?php echo htmlspecialchars($formatted_date); ?>.</small>
                                        </div>
                                        <span class="text-muted small"><?php echo htmlspecialchars($time_label); ?></span>
                                    </div>
                                </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="list-group-item bg-transparent border-light py-3 px-4">
                                    <div class="text-center text-muted py-2">No recent visitation logs found.</div>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>

            </div>

        </div>

    </div>
</main>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <form method="POST" action="profile.php">
                <div class="modal-header border-light">
                    <h5 class="modal-title fw-bold text-dark" id="editProfileModalLabel">Edit Profile Info</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    <div class="mb-3">
                        <label for="input_full_name" class="form-label small fw-semibold text-muted">Full Name</label>
                        <input type="text" class="form-control" id="input_full_name" name="full_name" value="<?php echo htmlspecialchars($family_name); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="input_email" class="form-label small fw-semibold text-muted">Email Address</label>
                        <input type="email" class="form-control" id="input_email" name="email" value="<?php echo htmlspecialchars($family_email); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="input_phone" class="form-label small fw-semibold text-muted">Contact Number</label>
                        <input type="text" class="form-control" id="input_phone" name="phone" value="<?php echo htmlspecialchars($family_phone); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="input_address" class="form-label small fw-semibold text-muted">Address</label>
                        <textarea class="form-control" id="input_address" name="address" rows="3"><?php echo htmlspecialchars($family_address); ?></textarea>
                    </div>
                </div>
                <div class="modal-footer border-light">
                    <button type="button" class="btn btn-sm btn-secondary fw-semibold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary fw-semibold">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editBtn = document.getElementById('editProfileBtn');
    if (editBtn) {
        editBtn.addEventListener('click', function() {
            const modal = new bootstrap.Modal(document.getElementById('editProfileModal'));
            modal.show();
        });
    }
});
</script>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
