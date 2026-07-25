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

/* ── Role & Page Configuration ──────────────────────────────────────────── */
$userRole    = 'family_member';
$currentPage = 'notifications.php';

/* ── Dynamic PHP Data (MySQL Database Ready) ────────────────────────────── */
// This array represents database rows. To connect with a MySQL database,
// replace this static array with database fetch results:
// E.g., $stmt = $pdo->prepare("SELECT * FROM notifications ORDER BY id DESC");
$notifications = [
    [
        'id'      => 1,
        'type'    => 'health', // health | visit | medication | doctor | report | billing | emergency
        'title'   => '❤️ Health Update',
        'message' => 'Blood pressure is stable today (120/80 mmHg). Resident is feeling healthy.',
        'time'    => 'Today • 9:45 AM',
        'status'  => 'unread', // unread | read
        'icon'    => 'bi-heart-pulse-fill'
    ],
    [
        'id'      => 2,
        'type'    => 'visit',
        'title'   => '📅 Visit Approved',
        'message' => 'Your visit request for 28 July 2026 has been approved.',
        'time'    => 'Yesterday • 4:30 PM',
        'status'  => 'unread',
        'icon'    => 'bi-calendar-check-fill'
    ],
    [
        'id'      => 3,
        'type'    => 'medication',
        'title'   => '💊 Medication Update',
        'message' => 'Morning medication has been successfully administered.',
        'time'    => 'Yesterday • 8:00 AM',
        'status'  => 'read',
        'icon'    => 'bi-capsule'
    ],
    [
        'id'      => 4,
        'type'    => 'doctor',
        'title'   => '🩺 Doctor Update',
        'message' => 'Routine health check-up completed. No health concerns observed.',
        'time'    => '22 Jul • 2:15 PM',
        'status'  => 'read',
        'icon'    => 'bi-person-vcard-fill'
    ],
    [
        'id'      => 5,
        'type'    => 'report',
        'title'   => '📄 Medical Report',
        'message' => 'Blood test report has been uploaded.',
        'time'    => '21 Jul • 11:30 AM',
        'status'  => 'read',
        'icon'    => 'bi-file-earmark-medical-fill'
    ],
    [
        'id'      => 6,
        'type'    => 'billing',
        'title'   => '💳 Billing Reminder',
        'message' => 'Monthly payment is due in 3 days.',
        'time'    => '20 Jul • 10:00 AM',
        'status'  => 'unread',
        'icon'    => 'bi-credit-card-2-front-fill'
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Notifications | SevaNest</title>
    <meta name="description" content="View all medical alerts, schedule confirmations, billing reminders and updates on SevaNest.">

    <!-- Bootstrap Icons (Required for visual markers) -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Google Fonts – Inter (Application Standard) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap"
          rel="stylesheet">

    <!-- Sidebar Layout CSS -->
    <link rel="stylesheet" href="assets/css/sidebar.css">

    <!-- Base dashboard variables & grid tokens -->
    <link rel="stylesheet" href="assets/css/dashboard.css">

    <!-- Notifications customized stylesheet -->
    <link rel="stylesheet" href="assets/css/notifications.css">

</head>
<body>

<?php
/* ── Sidebar Component ───────────────────────────────────────────────────── */
$sn_asset_root = "assets";
include 'includes/sidebar.php';
?>

<!-- ═══════════════════════════════════════════════════════════════════════
     TOP HEADER BAR (DO NOT MODIFY AS REQUESTED)
     ═══════════════════════════════════════════════════════════════════════ -->
<header class="sn-topbar" id="sn-topbar" role="banner" aria-label="Dashboard top bar">

    <!-- Hamburger toggle -->
    <button id="sn-toggle-btn"
            type="button"
            aria-label="Toggle sidebar navigation"
            aria-expanded="true"
            aria-controls="sn-sidebar"
            title="Toggle Sidebar">
        <i class="bi bi-list" aria-hidden="true"></i>
    </button>

    <!-- Profile button -->
    <div class="sn-profile">
        <a href="resident-profile.php" title="Profile">
            <i class="bi bi-person-circle"></i>
        </a>
    </div>

</header>
<!-- ── End Top Header Bar ─────────────────────────────────────────────── -->


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
                <?php foreach ($notifications as $notif): ?>
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


<!-- Bootstrap 5 JS bundle (required for dropdowns, tooltips, etc.) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- SevaNest Notifications JS Controls (Filter & Status handler) -->
<script src="assets/js/notifications.js" defer></script>

</body>
</html>
