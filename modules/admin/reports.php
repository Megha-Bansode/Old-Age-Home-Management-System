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

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'admin';
$currentPage   = 'reports.php';
$sn_asset_root = "../../assets";
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
                        <!-- July -->
                        <div class="d-flex align-items-center">
                            <span class="text-muted font-monospace small" style="width: 45px;">JUL</span>
                            <div class="flex-grow-1 mx-3" style="height: 20px;">
                                <div class="d-flex h-100 gap-1">
                                    <div class="bg-primary rounded-start" style="width: 80%;" title="Admissions"></div>
                                    <div class="bg-warning rounded-end" style="width: 30%;" title="Discharges"></div>
                                </div>
                            </div>
                            <span class="text-dark small font-monospace">8 / 3</span>
                        </div>
                        <!-- June -->
                        <div class="d-flex align-items-center">
                            <span class="text-muted font-monospace small" style="width: 45px;">JUN</span>
                            <div class="flex-grow-1 mx-3" style="height: 20px;">
                                <div class="d-flex h-100 gap-1">
                                    <div class="bg-primary rounded-start" style="width: 60%;" title="Admissions"></div>
                                    <div class="bg-warning rounded-end" style="width: 40%;" title="Discharges"></div>
                                </div>
                            </div>
                            <span class="text-dark small font-monospace">6 / 4</span>
                        </div>
                        <!-- May -->
                        <div class="d-flex align-items-center">
                            <span class="text-muted font-monospace small" style="width: 45px;">MAY</span>
                            <div class="flex-grow-1 mx-3" style="height: 20px;">
                                <div class="d-flex h-100 gap-1">
                                    <div class="bg-primary rounded-start" style="width: 90%;" title="Admissions"></div>
                                    <div class="bg-warning rounded-end" style="width: 20%;" title="Discharges"></div>
                                </div>
                            </div>
                            <span class="text-dark small font-monospace">9 / 2</span>
                        </div>
                        <!-- April -->
                        <div class="d-flex align-items-center">
                            <span class="text-muted font-monospace small" style="width: 45px;">APR</span>
                            <div class="flex-grow-1 mx-3" style="height: 20px;">
                                <div class="d-flex h-100 gap-1">
                                    <div class="bg-primary rounded-start" style="width: 50%;" title="Admissions"></div>
                                    <div class="bg-warning rounded-end" style="width: 50%;" title="Discharges"></div>
                                </div>
                            </div>
                            <span class="text-dark small font-monospace">5 / 5</span>
                        </div>
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
                                <span>74 Residents (77%)</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: 77%;"></div>
                            </div>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between text-dark small fw-semibold mb-1">
                                <span>Needs Regular Care / Supervision</span>
                                <span>18 Residents (19%)</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: 19%;"></div>
                            </div>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between text-dark small fw-semibold mb-1">
                                <span>Critical / Intensive Monitoring</span>
                                <span>4 Residents (4%)</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: 4%;"></div>
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
                            <span class="text-dark fw-bold">12 / Week</span>
                        </li>
                        <li class="list-group-item bg-transparent border-light py-2.5 px-0 d-flex justify-content-between">
                            <span class="text-muted">Emergency Checkups</span>
                            <span class="text-dark fw-bold">2 Cases</span>
                        </li>
                        <li class="list-group-item bg-transparent border-light py-2.5 px-0 d-flex justify-content-between">
                            <span class="text-muted">Nurse-to-Resident Ratio</span>
                            <span class="text-dark fw-bold">1 : 8</span>
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
                            <span class="text-dark fw-bold">₹1,45,000</span>
                        </li>
                        <li class="list-group-item bg-transparent border-light py-2.5 px-0 d-flex justify-content-between">
                            <span class="text-muted">Low Stock Warnings</span>
                            <span class="text-danger fw-bold">2 Items</span>
                        </li>
                        <li class="list-group-item bg-transparent border-light py-2.5 px-0 d-flex justify-content-between">
                            <span class="text-muted">Out-of-Stock Items</span>
                            <span class="text-danger fw-bold">1 Item</span>
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
                            <span class="text-dark fw-bold">96 residents</span>
                        </li>
                        <li class="list-group-item bg-transparent border-light py-2.5 px-0 d-flex justify-content-between">
                            <span class="text-muted">Male Residents</span>
                            <span class="text-dark fw-bold">42 residents</span>
                        </li>
                        <li class="list-group-item bg-transparent border-light py-2.5 px-0 d-flex justify-content-between">
                            <span class="text-muted">Female Residents</span>
                            <span class="text-dark fw-bold">54 residents</span>
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
