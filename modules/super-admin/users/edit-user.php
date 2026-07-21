<?php
$page_title = "Edit User";
$active_page = "users";
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
            <h1 class="page-title m-0 h4 font-medium text-slate">Edit System User</h1>
        </div>
    </header>
    
    <div class="content-body container-fluid py-4 px-4">
        <div class="custom-card" style="max-width: 800px; margin: 0 auto;">
            <h2 class="h5 mb-4 text-slate border-bottom pb-3">Update User Profiles</h2>
            
            <form action="#" method="POST" onsubmit="event.preventDefault(); alert('User Profile Updated Successfully (Mock)');">
                <div class="row g-3 mb-3">
                    <div class="col-12 col-sm-6">
                        <label class="form-label text-slate font-medium">First Name</label>
                        <input type="text" class="form-control rounded-3 p-2.5" value="Ravi" required>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="form-label text-slate font-medium">Last Name</label>
                        <input type="text" class="form-control rounded-3 p-2.5" value="Kumar" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label text-slate font-medium">Email Address</label>
                    <input type="email" class="form-control rounded-3 p-2.5" value="ravi.kumar@sevanest.com" required>
                </div>
                
                <div class="row g-3 mb-4">
                    <div class="col-12 col-sm-6">
                        <label class="form-label text-slate font-medium">System Privilege Role</label>
                        <select class="form-select rounded-3 p-2.5" required>
                            <option value="1">Super Administrator</option>
                            <option value="2" selected>Staff Manager</option>
                            <option value="3">Volunteer Coordinator</option>
                        </select>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="form-label text-slate font-medium">Account Status</label>
                        <select class="form-select rounded-3 p-2.5" required>
                            <option value="1" selected>Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                
                <div class="d-flex justify-content-end gap-3 border-top pt-3">
                    <a href="index.php" class="btn btn-secondary-custom border">Cancel</a>
                    <button type="submit" class="btn btn-primary-custom">Update Details</button>
                </div>
            </form>
        </div>
    </div>
    
    <footer class="app-footer text-center py-3 bg-white border-top mt-auto" style="font-size: 13px; color: var(--text-color); font-weight: 500;">
        &copy; <?= date('Y'); ?> <strong>SevaNest</strong> - Old Age Home Management System. All rights reserved.
    </footer>
</div>

<?php include $path_to_root . 'includes/footer.php'; ?>
