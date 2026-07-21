<?php
$page_title = "Security Settings";
$active_page = "settings";
$active_tab = "security";
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
                <a class="nav-link active font-medium" href="security.php" style="color: var(--primary-color); border-bottom: 3px solid var(--primary-color);">Security Policies</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="notifications.php" style="color: var(--text-color);">SMTP Gateways</a>
            </li>
        </ul>
        
        <div class="custom-card">
            <h2 class="h5 mb-4 text-slate border-bottom pb-3">Access Security Policies</h2>
            
            <form action="#" method="POST" onsubmit="event.preventDefault(); alert('Security rules successfully saved (Mock)');">
                <div class="mb-3">
                    <label class="form-label text-slate font-medium">Access Password Strength Control</label>
                    <select class="form-select rounded-3 p-2.5">
                        <option value="low">Standard (Min 6 characters)</option>
                        <option value="medium" selected>Medium (Min 8 characters, letters & digits)</option>
                        <option value="high">Strong (Min 10 characters, mixed case, numbers & special character)</option>
                    </select>
                </div>
                
                <div class="mb-3 py-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="mfa" checked>
                        <label class="form-check-label text-slate font-medium" for="mfa">
                            Force Multi-Factor Authentication (MFA) for Super Administrator Accounts
                        </label>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label text-slate font-medium">Inactive Session Timeout (Minutes)</label>
                    <input type="number" class="form-control rounded-3 p-2.5" value="30" required>
                </div>
                
                <div class="d-flex justify-content-end pt-2">
                    <button type="submit" class="btn btn-primary-custom">Update Security Policies</button>
                </div>
            </form>
        </div>
    </div>
    
    <footer class="app-footer text-center py-3 bg-white border-top mt-auto" style="font-size: 13px; color: var(--text-color); font-weight: 500;">
        &copy; <?= date('Y'); ?> <strong>SevaNest</strong> - Old Age Home Management System. All rights reserved.
    </footer>
</div>

<?php include $path_to_root . 'includes/footer.php'; ?>
