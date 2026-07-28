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
    $stmt = $pdo->query("SELECT COUNT(*) FROM emergency_cases");
    if ($stmt->fetchColumn() == 0) {
        $mocks = [
            ['resident_id' => 1, 'reported_by' => $user_id, 'incident_description' => 'Fall detected in bathroom at 14:05 PM. Spoke of severe left hip joint pain. Vitals recorded: BP 150/90, Pulse 92.', 'action_taken' => 'Administered pain relief. Immobilized left leg. Awaiting X-Ray scan validation.', 'hospital_name' => 'City General Hospital', 'status' => 'Active', 'created_at' => date('Y-m-d H:i:s')],
            ['resident_id' => 2, 'reported_by' => $user_id, 'incident_description' => 'BP Spike to 175.', 'action_taken' => 'Gave anti-hypertensive medication. Monitored for 2 hours.', 'hospital_name' => NULL, 'status' => 'Resolved', 'created_at' => date('Y-m-d H:i:s', strtotime('-1 day'))],
            ['resident_id' => 5, 'reported_by' => $user_id, 'incident_description' => 'Chest pain complaint.', 'action_taken' => 'Conducted ECG. Advised bed rest and medication.', 'hospital_name' => NULL, 'status' => 'Resolved', 'created_at' => date('Y-m-d H:i:s', strtotime('-2 days'))]
        ];
        
        foreach ($mocks as $m) {
            $stmt_ins = $pdo->prepare("INSERT INTO emergency_cases (resident_id, reported_by, incident_description, action_taken, hospital_name, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt_ins->execute([$m['resident_id'], $m['reported_by'], $m['incident_description'], $m['action_taken'], $m['hospital_name'], $m['status'], $m['created_at']]);
        }
    }
} catch (Exception $e) {
    // Fail silently
}

// Handle POST resolve actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'resolve') {
    $case_id = (int)($_POST['case_id'] ?? 0);
    $action_taken = trim($_POST['action_taken'] ?? 'Resolved by Doctor.');
    try {
        $stmt = $pdo->prepare("UPDATE emergency_cases SET status = 'Resolved', action_taken = ? WHERE case_id = ?");
        $stmt->execute([$action_taken, $case_id]);
        $formSuccess = "Incident resolved successfully!";
    } catch (Exception $e) {
        $formError = "Error resolving incident: " . $e->getMessage();
    }
}

// Query latest active emergency case
$stmt = $pdo->prepare("SELECT ec.*, r.full_name AS resident_name, r.room_number, u.full_name AS caregiver_name, u.phone AS caregiver_phone
                       FROM emergency_cases ec 
                       JOIN residents r ON ec.resident_id = r.resident_id
                       LEFT JOIN users u ON ec.reported_by = u.id
                       WHERE ec.status = 'Active' 
                       ORDER BY ec.created_at DESC LIMIT 1");
$stmt->execute();
$active_case = $stmt->fetch();

// Query emergency history log
$stmt = $pdo->prepare("SELECT ec.*, r.full_name AS resident_name, r.room_number
                       FROM emergency_cases ec 
                       JOIN residents r ON ec.resident_id = r.resident_id
                       ORDER BY ec.created_at DESC");
$stmt->execute();
$all_cases = $stmt->fetchAll();

$base_path = '../../';
$page_title = 'Emergency Cases | SevaNest';
$extra_css = [
    'assets/css/doctor.css'
];

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'doctor';
$currentPage   = 'emergency_cases.php';
$sn_asset_root = "../../assets";
$base_path = '../../';
include '../../includes/sidebar.php';
?>

<!-- ═══════════════════════════════════════════════════════════════════════
     MAIN CONTENT AREA
     ═══════════════════════════════════════════════════════════════════════ -->
<main id="sn-main-content" role="main" aria-label="Doctor emergency cases content">
    <div class="doctor-page-wrapper">
        
        <!-- Header Strip -->
        <div class="dr-header-strip animate-fade-in" style="border-left: 5px solid var(--color-danger);">
            <div>
                <h2 class="dr-header-strip__title">🚨 Patient Emergency Board</h2>
                <p style="color: var(--color-text-muted-team); margin: 4px 0 0;">Critical incidents requiring immediate medical intervention or review.</p>
            </div>
        </div>

        <!-- Emergency Cases Grid -->
        <div class="grid two-col animate-fade-in">
            
            <!-- Left Side · Active Case Card -->
            <?php if (!$active_case): ?>
                <div class="card d-flex flex-column gap-3 justify-content-center align-items-center p-5 text-muted">
                    <i class="bi bi-shield-check" style="font-size: 3rem; color: var(--color-success);"></i>
                    <h4 class="mt-3 mb-0">No Active Emergencies</h4>
                    <p class="small text-center mb-0">All reported critical patient incidents have been resolved.</p>
                </div>
            <?php else: ?>
                <?php
                    $initials = '';
                    $parts = explode(' ', $active_case['resident_name']);
                    foreach ($parts as $p) {
                        $initials .= strtoupper(substr($p, 0, 1));
                    }
                    $initials = substr($initials, 0, 2);
                    
                    // Deduce wing
                    $room = $active_case['room_number'] ?? '';
                    $wing_name = 'Wing A';
                    if ($room === '204') $wing_name = 'Wing A';
                    elseif (strpos($room, '1') === 0) $wing_name = 'Wing A';
                    elseif (strpos($room, '2') === 0) $wing_name = 'Wing B';
                    elseif (strpos($room, '3') === 0) $wing_name = 'Wing C';
                ?>
                <div class="card d-flex flex-column gap-3" style="border-top: 4px solid var(--color-danger);">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="d-flex align-items-center gap-3">
                            <div class="res-photo" style="width: 46px; height: 46px; background-color: rgba(231, 111, 81, 0.1); color: var(--color-danger);"><?php echo sn_e($initials); ?></div>
                            <div>
                                <h4 style="margin: 0; color: var(--color-text); font-weight: 700;"><?php echo sn_e($active_case['resident_name']); ?></h4>
                                <span style="font-size: var(--font-size-xs); color: var(--color-text-muted-team);">Room <?php echo sn_e($room); ?> (<?php echo $wing_name; ?>)</span>
                            </div>
                        </div>
                        <span class="badge red">Critical Priority</span>
                    </div>
                    
                    <hr style="margin: 0; border-top: 1px solid var(--color-border);">
                    
                    <!-- Treatment Notes -->
                    <div>
                        <strong style="font-size: var(--font-size-sm); color: var(--color-text); display: block; margin-bottom: 4px;">Incident Details:</strong>
                        <p style="font-size: var(--font-size-sm); color: var(--color-text-muted-team); margin: 0; line-height: 1.5;"><?php echo sn_e($active_case['incident_description']); ?></p>
                    </div>

                    <div>
                        <strong style="font-size: var(--font-size-sm); color: var(--color-text); display: block; margin-bottom: 4px;">Immediate Treatment Notes:</strong>
                        <p style="font-size: var(--font-size-sm); color: var(--color-text-muted-team); margin: 0; line-height: 1.5;"><?php echo sn_e($active_case['action_taken'] ?? 'No immediate notes logged.'); ?></p>
                    </div>

                    <hr style="margin: 0; border-top: 1px solid var(--color-border);">

                    <!-- Ambulance and Caregiver Details -->
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <span style="font-size: var(--font-size-xs); color: var(--color-text-muted-team); display: block;">Hospital / Ambulance:</span>
                            <strong style="color: var(--color-success); font-size: var(--font-size-sm);"><i class="bi bi-truck me-1"></i> <?php echo sn_e($active_case['hospital_name'] ?? 'Dispatched (Nearby)'); ?></strong>
                        </div>
                        <div>
                            <span style="font-size: var(--font-size-xs); color: var(--color-text-muted-team); display: block;">Reported By:</span>
                            <strong style="color: var(--color-text); font-size: var(--font-size-sm);"><?php echo sn_e($active_case['caregiver_name'] ?? 'Staff On-Duty'); ?></strong>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-2">
                        <a href="tel:<?php echo sn_e($active_case['caregiver_phone'] ?? ''); ?>" class="btn btn-danger btn-tiny w-100 text-center"><i class="bi bi-telephone-outbound me-1"></i> Contact Caregiver</a>
                        <form method="POST" action="emergency_cases.php" id="resolve-incident-form" class="w-100">
                            <input type="hidden" name="action" value="resolve">
                            <input type="hidden" name="case_id" value="<?php echo $active_case['case_id']; ?>">
                            <input type="hidden" name="action_taken" id="resolve_action_taken" value="">
                            <button type="submit" class="btn btn-outline-secondary btn-tiny w-100"><i class="bi bi-check2-square me-1"></i> Resolve Incident</button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Right Side · Incident History Log -->
            <div class="card">
                <div class="card-head">
                    <h3>Recent Emergency Cases Logs</h3>
                </div>
                <div class="table-wrap">
                    <table class="tbl">
                        <thead>
                            <tr>
                                <th>Incident</th>
                                <th>Severity</th>
                                <th>Date/Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($all_cases)): ?>
                                <tr><td colspan="4" class="text-center text-muted">No emergency history logs.</td></tr>
                            <?php else: ?>
                                <?php foreach ($all_cases as $c): ?>
                                    <tr>
                                        <td><b><?php echo sn_e(explode('.', $c['incident_description'])[0]); ?></b> (<?php echo sn_e($c['resident_name']); ?>)</td>
                                        <td><span class="badge red">Critical</span></td>
                                        <td><?php echo date('d M Y H:i', strtotime($c['created_at'])); ?></td>
                                        <td>
                                            <?php if ($c['status'] === 'Active'): ?>
                                                <span class="badge red">Active</span>
                                            <?php else: ?>
                                                <span class="badge green">Resolved</span>
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
    const resolveForm = document.getElementById('resolve-incident-form');
    if (resolveForm) {
        resolveForm.addEventListener('submit', (e) => {
            e.preventDefault();
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Resolve Incident?',
                    text: 'Please describe the action taken to resolve this emergency case:',
                    input: 'text',
                    inputPlaceholder: 'e.g. Patient stabilized, hospital transit confirmed.',
                    showCancelButton: true,
                    confirmButtonColor: '#2b4c3f',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Submit & Resolve',
                    inputValidator: (value) => {
                        if (!value) {
                            return 'You must enter action taken description!';
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed && result.value) {
                        document.getElementById('resolve_action_taken').value = result.value;
                        resolveForm.submit();
                    }
                });
            } else {
                const desc = prompt('Describe action taken to resolve:');
                if (desc) {
                    document.getElementById('resolve_action_taken').value = desc;
                    resolveForm.submit();
                }
            }
        });
    }
});
</script>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
