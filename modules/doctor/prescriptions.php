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

// Programmatic seeder
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM prescriptions");
    if ($stmt->fetchColumn() == 0) {
        $mocks = [
            [
                'resident_id' => 1,
                'doctor_id' => $user_id,
                'prescription_date' => '2026-07-24',
                'diagnosis' => 'Hypertension & Type II Diabetes',
                'notes' => json_encode([
                    'doctor_name' => 'Dr. Robert Watson',
                    'status' => 'Active',
                    'medicines' => [
                        ['name' => 'Amlodipine (BP)', 'instruction' => 'Take with water after breakfast', 'dosage' => '5 mg', 'frequency' => '1 Tab', 'duration' => 'Chronic'],
                        ['name' => 'Metformin (Diabetes)', 'instruction' => 'Take twice daily with meals', 'dosage' => '500 mg', 'frequency' => '1 Tab', 'duration' => 'Chronic']
                    ]
                ])
            ],
            [
                'resident_id' => 2,
                'doctor_id' => $user_id,
                'prescription_date' => '2026-06-18',
                'diagnosis' => 'Type II Diabetes & Cholesterol',
                'notes' => json_encode([
                    'doctor_name' => 'Dr. Robert Watson',
                    'status' => 'Active',
                    'medicines' => [
                        ['name' => 'Metformin (Diabetes)', 'instruction' => 'Take twice daily with meals', 'dosage' => '500 mg', 'frequency' => '1 Tab', 'duration' => 'Chronic'],
                        ['name' => 'Atorvastatin (Cholesterol)', 'instruction' => 'Take once daily at bedtime', 'dosage' => '10 mg', 'frequency' => '1 Tab', 'duration' => 'Chronic']
                    ]
                ])
            ],
            [
                'resident_id' => 3,
                'doctor_id' => $user_id,
                'prescription_date' => '2026-05-10',
                'diagnosis' => 'Bacterial Infection',
                'notes' => json_encode([
                    'doctor_name' => 'Dr. Robert Watson',
                    'status' => 'Completed',
                    'medicines' => [
                        ['name' => 'Amoxicillin (Antibiotic)', 'instruction' => 'Take 3 times daily after meals', 'dosage' => '500 mg', 'frequency' => '1 Capsule', 'duration' => '7 Days']
                    ]
                ])
            ]
        ];
        foreach ($mocks as $m) {
            $stmt_ins = $pdo->prepare("INSERT INTO prescriptions (resident_id, doctor_id, prescription_date, diagnosis, notes) VALUES (?, ?, ?, ?, ?)");
            $stmt_ins->execute([$m['resident_id'], $m['doctor_id'], $m['prescription_date'], $m['diagnosis'], $m['notes']]);
        }
    }
} catch (Exception $e) {
    // Fail silently
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

// Handle Add Prescription POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $resident_id = (int)($_POST['resident_id'] ?? 0);
    $diagnosis = trim($_POST['diagnosis'] ?? '');
    
    // Parse medicine details from form
    $med_names = $_POST['med_name'] ?? [];
    $med_instructions = $_POST['med_instruction'] ?? [];
    $med_dosages = $_POST['med_dosage'] ?? [];
    $med_frequencies = $_POST['med_frequency'] ?? [];
    $med_durations = $_POST['med_duration'] ?? [];
    
    $medicines = [];
    for ($i = 0; $i < count($med_names); $i++) {
        if (!empty($med_names[$i])) {
            $medicines[] = [
                'name' => trim($med_names[$i]),
                'instruction' => trim($med_instructions[$i] ?? 'Take as directed'),
                'dosage' => trim($med_dosages[$i] ?? '1 Tab'),
                'frequency' => trim($med_frequencies[$i] ?? 'Daily'),
                'duration' => trim($med_durations[$i] ?? 'Ongoing')
            ];
        }
    }
    
    if ($resident_id > 0 && !empty($diagnosis) && !empty($medicines)) {
        try {
            $serialized = json_encode([
                'doctor_name' => $_SESSION['user_full_name'] ?? 'Dr. Priya Nair',
                'status' => 'Active',
                'medicines' => $medicines
            ]);
            
            $stmt = $pdo->prepare("INSERT INTO prescriptions (resident_id, doctor_id, prescription_date, diagnosis, notes) VALUES (?, ?, CURDATE(), ?, ?)");
            $stmt->execute([$resident_id, $user_id, $diagnosis, $serialized]);
            $formSuccess = "Prescription written successfully!";
        } catch (Exception $e) {
            $formError = "Error creating prescription: " . $e->getMessage();
        }
    } else {
        $formError = "Please select a resident, diagnosis, and enter at least one medicine.";
    }
}

// Fetch all active residents for select dropdown
$residents_list = $pdo->query("SELECT resident_id, full_name, room_number FROM residents WHERE status = 'Active' ORDER BY full_name ASC")->fetchAll();

// Fetch prescriptions
$search = trim($_GET['search'] ?? '');
$status = trim($_GET['status'] ?? '');

$sql = "SELECT p.*, r.full_name AS resident_name 
        FROM prescriptions p 
        JOIN residents r ON p.resident_id = r.resident_id 
        WHERE p.doctor_id = ?";
$params = [$user_id];

if ($search !== '') {
    $sql .= " AND r.full_name LIKE ?";
    $params[] = '%' . $search . '%';
}

$sql .= " ORDER BY p.prescription_id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$prescriptions = $stmt->fetchAll();

// Filter by status on client-side or parse notes status in query if possible. We can filter in PHP array.
if ($status !== '' && $status !== 'All Prescriptions') {
    $filtered = [];
    foreach ($prescriptions as $p) {
        $details = get_prescription_details($p, $pdo);
        if (strcasecmp($details['status'], $status) === 0) {
            $filtered[] = $p;
        }
    }
    $prescriptions = $filtered;
}

$base_path = '../../';
$page_title = 'Patient Prescriptions | SevaNest';
$extra_css = [
    'assets/css/doctor.css'
];

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'doctor';
$currentPage   = 'prescriptions.php';
$sn_asset_root = "../../assets";
$base_path = '../../';
include '../../includes/sidebar.php';
?>

<!-- ═══════════════════════════════════════════════════════════════════════
     MAIN CONTENT AREA
     ═══════════════════════════════════════════════════════════════════════ -->
<main id="sn-main-content" role="main" aria-label="Doctor prescriptions content">
    <div class="doctor-page-wrapper">
        
        <!-- Header Strip -->
        <div class="dr-header-strip animate-fade-in">
            <div>
                <h2 class="dr-header-strip__title">Patient Prescriptions</h2>
                <p style="color: var(--color-text-muted-team); margin: 4px 0 0;">Create, edit and manage prescriptions for residents.</p>
            </div>
            <div>
                <button class="btn btn-primary" id="newPresBtn"><i class="bi bi-plus-circle me-2"></i>New Prescription</button>
            </div>
        </div>

        <!-- Filter Toolbar -->
        <div class="toolbar">
            <div class="search inline">
                <span>🔎</span>
                <input placeholder="Search prescription by patient..." id="attSearch">
            </div>
            <select class="select">
                <option>All Prescriptions</option>
                <option>Active</option>
                <option>Completed</option>
                <option>Temporary</option>
            </select>
        </div>

        <!-- Prescription Grid -->
        <div class="prescription-grid animate-fade-in">
            <?php if (empty($prescriptions)): ?>
                <div class="card p-4 text-center text-muted w-100">No prescriptions found.</div>
            <?php else: ?>
                <?php foreach ($prescriptions as $p): ?>
                    <?php
                        $details = get_prescription_details($p, $pdo);
                        $status_badge = ($details['status'] === 'Completed') ? 'green' : (($details['status'] === 'Cancelled') ? 'red' : 'red');
                    ?>
                    <div class="prescription-card">
                        <div class="pres-card-header">
                            <h4><?php echo sn_e($p['resident_name']); ?></h4>
                            <span class="badge <?php echo $status_badge; ?>"><?php echo sn_e($details['status']); ?></span>
                        </div>
                        <div class="pres-card-body">
                            <p style="font-size: var(--font-size-xs); color: var(--color-text-muted-team); margin-bottom: 12px;">
                                <i class="bi bi-person me-1"></i> Prescribed by: <?php echo sn_e($details['doctor_name']); ?> · <?php echo date('d M Y', strtotime($p['prescription_date'])); ?>
                            </p>
                            <p class="small text-dark mb-2"><strong>Diagnosis:</strong> <?php echo sn_e($p['diagnosis']); ?></p>
                            <?php foreach ($details['medicines'] as $med): ?>
                                <div class="pres-med-row">
                                    <div>
                                        <span class="pres-med-name"><?php echo sn_e($med['name']); ?></span>
                                        <div class="pres-med-instruction"><?php echo sn_e($med['instruction']); ?></div>
                                    </div>
                                    <div style="text-align: right;">
                                        <strong><?php echo sn_e($med['dosage']); ?> · <?php echo sn_e($med['frequency']); ?></strong>
                                        <div><?php echo sn_e($med['duration']); ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="pres-card-footer">
                            <button class="btn tiny btn-outline-primary btn-print-prescription" onclick="window.print()"><i class="bi bi-download"></i> Download PDF</button>
                            <button class="btn tiny btn-outline-secondary btn-print-prescription" onclick="window.print()"><i class="bi bi-printer"></i> Print Rx</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

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

    </div>
</main>

<!-- Modal for creating new prescription -->
<div class="modal fade" id="prescriptionModal" tabindex="-1" aria-labelledby="prescriptionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <form method="POST" action="prescriptions.php">
                <input type="hidden" name="action" value="add">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="prescriptionModalLabel">Write New Prescription</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Resident Patient <span class="text-danger">*</span></label>
                            <select name="resident_id" class="form-select select" required style="width:100%; border: 1px solid var(--color-border); border-radius: var(--radius-medium); padding: 8px;">
                                <option value="">Select Resident...</option>
                                <?php foreach ($residents_list as $res): ?>
                                    <option value="<?php echo $res['resident_id']; ?>">
                                        <?php echo sn_e($res['full_name']); ?> (Room <?php echo sn_e($res['room_number'] ?? 'N/A'); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Diagnosis / Condition <span class="text-danger">*</span></label>
                            <input type="text" name="diagnosis" class="form-control select" required style="width:100%; border: 1px solid var(--color-border); border-radius: var(--radius-medium); padding: 8px;" placeholder="e.g. Hypertension, Infection">
                        </div>
                    </div>

                    <div class="border-top pt-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0 text-dark">Medicines List</h6>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="addMedRowBtn"><i class="bi bi-plus-circle me-1"></i>Add Medicine</button>
                        </div>

                        <div id="medicines-container">
                            <div class="row mb-3 medicine-row border-bottom pb-3">
                                <div class="col-md-4 mb-2">
                                    <label class="form-label small fw-semibold text-dark">Medicine Name <span class="text-danger">*</span></label>
                                    <input type="text" name="med_name[]" class="form-control form-control-sm" required style="border: 1px solid var(--color-border); border-radius: var(--radius-medium); padding: 6px;" placeholder="e.g. Paracetamol">
                                </div>
                                <div class="col-md-2 mb-2">
                                    <label class="form-label small fw-semibold text-dark">Dosage</label>
                                    <input type="text" name="med_dosage[]" class="form-control form-control-sm" style="border: 1px solid var(--color-border); border-radius: var(--radius-medium); padding: 6px;" placeholder="e.g. 500 mg">
                                </div>
                                <div class="col-md-2 mb-2">
                                    <label class="form-label small fw-semibold text-dark">Frequency</label>
                                    <input type="text" name="med_frequency[]" class="form-control form-control-sm" style="border: 1px solid var(--color-border); border-radius: var(--radius-medium); padding: 6px;" placeholder="e.g. 1 Tab daily">
                                </div>
                                <div class="col-md-2 mb-2">
                                    <label class="form-label small fw-semibold text-dark">Duration</label>
                                    <input type="text" name="med_duration[]" class="form-control form-control-sm" style="border: 1px solid var(--color-border); border-radius: var(--radius-medium); padding: 6px;" placeholder="e.g. 5 days">
                                </div>
                                <div class="col-md-2 mb-2">
                                    <label class="form-label small fw-semibold text-dark">Instruction</label>
                                    <input type="text" name="med_instruction[]" class="form-control form-control-sm" style="border: 1px solid var(--color-border); border-radius: var(--radius-medium); padding: 6px;" placeholder="e.g. After meals">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-sm">Save Prescription</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const newPresBtn = document.getElementById('newPresBtn');
    if (newPresBtn) {
        newPresBtn.addEventListener('click', () => {
            const myModal = new bootstrap.Modal(document.getElementById('prescriptionModal'));
            myModal.show();
        });
    }

    const addRowBtn = document.getElementById('addMedRowBtn');
    const container = document.getElementById('medicines-container');
    if (addRowBtn && container) {
        addRowBtn.addEventListener('click', () => {
            const newRow = document.createElement('div');
            newRow.className = 'row mb-3 medicine-row border-bottom pb-3 position-relative';
            newRow.innerHTML = `
                <div class="col-md-4 mb-2">
                    <label class="form-label small fw-semibold text-dark">Medicine Name <span class="text-danger">*</span></label>
                    <input type="text" name="med_name[]" class="form-control form-control-sm" required style="border: 1px solid var(--color-border); border-radius: var(--radius-medium); padding: 6px;" placeholder="e.g. Paracetamol">
                </div>
                <div class="col-md-2 mb-2">
                    <label class="form-label small fw-semibold text-dark">Dosage</label>
                    <input type="text" name="med_dosage[]" class="form-control form-control-sm" style="border: 1px solid var(--color-border); border-radius: var(--radius-medium); padding: 6px;" placeholder="e.g. 500 mg">
                </div>
                <div class="col-md-2 mb-2">
                    <label class="form-label small fw-semibold text-dark">Frequency</label>
                    <input type="text" name="med_frequency[]" class="form-control form-control-sm" style="border: 1px solid var(--color-border); border-radius: var(--radius-medium); padding: 6px;" placeholder="e.g. 1 Tab daily">
                </div>
                <div class="col-md-2 mb-2">
                    <label class="form-label small fw-semibold text-dark">Duration</label>
                    <input type="text" name="med_duration[]" class="form-control form-control-sm" style="border: 1px solid var(--color-border); border-radius: var(--radius-medium); padding: 6px;" placeholder="e.g. 5 days">
                </div>
                <div class="col-md-2 mb-2">
                    <label class="form-label small fw-semibold text-dark">Instruction</label>
                    <input type="text" name="med_instruction[]" class="form-control form-control-sm" style="border: 1px solid var(--color-border); border-radius: var(--radius-medium); padding: 6px;" placeholder="e.g. After meals">
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-med position-absolute" style="top: 0; right: 10px; width: auto; font-size: 0.75rem; padding: 2px 6px;">Remove</button>
            `;
            container.appendChild(newRow);

            newRow.querySelector('.btn-remove-med').addEventListener('click', () => {
                newRow.remove();
            });
        });
    }
});
</script>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
