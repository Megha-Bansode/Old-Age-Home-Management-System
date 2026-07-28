<?php
/**
 * SevaNest – Family Member Health Updates
 * File     : health-updates.php
 * Version  : 1.0
 * Author   : SevaNest Dev Team
 *
 * Description:
 *   Health updates page for the Family Member role.
 *   Sections: Today's Health Updates, Medical Updates,
 *             Medication Tracker, Medical Reports.
 *   All values use PHP placeholders for MySQL integration.
 */

require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../config/database.php';

// Require login
require_login();

/* ── Role & Page ──────────────────────────────────────────────────────────── */
$userRole    = 'family_member';
$currentPage = 'health-updates.php';

/* ── PHP Placeholders – Today's Vitals (replace with DB fetch if available) ── */
$last_updated   = 'No health logs found';
$blood_pressure = '--/-- mmHg';
$heart_rate     = '-- bpm';
$temperature    = '-- °F';
$blood_sugar    = '-- mg/dL';
$weight         = '-- kg';

/* ── PHP Placeholders – Medical Updates ──────────────────────────────────── */
$doctor_name    = 'No doctor visited recently';
$symptoms       = 'No symptoms reported.';
$diagnosis      = 'No diagnosis recorded.';
$treatment      = 'No treatment prescribed.';
$doctor_advice  = 'No advice recorded.';

$medications = [];
$reports = [];

// Database binding hook
$pdo = get_db_connection();
if ($pdo) {
    try {
        $user_id = $_SESSION['user_id'] ?? 5;
        
        // Fetch resident ID
        $stmt_res = $pdo->prepare("SELECT resident_id FROM residents WHERE family_member_id = ? LIMIT 1");
        $stmt_res->execute([$user_id]);
        $resident_id = (int)$stmt_res->fetchColumn();
        
        if ($resident_id > 0) {
            // 1. Fetch latest vitals
            $stmt_v = $pdo->prepare("SELECT hr.*, u.full_name AS doctor_name FROM health_records hr LEFT JOIN users u ON hr.created_by = u.id WHERE hr.resident_id = ? ORDER BY hr.record_date DESC LIMIT 1");
            $stmt_v->execute([$resident_id]);
            $vitals = $stmt_v->fetch();
            
            if ($vitals) {
                $last_updated   = date('jS F Y • h:i A', strtotime($vitals['record_date']));
                $blood_pressure = $vitals['systolic_bp'] . '/' . $vitals['diastolic_bp'] . ' mmHg';
                $heart_rate     = $vitals['pulse'] . ' bpm';
                $temperature    = $vitals['temperature'] ? $vitals['temperature'] . ' °F' : '-- °F';
                $blood_sugar    = $vitals['blood_sugar'] ? $vitals['blood_sugar'] . ' mg/dL' : '-- mg/dL';
                $weight         = $vitals['weight'] ? $vitals['weight'] . ' kg' : '-- kg';
                
                $doctor_name    = $vitals['doctor_name'] ?? 'Attending Doctor';
                $symptoms       = $vitals['symptoms'] ?: 'No symptoms reported.';
                $diagnosis      = $vitals['notes'] ?: 'Blood pressure and vitals normal.';
                $treatment      = $vitals['notes'] ?: 'No treatment prescribed.';
                $doctor_advice  = 'Encourage mild morning walking and maintaining hydration.';
            }
            
            // 2. Fetch prescriptions
            $stmt_meds = $pdo->prepare("SELECT p.*, u.full_name AS doctor_name FROM prescriptions p LEFT JOIN users u ON p.doctor_id = u.id WHERE p.resident_id = ? ORDER BY p.created_at DESC");
            $stmt_meds->execute([$resident_id]);
            $db_meds = $stmt_meds->fetchAll();
            
            foreach ($db_meds as $m) {
                $medications[] = [
                    'name'     => $m['medication_name'],
                    'dosage'   => $m['dosage'],
                    'schedule' => $m['frequency'] . ' (' . ($m['instructions'] ?: 'No notes') . ')',
                    'status'   => ($m['status'] === 'Active') ? 'taken' : 'upcoming',
                ];
            }
            
            // 3. Fetch reports
            $stmt_reps = $pdo->prepare("SELECT mr.*, u.full_name AS doctor_name FROM medical_reports mr LEFT JOIN users u ON mr.uploaded_by = u.id WHERE mr.resident_id = ? ORDER BY mr.report_date DESC");
            $stmt_reps->execute([$resident_id]);
            $db_reps = $stmt_reps->fetchAll();
            
            foreach ($db_reps as $rep) {
                $reports[] = [
                    'name'       => $rep['report_name'],
                    'date'       => date('d M Y', strtotime($rep['report_date'])),
                    'doctor'     => $rep['doctor_name'] ?: 'System Health Log',
                    'type'       => strtolower($rep['report_type'] ?: 'blood'),
                    'type_label' => $rep['report_type'] ?: 'Medical Report'
                ];
            }
        }
    } catch (PDOException $e) {
        log_error("DB health updates fetch failed: " . $e->getMessage());
    }
}

/* ── Helper: medication status pill ─────────────────────────────────────── */
if (!function_exists('med_pill')) {
    function med_pill(string $status): string {
        $map = [
            'taken'    => ['hu-pill--taken',    'bi-check-circle-fill', 'Taken'],
            'pending'  => ['hu-pill--pending',  'bi-clock-fill',        'Pending'],
            'upcoming' => ['hu-pill--upcoming', 'bi-calendar2',         'Upcoming'],
        ];
        $s = $map[$status] ?? $map['upcoming'];
        return '<span class="hu-pill ' . $s[0] . '">'
             . '<i class="bi ' . $s[1] . '" aria-hidden="true"></i>'
             . htmlspecialchars($s[2])
             . '</span>';
    }
}

/* ── Helper: report type badge ───────────────────────────────────────────── */
if (!function_exists('report_badge')) {
    function report_badge(string $type, string $label): string {
        $class_map = [
            'blood' => 'hu-badge--blood',
            'xray'  => 'hu-badge--xray',
            'mri'   => 'hu-badge--mri',
            'ct'    => 'hu-badge--ct',
            'ecg'   => 'hu-badge--ecg',
            'rx'    => 'hu-badge--rx',
        ];
        $cls = $class_map[$type] ?? 'hu-badge--rx';
        return '<span class="hu-badge ' . $cls . '">'
             . htmlspecialchars($label)
             . '</span>';
    }
}
?>
<?php
$base_path = '../../';
$page_title = 'Health Updates | SevaNest';
$extra_css = [
    'assets/css/health-updates.css'
];

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'family_member';
$currentPage   = 'health-updates.php';
$sn_asset_root = "../../assets";
include '../../includes/sidebar.php';
?>

<!-- ═══════════════════════════════════════════════════════════════════════
     MAIN CONTENT AREA
     ═══════════════════════════════════════════════════════════════════════ -->
<main id="sn-main-content" role="main" aria-label="Health updates content">

    <div class="hu-page-wrapper">


        <!-- ── 1. PAGE HEADER STRIP ──────────────────────────────────── -->
        <section class="hu-header-strip hu-animate" aria-labelledby="hu-page-heading">
            <div>
                <h1 class="hu-header-strip__title" id="hu-page-heading">
                    Health Updates
                </h1>
            </div>
        </section>
        <!-- ── End Page Header Strip ─────────────────────────────────── -->


        <!-- ── 2. TODAY'S HEALTH UPDATES ─────────────────────────────── -->
        <article class="hu-card hu-animate hu-animate-d1"
                 aria-labelledby="hu-vitals-heading"
                 id="hu-vitals-card">

            <div class="hu-card__header">
                <h2 class="hu-card__title" id="hu-vitals-heading">
                    <i class="bi bi-heart-pulse-fill" aria-hidden="true"></i>
                    Today's Health Updates
                </h2>
                <span class="hu-last-updated"
                      id="hu-last-updated"
                      aria-label="Last updated: <?php echo htmlspecialchars($last_updated); ?>">
                    <i class="bi bi-clock" aria-hidden="true"></i>
                    Last Updated: <strong><?php echo htmlspecialchars($last_updated); ?></strong>
                </span>
            </div>

            <ul class="hu-vitals-list" role="list" aria-label="Health vitals">

                <!-- Blood Pressure -->
                <li class="hu-vital-row">
                    <div class="hu-vital-left">
                        <span class="hu-vital-icon" aria-hidden="true">❤️</span>
                        <span class="hu-vital-label">Blood Pressure</span>
                    </div>
                    <span class="hu-vital-value"
                          id="hu-val-bp"
                          aria-label="Blood pressure: <?php echo htmlspecialchars($blood_pressure); ?>">
                        <?php echo htmlspecialchars($blood_pressure); ?>
                    </span>
                </li>

                <!-- Heart Rate -->
                <li class="hu-vital-row">
                    <div class="hu-vital-left">
                        <span class="hu-vital-icon" aria-hidden="true">💓</span>
                        <span class="hu-vital-label">Heart Rate</span>
                    </div>
                    <span class="hu-vital-value"
                          id="hu-val-hr"
                          aria-label="Heart rate: <?php echo htmlspecialchars($heart_rate); ?>">
                        <?php echo htmlspecialchars($heart_rate); ?>
                    </span>
                </li>

                <!-- Body Temperature -->
                <li class="hu-vital-row">
                    <div class="hu-vital-left">
                        <span class="hu-vital-icon" aria-hidden="true">🌡️</span>
                        <span class="hu-vital-label">Body Temperature</span>
                    </div>
                    <span class="hu-vital-value"
                          id="hu-val-temp"
                          aria-label="Temperature: <?php echo htmlspecialchars($temperature); ?>">
                        <?php echo htmlspecialchars($temperature); ?>
                    </span>
                </li>

                <!-- Blood Sugar -->
                <li class="hu-vital-row">
                    <div class="hu-vital-left">
                        <span class="hu-vital-icon" aria-hidden="true">💧</span>
                        <span class="hu-vital-label">Blood Sugar Level</span>
                    </div>
                    <span class="hu-vital-value"
                          id="hu-val-sugar"
                          aria-label="Blood sugar: <?php echo htmlspecialchars($blood_sugar); ?>">
                        <?php echo htmlspecialchars($blood_sugar); ?>
                    </span>
                </li>

                <!-- Weight -->
                <li class="hu-vital-row">
                    <div class="hu-vital-left">
                        <span class="hu-vital-icon" aria-hidden="true">🧍</span>
                        <span class="hu-vital-label">Weight</span>
                    </div>
                    <span class="hu-vital-value"
                          id="hu-val-weight"
                          aria-label="Weight: <?php echo htmlspecialchars($weight); ?>">
                        <?php echo htmlspecialchars($weight); ?>
                    </span>
                </li>

            </ul>

        </article>
        <!-- ── End Today's Health Updates ────────────────────────────── -->


        <!-- ── 3. MEDICAL UPDATES CARD ───────────────────────────────── -->
        <article class="hu-card hu-animate hu-animate-d2"
                 aria-labelledby="hu-medical-heading"
                 id="hu-medical-updates">

            <div class="hu-card__header">
                <h2 class="hu-card__title" id="hu-medical-heading">
                    <i class="bi bi-clipboard2-pulse" aria-hidden="true"></i>
                    Medical Updates
                </h2>
                <a href="edit-health.php?section=medical"
                   class="hu-btn-edit"
                   id="hu-edit-medical-btn"
                   aria-label="Edit medical updates">
                    <i class="bi bi-pencil-square" aria-hidden="true"></i>
                    Edit
                </a>
            </div>

            <div class="hu-field-grid">

                <!-- Doctor Name -->
                <div class="hu-field-group">
                    <span class="hu-field-label">
                        <i class="bi bi-person-badge" aria-hidden="true"></i>
                        Doctor Name
                    </span>
                    <div class="hu-field-value"
                         id="hu-val-doctor"
                         aria-label="Doctor: <?php echo htmlspecialchars($doctor_name); ?>">
                        <?php echo htmlspecialchars($doctor_name); ?>
                    </div>
                </div>

                <!-- Diagnosis -->
                <div class="hu-field-group">
                    <span class="hu-field-label">
                        <i class="bi bi-file-earmark-medical" aria-hidden="true"></i>
                        Diagnosis
                    </span>
                    <div class="hu-field-value"
                         id="hu-val-diagnosis"
                         aria-label="Diagnosis: <?php echo htmlspecialchars($diagnosis); ?>">
                        <?php echo htmlspecialchars($diagnosis); ?>
                    </div>
                </div>

                <!-- Symptoms – full width -->
                <div class="hu-field-group hu-field-group--full">
                    <span class="hu-field-label">
                        <i class="bi bi-thermometer-half" aria-hidden="true"></i>
                        Symptoms Observed
                    </span>
                    <div class="hu-field-value"
                         id="hu-val-symptoms"
                         style="min-height:54px; align-items:flex-start; padding-top:12px;"
                         aria-label="Symptoms: <?php echo htmlspecialchars($symptoms); ?>">
                        <?php echo htmlspecialchars($symptoms); ?>
                    </div>
                </div>

                <!-- Treatment – full width -->
                <div class="hu-field-group hu-field-group--full">
                    <span class="hu-field-label">
                        <i class="bi bi-capsule" aria-hidden="true"></i>
                        Treatment Given
                    </span>
                    <div class="hu-field-value"
                         id="hu-val-treatment"
                         style="min-height:54px; align-items:flex-start; padding-top:12px;"
                         aria-label="Treatment: <?php echo htmlspecialchars($treatment); ?>">
                        <?php echo htmlspecialchars($treatment); ?>
                    </div>
                </div>

                <!-- Doctor Advice – full width highlight box -->
                <div class="hu-field-group hu-field-group--full">
                    <span class="hu-field-label">
                        <i class="bi bi-lightbulb" aria-hidden="true"></i>
                        Doctor Advice
                    </span>
                    <div class="hu-advice-box"
                         id="hu-val-advice"
                         aria-label="Doctor's advice">
                        <div class="hu-advice-box__label">
                            <i class="bi bi-info-circle-fill" aria-hidden="true"></i>
                            Doctor's Recommendation
                        </div>
                        <p class="hu-advice-box__text">
                            <?php echo htmlspecialchars($doctor_advice); ?>
                        </p>
                    </div>
                </div>

            </div><!-- /.hu-field-grid -->

        </article>
        <!-- ── End Medical Updates Card ───────────────────────────────── -->


        <!-- ── 4. MEDICATION TRACKER CARD ────────────────────────────── -->
        <article class="hu-card hu-animate hu-animate-d3"
                 aria-labelledby="hu-medication-heading"
                 id="hu-medication-tracker">

            <div class="hu-card__header">
                <h2 class="hu-card__title" id="hu-medication-heading">
                    <i class="bi bi-capsule-pill" aria-hidden="true"></i>
                    Medication Tracker
                </h2>
            </div>

            <!-- Toolbar: search + filter -->
            <div class="hu-toolbar">
                <div class="hu-search-wrap">
                    <i class="bi bi-search" aria-hidden="true"></i>
                    <input type="search"
                           class="hu-search-input"
                           id="hu-med-search"
                           placeholder="Search medication…"
                           aria-label="Search medication">
                </div>
                <select class="hu-filter-select"
                        id="hu-med-filter"
                        aria-label="Filter by status">
                    <option value="">All Status</option>
                    <option value="taken">Taken</option>
                    <option value="pending">Pending</option>
                    <option value="upcoming">Upcoming</option>
                </select>
            </div>

            <!-- Table -->
            <div class="hu-table-wrap" role="region" aria-label="Medication tracker table" tabindex="0">
                <table class="hu-table" id="hu-med-table" aria-label="Medication list">
                    <thead>
                        <tr>
                            <th scope="col">Medication Name</th>
                            <th scope="col">Dosage</th>
                            <th scope="col">Schedule</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody id="hu-med-tbody">
                        <?php foreach ($medications as $i => $med): ?>
                        <tr data-status="<?php echo htmlspecialchars($med['status']); ?>">
                            <td id="hu-med-name-<?php echo $i + 1; ?>">
                                <span style="display:flex;align-items:center;gap:8px;">
                                     <i class="bi bi-capsule" style="color:var(--hu-sage);font-size:0.9rem;" aria-hidden="true"></i>
                                    <?php echo htmlspecialchars($med['name']); ?>
                                </span>
                            </td>
                            <td id="hu-med-dosage-<?php echo $i + 1; ?>">
                                <?php echo htmlspecialchars($med['dosage']); ?>
                            </td>
                            <td id="hu-med-schedule-<?php echo $i + 1; ?>">
                                <span style="display:flex;align-items:center;gap:6px;">
                                    <i class="bi bi-clock" style="color:var(--hu-grey);font-size:0.8rem;" aria-hidden="true"></i>
                                    <?php echo htmlspecialchars($med['schedule']); ?>
                                </span>
                            </td>
                            <td>
                                <?php echo med_pill($med['status']); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </article>
        <!-- ── End Medication Tracker Card ────────────────────────────── -->


        <!-- ── 5. MEDICAL REPORTS CARD ───────────────────────────────── -->
        <article class="hu-card hu-animate hu-animate-d4"
                 aria-labelledby="hu-reports-heading"
                 id="hu-medical-reports">

            <div class="hu-card__header">
                <h2 class="hu-card__title" id="hu-reports-heading">
                    <i class="bi bi-folder2-open" aria-hidden="true"></i>
                    Medical Reports
                </h2>
            </div>

            <!-- Report toolbar: search -->
            <div class="hu-toolbar">
                <div class="hu-search-wrap">
                    <i class="bi bi-search" aria-hidden="true"></i>
                    <input type="search"
                           class="hu-search-input"
                           id="hu-report-search"
                           placeholder="Search reports…"
                           aria-label="Search medical reports">
                </div>
            </div>

            <!-- Reports table -->
            <div class="hu-table-wrap" role="region" aria-label="Medical reports table" tabindex="0">
                <table class="hu-table" id="hu-reports-table" aria-label="Medical reports list">
                    <thead>
                        <tr>
                            <th scope="col">Report Name</th>
                            <th scope="col">Date</th>
                            <th scope="col">Doctor</th>
                            <th scope="col">Type</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="hu-reports-tbody">
                        <?php foreach ($reports as $i => $report): ?>
                        <tr>
                            <td id="hu-report-name-<?php echo $i + 1; ?>">
                                <span style="display:flex;align-items:center;gap:8px;">
                                    <i class="bi bi-file-earmark-text" style="color:var(--hu-sage);font-size:0.9rem;" aria-hidden="true"></i>
                                    <?php echo htmlspecialchars($report['name']); ?>
                                </span>
                            </td>
                            <td id="hu-report-date-<?php echo $i + 1; ?>">
                                <span style="display:flex;align-items:center;gap:6px;">
                                    <i class="bi bi-calendar3" style="color:var(--hu-grey);font-size:0.8rem;" aria-hidden="true"></i>
                                    <?php echo htmlspecialchars($report['date']); ?>
                                </span>
                            </td>
                            <td id="hu-report-doctor-<?php echo $i + 1; ?>">
                                <?php echo htmlspecialchars($report['doctor']); ?>
                            </td>
                            <td>
                                <?php echo report_badge($report['type'], $report['type_label']); ?>
                            </td>
                            <td>
                                <div class="hu-action-btns">
                                    <a href="view-report.php?id=<?php echo $i + 1; ?>"
                                       class="hu-btn-action hu-btn-action--view"
                                       id="hu-view-report-<?php echo $i + 1; ?>"
                                       aria-label="View report <?php echo htmlspecialchars($report['name']); ?>">
                                        <i class="bi bi-eye" aria-hidden="true"></i>
                                        View
                                    </a>
                                    <a href="download-report.php?id=<?php echo $i + 1; ?>"
                                       class="hu-btn-action hu-btn-action--download"
                                       id="hu-download-report-<?php echo $i + 1; ?>"
                                       aria-label="Download report <?php echo htmlspecialchars($report['name']); ?>">
                                        <i class="bi bi-download" aria-hidden="true"></i>
                                        Download
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="hu-pagination" id="hu-reports-pagination" aria-label="Reports pagination">
                <span class="hu-pagination__info" id="hu-pagination-info">
                    Showing <?php echo count($reports); ?> of {{total_reports}} reports
                </span>
                <div class="hu-pagination__btns" role="group" aria-label="Page navigation">
                    <button class="hu-page-btn" id="hu-page-prev" aria-label="Previous page" disabled>
                        <i class="bi bi-chevron-left" aria-hidden="true"></i>
                    </button>
                    <button class="hu-page-btn active" id="hu-page-1" aria-label="Page 1" aria-current="page">1</button>
                    <button class="hu-page-btn" id="hu-page-2" aria-label="Page 2">2</button>
                    <button class="hu-page-btn" id="hu-page-3" aria-label="Page 3">3</button>
                    <button class="hu-page-btn" id="hu-page-next" aria-label="Next page">
                        <i class="bi bi-chevron-right" aria-hidden="true"></i>
                    </button>
                </div>
            </div>

        </article>
        <!-- ── End Medical Reports Card ───────────────────────────────── -->

        <!-- ── End Bottom Bar ─────────────────────────────────────────── -->


    </div><!-- /.hu-page-wrapper -->
</main>
<!-- ── End Main Content ───────────────────────────────────────────────── -->

<!-- Health Updates JS -->
<script src="../../assets/js/health-updates.js" defer></script>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
