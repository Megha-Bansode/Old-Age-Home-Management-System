<?php
require_once __DIR__ . '/includes/config.php';
$sn_page_title    = 'Welcome back, ' . ($_SESSION['admin_name'] ?? 'Admin');
$sn_page_subtitle = "Here's what's happening across the home today.";

$stats     = sn_dashboard_stats();
$admissions = sn_recent_admissions();

$icons = [
    'users'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
    'badge'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M6 21v-2a6 6 0 0 1 12 0v2"/></svg>',
    'clipboard'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="6" y="4" width="12" height="17" rx="2"/><rect x="9" y="2.5" width="6" height="3" rx="1"/><path d="M9 11h6M9 15h6"/></svg>',
    'visitor'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
];

$statusBadge = [
    'Admitted'      => 'success',
    'Under Review'  => 'warning',
    'Pending'       => 'muted',
];
$base_path = '';
$page_title = 'Dashboard · ' . APP_NAME . ' Admin';
$extra_css = ['assets/css/admin_style.css'];
$extra_js = ['assets/js/script.js'];
$show_sidebar = true;
require_once __DIR__ . '/includes/header.php';
?>
<div class="sn-app">
    <?php include __DIR__ . '/includes/sidebar.php'; ?>

    <div class="sn-main">
        <?php include __DIR__ . '/includes/topbar.php'; ?>

        <main class="sn-content">

            <section class="sn-kpi-grid">
                <?php foreach ($stats as $i => $s):
                    $variantClass = $s['variant'] ? ' sn-kpi--' . $s['variant'] : '';
                ?>
                <div class="sn-kpi<?= $variantClass ?>">
                    <div class="sn-kpi__top">
                        <div class="sn-kpi__icon"><?= $icons[$s['icon']] ?? '' ?></div>
                        <span class="sn-kpi__delta<?= $s['down'] ? ' is-down' : '' ?>"><?= sn_e($s['delta']) ?></span>
                    </div>
                    <div class="sn-kpi__value"><?= sn_e($s['value']) ?></div>
                    <div class="sn-kpi__label"><?= sn_e($s['label']) ?></div>
                </div>
                <?php endforeach; ?>
            </section>

            <section class="sn-panel-grid">
                <div>
                    <div class="sn-card">
                        <div class="sn-card__head">
                            <div>
                                <div class="sn-card__title">Recent admissions</div>
                                <div class="sn-card__desc">Latest residents registered or awaiting approval</div>
                            </div>
                            <a href="admission-management.php" class="sn-btn sn-btn--ghost sn-btn--sm">View all</a>
                        </div>
                        <div class="sn-table-wrap">
                            <table class="sn-table">
                                <thead>
                                    <tr>
                                        <th>Resident</th>
                                        <th>Room</th>
                                        <th>Date</th>
                                        <th>Guardian</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($admissions as $a):
                                        $badge = $statusBadge[$a['status']] ?? 'muted';
                                        $initials = strtoupper($a['name'][0]);
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="sn-cell-person">
                                                <div class="sn-cell-avatar"><?= sn_e($initials) ?></div>
                                                <div>
                                                    <div class="sn-cell-name"><?= sn_e($a['name']) ?></div>
                                                    <div class="sn-cell-sub">Age <?= (int) $a['age'] ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?= sn_e($a['room']) ?></td>
                                        <td><?= sn_e($a['date']) ?></td>
                                        <td><?= sn_e($a['guardian']) ?></td>
                                        <td><span class="sn-badge sn-badge--<?= $badge ?>"><span class="sn-badge__dot"></span><?= sn_e($a['status']) ?></span></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="sn-card">
                        <div class="sn-card__head">
                            <div>
                                <div class="sn-card__title">Quick actions</div>
                                <div class="sn-card__desc">Jump straight into a task</div>
                            </div>
                        </div>
                        <div style="display:flex; flex-direction:column; gap:8px;">
                            <a href="resident-registration.php" class="sn-btn sn-btn--primary" style="justify-content:flex-start;">+ Register new resident</a>
                            <a href="admission-management.php" class="sn-btn sn-btn--ghost" style="justify-content:flex-start;">Review pending admissions</a>
                            <a href="visitor-management.php" class="sn-btn sn-btn--ghost" style="justify-content:flex-start;">Log a visitor</a>
                            <a href="staff-management.php" class="sn-btn sn-btn--ghost" style="justify-content:flex-start;">Manage staff roster</a>
                        </div>
                    </div>

                    <div class="sn-card">
                        <div class="sn-card__head">
                            <div>
                                <div class="sn-card__title">Today's snapshot</div>
                            </div>
                        </div>
                        <div style="display:flex; flex-direction:column; gap:10px;">
                            <div style="display:flex; justify-content:space-between; font-size:var(--sn-text-sm);">
                                <span style="color:var(--sn-muted);">Beds occupied</span>
                                <span style="font-weight:var(--sn-fw-semi);">86 / 100</span>
                            </div>
                            <div style="display:flex; justify-content:space-between; font-size:var(--sn-text-sm);">
                                <span style="color:var(--sn-muted);">Staff on duty</span>
                                <span style="font-weight:var(--sn-fw-semi);">24</span>
                            </div>
                            <div style="display:flex; justify-content:space-between; font-size:var(--sn-text-sm);">
                                <span style="color:var(--sn-muted);">Visitors checked in</span>
                                <span style="font-weight:var(--sn-fw-semi);">12</span>
                            </div>
                            <div style="display:flex; justify-content:space-between; font-size:var(--sn-text-sm);">
                                <span style="color:var(--sn-muted);">Discharges scheduled</span>
                                <span style="font-weight:var(--sn-fw-semi);">1</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </main>
    </div>
</div>
<?php
require_once __DIR__ . '/includes/footer.php';
?>
