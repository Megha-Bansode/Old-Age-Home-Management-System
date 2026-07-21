<?php
$page_title = "View User";
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
            <h1 class="page-title m-0 h4 font-medium text-slate">User Profile Overview</h1>
        </div>
    </header>
    
    <div class="content-body container-fluid py-4 px-4">
        <div class="custom-card mb-4" style="max-width: 800px; margin: 0 auto;">
            <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                <h2 class="h5 mb-0 text-slate">Profile Information</h2>
                <a href="edit-user.php?id=2" class="btn btn-primary-custom d-inline-flex align-items-center gap-2" style="padding: 8px 16px; font-size: 14px;">
                    <i class="bi bi-pencil-square"></i>
                    <span>Edit Profile</span>
                </a>
            </div>
            
            <div class="row align-items-center g-4">
                <div class="col-12 col-md-3 text-center">
                    <div class="user-avatar mx-auto font-heading text-white" style="width: 100px; height: 100px; font-size: 36px; border-radius: 50%; border: 3px solid var(--accent-color);">
                        RK
                    </div>
                </div>
                
                <div class="col-12 col-md-9">
                    <div class="row g-3">
                        <div class="col-6">
                            <span class="text-muted d-block" style="font-size: 12px; text-transform: uppercase;">Full Name</span>
                            <span class="text-slate font-medium h6 mb-0">Ravi Kumar</span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block" style="font-size: 12px; text-transform: uppercase;">Email Address</span>
                            <span class="text-slate font-medium h6 mb-0">ravi.kumar@sevanest.com</span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block" style="font-size: 12px; text-transform: uppercase;">Assigned Role</span>
                            <span class="font-medium h6 mb-0" style="color: var(--accent-color);">Staff Manager</span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block" style="font-size: 12px; text-transform: uppercase;">Status</span>
                            <span class="badge" style="background-color: var(--primary-light); color: var(--primary-color);">Active</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="border-top pt-4 mt-4">
                <h3 class="h6 text-slate mb-3 uppercase font-medium">Recent Activity Records</h3>
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0" style="font-size: 13px;">
                        <thead>
                            <tr class="table-light">
                                <th class="ps-2">Operation Description</th>
                                <th>Result</th>
                                <th class="pe-2">Timestamp</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="ps-2">Updated room assignments for Resident #48</td>
                                <td><span class="badge bg-success-subtle text-success">Success</span></td>
                                <td class="pe-2 text-muted">2026-07-19 10:15:45</td>
                            </tr>
                            <tr>
                                <td class="ps-2">Logged in successfully via web interface</td>
                                <td><span class="badge bg-success-subtle text-success">Success</span></td>
                                <td class="pe-2 text-muted">2026-07-19 08:30:12</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="border-top pt-3 mt-4 text-start">
                <a href="index.php" class="btn btn-secondary-custom border">Back to List</a>
            </div>
        </div>
    </div>
    
    <footer class="app-footer text-center py-3 bg-white border-top mt-auto" style="font-size: 13px; color: var(--text-color); font-weight: 500;">
        &copy; <?= date('Y'); ?> <strong>SevaNest</strong> - Old Age Home Management System. All rights reserved.
    </footer>
</div>

<?php include $path_to_root . 'includes/footer.php'; ?>
