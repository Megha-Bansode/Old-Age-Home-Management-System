<?php
/**
 * SevaNest – Doctor Patient Health Reports
 * File     : modules/doctor/health_reports.php
 * Version  : 1.0
 */

require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../config/database.php';

// Require Doctor login
require_login();
require_role('Doctor');

$pdo = get_db_connection();
$user_id = $_SESSION['user_id'] ?? 3; // Default to Dr. Priya Nair

$formSuccess = '';
$formError = '';

// Programmatic seeder
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM medical_reports");
    if ($stmt->fetchColumn() == 0) {
        $mocks = [
            ['resident_id' => 1, 'report_name' => 'Complete Blood Count (CBC) Report', 'report_type' => 'Lab Report', 'report_date' => '2026-07-22', 'file_path' => '#', 'uploaded_by' => $user_id, 'status' => 'pending', 'notes' => 'Awaiting review'],
            ['resident_id' => 2, 'report_name' => 'Lipid Profile Test', 'report_type' => 'Blood Test', 'report_date' => '2026-07-18', 'file_path' => '#', 'uploaded_by' => $user_id, 'status' => 'approved', 'notes' => 'Borderline High cholesterol'],
            ['resident_id' => 3, 'report_name' => 'Electrocardiogram (ECG) Chart', 'report_type' => 'Cardiology', 'report_date' => '2026-06-10', 'file_path' => '#', 'uploaded_by' => $user_id, 'status' => 'approved', 'notes' => 'Stable heart pattern']
        ];
        
        foreach ($mocks as $m) {
            $stmt_ins = $pdo->prepare("INSERT INTO medical_reports (resident_id, report_name, report_type, report_date, file_path, uploaded_by, status, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt_ins->execute([$m['resident_id'], $m['report_name'], $m['report_type'], $m['report_date'], $m['file_path'], $m['uploaded_by'], $m['status'], $m['notes']]);
        }
    }
} catch (Exception $e) {
    // Fail silently
}

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'approve') {
    $report_id = (int)($_POST['report_id'] ?? 0);
    try {
        $stmt = $pdo->prepare("UPDATE medical_reports SET status = 'approved' WHERE report_id = ?");
        $stmt->execute([$report_id]);
        $formSuccess = "Medical report approved successfully!";
    } catch (Exception $e) {
        $formError = "Error approving report: " . $e->getMessage();
    }
}

// Fetch medical reports list
$stmt = $pdo->prepare("SELECT mr.*, r.full_name AS resident_name, u.full_name AS physician_name 
                       FROM medical_reports mr 
                       JOIN residents r ON mr.resident_id = r.resident_id 
                       LEFT JOIN users u ON mr.uploaded_by = u.id
                       ORDER BY mr.report_date DESC");
$stmt->execute();
$reports = $stmt->fetchAll();

$base_path = '../../';
$page_title = 'Patient Health Reports | SevaNest';
$extra_css = [
    'assets/css/doctor.css'
];

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'doctor';
$currentPage   = 'health_reports.php';
$sn_asset_root = "../../assets";
$base_path = '../../';
include '../../includes/sidebar.php';
?>

<!-- ═══════════════════════════════════════════════════════════════════════
     MAIN CONTENT AREA
     ═══════════════════════════════════════════════════════════════════════ -->
<main id="sn-main-content" role="main" aria-label="Doctor health reports content">
    <div class="doctor-page-wrapper">
        
        <!-- Header Strip -->
        <div class="dr-header-strip animate-fade-in">
            <div>
                <h2 class="dr-header-strip__title">Patient Health Statistics & Lab Reports</h2>
                <p style="color: var(--color-text-muted-team); margin: 4px 0 0;">View recovery progress metrics, disease trends and laboratory test summaries.</p>
            </div>
        </div>

        <!-- Charts Placeholders (using statistical overrides) -->
        <div class="grid two-col animate-fade-in">
            
            <!-- Recovery Progress Chart -->
            <div class="card">
                <div class="card-head">
                    <h3>Overall Patient Recovery Rate (Weekly)</h3>
                </div>
                <div class="report-placeholder-chart">
                    <div class="report-bars">
                        <span style="height: 50%;"></span>
                        <span style="height: 65%;"></span>
                        <span style="height: 80%;"></span>
                        <span style="height: 75%;"></span>
                        <span style="height: 90%;"></span>
                    </div>
                    <div class="chart-legend"><em>Avg Recovery rate: 82% over the last 5 weeks</em></div>
                </div>
            </div>

            <!-- Disease Statistics Card -->
            <div class="card">
                <div class="card-head">
                    <h3>Chronic Diseases distribution</h3>
                </div>
                <div class="table-wrap">
                    <table class="tbl">
                        <thead>
                            <tr>
                                <th>Condition</th>
                                <th>Resident Count</th>
                                <th>Trend State</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><b>Hypertension</b></td>
                                <td>24 Patients</td>
                                <td><span class="badge green">Stable</span></td>
                            </tr>
                            <tr>
                                <td><b>Diabetes Type II</b></td>
                                <td>18 Patients</td>
                                <td><span class="badge blue">Controlled</span></td>
                            </tr>
                            <tr>
                                <td><b>Arthritis / Mobility Problems</b></td>
                                <td>14 Patients</td>
                                <td><span class="badge amber">Increasing</span></td>
                            </tr>
                            <tr>
                                <td><b>Mild Dementia</b></td>
                                <td>8 Patients</td>
                                <td><span class="badge green">Stable</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Monthly & Lab Reports -->
        <div class="card animate-fade-in mt-4">
            <div class="card-head">
                <h3>Recent Laboratory & Lab Reports</h3>
            </div>
            <div class="table-wrap">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Report Name</th>
                            <th>Patient</th>
                            <th>Physician</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($reports)): ?>
                            <tr><td colspan="6" class="text-center text-muted py-4">No medical reports found.</td></tr>
                        <?php else: ?>
                            <?php foreach ($reports as $rep): ?>
                                <?php
                                    $badge = 'amber';
                                    if ($rep['status'] === 'approved') $badge = 'green';
                                    elseif ($rep['status'] === 'rejected') $badge = 'red';
                                ?>
                                <tr>
                                    <td><b><?php echo sn_e($rep['report_name']); ?></b></td>
                                    <td><?php echo sn_e($rep['resident_name']); ?></td>
                                    <td><?php echo sn_e($rep['physician_name'] ?? 'System Upload'); ?></td>
                                    <td><?php echo date('d M Y', strtotime($rep['report_date'])); ?></td>
                                    <td><span class="badge <?php echo $badge; ?>"><?php echo ucfirst(sn_e($rep['status'])); ?></span></td>
                                    <td>
                                        <button class="btn btn-outline-primary btn-tiny" onclick="window.open('<?php echo sn_e($rep['file_path']); ?>')"><i class="bi bi-file-earmark-pdf"></i> Download PDF</button>
                                        <?php if ($rep['status'] === 'pending'): ?>
                                            <form method="POST" action="health_reports.php" class="d-inline approve-report-form">
                                                <input type="hidden" name="action" value="approve">
                                                <input type="hidden" name="report_id" value="<?php echo $rep['report_id']; ?>">
                                                <button type="submit" class="btn btn-success btn-tiny text-white ms-1"><i class="bi bi-check-circle"></i> Approve</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</main>

        <?php if ($formSuccess): ?>
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: <?php echo json_encode($formSuccess); ?>,
                            confirmButtonColor: '#2b4c3f'
                        });
                    }
                });
            </script>
        <?php endif; ?>
        <?php if ($formError): ?>
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: <?php echo json_encode($formError); ?>,
                            confirmButtonColor: '#2b4c3f'
                        });
                    }
                });
            </script>
        <?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.approve-report-form').forEach(form => {
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Approve Report?',
                    text: 'Do you want to mark this medical report as approved?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#2b4c3f',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, approve!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            } else {
                if (confirm('Approve this medical report?')) {
                    form.submit();
                }
            }
        });
    });
});
</script>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
