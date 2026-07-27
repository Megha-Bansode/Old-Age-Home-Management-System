<?php
/**
 * SevaNest — Super Admin Statistics & Analytical Visualizations
 * 
 * Provides charts, demographic breakdowns, admission trends, and financial donation insights.
 */

$base_path = '../../../';
$page_title = 'System Statistics';
$active_page = 'statistics';
$module_name = 'Super Admin Module';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_name'])) {
    $_SESSION['user_name'] = 'Super Admin';
    $_SESSION['user_role'] = 'Super Admin';
}

require_once $base_path . 'includes/header.php';
require_once $base_path . 'includes/navbar.php';
?>

<div class="d-flex min-vh-100 position-relative">
    
    <!-- Sidebar Include -->
    <?php require_once $base_path . 'includes/sidebar.php'; ?>

    <!-- Main Content Body -->
    <main class="main-content flex-grow-1 bg-light p-4">
        
        <!-- Page Header Strip -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4 p-4 bg-white rounded-4 shadow-sm border-start border-4 border-warning">
            <div>
                <h2 class="fw-bold text-dark mb-0">System Statistics & Graphical Insights</h2>
                <p class="text-muted small mb-0 mt-1">Visual data breakdown for residents, staff distribution, admissions, and financial growth.</p>
            </div>
            <div class="d-flex gap-2">
                <span class="badge bg-warning-subtle text-warning px-3 py-2 rounded-pill fw-semibold">
                    <i class="bi bi-graph-up-arrow me-1"></i>Realtime Metrics Updated
                </span>
            </div>
        </div>

        <!-- 4 Stat Summary Header Cards -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-4 border-primary">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small fw-semibold text-uppercase">Resident Occupancy</div>
                            <div class="fs-3 fw-bold text-dark mt-1">245 / 300</div>
                            <small class="text-success fw-medium">81.6% Capacity Filled</small>
                        </div>
                        <div class="rounded-3 p-3 bg-primary-subtle text-primary">
                            <i class="bi bi-people-fill fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-4 border-success">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small fw-semibold text-uppercase">Total Donations (YTD)</div>
                            <div class="fs-3 fw-bold text-dark mt-1">₹25.4 Lakhs</div>
                            <small class="text-success fw-medium">+18% vs Last Year</small>
                        </div>
                        <div class="rounded-3 p-3 bg-success-subtle text-success">
                            <i class="bi bi-currency-rupee fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-4 border-warning">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small fw-semibold text-uppercase">Monthly Admissions</div>
                            <div class="fs-3 fw-bold text-dark mt-1">16 New</div>
                            <small class="text-muted fw-medium">Average 14 / Month</small>
                        </div>
                        <div class="rounded-3 p-3 bg-warning-subtle text-warning">
                            <i class="bi bi-person-badge-fill fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-4 border-info">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small fw-semibold text-uppercase">Staff & Care ratio</div>
                            <div class="fs-3 fw-bold text-dark mt-1">1 : 5.8</div>
                            <small class="text-info fw-medium">Ideal Caregiver Ratio</small>
                        </div>
                        <div class="rounded-3 p-3 bg-info-subtle text-info">
                            <i class="bi bi-heart-pulse-fill fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 1: Monthly Admissions Chart & Gender Distribution -->
        <div class="row g-4 mb-4">
            
            <!-- Left Chart Card: Monthly Admissions Trend (SVG Chart) -->
            <div class="col-12 col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 bg-white h-100">
                    <div class="card-header bg-transparent border-bottom p-3 d-flex align-items-center justify-content-between">
                        <h5 class="fw-bold text-dark mb-0">
                            <i class="bi bi-bar-chart-line-fill me-2 text-primary"></i>Monthly Admissions Trend (2026)
                        </h5>
                        <span class="badge bg-light text-dark border">Admissions / Month</span>
                    </div>
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        
                        <!-- Visual SVG Bar Chart -->
                        <div class="mb-4">
                            <svg viewBox="0 0 500 200" class="w-100" style="height: 220px; overflow: visible;">
                                <!-- Grid Lines -->
                                <line x1="40" y1="30" x2="480" y2="30" stroke="#e9ecef" stroke-width="1" />
                                <line x1="40" y1="80" x2="480" y2="80" stroke="#e9ecef" stroke-width="1" />
                                <line x1="40" y1="130" x2="480" y2="130" stroke="#e9ecef" stroke-width="1" />
                                <line x1="40" y1="170" x2="480" y2="170" stroke="#ced4da" stroke-width="1.5" />

                                <!-- Bars -->
                                <!-- Jan (12) -->
                                <rect x="55" y="70" width="30" height="100" rx="4" fill="var(--color-primary)" opacity="0.85" />
                                <text x="70" y="190" font-size="11" text-anchor="middle" fill="#6c757d">Jan</text>
                                
                                <!-- Feb (15) -->
                                <rect x="115" y="45" width="30" height="125" rx="4" fill="var(--color-primary)" opacity="0.85" />
                                <text x="130" y="190" font-size="11" text-anchor="middle" fill="#6c757d">Feb</text>

                                <!-- Mar (18) -->
                                <rect x="175" y="20" width="30" height="150" rx="4" fill="var(--color-primary)" />
                                <text x="190" y="190" font-size="11" text-anchor="middle" fill="#6c757d">Mar</text>

                                <!-- Apr (14) -->
                                <rect x="235" y="55" width="30" height="115" rx="4" fill="var(--color-primary)" opacity="0.85" />
                                <text x="250" y="190" font-size="11" text-anchor="middle" fill="#6c757d">Apr</text>

                                <!-- May (16) -->
                                <rect x="295" y="38" width="30" height="132" rx="4" fill="var(--color-primary)" />
                                <text x="310" y="190" font-size="11" text-anchor="middle" fill="#6c757d">May</text>

                                <!-- Jun (20) -->
                                <rect x="355" y="10" width="30" height="160" rx="4" fill="var(--color-accent)" />
                                <text x="370" y="190" font-size="11" text-anchor="middle" fill="#6c757d">Jun</text>

                                <!-- Jul (16) -->
                                <rect x="415" y="38" width="30" height="132" rx="4" fill="var(--color-primary)" />
                                <text x="430" y="190" font-size="11" text-anchor="middle" fill="#6c757d">Jul</text>
                            </svg>
                        </div>

                        <div class="d-flex align-items-center justify-content-between text-muted small border-top pt-3">
                            <span><i class="bi bi-info-circle me-1"></i>Peak Admission Month: <strong>June (20 Residents)</strong></span>
                            <span class="text-success fw-semibold"><i class="bi bi-graph-up me-1"></i>Growth Rate: +14%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Chart Card: Resident Gender Distribution -->
            <div class="col-12 col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 bg-white h-100">
                    <div class="card-header bg-transparent border-bottom p-3 d-flex align-items-center justify-content-between">
                        <h5 class="fw-bold text-dark mb-0">
                            <i class="bi bi-pie-chart-fill me-2 text-warning"></i>Gender Distribution
                        </h5>
                        <span class="badge bg-light text-dark border">Demographics</span>
                    </div>
                    <div class="card-body p-4 d-flex flex-column justify-content-center">
                        
                        <!-- Visual Progress Ring / Bars -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-semibold"><i class="bi bi-gender-female text-danger me-1"></i>Female Residents</span>
                                <span class="fw-bold text-dark">138 (56.3%)</span>
                            </div>
                            <div class="progress mb-3" style="height: 12px;">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: 56.3%"></div>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-semibold"><i class="bi bi-gender-male text-primary me-1"></i>Male Residents</span>
                                <span class="fw-bold text-dark">102 (41.6%)</span>
                            </div>
                            <div class="progress mb-3" style="height: 12px;">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: 41.6%"></div>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-semibold"><i class="bi bi-gender-ambiguous text-secondary me-1"></i>Other / Unspecified</span>
                                <span class="fw-bold text-dark">5 (2.1%)</span>
                            </div>
                            <div class="progress" style="height: 12px;">
                                <div class="progress-bar bg-secondary" role="progressbar" style="width: 2.1%"></div>
                            </div>
                        </div>

                        <!-- Age Demographic Summary Box -->
                        <div class="bg-light p-3 rounded-3 border mt-2">
                            <div class="fw-semibold text-dark small mb-2"><i class="bi bi-person-vcard me-1 text-primary"></i>Age Group Breakdown:</div>
                            <div class="row text-center g-2">
                                <div class="col-4">
                                    <div class="bg-white p-2 rounded border">
                                        <div class="fw-bold text-dark">32%</div>
                                        <small class="text-muted" style="font-size: 0.72rem;">60–70 Yrs</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="bg-white p-2 rounded border">
                                        <div class="fw-bold text-primary">48%</div>
                                        <small class="text-muted" style="font-size: 0.72rem;">71–80 Yrs</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="bg-white p-2 rounded border">
                                        <div class="fw-bold text-dark">20%</div>
                                        <small class="text-muted" style="font-size: 0.72rem;">80+ Yrs</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>

        <!-- Row 2: Donations & Staff Count Distribution -->
        <div class="row g-4">
            
            <!-- Left: Donations Distribution Card -->
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 bg-white h-100">
                    <div class="card-header bg-transparent border-bottom p-3">
                        <h5 class="fw-bold text-dark mb-0">
                            <i class="bi bi-cash-stack me-2 text-success"></i>Donations Breakdown by Category
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1 small fw-semibold">
                                <span>Healthcare & Medicines</span>
                                <span class="text-success">₹10.2 Lakhs (40%)</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: 40%"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1 small fw-semibold">
                                <span>Meal & Nutrition Program</span>
                                <span class="text-primary">₹7.6 Lakhs (30%)</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: 30%"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1 small fw-semibold">
                                <span>Infrastructure & Beds</span>
                                <span class="text-warning">₹5.1 Lakhs (20%)</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: 20%"></div>
                            </div>
                        </div>

                        <div>
                            <div class="d-flex justify-content-between mb-1 small fw-semibold">
                                <span>General Emergency Fund</span>
                                <span class="text-danger">₹2.5 Lakhs (10%)</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: 10%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Staff Count Distribution Card -->
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 bg-white h-100">
                    <div class="card-header bg-transparent border-bottom p-3">
                        <h5 class="fw-bold text-dark mb-0">
                            <i class="bi bi-people-fill me-2 text-info"></i>Staff Count & Roles Breakdown
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row text-center g-3">
                            <div class="col-6 col-sm-4">
                                <div class="p-3 bg-light rounded-3 border">
                                    <i class="bi bi-person-heart text-warning fs-3 mb-1 d-block"></i>
                                    <div class="fs-4 fw-bold text-dark">28</div>
                                    <div class="text-muted small">Caretakers</div>
                                </div>
                            </div>
                            <div class="col-6 col-sm-4">
                                <div class="p-3 bg-light rounded-3 border">
                                    <i class="bi bi-hospital-fill text-info fs-3 mb-1 d-block"></i>
                                    <div class="fs-4 fw-bold text-dark">14</div>
                                    <div class="text-muted small">Doctors</div>
                                </div>
                            </div>
                            <div class="col-6 col-sm-4">
                                <div class="p-3 bg-light rounded-3 border">
                                    <i class="bi bi-shield-check text-primary fs-3 mb-1 d-block"></i>
                                    <div class="fs-4 fw-bold text-dark">12</div>
                                    <div class="text-muted small">Admins</div>
                                </div>
                            </div>
                            <div class="col-6 col-sm-4">
                                <div class="p-3 bg-light rounded-3 border">
                                    <i class="bi bi-shield-lock-fill text-danger fs-3 mb-1 d-block"></i>
                                    <div class="fs-4 fw-bold text-dark">8</div>
                                    <div class="text-muted small">Security Guards</div>
                                </div>
                            </div>
                            <div class="col-6 col-sm-4">
                                <div class="p-3 bg-light rounded-3 border">
                                    <i class="bi bi-cup-hot-fill text-success fs-3 mb-1 d-block"></i>
                                    <div class="fs-4 fw-bold text-dark">6</div>
                                    <div class="text-muted small">Kitchen Staff</div>
                                </div>
                            </div>
                            <div class="col-6 col-sm-4">
                                <div class="p-3 bg-light rounded-3 border">
                                    <i class="bi bi-stars text-secondary fs-3 mb-1 d-block"></i>
                                    <div class="fs-4 fw-bold text-dark">4</div>
                                    <div class="text-muted small">Maintenance</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </main>

</div>

<?php require_once $base_path . 'includes/footer.php'; ?>
