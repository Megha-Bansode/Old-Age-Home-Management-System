<?php
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../config/database.php';

// Require caretaker login
require_login();
require_role('Caretaker');

$pdo = get_db_connection();
$caretaker_id = $_SESSION['user_id'] ?? 4; // Default to Radhika

$formSuccess = '';
$formError = '';

// Seed mock cases if special_care is empty
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM special_care");
    if ($stmt->fetchColumn() == 0) {
        $mocks = [
            ['resident_id' => 1, 'instruction' => '{"condition":"Post-fall recovery","priority":"High","medicine":"8AM · 2PM · 8PM","instruction":"Assisted mobility, 2h checks"}', 'status' => 'Active', 'assigned_to' => 4],
            ['resident_id' => 2, 'instruction' => '{"condition":"Diabetes Type II","priority":"Medium","medicine":"Before meals","instruction":"Sugar monitoring 3x/day"}', 'status' => 'Active', 'assigned_to' => 4],
            ['resident_id' => 3, 'instruction' => '{"condition":"Hypertension","priority":"Medium","medicine":"9AM · 9PM","instruction":"Low-salt diet, BP twice"}', 'status' => 'Active', 'assigned_to' => 4],
            ['resident_id' => 4, 'instruction' => '{"condition":"Alzheimer\'s — mild","priority":"High","medicine":"Morning only","instruction":"Memory support, escort"}', 'status' => 'Active', 'assigned_to' => 4]
        ];
        foreach ($mocks as $m) {
            $stmt_ins = $pdo->prepare("INSERT INTO special_care (resident_id, instruction, status, assigned_to) VALUES (?, ?, ?, ?)");
            $stmt_ins->execute([$m['resident_id'], $m['instruction'], $m['status'], $m['assigned_to']]);
        }
    }
} catch (Exception $e) {}

// Handle POST request to add a case
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_case') {
    $resident_id = (int)($_POST['resident_id'] ?? 0);
    $assigned_to = (int)($_POST['assigned_to'] ?? 0);
    $condition = trim($_POST['condition'] ?? '');
    $priority = trim($_POST['priority'] ?? 'Medium');
    $medicine = trim($_POST['medicine'] ?? '');
    $instructions_text = trim($_POST['instructions'] ?? '');
    
    if ($resident_id > 0 && !empty($instructions_text)) {
        try {
            $instruction_json = json_encode([
                'condition' => $condition,
                'priority' => $priority,
                'medicine' => $medicine,
                'instruction' => $instructions_text
            ]);
            
            $stmt = $pdo->prepare("INSERT INTO special_care (resident_id, instruction, status, assigned_to) VALUES (?, ?, 'Active', ?)");
            $stmt->execute([$resident_id, $instruction_json, $assigned_to > 0 ? $assigned_to : null]);
            
            // Add activity log
            $stmt_res = $pdo->prepare("SELECT full_name FROM residents WHERE resident_id = ?");
            $stmt_res->execute([$resident_id]);
            $res_name = $stmt_res->fetchColumn();
            
            $log_stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action, description) VALUES (?, 'Special Care Assignment', ?)");
            $log_stmt->execute([$caretaker_id, "Assigned special care for {$res_name}"]);
            
            $formSuccess = "Special care case assigned successfully!";
        } catch (Exception $e) {
            $formError = "Error adding case: " . $e->getMessage();
        }
    } else {
        $formError = "Please select a resident and fill out instructions.";
    }
}

// Fetch search and filters
$search = trim($_GET['search'] ?? '');
$priority_filter = trim($_GET['priority'] ?? '');
$caretaker_filter = trim($_GET['caretaker'] ?? '');

$sql = "SELECT sc.*, r.full_name AS resident_name, r.room_number, r.date_of_birth, r.age, u.full_name AS caretaker_name 
        FROM special_care sc 
        JOIN residents r ON sc.resident_id = r.resident_id 
        LEFT JOIN users u ON sc.assigned_to = u.id 
        WHERE 1=1";
$params = [];

if ($search !== '') {
    $sql .= " AND (r.full_name LIKE ? OR r.room_number LIKE ? OR sc.instruction LIKE ?)";
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
}

if ($caretaker_filter !== '') {
    $sql .= " AND u.full_name = ?";
    $params[] = $caretaker_filter;
}

$sql .= " ORDER BY sc.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$cases = $stmt->fetchAll();

// Filter list by Priority in PHP if filter is active
if ($priority_filter !== '') {
    $filtered_cases = [];
    foreach ($cases as $c) {
        $decoded = json_decode($c['instruction'], true);
        $priority = $decoded['priority'] ?? 'Medium';
        if (strcasecmp($priority, $priority_filter) === 0) {
            $filtered_cases[] = $c;
        }
    }
    $cases = $filtered_cases;
}

// Fetch all residents and caretakers for dropdowns
$all_residents = $pdo->query("SELECT resident_id, full_name FROM residents WHERE status = 'Active' ORDER BY full_name ASC")->fetchAll();
$all_caretakers = $pdo->query("SELECT id, full_name FROM users WHERE role = 'Caretaker' ORDER BY full_name ASC")->fetchAll();

$base_path = '../../';
$page_title = 'Special Care | SevaNest';
$extra_css = [
    'assets/css/caretaker.css'
];

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'caretaker';
$currentPage   = 'specialcare.php';
$sn_asset_root = "../../assets";
$base_path = '../../';
include '../../includes/sidebar.php';
?>

<!-- ═══════════════════════════════════════════════════════════════════════
     MAIN CONTENT AREA
     ═══════════════════════════════════════════════════════════════════════ -->
<main id="sn-main-content" role="main" aria-label="Caretaker special care content">
    <div class="pages">
        
        <div class="page-head animate-fade-in">
          <div>
            <h2 class="page-title">Special Care</h2>
            <p class="page-sub">Residents requiring close attention and medical priority.</p>
          </div>
          <div class="page-actions">
            <button class="btn primary" id="addCaseBtn">+ Add Case</button>
          </div>
        </div>

        <div class="toolbar">
          <div class="search inline">
            <span>🔎</span>
            <input placeholder="Search by name or condition…" id="scSearch" value="<?php echo sn_e($search); ?>" onkeyup="if(event.key==='Enter') location.href='?priority=<?php echo $priority_filter; ?>&caretaker=<?php echo $caretaker_filter; ?>&search='+encodeURIComponent(this.value)">
          </div>
          <select class="select" id="prioritySelect" onchange="location.href='?search=<?php echo urlencode($search); ?>&caretaker=<?php echo $caretaker_filter; ?>&priority='+this.value">
            <option value="" <?php echo ($priority_filter === '') ? 'selected' : ''; ?>>All Priorities</option>
            <option value="High" <?php echo ($priority_filter === 'High') ? 'selected' : ''; ?>>High</option>
            <option value="Medium" <?php echo ($priority_filter === 'Medium') ? 'selected' : ''; ?>>Medium</option>
            <option value="Low" <?php echo ($priority_filter === 'Low') ? 'selected' : ''; ?>>Low</option>
          </select>
          <select class="select" id="caretakerSelect" onchange="location.href='?search=<?php echo urlencode($search); ?>&priority=<?php echo $priority_filter; ?>&caretaker='+this.value">
            <option value="" <?php echo ($caretaker_filter === '') ? 'selected' : ''; ?>>All Caretakers</option>
            <?php foreach ($all_caretakers as $ct): ?>
                <option value="<?php echo sn_e($ct['full_name']); ?>" <?php echo ($caretaker_filter === $ct['full_name']) ? 'selected' : ''; ?>><?php echo sn_e($ct['full_name']); ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="card no-pad">
          <div class="table-wrap">
            <table class="tbl" id="specialTable">
              <thead>
                <tr>
                  <th>Resident</th><th>Age</th><th>Room</th><th>Condition</th>
                  <th>Care Instructions</th><th>Caretaker</th><th>Medicine</th>
                  <th>Priority</th><th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($cases)): ?>
                    <tr><td colspan="9" class="text-center text-muted py-4">No special care cases found.</td></tr>
                <?php else: ?>
                    <?php foreach ($cases as $case): ?>
                        <?php
                            $decoded = json_decode($case['instruction'], true);
                            $cond = $decoded['condition'] ?? ($case['health_status'] ?? 'N/A');
                            $priority = $decoded['priority'] ?? 'Medium';
                            $medicine = $decoded['medicine'] ?? 'As prescribed';
                            $instructions = $decoded['instruction'] ?? $case['instruction'];
                            
                            $initials = '';
                            $parts = explode(' ', $case['resident_name']);
                            foreach ($parts as $p) {
                                $initials .= strtoupper(substr($p, 0, 1));
                            }
                            $initials = substr($initials, 0, 2);
                            
                            $p_badge = 'green';
                            if (strcasecmp($priority, 'High') === 0) $p_badge = 'red';
                            elseif (strcasecmp($priority, 'Medium') === 0) $p_badge = 'amber';
                            
                            $s_badge = 'green';
                            if (strcasecmp($case['status'], 'Active') === 0) $s_badge = 'amber';
                        ?>
                        <tr>
                          <td>
                            <div class="res-cell">
                              <div class="res-photo"><?php echo sn_e($initials); ?></div>
                              <div><b><?php echo sn_e($case['resident_name']); ?></b><em>Room <?php echo sn_e($case['room_number']); ?></em></div>
                            </div>
                          </td>
                          <td><?php echo $case['age']; ?></td>
                          <td><?php echo sn_e($case['room_number']); ?></td>
                          <td><?php echo sn_e($cond); ?></td>
                          <td><?php echo sn_e($instructions); ?></td>
                          <td><?php echo sn_e($case['caretaker_name'] ?? 'Unassigned'); ?></td>
                          <td><?php echo sn_e($medicine); ?></td>
                          <td><span class="badge <?php echo $p_badge; ?>"><?php echo sn_e($priority); ?></span></td>
                          <td><span class="badge <?php echo $s_badge; ?>"><?php echo sn_e($case['status']); ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

    </div>
</main>

<!-- Modal to Add Special Care Case -->
<div class="modal fade" id="caseModal" tabindex="-1" aria-labelledby="caseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <form method="POST" action="specialcare.php">
                <input type="hidden" name="action" value="add_case">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="caseModalLabel">Add Special Care Case</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Resident <span class="text-danger">*</span></label>
                        <select name="resident_id" class="form-select select" required style="width:100%; border: 1px solid var(--color-border); border-radius: var(--radius-medium); padding: 8px;">
                            <option value="">Select Resident...</option>
                            <?php foreach ($all_residents as $r): ?>
                                <option value="<?php echo $r['resident_id']; ?>"><?php echo sn_e($r['full_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Condition / Health Concern</label>
                        <input type="text" name="condition" class="form-control select" placeholder="e.g. Diabetes Type II" style="width:100%; border: 1px solid var(--color-border); border-radius: var(--radius-medium); padding: 8px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Caretaker Assigned</label>
                        <select name="assigned_to" class="form-select select" style="width:100%; border: 1px solid var(--color-border); border-radius: var(--radius-medium); padding: 8px;">
                            <option value="">Select Caretaker...</option>
                            <?php foreach ($all_caretakers as $ct): ?>
                                <option value="<?php echo $ct['id']; ?>"><?php echo sn_e($ct['full_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Priority</label>
                        <select name="priority" class="form-select select" style="width:100%; border: 1px solid var(--color-border); border-radius: var(--radius-medium); padding: 8px;">
                            <option value="High">High</option>
                            <option value="Medium" selected>Medium</option>
                            <option value="Low">Low</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Medicine Schedule</label>
                        <input type="text" name="medicine" class="form-control select" placeholder="e.g. 9AM · 9PM" style="width:100%; border: 1px solid var(--color-border); border-radius: var(--radius-medium); padding: 8px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Care Instructions <span class="text-danger">*</span></label>
                        <textarea name="instructions" class="form-control select" rows="3" required style="width:100%; border: 1px solid var(--color-border); border-radius: var(--radius-medium); padding: 8px;" placeholder="Assisted mobility, regular updates..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-sm">Assign Case</button>
                </div>
            </form>
        </div>
    </div>
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

<script>
document.addEventListener('DOMContentLoaded', () => {
    const addCaseBtn = document.getElementById('addCaseBtn');
    if (addCaseBtn) {
        addCaseBtn.addEventListener('click', () => {
            const myModal = new bootstrap.Modal(document.getElementById('caseModal'));
            myModal.show();
        });
    }
});
</script>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
