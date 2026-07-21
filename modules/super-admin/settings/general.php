<?php
$page_title = "System Settings";
$active_page = "settings";
$active_tab = "general";
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
                <a class="nav-link active font-medium" href="general.php" style="color: var(--primary-color); border-bottom: 3px solid var(--primary-color);">General Settings</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="security.php" style="color: var(--text-color);">Security Policies</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="notifications.php" style="color: var(--text-color);">SMTP Gateways</a>
            </li>
        </ul>
        
        <div class="custom-card">
            <h2 class="h5 mb-4 text-slate border-bottom pb-3">Organization Profile</h2>
            
            <form action="#" method="POST" onsubmit="event.preventDefault(); alert('General settings updated successfully (Mock)');">
                <div class="row g-3 mb-3">
                    <div class="col-12 col-sm-6">
                        <label class="form-label text-slate font-medium">Old Age Home Brand Name</label>
                        <input type="text" class="form-control rounded-3 p-2.5" value="SevaNest" required>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="form-label text-slate font-medium">Contact Email address</label>
                        <input type="email" class="form-control rounded-3 p-2.5" value="contact@sevanest.org" required>
                    </div>
                </div>
                
                <div class="row g-3 mb-3">
                    <div class="col-12 col-sm-6">
                        <label class="form-label text-slate font-medium">Primary Helpline Number</label>
                        <input type="text" class="form-control rounded-3 p-2.5" value="+91 9876543210" required>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="form-label text-slate font-medium">Currency Symbol</label>
                        <input type="text" class="form-control rounded-3 p-2.5" value="INR (â‚¹)" required>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label text-slate font-medium">Street Address</label>
                    <input type="text" class="form-control rounded-3 p-2.5" value="Plot 45, VIP Road, Sector 5" required>
                </div>
                
                <div class="d-flex justify-content-end pt-2">
                    <button type="submit" class="btn btn-primary-custom">Save Configurations</button>
                </div>
            </form>
        </div>
    </div>
    
    <footer class="app-footer text-center py-3 bg-white border-top mt-auto" style="font-size: 13px; color: var(--text-color); font-weight: 500;">
        &copy; <?= date('Y'); ?> <strong>SevaNest</strong> - Old Age Home Management System. All rights reserved.
    </footer>
</div>

<?php include $path_to_root . 'includes/footer.php'; ?>
