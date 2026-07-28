<?php
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../config/database.php';

// Require Donor login
require_login();
require_role('Donor');

$pdo = get_db_connection();
$donor_id = $_SESSION['user_id'] ?? 6;
$donor_name = $_SESSION['user_full_name'] ?? 'John Doe';

// Query donor stats
$stmt = $pdo->prepare("SELECT SUM(amount) FROM donations WHERE donor_id = ?");
$stmt->execute([$donor_id]);
$my_total_donated = (float)($stmt->fetchColumn() ?? 0);

$my_total_residents = (int)$pdo->query("SELECT COUNT(*) FROM residents WHERE status = 'Active'")->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(DISTINCT purpose) FROM donations WHERE donor_id = ?");
$stmt->execute([$donor_id]);
$my_campaigns_count = (int)($stmt->fetchColumn() ?? 0);

$stmt = $pdo->prepare("SELECT SUM(amount) FROM donations WHERE donor_id = ? AND MONTH(donation_date) = MONTH(CURDATE()) AND YEAR(donation_date) = YEAR(CURDATE())");
$stmt->execute([$donor_id]);
$my_monthly_donated = (float)($stmt->fetchColumn() ?? 0);

// Top Donor Contribution (grouped by donor)
$top_donor_contribution = (float)$pdo->query("SELECT MAX(total_amount) FROM (SELECT SUM(amount) AS total_amount FROM donations GROUP BY donor_id) AS donor_totals")->fetchColumn();

// Active Donors
$active_donors_count = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role = 'Donor' AND status = 'active'")->fetchColumn();

// Pending Pledges for the logged-in donor
$stmt = $pdo->prepare("SELECT COUNT(*) FROM pledges WHERE donor_id = ? AND status = 'Pending'");
$stmt->execute([$donor_id]);
$my_pending_pledges = (int)($stmt->fetchColumn() ?? 0);

// Events Sponsored by the logged-in donor (created_by = user_id)
$stmt = $pdo->prepare("SELECT COUNT(*) FROM events WHERE created_by = ?");
$stmt->execute([$donor_id]);
$my_events_sponsored = (int)($stmt->fetchColumn() ?? 0);

// Query contribution trend for past 5 months
$stmt = $pdo->prepare("
    SELECT DATE_FORMAT(donation_date, '%b') AS month_name, SUM(amount) AS total_amount
    FROM donations
    WHERE donor_id = ? AND donation_date >= DATE_SUB(CURDATE(), INTERVAL 5 MONTH)
    GROUP BY YEAR(donation_date), MONTH(donation_date)
    ORDER BY YEAR(donation_date) ASC, MONTH(donation_date) ASC
");
$stmt->execute([$donor_id]);
$trend_data = $stmt->fetchAll();

// Query recent donations
$stmt = $pdo->prepare("SELECT * FROM donations WHERE donor_id = ? ORDER BY donation_date DESC LIMIT 4");
$stmt->execute([$donor_id]);
$recent_donations = $stmt->fetchAll();

// Query dynamic raised amounts for active campaigns
$raised_icu = (float)$pdo->query("SELECT SUM(amount) FROM donations WHERE purpose = 'Senior ICU Facility Setup'")->fetchColumn();
$raised_winter = (float)$pdo->query("SELECT SUM(amount) FROM donations WHERE purpose = 'Winter Clothing Drive'")->fetchColumn();
$raised_diet = (float)$pdo->query("SELECT SUM(amount) FROM donations WHERE purpose = 'Nutrition Diet Enhancement'")->fetchColumn();

// Query notifications
$stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 3");
$stmt->execute([$donor_id]);
$notifications = $stmt->fetchAll();

// Query events
$events = $pdo->query("SELECT * FROM events ORDER BY event_date DESC LIMIT 3")->fetchAll();

$base_path = '../../';
$page_title = 'Donor Dashboard | SevaNest';

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'donor';
$currentPage   = 'dashboard.php';
$sn_asset_root = "../../assets";
$base_path = '../../';
include '../../includes/sidebar.php';
?>

<main id="sn-main-content" role="main" aria-label="Donor dashboard content" class="p-4 flex-grow-1">
    <div class="container-fluid">
        
        <!-- Welcome Card -->
        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
            <div>
                <h3 class="fw-bold mb-0 text-dark">Thank You, <?php echo sn_e($donor_name); ?> 🌟</h3>
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
                    <h4 class="fw-bold mb-0 text-success mt-1">$<?php echo number_format($my_total_donated, 2); ?></h4>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-4 border-primary h-100">
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Elderly Helped</span>
                    <h4 class="fw-bold mb-0 text-primary mt-1"><?php echo $my_total_residents; ?> Residents</h4>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-4 border-info h-100">
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Campaigns Supported</span>
                    <h4 class="fw-bold mb-0 text-info mt-1"><?php echo $my_campaigns_count; ?> Categories</h4>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-4 border-warning h-100">
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Monthly Contribution</span>
                    <h4 class="fw-bold mb-0 text-warning mt-1">$<?php echo number_format($my_monthly_donated, 2); ?></h4>
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
                        <?php if (empty($trend_data)): ?>
                            <div class="text-muted small">No donation trend data available.</div>
                        <?php else: ?>
                            <?php
                                $max_val = 1.0;
                                foreach ($trend_data as $t) {
                                    if ((float)$t['total_amount'] > $max_val) {
                                        $max_val = (float)$t['total_amount'];
                                    }
                                }
                            ?>
                            <?php foreach ($trend_data as $month): ?>
                                <?php $width = round(((float)$month['total_amount'] / $max_val) * 100); ?>
                                <div class="d-flex align-items-center">
                                    <span class="text-muted small font-monospace" style="width: 50px;"><?php echo strtoupper($month['month_name']); ?></span>
                                    <div class="flex-grow-1 mx-3" style="height: 12px;">
                                        <div class="progress h-100">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $width; ?>%;"></div>
                                        </div>
                                    </div>
                                    <span class="text-dark small font-monospace">$<?php echo number_format($month['total_amount'], 0); ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
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
                            <tbody>
                                <?php if (empty($recent_donations)): ?>
                                    <tr><td colspan="4" class="text-center text-muted py-3">No recent donations.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($recent_donations as $don): ?>
                                        <tr>
                                            <td class="ps-3"><strong class="text-dark"><?php echo sn_e($don['purpose'] ?? 'General Fund'); ?></strong></td>
                                            <td>$<?php echo number_format($don['amount'], 2); ?></td>
                                            <td><?php echo date('d M Y', strtotime($don['donation_date'])); ?></td>
                                            <td class="pe-3 text-end"><span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1">Successful</span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
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
                        <?php
                            $pct_icu = min(100, round(($raised_icu / 25000) * 100));
                            $pct_winter = min(100, round(($raised_winter / 5000) * 100));
                            $pct_diet = min(100, round(($raised_diet / 1000) * 100)); // Default goal $1,000 for nutritional diet
                        ?>
                        <div class="d-flex gap-3 align-items-start">
                            <div class="bg-primary rounded-circle" style="width: 8px; height: 8px; margin-top: 6px; flex-shrink: 0;"></div>
                            <div class="w-100">
                                <strong class="d-block text-dark small" style="line-height: 1.3;">Senior ICU Facility Setup</strong>
                                <small class="text-muted d-block mt-0.5" style="font-size: 0.75rem;">Goal: $25,000 · Raised: $<?php echo number_format($raised_icu, 0); ?> (<?php echo $pct_icu; ?>% complete)</small>
                            </div>
                        </div>
                        <div class="d-flex gap-3 align-items-start">
                            <div class="bg-warning rounded-circle" style="width: 8px; height: 8px; margin-top: 6px; flex-shrink: 0;"></div>
                            <div class="w-100">
                                <strong class="d-block text-dark small" style="line-height: 1.3;">Warm Clothes &amp; Blankets Campaign</strong>
                                <small class="text-muted d-block mt-0.5" style="font-size: 0.75rem;">Goal: $5,000 · Raised: $<?php echo number_format($raised_winter, 0); ?> (<?php echo $pct_winter; ?>% complete)</small>
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
                        <?php if (empty($notifications)): ?>
                            <div class="d-flex gap-3 align-items-start">
                                <div class="bg-danger rounded-circle" style="width: 8px; height: 8px; margin-top: 6px; flex-shrink: 0;"></div>
                                <div>
                                    <strong class="d-block text-dark small" style="line-height: 1.3;">Welcome to SevaNest:</strong>
                                    <span class="text-muted small">Thank you for registering as a donor. Explore active campaigns to contribute!</span>
                                    <small class="text-muted d-block mt-1 font-monospace" style="font-size: 0.7rem;">Just now</small>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($notifications as $notif): ?>
                                <?php
                                    $dot_color = 'bg-primary';
                                    if (stripos($notif['title'], 'thank') !== false) $dot_color = 'bg-danger';
                                    elseif (stripos($notif['title'], 'receipt') !== false) $dot_color = 'bg-warning';
                                ?>
                                <div class="d-flex gap-3 align-items-start">
                                    <div class="<?php echo $dot_color; ?> rounded-circle" style="width: 8px; height: 8px; margin-top: 6px; flex-shrink: 0;"></div>
                                    <div>
                                        <strong class="d-block text-dark small" style="line-height: 1.3;"><?php echo sn_e($notif['title']); ?>:</strong>
                                        <span class="text-muted small"><?php echo sn_e($notif['message'] ?? ''); ?></span>
                                        <small class="text-muted d-block mt-1 font-monospace" style="font-size: 0.7rem;"><?php echo date('d M, H:i', strtotime($notif['created_at'] ?? 'now')); ?></small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Recent Activities & Upcoming Events -->
                <div class="card border-0 shadow-sm rounded-3 bg-white p-3">
                    <h6 class="fw-bold text-dark mb-3">Recent Activities &amp; Events</h6>
                    <ul class="list-group list-group-flush fs-6" style="font-size: 0.82rem;">
                        <?php if (empty($events)): ?>
                            <li class="list-group-item bg-transparent border-light py-2 px-0 d-flex gap-2">
                                <span class="text-muted">No upcoming events scheduled.</span>
                            </li>
                        <?php else: ?>
                            <?php foreach ($events as $ev): ?>
                                <li class="list-group-item bg-transparent border-light py-2 px-0 d-flex gap-2">
                                    <strong class="font-monospace text-primary"><?php echo date('d M', strtotime($ev['event_date'])); ?></strong>
                                    <span class="text-dark"><?php echo sn_e($ev['title']); ?> — <?php echo sn_e(substr($ev['description'] ?? '', 0, 80)); ?>...</span>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>

            </div>
        </div>

    </div>
</main>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
