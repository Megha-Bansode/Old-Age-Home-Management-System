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

// Database Connection
require_once __DIR__ . '/../../config/database.php';
$pdo = get_db_connection();

$formSuccess = '';
$formError = '';

// Programmatically seed mock data on first run
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM donations");
    if ($stmt->fetchColumn() == 0) {
        $mocks = [
            ['INV-9081', 15000, 'Cash', '[Fee:Paid:Kamala Devi] Maintenance Fee', '2026-07-05 10:00:00'],
            ['INV-9082', 18000, 'Card', '[Fee:Paid:Harish Mehta] Special Care Fee', '2026-07-04 11:30:00'],
            ['INV-9083', 12000, 'UPI', '[Fee:Paid:Gopal Prasad] Maintenance Fee', '2026-07-02 14:15:00'],
            ['INV-9084', 15000, 'Cash', '[Fee:Pending:Devaki Amma] Maintenance Fee', '2026-08-01 00:00:00'],
            ['INV-9085', 8500, 'UPI', '[Fee:Overdue:Savitri Bai] Special Care Fee', '2026-07-20 00:00:00'],
            ['REC-4011', 150000, 'Bank Transfer', '[Donation:Vikramaditya Mehta] Medical Equipment', '2026-07-25 09:00:00'],
            ['REC-4012', 200000, 'Bank Transfer', '[Donation:Sudha Murthy Foundation] Nutritional Meals', '2026-07-20 15:45:00'],
            ['REC-4013', 75000, 'UPI', '[Donation:Anil Kapoor] General Fund', '2026-07-15 12:20:00']
        ];
        
        foreach ($mocks as $m) {
            $stmt_ins = $pdo->prepare("INSERT INTO donations (receipt_number, amount, payment_method, purpose, donation_date) VALUES (?, ?, ?, ?, ?)");
            $stmt_ins->execute([$m[0], $m[1], $m[2], $m[3], $m[4]]);
        }
    }
} catch (Exception $e) {
    // Fail silently
}

// Parser Helper
if (!function_exists('parse_finance_records')) {
    function parse_finance_records($pdo) {
        $stmt = $pdo->query("SELECT * FROM donations ORDER BY donation_id DESC");
        $rows = $stmt->fetchAll();
        
        $payments = [];
        $pendingPayments = [];
        $donations = [];
        
        foreach ($rows as $row) {
            $purpose = $row['purpose'] ?? '';
            
            if (strpos($purpose, '[Fee:') === 0) {
                if (preg_match('/^\[Fee:(.*?):(.*?)\]\s*(.*)$/', $purpose, $matches)) {
                    $status = $matches[1];
                    $payer_name = $matches[2];
                    $fee_type = $matches[3];
                } else {
                    $status = 'Paid';
                    $payer_name = 'Anonymous';
                    $fee_type = 'Maintenance Fee';
                }
                
                if ($status === 'Paid') {
                    $payments[] = [
                        'invoice' => $row['receipt_number'],
                        'payer' => $payer_name,
                        'amount' => '₹' . number_format($row['amount']),
                        'date' => date('Y-m-d', strtotime($row['donation_date'])),
                        'status' => 'Paid',
                        'type' => $fee_type
                    ];
                } else {
                    $pendingPayments[] = [
                        'invoice' => $row['receipt_number'],
                        'payer' => $payer_name,
                        'amount' => '₹' . number_format($row['amount']),
                        'due_date' => date('Y-m-d', strtotime($row['donation_date'])),
                        'status' => $status
                    ];
                }
            } else {
                if (preg_match('/^\[Donation:(.*?)\]\s*(.*)$/', $purpose, $matches)) {
                    $donor_name = $matches[1];
                    $category = $matches[2];
                } else {
                    $donor_name = 'Anonymous';
                    $category = $purpose ?: 'General Fund';
                }
                
                $donations[] = [
                    'receipt' => $row['receipt_number'],
                    'donor' => $donor_name,
                    'amount' => '₹' . number_format($row['amount']),
                    'category' => $category,
                    'date' => date('Y-m-d', strtotime($row['donation_date']))
                ];
            }
        }
        
        return [
            'payments' => $payments,
            'pendingPayments' => $pendingPayments,
            'donations' => $donations
        ];
    }
}

// Rendering Helpers
if (!function_exists('render_history_tab')) {
    function render_history_tab($payments) {
        ob_start();
        if (empty($payments)) {
            echo '<tr><td colspan="7" class="text-center py-4 text-muted">No payment records found.</td></tr>';
        } else {
            foreach ($payments as $p) {
                ?>
                <tr>
                    <td class="ps-3"><span class="font-monospace text-muted"><?php echo sn_e($p['invoice']); ?></span></td>
                    <td><span class="fw-semibold text-dark"><?php echo sn_e($p['payer']); ?></span></td>
                    <td><span class="text-dark"><?php echo sn_e($p['type']); ?></span></td>
                    <td><span class="text-dark fw-bold"><?php echo sn_e($p['amount']); ?></span></td>
                    <td><span class="text-dark"><?php echo sn_e($p['date']); ?></span></td>
                    <td><span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1"><?php echo sn_e($p['status']); ?></span></td>
                    <td class="pe-3 text-end">
                        <button class="btn btn-sm btn-light text-primary btn-view-invoice" data-invoice="<?php echo sn_e($p['invoice']); ?>" data-payer="<?php echo sn_e($p['payer']); ?>" data-type="<?php echo sn_e($p['type']); ?>" data-date="<?php echo sn_e($p['date']); ?>" data-amount="<?php echo sn_e($p['amount']); ?>">
                            <i class="bi bi-file-earmark-text-fill"></i> View
                        </button>
                    </td>
                </tr>
                <?php
            }
        }
        return ob_get_clean();
    }
}

if (!function_exists('render_pending_tab')) {
    function render_pending_tab($pendingPayments) {
        ob_start();
        if (empty($pendingPayments)) {
            echo '<tr><td colspan="6" class="text-center py-4 text-muted">No pending invoices found.</td></tr>';
        } else {
            foreach ($pendingPayments as $pp) {
                $badge = ($pp['status'] === 'Overdue') ? 'danger' : 'warning';
                ?>
                <tr>
                    <td class="ps-3"><span class="font-monospace text-muted"><?php echo sn_e($pp['invoice']); ?></span></td>
                    <td><span class="fw-semibold text-dark"><?php echo sn_e($pp['payer']); ?></span></td>
                    <td><span class="text-dark fw-bold"><?php echo sn_e($pp['amount']); ?></span></td>
                    <td><span class="text-dark"><?php echo sn_e($pp['due_date']); ?></span></td>
                    <td><span class="badge bg-<?php echo $badge; ?>-subtle text-<?php echo $badge; ?> rounded-pill px-2.5 py-1"><?php echo sn_e($pp['status']); ?></span></td>
                    <td class="pe-3 text-end">
                        <form method="POST" action="payments.php" class="d-inline record-payment-form">
                            <input type="hidden" name="action" value="record_payment">
                            <input type="hidden" name="invoice" value="<?php echo sn_e($pp['invoice']); ?>">
                            <button type="submit" class="btn btn-sm btn-primary fw-semibold py-1">Record Payment</button>
                        </form>
                    </td>
                </tr>
                <?php
            }
        }
        return ob_get_clean();
    }
}

if (!function_exists('render_donations_tab')) {
    function render_donations_tab($donations) {
        ob_start();
        if (empty($donations)) {
            echo '<tr><td colspan="5" class="text-center py-4 text-muted">No donations logged yet.</td></tr>';
        } else {
            foreach ($donations as $don) {
                ?>
                <tr>
                    <td class="ps-3"><span class="font-monospace text-muted"><?php echo sn_e($don['receipt']); ?></span></td>
                    <td><span class="fw-semibold text-dark"><?php echo sn_e($don['donor']); ?></span></td>
                    <td><span class="text-dark"><?php echo sn_e($don['category']); ?></span></td>
                    <td><span class="text-success fw-bold"><?php echo sn_e($don['amount']); ?></span></td>
                    <td class="pe-3"><span class="text-dark"><?php echo sn_e($don['date']); ?></span></td>
                </tr>
                <?php
            }
        }
        return ob_get_clean();
    }
}

if (!function_exists('render_modals')) {
    function render_modals($payments) {
        ob_start();
        if (!empty($payments)) {
            foreach ($payments as $p) {
                ?>
                <div class="modal fade" id="invoiceModal_<?php echo sn_e($p['invoice']); ?>" tabindex="-1" aria-labelledby="invoiceModalLabel_<?php echo sn_e($p['invoice']); ?>" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow-lg rounded-3">
                            <div class="modal-header border-light">
                                <h5 class="modal-title fw-bold" id="invoiceModalLabel_<?php echo sn_e($p['invoice']); ?>">Invoice Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4">
                                <div class="text-center mb-4">
                                    <div class="fs-4 text-success"><i class="bi bi-patch-check-fill"></i></div>
                                    <h3 class="fw-bold mb-0 mt-2 text-dark"><?php echo sn_e($p['amount']); ?></h3>
                                    <span class="badge bg-success-subtle text-success rounded-pill mt-1">Paid</span>
                                </div>
                                <div class="row g-3">
                                    <div class="col-6 text-muted">Invoice No:</div>
                                    <div class="col-6 text-end fw-semibold text-dark"><?php echo sn_e($p['invoice']); ?></div>
                                    <div class="col-6 text-muted">Payer:</div>
                                    <div class="col-6 text-end fw-semibold text-dark"><?php echo sn_e($p['payer']); ?></div>
                                    <div class="col-6 text-muted">Fee Type:</div>
                                    <div class="col-6 text-end fw-semibold text-dark"><?php echo sn_e($p['type']); ?></div>
                                    <div class="col-6 text-muted">Payment Date:</div>
                                    <div class="col-6 text-end fw-semibold text-dark"><?php echo sn_e($p['date']); ?></div>
                                </div>
                            </div>
                            <div class="modal-footer border-light">
                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary btn-sm" onclick="window.print();">Print</button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
        }
        return ob_get_clean();
    }
}

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $is_ajax = (isset($_POST['ajax']) && $_POST['ajax'] == '1');

    if ($action === 'record_payment') {
        $invoice = trim($_POST['invoice'] ?? '');
        try {
            $stmt = $pdo->prepare("SELECT * FROM donations WHERE receipt_number = ?");
            $stmt->execute([$invoice]);
            $row = $stmt->fetch();
            
            if ($row) {
                $purpose = $row['purpose'];
                $new_purpose = preg_replace('/^\[Fee:(Pending|Overdue):/', '[Fee:Paid:', $purpose);
                
                $stmt_upd = $pdo->prepare("UPDATE donations SET purpose = ?, donation_date = NOW() WHERE receipt_number = ?");
                $stmt_upd->execute([$new_purpose, $invoice]);
                
                $msg = "Payment for invoice $invoice recorded successfully!";
                if ($is_ajax) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true, 'message' => $msg]);
                    exit;
                }
                $formSuccess = $msg;
            } else {
                throw new Exception("Invoice $invoice not found.");
            }
        } catch (Exception $e) {
            $msg = $e->getMessage();
            if ($is_ajax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $msg]);
                exit;
            }
            $formError = $msg;
        }
    }
}

// Fetch stats and lists
$all_records = parse_finance_records($pdo);
$payments = $all_records['payments'];
$pendingPayments = $all_records['pendingPayments'];
$donations = $all_records['donations'];

// Calculate stats sums
$total_donations_sum = 0;
$fees_collected_sum = 0;
$outstanding_dues_sum = 0;
$pending_invoices_count = 0;

foreach ($donations as $don) {
    $total_donations_sum += (float)str_replace(['₹', ','], '', $don['amount']);
}
foreach ($payments as $p) {
    $fees_collected_sum += (float)str_replace(['₹', ','], '', $p['amount']);
}
foreach ($pendingPayments as $pp) {
    $outstanding_dues_sum += (float)str_replace(['₹', ','], '', $pp['amount']);
    $pending_invoices_count++;
}

// Handle AJAX Fetch Request
if (isset($_GET['action']) && $_GET['action'] === 'fetch') {
    header('Content-Type: application/json');
    echo json_encode([
        'html_history' => render_history_tab($payments),
        'html_pending' => render_pending_tab($pendingPayments),
        'html_donations' => render_donations_tab($donations),
        'html_modals' => render_modals($payments),
        'total_donations' => '₹' . number_format($total_donations_sum),
        'fees_collected' => '₹' . number_format($fees_collected_sum),
        'outstanding_dues' => '₹' . number_format($outstanding_dues_sum),
        'outstanding_count' => $pending_invoices_count . ' invoice' . ($pending_invoices_count == 1 ? '' : 's') . ' unpaid'
    ]);
    exit;
}

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'admin';
$currentPage   = 'payments.php';
$sn_asset_root = "../../assets";
$base_path = '../../'; // Ensure correct path prefix
include '../../includes/sidebar.php';
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
                    <h4 class="fw-bold mb-0 text-success" id="card-total-donations">₹<?php echo number_format($total_donations_sum); ?></h4>
                    <small class="text-muted">This month</small>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-4 border-primary">
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.75rem;">Fees Collected</span>
                    <h4 class="fw-bold mb-0 text-primary" id="card-fees-collected">₹<?php echo number_format($fees_collected_sum); ?></h4>
                    <small class="text-muted">Total maintenance fees</small>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-4 border-danger">
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.75rem;">Outstanding Dues</span>
                    <h4 class="fw-bold mb-0 text-danger" id="card-outstanding-dues">₹<?php echo number_format($outstanding_dues_sum); ?></h4>
                    <small class="text-danger-emphasis" id="card-outstanding-count"><?php echo $pending_invoices_count; ?> invoice<?php echo $pending_invoices_count == 1 ? '' : 's'; ?> unpaid</small>
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
                                <tbody id="table-history-body">
                                    <?php echo render_history_tab($payments); ?>
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
                                <tbody id="table-pending-body">
                                    <?php echo render_pending_tab($pendingPayments); ?>
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
                                <tbody id="table-donations-body">
                                    <?php echo render_donations_tab($donations); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</main>

<!-- Invoice Modals Wrapper -->
<div id="invoice-modals-container">
    <?php echo render_modals($payments); ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const tablePending = document.getElementById('table-pending-body');
    const tableHistory = document.getElementById('table-history-body');
    const tableDonations = document.getElementById('table-donations-body');
    const modalsContainer = document.getElementById('invoice-modals-container');

    const cardDonations = document.getElementById('card-total-donations');
    const cardCollected = document.getElementById('card-fees-collected');
    const cardOutstanding = document.getElementById('card-outstanding-dues');
    const cardOutstandingCount = document.getElementById('card-outstanding-count');

    function refreshDashboard() {
        fetch('payments.php?action=fetch')
        .then(res => res.json())
        .then(data => {
            if (tablePending) tablePending.innerHTML = data.html_pending;
            if (tableHistory) tableHistory.innerHTML = data.html_history;
            if (tableDonations) tableDonations.innerHTML = data.html_donations;
            if (modalsContainer) modalsContainer.innerHTML = data.html_modals;

            if (cardDonations) cardDonations.textContent = data.total_donations;
            if (cardCollected) cardCollected.textContent = data.fees_collected;
            if (cardOutstanding) cardOutstanding.textContent = data.outstanding_dues;
            if (cardOutstandingCount) cardOutstandingCount.textContent = data.outstanding_count;
        })
        .catch(err => {
            console.error('Error refreshing billing portal:', err);
        });
    }

    // Intercept Record Payment form submissions using event delegation
    document.addEventListener('submit', (e) => {
        const form = e.target.closest('.record-payment-form');
        if (form) {
            e.preventDefault();
            const formData = new FormData(form);
            formData.append('ajax', '1');

            fetch('payments.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: data.message,
                            confirmButtonColor: '#2b4c3f'
                        });
                    }
                    refreshDashboard();
                } else {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message,
                            confirmButtonColor: '#2b4c3f'
                        });
                    }
                }
            })
            .catch(err => {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while recording payment.',
                        confirmButtonColor: '#2b4c3f'
                    });
                }
            });
        }
    });

    // Delegate view invoice click
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-view-invoice');
        if (btn) {
            const invoice = btn.getAttribute('data-invoice');
            const myModal = new bootstrap.Modal(document.getElementById(`invoiceModal_${invoice}`));
            myModal.show();
        }
    });
});
</script>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
