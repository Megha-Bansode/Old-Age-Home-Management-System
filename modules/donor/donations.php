<?php
/**
 * SevaNest – Donor Donation History
 * File     : modules/donor/donations.php
 * Version  : 1.0
 */

require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../config/database.php';

// Require Donor login
require_login();

$base_path = '../../';
$page_title = 'Donations | SevaNest';
$extra_css = [
    'assets/css/donor.css'
];

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'donor';
$currentPage   = 'donations.php';
$sn_asset_root = "../../assets";
include '../../includes/sidebar.php';
?>

<!-- ═══════════════════════════════════════════════════════════════════════
     MAIN CONTENT AREA
     ═══════════════════════════════════════════════════════════════════════ -->
<main id="sn-main-content" role="main" aria-label="Donor donations content">
    <div class="donor-page-wrapper">
        
        <!-- Header Strip -->
        <div class="dn-header-strip animate-fade-in">
            <div>
                <h2 class="dn-header-strip__title">My Donation History</h2>
                <p style="color: var(--color-text-muted-team); margin: 4px 0 0;">Review your contributions, track payment statuses and access tax receipts.</p>
            </div>
            <div>
                <a href="campaigns.php" class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>Make New Donation</a>
            </div>
        </div>

        <!-- Toolbar Filters -->
        <div class="toolbar">
            <div class="search inline">
                <span>🔎</span>
                <input placeholder="Search donations..." id="attSearch">
            </div>
            <select class="select">
                <option>All Types</option>
                <option>Campaign Specific</option>
                <option>General Fund</option>
                <option>Medical Support</option>
            </select>
            <select class="select">
                <option>All Statuses</option>
                <option>Successful</option>
                <option>Processing</option>
                <option>Failed</option>
            </select>
        </div>

        <!-- Donations Table -->
        <div class="card no-pad animate-fade-in">
            <div class="table-wrap">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Donation ID</th>
                            <th>Campaign/Category</th>
                            <th>Amount</th>
                            <th>Donation Type</th>
                            <th>Payment Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><b>#DON-90821</b></td>
                            <td>Winter Clothing Drive</td>
                            <td><strong>$250.00</strong></td>
                            <td>One-time</td>
                            <td><span class="badge green">Successful</span></td>
                            <td>
                                <a href="receipts.php" class="btn btn-outline-primary btn-tiny me-1"><i class="bi bi-file-earmark-pdf"></i> Receipt</a>
                                <button class="btn btn-outline-secondary btn-tiny"><i class="bi bi-eye"></i> Details</button>
                            </td>
                        </tr>
                        <tr>
                            <td><b>#DON-90765</b></td>
                            <td>General Welfare Fund</td>
                            <td><strong>$500.00</strong></td>
                            <td>Monthly Recurring</td>
                            <td><span class="badge green">Successful</span></td>
                            <td>
                                <a href="receipts.php" class="btn btn-outline-primary btn-tiny me-1"><i class="bi bi-file-earmark-pdf"></i> Receipt</a>
                                <button class="btn btn-outline-secondary btn-tiny"><i class="bi bi-eye"></i> Details</button>
                            </td>
                        </tr>
                        <tr>
                            <td><b>#DON-90123</b></td>
                            <td>Medical ICU Equipment</td>
                            <td><strong>$1,500.00</strong></td>
                            <td>One-time</td>
                            <td><span class="badge green">Successful</span></td>
                            <td>
                                <a href="receipts.php" class="btn btn-outline-primary btn-tiny me-1"><i class="bi bi-file-earmark-pdf"></i> Receipt</a>
                                <button class="btn btn-outline-secondary btn-tiny"><i class="bi bi-eye"></i> Details</button>
                            </td>
                        </tr>
                        <tr>
                            <td><b>#DON-89980</b></td>
                            <td>Nutritional Meal Enhancement</td>
                            <td><strong>$200.00</strong></td>
                            <td>One-time</td>
                            <td><span class="badge red">Failed</span></td>
                            <td>
                                <button class="btn btn-outline-primary btn-tiny me-1" disabled><i class="bi bi-file-earmark-pdf"></i> Receipt</button>
                                <button class="btn btn-outline-secondary btn-tiny"><i class="bi bi-eye"></i> Details</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pagination">
                <span>Showing 1-4 of 12 donations</span>
                <div>
                    <button class="btn tiny">‹</button>
                    <button class="btn tiny active">1</button>
                    <button class="btn tiny">2</button>
                    <button class="btn tiny">›</button>
                </div>
            </div>
        </div>

    </div>
</main>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
