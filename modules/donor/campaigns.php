<?php
/**
 * SevaNest – Donor Campaigns
 * File     : modules/donor/campaigns.php
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

$formSuccess = '';
$formError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'donate') {
        $amount = (float)($_POST['amount'] ?? 0);
        $type = $_POST['contribution_type'] ?? 'donation';
        $purpose = trim($_POST['purpose'] ?? 'General Welfare Fund');
        
        if ($amount <= 0) {
            $formError = 'Please enter a valid amount.';
        } else {
            try {
                if ($type === 'pledge') {
                    $target_date = $_POST['target_date'] ?? date('Y-m-d', strtotime('+30 days'));
                    $notes = trim($_POST['notes'] ?? '');
                    if (empty($notes)) {
                        $notes = "Pledge for campaign: " . $purpose;
                    }
                    $stmt = $pdo->prepare("INSERT INTO pledges (donor_id, amount, target_date, status, notes) VALUES (?, ?, ?, 'Pending', ?)");
                    $stmt->execute([$donor_id, $amount, $target_date, $notes]);

                    // Log notification
                    $notif_stmt = $pdo->prepare("INSERT INTO notifications (user_id, title, message) VALUES (?, ?, ?)");
                    $notif_stmt->execute([$donor_id, 'New Pledge Recorded', 'You recorded a pledge of $' . number_format($amount, 2) . ' targeting ' . $target_date]);
                    
                    $formSuccess = 'Pledge recorded successfully! Thank you for committing $' . number_format($amount, 2) . ' to "' . $purpose . '".';
                } else {
                    $payment_method = $_POST['payment_method'] ?? 'Card';
                    $transaction_id = trim($_POST['transaction_id'] ?? '');
                    $receipt_number = 'REC-' . rand(100000, 999999);
                    
                    $stmt = $pdo->prepare("INSERT INTO donations (donor_id, amount, payment_method, transaction_id, purpose, donation_date, receipt_number) VALUES (?, ?, ?, ?, ?, NOW(), ?)");
                    $stmt->execute([$donor_id, $amount, $payment_method, $transaction_id, $purpose, $receipt_number]);
                    
                    // Log activity or notification
                    $notif_stmt = $pdo->prepare("INSERT INTO notifications (user_id, title, message) VALUES (?, ?, ?)");
                    $notif_stmt->execute([$donor_id, 'Donation Recorded', 'Thank you for your donation of $' . number_format($amount, 2) . ' to ' . $purpose]);
                    
                    $formSuccess = 'Thank you! Your donation of $' . number_format($amount, 2) . ' to "' . $purpose . '" has been recorded.';
                }
            } catch (Exception $e) {
                $formError = 'Database error: ' . $e->getMessage();
            }
        }
    }
}

// Fetch dynamic raised amounts for active campaigns
$raised_icu = (float)$pdo->query("SELECT SUM(amount) FROM donations WHERE purpose = 'Senior ICU Facility Setup'")->fetchColumn();
$raised_winter = (float)$pdo->query("SELECT SUM(amount) FROM donations WHERE purpose = 'Winter Clothing Drive'")->fetchColumn();
$raised_diet = (float)$pdo->query("SELECT SUM(amount) FROM donations WHERE purpose = 'Nutrition Diet Enhancement'")->fetchColumn();

$base_path = '../../';
$page_title = 'Active Campaigns | SevaNest';
$extra_css = [
    'assets/css/donor.css'
];

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'donor';
$currentPage   = 'campaigns.php';
$sn_asset_root = "../../assets";
$base_path = '../../';
include '../../includes/sidebar.php';
?>

<!-- ═══════════════════════════════════════════════════════════════════════
     MAIN CONTENT AREA
     ═══════════════════════════════════════════════════════════════════════ -->
<main id="sn-main-content" role="main" aria-label="Donor campaigns content">
    <div class="donor-page-wrapper">
        
        <!-- Header Strip -->
        <div class="dn-header-strip animate-fade-in">
            <div>
                <h2 class="dn-header-strip__title">Active Support Campaigns</h2>
                <p style="color: var(--color-text-muted-team); margin: 4px 0 0;">Contribute to ongoing programs tailored to optimize residents' standard of life.</p>
            </div>
        </div>

        <!-- Campaign Cards Grid -->
        <div class="campaign-grid animate-fade-in">
            
            <!-- Campaign 1 -->
            <div class="campaign-card">
                <div class="camp-img-placeholder">🏥</div>
                <div class="camp-body">
                    <h4 class="camp-title">Senior ICU Facility Setup</h4>
                    <p class="camp-desc">Establishing a 4-bed ICU setup inside SevaNest to deal with critical cardiovascular and breathing failures in-house.</p>
                    <div class="camp-progress-container">
                        <?php $pct_icu = min(100, round(($raised_icu / 25000) * 100)); ?>
                        <div class="camp-progress-bar">
                            <div class="camp-progress-fill" style="width: <?php echo $pct_icu; ?>%;"></div>
                        </div>
                        <div class="camp-stats-row">
                            <span>Raised: $<?php echo number_format($raised_icu, 2); ?></span>
                            <span>Goal: $25,000</span>
                        </div>
                    </div>
                </div>
                <div class="camp-footer">
                    <span class="camp-days-left">14 Days Left</span>
                    <div>
                        <button class="btn btn-outline-primary btn-tiny me-1 btn-details-trigger" data-purpose="Senior ICU Facility Setup" data-desc="Establishing a 4-bed ICU setup inside SevaNest to deal with critical cardiovascular and breathing failures in-house.">Details</button>
                        <button class="btn btn-primary btn-tiny btn-donate-trigger" data-purpose="Senior ICU Facility Setup">Donate</button>
                    </div>
                </div>
            </div>

            <!-- Campaign 2 -->
            <div class="campaign-card">
                <div class="camp-img-placeholder">🧥</div>
                <div class="camp-body">
                    <h4 class="camp-title">Winter Clothing &amp; Blankets</h4>
                    <p class="camp-desc">Procuring winter coats, heavy thermal sheets, and indoor heaters for all resident rooms ahead of the winter season.</p>
                    <div class="camp-progress-container">
                        <?php $pct_winter = min(100, round(($raised_winter / 5000) * 100)); ?>
                        <div class="camp-progress-bar">
                            <div class="camp-progress-fill" style="width: <?php echo $pct_winter; ?>%;"></div>
                        </div>
                        <div class="camp-stats-row">
                            <span>Raised: $<?php echo number_format($raised_winter, 2); ?></span>
                            <span>Goal: $5,000</span>
                        </div>
                    </div>
                </div>
                <div class="camp-footer">
                    <span class="camp-days-left">8 Days Left</span>
                    <div>
                        <button class="btn btn-outline-primary btn-tiny me-1 btn-details-trigger" data-purpose="Winter Clothing Drive" data-desc="Procuring winter coats, heavy thermal sheets, and indoor heaters for all resident rooms ahead of the winter season.">Details</button>
                        <button class="btn btn-primary btn-tiny btn-donate-trigger" data-purpose="Winter Clothing Drive">Donate</button>
                    </div>
                </div>
            </div>

            <!-- Campaign 3 -->
            <div class="campaign-card">
                <div class="camp-img-placeholder">🥦</div>
                <div class="camp-body">
                    <h4 class="camp-title">Nutrition Diet Enhancement</h4>
                    <p class="camp-desc">Providing diabetic-safe low sodium food choices, organic supplements, and fresh fruit arrays daily.</p>
                    <div class="camp-progress-container">
                        <?php $pct_diet = min(100, round(($raised_diet / 1000) * 100)); ?>
                        <div class="camp-progress-bar">
                            <div class="camp-progress-fill" style="width: <?php echo $pct_diet; ?>%;"></div>
                        </div>
                        <div class="camp-stats-row">
                            <span>Raised: $<?php echo number_format($raised_diet, 2); ?></span>
                            <span>Goal: $1,000</span>
                        </div>
                    </div>
                </div>
                <div class="camp-footer">
                    <span class="camp-days-left">30 Days Left</span>
                    <div>
                        <button class="btn btn-outline-primary btn-tiny me-1 btn-details-trigger" data-purpose="Nutrition Diet Enhancement" data-desc="Providing diabetic-safe low sodium food choices, organic supplements, and fresh fruit arrays daily.">Details</button>
                        <button class="btn btn-primary btn-tiny btn-donate-trigger" data-purpose="Nutrition Diet Enhancement">Donate</button>
                    </div>
                </div>
            </div>

        </div>

    </div>
</main>

<!-- Make Donation Modal -->
<div class="modal fade" id="donateModal" tabindex="-1" aria-labelledby="donateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <form method="POST" action="campaigns.php">
                <input type="hidden" name="action" value="donate">
                <input type="hidden" name="purpose" id="donatePurpose">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="donateModalLabel">Make a Donation or Pledge</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Campaign</label>
                        <input type="text" id="donatePurposeDisplay" class="form-control select" disabled style="width:100%; border: 1px solid var(--color-border); border-radius: var(--radius-medium); padding: 8px; background: #eef2f0;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Contribution Type</label>
                        <select name="contribution_type" id="contributionType" class="form-select select" style="width:100%; border: 1px solid var(--color-border); border-radius: var(--radius-medium); padding: 8px;">
                            <option value="donation">Immediate Donation</option>
                            <option value="pledge">Record Future Pledge</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Amount ($) <span class="text-danger">*</span></label>
                        <input type="number" step="1" name="amount" class="form-control select" required style="width:100%; border: 1px solid var(--color-border); border-radius: var(--radius-medium); padding: 8px;" placeholder="e.g. 250" min="1">
                    </div>
                    
                    <!-- Immediate Donation Fields -->
                    <div id="donationFields">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Payment Method <span class="text-danger">*</span></label>
                            <select name="payment_method" class="form-select select" style="width:100%; border: 1px solid var(--color-border); border-radius: var(--radius-medium); padding: 8px;">
                                <option value="Card">Credit/Debit Card</option>
                                <option value="UPI">UPI</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="Cash">Cash</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Transaction Reference ID</label>
                            <input type="text" name="transaction_id" class="form-control select" placeholder="e.g. TXN1029384" style="width:100%; border: 1px solid var(--color-border); border-radius: var(--radius-medium); padding: 8px;">
                        </div>
                    </div>

                    <!-- Pledge Fields -->
                    <div id="pledgeFields" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Target Date <span class="text-danger">*</span></label>
                            <input type="date" name="target_date" class="form-control select" style="width:100%; border: 1px solid var(--color-border); border-radius: var(--radius-medium); padding: 8px;" value="<?php echo date('Y-m-d', strtotime('+30 days')); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Pledge Notes / Details</label>
                            <textarea name="notes" class="form-control select" rows="2" style="width:100%; border: 1px solid var(--color-border); border-radius: var(--radius-medium); padding: 8px;" placeholder="Additional details..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-sm">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if ($formSuccess): ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Thank You!',
                    text: <?php echo json_encode($formSuccess); ?>,
                    confirmButtonColor: '#2b4c3f'
                });
            } else {
                alert(<?php echo json_encode($formSuccess); ?>);
            }
        });
    </script>
<?php endif; ?>
<?php if ($formError): ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: <?php echo json_encode($formError); ?>,
                    confirmButtonColor: '#2b4c3f'
                });
            } else {
                alert(<?php echo json_encode($formError); ?>);
            }
        });
    </script>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Toggle fields based on contribution type
    const contributionType = document.getElementById('contributionType');
    const donationFields = document.getElementById('donationFields');
    const pledgeFields = document.getElementById('pledgeFields');

    if (contributionType) {
        contributionType.addEventListener('change', () => {
            if (contributionType.value === 'pledge') {
                donationFields.style.display = 'none';
                pledgeFields.style.display = 'block';
            } else {
                donationFields.style.display = 'block';
                pledgeFields.style.display = 'none';
            }
        });
    }

    // Donate modal trigger
    document.querySelectorAll('.btn-donate-trigger').forEach(btn => {
        btn.addEventListener('click', () => {
            const purpose = btn.getAttribute('data-purpose');
            document.getElementById('donatePurpose').value = purpose;
            document.getElementById('donatePurposeDisplay').value = purpose;
            
            const myModal = new bootstrap.Modal(document.getElementById('donateModal'));
            myModal.show();
        });
    });

    // Details alert trigger
    document.querySelectorAll('.btn-details-trigger').forEach(btn => {
        btn.addEventListener('click', () => {
            const title = btn.getAttribute('data-purpose');
            const desc = btn.getAttribute('data-desc');
            
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: title,
                    text: desc,
                    icon: 'info',
                    confirmButtonColor: '#2b4c3f'
                });
            } else {
                alert(`${title}\n\n${desc}`);
            }
        });
    });
});
</script>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
