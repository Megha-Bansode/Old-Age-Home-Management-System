<?php
/**
 * SevaNest – Donor Impact & History
 * File     : modules/donor/history.php
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

// Query donation timeline
$stmt = $pdo->prepare("SELECT * FROM donations WHERE donor_id = ? ORDER BY donation_date DESC");
$stmt->execute([$donor_id]);
$timeline_donations = $stmt->fetchAll();

// Query active residents
$total_residents = (int)$pdo->query("SELECT COUNT(*) FROM residents WHERE status = 'Active'")->fetchColumn();

// Query supported campaigns count
$stmt = $pdo->prepare("SELECT COUNT(DISTINCT purpose) FROM donations WHERE donor_id = ?");
$stmt->execute([$donor_id]);
$supported_campaigns = (int)($stmt->fetchColumn() ?? 0);

// Query monthly summary
$stmt = $pdo->prepare("
    SELECT DATE_FORMAT(donation_date, '%M %Y') AS month_name, SUM(amount) AS total_amount, COUNT(DISTINCT purpose) AS category_count
    FROM donations
    WHERE donor_id = ?
    GROUP BY YEAR(donation_date), MONTH(donation_date)
    ORDER BY YEAR(donation_date) DESC, MONTH(donation_date) DESC
");
$stmt->execute([$donor_id]);
$monthly_summaries = $stmt->fetchAll();

$base_path = '../../';
$page_title = 'Donation History & Impact | SevaNest';
$extra_css = [
    'assets/css/donor.css'
];

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'donor';
$currentPage   = 'history.php';
$sn_asset_root = "../../assets";
$base_path = '../../';
include '../../includes/sidebar.php';
?>

<!-- ═══════════════════════════════════════════════════════════════════════
     MAIN CONTENT AREA
     ═══════════════════════════════════════════════════════════════════════ -->
<main id="sn-main-content" role="main" aria-label="Donor history content">
    <div class="donor-page-wrapper">
        
        <!-- Header Strip -->
        <div class="dn-header-strip animate-fade-in">
            <div>
                <h2 class="dn-header-strip__title">My Philanthropy Timeline</h2>
                <p style="color: var(--color-text-muted-team); margin: 4px 0 0;">Trace your history of campaign participation and total impact milestones.</p>
            </div>
        </div>

        <!-- Two Column Layout -->
        <div class="grid two-col animate-fade-in">
            
            <!-- Left Side: History Timeline -->
            <div class="card">
                <div class="card-head">
                    <h3>Philanthropic History Timeline</h3>
                </div>
                <ul class="donor-timeline">
                    <?php if (empty($timeline_donations)): ?>
                        <li class="text-muted small">No philanthropy history found.</li>
                    <?php else: ?>
                        <?php foreach ($timeline_donations as $don): ?>
                            <li class="donor-timeline-item campaign">
                                <div class="donor-timeline-time"><?php echo date('d F Y', strtotime($don['donation_date'])); ?></div>
                                <div class="donor-timeline-title"><?php echo sn_e($don['purpose'] ?? 'General Contribution'); ?></div>
                                <div class="donor-timeline-desc">Contributed $<?php echo number_format($don['amount'], 2); ?> via <?php echo sn_e($don['payment_method']); ?>. Reference ID: <?php echo sn_e($don['transaction_id']); ?>.</div>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Right Side: Impact Summary & Milestones -->
            <div class="d-flex flex-column gap-4">
                
                <!-- Impact Summary -->
                <div class="card" style="border-top: 4px solid var(--color-accent);">
                    <div class="card-head">
                        <h3>Impact Milestones</h3>
                    </div>
                    <div class="d-flex flex-column gap-3" style="font-size: var(--font-size-sm);">
                        <div style="background: var(--color-secondary); padding: 15px; border-radius: var(--radius-medium);">
                            <strong style="color: var(--color-primary); font-size: var(--font-size-lg); display: block; margin-bottom: 2px;"><?php echo $total_residents; ?> Residents Helped</strong>
                            <span style="color: var(--color-text-muted-team);">Your donations funded medicine, dietary plans, or mobility support aids for <?php echo $total_residents; ?> elderly residents.</span>
                        </div>
                        <div style="background: var(--color-secondary); padding: 15px; border-radius: var(--radius-medium);">
                            <strong style="color: var(--color-accent); font-size: var(--font-size-lg); display: block; margin-bottom: 2px;"><?php echo $supported_campaigns; ?> Categories Supported</strong>
                            <span style="color: var(--color-text-muted-team);">You have actively funded <?php echo $supported_campaigns; ?> distinct support campaign areas inside SevaNest.</span>
                        </div>
                    </div>
                </div>

                <!-- Monthly contribution overview -->
                <div class="card">
                    <div class="card-head">
                        <h3>Monthly Summary</h3>
                    </div>
                    <ul class="tl">
                        <?php if (empty($monthly_summaries)): ?>
                            <li class="text-muted small">No monthly summary data available.</li>
                        <?php else: ?>
                            <?php foreach ($monthly_summaries as $month): ?>
                                <li><b><?php echo sn_e($month['month_name']); ?>:</b> Total donated: $<?php echo number_format($month['total_amount'], 2); ?> across <?php echo $month['category_count']; ?> categories.</li>
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
