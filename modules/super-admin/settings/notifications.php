<?php
$page_title = "Notification Settings";
$active_page = "settings";
$active_tab = "notifications";
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
            <h1 class="page-title m-0 h4 font-medium text-slate">System Settings</h1>
        </div>
    </header>
    
    <div class="content-body container-fluid py-4 px-4">
        <!-- Settings Tabs Navigation -->
        <ul class="nav nav-tabs mb-4 border-bottom" id="settingsTabs">
            <li class="nav-item">
                <a class="nav-link" href="general.php" style="color: var(--text-color);">General Settings</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="security.php" style="color: var(--text-color);">Security Policies</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active font-medium" href="notifications.php" style="color: var(--primary-color); border-bottom: 3px solid var(--primary-color);">SMTP Gateways</a>
            </li>
        </ul>
        
        <div class="custom-card">
            <h2 class="h5 mb-4 text-slate border-bottom pb-3">SMTP Host Configuration</h2>
            
            <form action="#" method="POST" onsubmit="event.preventDefault(); alert('SMTP credentials updated successfully (Mock)');">
                <div class="row g-3 mb-4">
                    <div class="col-12 col-sm-8">
                        <label class="form-label text-slate font-medium">SMTP Server Hostname</label>
                        <input type="text" class="form-control rounded-3 p-2.5" value="smtp.mailtrap.io" required>
                    </div>
                    <div class="col-12 col-sm-4">
                        <label class="form-label text-slate font-medium">SMTP Connection Port</label>
                        <input type="text" class="form-control rounded-3 p-2.5" value="2525" required>
                    </div>
                </div>
                
                <h3 class="h6 text-slate mb-3 border-bottom pb-2 font-medium">Automated Alerts Triggers</h3>
                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="n1" checked>
                            <label class="form-check-label text-slate" for="n1">Email alert on recorded donation inputs</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="n2" checked>
                            <label class="form-check-label text-slate" for="n2">Email alert on new resident registration files</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="n3">
                            <label class="form-check-label text-slate" for="n3">SMTP warning log upon invalid login attempts</label>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-end pt-2">
                    <button type="submit" class="btn btn-primary-custom">Update Notifications</button>
                </div>
            </form>
        </div>
    </div>
    
    <footer class="app-footer text-center py-3 bg-white border-top mt-auto" style="font-size: 13px; color: var(--text-color); font-weight: 500;">
        &copy; <?= date('Y'); ?> <strong>SevaNest</strong> - Old Age Home Management System. All rights reserved.
    </footer>
</div>

<?php include $path_to_root . 'includes/footer.php'; ?>
