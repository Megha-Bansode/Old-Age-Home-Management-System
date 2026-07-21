<?php
$page_title = "User Activity Audit Trail";
$active_page = "reports";
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
            <h1 class="page-title m-0 h4 font-medium text-slate">Reports Library</h1>
        </div>
    </header>
    
    <div class="content-body container-fluid py-4 px-4">
        <div class="custom-card">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 border-bottom pb-3 mb-4">
                <div>
                    <h2 class="h5 mb-1 text-slate">User Activity Audit Trail</h2>
                    <p class="text-muted mb-0" style="font-size: 13px;">View and generate active system summaries</p>
                </div>
                <div class="d-inline-flex gap-2">
                    <button onclick="window.print();" class="btn btn-primary-custom d-inline-flex align-items-center gap-2">
                        <i class="bi bi-printer-fill"></i>
                        <span>Print Document</span>
                    </button>
                    <a href="index.php" class="btn btn-secondary-custom border">Back to Reports</a>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr style="font-size: 12px; text-transform: uppercase; color: var(--text-color); font-weight: 700;">
                            <th class='ps-3'>Audit ID</th><th>Email</th><th>Action Triggered</th><th>IP Address</th><th>Result</th><th class='pe-3'>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 14px; color: var(--text-color);">
                                                <tr>
                            <td class="ps-3 text-muted font-medium">#AUD-891</td>
                            <td>admin@sevanest.com</td>
                            <td>User logged in</td>
                            <td>192.168.1.5</td>
                            <td><span class="badge" style="background-color: var(--primary-light); color: var(--primary-color);">Success</span></td>
                            <td class="pe-3 text-muted">2026-07-19 11:32:10</td>
                        </tr>
                        <tr>
                            <td class="ps-3 text-muted font-medium">#AUD-890</td>
                            <td>admin@sevanest.com</td>
                            <td>Attempted unauthorized settings update</td>
                            <td>192.168.1.5</td>
                            <td><span class="badge bg-danger-subtle text-danger">Denied</span></td>
                            <td class="pe-3 text-muted">2026-07-19 11:12:04</td>
                        </tr>
                        <tr>
                            <td class="ps-3 text-muted font-medium">#AUD-889</td>
                            <td>manager@sevanest.com</td>
                            <td>Added resident ID #48</td>
                            <td>192.168.1.12</td>
                            <td><span class="badge" style="background-color: var(--primary-light); color: var(--primary-color);">Success</span></td>
                            <td class="pe-3 text-muted">2026-07-19 10:15:45</td>
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
