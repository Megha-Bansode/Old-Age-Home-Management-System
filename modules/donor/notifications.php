<?php
/**
 * SevaNest – Donor Notifications
 * File     : modules/donor/notifications.php
 * Version  : 1.0
 */

require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../config/database.php';

// Require Donor login
require_login();

$base_path = '../../';
$page_title = 'Donor Notifications | SevaNest';
$extra_css = [
    'assets/css/donor.css'
];

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'donor';
$currentPage   = 'notifications.php';
$sn_asset_root = "../../assets";
include '../../includes/sidebar.php';
?>

<!-- ═══════════════════════════════════════════════════════════════════════
     MAIN CONTENT AREA
     ═══════════════════════════════════════════════════════════════════════ -->
<main id="sn-main-content" role="main" aria-label="Donor notifications content">
    <div class="donor-page-wrapper">
        
        <!-- Header Strip -->
        <div class="dn-header-strip animate-fade-in">
            <div>
                <h2 class="dn-header-strip__title">My Notifications</h2>
                <p style="color: var(--color-text-muted-team); margin: 4px 0 0;">Stay updated with campaign milestones, thank-you messages, and tax invoices.</p>
            </div>
            <div>
                <button class="btn btn-outline-primary btn-tiny"><i class="bi bi-check-all me-1"></i>Mark all as read</button>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="card animate-fade-in">
            <ul class="notif-list">
                
                <!-- Thank You Alert -->
                <li>
                    <span class="dot alert"></span>
                    <div class="w-100">
                        <div class="d-flex justify-content-between align-items-start">
                            <strong>Thank-You message from Mrs. Iyer</strong>
                            <em style="font-size: var(--font-size-xs); color: var(--color-text-muted-team);">1 hour ago</em>
                        </div>
                        <p style="font-size: var(--font-size-sm); color: var(--color-text-muted-team); margin: 4px 0 0; line-height: 1.5;">"Thank you for backing the medical care support and diabetic health plan. It has helped stabilize my parameters tremendously!"</p>
                    </div>
                </li>
                
                <hr style="margin: 10px 0; border-top: 1px solid var(--color-border);">

                <!-- Receipt Alert -->
                <li>
                    <span class="dot gold"></span>
                    <div class="w-100">
                        <div class="d-flex justify-content-between align-items-start">
                            <strong>Tax Exemption Receipt Generated</strong>
                            <em style="font-size: var(--font-size-xs); color: var(--color-text-muted-team);">Yesterday</em>
                        </div>
                        <p style="font-size: var(--font-size-sm); color: var(--color-text-muted-team); margin: 4px 0 0; line-height: 1.5;">Your receipt #REC-789021 for the Winter Clothing donation ($250.00) has been generated. You can download it under the Receipts panel.</p>
                    </div>
                </li>

                <hr style="margin: 10px 0; border-top: 1px solid var(--color-border);">

                <!-- Donation Confirmation -->
                <li>
                    <span class="dot"></span>
                    <div class="w-100">
                        <div class="d-flex justify-content-between align-items-start">
                            <strong>Donation Confirmation ($250.00)</strong>
                            <em style="font-size: var(--font-size-xs); color: var(--color-text-muted-team);">24 Jul 2026</em>
                        </div>
                        <p style="font-size: var(--font-size-sm); color: var(--color-text-muted-team); margin: 4px 0 0; line-height: 1.5;">Your contribution to the Winter Clothing &amp; Blankets drive has been successfully processed. Thank you for your support!</p>
                    </div>
                </li>

                <hr style="margin: 10px 0; border-top: 1px solid var(--color-border);">

                <!-- New Campaign Announcement -->
                <li>
                    <span class="dot pink"></span>
                    <div class="w-100">
                        <div class="d-flex justify-content-between align-items-start">
                            <strong>New Campaign Launch: "Nutrition Diet Enhancement"</strong>
                            <em style="font-size: var(--font-size-xs); color: var(--color-text-muted-team);">3 days ago</em>
                        </div>
                        <p style="font-size: var(--font-size-sm); color: var(--color-text-muted-team); margin: 4px 0 0; line-height: 1.5;">Help us supply low sodium fresh organic options for residents by contributing to our newly launched wellness campaigns.</p>
                    </div>
                </li>

                <hr style="margin: 10px 0; border-top: 1px solid var(--color-border);">

                <!-- Campaign Update -->
                <li>
                    <span class="dot gold"></span>
                    <div class="w-100">
                        <div class="d-flex justify-content-between align-items-start">
                            <strong>Campaign Milestone: ICU Facility Setup (70% Complete)</strong>
                            <em style="font-size: var(--font-size-xs); color: var(--color-text-muted-team);">5 days ago</em>
                        </div>
                        <p style="font-size: var(--font-size-sm); color: var(--color-text-muted-team); margin: 4px 0 0; line-height: 1.5;">With your support, we have raised $18,450 out of $25,000 for the senior ward setup. Procurement for clinical devices has commenced.</p>
                    </div>
                </li>

            </ul>
        </div>

    </div>
</main>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
