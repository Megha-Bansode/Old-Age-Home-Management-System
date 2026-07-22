<?php
$page_title = "Profile Settings";
$active_page = "profile";
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
            <h1 class="page-title m-0 h4 font-medium text-slate">My Profile</h1>
        </div>
    </header>
    
    <div class="content-body container-fluid py-4 px-4">
        <div class="custom-card" style="max-width: 800px; margin: 0 auto;">
            <h2 class="h5 mb-4 text-slate border-bottom pb-3">Super Admin Profile Settings</h2>
            
            <form action="#" method="POST" onsubmit="event.preventDefault(); alert('Profile credentials updated successfully (Mock)');">
                <div class="d-flex flex-column flex-sm-row gap-4 align-items-center mb-4">
                    <div class="user-avatar text-white font-heading" style="width: 100px; height: 100px; font-size: 36px; border-radius: 50%; border: 3px solid var(--accent-color);">
                        SA
                    </div>
                    <div class="text-center text-sm-start">
                        <label class="form-label text-slate font-medium d-block mb-2">Display Profile Photo</label>
                        <div class="d-flex gap-2 justify-content-center justify-content-sm-start">
                            <button type="button" class="btn btn-sm btn-light border font-medium">Upload Image</button>
                            <button type="button" class="btn btn-sm btn-light border text-danger font-medium border-danger-subtle">Remove</button>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-12 col-sm-6">
                        <label class="form-label text-slate font-medium">First Name</label>
                        <input type="text" class="form-control rounded-3 p-2.5" value="Super" required>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="form-label text-slate font-medium">Last Name</label>
                        <input type="text" class="form-control rounded-3 p-2.5" value="Admin" required>
                    </div>
                </div>
                
                <div class="row g-3 mb-4">
                    <div class="col-12 col-sm-6">
                        <label class="form-label text-slate font-medium">Email Address</label>
                        <input type="email" class="form-control rounded-3 p-2.5" value="admin@sevanest.com" required>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="form-label text-slate font-medium">Contact Phone Number</label>
                        <input type="text" class="form-control rounded-3 p-2.5" value="+91 9999999999">
                    </div>
                </div>

                <h3 class="h6 text-slate mb-3 border-bottom pb-2 font-medium">Change Account Password</h3>

                <div class="mb-3">
                    <label class="form-label text-slate font-medium">Current Password</label>
                    <input type="password" class="form-control rounded-3 p-2.5" placeholder="â€¢â€¢â€¢â€¢â€¢â€¢â€¢â€¢">
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-12 col-sm-6">
                        <label class="form-label text-slate font-medium">New Password</label>
                        <input type="password" class="form-control rounded-3 p-2.5" placeholder="Minimum 8 characters">
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="form-label text-slate font-medium">Confirm New Password</label>
                        <input type="password" class="form-control rounded-3 p-2.5" placeholder="Confirm new password">
                    </div>
                </div>
                
                <div class="d-flex justify-content-end border-top pt-3 mt-4">
                    <button type="submit" class="btn btn-primary-custom">Update Profile Settings</button>
                </div>
            </form>
        </div>
    </div>
    
    <footer class="app-footer text-center py-3 bg-white border-top mt-auto" style="font-size: 13px; color: var(--text-color); font-weight: 500;">
        &copy; <?= date('Y'); ?> <strong>SevaNest</strong> - Old Age Home Management System. All rights reserved.
    </footer>
</div>

<?php include $path_to_root . 'includes/footer.php'; ?>
