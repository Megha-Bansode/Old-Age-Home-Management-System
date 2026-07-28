<?php
/**
 * SevaNest – Family Member Notifications
 * File     : notifications.php
 * Version  : 1.0
 * Author   : SevaNest Dev Team
 *
 * Description:
 *   Premium, modern and responsive Notifications page for SevaNest family members.
 *   Contains Month-style/Message-bubble layout, "All" vs "Unread" interactive filters,
 *   subtle hover micro-animations, and dynamic backend data binding support.
 */

require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../config/database.php';

// Require login
require_login();
$pdo = get_db_connection();
$user_id = $_SESSION['user_id'] ?? 5;

// 1. Fetch notifications from the database
$stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$db_notifs = $stmt->fetchAll();

// 2. Mark all as read in database for next time
$stmt_read = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0");
$stmt_read->execute([$user_id]);

$familynotifications = [];
foreach ($db_notifs as $n) {
    $title = $n['title'] ?? 'Notification';
    $msg = $n['message'] ?? '';
    
    $type = 'visit';
    $icon = 'bi-bell-fill';
    
    if (stripos($title, 'health') !== false || stripos($title, 'pulse') !== false) {
        $type = 'health';
        $icon = 'bi-heart-pulse-fill';
    } elseif (stripos($title, 'visit') !== false) {
        $type = 'visit';
        $icon = 'bi-calendar-check-fill';
    } elseif (stripos($title, 'medication') !== false || stripos($title, 'medicine') !== false) {
        $type = 'medication';
        $icon = 'bi-capsule';
    } elseif (stripos($title, 'doctor') !== false || stripos($title, 'check-up') !== false) {
        $type = 'doctor';
        $icon = 'bi-person-vcard-fill';
    } elseif (stripos($title, 'report') !== false) {
        $type = 'report';
        $icon = 'bi-file-earmark-medical-fill';
    } elseif (stripos($title, 'billing') !== false || stripos($title, 'payment') !== false || stripos($title, 'donation') !== false) {
        $type = 'billing';
        $icon = 'bi-credit-card-2-front-fill';
    }
    
    $ts = strtotime($n['created_at']);
    $time_str = date('j M • h:i A', $ts);
    if (date('Y-m-d', $ts) === date('Y-m-d')) {
        $time_str = 'Today • ' . date('h:i A', $ts);
    } elseif (date('Y-m-d', $ts) === date('Y-m-d', strtotime('-1 day'))) {
        $time_str = 'Yesterday • ' . date('h:i A', $ts);
    }
    
    $familynotifications[] = [
        'id'      => (int)$n['notification_id'],
        'type'    => $type,
        'title'   => $title,
        'message' => $msg,
        'time'    => $time_str,
        'status'  => $n['is_read'] ? 'read' : 'unread',
        'icon'    => $icon
    ];
}
?>
<?php
$base_path = '../../';
$page_title = 'Notifications | SevaNest';
$extra_css = [
    'assets/css/notifications.css'
];

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'family_member';
$currentPage   = 'notifications.php';
$sn_asset_root = "../../assets";
include '../../includes/sidebar.php';
?>

<!-- ═══════════════════════════════════════════════════════════════════════
     MAIN CONTENT AREA
     ═══════════════════════════════════════════════════════════════════════ -->
<main id="sn-main-content" role="main" aria-label="Family member notifications content">

    <div class="nt-page-wrapper">

        <!-- ── 1. Page Header Strip ────────────────────────────────────── -->
        <section class="nt-header-strip nt-animate" aria-labelledby="nt-page-heading">
            <div>
                <h1 class="nt-header-strip__title" id="nt-page-heading">
                    Notifications
                </h1>
            </div>
        </section>
        <!-- ── End Page Header Strip ──────────────────────────────────── -->


        <!-- ── 2. Top Filter Bar ──────────────────────────────────────── -->
        <section class="nt-filter-bar nt-animate nt-animate-delay-1" aria-label="Notification filters">
            <button id="nt-filter-all" class="nt-filter-btn active" type="button" aria-label="Show all notifications">
                All
                <span class="nt-filter-count" id="nt-count-all">0</span>
            </button>
            <button id="nt-filter-unread" class="nt-filter-btn" type="button" aria-label="Show unread notifications only">
                Unread
                <span class="nt-filter-count" id="nt-count-unread">0</span>
            </button>
        </section>
        <!-- ── End Top Filter Bar ─────────────────────────────────────── -->


        <!-- ── 3. Notification Feed (Dynamic List) ─────────────────────── -->
        <section class="nt-feed-container nt-animate nt-animate-delay-2" aria-label="Notification feed list">
            
            <ul class="nt-feed-list" id="nt-feed-container" role="list">
                <?php foreach ($familynotifications as $notif): ?>
         
                    <?php 
                        $statusClass = ($notif['status'] === 'unread') ? 'nt-bubble--unread' : 'nt-bubble--read';
                        $categoryClass = 'nt-category--' . htmlspecialchars($notif['type']);
                    ?>
                    <li class="nt-bubble <?php echo $statusClass; ?> <?php echo $categoryClass; ?>" 
                        data-status="<?php echo htmlspecialchars($notif['status']); ?>"
                        data-id="<?php echo (int)$notif['id']; ?>"
                        role="listitem">
                        
                        <!-- Circular Badge & Icon Category styling -->
                        <div class="nt-icon-bg" aria-hidden="true">
                            <i class="bi <?php echo htmlspecialchars($notif['icon']); ?>"></i>
                        </div>
                        
                        <!-- Content Area -->
                        <div class="nt-content">
                            <div class="nt-title-row">
                                <h2 class="nt-title">
                                    <?php echo htmlspecialchars($notif['title']); ?>
                                </h2>
                                
                                <?php if ($notif['status'] === 'unread'): ?>
                                    <span class="nt-badge">New</span>
                                <?php endif; ?>
                            </div>
                            
                            <p class="nt-message">
                                <?php echo nl2br(htmlspecialchars($notif['message'])); ?>
                            </p>
                            
                            <div class="nt-footer-row">
                                <div class="nt-time">
                                    <i class="bi bi-clock" aria-hidden="true"></i>
                                    <span><?php echo htmlspecialchars($notif['time']); ?></span>
                                </div>
                            </div>
                        </div>
                        
                    </li>
                <?php endforeach; ?>
            </ul>

            <!-- ── 4. Empty State ──────────────────────────────────────── -->
            <div class="nt-empty-state" id="nt-empty-state" style="display: none;" role="status">
                <div class="nt-empty-icon" aria-hidden="true">
                    <i class="bi bi-bell-slash"></i>
                </div>
                <h2 class="nt-empty-title">No notifications available.</h2>
                <p class="nt-empty-desc">We'll notify you whenever there is a new update.</p>
            </div>
            <!-- ── End Empty State ────────────────────────────────────── -->

        </section>
        <!-- ── End Notification Feed ──────────────────────────────────── -->

    </div><!-- /.nt-page-wrapper -->
</main>
<!-- ── End Main Content ───────────────────────────────────────────────── -->

<!-- SevaNest Notifications JS Controls (Filter & Status handler) -->
<script src="../../assets/js/notifications.js" defer></script>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
