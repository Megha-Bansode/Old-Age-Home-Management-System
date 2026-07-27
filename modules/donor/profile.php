<?php
/**
 * SevaNest – Donor Profile
 * File     : modules/donor/profile.php
 * Version  : 1.0
 */

require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../config/database.php';

// Require Donor login
require_login();

$base_path = '../../';
$page_title = 'Donor Profile | SevaNest';
$extra_css = [
    'assets/css/donor.css'
];

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'donor';
$currentPage   = 'profile.php';
$sn_asset_root = "../../assets";
include '../../includes/sidebar.php';
?>

<!-- ═══════════════════════════════════════════════════════════════════════
     MAIN CONTENT AREA
     ═══════════════════════════════════════════════════════════════════════ -->
<main id="sn-main-content" role="main" aria-label="Donor profile content">
    <div class="donor-page-wrapper">
        
        <!-- Header Strip -->
        <div class="dn-header-strip animate-fade-in">
            <div>
                <h2 class="dn-header-strip__title">My Donor Profile</h2>
                <p style="color: var(--color-text-muted-team); margin: 4px 0 0;">Manage your personal profile, category preferences and privacy settings.</p>
            </div>
            <div>
                <button class="btn btn-primary" id="editProfileBtn"><i class="bi bi-pencil-square me-2"></i>Edit Profile</button>
            </div>
        </div>

        <!-- Two Column Layout -->
        <div class="grid two-col animate-fade-in">
            
            <!-- Left Column: Donor Info and Contacts -->
            <div class="card d-flex flex-column gap-4 align-items-center text-center">
                <div class="res-photo" style="width: 100px; height: 100px; font-size: 2.5rem; background-color: var(--color-primary-soft-team); color: var(--color-primary);">JD</div>
                <div>
                    <h3 style="font-size: 1.5rem; font-weight: 700; color: var(--color-text); margin: 0;">John Doe</h3>
                    <p style="color: var(--color-text-muted-team); margin: 4px 0 0;">Generous Benefactor since 2025</p>
                </div>
                <hr style="margin: 0; width: 100%; border-top: 1px solid var(--color-border);">
                <div class="w-100 text-start d-flex flex-column gap-3" style="font-size: var(--font-size-sm);">
                    <div>
                        <strong style="color: var(--color-text);"><i class="bi bi-envelope me-2"></i>Email Address:</strong>
                        <span style="color: var(--color-text-muted-team); display: block; margin-top: 2px; padding-left: 24px;">j.doe@example.com</span>
                    </div>
                    <div>
                        <strong style="color: var(--color-text);"><i class="bi bi-telephone me-2"></i>Contact Phone:</strong>
                        <span style="color: var(--color-text-muted-team); display: block; margin-top: 2px; padding-left: 24px;">+1 (555) 019-2834</span>
                    </div>
                    <div>
                        <strong style="color: var(--color-text);"><i class="bi bi-geo-alt me-2"></i>Billing Address:</strong>
                        <span style="color: var(--color-text-muted-team); display: block; margin-top: 2px; padding-left: 24px;">123 Harmony Street, Suite 400, NY</span>
                    </div>
                </div>
            </div>

            <!-- Right Column: Preferences, Anonymous toggle, Communication preferences -->
            <div class="d-flex flex-column gap-4">
                
                <!-- Donation Preferences -->
                <div class="card">
                    <div class="card-head">
                        <h3>Donation Preferences</h3>
                    </div>
                    <div class="d-flex flex-column gap-3" style="font-size: var(--font-size-sm);">
                        <div>
                            <strong style="color: var(--color-text); display: block; margin-bottom: 4px;">Preferred Category:</strong>
                            <span class="badge blue">Medical Supplies &amp; Support</span>
                            <span class="badge gold">Nutritional Diet Plans</span>
                        </div>
                        <hr style="margin: 8px 0; border-top: 1px dashed var(--color-border);">
                        
                        <!-- Anonymous Donation Option -->
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong style="color: var(--color-text); display: block; margin-bottom: 2px;">Anonymous Donations Option:</strong>
                                <span style="color: var(--color-text-muted-team); font-size: var(--font-size-xs);">Keep your identity hidden from public dashboard leaderboards.</span>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="anonCheck" checked style="cursor: pointer;">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Communication Preferences -->
                <div class="card">
                    <div class="card-head">
                        <h3>Communication Preferences</h3>
                    </div>
                    <div class="d-flex flex-column gap-3" style="font-size: var(--font-size-sm);">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Email Receipts &amp; Tax Documents</span>
                            <span style="color: var(--color-success); font-weight: 600;">Enabled</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Monthly Impact Newsletters</span>
                            <span style="color: var(--color-success); font-weight: 600;">Enabled</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span>New Campaign Alerts (SMS)</span>
                            <span style="color: var(--color-text-muted-team); font-weight: 600;">Disabled</span>
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
