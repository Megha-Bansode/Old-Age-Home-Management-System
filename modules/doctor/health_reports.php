<?php
/**
 * SevaNest – Doctor Patient Health Reports
 * File     : modules/doctor/health_reports.php
 * Version  : 1.0
 */

require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../config/database.php';

// Require Doctor login
require_login();

$base_path = '../../';
$page_title = 'Patient Health Reports | SevaNest';
$extra_css = [
    'assets/css/doctor.css'
];

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'doctor';
$currentPage   = 'health_reports.php';
$sn_asset_root = "../../assets";
include '../../includes/sidebar.php';
?>

<!-- ═══════════════════════════════════════════════════════════════════════
     MAIN CONTENT AREA
     ═══════════════════════════════════════════════════════════════════════ -->
<main id="sn-main-content" role="main" aria-label="Doctor health reports content">
    <div class="doctor-page-wrapper">
        
        <!-- Header Strip -->
        <div class="dr-header-strip animate-fade-in">
            <div>
                <h2 class="dr-header-strip__title">Patient Health Statistics & Lab Reports</h2>
                <p style="color: var(--color-text-muted-team); margin: 4px 0 0;">View recovery progress metrics, disease trends and laboratory test summaries.</p>
            </div>
        </div>

        <!-- Charts Placeholders (using statistical overrides) -->
        <div class="grid two-col animate-fade-in">
            
            <!-- Recovery Progress Chart -->
            <div class="card">
                <div class="card-head">
                    <h3>Overall Patient Recovery Rate (Weekly)</h3>
                </div>
                <div class="report-placeholder-chart">
                    <div class="report-bars">
                        <span style="height: 50%;"></span>
                        <span style="height: 65%;"></span>
                        <span style="height: 80%;"></span>
                        <span style="height: 75%;"></span>
                        <span style="height: 90%;"></span>
                    </div>
                    <div class="chart-legend"><em>Avg Recovery rate: 82% over the last 5 weeks</em></div>
                </div>
            </div>

            <!-- Disease Statistics Card -->
            <div class="card">
                <div class="card-head">
                    <h3>Chronic Diseases distribution</h3>
                </div>
                <div class="table-wrap">
                    <table class="tbl">
                        <thead>
                            <tr>
                                <th>Condition</th>
                                <th>Resident Count</th>
                                <th>Trend State</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><b>Hypertension</b></td>
                                <td>24 Patients</td>
                                <td><span class="badge green">Stable</span></td>
                            </tr>
                            <tr>
                                <td><b>Diabetes Type II</b></td>
                                <td>18 Patients</td>
                                <td><span class="badge blue">Controlled</span></td>
                            </tr>
                            <tr>
                                <td><b>Arthritis / Mobility Problems</b></td>
                                <td>14 Patients</td>
                                <td><span class="badge amber">Increasing</span></td>
                            </tr>
                            <tr>
                                <td><b>Mild Dementia</b></td>
                                <td>8 Patients</td>
                                <td><span class="badge green">Stable</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Monthly & Lab Reports -->
        <div class="card animate-fade-in mt-4">
            <div class="card-head">
                <h3>Recent Laboratory & Lab Reports</h3>
            </div>
            <div class="table-wrap">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Report Name</th>
                            <th>Patient</th>
                            <th>Physician</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><b>Complete Blood Count (CBC) Report</b></td>
                            <td>Mr. Arun Kapoor</td>
                            <td>Dr. Robert Watson</td>
                            <td>22 Jul 2026</td>
                            <td><span class="badge green">Normal</span></td>
                            <td>
                                <button class="btn btn-outline-primary btn-tiny"><i class="bi bi-file-earmark-pdf"></i> Download PDF</button>
                            </td>
                        </tr>
                        <tr>
                            <td><b>Lipid Profile Test</b></td>
                            <td>Mrs. Meera Iyer</td>
                            <td>Dr. Robert Watson</td>
                            <td>18 Jul 2026</td>
                            <td><span class="badge amber">Borderline High</span></td>
                            <td>
                                <button class="btn btn-outline-primary btn-tiny"><i class="bi bi-file-earmark-pdf"></i> Download PDF</button>
                            </td>
                        </tr>
                        <tr>
                            <td><b>Electrocardiogram (ECG) Chart</b></td>
                            <td>Mr. Suresh Nair</td>
                            <td>Dr. Rohan Kulkarni</td>
                            <td>10 Jun 2026</td>
                            <td><span class="badge green">Stable</span></td>
                            <td>
                                <button class="btn btn-outline-primary btn-tiny"><i class="bi bi-file-earmark-pdf"></i> Download PDF</button>
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
