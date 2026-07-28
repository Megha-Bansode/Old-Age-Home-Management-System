<?php
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

// Get selected resident ID
$resident_id = (int)($_GET['id'] ?? 0);

// Handle POST clinical notes save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_note') {
    $notes = trim($_POST['notes'] ?? '');
    $res_id = (int)($_POST['resident_id'] ?? 0);
    
    if ($res_id > 0 && !empty($notes)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO health_records (resident_id, recorded_by, record_date, clinical_notes) VALUES (?, ?, NOW(), ?)");
            $stmt->execute([$res_id, $user_id, $notes]);
            $formSuccess = "Clinical notes saved successfully!";
            $resident_id = $res_id; // Keep selected resident
        } catch (Exception $e) {
            $formError = "Error saving notes: " . $e->getMessage();
        }
    } else {
        $formError = "Please enter clinical notes.";
    }
}

// Fetch all active residents for dropdown switcher
$residents_list = $pdo->query("SELECT resident_id, full_name, room_number FROM residents WHERE status = 'Active' ORDER BY full_name ASC")->fetchAll();

// Find selected resident
$resident = null;
if ($resident_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM residents WHERE resident_id = ? AND status = 'Active'");
    $stmt->execute([$resident_id]);
    $resident = $stmt->fetch();
}

if (!$resident && !empty($residents_list)) {
    $resident_id = (int)$residents_list[0]['resident_id'];
    $stmt = $pdo->prepare("SELECT * FROM residents WHERE resident_id = ?");
    $stmt->execute([$resident_id]);
    $resident = $stmt->fetch();
}

// Fetch vital signs (latest health record)
$vitals = null;
if ($resident_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM health_records WHERE resident_id = ? AND (systolic_bp IS NOT NULL OR pulse IS NOT NULL) ORDER BY record_date DESC LIMIT 1");
    $stmt->execute([$resident_id]);
    $vitals = $stmt->fetch();
}

// Extract initials
$initials = '';
if ($resident) {
    $parts = explode(' ', $resident['full_name']);
    foreach ($parts as $p) {
        $initials .= strtoupper(substr($p, 0, 1));
    }
    $initials = substr($initials, 0, 2);
}

// Fetch current medications (from prescriptions)
$prescriptions = [];
if ($resident_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM prescriptions WHERE resident_id = ? ORDER BY prescription_id DESC");
    $stmt->execute([$resident_id]);
    $prescriptions = $stmt->fetchAll();
}

// Fetch timeline events (UNION of appointments, prescriptions, emergency cases)
$timeline = [];
if ($resident_id > 0) {
    $timeline_sql = "
        SELECT appointment_date AS event_date, reason AS title, notes AS description, 'appointment' AS type 
        FROM appointments WHERE resident_id = ?
        UNION ALL
        SELECT prescription_date AS event_date, diagnosis AS title, notes AS description, 'prescription' AS type 
        FROM prescriptions WHERE resident_id = ?
        UNION ALL
        SELECT created_at AS event_date, 'Emergency Incident' AS title, incident_description AS description, 'emergency' AS type 
        FROM emergency_cases WHERE resident_id = ?
        ORDER BY event_date DESC LIMIT 5
    ";
    $stmt = $pdo->prepare($timeline_sql);
    $stmt->execute([$resident_id, $resident_id, $resident_id]);
    $timeline = $stmt->fetchAll();
}

// Fetch doctor clinical notes history
$clinical_notes_history = [];
if ($resident_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM health_records WHERE resident_id = ? AND notes IS NOT NULL ORDER BY record_date DESC LIMIT 3");
    $stmt->execute([$resident_id]);
    $clinical_notes_history = $stmt->fetchAll();
}

// Helper to parse notes
if (!function_exists('get_prescription_details')) {
    function get_prescription_details($row, $pdo) {
        $notes = $row['notes'] ?? '';
        $details = json_decode($notes, true);
        
        $stmt = $pdo->prepare("SELECT full_name FROM users WHERE id = ?");
        $stmt->execute([$row['doctor_id']]);
        $dr_name = $stmt->fetchColumn() ?: 'Dr. Watson';
        
        if (is_array($details) && isset($details['medicines'])) {
            return [
                'doctor_name' => $details['doctor_name'] ?? $dr_name,
                'status' => $details['status'] ?? 'Active',
                'medicines' => $details['medicines']
            ];
        }
        
        return [
            'doctor_name' => $dr_name,
            'status' => 'Active',
            'medicines' => [
                [
                    'name' => $row['diagnosis'] ?: 'General Medication',
                    'instruction' => $row['notes'] ?: 'Take as directed',
                    'dosage' => '1 Tab',
                    'frequency' => 'Daily',
                    'duration' => 'Ongoing'
                ]
            ]
        ];
    }
}

$base_path = '../../';
$page_title = 'Patient Medical Records | SevaNest';
$extra_css = [
    'assets/css/doctor.css'
];

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'doctor';
$currentPage   = 'medical_records.php';
$sn_asset_root = "../../assets";
$base_path = '../../';
include '../../includes/sidebar.php';
?>

<!-- ═══════════════════════════════════════════════════════════════════════
     MAIN CONTENT AREA
     ═══════════════════════════════════════════════════════════════════════ -->
<main id="sn-main-content" role="main" aria-label="Doctor medical records content">
    <div class="doctor-page-wrapper">
        
        <!-- Header Strip -->
        <div class="dr-header-strip animate-fade-in">
            <div class="d-flex align-items-center gap-3">
                <div class="res-photo" style="width: 50px; height: 50px; font-size: 1.25rem;"><?php echo sn_e($initials); ?></div>
                <div>
                    <h2 class="dr-header-strip__title" style="font-size: 24px;"><?php echo sn_e($resident['full_name']); ?></h2>
                    <p style="color: var(--color-text-muted-team); margin: 2px 0 0;">
                        <?php 
                            $dob = new DateTime($resident['date_of_birth']);
                            $now = new DateTime();
                            $age = $now->diff($dob)->y;
                            echo sn_e($resident['gender']) . ', ' . $age . ' Years Old · Room ' . sn_e($resident['room_number'] ?? 'N/A') . ' · Case ID: #MD-908' . $resident['resident_id'];
                        ?>
                    </p>
                </div>
            </div>
            <div>
                <select class="select" id="patientSelect" onchange="location = 'medical_records.php?id=' + this.value;">
                    <option value="">Select Patient...</option>
                    <?php foreach ($residents_list as $res): ?>
                        <option value="<?php echo $res['resident_id']; ?>" <?php echo ($res['resident_id'] == $resident_id) ? 'selected' : ''; ?>>
                            <?php echo sn_e($res['full_name']); ?> (Room <?php echo sn_e($res['room_number'] ?? 'N/A'); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Vital Signs (Horizontal Grid) -->
        <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 15px; color: var(--color-text);">Current Vital Signs</h3>
        <div class="vitals-grid animate-fade-in">
            <div class="vital-card">
                <div class="vital-icon red"><i class="bi bi-heart-pulse-fill"></i></div>
                <div class="vital-details">
                    <span>Blood Pressure</span>
                    <h4><?php echo ($vitals && $vitals['systolic_bp']) ? ($vitals['systolic_bp'] . '/' . $vitals['diastolic_bp'] . ' mmHg') : '138/85 mmHg'; ?></h4>
                </div>
            </div>
            <div class="vital-card">
                <div class="vital-icon"><i class="bi bi-activity"></i></div>
                <div class="vital-details">
                    <span>Heart Rate</span>
                    <h4><?php echo ($vitals && $vitals['pulse_rate']) ? ($vitals['pulse_rate'] . ' bpm') : '76 bpm'; ?></h4>
                </div>
            </div>
            <div class="vital-card">
                <div class="vital-icon blue"><i class="bi bi-rulers"></i></div>
                <div class="vital-details">
                    <span>Temperature</span>
                    <h4><?php echo ($vitals && $vitals['temperature']) ? ($vitals['temperature'] . ' °F') : '98.4 °F'; ?></h4>
                </div>
            </div>
            <div class="vital-card">
                <div class="vital-icon blue"><i class="bi bi-speedometer2"></i></div>
                <div class="vital-details">
                    <span>Weight</span>
                    <h4><?php echo ($vitals && $vitals['weight']) ? ($vitals['weight'] . ' kg') : '68 kg'; ?></h4>
                </div>
            </div>
            <div class="vital-card">
                <div class="vital-icon gold"><i class="bi bi-calculator"></i></div>
                <div class="vital-details">
                    <span>General Status</span>
                    <h4 style="font-size: 0.9rem;"><?php echo ($vitals && $vitals['general_condition']) ? sn_e($vitals['general_condition']) : 'Stable'; ?></h4>
                </div>
            </div>
        </div>

        <!-- Two Column Content -->
        <div class="grid two-col animate-fade-in">
            
            <!-- Left Side · Diagnosis & Allergies -->
            <div class="d-flex flex-column gap-4">
                
                <!-- Diagnoses Card -->
                <div class="card">
                    <div class="card-head">
                        <h3>Active Diagnoses</h3>
                    </div>
                    <ul class="tl">
                        <?php
                            $conds = array_filter(array_map('trim', explode(',', $resident['medical_conditions'] ?? '')));
                            if (empty($conds)) {
                                echo '<li>No active diagnoses listed.</li>';
                            } else {
                                foreach ($conds as $c) {
                                    echo '<li><b>' . sn_e($c) . '</b></li>';
                                }
                            }
                        ?>
                    </ul>
                </div>

                <!-- Allergies Card -->
                <div class="card">
                    <div class="card-head">
                        <h3>Known Allergies</h3>
                    </div>
                    <div class="d-flex gap-2">
                        <span class="badge red"><i class="bi bi-exclamation-octagon me-1"></i> Penicillin</span>
                        <span class="badge red"><i class="bi bi-exclamation-octagon me-1"></i> Peanuts</span>
                    </div>
                </div>

                <!-- Current Medications -->
                <div class="card">
                    <div class="card-head">
                        <h3>Current Medications</h3>
                    </div>
                    <div class="table-wrap">
                        <table class="tbl">
                            <thead>
                                <tr>
                                    <th>Medicine</th>
                                    <th>Dosage</th>
                                    <th>Frequency</th>
                                    <th>Duration</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    $med_found = false;
                                    foreach ($prescriptions as $p) {
                                        $details = get_prescription_details($p, $pdo);
                                        foreach ($details['medicines'] as $med) {
                                            $med_found = true;
                                            ?>
                                            <tr>
                                                <td><b><?php echo sn_e($med['name']); ?></b></td>
                                                <td><?php echo sn_e($med['dosage']); ?></td>
                                                <td><?php echo sn_e($med['frequency']); ?></td>
                                                <td><?php echo sn_e($med['duration']); ?></td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                    if (!$med_found) {
                                        echo '<tr><td colspan="4" class="text-center text-muted">No medications prescribed.</td></tr>';
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <!-- Right Side · Medical Timeline & Notes -->
            <div class="d-flex flex-column gap-4">
                
                <!-- Medical Timeline -->
                <div class="card">
                    <div class="card-head">
                        <h3>Medical History Timeline</h3>
                    </div>
                    <ul class="med-timeline">
                        <?php if (empty($timeline)): ?>
                            <div class="text-muted small p-3">No medical history events logged.</div>
                        <?php else: ?>
                            <?php foreach ($timeline as $t): ?>
                                <?php
                                    $timeline_class = '';
                                    if ($t['type'] === 'emergency') $timeline_class = 'emergency';
                                    elseif ($t['type'] === 'prescription') $timeline_class = 'prescription';
                                ?>
                                <li class="med-timeline-item <?php echo $timeline_class; ?>">
                                    <div class="med-timeline-time"><?php echo date('d M Y', strtotime($t['event_date'])); ?></div>
                                    <div class="med-timeline-title"><?php echo sn_e($t['title']); ?></div>
                                    <div class="med-timeline-desc"><?php echo sn_e($t['description']); ?></div>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- Doctor Notes -->
                <div class="card">
                    <div class="card-head">
                        <h3>Doctor's Clinical Notes</h3>
                    </div>
                    
                    <?php if (!empty($clinical_notes_history)): ?>
                        <div class="mb-3 border-bottom pb-2">
                            <span class="text-muted small fw-semibold">Recent Notes:</span>
                            <?php foreach ($clinical_notes_history as $hist): ?>
                                <div class="bg-light p-2 rounded mb-2 small">
                                    <div class="d-flex justify-content-between text-muted mb-1" style="font-size: 0.75rem;">
                                        <span>Logged Note</span>
                                        <span><?php echo date('Y-m-d H:i', strtotime($hist['record_date'])); ?></span>
                                    </div>
                                    <p class="mb-0 text-dark"><?php echo sn_e($hist['clinical_notes']); ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="medical_records.php?id=<?php echo $resident_id; ?>">
                        <input type="hidden" name="action" value="save_note">
                        <input type="hidden" name="resident_id" value="<?php echo $resident_id; ?>">
                        <div class="mb-3">
                            <textarea name="notes" class="form-control" rows="4" style="font-size: var(--font-size-sm); border: 1px solid var(--color-border); border-radius: var(--radius-medium); padding: 12px; width: 100%;" placeholder="Add clinical checkup notes..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-tiny align-self-end"><i class="bi bi-save me-1"></i> Save Note</button>
                    </form>
                </div>

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

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
