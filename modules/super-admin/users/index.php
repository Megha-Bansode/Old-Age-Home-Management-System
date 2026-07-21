<?php
$page_title = "User Management";
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
            <h1 class="page-title m-0 h4 font-medium text-slate">User Management</h1>
        </div>
        
        <div class="d-flex align-items-center gap-3">
            <div class="d-none d-md-flex align-items-center text-muted me-2" style="font-size: 14px;">
                <i class="bi bi-calendar3 me-2"></i>
                <span><?= date('l, F j, Y'); ?></span>
            </div>
        </div>
    </header>
    
    <div class="content-body container-fluid py-4 px-4">
        <div class="custom-card">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
                <h2 class="h5 mb-0 text-slate">System User Directory</h2>
                <a href="add-user.php" class="btn btn-primary-custom d-inline-flex align-items-center gap-2">
                    <i class="bi bi-person-plus-fill"></i>
                    <span>Add New User</span>
                </a>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr style="font-size: 12px; text-transform: uppercase; color: var(--text-color); font-weight: 700;">
                            <th class="ps-3">ID</th>
                            <th>Full Name</th>
                            <th>Email Address</th>
                            <th>Assigned Role</th>
                            <th>Account Status</th>
                            <th class="pe-3 text-end">Operations</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 14px; color: var(--text-color);">
                        <tr>
                            <td class="ps-3 text-muted font-medium">#U-001</td>
                            <td>Super Admin</td>
                            <td>admin@sevanest.com</td>
                            <td>Super Administrator</td>
                            <td><span class="badge" style="background-color: var(--primary-light); color: var(--primary-color);">Active</span></td>
                            <td class="pe-3 text-end">
                                <div class="d-inline-flex gap-2">
                                    <a href="view-user.php?id=1" class="btn btn-sm btn-light border"><i class="bi bi-eye"></i></a>
                                    <a href="edit-user.php?id=1" class="btn btn-sm btn-light border"><i class="bi bi-pencil"></i></a>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="ps-3 text-muted font-medium">#U-002</td>
                            <td>Ravi Kumar</td>
                            <td>ravi.kumar@sevanest.com</td>
                            <td>Staff Manager</td>
                            <td><span class="badge" style="background-color: var(--primary-light); color: var(--primary-color);">Active</span></td>
                            <td class="pe-3 text-end">
                                <div class="d-inline-flex gap-2">
                                    <a href="view-user.php?id=2" class="btn btn-sm btn-light border"><i class="bi bi-eye"></i></a>
                                    <a href="edit-user.php?id=2" class="btn btn-sm btn-light border"><i class="bi bi-pencil"></i></a>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="ps-3 text-muted font-medium">#U-003</td>
                            <td>Priya Sharma</td>
                            <td>priya.sharma@sevanest.com</td>
                            <td>Volunteer Coordinator</td>
                            <td><span class="badge bg-danger-subtle text-danger">Inactive</span></td>
                            <td class="pe-3 text-end">
                                <div class="d-inline-flex gap-2">
                                    <a href="view-user.php?id=3" class="btn btn-sm btn-light border"><i class="bi bi-eye"></i></a>
                                    <a href="edit-user.php?id=3" class="btn btn-sm btn-light border"><i class="bi bi-pencil"></i></a>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <footer class="app-footer text-center py-3 bg-white border-top mt-auto" style="font-size: 13px; color: var(--text-color); font-weight: 500;">
        &copy; <?= date('Y'); ?> <strong>SevaNest</strong> - Old Age Home Management System. All rights reserved.
    </footer>
</div>

<?php include $path_to_root . 'includes/footer.php'; ?>
