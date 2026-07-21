<?php
$page_title = "System Reports";
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
            <h1 class="page-title m-0 h4 font-medium text-slate">Administrative Reports</h1>
        </div>
    </header>
    
    <div class="content-body container-fluid py-4 px-4">
        <div class="row g-4">
            <!-- Card 1 -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="custom-card h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="card-icon primary mb-3">
                            <i class="bi bi-person-lines-fill"></i>
                        </div>
                        <h3 class="h6 text-slate mb-2 font-medium">User Activity Logs</h3>
                        <p class="text-muted" style="font-size: 13px; line-height: 1.5;">Access authentication history records, IP security flags, operations tracks, and administrative changes logs.</p>
                    </div>
                    <div class="border-top pt-3 mt-3">
                        <a href="user-report.php" class="btn btn-primary-custom w-100 text-center justify-content-center d-inline-flex gap-2">
                            <i class="bi bi-file-earmark-spreadsheet"></i>
                            <span>View User Audit</span>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Card 2 -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="custom-card h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="card-icon success mb-3">
                            <i class="bi bi-currency-exchange"></i>
                        </div>
                        <h3 class="h6 text-slate mb-2 font-medium">Donation Statement</h3>
                        <p class="text-muted" style="font-size: 13px; line-height: 1.5;">Track incoming donation transactions, filter donor profiles, payment channels, and generate monthly totals tables.</p>
                    </div>
                    <div class="border-top pt-3 mt-3">
                        <a href="donation-report.php" class="btn btn-primary-custom w-100 text-center justify-content-center d-inline-flex gap-2">
                            <i class="bi bi-file-earmark-spreadsheet"></i>
                            <span>View Financials</span>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Card 3 -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="custom-card h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="card-icon accent mb-3">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <h3 class="h6 text-slate mb-2 font-medium">Resident Occupancy Directory</h3>
                        <p class="text-muted" style="font-size: 13px; line-height: 1.5;">Summarize active residential admissions, monitor room allocation, room availability status, and patient classifications.</p>
                    </div>
                    <div class="border-top pt-3 mt-3">
                        <a href="resident-report.php" class="btn btn-primary-custom w-100 text-center justify-content-center d-inline-flex gap-2">
                            <i class="bi bi-file-earmark-spreadsheet"></i>
                            <span>View Resident Stats</span>
                        </a>
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
