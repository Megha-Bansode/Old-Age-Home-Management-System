<?php
require_once __DIR__ . '/includes/config.php';
$sn_page_title    = 'Admission Management';
$sn_page_subtitle = 'Review, approve, and track incoming admission requests';

$admissions = sn_admissions();
$statusBadge = ['Pending' => 'warning', 'Under Review' => 'muted', 'Approved' => 'success', 'Rejected' => 'danger'];
$counts = ['All' => count($admissions)];
foreach ($admissions as $a) { $counts[$a['status']] = ($counts[$a['status']] ?? 0) + 1; }
$base_path = '';
$page_title = 'Admission Management · ' . APP_NAME . ' Admin';
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
                        <div class="sn-card__title">Admission requests</div>
                        <div class="sn-card__desc"><?= $counts['Pending'] ?? 0 ?> awaiting decision</div>
                    </div>
                    <button class="sn-btn sn-btn--primary" data-modal-open="newAdmissionModal">+ New request</button>
                </div>

                <div class="sn-toolbar">
                    <div class="sn-search">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                        <input type="text" placeholder="Search by name or guardian..." data-table-search data-target="#admissionsTable">
                    </div>
                    <div class="sn-tabs" data-table="#admissionsTable">
                        <button class="is-active" data-tab-target="All">All</button>
                        <button data-tab-target="Pending">Pending</button>
                        <button data-tab-target="Under Review">Under Review</button>
                        <button data-tab-target="Approved">Approved</button>
                        <button data-tab-target="Rejected">Rejected</button>
                    </div>
                </div>

                <div class="sn-table-wrap">
                    <table class="sn-table" id="admissionsTable">
                        <thead>
                            <tr>
                                <th>Applicant</th>
                                <th>Request ID</th>
                                <th>Requested on</th>
                                <th>Guardian</th>
                                <th>Contact</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($admissions as $a): ?>
                            <tr data-status="<?= sn_e($a['status']) ?>">
                                <td>
                                    <div class="sn-cell-person">
                                        <div class="sn-cell-avatar"><?= sn_e(strtoupper($a['name'][0])) ?></div>
                                        <div>
                                            <div class="sn-cell-name"><?= sn_e($a['name']) ?></div>
                                            <div class="sn-cell-sub">Age <?= (int) $a['age'] ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><?= sn_e($a['id']) ?></td>
                                <td><?= sn_e($a['requested']) ?></td>
                                <td><?= sn_e($a['guardian']) ?></td>
                                <td><?= sn_e($a['phone']) ?></td>
                                <td><span class="sn-badge sn-badge--<?= $statusBadge[$a['status']] ?? 'muted' ?>"><span class="sn-badge__dot"></span><?= sn_e($a['status']) ?></span></td>
                                <td>
                                    <div class="sn-row-actions">
                                        <button class="sn-icon-action" aria-label="Approve" title="Approve"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="M22 4L12 14.01l-3-3"/></svg></button>
                                        <button class="sn-icon-action is-danger" aria-label="Reject" title="Reject"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/></svg></button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="sn-empty" style="display:none;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                        <p>No requests found</p>
                        <p>Try a different name or filter</p>
                    </div>
                </div>
            </div>

        </main>
    </div>
</div>

<!-- New admission request modal -->
<div class="sn-modal-overlay" id="newAdmissionModal">
    <div class="sn-modal">
        <div class="sn-modal__title">New admission request</div>
        <div class="sn-modal__desc">Capture the essentials now — full intake happens on the Resident Registration page once approved.</div>
        <form method="POST" action="admission-management.php" data-validate>
            <div class="sn-field" style="margin-bottom:14px;">
                <label for="am_name">Applicant name <span class="req">*</span></label>
                <input type="text" id="am_name" name="applicant_name" required>
            </div>
            <div class="sn-field" style="margin-bottom:14px;">
                <label for="am_guardian">Guardian name <span class="req">*</span></label>
                <input type="text" id="am_guardian" name="guardian_name" required>
            </div>
            <div class="sn-field" style="margin-bottom:6px;">
                <label for="am_phone">Contact number <span class="req">*</span></label>
                <input type="tel" id="am_phone" name="phone" required>
            </div>
            <div class="sn-modal__actions" style="margin-top:20px;">
                <button type="button" class="sn-btn sn-btn--ghost" data-modal-close>Cancel</button>
                <button type="submit" class="sn-btn sn-btn--primary">Submit request</button>
            </div>
        </form>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
