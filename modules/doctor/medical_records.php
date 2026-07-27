<?php
/**
 * SevaNest – Doctor Patient Medical Records
 * File     : modules/doctor/medical_records.php
 * Version  : 1.0
 */

require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../config/database.php';

// Require Doctor login
require_login();

$base_path = '../../';
$page_title = 'Patient Medical Records | SevaNest';
$extra_css = [
    'assets/css/doctor.css'
];

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'doctor';
$currentPage   = 'medical_records.php';
$sn_asset_root = "../../assets";
include '../../includes/sidebar.php';
?>

<!-- ═══════════════════════════════════════════════════════════════════════
     MAIN CONTENT AREA
     ═══════════════════════════════════════════════════════════════════════ -->
<main id="sn-main-content" role="main" aria-label="Doctor medical records content">
    <div class="doctor-page-wrapper">
        
        <!-- Header Strip -->
        <div class="dr-header-strip animate-fade-in">
            <div class="d-flex align-items-center gap-3">
                <div class="res-photo" style="width: 50px; height: 50px; font-size: 1.25rem;">AK</div>
                <div>
                    <h2 class="dr-header-strip__title" style="font-size: 24px;">Mr. Arun Kapoor</h2>
                    <p style="color: var(--color-text-muted-team); margin: 2px 0 0;">Male, 78 Years Old · Room 204 · Case ID: #MD-9082</p>
                </div>
            </div>
            <div>
                <select class="select" id="patientSelect">
                    <option selected>Select Patient...</option>
                    <option>Mr. Arun Kapoor (Room 204)</option>
                    <option>Mrs. Meera Iyer (Room 118)</option>
                    <option>Mr. Vijay Verma (Room 301)</option>
                </select>
            </div>
        </div>

        <!-- Vital Signs (Horizontal Grid) -->
        <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 15px; color: var(--color-text);">Current Vital Signs</h3>
        <div class="vitals-grid animate-fade-in">
            <div class="vital-card">
                <div class="vital-icon red"><i class="bi bi-heart-pulse-fill"></i></div>
                <div class="vital-details">
                    <span>Blood Pressure</span>
                    <h4>138/85 mmHg</h4>
                </div>
            </div>
            <div class="vital-card">
                <div class="vital-icon"><i class="bi bi-activity"></i></div>
                <div class="vital-details">
                    <span>Heart Rate</span>
                    <h4>76 bpm</h4>
                </div>
            </div>
            <div class="vital-card">
                <div class="vital-icon blue"><i class="bi bi-rulers"></i></div>
                <div class="vital-details">
                    <span>Height</span>
                    <h4>174 cm</h4>
                </div>
            </div>
            <div class="vital-card">
                <div class="vital-icon blue"><i class="bi bi-speedometer2"></i></div>
                <div class="vital-details">
                    <span>Weight</span>
                    <h4>68 kg</h4>
                </div>
            </div>
            <div class="vital-card">
                <div class="vital-icon gold"><i class="bi bi-calculator"></i></div>
                <div class="vital-details">
                    <span>BMI</span>
                    <h4>22.5 (Normal)</h4>
                </div>
            </div>
        </div>

        <!-- Two Column Content -->
        <div class="grid two-col animate-fade-in">
            
            <!-- Left Side · Diagnosis & Allergies -->
            <div class="d-flex flex-column gap-4">
                
                <!-- Diagnoses Card -->
                <div class="card">
                    <div class="card-head">
                        <h3>Active Diagnoses</h3>
                    </div>
                    <ul class="tl">
                        <li><b>Post-fall Recovery:</b> Currently recovering from a minor fracture in the left hip. Needs walking aid support.</li>
                        <li><b>Type II Diabetes:</b> Controlled via diet and Metformin 500mg.</li>
                        <li><b>Mild Hypertension:</b> Managed with daily Amlodipine 5mg.</li>
                    </ul>
                </div>

                <!-- Allergies Card -->
                <div class="card">
                    <div class="card-head">
                        <h3>Known Allergies</h3>
                    </div>
                    <div class="d-flex gap-2">
                        <span class="badge red"><i class="bi bi-exclamation-octagon me-1"></i> Penicillin</span>
                        <span class="badge red"><i class="bi bi-exclamation-octagon me-1"></i> Peanuts</span>
                    </div>
                </div>

                <!-- Current Medications -->
                <div class="card">
                    <div class="card-head">
                        <h3>Current Medications</h3>
                    </div>
                    <div class="table-wrap">
                        <table class="tbl">
                            <thead>
                                <tr>
                                    <th>Medicine</th>
                                    <th>Dosage</th>
                                    <th>Frequency</th>
                                    <th>Duration</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><b>Amlodipine</b></td>
                                    <td>5 mg</td>
                                    <td>Once Daily (Morning)</td>
                                    <td>Chronic (Ongoing)</td>
                                </tr>
                                <tr>
                                    <td><b>Metformin</b></td>
                                    <td>500 mg</td>
                                    <td>Twice Daily (Meals)</td>
                                    <td>Chronic (Ongoing)</td>
                                </tr>
                                <tr>
                                    <td><b>Calcium & Vitamin D</b></td>
                                    <td>1 Tab</td>
                                    <td>Once Daily (Night)</td>
                                    <td>3 Months (Recovery)</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <!-- Right Side · Medical Timeline & Notes -->
            <div class="d-flex flex-column gap-4">
                
                <!-- Medical Timeline -->
                <div class="card">
                    <div class="card-head">
                        <h3>Medical History Timeline</h3>
                    </div>
                    <ul class="med-timeline">
                        <li class="med-timeline-item emergency">
                            <div class="med-timeline-time">24 July 2026</div>
                            <div class="med-timeline-title">Emergency incident - Fall in Room 204</div>
                            <div class="med-timeline-desc">Patient slipped in room. Minor hip fracture diagnosed. Admitted to general checkup. BP spiked to 150/90.</div>
                        </li>
                        <li class="med-timeline-item prescription">
                            <div class="med-timeline-time">18 June 2026</div>
                            <div class="med-timeline-title">Prescription modification by Dr. Watson</div>
                            <div class="med-timeline-desc">Added Metformin 500mg twice daily to stabilize blood sugar level (135 mg/dL).</div>
                        </li>
                        <li class="med-timeline-item">
                            <div class="med-timeline-time">10 May 2026</div>
                            <div class="med-timeline-title">Routine Cardiac Screening</div>
                            <div class="med-timeline-desc">ECG normal. Mild age-related fatigue. Advised diet modifications and low-salt plates.</div>
                        </li>
                    </ul>
                </div>

                <!-- Doctor Notes -->
                <div class="card">
                    <div class="card-head">
                        <h3>Doctor's Clinical Notes</h3>
                    </div>
                    <div class="mb-3">
                        <textarea class="form-control" rows="4" style="font-size: var(--font-size-sm); border: 1px solid var(--color-border); border-radius: var(--radius-medium); padding: 12px; width: 100%;" placeholder="Add clinical checkup notes..."></textarea>
                    </div>
                    <button class="btn btn-primary btn-tiny align-self-end"><i class="bi bi-save me-1"></i> Save Note</button>
                </div>

            </div>

        </div>

    </div>
</main>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
