<?php
/**
 * SevaNest – Doctor Prescriptions
 * File     : modules/doctor/prescriptions.php
 * Version  : 1.0
 */

require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../config/database.php';

// Require Doctor login
require_login();

$base_path = '../../';
$page_title = 'Patient Prescriptions | SevaNest';
$extra_css = [
    'assets/css/doctor.css'
];

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'doctor';
$currentPage   = 'prescriptions.php';
$sn_asset_root = "../../assets";
include '../../includes/sidebar.php';
?>

<!-- ═══════════════════════════════════════════════════════════════════════
     MAIN CONTENT AREA
     ═══════════════════════════════════════════════════════════════════════ -->
<main id="sn-main-content" role="main" aria-label="Doctor prescriptions content">
    <div class="doctor-page-wrapper">
        
        <!-- Header Strip -->
        <div class="dr-header-strip animate-fade-in">
            <div>
                <h2 class="dr-header-strip__title">Patient Prescriptions</h2>
                <p style="color: var(--color-text-muted-team); margin: 4px 0 0;">Create, edit and manage prescriptions for residents.</p>
            </div>
            <div>
                <button class="btn btn-primary" id="newPresBtn"><i class="bi bi-plus-circle me-2"></i>New Prescription</button>
            </div>
        </div>

        <!-- Filter Toolbar -->
        <div class="toolbar">
            <div class="search inline">
                <span>🔎</span>
                <input placeholder="Search prescription by patient..." id="attSearch">
            </div>
            <select class="select">
                <option>All Prescriptions</option>
                <option>Active</option>
                <option>Completed</option>
                <option>Temporary</option>
            </select>
        </div>

        <!-- Prescription Grid -->
        <div class="prescription-grid animate-fade-in">
            
            <!-- Card 1 -->
            <div class="prescription-card">
                <div class="pres-card-header">
                    <h4>Mr. Arun Kapoor</h4>
                    <span class="badge red">Active</span>
                </div>
                <div class="pres-card-body">
                    <p style="font-size: var(--font-size-xs); color: var(--color-text-muted-team); margin-bottom: 12px;"><i class="bi bi-person me-1"></i> Prescribed by: Dr. Watson · 24 Jul 2026</p>
                    <div class="pres-med-row">
                        <div>
                            <span class="pres-med-name">Amlodipine (BP)</span>
                            <div class="pres-med-instruction">Take with water after breakfast</div>
                        </div>
                        <div style="text-align: right;">
                            <strong>5 mg · 1 Tab</strong>
                            <div>Chronic</div>
                        </div>
                    </div>
                    <div class="pres-med-row">
                        <div>
                            <span class="pres-med-name">Metformin (Diabetes)</span>
                            <div class="pres-med-instruction">Take twice daily with meals</div>
                        </div>
                        <div style="text-align: right;">
                            <strong>500 mg · 1 Tab</strong>
                            <div>Chronic</div>
                        </div>
                    </div>
                </div>
                <div class="pres-card-footer">
                    <button class="btn tiny btn-outline-primary"><i class="bi bi-download"></i> Download PDF</button>
                    <button class="btn tiny btn-outline-secondary"><i class="bi bi-printer"></i> Print Rx</button>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="prescription-card">
                <div class="pres-card-header">
                    <h4>Mrs. Meera Iyer</h4>
                    <span class="badge red">Active</span>
                </div>
                <div class="pres-card-body">
                    <p style="font-size: var(--font-size-xs); color: var(--color-text-muted-team); margin-bottom: 12px;"><i class="bi bi-person me-1"></i> Prescribed by: Dr. Watson · 18 Jun 2026</p>
                    <div class="pres-med-row">
                        <div>
                            <span class="pres-med-name">Metformin (Diabetes)</span>
                            <div class="pres-med-instruction">Take twice daily with meals</div>
                        </div>
                        <div style="text-align: right;">
                            <strong>500 mg · 1 Tab</strong>
                            <div>Chronic</div>
                        </div>
                    </div>
                    <div class="pres-med-row">
                        <div>
                            <span class="pres-med-name">Atorvastatin (Cholesterol)</span>
                            <div class="pres-med-instruction">Take once daily at bedtime</div>
                        </div>
                        <div style="text-align: right;">
                            <strong>10 mg · 1 Tab</strong>
                            <div>Chronic</div>
                        </div>
                    </div>
                </div>
                <div class="pres-card-footer">
                    <button class="btn tiny btn-outline-primary"><i class="bi bi-download"></i> Download PDF</button>
                    <button class="btn tiny btn-outline-secondary"><i class="bi bi-printer"></i> Print Rx</button>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="prescription-card">
                <div class="pres-card-header">
                    <h4>Mr. Vijay Verma</h4>
                    <span class="badge green">Completed</span>
                </div>
                <div class="pres-card-body">
                    <p style="font-size: var(--font-size-xs); color: var(--color-text-muted-team); margin-bottom: 12px;"><i class="bi bi-person me-1"></i> Prescribed by: Dr. Watson · 10 May 2026</p>
                    <div class="pres-med-row">
                        <div>
                            <span class="pres-med-name">Amoxicillin (Antibiotic)</span>
                            <div class="pres-med-instruction">Take 3 times daily after meals</div>
                        </div>
                        <div style="text-align: right;">
                            <strong>500 mg · 1 Capsule</strong>
                            <div>7 Days</div>
                        </div>
                    </div>
                </div>
                <div class="pres-card-footer">
                    <button class="btn tiny btn-outline-primary" disabled><i class="bi bi-download"></i> Download PDF</button>
                    <button class="btn tiny btn-outline-secondary" disabled><i class="bi bi-printer"></i> Print Rx</button>
                </div>
            </div>

        </div>

    </div>
</main>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
