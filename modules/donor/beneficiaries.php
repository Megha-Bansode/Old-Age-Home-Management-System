<?php
/**
 * SevaNest – Donor Beneficiary Stories
 * File     : modules/donor/beneficiaries.php
 * Version  : 1.0
 */

require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../config/database.php';

// Require Donor login
require_login();

$base_path = '../../';
$page_title = 'Beneficiaries | SevaNest';
$extra_css = [
    'assets/css/donor.css'
];

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'donor';
$currentPage   = 'beneficiaries.php';
$sn_asset_root = "../../assets";
include '../../includes/sidebar.php';
?>

<!-- ═══════════════════════════════════════════════════════════════════════
     MAIN CONTENT AREA
     ═══════════════════════════════════════════════════════════════════════ -->
<main id="sn-main-content" role="main" aria-label="Donor beneficiaries content">
    <div class="donor-page-wrapper">
        
        <!-- Header Strip -->
        <div class="dn-header-strip animate-fade-in">
            <div>
                <h2 class="dn-header-strip__title">Your Beneficiaries &amp; Impact Stories</h2>
                <p style="color: var(--color-text-muted-team); margin: 4px 0 0;">Meet the residents supported by your contributions and see how your donations are utilized.</p>
            </div>
        </div>

        <!-- Beneficiary Grid -->
        <div class="beneficiary-grid animate-fade-in">
            
            <!-- Beneficiary 1 -->
            <div class="beneficiary-card">
                <div class="bene-header">
                    <div class="bene-photo">AK</div>
                    <div class="bene-meta">
                        <h4>Mr. Arun Kapoor</h4>
                        <span>Age: 78 · Wing A Resident</span>
                    </div>
                </div>
                <div class="bene-body">
                    <p class="bene-story"><strong>Their Story:</strong> Slipped and suffered a hip fracture. Your medical donation funded his physiotherapy sessions and customized walking support frame, restoring his mobility.</p>
                    <div class="bene-support-list">
                        <span class="bene-support-tag medical"><i class="bi bi-heart-pulse"></i> Medical Support</span>
                        <span class="bene-support-tag shelter"><i class="bi bi-house"></i> Shelter Care</span>
                    </div>
                    <hr style="margin: 0; border-top: 1px dashed var(--color-border);">
                    <div style="font-size: var(--font-size-xs);">
                        <span style="color: var(--color-text-muted-team); display: block; margin-bottom: 2px;">Donation Utilization:</span>
                        <strong style="color: var(--color-text);">Physiotherapy &amp; Rehabilitation Equipment (100% Utilized)</strong>
                    </div>
                </div>
            </div>

            <!-- Beneficiary 2 -->
            <div class="beneficiary-card">
                <div class="bene-header">
                    <div class="bene-photo">MI</div>
                    <div class="bene-meta">
                        <h4>Mrs. Meera Iyer</h4>
                        <span>Age: 72 · Wing B Resident</span>
                    </div>
                </div>
                <div class="bene-body">
                    <p class="bene-story"><strong>Their Story:</strong> Managing severe Type II Diabetes. Your nutrition support funding provides daily diabetic-friendly meals and low-sodium organic snacks customized to her health chart.</p>
                    <div class="bene-support-list">
                        <span class="bene-support-tag food"><i class="bi bi-egg-fried"></i> Food Support</span>
                        <span class="bene-support-tag medical"><i class="bi bi-capsule"></i> Medical Supplies</span>
                    </div>
                    <hr style="margin: 0; border-top: 1px dashed var(--color-border);">
                    <div style="font-size: var(--font-size-xs);">
                        <span style="color: var(--color-text-muted-team); display: block; margin-bottom: 2px;">Donation Utilization:</span>
                        <strong style="color: var(--color-text);">Daily Diabetic Meals &amp; Insulin Supplies (100% Utilized)</strong>
                    </div>
                </div>
            </div>

        </div>

    </div>
</main>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
