<?php
/**
 * SevaNest – Sidebar Component
 * File     : includes/sidebar.php
 * Version  : 1.0
 * Author   : SevaNest Dev Team
 *
 * Description:
 *   Reusable PHP sidebar component for the Old-Age Home Management System.
 *   Renders role-aware navigation items based on the $userRole variable
 *   (or session). Designed to be included via PHP include() / require().
 *
 * Usage:
 *   <?php
 *       $userRole    = 'admin';          // Set before including this file
 *       $currentPage = 'dashboard.php';  // Optional – auto-detected if omitted
 *       require_once __DIR__ . '/../includes/sidebar.php';
 *   ?>
 *
 * Supported Roles:
 *   super_admin | admin | caretaker | doctor | donor | family_member
 *
 * Required assets (already linked from this file):
 *   assets/css/sidebar.css
 *   assets/js/sidebar.js
 *   Bootstrap 5 CSS + Icons (via CDN)
 *   Inter font (via Google Fonts, in sidebar.css)
 *
 * ==========================================================================
 */

/* ─────────────────────────────────────────────────────────────────────────
 * Role & Page Detection
 * ───────────────────────────────────────────────────────────────────────── */

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'path' => '/',
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

// /**
//  * Resolve the current user role.
//  * Priori// Start session if not already started
// if ty: local $userRole variable → session → default 'admin'.
//  */
// $sn_role = $userRole ?? $_SESSION['role'] ?? 'admin';
// $sn_role = strtolower(trim($sn_role));
/**
 * Resolve the current user role.
 * Priority: local $userRole variable → session → default 'admin'.
 */
$sn_role = $userRole ?? $_SESSION['role'] ?? 'admin';
$sn_role = strtolower(trim($sn_role));
/**
 * Resolve the current page filename for active-state highlighting.
 * Priority: local $currentPage → basename of the actual script.
 */
$sn_current_page = $currentPage ?? basename($_SERVER['PHP_SELF'] ?? '');

/**
 * Helper: generate nav item HTML.
 *
 * @param string $href        – relative URL (e.g. 'dashboard.php')
 * @param string $icon        – Bootstrap Icons class (e.g. 'bi-house-door')
 * @param string $label       – visible menu label
 * @param int    $badge       – optional notification count; 0 = no badge
 * @param string $extraClass  – optional extra CSS classes on the <a> tag
 * @return string             – complete <li> HTML string
 */
function sn_nav_item(
    string $href,
    string $icon,
    string $label,
    int    $badge      = 0,
    string $extraClass = ''
): string {
    global $sn_current_page;

    $file        = basename($href);
    $is_active   = ($file === $sn_current_page);
    $active_cls  = $is_active ? ' active' : '';
    $aria_cur    = $is_active ? ' aria-current="page"' : '';
    $badge_html  = '';

    if ($badge > 0) {
        $count      = $badge > 99 ? '99+' : $badge;
        $badge_html = '<span class="sn-badge" aria-label="' . (int)$badge . ' notifications">'
                    . htmlspecialchars((string)$count) . '</span>';
    }

    $tooltip = htmlspecialchars($label);
    $label_e = htmlspecialchars($label);
    $href_e  = htmlspecialchars($href);
    $icon_e  = htmlspecialchars($icon);
    $extra   = $extraClass ? ' ' . htmlspecialchars($extraClass) : '';

    return <<<HTML
            <li class="sn-nav-item">
                <a href="{$href_e}"
                   class="sn-nav-link{$active_cls}{$extra}"{$aria_cur}
                   tabindex="0">
                    <i class="bi {$icon_e} sn-icon" aria-hidden="true"></i>
                    <span class="sn-label">{$label_e}</span>
                    {$badge_html}
                </a>
                <span class="sn-tooltip" aria-hidden="true">{$tooltip}</span>
            </li>
HTML;
}

/**
 * Helper: render a section heading (only visible in expanded state).
 *
 * @param string $text – heading text
 * @return string
 */
function sn_nav_heading(string $text): string {
    return '<li class="sn-nav-heading" role="presentation">'
         . htmlspecialchars($text)
         . '</li>';
}


/* ─────────────────────────────────────────────────────────────────────────
 * Role-Based Navigation Map
 *
 * Each role key maps to an array of navigation sections.
 * A section is an array with:
 *   'heading' => string|null   – optional section label
 *   'items'   => array[]       – list of nav items
 *
 * Item keys:
 *   href, icon, label, badge (optional)
 * ───────────────────────────────────────────────────────────────────────── */
$sn_nav_map = [

    /* ── Super Admin – full access ───────────────────────────────────── */
    'super_admin' => [
        [
            'heading' => 'Main',
            'items'   => [
                ['href' => 'dashboard.php',      'icon' => 'bi-grid-1x2',        'label' => 'Dashboard'],
                ['href' => 'residents.php',       'icon' => 'bi-people',           'label' => 'Residents'],
                ['href' => 'medical-records.php', 'icon' => 'bi-file-earmark-medical', 'label' => 'Medical Records'],
                ['href' => 'doctors.php',         'icon' => 'bi-hospital',     'label' => 'Doctors'],
                ['href' => 'caretakers.php',      'icon' => 'bi-person-heart',     'label' => 'Caretakers'],
                ['href' => 'visitors.php',        'icon' => 'bi-person-lines-fill','label' => 'Visitors'],
            ],
        ],
        [
            'heading' => 'Finance',
            'items'   => [
                ['href' => 'donations.php',  'icon' => 'bi-gift',       'label' => 'Donations'],
                ['href' => 'billing.php',    'icon' => 'bi-receipt',    'label' => 'Billing'],
                ['href' => 'expenses.php',   'icon' => 'bi-wallet2',    'label' => 'Expenses'],
            ],
        ],
        [
            'heading' => 'System',
            'items'   => [
                ['href' => 'reports.php',       'icon' => 'bi-bar-chart-line',    'label' => 'Reports'],
                ['href' => 'notifications.php', 'icon' => 'bi-bell',              'label' => 'Notifications', 'badge' => 3],
                ['href' => 'users.php',         'icon' => 'bi-person-gear',       'label' => 'User Management'],
                ['href' => 'settings.php',      'icon' => 'bi-sliders',           'label' => 'Settings'],
                ['href' => 'audit-log.php',     'icon' => 'bi-journal-text',      'label' => 'Audit Log'],
            ],
        ],
        [
            'heading' => 'Account',
            'items'   => [
                ['href' => 'profile.php', 'icon' => 'bi-person-circle', 'label' => 'Profile'],
            ],
        ],
    ],

    /* ── Admin ───────────────────────────────────────────────────────── */
    'admin' => [
        [
            'heading' => 'Main',
            'items'   => [
                ['href' => 'dashboard.php',      'icon' => 'bi-grid-1x2',             'label' => 'Dashboard'],
                ['href' => 'residents.php',       'icon' => 'bi-people',               'label' => 'Residents'],
                ['href' => 'medical-records.php', 'icon' => 'bi-file-earmark-medical', 'label' => 'Medical Records'],
                ['href' => 'doctors.php',         'icon' => 'bi-hospital',         'label' => 'Doctors'],
                ['href' => 'caretakers.php',      'icon' => 'bi-person-heart',         'label' => 'Caretakers'],
                ['href' => 'visitors.php',        'icon' => 'bi-person-lines-fill',    'label' => 'Visitors'],
            ],
        ],
        [
            'heading' => 'Finance',
            'items'   => [
                ['href' => 'donations.php', 'icon' => 'bi-gift',    'label' => 'Donations'],
                ['href' => 'billing.php',   'icon' => 'bi-receipt', 'label' => 'Billing'],
            ],
        ],
        [
            'heading' => 'Reports & Tools',
            'items'   => [
                ['href' => 'reports.php',       'icon' => 'bi-bar-chart-line', 'label' => 'Reports'],
                ['href' => 'notifications.php', 'icon' => 'bi-bell',           'label' => 'Notifications', 'badge' => 2],
                ['href' => 'settings.php',      'icon' => 'bi-sliders',        'label' => 'Settings'],
            ],
        ],
        [
            'heading' => 'Account',
            'items'   => [
                ['href' => 'profile.php', 'icon' => 'bi-person-circle', 'label' => 'Profile'],
            ],
        ],
    ],

    /* ── Caretaker ───────────────────────────────────────────────────── */
    'caretaker' => [
        [
            'heading' => 'Main',
            'items'   => [
                ['href' => 'dashboard.php',   'icon' => 'bi-grid-1x2',             'label' => 'Dashboard'],
                ['href' => 'attendance.php',  'icon' => 'bi-check-circle',         'label' => 'Resident Attendance'],
                ['href' => 'activities.php',  'icon' => 'bi-calendar2-range',      'label' => 'Daily Activities'],
                ['href' => 'meals.php',       'icon' => 'bi-egg-fried',            'label' => 'Meal Schedule'],
                ['href' => 'specialcare.php', 'icon' => 'bi-heart-fill',           'label' => 'Special Care'],
                ['href' => 'emergency.php',   'icon' => 'bi-exclamation-triangle', 'label' => 'Emergency Report'],
            ],
        ],
        [
            'heading' => 'Account',
            'items'   => [
                ['href' => 'profile.php', 'icon' => 'bi-person-circle', 'label' => 'Profile'],
            ],
        ],
    ],

    /* ── Doctor ──────────────────────────────────────────────────────── */
    'doctor' => [
        [
            'heading' => 'Main',
            'items'   => [
                ['href' => 'dashboard.php',      'icon' => 'bi-grid-1x2',             'label' => 'Dashboard'],
                ['href' => 'residents.php',       'icon' => 'bi-people',               'label' => 'Patients'],
                ['href' => 'medical-records.php', 'icon' => 'bi-file-earmark-medical', 'label' => 'Medical Records'],
                ['href' => 'prescriptions.php',   'icon' => 'bi-capsule',              'label' => 'Prescriptions'],
                ['href' => 'appointments.php',    'icon' => 'bi-calendar-check',       'label' => 'Appointments'],
            ],
        ],
        [
            'heading' => 'Communication',
            'items'   => [
                ['href' => 'notifications.php', 'icon' => 'bi-bell', 'label' => 'Notifications', 'badge' => 1],
            ],
        ],
        [
            'heading' => 'Account',
            'items'   => [
                ['href' => 'profile.php', 'icon' => 'bi-person-circle', 'label' => 'Profile'],
            ],
        ],
    ],

    /* ── Donor ───────────────────────────────────────────────────────── */
    'donor' => [
        [
            'heading' => 'Main',
            'items'   => [
                ['href' => 'dashboard.php',        'icon' => 'bi-grid-1x2',       'label' => 'Dashboard'],
                ['href' => 'donations.php',        'icon' => 'bi-gift',           'label' => 'My Donations'],
                ['href' => 'donation-history.php', 'icon' => 'bi-clock-history',  'label' => 'Donation History'],
                ['href' => 'impact.php',           'icon' => 'bi-heart-pulse',    'label' => 'Impact Stories'],
            ],
        ],
        [
            'heading' => 'Account',
            'items'   => [
                ['href' => 'notifications.php', 'icon' => 'bi-bell',           'label' => 'Notifications'],
                ['href' => 'profile.php',       'icon' => 'bi-person-circle',  'label' => 'Profile'],
            ],
        ],
    ],

    /* ── Family Member ───────────────────────────────────────────────── */
    'family_member' => [
        [
            'heading' => 'My Loved One',
            'items'   => [
                ['href' => 'dashboard.php',        'icon' => 'bi-grid-1x2',             'label' => 'Dashboard'],
                ['href' => 'resident-profile.php', 'icon' => 'bi-person-heart',         'label' => 'Resident Profile'],
                ['href' => 'health-updates.php',   'icon' => 'bi-heart-pulse',          'label' => 'Health Updates'],
                ['href' => 'visitors.php',         'icon' => 'bi-calendar-event',       'label' => 'Visit Schedule'],
                ['href' => 'billing.php',          'icon' => 'bi-receipt',              'label' => 'Billing'],
            ],
        ],
        [
            'heading' => 'Account',
            'items'   => [
                ['href' => 'notifications.php', 'icon' => 'bi-bell',           'label' => 'Notifications'],
                ['href' => 'profile.php',       'icon' => 'bi-person-circle',  'label' => 'Profile'],
            ],
        ],
    ],
];

/* Fallback if role is unrecognised */
if (!array_key_exists($sn_role, $sn_nav_map)) {
    $sn_role = 'admin';
}

$sn_sections = $sn_nav_map[$sn_role];

/* Relative path prefix for assets (adjust if sidebar.php moves) */
$sn_base = rtrim(dirname($_SERVER['PHP_SELF'] ?? ''), '/');
/* Resolve asset root – assumes sidebar.php is in /includes/ */

?>
<!-- =========================================================
     SevaNest Sidebar Component
     Role: <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $sn_role))); ?>
     ========================================================= -->

<!-- Bootstrap 5 CSS (CDN) -->
<link rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
      crossorigin="anonymous">

<!-- Bootstrap Icons (CDN) -->
<link rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<!-- SevaNest Sidebar Stylesheet -->
<link rel="stylesheet" href="<?php echo htmlspecialchars($sn_asset_root); ?>/css/sidebar.css">

<!-- Mobile Overlay -->
<div id="sn-overlay" aria-hidden="true"></div>

<!-- ═══════════════════════════════════════════════
     SIDEBAR
     ═══════════════════════════════════════════════ -->
<!-- <aside id="sn-sidebar"
       role="navigation"
       aria-label="Main sidebar navigation">
       
       <button id="sn-toggle-btn" type="button" aria-label="Toggle Sidebar">
    <i class="bi bi-list"></i>
</button> -->

    <!-- ── Logo ──────────────────────────────────────────────────────── -->
<!-- <div class="sn-logo-wrap">
    <div class="sn-brand">

        <img
            src="<?php echo htmlspecialchars($sn_asset_root); ?>/images/logo/logo.jpeg"
            alt="SevaNest Logo"
            class="sn-logo">

        <span class="sn-brand-name">SevaNest</span>

    </div>
</div> -->
<aside id="sn-sidebar"
       role="navigation"
       aria-label="Main sidebar navigation">

    <!-- ── Sidebar Header ───────────────────────────────────────────── -->
    <div class="sn-logo-wrap">

        <button id="sn-toggle-btn" type="button" aria-label="Toggle Sidebar">
            <i class="bi bi-list"></i>
        </button>

        <div class="sn-brand">

            <img
                src="<?php echo htmlspecialchars($sn_asset_root); ?>/images/logo/logo.jpeg"
                alt="SevaNest Logo"
                class="sn-logo">

            <span class="sn-brand-name">SevaNest</span>

        </div>

    </div>

    <!-- ── Navigation ────────────────────────────────────────────────── -->
    <nav class="sn-nav-wrap" aria-label="Sidebar navigation menu">
        <ul class="sn-nav-list" role="list">

<?php foreach ($sn_sections as $section): ?>

<?php if (!empty($section['heading'])): ?>
            <?php echo sn_nav_heading($section['heading']); ?>
<?php endif; ?>

<?php foreach ($section['items'] as $item): ?>
            <?php
                echo sn_nav_item(
                    href      : $item['href'],
                    icon      : $item['icon'],
                    label     : $item['label'],
                    badge     : (int)($item['badge'] ?? 0),
                    extraClass: $item['extraClass'] ?? ''
                );
            ?>
<?php endforeach; ?>

<?php endforeach; ?>

        </ul>
    </nav>

    <!-- ── Footer ────────────────────────────────────────────────────── -->
    <footer class="sn-footer" role="contentinfo">
        <ul class="sn-nav-list" role="list">
            <li class="sn-nav-item">
                <a href="<?php echo htmlspecialchars($sn_asset_root); ?>/../modules/authentication/logout.php"
                   class="sn-nav-link sn-logout-link"
                   aria-label="Logout of SevaNest"
                   tabindex="0"
                   onclick="return confirm('Are you sure you want to logout?');">
                    <i class="bi bi-box-arrow-left sn-icon" aria-hidden="true"></i>
                    <span class="sn-label">Logout</span>
                </a>
                <span class="sn-tooltip" aria-hidden="true">Logout</span>
            </li>
        </ul>

        <hr class="sn-footer-divider" aria-hidden="true">

        <p class="sn-version" aria-label="Application version 1.0">
            Version 1.0
        </p>
    </footer>

</aside>
<!-- ── End Sidebar ── -->

<!-- SevaNest Sidebar JavaScript (deferred for performance) -->
<script src="<?php echo htmlspecialchars($sn_asset_root); ?>/js/sidebar.js" defer></script>
