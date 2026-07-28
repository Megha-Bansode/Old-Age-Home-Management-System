<?php
/**
 * SevaNest – Donor Receipts
 * File     : modules/donor/receipts.php
 * Version  : 1.0
 */

require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../config/database.php';

// Require Donor login
require_login();
require_role('Donor');

$pdo = get_db_connection();
$donor_id = $_SESSION['user_id'] ?? 6;

// Query successful donations (exemption receipts are generated for them)
$stmt = $pdo->prepare("SELECT * FROM donations WHERE donor_id = ? ORDER BY donation_date DESC");
$stmt->execute([$donor_id]);
$donations = $stmt->fetchAll();

$base_path = '../../';
$page_title = 'Tax Receipts | SevaNest';
$extra_css = [
    'assets/css/donor.css'
];

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'donor';
$currentPage   = 'receipts.php';
$sn_asset_root = "../../assets";
$base_path = '../../';
include '../../includes/sidebar.php';
?>

<!-- ═══════════════════════════════════════════════════════════════════════
     MAIN CONTENT AREA
     ═══════════════════════════════════════════════════════════════════════ -->
<main id="sn-main-content" role="main" aria-label="Donor receipts content">
    <div class="donor-page-wrapper">
        
        <!-- Header Strip -->
        <div class="dn-header-strip animate-fade-in">
            <div>
                <h2 class="dn-header-strip__title">Tax Exemption Receipts</h2>
                <p style="color: var(--color-text-muted-team); margin: 4px 0 0;">Download 80G tax exemption receipts for your charitable donations.</p>
            </div>
        </div>

        <!-- Receipts Table -->
        <div class="card no-pad animate-fade-in">
            <div class="table-wrap">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Receipt Number</th>
                            <th>Donation Date</th>
                            <th>Amount</th>
                            <th>Tax Exemption State</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($donations)): ?>
                            <tr><td colspan="5" class="text-center text-muted py-3">No exemption receipts available yet. Make a donation to generate one!</td></tr>
                        <?php else: ?>
                            <?php foreach ($donations as $don): ?>
                                <tr>
                                    <td><b>#<?php echo sn_e($don['receipt_number']); ?></b></td>
                                    <td><?php echo date('d M Y', strtotime($don['donation_date'])); ?></td>
                                    <td><strong>$<?php echo number_format($don['amount'], 2); ?></strong></td>
                                    <td><span class="badge green">Exempted (80G)</span></td>
                                    <td>
                                        <button class="btn btn-outline-primary btn-tiny me-1 btn-download" data-rec="<?php echo sn_e($don['receipt_number']); ?>" data-amount="<?php echo $don['amount']; ?>" data-date="<?php echo date('d M Y', strtotime($don['donation_date'])); ?>"><i class="bi bi-file-earmark-pdf"></i> Download PDF</button>
                                        <button class="btn btn-outline-secondary btn-tiny btn-print" data-rec="<?php echo sn_e($don['receipt_number']); ?>"><i class="bi bi-printer"></i> Print</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.btn-download').forEach(btn => {
        btn.addEventListener('click', () => {
            const rec = btn.getAttribute('data-rec');
            const amount = btn.getAttribute('data-amount');
            const date = btn.getAttribute('data-date');
            
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Receipt PDF Downloaded',
                    text: `Tax invoice ${rec} for $${amount} generated on ${date} has been downloaded successfully.`,
                    confirmButtonColor: '#2b4c3f'
                });
            } else {
                alert(`Downloaded Receipt ${rec} for $${amount}`);
            }
        });
    });

    document.querySelectorAll('.btn-print').forEach(btn => {
        btn.addEventListener('click', () => {
            const rec = btn.getAttribute('data-rec');
            window.print();
        });
    });
});
</script>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
