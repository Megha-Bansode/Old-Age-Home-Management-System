<?php
if (!isset($active_page)) {
    $active_page = 'dashboard';
}
if (!isset($path_to_root)) {
    $path_to_root = "./";
}
?>
<!-- Collapsible Sidebar Navigation -->
<aside class="sidebar">
    <div class="brand">
        <div class="brand-icon">
            <img src="<?php echo $path_to_root; ?>assets/images/logo.jpg" alt="SevaNest Logo" style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px;">
        </div>
        <div class="brand-name">SevaNest</div>
    </div>
    
    <nav class="sidebar-menu">
        <a href="<?php echo $path_to_root; ?>modules/super-admin/dashboard/index.php" class="menu-link <?php echo $active_page == 'dashboard' ? 'active' : ''; ?>">
            <i class="bi bi-grid-1x2-fill"></i>
            <span>Dashboard</span>
        </a>
        <a href="<?php echo $path_to_root; ?>modules/super-admin/users/index.php" class="menu-link <?php echo $active_page == 'users' ? 'active' : ''; ?>">
            <i class="bi bi-people-fill"></i>
            <span>User Management</span>
        </a>
        <a href="<?php echo $path_to_root; ?>modules/super-admin/roles/index.php" class="menu-link <?php echo $active_page == 'roles' ? 'active' : ''; ?>">
            <i class="bi bi-shield-lock-fill"></i>
            <span>Role Management</span>
        </a>
        <a href="<?php echo $path_to_root; ?>modules/super-admin/reports/index.php" class="menu-link <?php echo $active_page == 'reports' ? 'active' : ''; ?>">
            <i class="bi bi-file-earmark-pdf-fill"></i>
            <span>Reports</span>
        </a>
        <a href="<?php echo $path_to_root; ?>modules/super-admin/statistics/index.php" class="menu-link <?php echo $active_page == 'statistics' ? 'active' : ''; ?>">
            <i class="bi bi-bar-chart-fill"></i>
            <span>Statistics</span>
        </a>
        <a href="<?php echo $path_to_root; ?>modules/super-admin/settings/general.php" class="menu-link <?php echo $active_page == 'settings' ? 'active' : ''; ?>">
            <i class="bi bi-sliders2"></i>
            <span>System Settings</span>
        </a>
    </nav>
    
    <div class="sidebar-footer">
        <a href="<?php echo $path_to_root; ?>modules/super-admin/profile/index.php" class="user-profile-widget">
            <div class="user-avatar">SA</div>
            <div class="user-info flex-grow-1 overflow-hidden">
                <div class="user-name text-truncate">Super Admin</div>
                <div class="user-role text-truncate" style="font-size: 11px; opacity: 0.7;">System Control</div>
            </div>
            <i class="bi bi-chevron-right" style="font-size: 12px;"></i>
        </a>
    </div>
</aside>
