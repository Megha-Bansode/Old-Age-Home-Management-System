<?php
/**
 * SevaNest — Family Member Billing Page
 * File     : modules/family/billing.php
 * Version  : 1.0
 */

require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../config/database.php';

// Require Family Member login
require_login();
require_role('Family Member');

$base_path = '../../';
$page_title = 'Billing & Payments | SevaNest';

// Mock payment trigger
$formSuccess = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formSuccess = 'Payment transaction processed successfully! Receipt generated.';
}

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'family_member';
$currentPage   = 'billing.php';
$sn_asset_root = "../../assets";
include '../../includes/sidebar.php';

// Mock billing datasets
$invoices = [
    ['no' => 'INV-9081', 'date' => '2026-07-05', 'type' => 'Maintenance Fee', 'amount' => '₹15,000', 'status' => 'Paid'],
    ['no' => 'INV-9079', 'date' => '2026-06-05', 'type' => 'Maintenance Fee', 'amount' => '₹15,000', 'status' => 'Paid'],
];

$medBills = [
    ['id' => 'MED-302', 'date' => '2026-07-15', 'desc' => 'Cardiology Checkup & Lab Tests', 'amount' => '₹3,500', 'status' => 'Paid'],
    ['id' => 'MED-301', 'date' => '2026-06-18', 'desc' => 'Monthly Medication Refill', 'amount' => '₹1,200', 'status' => 'Paid'],
];

$pending = [
    ['no' => 'INV-9084', 'due' => '2026-08-01', 'type' => 'Maintenance Fee (August)', 'amount' => '₹15,000', 'status' => 'Pending'],
];

$receipts = [
    ['receipt' => 'REC-7081', 'date' => '2026-07-05', 'amount' => '₹15,000', 'ref' => 'INV-9081'],
    ['receipt' => 'REC-7079', 'date' => '2026-06-05', 'amount' => '₹15,000', 'ref' => 'INV-9079'],
];
?>

<main id="sn-main-content" role="main" aria-label="Family Member billing content" class="p-4 flex-grow-1">
    <div class="container-fluid">

        <?php if ($formSuccess): ?>
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> <?php echo htmlspecialchars($formSuccess); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Page Header -->
        <div class="mb-4">
            <h3 class="fw-bold mb-0 text-dark">Billing &amp; Invoices</h3>
            <small class="text-muted">Review fee schedules, medical checkup invoices, pending dues, and download receipts for Kamala Devi</small>
        </div>

        <!-- Stat summaries -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-3">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-4 border-success">
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Total Amount Paid</span>
                    <h4 class="fw-bold mb-0 text-success">₹34,700</h4>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-4 border-warning">
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Outstanding Due</span>
                    <h4 class="fw-bold mb-0 text-warning">₹15,000</h4>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-4 border-primary">
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Next Due Date</span>
                    <h4 class="fw-bold mb-0 text-primary" style="font-size: 1.25rem; line-height: 1.6;">01 August 2026</h4>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-4 border-dark">
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Last Payment Cleared</span>
                    <h4 class="fw-bold mb-0 text-dark" style="font-size: 1.25rem; line-height: 1.6;">05 July 2026</h4>
                </div>
            </div>
        </div>

        <!-- Billing Tabs Card -->
        <div class="card border-0 shadow-sm rounded-3 bg-white">
            <div class="card-header bg-white border-bottom border-light p-3">
                <ul class="nav nav-tabs card-header-tabs" id="familyBillingTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold text-dark" id="invoices-tab" data-bs-toggle="tab" data-bs-target="#panel-invoices" type="button" role="tab">Maintenance Invoices</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold text-dark" id="medical-tab" data-bs-toggle="tab" data-bs-target="#panel-medical" type="button" role="tab">Medical Bills</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold text-dark" id="pending-tab" data-bs-toggle="tab" data-bs-target="#panel-pending" type="button" role="tab">Pending Payments</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold text-dark" id="receipts-tab" data-bs-toggle="tab" data-bs-target="#panel-receipts" type="button" role="tab">Receipts</button>
                    </li>
                </ul>
            </div>

            <div class="card-body p-0">
                <div class="tab-content" id="familyBillingTabsContent">
                    
                    <!-- Invoices Tab -->
                    <div class="tab-pane fade show active" id="panel-invoices" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                                <thead class="table-light text-muted text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.05em;">
                                    <tr>
                                        <th class="ps-3">Invoice ID</th>
                                        <th>Date</th>
                                        <th>Description</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th class="pe-3 text-end">Invoice Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($invoices as $inv): ?>
                                        <tr>
                                            <td class="ps-3"><span class="font-monospace text-muted"><?php echo htmlspecialchars($inv['no']); ?></span></td>
                                            <td><span class="text-dark"><?php echo htmlspecialchars($inv['date']); ?></span></td>
                                            <td><span class="text-dark"><?php echo htmlspecialchars($inv['type']); ?></span></td>
                                            <td><span class="text-dark fw-bold"><?php echo htmlspecialchars($inv['amount']); ?></span></td>
                                            <td><span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1"><?php echo htmlspecialchars($inv['status']); ?></span></td>
                                            <td class="pe-3 text-end">
                                                <button class="btn btn-sm btn-light text-primary" data-bs-toggle="modal" data-bs-target="#invoiceModal_<?php echo htmlspecialchars($inv['no']); ?>">
                                                    <i class="bi bi-file-earmark-text-fill"></i> View Invoice
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Medical Bills Tab -->
                    <div class="tab-pane fade" id="panel-medical" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                                <thead class="table-light text-muted text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.05em;">
                                    <tr>
                                        <th class="ps-3">Bill ID</th>
                                        <th>Date</th>
                                        <th>Medical Description</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th class="pe-3 text-end">Invoice</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($medBills as $mb): ?>
                                        <tr>
                                            <td class="ps-3"><span class="font-monospace text-muted"><?php echo htmlspecialchars($mb['id']); ?></span></td>
                                            <td><span class="text-dark"><?php echo htmlspecialchars($mb['date']); ?></span></td>
                                            <td><span class="text-dark"><?php echo htmlspecialchars($mb['desc']); ?></span></td>
                                            <td><span class="text-dark fw-bold"><?php echo htmlspecialchars($mb['amount']); ?></span></td>
                                            <td><span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1"><?php echo htmlspecialchars($mb['status']); ?></span></td>
                                            <td class="pe-3 text-end">
                                                <button class="btn btn-sm btn-light text-primary" data-bs-toggle="modal" data-bs-target="#medModal_<?php echo htmlspecialchars($mb['id']); ?>">
                                                    <i class="bi bi-file-earmark-text-fill"></i> View Details
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Pending Payments Tab -->
                    <div class="tab-pane fade" id="panel-pending" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                                <thead class="table-light text-muted text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.05em;">
                                    <tr>
                                        <th class="ps-3">Invoice ID</th>
                                        <th>Due Date</th>
                                        <th>Description</th>
                                        <th>Due Amount</th>
                                        <th>Status</th>
                                        <th class="pe-3 text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pending as $pend): ?>
                                        <tr>
                                            <td class="ps-3"><span class="font-monospace text-muted"><?php echo htmlspecialchars($pend['no']); ?></span></td>
                                            <td><span class="text-dark"><?php echo htmlspecialchars($pend['due']); ?></span></td>
                                            <td><span class="text-dark"><?php echo htmlspecialchars($pend['type']); ?></span></td>
                                            <td><span class="text-dark fw-bold"><?php echo htmlspecialchars($pend['amount']); ?></span></td>
                                            <td><span class="badge bg-warning-subtle text-warning rounded-pill px-2.5 py-1"><?php echo htmlspecialchars($pend['status']); ?></span></td>
                                            <td class="pe-3 text-end">
                                                <form method="POST" action="billing.php">
                                                    <button type="submit" class="btn btn-sm btn-primary fw-semibold py-1">Pay Outstanding</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Receipts Tab -->
                    <div class="tab-pane fade" id="panel-receipts" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                                <thead class="table-light text-muted text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.05em;">
                                    <tr>
                                        <th class="ps-3">Receipt ID</th>
                                        <th>Date Generated</th>
                                        <th>Reference ID</th>
                                        <th>Amount Paid</th>
                                        <th class="pe-3 text-end">PDF Copy</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($receipts as $rec): ?>
                                        <tr>
                                            <td class="ps-3"><span class="font-monospace text-muted"><?php echo htmlspecialchars($rec['receipt']); ?></span></td>
                                            <td><span class="text-dark"><?php echo htmlspecialchars($rec['date']); ?></span></td>
                                            <td><span class="font-monospace text-muted"><?php echo htmlspecialchars($rec['ref']); ?></span></td>
                                            <td><span class="text-success fw-bold"><?php echo htmlspecialchars($rec['amount']); ?></span></td>
                                            <td class="pe-3 text-end">
                                                <button class="btn btn-sm btn-light text-primary" onclick="window.print()">
                                                    <i class="bi bi-download"></i> Download PDF
                                                </button>
                                            </td>
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

<!-- Invoices Modals -->
<?php foreach ($invoices as $inv): ?>
    <div class="modal fade" id="invoiceModal_<?php echo htmlspecialchars($inv['no']); ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <div class="modal-header border-light">
                    <h5 class="modal-title fw-bold text-dark">Invoice Summary</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    <div class="card border-0 rounded-3 p-4 shadow-sm bg-white font-monospace" style="font-size: 0.85rem; color: #333;">
                        <div class="text-center mb-3">
                            <h5 class="fw-bold mb-0">SEVANEST OLD AGE HOME</h5>
                            <small class="text-muted">Monthly Maintenance Invoice</small>
                        </div>
                        <hr class="border-secondary my-2">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Invoice ID:</span>
                            <span class="fw-bold"><?php echo htmlspecialchars($inv['no']); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span>Resident Name:</span>
                            <span class="fw-bold">Kamala Devi</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span>Fee Type:</span>
                            <span class="fw-bold"><?php echo htmlspecialchars($inv['type']); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Date Paid:</span>
                            <span class="fw-bold"><?php echo htmlspecialchars($inv['date']); ?></span>
                        </div>
                        <hr class="border-secondary my-2">
                        <div class="d-flex justify-content-between fs-6 fw-bold text-dark pt-2">
                            <span>TOTAL AMOUNT CLEARED:</span>
                            <span><?php echo htmlspecialchars($inv['amount']); ?></span>
                        </div>
                        <hr class="border-secondary my-2">
                        <div class="text-center mt-3 text-success">
                            <strong>★★★ PAID ONLINE — THANK YOU ★★★</strong>
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

<!-- Medical Bill Modals -->
<?php foreach ($medBills as $mb): ?>
    <div class="modal fade" id="medModal_<?php echo htmlspecialchars($mb['id']); ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <div class="modal-header border-light">
                    <h5 class="modal-title fw-bold text-dark">Medical Receipt Summary</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    <div class="card border-0 rounded-3 p-4 shadow-sm bg-white font-monospace" style="font-size: 0.85rem; color: #333;">
                        <div class="text-center mb-3">
                            <h5 class="fw-bold mb-0">SEVANEST HEALTH CLINIC</h5>
                            <small class="text-muted">Medical Invoice Roster</small>
                        </div>
                        <hr class="border-secondary my-2">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Bill ID:</span>
                            <span class="fw-bold"><?php echo htmlspecialchars($mb['id']); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span>Resident:</span>
                            <span class="fw-bold">Kamala Devi</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span>Treatment:</span>
                            <span class="fw-bold"><?php echo htmlspecialchars($mb['desc']); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Date:</span>
                            <span class="fw-bold"><?php echo htmlspecialchars($mb['date']); ?></span>
                        </div>
                        <hr class="border-secondary my-2">
                        <div class="d-flex justify-content-between fs-6 fw-bold text-dark pt-2">
                            <span>AMOUNT PAID:</span>
                            <span><?php echo htmlspecialchars($mb['amount']); ?></span>
                        </div>
                        <hr class="border-secondary my-2">
                        <div class="text-center mt-3 text-success">
                            <strong>★★★ TRANSACTION APPROVED ★★★</strong>
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
