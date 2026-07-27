<?php
/**
 * SevaNest – Donor Dashboard
 * File     : modules/donor/dashboard.php
 * Version  : 1.1
 */

require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../config/database.php';

// Require Donor login
require_login();
require_role('Donor');

$base_path = '../../';
$page_title = 'Donor Dashboard | SevaNest';

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'donor';
$currentPage   = 'dashboard.php';
$sn_asset_root = "../../assets";
include '../../includes/sidebar.php';
?>

<main id="sn-main-content" role="main" aria-label="Donor dashboard content" class="p-4 flex-grow-1">
    <div class="container-fluid">
        
        <!-- Welcome Card -->
        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
            <div>
                <h3 class="fw-bold mb-0 text-dark">Thank You, Mr. John Doe 🌟</h3>
                <small class="text-muted">Your generosity changes lives at SevaNest Old Age Home.</small>
            </div>
            <div>
                <a href="campaigns.php" class="btn btn-primary fw-semibold btn-sm" id="quickDonateBtn"><i class="bi bi-heart-fill me-2"></i>Quick Donate</a>
            </div>
        </div>

        <!-- Dashboard widgets & stats grid -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-4 border-success h-100">
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Total Donations</span>
                    <h4 class="fw-bold mb-0 text-success mt-1">$12,450.00</h4>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-4 border-primary h-100">
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Elderly Helped</span>
                    <h4 class="fw-bold mb-0 text-primary mt-1">15 Residents</h4>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-4 border-info h-100">
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Campaigns Supported</span>
                    <h4 class="fw-bold mb-0 text-info mt-1">4 Active</h4>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-4 border-warning h-100">
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Monthly Contribution</span>
                    <h4 class="fw-bold mb-0 text-warning mt-1">$500.00</h4>
                </div>
            </div>
        </div>

        <!-- Main Dashboard Widgets -->
        <div class="row g-4">
            
            <!-- Left Side: Recent Donations & Donation Trends -->
            <div class="col-lg-8 d-flex flex-column gap-4">
                
                <!-- Donation trend chart placeholder -->
                <div class="card border-0 shadow-sm rounded-3 bg-white p-4">
                    <h6 class="fw-bold text-dark mb-1">Donation Contribution Trend</h6>
                    <p class="text-muted small mb-4">Total contribution trend over past 5 months</p>
                    
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex align-items-center">
                            <span class="text-muted small font-monospace" style="width: 50px;">JUL</span>
                            <div class="flex-grow-1 mx-3" style="height: 12px;">
                                <div class="progress h-100">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 75%;"></div>
                                </div>
                            </div>
                            <span class="text-dark small font-monospace">$1,500</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="text-muted small font-monospace" style="width: 50px;">JUN</span>
                            <div class="flex-grow-1 mx-3" style="height: 12px;">
                                <div class="progress h-100">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 55%;"></div>
                                </div>
                            </div>
                            <span class="text-dark small font-monospace">$1,100</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="text-muted small font-monospace" style="width: 50px;">MAY</span>
                            <div class="flex-grow-1 mx-3" style="height: 12px;">
                                <div class="progress h-100">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 60%;"></div>
                                </div>
                            </div>
                            <span class="text-dark small font-monospace">$1,200</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="text-muted small font-monospace" style="width: 50px;">APR</span>
                            <div class="flex-grow-1 mx-3" style="height: 12px;">
                                <div class="progress h-100">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 45%;"></div>
                                </div>
                            </div>
                            <span class="text-dark small font-monospace">$900</span>
                        </div>
                    </div>
                </div>

                <!-- Recent Donations table -->
                <div class="card border-0 shadow-sm rounded-3 bg-white">
                    <div class="card-header bg-white border-bottom border-light p-3 d-flex align-items-center justify-content-between">
                        <h6 class="fw-bold mb-0 text-dark">Recent Donations</h6>
                        <a href="donations.php" class="btn btn-sm btn-link text-primary p-0 text-decoration-none fw-semibold">View All</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                            <thead class="table-light text-muted text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.05em;">
                                <tr>
                                    <th class="ps-3">Campaign</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th class="pe-3 text-end">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="ps-3"><strong class="text-dark">Winter Clothing Drive</strong></td>
                                    <td>$250.00</td>
                                    <td>24 Jul 2026</td>
                                    <td class="pe-3 text-end"><span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1">Successful</span></td>
                                </tr>
                                <tr>
                                    <td class="ps-3"><strong class="text-dark">General Welfare Fund</strong></td>
                                    <td>$500.00</td>
                                    <td>15 Jul 2026</td>
                                    <td class="pe-3 text-end"><span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1">Successful</span></td>
                                </tr>
                                <tr>
                                    <td class="ps-3"><strong class="text-dark">Medical ICU Equipment</strong></td>
                                    <td>$1,500.00</td>
                                    <td>02 Jun 2026</td>
                                    <td class="pe-3 text-end"><span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1">Successful</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <!-- Right Side: Active Campaigns & Notifications -->
            <div class="col-lg-4 d-flex flex-column gap-4">
                
                <!-- Active Campaigns -->
                <div class="card border-0 shadow-sm rounded-3 bg-white p-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold text-dark mb-0">Active Support Campaigns</h6>
                        <a href="campaigns.php" class="btn btn-sm btn-link text-primary p-0 text-decoration-none fw-semibold">Browse</a>
                    </div>
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex gap-3 align-items-start">
                            <div class="bg-primary rounded-circle" style="width: 8px; height: 8px; margin-top: 6px; flex-shrink: 0;"></div>
                            <div>
                                <strong class="d-block text-dark small" style="line-height: 1.3;">Senior ICU Facility Setup</strong>
                                <small class="text-muted d-block mt-0.5" style="font-size: 0.75rem;">Goal: $25,000 · Raised: $18,450 (73% complete)</small>
                            </div>
                        </div>
                        <div class="d-flex gap-3 align-items-start">
                            <div class="bg-warning rounded-circle" style="width: 8px; height: 8px; margin-top: 6px; flex-shrink: 0;"></div>
                            <div>
                                <strong class="d-block text-dark small" style="line-height: 1.3;">Warm Clothes &amp; Blankets Campaign</strong>
                                <small class="text-muted d-block mt-0.5" style="font-size: 0.75rem;">Goal: $5,000 · Raised: $4,200 (84% complete)</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Notifications & Thank You Messages -->
                <div class="card border-0 shadow-sm rounded-3 bg-white p-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold text-dark mb-0">Notifications &amp; Impact Alerts</h6>
                        <a href="notifications.php" class="btn btn-sm btn-link text-primary p-0 text-decoration-none fw-semibold">View All</a>
                    </div>
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex gap-3 align-items-start">
                            <div class="bg-danger rounded-circle" style="width: 8px; height: 8px; margin-top: 6px; flex-shrink: 0;"></div>
                            <div>
                                <strong class="d-block text-dark small" style="line-height: 1.3;">Thank You Alert:</strong>
                                <span class="text-muted small">Mrs. Iyer sent a thank-you note for the wheelchair support campaign.</span>
                                <small class="text-muted d-block mt-1 font-monospace" style="font-size: 0.7rem;">1h ago</small>
                            </div>
                        </div>
                        <div class="d-flex gap-3 align-items-start">
                            <div class="bg-warning rounded-circle" style="width: 8px; height: 8px; margin-top: 6px; flex-shrink: 0;"></div>
                            <div>
                                <strong class="d-block text-dark small" style="line-height: 1.3;">Receipt Issued:</strong>
                                <span class="text-muted small">Receipt #REC-7890 generated for $250.00.</span>
                                <small class="text-muted d-block mt-1 font-monospace" style="font-size: 0.7rem;">Yesterday</small>
                            </div>
                        </div>
                        <div class="d-flex gap-3 align-items-start">
                            <div class="bg-primary rounded-circle" style="width: 8px; height: 8px; margin-top: 6px; flex-shrink: 0;"></div>
                            <div>
                                <strong class="d-block text-dark small" style="line-height: 1.3;">New Campaign:</strong>
                                <span class="text-muted small">"Nutrition Diet Enhancement" has been launched.</span>
                                <small class="text-muted d-block mt-1 font-monospace" style="font-size: 0.7rem;">3 days ago</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activities & Upcoming Events -->
                <div class="card border-0 shadow-sm rounded-3 bg-white p-3">
                    <h6 class="fw-bold text-dark mb-3">Recent Activities &amp; Events</h6>
                    <ul class="list-group list-group-flush fs-6" style="font-size: 0.82rem;">
                        <li class="list-group-item bg-transparent border-light py-2 px-0 d-flex gap-2">
                            <strong class="font-monospace text-primary">25 Jul</strong>
                            <span class="text-dark">John Doe donated $250.00 to winter drive.</span>
                        </li>
                        <li class="list-group-item bg-transparent border-light py-2 px-0 d-flex gap-2">
                            <strong class="font-monospace text-primary">20 Jul</strong>
                            <span class="text-dark">General Health Camp checkup completed for Wing B.</span>
                        </li>
                        <li class="list-group-item bg-transparent border-light py-2 px-0 d-flex gap-2">
                            <strong class="font-monospace text-primary">15 Jul</strong>
                            <span class="text-dark">Monthly recurring support donation of $500.00 successfully cleared.</span>
                        </li>
                    </ul>
                </div>

            </div>
        </div>

    </div>
</main>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
