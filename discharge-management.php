<?php
require_once __DIR__ . '/includes/config.php';
$sn_page_title    = 'Discharge Management';
$sn_page_subtitle = 'Process and review resident discharges';

$discharges = sn_discharges();
$statusBadge = ['Scheduled' => 'warning', 'Completed' => 'success'];
$base_path = '';
$page_title = 'Discharge Management · ' . APP_NAME . ' Admin';
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
                        <div class="sn-card__title">Discharge records</div>
                        <div class="sn-card__desc">Every resident discharge, with reason and handover details</div>
                    </div>
                    <button class="sn-btn sn-btn--primary" data-modal-open="newDischargeModal">+ Initiate discharge</button>
                </div>

                <div class="sn-toolbar">
                    <div class="sn-search">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                        <input type="text" placeholder="Search by resident name..." data-table-search data-target="#dischargeTable">
                    </div>
                    <div class="sn-filters">
                        <select data-table-filter data-target="#dischargeTable">
                            <option value="All">All statuses</option>
                            <option value="Scheduled">Scheduled</option>
                            <option value="Completed">Completed</option>
                        </select>
                    </div>
                </div>

                <div class="sn-table-wrap">
                    <table class="sn-table" id="dischargeTable">
                        <thead>
                            <tr>
                                <th>Resident</th>
                                <th>Room</th>
                                <th>Discharge date</th>
                                <th>Reason</th>
                                <th>Handed over to</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($discharges as $d): ?>
                            <tr data-status="<?= sn_e($d['status']) ?>">
                                <td>
                                    <div class="sn-cell-person">
                                        <div class="sn-cell-avatar"><?= sn_e(strtoupper($d['name'][0])) ?></div>
                                        <div class="sn-cell-name"><?= sn_e($d['name']) ?></div>
                                    </div>
                                </td>
                                <td><?= sn_e($d['room']) ?></td>
                                <td><?= sn_e($d['date']) ?></td>
                                <td><?= sn_e($d['reason']) ?></td>
                                <td><?= sn_e($d['handedTo']) ?></td>
                                <td><span class="sn-badge sn-badge--<?= $statusBadge[$d['status']] ?? 'muted' ?>"><span class="sn-badge__dot"></span><?= sn_e($d['status']) ?></span></td>
                                <td>
                                    <div class="sn-row-actions">
                                        <button class="sn-icon-action" aria-label="View certificate" title="Discharge summary"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg></button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="sn-empty" style="display:none;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                        <p>No discharge records found</p>
                        <p>Try a different name or filter</p>
                    </div>
                </div>
            </div>

        </main>
    </div>
</div>

<!-- New discharge modal -->
<div class="sn-modal-overlay" id="newDischargeModal">
    <div class="sn-modal">
        <div class="sn-modal__title">Initiate discharge</div>
        <div class="sn-modal__desc">This starts the discharge workflow for a current resident.</div>
        <form method="POST" action="discharge-management.php" data-validate>
            <div class="sn-field" style="margin-bottom:14px;">
                <label for="dm_resident">Resident <span class="req">*</span></label>
                <select id="dm_resident" name="resident_id" required>
                    <option value="">Select resident</option>
                    <?php foreach (sn_residents() as $r): if ($r['status'] === 'Active'): ?>
                    <option value="<?= sn_e($r['id']) ?>"><?= sn_e($r['name']) ?> — <?= sn_e($r['room']) ?></option>
                    <?php endif; endforeach; ?>
                </select>
            </div>
            <div class="sn-field" style="margin-bottom:14px;">
                <label for="dm_date">Discharge date <span class="req">*</span></label>
                <input type="date" id="dm_date" name="discharge_date" required>
            </div>
            <div class="sn-field" style="margin-bottom:6px;">
                <label for="dm_reason">Reason <span class="req">*</span></label>
                <textarea id="dm_reason" name="reason" required></textarea>
            </div>
            <div class="sn-modal__actions" style="margin-top:20px;">
                <button type="button" class="sn-btn sn-btn--ghost" data-modal-close>Cancel</button>
                <button type="submit" class="sn-btn sn-btn--primary">Save discharge</button>
            </div>
        </form>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
