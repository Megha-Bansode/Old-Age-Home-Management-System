<?php
/**
 * SevaNest — Shared Header Component
 *
 * Expected Session Variables:
 *   - $_SESSION['user_name'] : Full name of the logged-in user (string).
 *   - $_SESSION['user_role'] : Role label, e.g. "Doctor", "Caretaker" (string).
 *
 * Optional Config:
 *   - $base_path : Relative path multiplier to root (e.g. "../../")
 */

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'path' => '/',
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

/* ── Path prefix helper for subfolder modules ──────────── */
$path_prefix = isset($base_path) ? $base_path : '';

/* ── Session state ─────────────────────────────────────── */
$is_logged_in = isset($_SESSION['user_name']) && !empty($_SESSION['user_name']);
$user_name    = $is_logged_in ? $_SESSION['user_name'] : '';
$user_role    = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : '';

/* ── Role → Dashboard URL map ──────────────────────────── */
$dashboard_mapping = [
    'Super Admin'        => 'modules/super_admin/index.php',
    'Old Age Home Admin' => 'modules/admin/index.php',
    'Caretaker'          => 'modules/caretaker/index.php',
    'Doctor'             => 'modules/doctor/index.php',
    'Donor'              => 'modules/donor/index.php',
    'Family Member'      => 'modules/family/index.php',
];
$dashboard_url = ($is_logged_in && array_key_exists($user_role, $dashboard_mapping))
    ? $path_prefix . $dashboard_mapping[$user_role]
    : $path_prefix . 'index.php';

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

/* ── Logo check ────────────────────────────────────────── */
$logo_file = $path_prefix . 'assets/images/logo/logo.jpeg';
$show_logo = file_exists($logo_file);

/* ── Mock notifications ────────────────────────────────── */
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
    <link href="<?php echo htmlspecialchars($path_prefix); ?>assets/css/variables.css" rel="stylesheet">
    <link href="<?php echo htmlspecialchars($path_prefix); ?>assets/css/base.css" rel="stylesheet">
    <link href="<?php echo htmlspecialchars($path_prefix); ?>assets/css/layout.css" rel="stylesheet">
    <link href="<?php echo htmlspecialchars($path_prefix); ?>assets/css/components.css" rel="stylesheet">
    <link href="<?php echo htmlspecialchars($path_prefix); ?>assets/css/animations.css" rel="stylesheet">
    <link href="<?php echo htmlspecialchars($path_prefix); ?>assets/css/responsive.css" rel="stylesheet">
    
    <?php if (isset($extra_css) && is_array($extra_css)): ?>
        <?php foreach ($extra_css as $css_file): ?>
            <link href="<?php echo htmlspecialchars($path_prefix . $css_file); ?>" rel="stylesheet">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body class="<?php echo isset($body_class) ? htmlspecialchars($body_class) : ''; ?>">

<!-- ╔══════════════════════════════════════════════════════════╗
     ║  SEVANEST NAVIGATION HEADER                             ║
     ╚══════════════════════════════════════════════════════════╝ -->
<nav class="navbar navbar-expand-lg navbar-dark oahms-header" aria-label="Main navigation">
    <div class="container-fluid">

        <!-- ① BRAND — always visible ──────────────────────── -->
        <a class="oahms-navbar-brand" href="<?php echo htmlspecialchars($dashboard_url); ?>">
            <?php if ($show_logo): ?>
                <img src="<?php echo htmlspecialchars($logo_file); ?>"
                     alt="SevaNest Logo"
                     class="oahms-brand-logo">
            <?php else: ?>
                <span class="oahms-brand-logo-fallback" style="color: #FFFFFF; font-weight: 800; font-size: 1.45rem;">SevaNest</span>
            <?php endif; ?>
            <div class="oahms-brand-text">
                <span class="oahms-brand-name">Seva<span>Nest</span></span>
                <span class="oahms-brand-tagline">Care &bull; Respect &bull; Together</span>
            </div>
        </a>

        <!-- Collapsible Menu for Landing Page -->
        <?php if (isset($is_landing_page) && $is_landing_page): ?>
            <button class="navbar-toggler ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#landingNavbar" aria-controls="landingNavbar" aria-expanded="false" aria-label="Toggle navigation" style="border: none;">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-center" id="landingNavbar">
                <div class="navbar-nav">
                    <a class="nav-link px-3" href="#home" style="font-weight: 600;">Home</a>
                    <a class="nav-link px-3" href="#about" style="font-weight: 600;">About</a>
                    <a class="nav-link px-3" href="#services" style="font-weight: 600;">Services</a>
                    <a class="nav-link px-3" href="#nearby" style="font-weight: 600;">Nearby Homes</a>
                    <a class="nav-link px-3" href="#donate" style="font-weight: 600;">Donation</a>
                    <a class="nav-link px-3" href="#volunteer" style="font-weight: 600;">Volunteer</a>
                    <a class="nav-link px-3" href="#contact" style="font-weight: 600;">Contact</a>
                </div>
            </div>
        <?php endif; ?>

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
                            <a class="dropdown-item oahms-dropdown-item" href="<?php echo htmlspecialchars($path_prefix); ?>profile.php">
                                <i class="bi bi-person-circle"></i> My Profile
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item oahms-dropdown-item" href="#">
                                <i class="bi bi-gear"></i> Settings
                            </a>
                        </li>

                        <li><hr class="dropdown-divider oahms-dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item oahms-dropdown-item text-danger fw-semibold" href="<?php echo htmlspecialchars($path_prefix); ?>modules/authentication/logout.php">
                                <i class="bi bi-box-arrow-right"></i> Sign Out
                            </a>
                        </li>
                    </ul>
                </div>

            <?php else: ?>
                <!-- Guest: Login CTA -->
                <a href="<?php echo htmlspecialchars($path_prefix); ?>modules/authentication/login.php" class="btn oahms-login-btn">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Login
                </a>
            <?php endif; ?>
        </div>

    </div>
</nav>

<!-- Page content wrapper — closed by footer.php -->
<div class="oahms-main-content">
