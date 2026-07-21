<?php
/**
 * SevaNest — Shared Header Component
 *
 * Expected Session Variables:
 *   - $_SESSION['user_name'] : Full name of the logged-in user (string).
 *   - $_SESSION['user_role'] : Role label, e.g. "Doctor", "Caretaker" (string).
 *
 * Outputs:
 *   - Full HTML document open: DOCTYPE, <html>, <head> (Bootstrap 5, BI icons, layout.css, Google Fonts).
 *   - Opening <body> tag.
 *   - Responsive navbar: SevaNest logo + brand name, live clock, notification bell, user profile.
 *   - Guest fallback: renders a "Login" button when no session exists.
 *
 * Usage:
 *   <?php
 *     $page_title = "Doctor Dashboard — SevaNest";  // optional
 *     include 'includes/header.php';
 *   ?>
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* ── Session state ─────────────────────────────────────── */
$is_logged_in = isset($_SESSION['user_name']) && !empty($_SESSION['user_name']);
$user_name    = $is_logged_in ? $_SESSION['user_name'] : '';
$user_role    = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : '';

/* ── Role → Dashboard URL map ──────────────────────────── */
$dashboard_mapping = [
    'Super Admin'       => 'super_admin_dashboard.php',
    'Old Age Home Admin'=> 'admin_dashboard.php',
    'Caretaker'         => 'caretaker_dashboard.php',
    'Doctor'            => 'doctor_dashboard.php',
    'Donor'             => 'donor_dashboard.php',
    'Family Member'     => 'family_dashboard.php',
];
$dashboard_url = ($is_logged_in && array_key_exists($user_role, $dashboard_mapping))
    ? $dashboard_mapping[$user_role]
    : 'index.php';

/* ── Initials avatar (up to 2 letters) ─────────────────── */
$initials = 'U';
if ($is_logged_in) {
    $parts    = preg_split('/\s+/', trim($user_name));
    $initials = '';
    foreach ($parts as $p) {
        if (!empty($p)) $initials .= strtoupper($p[0]);
    }
    $initials = substr($initials, 0, 2);
}

/* ── Mock notifications (replace with DB query in production) ── */
$notifications = [
    ['title' => 'New Resident Admission Request',     'time' => '15 mins ago', 'unread' => true ],
    ['title' => 'Dr. Sharma Scheduled Visit Today',   'time' => '2 hrs ago',   'unread' => true ],
    ['title' => 'Weekly activity log has been updated','time' => '1 day ago',   'unread' => false],
];
$unread_count = array_sum(array_column($notifications, 'unread'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) : 'SevaNest — Old Age Home Management'; ?></title>
    <meta name="description" content="SevaNest — Care, Respect, Together. Comprehensive management for old age homes.">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- SevaNest Design System -->
    <link href="assets/css/layout.css" rel="stylesheet">
</head>
<body>

<!-- ╔══════════════════════════════════════════════════════════╗
     ║  SEVANEST NAVIGATION HEADER                             ║
     ╚══════════════════════════════════════════════════════════╝ -->
<nav class="navbar navbar-expand-lg navbar-dark oahms-header" aria-label="Main navigation">
    <div class="container-fluid">

        <!-- ① BRAND — always visible ──────────────────────── -->
        <a class="oahms-navbar-brand" href="<?php echo htmlspecialchars($dashboard_url); ?>">
            <img src="assets/images/sevanest-logo.png"
                 alt="SevaNest Logo"
                 class="oahms-brand-logo">
            <div class="oahms-brand-text">
                <span class="oahms-brand-name">Seva<span>Nest</span></span>
                <span class="oahms-brand-tagline">Care &bull; Respect &bull; Together</span>
            </div>
        </a>

        <!-- ② RIGHT CONTROLS — always visible on all sizes ── -->
        <div class="oahms-nav-controls ms-auto order-lg-last">

            <?php if ($is_logged_in): ?>

                <!-- Notification Bell -->
                <div class="dropdown">
                    <button class="oahms-notification-btn"
                            type="button"
                            id="oahmsNotifBtn"
                            data-bs-toggle="dropdown"
                            data-bs-auto-close="outside"
                            aria-expanded="false"
                            aria-label="Notifications (<?php echo $unread_count; ?> unread)">
                        <i class="bi bi-bell-fill"></i>
                        <?php if ($unread_count > 0): ?>
                            <span class="oahms-badge"><?php echo $unread_count; ?></span>
                        <?php endif; ?>
                    </button>

                    <div class="dropdown-menu dropdown-menu-end oahms-dropdown-menu"
                         aria-labelledby="oahmsNotifBtn"
                         style="min-width: 300px;">
                        <!-- Dropdown header strip -->
                        <div class="oahms-dropdown-header">
                            <span><i class="bi bi-bell-fill me-2"></i>Notifications</span>
                            <?php if ($unread_count > 0): ?>
                                <span class="oahms-notif-badge"><?php echo $unread_count; ?> New</span>
                            <?php endif; ?>
                        </div>

                        <?php foreach ($notifications as $notif): ?>
                            <a class="dropdown-item oahms-dropdown-item oahms-notif-item" href="#">
                                <span class="oahms-notif-dot"
                                      style="background:<?php echo $notif['unread'] ? 'var(--oahms-accent)' : 'var(--oahms-success)'; ?>;">
                                </span>
                                <div style="flex:1; min-width:0;">
                                    <div class="fw-semibold text-truncate" style="font-size:0.82rem; color:var(--oahms-text);">
                                        <?php echo htmlspecialchars($notif['title']); ?>
                                    </div>
                                    <small class="text-muted" style="font-size:0.72rem;">
                                        <i class="bi bi-clock me-1"></i><?php echo htmlspecialchars($notif['time']); ?>
                                    </small>
                                </div>
                            </a>
                        <?php endforeach; ?>

                        <div class="oahms-dropdown-divider"></div>
                        <a class="dropdown-item oahms-dropdown-item justify-content-center text-center"
                           href="#"
                           style="color:var(--oahms-primary)!important; font-size:0.8rem;">
                            <i class="bi bi-arrow-right-circle me-1"></i> View all notifications
                        </a>
                    </div>
                </div>

                <div class="oahms-nav-divider"></div>

                <!-- Profile Avatar + Dropdown -->
                <div class="dropdown">
                    <a href="#"
                       class="d-flex align-items-center gap-2 text-decoration-none dropdown-toggle"
                       id="oahmsUserBtn"
                       data-bs-toggle="dropdown"
                       aria-expanded="false"
                       style="color:inherit;">
                        <div class="oahms-avatar"><?php echo htmlspecialchars($initials); ?></div>
                        <!-- Hidden on mobile via CSS -->
                        <div class="oahms-user-meta">
                            <span class="oahms-user-name"><?php echo htmlspecialchars($user_name); ?></span>
                            <span class="oahms-user-role"><?php echo htmlspecialchars($user_role); ?></span>
                        </div>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end oahms-dropdown-menu"
                        aria-labelledby="oahmsUserBtn"
                        style="min-width: 220px;">

                        <!-- Mini profile panel -->
                        <li>
                            <div class="oahms-profile-panel">
                                <div class="oahms-avatar">
                                    <?php echo htmlspecialchars($initials); ?>
                                </div>
                                <div>
                                    <div class="oahms-profile-name"><?php echo htmlspecialchars($user_name); ?></div>
                                    <div class="oahms-profile-role"><?php echo htmlspecialchars($user_role); ?></div>
                                </div>
                            </div>
                        </li>

                        <li><hr class="dropdown-divider oahms-dropdown-divider"></li>

                        <li>
                            <a class="dropdown-item oahms-dropdown-item" href="profile.php">
                                <i class="bi bi-person-circle"></i> My Profile
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item oahms-dropdown-item" href="#">
                                <i class="bi bi-gear"></i> Settings
                            </a>
                        </li>

                        <!--
                        ╔══════════════════════════════════════════════════╗
                        ║  ROLE-SPECIFIC MENU ITEMS — add here             ║
                        ║  Example:                                        ║
                        ║  <?php if ($user_role === 'Doctor'): ?>          ║
                        ║    <li><a class="dropdown-item oahms-dropdown-item"
                        ║           href="consultations.php">              ║
                        ║      <i class="bi bi-clipboard2-pulse"></i>      ║
                        ║      Consultations</a></li>                      ║
                        ║  <?php endif; ?>                                 ║
                        ╚══════════════════════════════════════════════════╝
                        -->

                        <li><hr class="dropdown-divider oahms-dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item oahms-dropdown-item text-danger fw-semibold" href="logout.php">
                                <i class="bi bi-box-arrow-right"></i> Sign Out
                            </a>
                        </li>
                    </ul>
                </div>

            <?php else: ?>
                <!-- Guest: Login CTA -->
                <a href="login.php" class="btn oahms-login-btn">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Login
                </a>
            <?php endif; ?>
        </div>

        <!-- Right controls (Notifications, Profile, Login) remain here, clock and hamburger removed -->

    </div>
</nav>

<!-- Page content wrapper — closed by footer.php -->
<div class="oahms-main-content">
