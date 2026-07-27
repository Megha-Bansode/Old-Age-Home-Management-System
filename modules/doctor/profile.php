<?php
/**
 * SevaNest – Doctor Profile Page
 * File     : modules/doctor/profile.php
 * Version  : 1.0
 */

require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../config/database.php';

// Require Doctor login
require_login();

$base_path = '../../';
$page_title = 'Doctor Profile | SevaNest';
$extra_css = [
    'assets/css/doctor.css'
];

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'doctor';
$currentPage   = 'profile.php';
$sn_asset_root = "../../assets";
include '../../includes/sidebar.php';
?>

<!-- ═══════════════════════════════════════════════════════════════════════
     MAIN CONTENT AREA
     ═══════════════════════════════════════════════════════════════════════ -->
<main id="sn-main-content" role="main" aria-label="Doctor profile content">
    <div class="doctor-page-wrapper">
        
        <!-- Header Strip -->
        <div class="dr-header-strip animate-fade-in">
            <div>
                <h2 class="dr-header-strip__title">My Professional Profile</h2>
                <p style="color: var(--color-text-muted-team); margin: 4px 0 0;">Manage your qualifications, contact details and availability slots.</p>
            </div>
            <div>
                <button class="btn btn-primary" id="editProfileBtn"><i class="bi bi-pencil-square me-2"></i>Edit Profile</button>
            </div>
        </div>

        <!-- Profile Card & Details Grid -->
        <div class="grid two-col animate-fade-in">
            
            <!-- Left Side · Doctor Profile details -->
            <div class="card d-flex flex-column gap-4 text-center align-items-center">
                <div class="res-photo" style="width: 100px; height: 100px; font-size: 2.5rem; background-color: var(--color-primary-soft-team); color: var(--color-primary);">RW</div>
                <div>
                    <h3 style="font-size: 1.5rem; font-weight: 700; color: var(--color-text); margin: 0;">Dr. Robert Watson</h3>
                    <p style="color: var(--color-text-muted-team); margin: 4px 0 0;">Senior Cardiologist &amp; Medical Officer</p>
                </div>
                <hr style="margin: 0; width: 100%; border-top: 1px solid var(--color-border);">
                <div class="w-100 text-start d-flex flex-column gap-3" style="font-size: var(--font-size-sm);">
                    <div>
                        <strong style="color: var(--color-text);"><i class="bi bi-envelope-at me-2"></i>Email Address:</strong>
                        <span style="color: var(--color-text-muted-team); display: block; margin-top: 2px; padding-left: 24px;">r.watson@sevanest.org</span>
                    </div>
                    <div>
                        <strong style="color: var(--color-text);"><i class="bi bi-telephone me-2"></i>Contact Number:</strong>
                        <span style="color: var(--color-text-muted-team); display: block; margin-top: 2px; padding-left: 24px;">+91 98901 23456</span>
                    </div>
                    <div>
                        <strong style="color: var(--color-text);"><i class="bi bi-geo-alt me-2"></i>Office Location:</strong>
                        <span style="color: var(--color-text-muted-team); display: block; margin-top: 2px; padding-left: 24px;">Clinic block, Ground Floor (A Wing)</span>
                    </div>
                </div>
            </div>

            <!-- Right Side · Qualifications & Experience -->
            <div class="d-flex flex-column gap-4">
                
                <!-- Qualifications & Experience -->
                <div class="card">
                    <div class="card-head">
                        <h3>Qualifications &amp; Background</h3>
                    </div>
                    <div class="d-flex flex-column gap-3" style="font-size: var(--font-size-sm);">
                        <div>
                            <strong style="color: var(--color-text); display: block; margin-bottom: 2px;">Education:</strong>
                            <span style="color: var(--color-text-muted-team);">MD in General Medicine &amp; Cardiology (AIMS Medical College)</span>
                        </div>
                        <div>
                            <strong style="color: var(--color-text); display: block; margin-bottom: 2px;">Specialty Department:</strong>
                            <span style="color: var(--color-text-muted-team);">Cardiovascular health, Hypertension management &amp; Geriatric Wellness</span>
                        </div>
                        <div>
                            <strong style="color: var(--color-text); display: block; margin-bottom: 2px;">Experience:</strong>
                            <span style="color: var(--color-text-muted-team);">14+ Years practicing in cardiovascular &amp; general medicine</span>
                        </div>
                    </div>
                </div>

                <!-- Shift Availability -->
                <div class="card">
                    <div class="card-head">
                        <h3>Shift &amp; Duty Availability</h3>
                    </div>
                    <div class="d-flex flex-column gap-3" style="font-size: var(--font-size-sm);">
                        <div>
                            <strong style="color: var(--color-text); display: block; margin-bottom: 4px;">OPD Timings:</strong>
                            <span class="badge blue">Mon, Wed, Fri (09:00 AM - 01:00 PM)</span>
                        </div>
                        <div>
                            <strong style="color: var(--color-text); display: block; margin-bottom: 4px;">Resident Home Visits:</strong>
                            <span class="badge gold">Tue, Thu (11:00 AM - 03:00 PM)</span>
                        </div>
                        <div>
                            <strong style="color: var(--color-text); display: block; margin-bottom: 4px;">On-Call Duty Hours:</strong>
                            <span class="badge red">Emergency Calls 24/7 (Wing A &amp; B)</span>
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
