<?php
/**
 * SevaNest – Donor Dashboard
 * File     : modules/donor/dashboard.php
 * Version  : 1.0
 */

require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../config/database.php';

// Require Donor login
require_login();

$base_path = '../../';
$page_title = 'Donor Dashboard | SevaNest';
$extra_css = [
    'assets/css/donor.css'
];

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'donor';
$currentPage   = 'dashboard.php';
$sn_asset_root = "../../assets";
include '../../includes/sidebar.php';
?>

<!-- ═══════════════════════════════════════════════════════════════════════
     MAIN CONTENT AREA
     ═══════════════════════════════════════════════════════════════════════ -->
<main id="sn-main-content" role="main" aria-label="Donor dashboard content">
    <div class="donor-page-wrapper">
        
        <!-- Welcome Card -->
        <div class="dn-header-strip animate-fade-in">
            <div>
                <h2 class="dn-header-strip__title">Thank You, Mr. John Doe 🌟</h2>
                <p style="color: var(--color-text-muted-team); margin: 4px 0 0;">Your generosity changes lives at SevaNest Old Age Home.</p>
            </div>
            <div>
                <a href="campaigns.php" class="btn btn-primary" id="quickDonateBtn"><i class="bi bi-heart-fill me-2"></i>Quick Donate</a>
            </div>
        </div>

        <!-- Dashboard widgets & stats grid -->
        <div class="vitals-grid">
            <div class="vital-card">
                <div class="vital-icon gold"><i class="bi bi-cash-stack"></i></div>
                <div class="vital-details">
                    <span>Total Donations</span>
                    <h4>$12,450.00</h4>
                </div>
            </div>
            <div class="vital-card">
                <div class="vital-icon"><i class="bi bi-people-fill"></i></div>
                <div class="vital-details">
                    <span>Elderly Helped</span>
                    <h4>15 Residents</h4>
                </div>
            </div>
            <div class="vital-card">
                <div class="vital-icon blue"><i class="bi bi-flag-fill"></i></div>
                <div class="vital-details">
                    <span>Campaigns Supported</span>
                    <h4>4 Active</h4>
                </div>
            </div>
            <div class="vital-card">
                <div class="vital-icon gold"><i class="bi bi-graph-up-arrow"></i></div>
                <div class="vital-details">
                    <span>Monthly Contribution</span>
                    <h4>$500.00</h4>
                </div>
            </div>
        </div>

        <!-- Main Dashboard Widgets -->
        <div class="grid two-col animate-fade-in">
            
            <!-- Left Side: Recent Donations & Donation Trends -->
            <div class="d-flex flex-column gap-4">
                
                <!-- Donation trend chart placeholder -->
                <div class="card">
                    <div class="card-head">
                        <h3>Donation Contribution Trend</h3>
                    </div>
                    <div class="chart-placeholder" style="background: var(--color-secondary); border-radius: var(--radius-medium); padding: 20px; height: 250px; display: flex; flex-direction: column; justify-content: space-between;">
                        <div class="bars" style="display: flex; align-items: flex-end; gap: 14px; height: 180px;">
                            <span style="height: 30%; flex: 1; background: linear-gradient(180deg, var(--color-primary), var(--color-accent)); border-radius: 4px;"></span>
                            <span style="height: 45%; flex: 1; background: linear-gradient(180deg, var(--color-primary), var(--color-accent)); border-radius: 4px;"></span>
                            <span style="height: 60%; flex: 1; background: linear-gradient(180deg, var(--color-primary), var(--color-accent)); border-radius: 4px;"></span>
                            <span style="height: 55%; flex: 1; background: linear-gradient(180deg, var(--color-primary), var(--color-accent)); border-radius: 4px;"></span>
                            <span style="height: 75%; flex: 1; background: linear-gradient(180deg, var(--color-primary), var(--color-accent)); border-radius: 4px;"></span>
                        </div>
                        <div class="chart-legend" style="text-align: center; color: var(--color-text-muted-team); font-size: var(--font-size-xs);">
                            <em>Total contribution trend over past 5 months</em>
                        </div>
                    </div>
                </div>

                <!-- Recent Donations table -->
                <div class="card">
                    <div class="card-head">
                        <h3>Recent Donations</h3>
                        <a href="donations.php" class="link">View All</a>
                    </div>
                    <div class="table-wrap">
                        <table class="tbl">
                            <thead>
                                <tr>
                                    <th>Campaign</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><b>Winter Clothing Drive</b></td>
                                    <td>$250.00</td>
                                    <td>24 Jul 2026</td>
                                    <td><span class="badge green">Successful</span></td>
                                </tr>
                                <tr>
                                    <td><b>General Welfare Fund</b></td>
                                    <td>$500.00</td>
                                    <td>15 Jul 2026</td>
                                    <td><span class="badge green">Successful</span></td>
                                </tr>
                                <tr>
                                    <td><b>Medical ICU Equipment</b></td>
                                    <td>$1,500.00</td>
                                    <td>02 Jun 2026</td>
                                    <td><span class="badge green">Successful</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <!-- Right Side: Active Campaigns & Notifications -->
            <div class="d-flex flex-column gap-4">
                
                <!-- Active Campaigns -->
                <div class="card">
                    <div class="card-head">
                        <h3>Active Support Campaigns</h3>
                        <a href="campaigns.php" class="link">Browse Campaigns</a>
                    </div>
                    <ul class="timeline">
                        <li>
                            <span class="dot"></span>
                            <div>
                                <strong>Senior ICU Facility Setup</strong>
                                <p style="font-size: var(--font-size-xs); color: var(--color-text-muted-team); margin: 2px 0;">Goal: $25,000 · Raised: $18,450 (73% complete)</p>
                            </div>
                        </li>
                        <li>
                            <span class="dot gold"></span>
                            <div>
                                <strong>Warm Clothes &amp; Blankets Campaign</strong>
                                <p style="font-size: var(--font-size-xs); color: var(--color-text-muted-team); margin: 2px 0;">Goal: $5,000 · Raised: $4,200 (84% complete)</p>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- Recent Notifications & Thank You Messages -->
                <div class="card">
                    <div class="card-head">
                        <h3>Notifications &amp; Impact Alerts</h3>
                        <a href="notifications.php" class="link">View All</a>
                    </div>
                    <ul class="notif-list">
                        <li>
                            <span class="dot alert"></span>
                            <div><strong>Thank You Alert:</strong> Mrs. Iyer sent a thank-you note for the wheelchair support campaign. <em>1h ago</em></div>
                        </li>
                        <li>
                            <span class="dot gold"></span>
                            <div><strong>Receipt Issued:</strong> Receipt #REC-7890 generated for $250.00. <em>Yesterday</em></div>
                        </li>
                        <li>
                            <span class="dot"></span>
                            <div><strong>New Campaign:</strong> "Nutrition Diet Enhancement" has been launched. <em>3 days ago</em></div>
                        </li>
                    </ul>
                </div>

                <!-- Recent Activities & Upcoming Events -->
                <div class="card">
                    <div class="card-head">
                        <h3>Recent Activities &amp; Events</h3>
                    </div>
                    <ul class="tl">
                        <li><b>25 Jul:</b> John Doe donated $250.00 to winter drive.</li>
                        <li><b>20 Jul:</b> General Health Camp checkup completed for Wing B.</li>
                        <li><b>15 Jul:</b> Monthly recurring support donation of $500.00 successfully cleared.</li>
                    </ul>
                </div>

            </div>

        </div>

    </div>
</main>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
