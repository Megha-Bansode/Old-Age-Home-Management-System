<?php
$page_title = "Donation Financial Statement";
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
                    <h2 class="h5 mb-1 text-slate">Donation Financial Statement</h2>
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
                            <th class='ps-3'>Receipt No</th><th>Donor Name</th><th>Method</th><th>Amount</th><th>Status</th><th class='pe-3'>Date</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 14px; color: var(--text-color);">
                                                <tr>
                            <td class="ps-3 text-muted font-medium">#REC-2041</td>
                            <td>Karan Johar Foundation</td>
                            <td>Online Transfer</td>
                            <td>â‚¹ 50,000</td>
                            <td><span class="badge bg-success-subtle text-success">Completed</span></td>
                            <td class="pe-3 text-muted">2026-07-18</td>
                        </tr>
                        <tr>
                            <td class="ps-3 text-muted font-medium">#REC-2040</td>
                            <td>Sunita Deshpande</td>
                            <td>Cheque</td>
                            <td>â‚¹ 15,000</td>
                            <td><span class="badge bg-success-subtle text-success">Completed</span></td>
                            <td class="pe-3 text-muted">2026-07-17</td>
                        </tr>
                        <tr>
                            <td class="ps-3 text-muted font-medium">#REC-2039</td>
                            <td>Anil Kapoor</td>
                            <td>Cash</td>
                            <td>â‚¹ 5,000</td>
                            <td><span class="badge bg-success-subtle text-success">Completed</span></td>
                            <td class="pe-3 text-muted">2026-07-15</td>
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
