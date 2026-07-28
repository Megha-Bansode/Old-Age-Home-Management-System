<?php
/**
 * SevaNest — Resident Registration
 * File     : modules/admin/resident_registration.php
 * Version  : 1.0
 */

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/functions.php';

// Require Admin login
require_login();
require_role(['Admin', 'Old Age Home Admin']);

$base_path = '../../';
$page_title = 'Resident Registration | SevaNest';

// Database Integration
require_once __DIR__ . '/../../config/database.php';
$pdo = get_db_connection();

$formSuccess = '';
$formError = '';

// Helper to determine health grade badge
if (!function_exists('get_health_grade')) {
    function get_health_grade($health_status) {
        if (empty($health_status)) {
            return 'Stable';
        }
        if (stripos($health_status, 'Critical') !== false) {
            return 'Critical';
        }
        if (stripos($health_status, 'Needs Care') !== false || stripos($health_status, 'Care') !== false) {
            return 'Needs Care';
        }
        return 'Stable';
    }
}

// Handle Delete Action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $del_id = (int)$_GET['id'];
    $is_ajax = (isset($_GET['ajax']) && $_GET['ajax'] == '1');
    try {
        $stmt = $pdo->prepare("DELETE FROM residents WHERE resident_id = ?");
        $stmt->execute([$del_id]);
        if ($is_ajax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Resident deleted successfully!']);
            exit;
        }
        $formSuccess = 'Resident deleted successfully!';
    } catch (Exception $e) {
        if ($is_ajax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Error deleting resident: ' . $e->getMessage()]);
            exit;
        }
        $formError = 'Error deleting resident: ' . $e->getMessage();
    }
}

// Handle Form Submission (Register/Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'register';
    $resident_id = $_POST['resident_id'] ?? null;
    $is_ajax = (isset($_POST['ajax']) && $_POST['ajax'] == '1');
    
    $full_name = trim($_POST['full_name'] ?? '');
    $dob = $_POST['dob'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $blood_group = $_POST['blood_group'] ?? NULL;
    $room_no = trim($_POST['room_no'] ?? '');
    $admission_date = $_POST['admission_date'] ?? '';
    
    $guardian_name = trim($_POST['guardian_name'] ?? '');
    $relation = trim($_POST['relation'] ?? '');
    $guardian_phone = trim($_POST['guardian_phone'] ?? '');
    $guardian_email = trim($_POST['guardian_email'] ?? '');
    $guardian_address = trim($_POST['address'] ?? '');
    
    $notes = trim($_POST['notes'] ?? '');

    if (empty($full_name) || empty($dob) || empty($gender) || empty($admission_date) || empty($guardian_name) || empty($guardian_phone)) {
        $msg = 'Please fill in all required fields.';
        if ($is_ajax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $msg]);
            exit;
        }
        $formError = $msg;
    } else {
        // Calculate age
        $dob_dt = new DateTime($dob);
        $now_dt = new DateTime();
        $age = $now_dt->diff($dob_dt)->y;
        
        // Construct health status by appending notes, relation, email
        $health_status_parts = [];
        if (!empty($notes)) {
            $health_status_parts[] = "Medical Notes: " . $notes;
        }
        if (!empty($relation)) {
            $health_status_parts[] = "Relation to Guardian: " . $relation;
        }
        if (!empty($guardian_email)) {
            $health_status_parts[] = "Guardian Email: " . $guardian_email;
        }
        $health_status = implode("\n", $health_status_parts);

        try {
            $pdo->beginTransaction();

            if (!empty($room_no)) {
                $stmt = $pdo->prepare("SELECT room_id FROM rooms WHERE room_number = ?");
                $stmt->execute([$room_no]);
                if (!$stmt->fetch()) {
                    $stmt = $pdo->prepare("INSERT INTO rooms (room_number, room_type, capacity) VALUES (?, 'Shared', 2)");
                    $stmt->execute([$room_no]);
                }
            } else {
                $room_no = NULL;
            }

            if ($action === 'update' && !empty($resident_id)) {
                $stmt = $pdo->prepare("UPDATE residents SET 
                    full_name = ?, 
                    gender = ?, 
                    date_of_birth = ?, 
                    age = ?, 
                    blood_group = ?, 
                    emergency_contact_name = ?, 
                    emergency_contact_phone = ?, 
                    address = ?, 
                    room_number = ?, 
                    health_status = ?, 
                    admission_date = ? 
                    WHERE resident_id = ?");
                $stmt->execute([
                    $full_name,
                    $gender,
                    $dob,
                    $age,
                    $blood_group,
                    $guardian_name,
                    $guardian_phone,
                    $guardian_address,
                    $room_no,
                    $health_status,
                    $admission_date,
                    $resident_id
                ]);
                $msg = 'Resident profile updated successfully!';
            } else {
                $stmt = $pdo->query("SELECT MAX(resident_id) AS max_id FROM residents");
                $row = $stmt->fetch();
                $nextId = ($row['max_id'] ?? 0) + 1;
                $resident_code = 'RES-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

                $stmt = $pdo->prepare("INSERT INTO residents (
                    resident_code, full_name, gender, date_of_birth, age, blood_group, 
                    emergency_contact_name, emergency_contact_phone, address, room_number, 
                    health_status, admission_date, status
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Active')");
                $stmt->execute([
                    $resident_code,
                    $full_name,
                    $gender,
                    $dob,
                    $age,
                    $blood_group,
                    $guardian_name,
                    $guardian_phone,
                    $guardian_address,
                    $room_no,
                    $health_status,
                    $admission_date
                ]);
                $msg = 'Resident registered successfully!';
            }

            $pdo->commit();
            
            if ($is_ajax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => $msg]);
                exit;
            }
            $formSuccess = $msg;
        } catch (Exception $e) {
            $pdo->rollBack();
            if ($is_ajax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Error processing request: ' . $e->getMessage()]);
                exit;
            }
            $formError = 'Error processing request: ' . $e->getMessage();
        }
    }
}

// Fetch helper mapping logic
function fetch_residents_list($pdo, $search = '', $status_filter = 'All') {
    $query_str = "SELECT * FROM residents WHERE 1=1";
    $params = [];
    
    if ($status_filter !== 'All') {
        $query_str .= " AND status = ?";
        $params[] = $status_filter;
    }
    
    if (!empty($search)) {
        $query_str .= " AND (resident_code LIKE ? OR full_name LIKE ? OR emergency_contact_name LIKE ? OR emergency_contact_phone LIKE ? OR room_number LIKE ?)";
        $search_term = "%$search%";
        $params[] = $search_term;
        $params[] = $search_term;
        $params[] = $search_term;
        $params[] = $search_term;
        $params[] = $search_term;
    }
    
    $query_str .= " ORDER BY resident_id DESC";
    $stmt = $pdo->prepare($query_str);
    $stmt->execute($params);
    $db_residents = $stmt->fetchAll();
    
    $residents = [];
    foreach ($db_residents as $db_r) {
        $hs = $db_r['health_status'] ?? '';
        $health_grade = get_health_grade($hs);
        
        $notes_content = '';
        $relation_content = '';
        $email_content = '';
        
        $lines = explode("\n", $hs);
        foreach ($lines as $line) {
            if (strpos($line, 'Medical Notes: ') === 0) {
                $notes_content = substr($line, strlen('Medical Notes: '));
            } elseif (strpos($line, 'Relation to Guardian: ') === 0) {
                $relation_content = substr($line, strlen('Relation to Guardian: '));
            } elseif (strpos($line, 'Guardian Email: ') === 0) {
                $email_content = substr($line, strlen('Guardian Email: '));
            }
        }
        
        $residents[] = [
            'id' => $db_r['resident_id'],
            'resident_code' => $db_r['resident_code'],
            'name' => $db_r['full_name'],
            'dob' => $db_r['date_of_birth'],
            'gender' => $db_r['gender'],
            'blood_group' => $db_r['blood_group'],
            'age' => $db_r['age'],
            'room' => $db_r['room_number'] ?? '—',
            'admission' => $db_r['admission_date'],
            'guardian' => $db_r['emergency_contact_name'],
            'phone' => $db_r['emergency_contact_phone'],
            'address' => $db_r['address'],
            'relation' => $relation_content,
            'guardian_email' => $email_content,
            'notes' => $notes_content,
            'health' => $health_grade,
            'status' => $db_r['status']
        ];
    }
    return $residents;
}

$healthBadge = ['Stable' => 'success', 'Needs Care' => 'warning', 'Critical' => 'danger'];
$statusBadge = ['Active' => 'success', 'Discharged' => 'secondary', 'Inactive' => 'warning'];

// Handle AJAX Fetch Request
if (isset($_GET['action']) && $_GET['action'] === 'fetch') {
    $search = trim($_GET['search'] ?? '');
    $status_filter = trim($_GET['status'] ?? 'All');
    
    $residents = fetch_residents_list($pdo, $search, $status_filter);
    
    ob_start();
    if (empty($residents)) {
        echo '<tr><td colspan="7" class="text-center py-4 text-muted">No residents found</td></tr>';
    } else {
        foreach ($residents as $r) {
            $hBadge = $healthBadge[$r['health']] ?? 'secondary';
            $sBadge = $statusBadge[$r['status']] ?? 'secondary';
            $initials = !empty($r['name']) ? strtoupper($r['name'][0]) : 'R';
            ?>
            <tr data-status="<?php echo sn_e($r['status']); ?>">
                <td class="ps-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center fw-bold text-primary" style="width: 40px; height: 40px; font-size: 0.95rem;">
                            <?php echo sn_e($initials); ?>
                        </div>
                        <div>
                            <div class="fw-semibold text-dark"><?php echo sn_e($r['name']); ?></div>
                            <small class="text-muted"><?php echo sn_e($r['resident_code']); ?> · Age <?php echo (int)$r['age']; ?></small>
                        </div>
                    </div>
                </td>
                <td><span class="font-monospace text-dark"><?php echo sn_e($r['room']); ?></span></td>
                <td><span class="text-dark"><?php echo sn_e($r['admission']); ?></span></td>
                <td>
                    <div class="fw-semibold text-dark"><?php echo sn_e($r['guardian']); ?></div>
                    <small class="text-muted"><?php echo sn_e($r['phone']); ?></small>
                </td>
                <td>
                    <span class="badge bg-<?php echo $hBadge; ?>-subtle text-<?php echo $hBadge; ?> rounded-pill px-2.5 py-1">
                        <?php echo sn_e($r['health']); ?>
                    </span>
                </td>
                <td>
                    <span class="badge bg-<?php echo $sBadge; ?>-subtle text-<?php echo $sBadge; ?> rounded-pill px-2.5 py-1">
                        <?php echo sn_e($r['status']); ?>
                    </span>
                </td>
                <td class="pe-3 text-end">
                    <button class="btn btn-sm btn-light text-primary me-1 btn-edit-resident" 
                            title="Edit" 
                            data-resident="<?php echo sn_e(json_encode($r)); ?>">
                        <i class="bi bi-pencil-fill"></i>
                    </button>
                    <button class="btn btn-sm btn-light text-danger btn-delete-resident" 
                            title="Delete" 
                            data-id="<?php echo (int)$r['id']; ?>"
                            data-name="<?php echo sn_e($r['name']); ?>">
                        <i class="bi bi-trash3-fill"></i>
                    </button>
                </td>
            </tr>
            <?php
        }
    }
    $html = ob_get_clean();
    
    header('Content-Type: application/json');
    echo json_encode([
        'html' => $html,
        'count' => count($residents)
    ]);
    exit;
}

// Initial fetch for page load
$residents = fetch_residents_list($pdo);

$healthBadge = ['Stable' => 'success', 'Needs Care' => 'warning', 'Critical' => 'danger'];
$statusBadge = ['Active' => 'success', 'Discharged' => 'secondary', 'Inactive' => 'warning'];

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'admin';
$currentPage   = 'resident_registration.php';
$sn_asset_root = "../../assets";
$base_path = '../../'; // Ensure correct path prefix
include '../../includes/sidebar.php';
?>

<main id="sn-main-content" role="main" aria-label="Resident Registration Content" class="p-4 flex-grow-1">
    <div class="container-fluid">

        <?php if ($formSuccess): ?>
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> <?php echo sn_e($formSuccess); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if ($formError): ?>
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo sn_e($formError); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Page Header -->
        <div class="mb-4">
            <h3 class="fw-bold mb-0 text-dark">Resident Registration</h3>
            <small class="text-muted">Register a new resident and manage profiles</small>
        </div>

        <!-- Registration Form Card -->
        <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
            <div class="card-header bg-white border-bottom border-light p-3">
                <h5 class="card-title fw-bold mb-0 text-dark">New Resident Intake</h5>
                <small class="text-muted">Fields marked with <span class="text-danger">*</span> are required</small>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="resident_registration.php">
                    <input type="hidden" name="action" id="form_action" value="register">
                    <input type="hidden" name="resident_id" id="edit_resident_id" value="">
                    
                    <!-- Section: Personal Info -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-primary mb-3 border-bottom pb-2" style="font-size: 0.95rem; letter-spacing: 0.05em; text-transform: uppercase;">1. Personal Information</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="full_name" class="form-label fw-semibold text-dark small">Full Name <span class="text-danger">*</span></label>
                                <input type="text" id="full_name" name="full_name" class="form-control" placeholder="e.g. Kamala Devi" required>
                            </div>
                            <div class="col-md-4">
                                <label for="dob" class="form-label fw-semibold text-dark small">Date of Birth <span class="text-danger">*</span></label>
                                <input type="date" id="dob" name="dob" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label for="gender" class="form-label fw-semibold text-dark small">Gender <span class="text-danger">*</span></label>
                                <select id="gender" name="gender" class="form-select" required>
                                    <option value="">Select</option>
                                    <option>Female</option>
                                    <option>Male</option>
                                    <option>Other</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="blood_group" class="form-label fw-semibold text-dark small">Blood Group</label>
                                <select id="blood_group" name="blood_group" class="form-select">
                                    <option value="">Select</option>
                                    <option>A+</option><option>A-</option>
                                    <option>B+</option><option>B-</option>
                                    <option>O+</option><option>O-</option>
                                    <option>AB+</option><option>AB-</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="room_no" class="form-label fw-semibold text-dark small">Room Number</label>
                                <input type="text" id="room_no" name="room_no" class="form-control" placeholder="e.g. A-102">
                            </div>
                            <div class="col-md-4">
                                <label for="admission_date" class="form-label fw-semibold text-dark small">Admission Date <span class="text-danger">*</span></label>
                                <input type="date" id="admission_date" name="admission_date" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <!-- Section: Guardian details -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-primary mb-3 border-bottom pb-2" style="font-size: 0.95rem; letter-spacing: 0.05em; text-transform: uppercase;">2. Guardian / Emergency Contact</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="guardian_name" class="form-label fw-semibold text-dark small">Guardian Name <span class="text-danger">*</span></label>
                                <input type="text" id="guardian_name" name="guardian_name" class="form-control" placeholder="e.g. Ravi Devi" required>
                            </div>
                            <div class="col-md-4">
                                <label for="relation" class="form-label fw-semibold text-dark small">Relationship</label>
                                <input type="text" id="relation" name="relation" class="form-control" placeholder="e.g. Son">
                            </div>
                            <div class="col-md-4">
                                <label for="guardian_phone" class="form-label fw-semibold text-dark small">Phone Number <span class="text-danger">*</span></label>
                                <input type="tel" id="guardian_phone" name="guardian_phone" class="form-control" placeholder="98450 11234" required>
                            </div>
                            <div class="col-md-4">
                                <label for="guardian_email" class="form-label fw-semibold text-dark small">Email Address</label>
                                <input type="email" id="guardian_email" name="guardian_email" class="form-control" placeholder="name@example.com">
                            </div>
                            <div class="col-md-8">
                                <label for="address" class="form-label fw-semibold text-dark small">Guardian Address</label>
                                <input type="text" id="address" name="address" class="form-control" placeholder="Street, city, state, PIN">
                            </div>
                        </div>
                    </div>

                    <!-- Section: Medical details -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-primary mb-3 border-bottom pb-2" style="font-size: 0.95rem; letter-spacing: 0.05em; text-transform: uppercase;">3. Medical Notes</h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="notes" class="form-label fw-semibold text-dark small">Health Conditions / Medication</label>
                                <textarea id="notes" name="notes" class="form-control" rows="3" placeholder="Diabetes, hypertension, current medication, allergies..."></textarea>
                                <div class="form-text small text-muted">This information is visible to medical and caregiving staff only.</div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-3">
                        <button type="reset" class="btn btn-sm btn-secondary fw-semibold">Clear Form</button>
                        <button type="submit" class="btn btn-sm btn-primary fw-semibold">Register Resident</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table Card of Registered Residents -->
        <div class="card border-0 shadow-sm rounded-3 bg-white">
            <div class="card-header bg-white border-bottom border-light p-3">
                <div class="row align-items-center g-3">
                    <div class="col-md-4">
                        <h5 class="card-title fw-bold mb-0 text-dark">Registered Residents</h5>
                        <small class="text-muted"><?php echo count($residents); ?> residents on record</small>
                    </div>
                    <div class="col-md-4 ms-auto">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-light text-muted"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control bg-light border-light" placeholder="Search residents..." data-table-search>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select bg-light border-light" data-table-filter>
                            <option value="All">All statuses</option>
                            <option value="Active">Active</option>
                            <option value="Discharged">Discharged</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Table body -->
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="residentsTable" style="font-size: 0.9rem;">
                        <thead class="table-light text-muted" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em;">
                            <tr>
                                <th class="ps-3">Resident</th>
                                <th>Room</th>
                                <th>Admitted</th>
                                <th>Guardian</th>
                                <th>Health</th>
                                <th>Status</th>
                                <th class="pe-3 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($residents as $r): ?>
                                <?php 
                                    $hBadge = $healthBadge[$r['health']] ?? 'secondary';
                                    $sBadge = $statusBadge[$r['status']] ?? 'secondary';
                                    $initials = !empty($r['name']) ? strtoupper($r['name'][0]) : 'R';
                                ?>
                                <tr data-status="<?php echo sn_e($r['status']); ?>">
                                    <td class="ps-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center fw-bold text-primary" style="width: 40px; height: 40px; font-size: 0.95rem;">
                                                <?php echo sn_e($initials); ?>
                                            </div>
                                            <div>
                                                <div class="fw-semibold text-dark"><?php echo sn_e($r['name']); ?></div>
                                                <small class="text-muted"><?php echo sn_e($r['resident_code']); ?> · Age <?php echo (int)$r['age']; ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="font-monospace text-dark"><?php echo sn_e($r['room']); ?></span></td>
                                    <td><span class="text-dark"><?php echo sn_e($r['admission']); ?></span></td>
                                    <td>
                                        <div class="fw-semibold text-dark"><?php echo sn_e($r['guardian']); ?></div>
                                        <small class="text-muted"><?php echo sn_e($r['phone']); ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $hBadge; ?>-subtle text-<?php echo $hBadge; ?> rounded-pill px-2.5 py-1">
                                            <?php echo sn_e($r['health']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $sBadge; ?>-subtle text-<?php echo $sBadge; ?> rounded-pill px-2.5 py-1">
                                            <?php echo sn_e($r['status']); ?>
                                        </span>
                                    </td>
                                    <td class="pe-3 text-end">
                                        <button class="btn btn-sm btn-light text-primary me-1 btn-edit-resident" 
                                                title="Edit" 
                                                data-resident="<?php echo sn_e(json_encode($r)); ?>">
                                            <i class="bi bi-pencil-fill"></i>
                                        </button>
                                        <button class="btn btn-sm btn-light text-danger btn-delete-resident" 
                                                title="Delete" 
                                                data-id="<?php echo (int)$r['id']; ?>"
                                                data-name="<?php echo sn_e($r['name']); ?>">
                                            <i class="bi bi-trash3-fill"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Empty State -->
                <div class="sn-empty text-center py-5" style="display: none;">
                    <i class="bi bi-person-x text-muted display-4"></i>
                    <p class="mt-2 fw-semibold text-dark">No residents found</p>
                    <p class="text-muted small">Try a different name or adjust your filter options</p>
                </div>
            </div>
        </div>

    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.querySelector('[data-table-search]');
    const statusFilter = document.querySelector('[data-table-filter]');
    const tbody = document.querySelector('#residentsTable tbody');
    const emptyRow = document.querySelector('.sn-empty');

    const formAction = document.getElementById('form_action');
    const residentIdInput = document.getElementById('edit_resident_id');
    const formTitle = document.querySelector('.card-header h5');
    const submitBtn = document.querySelector('button[type="submit"]');
    
    // Form fields
    const fullNameInput = document.getElementById('full_name');
    const dobInput = document.getElementById('dob');
    const genderSelect = document.getElementById('gender');
    const bloodGroupSelect = document.getElementById('blood_group');
    const roomNoInput = document.getElementById('room_no');
    const admissionDateInput = document.getElementById('admission_date');
    const guardianNameInput = document.getElementById('guardian_name');
    const relationInput = document.getElementById('relation');
    const guardianPhoneInput = document.getElementById('guardian_phone');
    const guardianEmailInput = document.getElementById('guardian_email');
    const addressInput = document.getElementById('address');
    const notesInput = document.getElementById('notes');

    function showAlert(type, message) {
        const existingAlerts = document.querySelectorAll('.container-fluid > .alert');
        existingAlerts.forEach(a => a.remove());

        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4`;
        alertDiv.setAttribute('role', 'alert');
        
        const icon = type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill';
        alertDiv.innerHTML = `
            <i class="bi ${icon} me-2"></i> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        const container = document.querySelector('.container-fluid');
        container.insertBefore(alertDiv, container.firstChild);
        alertDiv.scrollIntoView({ behavior: 'smooth' });
    }

    function loadTable() {
        const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const filterVal = statusFilter ? statusFilter.value : 'All';
        
        fetch(`resident_registration.php?action=fetch&search=${encodeURIComponent(query)}&status=${encodeURIComponent(filterVal)}`)
        .then(res => res.json())
        .then(data => {
            tbody.innerHTML = data.html;
            
            // Toggle empty state if no rows found
            if (emptyRow) {
                emptyRow.style.display = (data.count === 0) ? 'block' : 'none';
            }

            // Update count display
            const countDisplay = document.querySelector('.card-header small.text-muted');
            if (countDisplay) {
                countDisplay.textContent = `${data.count} residents on record`;
            }
        })
        .catch(err => {
            console.error('Error loading table:', err);
        });
    }

    // Debounce search input
    if (searchInput) {
        let timeout = null;
        searchInput.addEventListener('input', () => {
            clearTimeout(timeout);
            timeout = setTimeout(loadTable, 300);
        });
    }

    if (statusFilter) {
        statusFilter.addEventListener('change', loadTable);
    }

    // Delegate Edit Action
    tbody.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-edit-resident');
        if (btn) {
            const data = JSON.parse(btn.getAttribute('data-resident'));
            
            fullNameInput.value = data.name;
            dobInput.value = data.dob;
            genderSelect.value = data.gender;
            bloodGroupSelect.value = data.blood_group || '';
            roomNoInput.value = data.room === '—' ? '' : data.room;
            admissionDateInput.value = data.admission;
            guardianNameInput.value = data.guardian;
            relationInput.value = data.relation;
            guardianPhoneInput.value = data.phone;
            guardianEmailInput.value = data.guardian_email;
            addressInput.value = data.address || '';
            notesInput.value = data.notes;

            formAction.value = 'update';
            residentIdInput.value = data.id;
            formTitle.textContent = 'Edit Resident Profile: ' + data.name;
            submitBtn.textContent = 'Update Resident';
            
            document.querySelector('.card').scrollIntoView({ behavior: 'smooth' });
        }
    });

    // Delegate Delete Action
    tbody.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-delete-resident');
        if (btn) {
            const residentId = btn.getAttribute('data-id');
            const name = btn.getAttribute('data-name');
            if (confirm('Are you sure you want to delete resident "' + name + '"?')) {
                fetch(`resident_registration.php?action=delete&id=${residentId}&ajax=1`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showAlert('success', data.message);
                        loadTable();
                    } else {
                        showAlert('danger', data.message);
                    }
                })
                .catch(err => {
                    showAlert('danger', 'An error occurred while deleting the resident.');
                });
            }
        }
    });

    // Reset Form Revert Mode
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('reset', () => {
            formAction.value = 'register';
            residentIdInput.value = '';
            formTitle.textContent = 'New Resident Intake';
            submitBtn.textContent = 'Register Resident';
        });

        // Submit form via AJAX
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(form);
            formData.append('ajax', '1');
            
            fetch('resident_registration.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    form.reset();
                    // Revert mode
                    formAction.value = 'register';
                    residentIdInput.value = '';
                    formTitle.textContent = 'New Resident Intake';
                    submitBtn.textContent = 'Register Resident';
                    loadTable();
                } else {
                    showAlert('danger', data.message);
                }
            })
            .catch(err => {
                showAlert('danger', 'An error occurred while saving the resident.');
            });
        });
    }
});
</script>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
