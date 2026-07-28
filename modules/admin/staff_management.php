<?php
/**
 * SevaNest — Staff Management
 * File     : modules/admin/staff_management.php
 * Version  : 1.0
 */

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/functions.php';

// Require Admin login
require_login();
require_role(['Admin', 'Old Age Home Admin']);

$base_path = '../../';
$page_title = 'Staff Management | SevaNest';

// Database Connection
require_once __DIR__ . '/../../config/database.php';
$pdo = get_db_connection();

$formSuccess = '';
$formError = '';

// Handle POST actions (Add Staff, Delete Staff)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'add';
    $is_ajax = (isset($_POST['ajax']) && $_POST['ajax'] == '1');

    if ($action === 'add') {
        $name = trim($_POST['name'] ?? '');
        $role = trim($_POST['role'] ?? '');
        $dept = trim($_POST['dept'] ?? '');
        $shift = trim($_POST['shift'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        if (empty($name) || empty($role) || empty($dept) || empty($shift) || empty($phone)) {
            $msg = 'Please fill in all required fields.';
            if ($is_ajax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $msg]);
                exit;
            }
            $formError = $msg;
        } else {
            try {
                $pdo->beginTransaction();

                // 1. Prevent duplicate phone numbers
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM staff WHERE phone = ?");
                $stmt->execute([$phone]);
                if ($stmt->fetchColumn() > 0) {
                    throw new Exception('Phone number already registered.');
                }

                // 2. Generate staff code
                $stmt = $pdo->query("SELECT MAX(staff_id) AS max_id FROM staff");
                $row = $stmt->fetch();
                $nextId = ($row['max_id'] ?? 0) + 1;
                $staff_code = 'STF-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

                // 3. Insert staff record
                $stmt_ins = $pdo->prepare("INSERT INTO staff (
                    staff_code, full_name, designation, department, shift, phone, joining_date, status
                ) VALUES (?, ?, ?, ?, ?, ?, CURDATE(), 'Active')");
                $stmt_ins->execute([$staff_code, $name, $role, $dept, $shift, $phone]);

                $pdo->commit();
                $msg = 'New staff member registered successfully!';
                if ($is_ajax) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true, 'message' => $msg]);
                    exit;
                }
                $formSuccess = $msg;
            } catch (Exception $e) {
                $pdo->rollBack();
                $msg = $e->getMessage();
                if ($is_ajax) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => $msg]);
                    exit;
                }
                $formError = $msg;
            }
        }
    } elseif ($action === 'delete') {
        $staff_id = (int)($_POST['id'] ?? 0);
        try {
            $stmt = $pdo->prepare("DELETE FROM staff WHERE staff_id = ?");
            $stmt->execute([$staff_id]);
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Staff member removed successfully!']);
            exit;
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            exit;
        }
    }
}

// Fetch helper mapping logic
function fetch_staff_list($pdo, $search = '', $status_filter = 'All') {
    $query_str = "SELECT * FROM staff WHERE 1=1";
    $params = [];

    if ($status_filter !== 'All') {
        if ($status_filter === 'On Duty') {
            $query_str .= " AND status = 'Active'";
        } elseif ($status_filter === 'Off Duty') {
            $query_str .= " AND status = 'Resigned'";
        } elseif ($status_filter === 'On Leave') {
            $query_str .= " AND status = 'On Leave'";
        }
    }

    if (!empty($search)) {
        $query_str .= " AND (full_name LIKE ? OR staff_code LIKE ? OR email LIKE ? OR phone LIKE ? OR department LIKE ?)";
        $search_term = "%$search%";
        $params[] = $search_term;
        $params[] = $search_term;
        $params[] = $search_term;
        $params[] = $search_term;
        $params[] = $search_term;
    }

    $query_str .= " ORDER BY staff_id ASC";
    $stmt = $pdo->prepare($query_str);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();

    $staff_list = [];
    foreach ($rows as $row) {
        $status = 'Off Duty';
        if ($row['status'] === 'Active') {
            $status = 'On Duty';
        } elseif ($row['status'] === 'On Leave') {
            $status = 'On Leave';
        }

        $staff_list[] = [
            'id' => $row['staff_code'],
            'staff_id' => $row['staff_id'],
            'name' => $row['full_name'],
            'role' => $row['designation'] ?? 'Caregiver',
            'dept' => $row['department'] ?? 'Care',
            'shift' => $row['shift'] ?? 'Morning',
            'phone' => $row['phone'] ?? '—',
            'status' => $status
        ];
    }
    return $staff_list;
}

$statusBadge = [
    'On Duty'  => 'success',
    'Off Duty' => 'secondary',
    'On Leave' => 'warning'
];

// Handle AJAX Fetch Request
if (isset($_GET['action']) && $_GET['action'] === 'fetch') {
    $search = trim($_GET['search'] ?? '');
    $status_filter = trim($_GET['status'] ?? 'All');

    $staff = fetch_staff_list($pdo, $search, $status_filter);

    ob_start();
    if (empty($staff)) {
        echo '<tr><td colspan="7" class="text-center py-4 text-muted">No staff members found</td></tr>';
    } else {
        foreach ($staff as $s) {
            $badgeCls = $statusBadge[$s['status']] ?? 'secondary';
            ?>
            <tr data-status="<?php echo sn_e($s['status']); ?>">
                <td class="ps-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center fw-bold text-primary" style="width: 40px; height: 40px; font-size: 0.95rem;">
                            <?php echo sn_e(!empty($s['name']) ? strtoupper($s['name'][0]) : 'S'); ?>
                        </div>
                        <div>
                            <div class="fw-semibold text-dark"><?php echo sn_e($s['name']); ?></div>
                            <small class="text-muted"><?php echo sn_e($s['id']); ?></small>
                        </div>
                    </div>
                </td>
                <td><span class="text-dark fw-semibold"><?php echo sn_e($s['role']); ?></span></td>
                <td><span class="text-dark"><?php echo sn_e($s['dept']); ?></span></td>
                <td><span class="text-dark"><?php echo sn_e($s['shift']); ?></span></td>
                <td><span class="text-dark"><?php echo sn_e($s['phone']); ?></span></td>
                <td>
                    <span class="badge bg-<?php echo $badgeCls; ?>-subtle text-<?php echo $badgeCls; ?> rounded-pill px-2.5 py-1">
                        <?php echo sn_e($s['status']); ?>
                    </span>
                </td>
                <td class="pe-3 text-end">
                    <button class="btn btn-sm btn-light text-primary me-1" title="Edit Roster" disabled>
                        <i class="bi bi-pencil-fill"></i>
                    </button>
                    <button class="btn btn-sm btn-light text-danger btn-delete-staff" 
                            title="Remove"
                            data-id="<?php echo (int)$s['staff_id']; ?>"
                            data-name="<?php echo sn_e($s['name']); ?>">
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
        'count' => count($staff)
    ]);
    exit;
}

// Initial fetch for page load
$staff = fetch_staff_list($pdo);

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'admin';
$currentPage   = 'staff_management.php';
$sn_asset_root = "../../assets";
$base_path = '../../'; // Ensure correct path prefix
include '../../includes/sidebar.php';
?>

<main id="sn-main-content" role="main" aria-label="Staff Management Content" class="p-4 flex-grow-1">
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
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h3 class="fw-bold mb-0 text-dark">Staff Management</h3>
                <small class="text-muted">Manage caregivers, medical staff, and support roles</small>
            </div>
            <button class="btn btn-primary fw-semibold" data-bs-toggle="modal" data-bs-target="#newStaffModal">
                <i class="bi bi-person-plus me-1"></i> Add Staff Member
            </button>
        </div>

        <!-- Main Card -->
        <div class="card border-0 shadow-sm rounded-3 bg-white">
            <div class="card-header bg-white border-bottom border-light p-3">
                <div class="row align-items-center g-3">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-light text-muted"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control bg-light border-light" placeholder="Search staff by name..." data-table-search>
                        </div>
                    </div>
                    <div class="col-md-3 ms-auto">
                        <select class="form-select bg-light border-light" data-table-filter>
                            <option value="All">All statuses</option>
                            <option value="On Duty">On Duty</option>
                            <option value="Off Duty">Off Duty</option>
                            <option value="On Leave">On Leave</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Table Block -->
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="staffTable" style="font-size: 0.9rem;">
                        <thead class="table-light text-muted" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em;">
                            <tr>
                                <th class="ps-3">Name</th>
                                <th>Role</th>
                                <th>Department</th>
                                <th>Shift</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th class="pe-3 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($staff as $s): ?>
                                <?php $badgeCls = $statusBadge[$s['status']] ?? 'secondary'; ?>
                                <tr data-status="<?php echo sn_e($s['status']); ?>">
                                    <td class="ps-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center fw-bold text-primary" style="width: 40px; height: 40px; font-size: 0.95rem;">
                                                <?php echo sn_e(strtoupper($s['name'][0])); ?>
                                            </div>
                                            <div>
                                                <div class="fw-semibold text-dark"><?php echo sn_e($s['name']); ?></div>
                                                <small class="text-muted"><?php echo sn_e($s['id']); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="text-dark fw-semibold"><?php echo sn_e($s['role']); ?></span></td>
                                    <td><span class="text-dark"><?php echo sn_e($s['dept']); ?></span></td>
                                    <td><span class="text-dark"><?php echo sn_e($s['shift']); ?></span></td>
                                    <td><span class="text-dark"><?php echo sn_e($s['phone']); ?></span></td>
                                    <td>
                                        <span class="badge bg-<?php echo $badgeCls; ?>-subtle text-<?php echo $badgeCls; ?> rounded-pill px-2.5 py-1">
                                            <?php echo sn_e($s['status']); ?>
                                        </span>
                                    </td>
                                    <td class="pe-3 text-end">
                                        <button class="btn btn-sm btn-light text-primary me-1" title="Edit Roster">
                                            <i class="bi bi-pencil-fill"></i>
                                        </button>
                                        <button class="btn btn-sm btn-light text-danger" title="Remove">
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
                    <p class="mt-2 fw-semibold text-dark">No staff members found</p>
                    <p class="text-muted small">Try a different name or filter option</p>
                </div>
            </div>
        </div>

    </div>
</main>

<!-- New Staff Modal -->
<div class="modal fade" id="newStaffModal" tabindex="-1" aria-labelledby="newStaffModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header border-light">
                <h5 class="modal-title fw-bold text-dark" id="newStaffModalLabel">Add Staff Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="staff_management.php" class="staff-form">
                <input type="hidden" name="action" value="add">
                <div class="modal-body">
                    <p class="text-muted small mb-4">Add caregiver, nurse, doctor or operations staff to the database.</p>
                    
                    <div class="mb-3">
                        <label for="staff_name" class="form-label text-dark fw-semibold small">Staff Name <span class="text-danger">*</span></label>
                        <input type="text" id="staff_name" name="name" class="form-control" placeholder="e.g. Meena Patil" required>
                    </div>
                    <div class="mb-3">
                        <label for="staff_role" class="form-label text-dark fw-semibold small">Role <span class="text-danger">*</span></label>
                        <select id="staff_role" name="role" class="form-select" required>
                            <option value="">Select Role</option>
                            <option>Caregiver</option>
                            <option>Nurse</option>
                            <option>Doctor</option>
                            <option>Security</option>
                            <option>Chef</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="staff_dept" class="form-label text-dark fw-semibold small">Department <span class="text-danger">*</span></label>
                        <select id="staff_dept" name="dept" class="form-select" required>
                            <option value="">Select Department</option>
                            <option>Care</option>
                            <option>Medical</option>
                            <option>Operations</option>
                            <option>Kitchen</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="staff_shift" class="form-label text-dark fw-semibold small">Shift <span class="text-danger">*</span></label>
                        <select id="staff_shift" name="shift" class="form-select" required>
                            <option value="">Select Shift</option>
                            <option>Morning</option>
                            <option>Evening</option>
                            <option>Night</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="staff_phone" class="form-label text-dark fw-semibold small">Phone Number <span class="text-danger">*</span></label>
                        <input type="tel" id="staff_phone" name="phone" class="form-control" placeholder="e.g. 98765 43201" required>
                    </div>
                </div>
                <div class="modal-footer border-light">
                    <button type="button" class="btn btn-sm btn-secondary fw-semibold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary fw-semibold">Add Staff</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.querySelector('[data-table-search]');
    const statusFilter = document.querySelector('[data-table-filter]');
    const tbody = document.querySelector('#staffTable tbody');
    const emptyRow = document.querySelector('.sn-empty');

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
        
        fetch(`staff_management.php?action=fetch&search=${encodeURIComponent(query)}&status=${encodeURIComponent(filterVal)}`)
        .then(res => res.json())
        .then(data => {
            tbody.innerHTML = data.html;
            
            // Check empty state
            const hasRows = tbody.querySelectorAll('tr').length > 0 && !tbody.querySelector('td[colspan]');
            if (emptyRow) {
                emptyRow.style.display = hasRows ? 'none' : 'block';
            }
        })
        .catch(err => {
            console.error('Error loading staff:', err);
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

    // Delegate Delete click
    tbody.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-delete-staff');
        if (btn) {
            const id = btn.getAttribute('data-id');
            const name = btn.getAttribute('data-name');
            if (confirm(`Are you sure you want to remove staff member "${name}"?`)) {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('id', id);
                formData.append('ajax', '1');

                fetch('staff_management.php', {
                    method: 'POST',
                    body: formData
                })
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
                    showAlert('danger', 'An error occurred while removing the staff member.');
                });
            }
        }
    });

    // Intercept form submit inside modals
    const form = document.querySelector('form.staff-form');
    if (form) {
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(form);
            formData.append('ajax', '1');

            fetch('staff_management.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                // Find and close modal
                const modalEl = form.closest('.modal');
                if (modalEl) {
                    const modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                    modalInstance.hide();
                    
                    const backdrop = document.querySelector('.modal-backdrop');
                    if (backdrop) backdrop.remove();
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                    document.body.style.paddingRight = '';
                }

                if (data.success) {
                    showAlert('success', data.message);
                    form.reset();
                    loadTable();
                } else {
                    showAlert('danger', data.message);
                }
            })
            .catch(err => {
                showAlert('danger', 'An error occurred while submitting.');
            });
        });
    }
});
</script>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
