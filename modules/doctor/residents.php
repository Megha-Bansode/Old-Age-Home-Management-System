<?php
/**
 * SevaNest – Doctor Residents Page
 * File     : modules/doctor/residents.php
 * Version  : 1.0
 */

require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../config/database.php';

// Require Doctor login
require_login();

$base_path = '../../';
$page_title = 'Residents | SevaNest';
$extra_css = [
    'assets/css/doctor.css'
];

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'doctor';
$currentPage   = 'residents.php';
$sn_asset_root = "../../assets";
include '../../includes/sidebar.php';
?>

<!-- ═══════════════════════════════════════════════════════════════════════
     MAIN CONTENT AREA
     ═══════════════════════════════════════════════════════════════════════ -->
<main id="sn-main-content" role="main" aria-label="Doctor residents content">
    <div class="doctor-page-wrapper">
        
        <!-- Header Strip -->
        <div class="dr-header-strip animate-fade-in">
            <div>
                <h2 class="dr-header-strip__title">Resident Patient Profiles</h2>
                <p style="color: var(--color-text-muted-team); margin: 4px 0 0;">View health status updates and medical profiles for all residents.</p>
            </div>
        </div>

        <!-- Filter Toolbar -->
        <div class="toolbar">
            <div class="search inline">
                <span>🔎</span>
                <input placeholder="Search patient by name or room..." id="attSearch">
            </div>
            <select class="select">
                <option>All Wings</option>
                <option>Wing A</option>
                <option>Wing B</option>
                <option>Wing C</option>
            </select>
            <select class="select">
                <option>All Health Statuses</option>
                <option>Stable</option>
                <option>Monitoring</option>
                <option>Critical</option>
            </select>
            <select class="select">
                <option>All Assigned Doctors</option>
                <option>Dr. Watson</option>
                <option>Dr. Nair</option>
                <option>Dr. Kulkarni</option>
            </select>
        </div>

        <!-- Resident Grid (using statistical/profile card styles) -->
        <div class="grid three-col animate-fade-in">
            
            <!-- Card 1 -->
            <div class="card d-flex flex-column gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="res-photo" style="width: 48px; height: 48px; font-size: 1.1rem;">AK</div>
                    <div>
                        <h4 style="margin: 0; color: var(--color-text); font-weight: 700;">Mr. Arun Kapoor</h4>
                        <span style="font-size: var(--font-size-xs); color: var(--color-text-muted-team);">Age: 78 · Room 204 (Wing A)</span>
                    </div>
                </div>
                <hr style="margin: 0; border-top: 1px solid var(--color-border);">
                <div>
                    <span style="font-size: var(--font-size-xs); color: var(--color-text-muted-team); display: block; margin-bottom: 2px;">Assigned Doctor:</span>
                    <strong style="font-size: var(--font-size-sm); color: var(--color-text);">Dr. Robert Watson</strong>
                </div>
                <div>
                    <span style="font-size: var(--font-size-xs); color: var(--color-text-muted-team); display: block; margin-bottom: 4px;">Health Status:</span>
                    <span class="badge red"><i class="bi bi-circle-fill me-1" style="font-size: 6px;"></i> Critical Recovery</span>
                </div>
                <div class="d-flex gap-2 mt-2">
                    <a href="medical_records.php?id=1" class="btn btn-outline-primary btn-tiny w-100"><i class="bi bi-file-earmark-medical me-1"></i> Medical History</a>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="card d-flex flex-column gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="res-photo" style="width: 48px; height: 48px; font-size: 1.1rem;">MI</div>
                    <div>
                        <h4 style="margin: 0; color: var(--color-text); font-weight: 700;">Mrs. Meera Iyer</h4>
                        <span style="font-size: var(--font-size-xs); color: var(--color-text-muted-team);">Age: 72 · Room 118 (Wing B)</span>
                    </div>
                </div>
                <hr style="margin: 0; border-top: 1px solid var(--color-border);">
                <div>
                    <span style="font-size: var(--font-size-xs); color: var(--color-text-muted-team); display: block; margin-bottom: 2px;">Assigned Doctor:</span>
                    <strong style="font-size: var(--font-size-sm); color: var(--color-text);">Dr. Priya Nair</strong>
                </div>
                <div>
                    <span style="font-size: var(--font-size-xs); color: var(--color-text-muted-team); display: block; margin-bottom: 4px;">Health Status:</span>
                    <span class="badge green"><i class="bi bi-circle-fill me-1" style="font-size: 6px;"></i> Stable</span>
                </div>
                <div class="d-flex gap-2 mt-2">
                    <a href="medical_records.php?id=2" class="btn btn-outline-primary btn-tiny w-100"><i class="bi bi-file-earmark-medical me-1"></i> Medical History</a>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="card d-flex flex-column gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="res-photo" style="width: 48px; height: 48px; font-size: 1.1rem;">VV</div>
                    <div>
                        <h4 style="margin: 0; color: var(--color-text); font-weight: 700;">Mr. Vijay Verma</h4>
                        <span style="font-size: var(--font-size-xs); color: var(--color-text-muted-team);">Age: 81 · Room 301 (Wing C)</span>
                    </div>
                </div>
                <hr style="margin: 0; border-top: 1px solid var(--color-border);">
                <div>
                    <span style="font-size: var(--font-size-xs); color: var(--color-text-muted-team); display: block; margin-bottom: 2px;">Assigned Doctor:</span>
                    <strong style="font-size: var(--font-size-sm); color: var(--color-text);">Dr. Robert Watson</strong>
                </div>
                <div>
                    <span style="font-size: var(--font-size-xs); color: var(--color-text-muted-team); display: block; margin-bottom: 4px;">Health Status:</span>
                    <span class="badge green"><i class="bi bi-circle-fill me-1" style="font-size: 6px;"></i> Stable</span>
                </div>
                <div class="d-flex gap-2 mt-2">
                    <a href="medical_records.php?id=3" class="btn btn-outline-primary btn-tiny w-100"><i class="bi bi-file-earmark-medical me-1"></i> Medical History</a>
                </div>
            </div>

            <!-- Card 4 -->
            <div class="card d-flex flex-column gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="res-photo" style="width: 48px; height: 48px; font-size: 1.1rem;">LR</div>
                    <div>
                        <h4 style="margin: 0; color: var(--color-text); font-weight: 700;">Mrs. Latha Rao</h4>
                        <span style="font-size: var(--font-size-xs); color: var(--color-text-muted-team);">Age: 75 · Room 302 (Wing C)</span>
                    </div>
                </div>
                <hr style="margin: 0; border-top: 1px solid var(--color-border);">
                <div>
                    <span style="font-size: var(--font-size-xs); color: var(--color-text-muted-team); display: block; margin-bottom: 2px;">Assigned Doctor:</span>
                    <strong style="font-size: var(--font-size-sm); color: var(--color-text);">Dr. Robert Watson</strong>
                </div>
                <div>
                    <span style="font-size: var(--font-size-xs); color: var(--color-text-muted-team); display: block; margin-bottom: 4px;">Health Status:</span>
                    <span class="badge blue"><i class="bi bi-circle-fill me-1" style="font-size: 6px;"></i> Monitoring</span>
                </div>
                <div class="d-flex gap-2 mt-2">
                    <a href="medical_records.php?id=4" class="btn btn-outline-primary btn-tiny w-100"><i class="bi bi-file-earmark-medical me-1"></i> Medical History</a>
                </div>
            </div>

            <!-- Card 5 -->
            <div class="card d-flex flex-column gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="res-photo" style="width: 48px; height: 48px; font-size: 1.1rem;">SN</div>
                    <div>
                        <h4 style="margin: 0; color: var(--color-text); font-weight: 700;">Mr. Suresh Nair</h4>
                        <span style="font-size: var(--font-size-xs); color: var(--color-text-muted-team);">Age: 69 · Room 210 (Wing B)</span>
                    </div>
                </div>
                <hr style="margin: 0; border-top: 1px solid var(--color-border);">
                <div>
                    <span style="font-size: var(--font-size-xs); color: var(--color-text-muted-team); display: block; margin-bottom: 2px;">Assigned Doctor:</span>
                    <strong style="font-size: var(--font-size-sm); color: var(--color-text);">Dr. Rohan Kulkarni</strong>
                </div>
                <div>
                    <span style="font-size: var(--font-size-xs); color: var(--color-text-muted-team); display: block; margin-bottom: 4px;">Health Status:</span>
                    <span class="badge green"><i class="bi bi-circle-fill me-1" style="font-size: 6px;"></i> Stable</span>
                </div>
                <div class="d-flex gap-2 mt-2">
                    <a href="medical_records.php?id=5" class="btn btn-outline-primary btn-tiny w-100"><i class="bi bi-file-earmark-medical me-1"></i> Medical History</a>
                </div>
            </div>

        </div>

    </div>
</main>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
