<?php
/**
 * SevaNest – Doctor Patient Emergency Cases
 * File     : modules/doctor/emergency_cases.php
 * Version  : 1.0
 */

require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../config/database.php';

// Require Doctor login
require_login();

$base_path = '../../';
$page_title = 'Emergency Cases | SevaNest';
$extra_css = [
    'assets/css/doctor.css'
];

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'doctor';
$currentPage   = 'emergency_cases.php';
$sn_asset_root = "../../assets";
include '../../includes/sidebar.php';
?>

<!-- ═══════════════════════════════════════════════════════════════════════
     MAIN CONTENT AREA
     ═══════════════════════════════════════════════════════════════════════ -->
<main id="sn-main-content" role="main" aria-label="Doctor emergency cases content">
    <div class="doctor-page-wrapper">
        
        <!-- Header Strip -->
        <div class="dr-header-strip animate-fade-in" style="border-left: 5px solid var(--color-danger);">
            <div>
                <h2 class="dr-header-strip__title">🚨 Patient Emergency Board</h2>
                <p style="color: var(--color-text-muted-team); margin: 4px 0 0;">Critical incidents requiring immediate medical intervention or review.</p>
            </div>
        </div>

        <!-- Emergency Cases Grid -->
        <div class="grid two-col animate-fade-in">
            
            <!-- Left Side · Active Case Card -->
            <div class="card d-flex flex-column gap-3" style="border-top: 4px solid var(--color-danger);">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="d-flex align-items-center gap-3">
                        <div class="res-photo" style="width: 46px; height: 46px; background-color: rgba(231, 111, 81, 0.1); color: var(--color-danger);">AK</div>
                        <div>
                            <h4 style="margin: 0; color: var(--color-text); font-weight: 700;">Mr. Arun Kapoor</h4>
                            <span style="font-size: var(--font-size-xs); color: var(--color-text-muted-team);">Room 204 (Wing A)</span>
                        </div>
                    </div>
                    <span class="badge red">Critical Priority</span>
                </div>
                
                <hr style="margin: 0; border-top: 1px solid var(--color-border);">
                
                <!-- Treatment Notes -->
                <div>
                    <strong style="font-size: var(--font-size-sm); color: var(--color-text); display: block; margin-bottom: 4px;">Incident Details:</strong>
                    <p style="font-size: var(--font-size-sm); color: var(--color-text-muted-team); margin: 0; line-height: 1.5;">Fall detected in bathroom at 14:05 PM. Spoke of severe left hip joint pain. Vitals recorded: BP 150/90, Pulse 92.</p>
                </div>

                <div>
                    <strong style="font-size: var(--font-size-sm); color: var(--color-text); display: block; margin-bottom: 4px;">Immediate Treatment Notes:</strong>
                    <p style="font-size: var(--font-size-sm); color: var(--color-text-muted-team); margin: 0; line-height: 1.5;">Administered pain relief. Immobilized left leg. Awaiting X-Ray scan validation.</p>
                </div>

                <hr style="margin: 0; border-top: 1px solid var(--color-border);">

                <!-- Ambulance and Caregiver Details -->
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <span style="font-size: var(--font-size-xs); color: var(--color-text-muted-team); display: block;">Ambulance Status:</span>
                        <strong style="color: var(--color-success); font-size: var(--font-size-sm);"><i class="bi bi-truck me-1"></i> Dispatched (10 mins away)</strong>
                    </div>
                    <div>
                        <span style="font-size: var(--font-size-xs); color: var(--color-text-muted-team); display: block;">Caregiver Assigned:</span>
                        <strong style="color: var(--color-text); font-size: var(--font-size-sm);">Radhika Sharma</strong>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-2">
                    <button class="btn btn-danger btn-tiny w-100"><i class="bi bi-telephone-outbound me-1"></i> Contact Caregiver</button>
                    <button class="btn btn-outline-secondary btn-tiny w-100"><i class="bi bi-check2-square me-1"></i> Resolve Incident</button>
                </div>
            </div>

            <!-- Right Side · Incident History Log -->
            <div class="card">
                <div class="card-head">
                    <h3>Recent Emergency Cases Logs</h3>
                </div>
                <div class="table-wrap">
                    <table class="tbl">
                        <thead>
                            <tr>
                                <th>Incident</th>
                                <th>Severity</th>
                                <th>Date/Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><b>Fall in Room 204</b> (Mr. Kapoor)</td>
                                <td><span class="badge red">Critical</span></td>
                                <td>Today · 14:05</td>
                                <td>Active</td>
                            </tr>
                            <tr>
                                <td><b>BP Spike to 175</b> (Mrs. Iyer)</td>
                                <td><span class="badge amber">High</span></td>
                                <td>Yesterday · 11:20</td>
                                <td><span class="badge green">Resolved</span></td>
                            </tr>
                            <tr>
                                <td><b>Chest pain complaint</b> (Mr. Nair)</td>
                                <td><span class="badge red">Critical</span></td>
                                <td>2 days ago</td>
                                <td><span class="badge green">Resolved</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>
</main>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
