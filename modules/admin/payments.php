<?php
/**
 * SevaNest — Financial & Billings Control
 * File     : modules/admin/payments.php
 * Version  : 1.0
 */

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/functions.php';

// Require Admin login
require_login();
require_role(['Admin', 'Old Age Home Admin']);

$base_path = '../../';
$page_title = 'Financial Dashboard | SevaNest';

// Handle Form Submission Mock
$formSuccess = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formSuccess = 'Payment clearance logged successfully!';
}

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'admin';
$currentPage   = 'payments.php';
$sn_asset_root = "../../assets";
include '../../includes/sidebar.php';

// Mock Financial Records
$payments = [
    ['invoice' => 'INV-9081', 'payer' => 'Kamala Devi', 'amount' => '₹15,000', 'date' => '2026-07-05', 'status' => 'Paid', 'type' => 'Maintenance Fee'],
    ['invoice' => 'INV-9082', 'payer' => 'Harish Mehta', 'amount' => '₹18,000', 'date' => '2026-07-04', 'status' => 'Paid', 'type' => 'Special Care Fee'],
    ['invoice' => 'INV-9083', 'payer' => 'Gopal Prasad', 'amount' => '₹12,000', 'date' => '2026-07-02', 'status' => 'Paid', 'type' => 'Maintenance Fee'],
];

$pendingPayments = [
    ['invoice' => 'INV-9084', 'payer' => 'Devaki Amma', 'amount' => '₹15,000', 'due_date' => '2026-08-01', 'status' => 'Pending'],
    ['invoice' => 'INV-9085', 'payer' => 'Savitri Bai', 'amount' => '₹8,500', 'due_date' => '2026-07-20', 'status' => 'Overdue'],
];

$donations = [
    ['receipt' => 'REC-4011', 'donor' => 'Vikramaditya Mehta', 'amount' => '₹1,50,000', 'category' => 'Medical Equipment', 'date' => '2026-07-25'],
    ['receipt' => 'REC-4012', 'donor' => 'Sudha Murthy Foundation', 'amount' => '₹2,00,000', 'category' => 'Nutritional Meals', 'date' => '2026-07-20'],
    ['receipt' => 'REC-4013', 'donor' => 'Anil Kapoor', 'amount' => '₹75,000', 'category' => 'General Fund', 'date' => '2026-07-15'],
];
?>

<main id="sn-main-content" role="main" aria-label="Payments Content" class="p-4 flex-grow-1">
    <div class="container-fluid">

        <?php if ($formSuccess): ?>
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> <?php echo sn_e($formSuccess); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Page Header -->
        <div class="mb-4">
            <h3 class="fw-bold mb-0 text-dark">Financial & Billing Portal</h3>
            <small class="text-muted">Monitor donations received, expense summaries, monthly collection rosters, and dues clearance</small>
        </div>

        <!-- 4 Financial Revenue Cards -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-4 border-success">
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.75rem;">Total Donations</span>
                    <h4 class="fw-bold mb-0 text-success">₹4,25,000</h4>
                    <small class="text-muted">This month</small>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-4 border-primary">
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.75rem;">Fees Collected</span>
                    <h4 class="fw-bold mb-0 text-primary">₹45,000</h4>
                    <small class="text-muted">Total maintenance fees</small>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-4 border-danger">
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.75rem;">Outstanding Dues</span>
                    <h4 class="fw-bold mb-0 text-danger">₹23,500</h4>
                    <small class="text-danger-emphasis">2 invoices unpaid</small>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-4 border-warning">
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.75rem;">Monthly Expenses</span>
                    <h4 class="fw-bold mb-0 text-warning">₹1,80,000</h4>
                    <small class="text-muted">Food, meds, & operations</small>
                </div>
            </div>
        </div>

        <!-- Billing Tabs Card -->
        <div class="card border-0 shadow-sm rounded-3 bg-white">
            <div class="card-header bg-white border-bottom border-light p-3">
                <ul class="nav nav-tabs card-header-tabs" id="paymentTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold text-dark" id="history-tab" data-bs-toggle="tab" data-bs-target="#panel-history" type="button" role="tab">Payment History</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold text-dark" id="pending-tab" data-bs-toggle="tab" data-bs-target="#panel-pending" type="button" role="tab">Pending Payments</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold text-dark" id="donations-tab" data-bs-toggle="tab" data-bs-target="#panel-donations" type="button" role="tab">Donations Received</button>
                    </li>
                </ul>
            </div>

            <div class="card-body p-0">
                <div class="tab-content" id="paymentTabsContent">
                    
                    <!-- Tab 1: Payment History -->
                    <div class="tab-pane fade show active" id="panel-history" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                                <thead class="table-light text-muted text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.05em;">
                                    <tr>
                                        <th class="ps-3">Invoice No</th>
                                        <th>Resident Payer</th>
                                        <th>Fee Type</th>
                                        <th>Amount</th>
                                        <th>Date Cleared</th>
                                        <th>Status</th>
                                        <th class="pe-3 text-end">Invoice</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($payments as $p): ?>
                                        <tr>
                                            <td class="ps-3"><span class="font-monospace text-muted"><?php echo sn_e($p['invoice']); ?></span></td>
                                            <td><span class="fw-semibold text-dark"><?php echo sn_e($p['payer']); ?></span></td>
                                            <td><span class="text-dark"><?php echo sn_e($p['type']); ?></span></td>
                                            <td><span class="text-dark fw-bold"><?php echo sn_e($p['amount']); ?></span></td>
                                            <td><span class="text-dark"><?php echo sn_e($p['date']); ?></span></td>
                                            <td><span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1"><?php echo sn_e($p['status']); ?></span></td>
                                            <td class="pe-3 text-end">
                                                <button class="btn btn-sm btn-light text-primary" data-bs-toggle="modal" data-bs-target="#invoiceModal_<?php echo sn_e($p['invoice']); ?>">
                                                    <i class="bi bi-file-earmark-text-fill"></i> View
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tab 2: Pending Payments -->
                    <div class="tab-pane fade" id="panel-pending" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                                <thead class="table-light text-muted text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.05em;">
                                    <tr>
                                        <th class="ps-3">Invoice No</th>
                                        <th>Resident</th>
                                        <th>Due Amount</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                        <th class="pe-3 text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pendingPayments as $pp): ?>
                                        <?php $badge = ($pp['status'] === 'Overdue') ? 'danger' : 'warning'; ?>
                                        <tr>
                                            <td class="ps-3"><span class="font-monospace text-muted"><?php echo sn_e($pp['invoice']); ?></span></td>
                                            <td><span class="fw-semibold text-dark"><?php echo sn_e($pp['payer']); ?></span></td>
                                            <td><span class="text-dark fw-bold"><?php echo sn_e($pp['amount']); ?></span></td>
                                            <td><span class="text-dark"><?php echo sn_e($pp['due_date']); ?></span></td>
                                            <td><span class="badge bg-<?php echo $badge; ?>-subtle text-<?php echo $badge; ?> rounded-pill px-2.5 py-1"><?php echo sn_e($pp['status']); ?></span></td>
                                            <td class="pe-3 text-end">
                                                <form method="POST" action="payments.php" class="d-inline">
                                                    <button type="submit" class="btn btn-sm btn-primary fw-semibold py-1">Record Payment</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tab 3: Donations Received -->
                    <div class="tab-pane fade" id="panel-donations" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                                <thead class="table-light text-muted text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.05em;">
                                    <tr>
                                        <th class="ps-3">Receipt No</th>
                                        <th>Donor Name</th>
                                        <th>Contribution Category</th>
                                        <th>Amount</th>
                                        <th class="pe-3">Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($donations as $don): ?>
                                        <tr>
                                            <td class="ps-3"><span class="font-monospace text-muted"><?php echo sn_e($don['receipt']); ?></span></td>
                                            <td><span class="fw-semibold text-dark"><?php echo sn_e($don['donor']); ?></span></td>
                                            <td><span class="text-dark"><?php echo sn_e($don['category']); ?></span></td>
                                            <td><span class="text-success fw-bold"><?php echo sn_e($don['amount']); ?></span></td>
                                            <td class="pe-3"><span class="text-dark"><?php echo sn_e($don['date']); ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</main>

<!-- Invoice Modals -->
<?php foreach ($payments as $p): ?>
    <div class="modal fade" id="invoiceModal_<?php echo sn_e($p['invoice']); ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <div class="modal-header border-light">
                    <h5 class="modal-title fw-bold text-dark">Invoice Summary</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    <!-- Invoice Sheet -->
                    <div class="card border-0 rounded-3 p-4 shadow-sm bg-white font-monospace" style="font-size: 0.85rem; color: #333;">
                        <div class="text-center mb-3">
                            <h5 class="fw-bold mb-0">SEVANEST OLD AGE HOME</h5>
                            <small class="text-muted">Receipt / Tax Invoice</small>
                        </div>
                        <hr class="border-secondary my-2">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Invoice ID:</span>
                            <span class="fw-bold"><?php echo sn_e($p['invoice']); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span>Resident Name:</span>
                            <span class="fw-bold"><?php echo sn_e($p['payer']); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span>Fee Head:</span>
                            <span class="fw-bold"><?php echo sn_e($p['type']); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Date Cleared:</span>
                            <span class="fw-bold"><?php echo sn_e($p['date']); ?></span>
                        </div>
                        <hr class="border-secondary my-2">
                        <div class="d-flex justify-content-between fs-6 fw-bold text-dark pt-2">
                            <span>TOTAL AMOUNT CLEARED:</span>
                            <span><?php echo sn_e($p['amount']); ?></span>
                        </div>
                        <hr class="border-secondary my-2">
                        <div class="text-center mt-3 text-success">
                            <strong>★★★ PAYMENT RECEIVED — THANK YOU ★★★</strong>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-light">
                    <button type="button" class="btn btn-sm btn-secondary fw-semibold" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-sm btn-primary fw-semibold" onclick="window.print()"><i class="bi bi-printer me-1"></i> Print Invoice</button>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
