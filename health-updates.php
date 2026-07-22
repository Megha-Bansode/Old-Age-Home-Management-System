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

/* ── Role & Page ──────────────────────────────────────────────────────────── */
$userRole    = 'family_member';
$currentPage = 'medical-records.php';

/* ── PHP Placeholders – Today's Vitals ───────────────────────────────────── */
$last_updated   = '{{last_updated}}';
$blood_pressure = '{{blood_pressure}}';
$heart_rate     = '{{heart_rate}}';
$temperature    = '{{temperature}}';
$blood_sugar    = '{{blood_sugar}}';
$weight         = '{{weight}}';

/* ── PHP Placeholders – Medical Updates ──────────────────────────────────── */
$doctor_name    = '{{doctor_name}}';
$symptoms       = '{{symptoms_observed}}';
$diagnosis      = '{{diagnosis}}';
$treatment      = '{{treatment_given}}';
$doctor_advice  = '{{doctor_advice}}';

/* ── PHP Placeholders – Medication Tracker rows ──────────────────────────── */
// Replace with DB-driven array of associative arrays
$medications = [
    [
        'name'     => '{{medication_name_1}}',
        'dosage'   => '{{dosage_1}}',
        'schedule' => '{{schedule_1}}',
        'status'   => 'taken',
    ],
    [
        'name'     => '{{medication_name_2}}',
        'dosage'   => '{{dosage_2}}',
        'schedule' => '{{schedule_2}}',
        'status'   => 'pending',
    ],
    [
        'name'     => '{{medication_name_3}}',
        'dosage'   => '{{dosage_3}}',
        'schedule' => '{{schedule_3}}',
        'status'   => 'upcoming',
    ],
];

/* ── PHP Placeholders – Medical Reports rows ─────────────────────────────── */
$reports = [
    [
        'name'   => '{{report_name_1}}',
        'date'   => '{{report_date_1}}',
        'doctor' => '{{report_doctor_1}}',
        'type'   => 'blood',
        'type_label' => 'Blood Test',
    ],
    [
        'name'   => '{{report_name_2}}',
        'date'   => '{{report_date_2}}',
        'doctor' => '{{report_doctor_2}}',
        'type'   => 'xray',
        'type_label' => 'X-Ray',
    ],
    [
        'name'   => '{{report_name_3}}',
        'date'   => '{{report_date_3}}',
        'doctor' => '{{report_doctor_3}}',
        'type'   => 'ecg',
        'type_label' => 'ECG',
    ],
];

/* ── Helper: medication status pill ─────────────────────────────────────── */
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

/* ── Helper: report type badge ───────────────────────────────────────────── */
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
?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Health Updates | SevaNest</title>
    <meta name="description"
          content="Track your loved one's daily health vitals, medical updates, medications, and reports on SevaNest.">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Google Fonts – Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap"
          rel="stylesheet">

    <!-- Sidebar CSS -->
    <link rel="stylesheet" href="assets/css/sidebar.css">

    <!-- Dashboard base tokens (shared body / layout) -->
    <link rel="stylesheet" href="assets/css/dashboard.css">

    <!-- Health Updates specific styles -->
    <link rel="stylesheet" href="assets/css/health-updates.css">

</head>
<body>

<?php
/* ── Sidebar Component ───────────────────────────────────────────────────── */
$sn_asset_root = "assets";
include 'includes/sidebar.php';
?>

<!-- ═══════════════════════════════════════════════════════════════════════
     TOP HEADER BAR
     ═══════════════════════════════════════════════════════════════════════ -->
<header class="sn-topbar" id="sn-topbar" role="banner" aria-label="Top bar">

    <button id="sn-toggle-btn"
            type="button"
            aria-label="Toggle sidebar navigation"
            aria-expanded="true"
            aria-controls="sn-sidebar"
            title="Toggle Sidebar">
        <i class="bi bi-list" aria-hidden="true"></i>
    </button>

    <div class="sn-profile">
        <a href="profile.php" title="Profile" class="top-profile-btn">
            <i class="bi bi-person-circle"></i>
        </a>
    </div>

</header>
<!-- ── End Top Header Bar ─────────────────────────────────────────────── -->


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


        <!-- ── BOTTOM – Back to Dashboard ────────────────────────────── -->
        <div class="hu-bottom-bar hu-animate hu-animate-d5">
            <a href="dashboard.php"
               class="hu-btn-back"
               id="hu-back-btn"
               aria-label="Back to Family Dashboard">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Back to Dashboard
            </a>
        </div>
        <!-- ── End Bottom Bar ─────────────────────────────────────────── -->


    </div><!-- /.hu-page-wrapper -->
</main>
<!-- ── End Main Content ───────────────────────────────────────────────── -->


<!-- Bootstrap 5 JS bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Sidebar JS -->
<script src="assets/js/sidebar.js" defer></script>

<!-- Health Updates JS -->
<script src="assets/js/health-updates.js" defer></script>

</body>
</html>
