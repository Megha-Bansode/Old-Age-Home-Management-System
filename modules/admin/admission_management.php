<?php
/**
 * SevaNest — Admission Management
 * File     : modules/admin/admission_management.php
 * Version  : 1.0
 */

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/functions.php';

// Require Admin login
require_login();
require_role(['Admin', 'Old Age Home Admin']);

$base_path = '../../';
$page_title = 'Admission Management | SevaNest';

// Database Connection
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

// Handle POST actions (New Request, Approve, Reject)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'add';
    $is_ajax = (isset($_POST['ajax']) && $_POST['ajax'] == '1');

    if ($action === 'add') {
        $applicant_name = trim($_POST['applicant_name'] ?? '');
        $guardian_name = trim($_POST['guardian_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        if (empty($applicant_name) || empty($guardian_name) || empty($phone)) {
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

                // 1. Create resident record with status Inactive
                $stmt = $pdo->query("SELECT MAX(resident_id) AS max_id FROM residents");
                $row = $stmt->fetch();
                $nextId = ($row['max_id'] ?? 0) + 1;
                $resident_code = 'RES-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

                $stmt_res = $pdo->prepare("INSERT INTO residents (
                    resident_code, full_name, emergency_contact_name, emergency_contact_phone, status
                ) VALUES (?, ?, ?, ?, 'Inactive')");
                $stmt_res->execute([$resident_code, $applicant_name, $guardian_name, $phone]);
                $resident_id = $pdo->lastInsertId();

                // 2. Create admission record
                $admitted_by = $_SESSION['user_id'] ?? 1; // Default to admin user id
                $stmt_adm = $pdo->prepare("INSERT INTO admissions (
                    resident_id, admission_date, admitted_by, emergency_contact_name, emergency_contact_phone, status
                ) VALUES (?, CURDATE(), ?, ?, ?, 'Pending')");
                $stmt_adm->execute([$resident_id, $admitted_by, $guardian_name, $phone]);

                $pdo->commit();
                $msg = 'Admission request submitted successfully!';
                if ($is_ajax) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true, 'message' => $msg]);
                    exit;
                }
                $formSuccess = $msg;
            } catch (Exception $e) {
                $pdo->rollBack();
                $msg = 'Error creating admission request: ' . $e->getMessage();
                if ($is_ajax) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => $msg]);
                    exit;
                }
                $formError = $msg;
            }
        }
    } elseif ($action === 'approve') {
        $admission_id = (int)($_POST['id'] ?? 0);
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("SELECT resident_id FROM admissions WHERE admission_id = ?");
            $stmt->execute([$admission_id]);
            $res = $stmt->fetch();
            if (!$res) {
                throw new Exception('Admission record not found.');
            }
            $resident_id = $res['resident_id'];

            $stmt_update_adm = $pdo->prepare("UPDATE admissions SET status = 'Approved' WHERE admission_id = ?");
            $stmt_update_adm->execute([$admission_id]);

            $stmt_update_res = $pdo->prepare("UPDATE residents SET status = 'Active' WHERE resident_id = ?");
            $stmt_update_res->execute([$resident_id]);

            $pdo->commit();
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Admission request approved successfully!']);
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            exit;
        }
    } elseif ($action === 'reject') {
        $admission_id = (int)($_POST['id'] ?? 0);
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("SELECT resident_id FROM admissions WHERE admission_id = ?");
            $stmt->execute([$admission_id]);
            $res = $stmt->fetch();
            if (!$res) {
                throw new Exception('Admission record not found.');
            }
            $resident_id = $res['resident_id'];

            $stmt_update_adm = $pdo->prepare("UPDATE admissions SET status = 'Rejected' WHERE admission_id = ?");
            $stmt_update_adm->execute([$admission_id]);

            $stmt_update_res = $pdo->prepare("UPDATE residents SET status = 'Inactive' WHERE resident_id = ?");
            $stmt_update_res->execute([$resident_id]);

            $pdo->commit();
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Admission request rejected successfully!']);
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            exit;
        }
    }
}

// Fetch helper mapping logic
function fetch_admissions_list($pdo, $search = '', $status_filter = 'All') {
    $query_str = "SELECT a.*, r.full_name AS resident_name, r.age AS resident_age 
                  FROM admissions a 
                  INNER JOIN residents r ON a.resident_id = r.resident_id 
                  WHERE 1=1";
    $params = [];

    if ($status_filter !== 'All') {
        $query_str .= " AND a.status = ?";
        $params[] = $status_filter;
    }

    if (!empty($search)) {
        $query_str .= " AND (r.full_name LIKE ? OR a.emergency_contact_name LIKE ? OR a.admission_id LIKE ?)";
        $search_term = "%$search%";
        $params[] = $search_term;
        $params[] = $search_term;
        $params[] = $search_term;
    }

    $query_str .= " ORDER BY a.admission_id DESC";
    $stmt = $pdo->prepare($query_str);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();

    $admissions = [];
    foreach ($rows as $row) {
        $admissions[] = [
            'id' => 'ADM-' . str_pad($row['admission_id'], 4, '0', STR_PAD_LEFT),
            'admission_id' => $row['admission_id'],
            'name' => $row['resident_name'],
            'age' => $row['resident_age'] ?? 0,
            'requested' => $row['admission_date'],
            'guardian' => $row['emergency_contact_name'] ?? '—',
            'phone' => $row['emergency_contact_phone'] ?? '—',
            'status' => $row['status']
        ];
    }
    return $admissions;
}

$statusBadge = [
    'Pending'      => 'warning',
    'Under Review' => 'secondary',
    'Approved'     => 'success',
    'Rejected'     => 'danger'
];

// Handle AJAX Fetch Request
if (isset($_GET['action']) && $_GET['action'] === 'fetch') {
    $search = trim($_GET['search'] ?? '');
    $status_filter = trim($_GET['status'] ?? 'All');

    $admissions = fetch_admissions_list($pdo, $search, $status_filter);
    
    // Get updated counts
    $counts = ['All' => 0, 'Pending' => 0, 'Under Review' => 0, 'Approved' => 0, 'Rejected' => 0];
    $stmt_counts = $pdo->query("SELECT status, COUNT(*) as cnt FROM admissions GROUP BY status");
    while ($row = $stmt_counts->fetch()) {
        $counts[$row['status']] = $row['cnt'];
        $counts['All'] += $row['cnt'];
    }

    ob_start();
    if (empty($admissions)) {
        echo '<tr><td colspan="7" class="text-center py-4 text-muted">No admission requests found</td></tr>';
    } else {
        foreach ($admissions as $a) {
            $badgeCls = $statusBadge[$a['status']] ?? 'secondary';
            ?>
            <tr data-status="<?php echo sn_e($a['status']); ?>">
                <td class="ps-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center fw-bold text-primary" style="width: 40px; height: 40px; font-size: 0.95rem;">
                            <?php echo sn_e(!empty($a['name']) ? strtoupper($a['name'][0]) : 'R'); ?>
                        </div>
                        <div>
                            <div class="fw-semibold text-dark"><?php echo sn_e($a['name']); ?></div>
                            <small class="text-muted">Age <?php echo (int)$a['age']; ?></small>
                        </div>
                    </div>
                </td>
                <td><span class="font-monospace text-muted"><?php echo sn_e($a['id']); ?></span></td>
                <td><span class="text-dark"><?php echo sn_e($a['requested']); ?></span></td>
                <td><span class="text-dark"><?php echo sn_e($a['guardian']); ?></span></td>
                <td><span class="text-dark"><?php echo sn_e($a['phone']); ?></span></td>
                <td>
                    <span class="badge bg-<?php echo $badgeCls; ?>-subtle text-<?php echo $badgeCls; ?> rounded-pill px-2.5 py-1">
                        <?php echo sn_e($a['status']); ?>
                    </span>
                </td>
                <td class="pe-3 text-end">
                    <?php if ($a['status'] === 'Pending'): ?>
                        <button class="btn btn-sm btn-light text-success me-1 btn-approve-admission" 
                                title="Approve" 
                                data-id="<?php echo (int)$a['admission_id']; ?>"
                                data-name="<?php echo sn_e($a['name']); ?>">
                            <i class="bi bi-check-circle-fill"></i>
                        </button>
                        <button class="btn btn-sm btn-light text-danger btn-reject-admission" 
                                title="Reject" 
                                data-id="<?php echo (int)$a['admission_id']; ?>"
                                data-name="<?php echo sn_e($a['name']); ?>">
                            <i class="bi bi-x-circle-fill"></i>
                        </button>
                    <?php else: ?>
                        <button class="btn btn-sm btn-light text-muted" disabled>
                            <i class="bi bi-slash-circle"></i>
                        </button>
                    <?php endif; ?>
                </td>
            </tr>
            <?php
        }
    }
    $html = ob_get_clean();

    header('Content-Type: application/json');
    echo json_encode([
        'html' => $html,
        'counts' => $counts
    ]);
    exit;
}

// Initial fetch for page load
$admissions = fetch_admissions_list($pdo);

$counts = ['All' => 0, 'Pending' => 0, 'Under Review' => 0, 'Approved' => 0, 'Rejected' => 0];
$stmt_counts = $pdo->query("SELECT status, COUNT(*) as cnt FROM admissions GROUP BY status");
while ($row = $stmt_counts->fetch()) {
    $counts[$row['status']] = $row['cnt'];
    $counts['All'] += $row['cnt'];
}

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'admin';
$currentPage   = 'admission_management.php';
$sn_asset_root = "../../assets";
$base_path = '../../'; // Ensure correct path prefix
include '../../includes/sidebar.php';
?>

<main id="sn-main-content" role="main" aria-label="Admission Management Content" class="p-4 flex-grow-1">
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
                <h3 class="fw-bold mb-0 text-dark">Admission Management</h3>
                <small class="text-muted">Review, approve, and track incoming admission requests</small>
            </div>
            <button class="btn btn-primary fw-semibold" data-bs-toggle="modal" data-bs-target="#newAdmissionModal">
                <i class="bi bi-plus-circle me-1"></i> New Request
            </button>
        </div>

        <!-- Main Card -->
        <div class="card border-0 shadow-sm rounded-3 bg-white">
            <div class="card-header bg-white border-bottom border-light p-3">
                <div class="row align-items-center g-3">
                    <!-- Search Input -->
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-light text-muted"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control bg-light border-light" placeholder="Search by name or guardian..." data-table-search>
                        </div>
                    </div>
                    <!-- Tabs buttons -->
                    <div class="col-md-8 text-md-end">
                        <div class="btn-group sn-tabs" role="group" aria-label="Admission Filter">
                            <button type="button" class="btn btn-primary btn-sm is-active" data-tab-target="All">All (<?php echo $counts['All']; ?>)</button>
                            <button type="button" class="btn btn-outline-primary btn-sm" data-tab-target="Pending">Pending (<?php echo $counts['Pending'] ?? 0; ?>)</button>
                            <button type="button" class="btn btn-outline-primary btn-sm" data-tab-target="Under Review">Under Review (<?php echo $counts['Under Review'] ?? 0; ?>)</button>
                            <button type="button" class="btn btn-outline-primary btn-sm" data-tab-target="Approved">Approved (<?php echo $counts['Approved'] ?? 0; ?>)</button>
                            <button type="button" class="btn btn-outline-primary btn-sm" data-tab-target="Rejected">Rejected (<?php echo $counts['Rejected'] ?? 0; ?>)</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Block -->
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="admissionsTable" style="font-size: 0.9rem;">
                        <thead class="table-light text-muted" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em;">
                            <tr>
                                <th class="ps-3">Applicant</th>
                                <th>Request ID</th>
                                <th>Requested On</th>
                                <th>Guardian</th>
                                <th>Contact</th>
                                <th>Status</th>
                                <th class="pe-3 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($admissions as $a): ?>
                                <?php $badgeCls = $statusBadge[$a['status']] ?? 'secondary'; ?>
                                <tr data-status="<?php echo sn_e($a['status']); ?>">
                                    <td class="ps-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center fw-bold text-primary" style="width: 40px; height: 40px; font-size: 0.95rem;">
                                                <?php echo sn_e(strtoupper($a['name'][0])); ?>
                                            </div>
                                            <div>
                                                <div class="fw-semibold text-dark"><?php echo sn_e($a['name']); ?></div>
                                                <small class="text-muted">Age <?php echo (int)$a['age']; ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="font-monospace text-muted"><?php echo sn_e($a['id']); ?></span></td>
                                    <td><span class="text-dark"><?php echo sn_e($a['requested']); ?></span></td>
                                    <td><span class="text-dark"><?php echo sn_e($a['guardian']); ?></span></td>
                                    <td><span class="text-dark"><?php echo sn_e($a['phone']); ?></span></td>
                                    <td>
                                        <span class="badge bg-<?php echo $badgeCls; ?>-subtle text-<?php echo $badgeCls; ?> rounded-pill px-2.5 py-1">
                                            <?php echo sn_e($a['status']); ?>
                                        </span>
                                    </td>
                                    <td class="pe-3 text-end">
                                        <button class="btn btn-sm btn-light text-success me-1" title="Approve">
                                            <i class="bi bi-check-circle-fill"></i>
                                        </button>
                                        <button class="btn btn-sm btn-light text-danger" title="Reject">
                                            <i class="bi bi-x-circle-fill"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Empty State -->
                <div class="sn-empty text-center py-5" style="display: none;">
                    <i class="bi bi-clipboard-x text-muted display-4"></i>
                    <p class="mt-2 fw-semibold text-dark">No admission requests found</p>
                    <p class="text-muted small">Try a different name or adjust your filter tabs</p>
                </div>
            </div>
        </div>

    </div>
</main>

<!-- New Admission Request Modal -->
<div class="modal fade" id="newAdmissionModal" tabindex="-1" aria-labelledby="newAdmissionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header border-light">
                <h5 class="modal-title fw-bold text-dark" id="newAdmissionModalLabel">New Admission Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="admission_management.php" class="admission-form">
                <input type="hidden" name="action" value="add">
                <div class="modal-body">
                    <p class="text-muted small mb-4">Capture the essentials now — full intake happens on the Resident Registration page once approved.</p>
                    
                    <div class="mb-3">
                        <label for="am_name" class="form-label text-dark fw-semibold small">Applicant Name <span class="text-danger">*</span></label>
                        <input type="text" id="am_name" name="applicant_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="am_guardian" class="form-label text-dark fw-semibold small">Guardian Name <span class="text-danger">*</span></label>
                        <input type="text" id="am_guardian" name="guardian_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="am_phone" class="form-label text-dark fw-semibold small">Contact Number <span class="text-danger">*</span></label>
                        <input type="tel" id="am_phone" name="phone" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer border-light">
                    <button type="button" class="btn btn-sm btn-secondary fw-semibold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary fw-semibold">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.querySelector('[data-table-search]');
    const tabButtons = document.querySelectorAll('.sn-tabs button');
    const tbody = document.querySelector('#admissionsTable tbody');
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
        const activeTab = document.querySelector('.sn-tabs button.is-active')?.getAttribute('data-tab-target') || 'All';
        
        fetch(`admission_management.php?action=fetch&search=${encodeURIComponent(query)}&status=${encodeURIComponent(activeTab)}`)
        .then(res => res.json())
        .then(data => {
            tbody.innerHTML = data.html;
            
            // Check empty state
            const hasRows = tbody.querySelectorAll('tr').length > 0 && !tbody.querySelector('td[colspan]');
            if (emptyRow) {
                emptyRow.style.display = hasRows ? 'none' : 'block';
            }

            // Update tab counts
            tabButtons.forEach(btn => {
                const target = btn.getAttribute('data-tab-target');
                if (target === 'All') {
                    btn.textContent = `All (${data.counts.All})`;
                } else if (target === 'Pending') {
                    btn.textContent = `Pending (${data.counts.Pending})`;
                } else if (target === 'Under Review') {
                    btn.textContent = `Under Review (${data.counts['Under Review'] ?? 0})`;
                } else if (target === 'Approved') {
                    btn.textContent = `Approved (${data.counts.Approved})`;
                } else if (target === 'Rejected') {
                    btn.textContent = `Rejected (${data.counts.Rejected})`;
                }
            });
        })
        .catch(err => {
            console.error('Error loading admissions:', err);
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

    if (tabButtons) {
        tabButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                tabButtons.forEach(b => b.classList.remove('is-active', 'btn-primary'));
                tabButtons.forEach(b => b.classList.add('btn-outline-primary'));
                btn.classList.add('is-active', 'btn-primary');
                btn.classList.remove('btn-outline-primary');
                loadTable();
            });
        });
    }

    // Delegate Approve click
    tbody.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-approve-admission');
        if (btn) {
            const id = btn.getAttribute('data-id');
            const name = btn.getAttribute('data-name');
            if (confirm(`Are you sure you want to approve the admission of "${name}"?`)) {
                const formData = new FormData();
                formData.append('action', 'approve');
                formData.append('id', id);
                formData.append('ajax', '1');

                fetch('admission_management.php', {
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
                    showAlert('danger', 'An error occurred while approving the request.');
                });
            }
        }
    });

    // Delegate Reject click
    tbody.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-reject-admission');
        if (btn) {
            const id = btn.getAttribute('data-id');
            const name = btn.getAttribute('data-name');
            if (confirm(`Are you sure you want to reject the admission of "${name}"?`)) {
                const formData = new FormData();
                formData.append('action', 'reject');
                formData.append('id', id);
                formData.append('ajax', '1');

                fetch('admission_management.php', {
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
                    showAlert('danger', 'An error occurred while rejecting the request.');
                });
            }
        }
    });

    // Intercept form submit inside modals
    const form = document.querySelector('form.admission-form');
    if (form) {
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(form);
            formData.append('ajax', '1');

            fetch('admission_management.php', {
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
