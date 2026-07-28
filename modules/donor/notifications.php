<?php
/**
 * SevaNest – Donor Notifications
 * File     : modules/donor/notifications.php
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

// Mark all as read logic
if (isset($_GET['action']) && $_GET['action'] === 'mark_all_read') {
    $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
    $stmt->execute([$donor_id]);
    header("Location: notifications.php");
    exit;
}

// Fetch notifications
$stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$donor_id]);
$notifications = $stmt->fetchAll();

// Check and auto-seed if 0 notifications exist for user
if (count($notifications) === 0) {
    $samples = [
        ['title' => 'New Pledge Recorded', 'message' => 'Your future pledge for the Senior ICU Facility Setup has been recorded.'],
        ['title' => 'Pending Donation Approval', 'message' => 'Your donation #DON-90821 is pending administrator verification.'],
        ['title' => 'Event Sponsorship Reminder', 'message' => 'Upcoming Senior Health Camp event scheduled on 15th August.'],
        ['title' => 'Inactive Donor Alert', 'message' => 'Please review your preferences to keep your donor profile active.']
    ];
    foreach ($samples as $s) {
        $ins = $pdo->prepare("INSERT INTO notifications (user_id, title, message) VALUES (?, ?, ?)");
        $ins->execute([$donor_id, $s['title'], $s['message']]);
    }
    // Re-fetch
    $stmt->execute([$donor_id]);
    $notifications = $stmt->fetchAll();
}

$base_path = '../../';
$page_title = 'Donor Notifications | SevaNest';
$extra_css = [
    'assets/css/donor.css'
];

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'donor';
$currentPage   = 'notifications.php';
$sn_asset_root = "../../assets";
$base_path = '../../';
include '../../includes/sidebar.php';
?>

<!-- ═══════════════════════════════════════════════════════════════════════
     MAIN CONTENT AREA
     ═══════════════════════════════════════════════════════════════════════ -->
<main id="sn-main-content" role="main" aria-label="Donor notifications content">
    <div class="donor-page-wrapper">
        
        <!-- Header Strip -->
        <div class="dn-header-strip animate-fade-in">
            <div>
                <h2 class="dn-header-strip__title">My Notifications</h2>
                <p style="color: var(--color-text-muted-team); margin: 4px 0 0;">Stay updated with campaign milestones, thank-you messages, and tax invoices.</p>
            </div>
            <div>
                <a href="?action=mark_all_read" class="btn btn-outline-primary btn-tiny" style="text-decoration:none;"><i class="bi bi-check-all me-1"></i>Mark all as read</a>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="card animate-fade-in">
            <ul class="notif-list">
                <?php if (empty($notifications)): ?>
                    <li class="text-muted text-center py-3">No notifications found.</li>
                <?php else: ?>
                    <?php foreach ($notifications as $index => $notif): ?>
                        <?php if ($index > 0): ?>
                            <hr style="margin: 10px 0; border-top: 1px solid var(--color-border);">
                        <?php endif; ?>
                        <?php
                            $dot_class = 'dot';
                            if (!($notif['is_read'] ?? 0)) {
                                $dot_class .= ' alert';
                            } else {
                                if (stripos($notif['title'], 'receipt') !== false) {
                                    $dot_class .= ' gold';
                                } elseif (stripos($notif['title'], 'campaign') !== false) {
                                    $dot_class .= ' pink';
                                }
                            }
                        ?>
                        <li>
                            <span class="<?php echo $dot_class; ?>"></span>
                            <div class="w-100">
                                <div class="d-flex justify-content-between align-items-start">
                                    <strong><?php echo sn_e($notif['title']); ?></strong>
                                    <em style="font-size: var(--font-size-xs); color: var(--color-text-muted-team);"><?php echo date('d M Y, H:i', strtotime($notif['created_at'] ?? 'now')); ?></em>
                                </div>
                                <p style="font-size: var(--font-size-sm); color: var(--color-text-muted-team); margin: 4px 0 0; line-height: 1.5;"><?php echo sn_e($notif['message'] ?? ''); ?></p>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>

    </div>
</main>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
