<?php
/**
 * SevaNest — Shared Sidebar Navigation Component
 * 
 * Expected Variables:
 *   - $base_path : Relative path multiplier to root (e.g. "../../" or "../../../")
 *   - $active_page : Identifier of current page (e.g. 'dashboard', 'user_management', 'role_management', 'reports', 'statistics', 'settings')
 */

$path_prefix = isset($base_path) ? $base_path : '';
$current_page_file = basename($_SERVER['PHP_SELF']);

// Fallback active page detection if $active_page is not explicitly set
if (!isset($active_page)) {
    if (strpos($current_page_file, 'user_management') !== false) {
        $active_page = 'user_management';
    } elseif (strpos($current_page_file, 'role_management') !== false) {
        $active_page = 'role_management';
    } elseif (strpos($current_page_file, 'reports') !== false) {
        $active_page = 'reports';
    } elseif (strpos($current_page_file, 'statistics') !== false) {
        $active_page = 'statistics';
    } elseif (strpos($current_page_file, 'settings') !== false) {
        $active_page = 'settings';
    } else {
        $active_page = 'dashboard';
    }
}
?>

<!-- ╔══════════════════════════════════════════════════════════╗
     ║  SEVANEST SIDEBAR NAVIGATION                            ║
     ╚══════════════════════════════════════════════════════════╝ -->
<aside class="sidebar-wrapper" aria-label="Main Module Sidebar">
    <div class="sidebar-brand-box">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-shield-lock-fill text-warning fs-4"></i>
            <div>
                <div class="sidebar-brand-title">Super Admin</div>
                <small class="sidebar-brand-subtitle">Control Panel</small>
            </div>
        </div>
        <button type="button" class="sidebar-toggle btn btn-sm p-0 text-white opacity-75" aria-label="Toggle Sidebar">
            <i class="bi bi-chevron-left fs-5"></i>
        </button>
    </div>

    <nav class="sidebar-menu-nav">
        <div class="sidebar-section-label">MAIN MENU</div>
        <ul class="sidebar-menu">
            <li class="sidebar-item">
                <a href="<?php echo htmlspecialchars($path_prefix); ?>modules/super_admin/index.php" 
                   class="sidebar-link <?php echo ($active_page === 'dashboard') ? 'active' : ''; ?>">
                    <i class="bi bi-speedometer2 me-2"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="<?php echo htmlspecialchars($path_prefix); ?>modules/super_admin/user_management/user_management.php" 
                   class="sidebar-link <?php echo ($active_page === 'user_management') ? 'active' : ''; ?>">
                    <i class="bi bi-people-fill me-2"></i>
                    <span>User Management</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="<?php echo htmlspecialchars($path_prefix); ?>modules/super_admin/role_management/role_management.php" 
                   class="sidebar-link <?php echo ($active_page === 'role_management') ? 'active' : ''; ?>">
                    <i class="bi bi-shield-check me-2"></i>
                    <span>Role Management</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="<?php echo htmlspecialchars($path_prefix); ?>modules/super_admin/reports/reports.php" 
                   class="sidebar-link <?php echo ($active_page === 'reports') ? 'active' : ''; ?>">
                    <i class="bi bi-file-earmark-bar-graph-fill me-2"></i>
                    <span>Reports</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="<?php echo htmlspecialchars($path_prefix); ?>modules/super_admin/statistics/statistics.php" 
                   class="sidebar-link <?php echo ($active_page === 'statistics') ? 'active' : ''; ?>">
                    <i class="bi bi-pie-chart-fill me-2"></i>
                    <span>Statistics</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="<?php echo htmlspecialchars($path_prefix); ?>modules/super_admin/settings/settings.php" 
                   class="sidebar-link <?php echo ($active_page === 'settings') ? 'active' : ''; ?>">
                    <i class="bi bi-gear-fill me-2"></i>
                    <span>Settings</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-section-label mt-4">ACCOUNT</div>
        <ul class="sidebar-menu">
            <li class="sidebar-item">
                <a href="<?php echo htmlspecialchars($path_prefix); ?>modules/authentication/logout.php" 
                   class="sidebar-link text-danger-custom">
                    <i class="bi bi-box-arrow-right me-2"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>
