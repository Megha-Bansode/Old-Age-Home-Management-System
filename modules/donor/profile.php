<?php
/**
 * SevaNest – Donor Profile
 * File     : modules/donor/profile.php
 * Version  : 1.0
 */

require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../config/database.php';

// Require Donor login
require_login();
require_role('Donor');

$pdo = get_db_connection();
$donor_id = $_SESSION['user_id'] ?? 6;

$formSuccess = '';
$formError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'update_profile') {
        $full_name = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        
        if (empty($full_name) || empty($email)) {
            $formError = 'Name and Email are required.';
        } else {
            try {
                $chk = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ? AND id != ?");
                $chk->execute([$email, $donor_id]);
                if ($chk->fetchColumn() > 0) {
                    $formError = 'This email address is already registered by another user.';
                } else {
                    $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, address = ? WHERE id = ?");
                    $stmt->execute([$full_name, $email, $phone, $address, $donor_id]);
                    
                    $_SESSION['user_full_name'] = $full_name;
                    $formSuccess = 'Your profile has been updated successfully.';
                }
            } catch (Exception $e) {
                $formError = 'Database error: ' . $e->getMessage();
            }
        }
    }
}

// Fetch latest user details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$donor_id]);
$user = $stmt->fetch();

$base_path = '../../';
$page_title = 'Donor Profile | SevaNest';
$extra_css = [
    'assets/css/donor.css'
];

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'donor';
$currentPage   = 'profile.php';
$sn_asset_root = "../../assets";
$base_path = '../../';
include '../../includes/sidebar.php';
?>

<!-- ═══════════════════════════════════════════════════════════════════════
     MAIN CONTENT AREA
     ═══════════════════════════════════════════════════════════════════════ -->
<main id="sn-main-content" role="main" aria-label="Donor profile content">
    <div class="donor-page-wrapper">
        
        <!-- Header Strip -->
        <div class="dn-header-strip animate-fade-in">
            <div>
                <h2 class="dn-header-strip__title">My Donor Profile</h2>
                <p style="color: var(--color-text-muted-team); margin: 4px 0 0;">Manage your personal profile, category preferences and privacy settings.</p>
            </div>
            <div>
                <button class="btn btn-primary" id="editProfileBtn"><i class="bi bi-pencil-square me-2"></i>Edit Profile</button>
            </div>
        </div>

        <!-- Two Column Layout -->
        <div class="grid two-col animate-fade-in">
            
            <!-- Left Column: Donor Info and Contacts -->
            <div class="card d-flex flex-column gap-4 align-items-center text-center">
                <?php
                $initials = '';
                if (!empty($user['full_name'])) {
                    $parts = explode(' ', $user['full_name']);
                    foreach ($parts as $p) {
                        $initials .= substr($p, 0, 1);
                    }
                    $initials = strtoupper(substr($initials, 0, 2));
                } else {
                    $initials = 'DN';
                }
                ?>
                <div class="res-photo" style="width: 100px; height: 100px; font-size: 2.5rem; background-color: var(--color-primary-soft-team); color: var(--color-primary);"><?php echo sn_e($initials); ?></div>
                <div>
                    <h3 style="font-size: 1.5rem; font-weight: 700; color: var(--color-text); margin: 0;"><?php echo sn_e($user['full_name']); ?></h3>
                    <p style="color: var(--color-text-muted-team); margin: 4px 0 0;">Generous Benefactor since <?php echo date('Y', strtotime($user['created_at'])); ?></p>
                </div>
                <hr style="margin: 0; width: 100%; border-top: 1px solid var(--color-border);">
                <div class="w-100 text-start d-flex flex-column gap-3" style="font-size: var(--font-size-sm);">
                    <div>
                        <strong style="color: var(--color-text);"><i class="bi bi-envelope me-2"></i>Email Address:</strong>
                        <span style="color: var(--color-text-muted-team); display: block; margin-top: 2px; padding-left: 24px;"><?php echo sn_e($user['email']); ?></span>
                    </div>
                    <div>
                        <strong style="color: var(--color-text);"><i class="bi bi-telephone me-2"></i>Contact Phone:</strong>
                        <span style="color: var(--color-text-muted-team); display: block; margin-top: 2px; padding-left: 24px;"><?php echo sn_e($user['phone'] ?? 'N/A'); ?></span>
                    </div>
                    <div>
                        <strong style="color: var(--color-text);"><i class="bi bi-geo-alt me-2"></i>Billing Address:</strong>
                        <span style="color: var(--color-text-muted-team); display: block; margin-top: 2px; padding-left: 24px;"><?php echo sn_e($user['address'] ?? 'N/A'); ?></span>
                    </div>
                </div>
            </div>

            <!-- Right Column: Preferences, Anonymous toggle, Communication preferences -->
            <div class="d-flex flex-column gap-4">
                
                <!-- Donation Preferences -->
                <div class="card">
                    <div class="card-head">
                        <h3>Donation Preferences</h3>
                    </div>
                    <div class="d-flex flex-column gap-3" style="font-size: var(--font-size-sm);">
                        <div>
                            <strong style="color: var(--color-text); display: block; margin-bottom: 4px;">Preferred Category:</strong>
                            <span class="badge blue">Medical Supplies &amp; Support</span>
                            <span class="badge gold">Nutritional Diet Plans</span>
                        </div>
                        <hr style="margin: 8px 0; border-top: 1px dashed var(--color-border);">
                        
                        <!-- Anonymous Donation Option -->
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong style="color: var(--color-text); display: block; margin-bottom: 2px;">Anonymous Donations Option:</strong>
                                <span style="color: var(--color-text-muted-team); font-size: var(--font-size-xs);">Keep your identity hidden from public dashboard leaderboards.</span>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="anonCheck" checked style="cursor: pointer;">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Communication Preferences -->
                <div class="card">
                    <div class="card-head">
                        <h3>Communication Preferences</h3>
                    </div>
                    <div class="d-flex flex-column gap-3" style="font-size: var(--font-size-sm);">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Email Receipts &amp; Tax Documents</span>
                            <span style="color: var(--color-success); font-weight: 600;">Enabled</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Monthly Impact Newsletters</span>
                            <span style="color: var(--color-success); font-weight: 600;">Enabled</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span>New Campaign Alerts (SMS)</span>
                            <span style="color: var(--color-text-muted-team); font-weight: 600;">Disabled</span>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>
</main>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <form method="POST" action="profile.php">
                <input type="hidden" name="action" value="update_profile">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="editProfileModalLabel">Edit Donor Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="full_name" class="form-control select" required style="width:100%; border: 1px solid var(--color-border); border-radius: var(--radius-medium); padding: 8px;" value="<?php echo sn_e($user['full_name']); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control select" required style="width:100%; border: 1px solid var(--color-border); border-radius: var(--radius-medium); padding: 8px;" value="<?php echo sn_e($user['email']); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Contact Phone</label>
                        <input type="text" name="phone" class="form-control select" style="width:100%; border: 1px solid var(--color-border); border-radius: var(--radius-medium); padding: 8px;" value="<?php echo sn_e($user['phone'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Billing Address</label>
                        <textarea name="address" class="form-control select" rows="3" style="width:100%; border: 1px solid var(--color-border); border-radius: var(--radius-medium); padding: 8px;"><?php echo sn_e($user['address'] ?? ''); ?></textarea>
                    </div>
                </div>
                <div class="modal-footer border-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-sm">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if ($formSuccess): ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: <?php echo json_encode($formSuccess); ?>,
                    confirmButtonColor: '#2b4c3f'
                });
            } else {
                alert(<?php echo json_encode($formSuccess); ?>);
            }
        });
    </script>
<?php endif; ?>
<?php if ($formError): ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: <?php echo json_encode($formError); ?>,
                    confirmButtonColor: '#2b4c3f'
                });
            } else {
                alert(<?php echo json_encode($formError); ?>);
            }
        });
    </script>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const editProfileBtn = document.getElementById('editProfileBtn');
    if (editProfileBtn) {
        editProfileBtn.addEventListener('click', () => {
            const myModal = new bootstrap.Modal(document.getElementById('editProfileModal'));
            myModal.show();
        });
    }
});
</script>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
