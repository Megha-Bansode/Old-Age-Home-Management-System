<?php
/**
 * SevaNest – Donor Campaigns
 * File     : modules/donor/campaigns.php
 * Version  : 1.0
 */

require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../config/database.php';

// Require Donor login
require_login();

$base_path = '../../';
$page_title = 'Active Campaigns | SevaNest';
$extra_css = [
    'assets/css/donor.css'
];

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'donor';
$currentPage   = 'campaigns.php';
$sn_asset_root = "../../assets";
include '../../includes/sidebar.php';
?>

<!-- ═══════════════════════════════════════════════════════════════════════
     MAIN CONTENT AREA
     ═══════════════════════════════════════════════════════════════════════ -->
<main id="sn-main-content" role="main" aria-label="Donor campaigns content">
    <div class="donor-page-wrapper">
        
        <!-- Header Strip -->
        <div class="dn-header-strip animate-fade-in">
            <div>
                <h2 class="dn-header-strip__title">Active Support Campaigns</h2>
                <p style="color: var(--color-text-muted-team); margin: 4px 0 0;">Contribute to ongoing programs tailored to optimize residents' standard of life.</p>
            </div>
        </div>

        <!-- Campaign Cards Grid -->
        <div class="campaign-grid animate-fade-in">
            
            <!-- Campaign 1 -->
            <div class="campaign-card">
                <div class="camp-img-placeholder">🏥</div>
                <div class="camp-body">
                    <h4 class="camp-title">Senior ICU Facility Setup</h4>
                    <p class="camp-desc">Establishing a 4-bed ICU setup inside SevaNest to deal with critical cardiovascular and breathing failures in-house.</p>
                    <div class="camp-progress-container">
                        <div class="camp-progress-bar">
                            <div class="camp-progress-fill" style="width: 73%;"></div>
                        </div>
                        <div class="camp-stats-row">
                            <span>Raised: $18,450</span>
                            <span>Goal: $25,000</span>
                        </div>
                    </div>
                </div>
                <div class="camp-footer">
                    <span class="camp-days-left">14 Days Left</span>
                    <div>
                        <button class="btn btn-outline-primary btn-tiny me-1">Details</button>
                        <button class="btn btn-primary btn-tiny">Donate</button>
                    </div>
                </div>
            </div>

            <!-- Campaign 2 -->
            <div class="campaign-card">
                <div class="camp-img-placeholder">🧥</div>
                <div class="camp-body">
                    <h4 class="camp-title">Winter Clothing &amp; Blankets</h4>
                    <p class="camp-desc">Procuring winter coats, heavy thermal sheets, and indoor heaters for all resident rooms ahead of the winter season.</p>
                    <div class="camp-progress-container">
                        <div class="camp-progress-bar">
                            <div class="camp-progress-fill" style="width: 84%;"></div>
                        </div>
                        <div class="camp-stats-row">
                            <span>Raised: $4,200</span>
                            <span>Goal: $5,000</span>
                        </div>
                    </div>
                </div>
                <div class="camp-footer">
                    <span class="camp-days-left">8 Days Left</span>
                    <div>
                        <button class="btn btn-outline-primary btn-tiny me-1">Details</button>
                        <button class="btn btn-primary btn-tiny">Donate</button>
                    </div>
                </div>
            </div>

            <!-- Campaign 3 -->
            <div class="campaign-card">
                <div class="camp-img-placeholder">🥦</div>
                <div class="camp-body">
                    <h4 class="camp-title">Nutrition Diet Enhancement</h4>
                    <p class="camp-desc">Providing diabetic-safe low sodium food choices, organic supplements, and fresh fruit arrays daily.</p>
                    <div class="camp-progress-container">
                        <div class="camp-progress-bar">
                            <div class="camp-progress-fill" style="width: 32%;"></div>
                        </div>
                        <div class="camp-stats-row">
                            <span>Raised: $3,200</span>
                            <span>Goal: $10,000</span>
                        </div>
                    </div>
                </div>
                <div class="camp-footer">
                    <span class="camp-days-left">30 Days Left</span>
                    <div>
                        <button class="btn btn-outline-primary btn-tiny me-1">Details</button>
                        <button class="btn btn-primary btn-tiny">Donate</button>
                    </div>
                </div>
            </div>

        </div>

    </div>
</main>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
