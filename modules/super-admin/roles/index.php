<?php
$page_title = "Role Management";
$active_page = "roles";
$path_to_root = "../../../";

include $path_to_root . 'includes/header.php';
include $path_to_root . 'includes/sidebar.php';
?>

<div class="main-content">
    <header class="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="toggle-sidebar-btn d-lg-none" aria-label="Toggle Navigation">
                <i class="bi bi-list"></i>
            </button>
            <h1 class="page-title m-0 h4 font-medium text-slate">Role Management</h1>
        </div>
    </header>
    
    <div class="content-body container-fluid py-4 px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h5 mb-0 text-slate">Access Authorization Roles</h2>
            <a href="add-role.php" class="btn btn-primary-custom d-inline-flex align-items-center gap-2">
                <i class="bi bi-shield-plus"></i>
                <span>Add New Role</span>
            </a>
        </div>
        
        <div class="row g-4">
            <!-- Card 1 -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="custom-card h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3 class="h6 mb-0 font-medium text-slate">Super Administrator</h3>
                            <span class="badge" style="background-color: var(--primary-light); color: var(--primary-color);">Full Root</span>
                        </div>
                        <p class="text-muted mb-4" style="font-size: 13px; line-height: 1.5;">Unrestricted access to all modules, financial details, security policies, DB records, and role manager matrices.</p>
                    </div>
                    <div class="border-top pt-3">
                        <a href="edit-role.php?id=1" class="btn btn-secondary-custom border w-100 text-center d-block">Modify Permissions</a>
                    </div>
                </div>
            </div>
            
            <!-- Card 2 -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="custom-card h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3 class="h6 mb-0 font-medium text-slate">Staff Manager</h3>
                            <span class="badge" style="background-color: var(--primary-light); color: var(--primary-color);">Manager Access</span>
                        </div>
                        <p class="text-muted mb-4" style="font-size: 13px; line-height: 1.5;">Manage resident records, admission logs, daily health metrics, and financial contributions logs. Excludes core server tweaks.</p>
                    </div>
                    <div class="border-top pt-3">
                        <a href="edit-role.php?id=2" class="btn btn-secondary-custom border w-100 text-center d-block">Modify Permissions</a>
                    </div>
                </div>
            </div>
            
            <!-- Card 3 -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="custom-card h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3 class="h6 mb-0 font-medium text-slate">Volunteer Coordinator</h3>
                            <span class="badge" style="background-color: var(--primary-light); color: var(--primary-color);">Restricted</span>
                        </div>
                        <p class="text-muted mb-4" style="font-size: 13px; line-height: 1.5;">Register volunteers, assign task slots, update activity charts. No monetary or security parameter privileges.</p>
                    </div>
                    <div class="border-top pt-3">
                        <a href="edit-role.php?id=3" class="btn btn-secondary-custom border w-100 text-center d-block">Modify Permissions</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <footer class="app-footer text-center py-3 bg-white border-top mt-auto" style="font-size: 13px; color: var(--text-color); font-weight: 500;">
        &copy; <?= date('Y'); ?> <strong>SevaNest</strong> - Old Age Home Management System. All rights reserved.
    </footer>
</div>

<?php include $path_to_root . 'includes/footer.php'; ?>
