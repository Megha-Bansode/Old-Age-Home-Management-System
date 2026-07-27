<?php
require_once __DIR__ . '/includes/config.php';
$sn_page_title    = 'Resident Registration';
$sn_page_subtitle = 'Add a new resident and their guardian details to the system';

$formError = '';
$formSuccess = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ---- Backend integration point ----
    // Validate + sanitize, then insert into `residents` table, e.g.:
    //
    // $name = trim($_POST['full_name'] ?? '');
    // if ($name === '') { $formError = 'Full name is required.'; }
    // else {
    //     sn_query(
    //         "INSERT INTO residents (full_name, dob, gender, guardian_name, guardian_phone, room_no, notes)
    //          VALUES (?,?,?,?,?,?,?)",
    //         ['sssssss', $name, $_POST['dob'], $_POST['gender'], $_POST['guardian_name'],
    //          $_POST['guardian_phone'], $_POST['room_no'], $_POST['notes']]
    //     );
    //     $formSuccess = 'Resident registered successfully.';
    // }
    $formSuccess = 'Resident registered successfully. (Demo mode — connect includes/config.php to persist.)';
}

$residents = sn_residents();
$healthBadge = ['Stable' => 'success', 'Needs Care' => 'warning', 'Critical' => 'danger'];
$statusBadge = ['Active' => 'success', 'Discharged' => 'muted'];
$base_path = '';
$page_title = 'Resident Registration · ' . APP_NAME . ' Admin';
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

            <?php if ($formSuccess): ?>
            <div class="sn-alert sn-alert--success">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="M22 4L12 14.01l-3-3"/></svg>
                <?= sn_e($formSuccess) ?>
            </div>
            <?php endif; ?>
            <?php if ($formError): ?>
            <div class="sn-alert sn-alert--error">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
                <?= sn_e($formError) ?>
            </div>
            <?php endif; ?>

            <div class="sn-card">
                <div class="sn-card__head">
                    <div>
                        <div class="sn-card__title">New resident details</div>
                        <div class="sn-card__desc">Fields marked <span style="color:var(--sn-danger)">*</span> are required</div>
                    </div>
                </div>

                <form method="POST" action="resident-registration.php" data-validate>
                    <div class="sn-form-grid">
                        <div class="sn-section-label">Personal information</div>

                        <div class="sn-field">
                            <label for="full_name">Full name <span class="req">*</span></label>
                            <input type="text" id="full_name" name="full_name" placeholder="e.g. Kamala Devi" required>
                        </div>
                        <div class="sn-field">
                            <label for="dob">Date of birth <span class="req">*</span></label>
                            <input type="date" id="dob" name="dob" required>
                        </div>
                        <div class="sn-field">
                            <label for="gender">Gender <span class="req">*</span></label>
                            <select id="gender" name="gender" required>
                                <option value="">Select</option>
                                <option>Female</option>
                                <option>Male</option>
                                <option>Other</option>
                            </select>
                        </div>
                        <div class="sn-field">
                            <label for="blood_group">Blood group</label>
                            <select id="blood_group" name="blood_group">
                                <option value="">Select</option>
                                <option>A+</option><option>A-</option>
                                <option>B+</option><option>B-</option>
                                <option>O+</option><option>O-</option>
                                <option>AB+</option><option>AB-</option>
                            </select>
                        </div>
                        <div class="sn-field">
                            <label for="room_no">Room number</label>
                            <input type="text" id="room_no" name="room_no" placeholder="e.g. A-104">
                        </div>
                        <div class="sn-field">
                            <label for="admission_date">Admission date <span class="req">*</span></label>
                            <input type="date" id="admission_date" name="admission_date" required>
                        </div>

                        <div class="sn-section-label">Guardian / emergency contact</div>

                        <div class="sn-field">
                            <label for="guardian_name">Guardian name <span class="req">*</span></label>
                            <input type="text" id="guardian_name" name="guardian_name" placeholder="e.g. Ravi Devi" required>
                        </div>
                        <div class="sn-field">
                            <label for="relation">Relationship</label>
                            <input type="text" id="relation" name="relation" placeholder="e.g. Son">
                        </div>
                        <div class="sn-field">
                            <label for="guardian_phone">Phone number <span class="req">*</span></label>
                            <input type="tel" id="guardian_phone" name="guardian_phone" placeholder="98450 11234" required>
                        </div>
                        <div class="sn-field">
                            <label for="guardian_email">Email address</label>
                            <input type="email" id="guardian_email" name="guardian_email" placeholder="name@example.com">
                        </div>
                        <div class="sn-field sn-field--full">
                            <label for="address">Guardian address</label>
                            <textarea id="address" name="address" placeholder="Street, city, state, PIN"></textarea>
                        </div>

                        <div class="sn-section-label">Medical notes</div>
                        <div class="sn-field sn-field--full">
                            <label for="notes">Health conditions / medication</label>
                            <textarea id="notes" name="notes" placeholder="Diabetes, hypertension, current medication, allergies..."></textarea>
                            <div class="sn-field-hint">This information is visible to caregiving staff only.</div>
                        </div>
                    </div>

                    <div class="sn-form-actions">
                        <button type="reset" class="sn-btn sn-btn--ghost">Clear form</button>
                        <button type="submit" class="sn-btn sn-btn--primary">Register resident</button>
                    </div>
                </form>
            </div>

            <div class="sn-card">
                <div class="sn-card__head">
                    <div>
                        <div class="sn-card__title">Registered residents</div>
                        <div class="sn-card__desc"><?= count($residents) ?> residents on record</div>
                    </div>
                </div>

                <div class="sn-toolbar">
                    <div class="sn-search">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                        <input type="text" placeholder="Search residents..." data-table-search data-target="#residentsTable">
                    </div>
                    <div class="sn-filters">
                        <select data-table-filter data-target="#residentsTable">
                            <option value="All">All statuses</option>
                            <option value="Active">Active</option>
                            <option value="Discharged">Discharged</option>
                        </select>
                    </div>
                </div>

                <div class="sn-table-wrap">
                    <table class="sn-table" id="residentsTable">
                        <thead>
                            <tr>
                                <th>Resident</th>
                                <th>Room</th>
                                <th>Admitted</th>
                                <th>Guardian</th>
                                <th>Health</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($residents as $r): ?>
                            <tr data-status="<?= sn_e($r['status']) ?>">
                                <td>
                                    <div class="sn-cell-person">
                                        <div class="sn-cell-avatar"><?= sn_e(strtoupper($r['name'][0])) ?></div>
                                        <div>
                                            <div class="sn-cell-name"><?= sn_e($r['name']) ?></div>
                                            <div class="sn-cell-sub"><?= sn_e($r['id']) ?> · Age <?= (int) $r['age'] ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><?= sn_e($r['room']) ?></td>
                                <td><?= sn_e($r['admission']) ?></td>
                                <td>
                                    <div class="sn-cell-name"><?= sn_e($r['guardian']) ?></div>
                                    <div class="sn-cell-sub"><?= sn_e($r['phone']) ?></div>
                                </td>
                                <td><span class="sn-badge sn-badge--<?= $healthBadge[$r['health']] ?? 'muted' ?>"><span class="sn-badge__dot"></span><?= sn_e($r['health']) ?></span></td>
                                <td><span class="sn-badge sn-badge--<?= $statusBadge[$r['status']] ?? 'muted' ?>"><?= sn_e($r['status']) ?></span></td>
                                <td>
                                    <div class="sn-row-actions">
                                        <button class="sn-icon-action" aria-label="Edit"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>
                                        <button class="sn-icon-action is-danger" aria-label="Delete"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6h14z"/></svg></button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="sn-empty" style="display:none;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                        <p>No residents found</p>
                        <p>Try a different name or clear your filters</p>
                    </div>
                </div>
            </div>

        </main>
    </div>
</div>
<?php
require_once __DIR__ . '/includes/footer.php';
?>
