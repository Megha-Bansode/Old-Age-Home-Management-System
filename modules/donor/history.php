<?php
/**
 * SevaNest – Donor Impact & History
 * File     : modules/donor/history.php
 * Version  : 1.0
 */

require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../config/database.php';

// Require Donor login
require_login();

$base_path = '../../';
$page_title = 'Donation History & Impact | SevaNest';
$extra_css = [
    'assets/css/donor.css'
];

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'donor';
$currentPage   = 'history.php';
$sn_asset_root = "../../assets";
include '../../includes/sidebar.php';
?>

<!-- ═══════════════════════════════════════════════════════════════════════
     MAIN CONTENT AREA
     ═══════════════════════════════════════════════════════════════════════ -->
<main id="sn-main-content" role="main" aria-label="Donor history content">
    <div class="donor-page-wrapper">
        
        <!-- Header Strip -->
        <div class="dn-header-strip animate-fade-in">
            <div>
                <h2 class="dn-header-strip__title">My Philanthropy Timeline</h2>
                <p style="color: var(--color-text-muted-team); margin: 4px 0 0;">Trace your history of campaign participation and total impact milestones.</p>
            </div>
        </div>

        <!-- Two Column Layout -->
        <div class="grid two-col animate-fade-in">
            
            <!-- Left Side: History Timeline -->
            <div class="card">
                <div class="card-head">
                    <h3>Philanthropic History Timeline</h3>
                </div>
                <ul class="donor-timeline">
                    <li class="donor-timeline-item campaign">
                        <div class="donor-timeline-time">24 July 2026</div>
                        <div class="donor-timeline-title">Participated in Winter Clothing Drive</div>
                        <div class="donor-timeline-desc">Contributed $250.00 to procure winter garments and blankets for Wing A senior residents.</div>
                    </li>
                    <li class="donor-timeline-item">
                        <div class="donor-timeline-time">15 July 2026</div>
                        <div class="donor-timeline-title">Monthly Welfare Support Cleared</div>
                        <div class="donor-timeline-desc">Cleared $500.00 monthly subscription targeting general dietary requirements and fresh fruit plans.</div>
                    </li>
                    <li class="donor-timeline-item campaign">
                        <div class="donor-timeline-time">02 June 2026</div>
                        <div class="donor-timeline-title">Contributed to ICU Equipment Setup</div>
                        <div class="donor-timeline-desc">Contributed $1,500.00 to purchase ECG monitors and oxygen concentrators for the on-site clinic.</div>
                    </li>
                </ul>
            </div>

            <!-- Right Side: Impact Summary & Milestones -->
            <div class="d-flex flex-column gap-4">
                
                <!-- Impact Summary -->
                <div class="card" style="border-top: 4px solid var(--color-accent);">
                    <div class="card-head">
                        <h3>Impact Milestones</h3>
                    </div>
                    <div class="d-flex flex-column gap-3" style="font-size: var(--font-size-sm);">
                        <div style="background: var(--color-secondary); padding: 15px; border-radius: var(--radius-medium);">
                            <strong style="color: var(--color-primary); font-size: var(--font-size-lg); display: block; margin-bottom: 2px;">15 Residents Helped</strong>
                            <span style="color: var(--color-text-muted-team);">Your donations funded medicine, dietary plans, or mobility support aids for 15 elderly residents.</span>
                        </div>
                        <div style="background: var(--color-secondary); padding: 15px; border-radius: var(--radius-medium);">
                            <strong style="color: var(--color-accent); font-size: var(--font-size-lg); display: block; margin-bottom: 2px;">4 Active Campaigns Supported</strong>
                            <span style="color: var(--color-text-muted-team);">You have actively funded 4 medical and utility setups inside SevaNest this year.</span>
                        </div>
                    </div>
                </div>

                <!-- Monthly contribution overview -->
                <div class="card">
                    <div class="card-head">
                        <h3>Monthly Summary</h3>
                    </div>
                    <ul class="tl">
                        <li><b>July 2026:</b> Total donated: $750.00 across 2 categories.</li>
                        <li><b>June 2026:</b> Total donated: $1,500.00 across 1 category.</li>
                        <li><b>May 2026:</b> Total donated: $500.00 general subscription.</li>
                    </ul>
                </div>

            </div>

        </div>

    </div>
</main>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
