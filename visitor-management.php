<?php
require_once __DIR__ . '/includes/config.php';
$sn_page_title    = 'Visitor Management';
$sn_page_subtitle = 'Log visitor check-ins and check-outs';

$visitors = sn_visitors();
$statusBadge = ['Checked In' => 'success', 'Checked Out' => 'muted'];
$base_path = '';
$page_title = 'Visitor Management · ' . APP_NAME . ' Admin';
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

            <div class="sn-card">
                <div class="sn-card__head">
                    <div>
                        <div class="sn-card__title">Visitor log</div>
                        <div class="sn-card__desc">Every visit recorded with check-in and check-out times</div>
                    </div>
                    <button class="sn-btn sn-btn--primary" data-modal-open="newVisitorModal">+ Log visitor</button>
                </div>

                <div class="sn-toolbar">
                    <div class="sn-search">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                        <input type="text" placeholder="Search by visitor or resident..." data-table-search data-target="#visitorTable">
                    </div>
                    <div class="sn-tabs" data-table="#visitorTable">
                        <button class="is-active" data-tab-target="All">All</button>
                        <button data-tab-target="Checked In">Checked In</button>
                        <button data-tab-target="Checked Out">Checked Out</button>
                    </div>
                </div>

                <div class="sn-table-wrap">
                    <table class="sn-table" id="visitorTable">
                        <thead>
                            <tr>
                                <th>Visitor</th>
                                <th>Visiting</th>
                                <th>Relation</th>
                                <th>Date</th>
                                <th>Check-in</th>
                                <th>Check-out</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($visitors as $v): ?>
                            <tr data-status="<?= sn_e($v['status']) ?>">
                                <td>
                                    <div class="sn-cell-person">
                                        <div class="sn-cell-avatar"><?= sn_e(strtoupper($v['name'][0])) ?></div>
                                        <div class="sn-cell-name"><?= sn_e($v['name']) ?></div>
                                    </div>
                                </td>
                                <td><?= sn_e($v['visiting']) ?></td>
                                <td><?= sn_e($v['relation']) ?></td>
                                <td><?= sn_e($v['date']) ?></td>
                                <td><?= sn_e($v['checkin']) ?></td>
                                <td><?= sn_e($v['checkout']) ?></td>
                                <td><span class="sn-badge sn-badge--<?= $statusBadge[$v['status']] ?? 'muted' ?>"><span class="sn-badge__dot"></span><?= sn_e($v['status']) ?></span></td>
                                <td>
                                    <div class="sn-row-actions">
                                        <?php if ($v['status'] === 'Checked In'): ?>
                                        <button class="sn-icon-action" aria-label="Check out" title="Check out"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/></svg></button>
                                        <?php else: ?>
                                        <button class="sn-icon-action" aria-label="View" title="View details"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="sn-empty" style="display:none;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                        <p>No visitors found</p>
                        <p>Try a different name or filter</p>
                    </div>
                </div>
            </div>

        </main>
    </div>
</div>

<!-- Log visitor modal -->
<div class="sn-modal-overlay" id="newVisitorModal">
    <div class="sn-modal">
        <div class="sn-modal__title">Log a visitor</div>
        <div class="sn-modal__desc">Check-out time is filled in automatically when they leave.</div>
        <form method="POST" action="visitor-management.php" data-validate>
            <div class="sn-field" style="margin-bottom:14px;">
                <label for="vm_name">Visitor name <span class="req">*</span></label>
                <input type="text" id="vm_name" name="visitor_name" required>
            </div>
            <div class="sn-field" style="margin-bottom:14px;">
                <label for="vm_resident">Visiting resident <span class="req">*</span></label>
                <select id="vm_resident" name="resident_id" required>
                    <option value="">Select resident</option>
                    <?php foreach (sn_residents() as $r): if ($r['status'] === 'Active'): ?>
                    <option value="<?= sn_e($r['id']) ?>"><?= sn_e($r['name']) ?> — <?= sn_e($r['room']) ?></option>
                    <?php endif; endforeach; ?>
                </select>
            </div>
            <div class="sn-field" style="margin-bottom:6px;">
                <label for="vm_relation">Relationship <span class="req">*</span></label>
                <input type="text" id="vm_relation" name="relation" placeholder="e.g. Son, Daughter, Friend" required>
            </div>
            <div class="sn-modal__actions" style="margin-top:20px;">
                <button type="button" class="sn-btn sn-btn--ghost" data-modal-close>Cancel</button>
                <button type="submit" class="sn-btn sn-btn--primary">Check in</button>
            </div>
        </form>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
