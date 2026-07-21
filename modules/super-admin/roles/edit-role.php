<?php
$page_title = "Edit Role";
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
            <h1 class="page-title m-0 h4 font-medium text-slate">Modify Access Role</h1>
        </div>
    </header>
    
    <div class="content-body container-fluid py-4 px-4">
        <div class="custom-card" style="max-width: 800px; margin: 0 auto;">
            <h2 class="h5 mb-4 text-slate border-bottom pb-3">Update Permissions: Staff Manager</h2>
            
            <form action="#" method="POST" onsubmit="event.preventDefault(); alert('Security role successfully updated (Mock)');">
                <div class="mb-3">
                    <label class="form-label text-slate font-medium">Role Identifier Name</label>
                    <input type="text" class="form-control rounded-3 p-2.5" value="Staff Manager" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label text-slate font-medium">Description</label>
                    <textarea class="form-control rounded-3 p-2.5" rows="3" required>Manage resident records, admission logs, daily health metrics, and financial contributions logs. Excludes core server tweaks.</textarea>
                </div>
                
                <div class="mb-4">
                    <label class="form-label text-slate font-medium border-bottom pb-2 w-100 mb-3">Permissions Matrix Authorization</label>
                    <div class="row g-3">
                        <div class="col-12 col-sm-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="p1" checked>
                                <label class="form-check-label text-slate" for="p1">User Profile Editor (Add / Modify System Users)</label>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="p2" checked>
                                <label class="form-check-label text-slate" for="p2">Manage Financial Contributions & Donations</label>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="p3" checked>
                                <label class="form-check-label text-slate" for="p3">Resident Health Care Logs & Admission Details</label>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="p4">
                                <label class="form-check-label text-slate" for="p4">Adjust Global System configurations</label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-end gap-3 border-top pt-3">
                    <a href="index.php" class="btn btn-secondary-custom border">Cancel</a>
                    <button type="submit" class="btn btn-primary-custom">Update Permissions</button>
                </div>
            </form>
        </div>
    </div>
    
    <footer class="app-footer text-center py-3 bg-white border-top mt-auto" style="font-size: 13px; color: var(--text-color); font-weight: 500;">
        &copy; <?= date('Y'); ?> <strong>SevaNest</strong> - Old Age Home Management System. All rights reserved.
    </footer>
</div>

<?php include $path_to_root . 'includes/footer.php'; ?>
