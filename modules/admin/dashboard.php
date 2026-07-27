<?php
/**
 * SevaNest — Admin Dashboard
 * File     : modules/admin/dashboard.php
 * Version  : 1.0
 */

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/functions.php';

// Require Admin login
require_login();
require_role(['Admin', 'Old Age Home Admin']);

$base_path = '../../';
$page_title = 'Admin Dashboard | SevaNest';

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'admin';
$currentPage   = 'dashboard.php';
$sn_asset_root = "../../assets";
include '../../includes/sidebar.php';

// Fetch Mock Data
$stats      = sn_dashboard_stats();
$admissions = sn_recent_admissions();

$statusBadge = [
    'Admitted'      => 'success',
    'Under Review'  => 'warning',
    'Pending'       => 'secondary',
];
?>

<main id="sn-main-content" role="main" aria-label="Admin Dashboard Content" class="p-4 flex-grow-1">
    <div class="container-fluid">
        
        <!-- Welcome Strip -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" 
             style="background: linear-gradient(135deg, var(--color-primary) 0%, #4E7466 100%); color: #FFFFFF;">
            <div class="card-body p-4 p-lg-5">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <span class="badge bg-white text-dark mb-2 px-3 py-2 rounded-pill fw-semibold" style="font-size: 0.8rem;">
                            <i class="bi bi-shield-check text-success me-1"></i> Admin Portal
                        </span>
                        <h1 class="display-6 fw-bold mb-2">Welcome back, <?php echo sn_e($_SESSION['full_name'] ?? 'Admin'); ?>!</h1>
                        <p class="lead opacity-90 mb-0" style="font-size: 1rem;">
                            Here's what's happening across SevaNest today. Track admissions, register new residents, manage caregiving staff, and check visitor logs.
                        </p>
                    </div>
                    <div class="col-lg-4 text-end d-none d-lg-block">
                        <i class="bi bi-speedometer2 display-1 opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4 KPI Stat Cards -->
        <div class="row g-3 mb-4">
            <?php foreach ($stats as $s): ?>
                <?php
                    $cardBorder = 'border-primary';
                    if ($s['variant'] === 'warning') $cardBorder = 'border-warning';
                    
                    $iconClass = 'bi-people';
                    if ($s['icon'] === 'badge') $iconClass = 'bi-shield-check';
                    if ($s['icon'] === 'clipboard') $iconClass = 'bi-clipboard-plus';
                    if ($s['icon'] === 'visitor') $iconClass = 'bi-person-badge';
                ?>
                <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-3 h-100 p-3 bg-white border-start border-4 <?php echo $cardBorder; ?>">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em; font-size: 0.75rem;"><?php echo sn_e($s['label']); ?></span>
                            <div class="bg-light p-2 rounded-3 text-primary">
                                <i class="bi <?php echo $iconClass; ?> fs-5"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-baseline gap-2">
                            <h3 class="fw-bold mb-0 text-dark"><?php echo sn_e($s['value']); ?></h3>
                            <span class="small <?php echo $s['down'] ? 'text-danger' : 'text-success'; ?> fw-semibold">
                                <i class="bi <?php echo $s['down'] ? 'bi-arrow-down-short' : 'bi-arrow-up-short'; ?>"></i>
                                <?php echo sn_e($s['delta']); ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Two Column Content Layout -->
        <div class="row g-4">
            <!-- Left Side: Recent Admissions -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                    <div class="card-header bg-white border-bottom border-light p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="card-title fw-bold mb-0 text-dark">Recent Admissions</h5>
                            <small class="text-muted">Latest residents registered or awaiting approval</small>
                        </div>
                        <a href="admission_management.php" class="btn btn-sm btn-outline-primary fw-semibold">View all</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                                <thead class="table-light text-muted" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em;">
                                    <tr>
                                        <th class="ps-3">Resident</th>
                                        <th>Room</th>
                                        <th>Date</th>
                                        <th>Guardian</th>
                                        <th class="pe-3">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($admissions as $a): ?>
                                        <?php 
                                            $badgeCls = $statusBadge[$a['status']] ?? 'secondary';
                                            $initials = strtoupper($a['name'][0]);
                                        ?>
                                        <tr>
                                            <td class="ps-3">
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center fw-bold text-primary" style="width: 40px; height: 40px; font-size: 0.95rem;">
                                                        <?php echo sn_e($initials); ?>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold text-dark"><?php echo sn_e($a['name']); ?></div>
                                                        <small class="text-muted">Age <?php echo (int)$a['age']; ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="font-monospace text-dark"><?php echo sn_e($a['room']); ?></span></td>
                                            <td><span class="text-dark"><?php echo sn_e($a['date']); ?></span></td>
                                            <td><span class="text-dark"><?php echo sn_e($a['guardian']); ?></span></td>
                                            <td class="pe-3">
                                                <span class="badge bg-<?php echo $badgeCls; ?>-subtle text-<?php echo $badgeCls; ?> rounded-pill px-2.5 py-1">
                                                    <?php echo sn_e($a['status']); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Quick Actions & Today's Snapshot -->
            <div class="col-lg-4 d-flex flex-column gap-4">
                <!-- Quick Actions -->
                <div class="card border-0 shadow-sm rounded-3 bg-white">
                    <div class="card-header bg-white border-bottom border-light p-3">
                        <h5 class="card-title fw-bold mb-0 text-dark">Quick Actions</h5>
                        <small class="text-muted">Jump straight into a task</small>
                    </div>
                    <div class="card-body d-flex flex-column gap-2">
                        <a href="resident_registration.php" class="btn btn-primary w-100 justify-content-start py-2.5">
                            <i class="bi bi-person-plus me-2"></i> Register New Resident
                        </a>
                        <a href="admission_management.php" class="btn btn-outline-primary w-100 justify-content-start py-2.5">
                            <i class="bi bi-clipboard-check me-2"></i> Review Pending Admissions
                        </a>
                        <a href="visitor_management.php" class="btn btn-outline-primary w-100 justify-content-start py-2.5">
                            <i class="bi bi-person-bounding-box me-2"></i> Log a Visitor
                        </a>
                        <a href="staff_management.php" class="btn btn-outline-primary w-100 justify-content-start py-2.5">
                            <i class="bi bi-people me-2"></i> Manage Staff Roster
                        </a>
                    </div>
                </div>

                <!-- Today's Snapshot -->
                <div class="card border-0 shadow-sm rounded-3 bg-white">
                    <div class="card-header bg-white border-bottom border-light p-3">
                        <h5 class="card-title fw-bold mb-0 text-dark">Today's Snapshot</h5>
                    </div>
                    <div class="card-body d-flex flex-column gap-3 fs-6">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Beds occupied</span>
                            <span class="fw-bold text-dark">86 / 100</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Staff on duty</span>
                            <span class="fw-bold text-dark">24</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Visitors checked in</span>
                            <span class="fw-bold text-dark">12</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center border-top pt-2">
                            <span class="text-muted">Discharges scheduled</span>
                            <span class="badge bg-warning-subtle text-warning-emphasis">1 Pending</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
