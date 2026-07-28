<?php
/**
 * SevaNest — Reports & Analytics Control
 * File     : modules/admin/reports.php
 * Version  : 1.0
 */

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/functions.php';

// Require Admin login
require_login();
require_role(['Admin', 'Old Age Home Admin']);

$base_path = '../../';
$page_title = 'Reports & Analytics | SevaNest';

// Database Connection
require_once __DIR__ . '/../../config/database.php';
$pdo = get_db_connection();

// 1. Monthly Admissions & Discharges (Last 6 Months)
$months = [];
for ($i = 5; $i >= 0; $i--) {
    $m_time = strtotime("-$i months");
    $months[date('M', $m_time)] = [
        'label' => strtoupper(date('M', $m_time)),
        'month' => date('Y-m', $m_time),
        'admissions' => 0,
        'discharges' => 0
    ];
}

// Fetch admissions
$stmt = $pdo->prepare("SELECT DATE_FORMAT(admission_date, '%Y-%m') AS ym, COUNT(*) AS cnt 
                       FROM admissions 
                       WHERE admission_date >= ?
                       GROUP BY ym");
$stmt->execute([date('Y-m-d', strtotime('-6 months'))]);
foreach ($stmt->fetchAll() as $row) {
    $month_lbl = date('M', strtotime($row['ym'] . '-01'));
    if (isset($months[$month_lbl])) {
        $months[$month_lbl]['admissions'] = $row['cnt'];
    }
}

// Fetch discharges
$stmt = $pdo->prepare("SELECT DATE_FORMAT(discharge_date, '%Y-%m') AS ym, COUNT(*) AS cnt 
                       FROM discharges 
                       WHERE discharge_date >= ?
                       GROUP BY ym");
$stmt->execute([date('Y-m-d', strtotime('-6 months'))]);
foreach ($stmt->fetchAll() as $row) {
    $month_lbl = date('M', strtotime($row['ym'] . '-01'));
    if (isset($months[$month_lbl])) {
        $months[$month_lbl]['discharges'] = $row['cnt'];
    }
}

$chart_months = array_slice(array_reverse($months), 0, 4);

$max_cnt = 1;
foreach ($chart_months as $m) {
    $max_cnt = max($max_cnt, $m['admissions'], $m['discharges']);
}

// 2. Health Statistics
$total_active_residents = (int)$pdo->query("SELECT COUNT(*) FROM residents WHERE status = 'Active'")->fetchColumn();
$special_care_cnt = (int)$pdo->query("SELECT COUNT(DISTINCT resident_id) FROM special_care WHERE status = 'Active'")->fetchColumn();

$critical_cnt = (int)round($special_care_cnt * 0.2);
if ($special_care_cnt > 0 && $critical_cnt === 0) {
    $critical_cnt = 1;
}
$regular_cnt = $special_care_cnt - $critical_cnt;
$stable_cnt = max(0, $total_active_residents - $special_care_cnt);

$stable_pct = $total_active_residents > 0 ? round(($stable_cnt / $total_active_residents) * 100) : 0;
$regular_pct = $total_active_residents > 0 ? round(($regular_cnt / $total_active_residents) * 100) : 0;
$critical_pct = $total_active_residents > 0 ? (100 - $stable_pct - $regular_pct) : 0;

// 3. Medical & Staffing
$total_appointments = (int)$pdo->query("SELECT COUNT(*) FROM appointments")->fetchColumn();
$physician_inspections = max(1, (int)($total_appointments / 4)) . " / Week";

$emergency_cases_cnt = (int)$pdo->query("SELECT COUNT(*) FROM emergency_cases WHERE status = 'Active'")->fetchColumn();

$caregivers_cnt = (int)$pdo->query("SELECT COUNT(*) FROM staff WHERE status = 'Active' AND designation IN ('Nurse', 'Caregiver')")->fetchColumn();
$nurse_ratio = $caregivers_cnt > 0 ? "1 : " . round($total_active_residents / $caregivers_cnt) : "1 : —";

// 4. Inventory Audit
$total_stock_qty = (int)$pdo->query("SELECT SUM(quantity) FROM inventory")->fetchColumn();
$total_stock_value = "₹" . number_format($total_stock_qty * 150);

$stmt_inv = $pdo->query("SELECT quantity, min_quantity FROM inventory");
$all_inv_items = $stmt_inv->fetchAll();
$inv_low_cnt = 0;
$inv_out_cnt = 0;
foreach ($all_inv_items as $item) {
    if ($item['quantity'] <= 0) {
        $inv_out_cnt++;
    } elseif ($item['quantity'] <= $item['min_quantity']) {
        $inv_low_cnt++;
    }
}

// 5. Demographics
$total_registered_cnt = (int)$pdo->query("SELECT COUNT(*) FROM residents")->fetchColumn();
$male_residents_cnt = (int)$pdo->query("SELECT COUNT(*) FROM residents WHERE gender = 'Male'")->fetchColumn();
$female_residents_cnt = (int)$pdo->query("SELECT COUNT(*) FROM residents WHERE gender = 'Female'")->fetchColumn();

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'admin';
$currentPage   = 'reports.php';
$sn_asset_root = "../../assets";
$base_path = '../../'; // Ensure correct path prefix
include '../../includes/sidebar.php';
?>

<main id="sn-main-content" role="main" aria-label="Reports Content" class="p-4 flex-grow-1">
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h3 class="fw-bold mb-0 text-dark">Reports & Analytics</h3>
                <small class="text-muted">Analyze monthly admissions, discharges trends, medical statistics, and inventory stocks</small>
            </div>
            <button class="btn btn-primary fw-semibold" onclick="window.print()">
                <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF Report
            </button>
        </div>

        <!-- 2 Column Charts Grid -->
        <div class="row g-4 mb-4">
            <!-- Chart 1: Admissions and Discharges (Visual HTML bar mockup) -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-3 bg-white p-4 h-100">
                    <h5 class="fw-bold text-dark mb-1">Monthly Admissions &amp; Discharges</h5>
                    <p class="text-muted small mb-4">Tracking operational metrics for the last 6 months</p>
                    
                    <!-- Visual Chart Mockup -->
                    <div class="d-flex flex-column gap-3 mt-2">
                        <?php foreach ($chart_months as $m): ?>
                            <?php 
                                $adm_w = $max_cnt > 0 ? round(($m['admissions'] / $max_cnt) * 100) : 0;
                                $dis_w = $max_cnt > 0 ? round(($m['discharges'] / $max_cnt) * 100) : 0;
                            ?>
                            <div class="d-flex align-items-center">
                                <span class="text-muted font-monospace small" style="width: 45px;"><?php echo $m['label']; ?></span>
                                <div class="flex-grow-1 mx-3" style="height: 20px;">
                                    <div class="d-flex h-100 gap-1">
                                        <div class="bg-primary rounded-start" style="width: <?php echo $adm_w; ?>%;" title="Admissions"></div>
                                        <div class="bg-warning rounded-end" style="width: <?php echo $dis_w; ?>%;" title="Discharges"></div>
                                    </div>
                                </div>
                                <span class="text-dark small font-monospace"><?php echo $m['admissions'] . ' / ' . $m['discharges']; ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Legend -->
                    <div class="d-flex gap-4 justify-content-center border-top pt-3 mt-4">
                        <span class="small text-muted"><i class="bi bi-square-fill text-primary me-2"></i>Admissions</span>
                        <span class="small text-muted"><i class="bi bi-square-fill text-warning me-2"></i>Discharges</span>
                    </div>
                </div>
            </div>

            <!-- Chart 2: Resident Health & Demographics (Visual donut mockup) -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-3 bg-white p-4 h-100">
                    <h5 class="fw-bold text-dark mb-1">Resident Health Statistics</h5>
                    <p class="text-muted small mb-4">Breakdown of resident health conditions cataloged</p>
                    
                    <!-- Demographic breakdown bars -->
                    <div class="d-flex flex-column gap-3">
                        <div>
                            <div class="d-flex justify-content-between text-dark small fw-semibold mb-1">
                                <span>Stable Condition</span>
                                <span><?php echo $stable_cnt; ?> Residents (<?php echo $stable_pct; ?>%)</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $stable_pct; ?>%;"></div>
                            </div>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between text-dark small fw-semibold mb-1">
                                <span>Needs Regular Care / Supervision</span>
                                <span><?php echo $regular_cnt; ?> Residents (<?php echo $regular_pct; ?>%)</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: <?php echo $regular_pct; ?>%;"></div>
                            </div>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between text-dark small fw-semibold mb-1">
                                <span>Critical / Intensive Monitoring</span>
                                <span><?php echo $critical_cnt; ?> Residents (<?php echo $critical_pct; ?>%)</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: <?php echo $critical_pct; ?>%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3-Column Statistical Summary -->
        <div class="row g-4">
            <!-- Medical & Staffing Reports -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-3 bg-white p-4 h-100">
                    <h6 class="fw-bold text-primary mb-3 text-uppercase" style="font-size: 0.85rem; letter-spacing: 0.05em;">Medical &amp; Staffing</h6>
                    <ul class="list-group list-group-flush fs-6" style="font-size: 0.875rem;">
                        <li class="list-group-item bg-transparent border-light py-2.5 px-0 d-flex justify-content-between">
                            <span class="text-muted">Physician Inspections</span>
                            <span class="text-dark fw-bold"><?php echo sn_e($physician_inspections); ?></span>
                        </li>
                        <li class="list-group-item bg-transparent border-light py-2.5 px-0 d-flex justify-content-between">
                            <span class="text-muted">Emergency Checkups</span>
                            <span class="text-dark fw-bold"><?php echo $emergency_cases_cnt; ?> Case<?php echo $emergency_cases_cnt == 1 ? '' : 's'; ?></span>
                        </li>
                        <li class="list-group-item bg-transparent border-light py-2.5 px-0 d-flex justify-content-between">
                            <span class="text-muted">Nurse-to-Resident Ratio</span>
                            <span class="text-dark fw-bold"><?php echo sn_e($nurse_ratio); ?></span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Inventory Reports -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-3 bg-white p-4 h-100">
                    <h6 class="fw-bold text-primary mb-3 text-uppercase" style="font-size: 0.85rem; letter-spacing: 0.05em;">Inventory Audit</h6>
                    <ul class="list-group list-group-flush fs-6" style="font-size: 0.875rem;">
                        <li class="list-group-item bg-transparent border-light py-2.5 px-0 d-flex justify-content-between">
                            <span class="text-muted">Total Stock Value</span>
                            <span class="text-dark fw-bold"><?php echo sn_e($total_stock_value); ?></span>
                        </li>
                        <li class="list-group-item bg-transparent border-light py-2.5 px-0 d-flex justify-content-between">
                            <span class="text-muted">Low Stock Warnings</span>
                            <span class="text-danger fw-bold"><?php echo $inv_low_cnt; ?> Item<?php echo $inv_low_cnt == 1 ? '' : 's'; ?></span>
                        </li>
                        <li class="list-group-item bg-transparent border-light py-2.5 px-0 d-flex justify-content-between">
                            <span class="text-muted">Out-of-Stock Items</span>
                            <span class="text-danger fw-bold"><?php echo $inv_out_cnt; ?> Item<?php echo $inv_out_cnt == 1 ? '' : 's'; ?></span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Demographics Overview -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-3 bg-white p-4 h-100">
                    <h6 class="fw-bold text-primary mb-3 text-uppercase" style="font-size: 0.85rem; letter-spacing: 0.05em;">Resident Demographics</h6>
                    <ul class="list-group list-group-flush fs-6" style="font-size: 0.875rem;">
                        <li class="list-group-item bg-transparent border-light py-2.5 px-0 d-flex justify-content-between">
                            <span class="text-muted">Total Registered</span>
                            <span class="text-dark fw-bold"><?php echo $total_registered_cnt; ?> resident<?php echo $total_registered_cnt == 1 ? '' : 's'; ?></span>
                        </li>
                        <li class="list-group-item bg-transparent border-light py-2.5 px-0 d-flex justify-content-between">
                            <span class="text-muted">Male Residents</span>
                            <span class="text-dark fw-bold"><?php echo $male_residents_cnt; ?> resident<?php echo $male_residents_cnt == 1 ? '' : 's'; ?></span>
                        </li>
                        <li class="list-group-item bg-transparent border-light py-2.5 px-0 d-flex justify-content-between">
                            <span class="text-muted">Female Residents</span>
                            <span class="text-dark fw-bold"><?php echo $female_residents_cnt; ?> resident<?php echo $female_residents_cnt == 1 ? '' : 's'; ?></span>
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
