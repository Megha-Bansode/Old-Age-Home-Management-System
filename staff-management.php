<?php
require_once __DIR__ . '/includes/config.php';
$sn_page_title    = 'Staff Management';
$sn_page_subtitle = 'Manage caregivers, medical staff, and support roles';

$staff = sn_staff();
$statusBadge = ['On Duty' => 'success', 'Off Duty' => 'muted', 'On Leave' => 'warning'];
$base_path = '';
$page_title = 'Staff Management · ' . APP_NAME . ' Admin';
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
                        <div class="sn-card__title">Staff roster</div>
                        <div class="sn-card__desc"><?= count($staff) ?> team members on record</div>
                    </div>
                    <button class="sn-btn sn-btn--primary" data-modal-open="newStaffModal">+ Add staff member</button>
                </div>

                <div class="sn-toolbar">
                    <div class="sn-search">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                        <input type="text" placeholder="Search by name or role..." data-table-search data-target="#staffTable">
                    </div>
                    <div class="sn-filters">
                        <select data-table-filter data-target="#staffTable">
                            <option value="All">All statuses</option>
                            <option value="On Duty">On Duty</option>
                            <option value="Off Duty">Off Duty</option>
                            <option value="On Leave">On Leave</option>
                        </select>
                    </div>
                </div>

                <div class="sn-table-wrap">
                    <table class="sn-table" id="staffTable">
                        <thead>
                            <tr>
                                <th>Staff member</th>
                                <th>Role</th>
                                <th>Department</th>
                                <th>Shift</th>
                                <th>Contact</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($staff as $s): ?>
                            <tr data-status="<?= sn_e($s['status']) ?>">
                                <td>
                                    <div class="sn-cell-person">
                                        <div class="sn-cell-avatar"><?= sn_e(strtoupper($s['name'][0])) ?></div>
                                        <div>
                                            <div class="sn-cell-name"><?= sn_e($s['name']) ?></div>
                                            <div class="sn-cell-sub"><?= sn_e($s['id']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><?= sn_e($s['role']) ?></td>
                                <td><?= sn_e($s['dept']) ?></td>
                                <td><?= sn_e($s['shift']) ?></td>
                                <td><?= sn_e($s['phone']) ?></td>
                                <td><span class="sn-badge sn-badge--<?= $statusBadge[$s['status']] ?? 'muted' ?>"><span class="sn-badge__dot"></span><?= sn_e($s['status']) ?></span></td>
                                <td>
                                    <div class="sn-row-actions">
                                        <button class="sn-icon-action" aria-label="Edit"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>
                                        <button class="sn-icon-action is-danger" aria-label="Remove"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6h14z"/></svg></button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="sn-empty" style="display:none;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                        <p>No staff found</p>
                        <p>Try a different name or filter</p>
                    </div>
                </div>
            </div>

        </main>
    </div>
</div>

<!-- New staff modal -->
<div class="sn-modal-overlay" id="newStaffModal">
    <div class="sn-modal">
        <div class="sn-modal__title">Add staff member</div>
        <div class="sn-modal__desc">New members get login access once their profile is approved.</div>
        <form method="POST" action="staff-management.php" data-validate>
            <div class="sn-field" style="margin-bottom:14px;">
                <label for="st_name">Full name <span class="req">*</span></label>
                <input type="text" id="st_name" name="full_name" required>
            </div>
            <div class="sn-field" style="margin-bottom:14px;">
                <label for="st_role">Role <span class="req">*</span></label>
                <input type="text" id="st_role" name="role" placeholder="e.g. Nurse, Caregiver, Cook" required>
            </div>
            <div class="sn-field" style="margin-bottom:14px;">
                <label for="st_dept">Department</label>
                <select id="st_dept" name="department">
                    <option>Medical</option>
                    <option>Care</option>
                    <option>Kitchen</option>
                    <option>Facilities</option>
                    <option>Administration</option>
                </select>
            </div>
            <div class="sn-field" style="margin-bottom:6px;">
                <label for="st_phone">Phone number <span class="req">*</span></label>
                <input type="tel" id="st_phone" name="phone" required>
            </div>
            <div class="sn-modal__actions" style="margin-top:20px;">
                <button type="button" class="sn-btn sn-btn--ghost" data-modal-close>Cancel</button>
                <button type="submit" class="sn-btn sn-btn--primary">Add staff member</button>
            </div>
        </form>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
