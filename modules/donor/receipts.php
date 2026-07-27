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
                        <tr>
                            <td><b>#REC-789021</b></td>
                            <td>24 Jul 2026</td>
                            <td><strong>$250.00</strong></td>
                            <td><span class="badge green">Exempted (80G)</span></td>
                            <td>
                                <button class="btn btn-outline-primary btn-tiny me-1"><i class="bi bi-file-earmark-pdf"></i> Download PDF</button>
                                <button class="btn btn-outline-secondary btn-tiny"><i class="bi bi-printer"></i> Print</button>
                            </td>
                        </tr>
                        <tr>
                            <td><b>#REC-789012</b></td>
                            <td>15 Jul 2026</td>
                            <td><strong>$500.00</strong></td>
                            <td><span class="badge green">Exempted (80G)</span></td>
                            <td>
                                <button class="btn btn-outline-primary btn-tiny me-1"><i class="bi bi-file-earmark-pdf"></i> Download PDF</button>
                                <button class="btn btn-outline-secondary btn-tiny"><i class="bi bi-printer"></i> Print</button>
                            </td>
                        </tr>
                        <tr>
                            <td><b>#REC-788998</b></td>
                            <td>02 Jun 2026</td>
                            <td><strong>$1,500.00</strong></td>
                            <td><span class="badge green">Exempted (80G)</span></td>
                            <td>
                                <button class="btn btn-outline-primary btn-tiny me-1"><i class="bi bi-file-earmark-pdf"></i> Download PDF</button>
                                <button class="btn btn-outline-secondary btn-tiny"><i class="bi bi-printer"></i> Print</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</main>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
