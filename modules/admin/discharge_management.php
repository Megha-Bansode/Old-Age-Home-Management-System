<?php
/**
 * SevaNest — Discharge Management
 * File     : modules/admin/discharge_management.php
 * Version  : 1.0
 */

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/functions.php';

// Require Admin login
require_login();
require_role(['Admin', 'Old Age Home Admin']);

$base_path = '../../';
$page_title = 'Discharge Management | SevaNest';

// Database Connection
require_once __DIR__ . '/../../config/database.php';
$pdo = get_db_connection();

$formSuccess = '';
$formError = '';

// Summary serializer/parser helpers
if (!function_exists('serialize_discharge_summary')) {
    function serialize_discharge_summary($reason, $handed_to, $status) {
        return "Reason: " . str_replace('|', '', $reason) . " | Handed Over To: " . str_replace('|', '', $handed_to) . " | Status: " . $status;
    }
}

if (!function_exists('parse_discharge_summary')) {
    function parse_discharge_summary($summary) {
        $data = [
            'reason' => 'Request',
            'handed_to' => '—',
            'status' => 'Completed'
        ];
        if (empty($summary)) {
            return $data;
        }
        $parts = explode('|', $summary);
        foreach ($parts as $part) {
            $subparts = explode(':', $part, 2);
            if (count($subparts) === 2) {
                $key = strtolower(trim($subparts[0]));
                $val = trim($subparts[1]);
                if ($key === 'reason') {
                    $data['reason'] = $val;
                } elseif ($key === 'handed over to') {
                    $data['handed_to'] = $val;
                } elseif ($key === 'status') {
                    $data['status'] = $val;
                }
            }
        }
        return $data;
    }
}

// Fetch active residents dropdown helper
function fetch_active_residents($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM residents WHERE status = 'Active' ORDER BY full_name ASC");
        $db_residents = $stmt->fetchAll();
    } catch (Exception $e) {
        return [];
    }

    $residents = [];
    foreach ($db_residents as $db_r) {
        $residents[] = [
            'id' => $db_r['resident_id'],
            'name' => $db_r['full_name'],
            'room' => $db_r['room_number'] ?? '—',
            'status' => $db_r['status']
        ];
    }
    return $residents;
}

// Handle POST actions (New Request, Approve, Cancel)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'add';
    $is_ajax = (isset($_POST['ajax']) && $_POST['ajax'] == '1');

    if ($action === 'add') {
        $resident_id = (int)($_POST['resident_id'] ?? 0);
        $discharge_date = $_POST['discharge_date'] ?? '';
        $reason = trim($_POST['reason'] ?? '');
        $handed_to = trim($_POST['handed_to'] ?? '');

        if (empty($resident_id) || empty($discharge_date) || empty($reason) || empty($handed_to)) {
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

                // 1. Verify resident has active status
                $stmt = $pdo->prepare("SELECT status, room_number FROM residents WHERE resident_id = ?");
                $stmt->execute([$resident_id]);
                $res = $stmt->fetch();
                if (!$res || $res['status'] !== 'Active') {
                    throw new Exception('Resident does not have an active admission.');
                }

                // 2. Prevent duplicate active (Scheduled) discharge requests
                $stmt_chk = $pdo->prepare("SELECT COUNT(*) FROM discharges WHERE resident_id = ? AND summary LIKE '%Status: Scheduled%'");
                $stmt_chk->execute([$resident_id]);
                if ($stmt_chk->fetchColumn() > 0) {
                    throw new Exception('An active discharge request already exists for this resident.');
                }

                // 3. Insert discharge request as Scheduled
                $discharged_by = $_SESSION['user_id'] ?? 1;
                $summary = serialize_discharge_summary($reason, $handed_to, 'Scheduled');

                $stmt_ins = $pdo->prepare("INSERT INTO discharges (
                    resident_id, discharge_date, discharged_by, reason, summary
                ) VALUES (?, ?, ?, 'Request', ?)");
                $stmt_ins->execute([$resident_id, $discharge_date, $discharged_by, $summary]);

                $pdo->commit();
                $msg = 'Discharge process initiated successfully!';
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
    } elseif ($action === 'approve') {
        $discharge_id = (int)($_POST['id'] ?? 0);
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("SELECT * FROM discharges WHERE discharge_id = ?");
            $stmt->execute([$discharge_id]);
            $d = $stmt->fetch();
            if (!$d) {
                throw new Exception('Discharge record not found.');
            }

            $parsed = parse_discharge_summary($d['summary']);
            if ($parsed['status'] === 'Completed') {
                throw new Exception('Discharge request is already completed.');
            }

            $new_summary = serialize_discharge_summary($parsed['reason'], $parsed['handed_to'], 'Completed');
            $stmt_up_d = $pdo->prepare("UPDATE discharges SET summary = ? WHERE discharge_id = ?");
            $stmt_up_d->execute([$new_summary, $discharge_id]);

            $resident_id = $d['resident_id'];
            $stmt_room = $pdo->prepare("SELECT room_number FROM residents WHERE resident_id = ?");
            $stmt_room->execute([$resident_id]);
            $room_no = $stmt_room->fetchColumn();

            $stmt_up_res = $pdo->prepare("UPDATE residents SET room_number = NULL, status = 'Discharged' WHERE resident_id = ?");
            $stmt_up_res->execute([$resident_id]);

            if (!empty($room_no)) {
                $pdo->prepare("UPDATE rooms SET occupancy = (SELECT COUNT(*) FROM residents WHERE room_number = ? AND status = 'Active') WHERE room_number = ?")->execute([$room_no, $room_no]);
            }

            $pdo->commit();
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Discharge request approved and finalized!']);
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            exit;
        }
    } elseif ($action === 'cancel') {
        $discharge_id = (int)($_POST['id'] ?? 0);
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("SELECT * FROM discharges WHERE discharge_id = ?");
            $stmt->execute([$discharge_id]);
            $d = $stmt->fetch();
            if (!$d) {
                throw new Exception('Discharge record not found.');
            }

            $parsed = parse_discharge_summary($d['summary']);
            if ($parsed['status'] === 'Completed') {
                throw new Exception('Completed discharge requests cannot be cancelled.');
            }

            $new_summary = serialize_discharge_summary($parsed['reason'], $parsed['handed_to'], 'Cancelled');
            $stmt_up_d = $pdo->prepare("UPDATE discharges SET summary = ? WHERE discharge_id = ?");
            $stmt_up_d->execute([$new_summary, $discharge_id]);

            $pdo->commit();
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Discharge request cancelled successfully.']);
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
function fetch_discharges_list($pdo, $search = '', $status_filter = 'All') {
    $query_str = "SELECT d.*, r.full_name AS resident_name, r.room_number 
                  FROM discharges d 
                  INNER JOIN residents r ON d.resident_id = r.resident_id 
                  WHERE 1=1";
    $params = [];

    if (!empty($search)) {
        $query_str .= " AND r.full_name LIKE ?";
        $params[] = "%$search%";
    }

    $query_str .= " ORDER BY d.discharge_id DESC";
    $stmt = $pdo->prepare($query_str);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();

    $discharges = [];
    foreach ($rows as $row) {
        $parsed = parse_discharge_summary($row['summary']);
        
        if ($status_filter !== 'All' && $parsed['status'] !== $status_filter) {
            continue;
        }

        $discharges[] = [
            'id' => $row['discharge_id'],
            'name' => $row['resident_name'],
            'room' => $row['room_number'] ?? '—',
            'date' => $row['discharge_date'],
            'reason' => $parsed['reason'],
            'handedTo' => $parsed['handed_to'],
            'status' => $parsed['status']
        ];
    }
    return $discharges;
}

$statusBadge = [
    'Scheduled' => 'warning',
    'Completed' => 'success',
    'Cancelled' => 'danger'
];

// Handle AJAX Fetch Request
if (isset($_GET['action']) && $_GET['action'] === 'fetch') {
    $search = trim($_GET['search'] ?? '');
    $status_filter = trim($_GET['status'] ?? 'All');

    $discharges = fetch_discharges_list($pdo, $search, $status_filter);

    ob_start();
    if (empty($discharges)) {
        echo '<tr><td colspan="7" class="text-center py-4 text-muted">No discharge records found</td></tr>';
    } else {
        foreach ($discharges as $d) {
            $badgeCls = $statusBadge[$d['status']] ?? 'secondary';
            ?>
            <tr data-status="<?php echo sn_e($d['status']); ?>">
                <td class="ps-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center fw-bold text-primary" style="width: 40px; height: 40px; font-size: 0.95rem;">
                            <?php echo sn_e(!empty($d['name']) ? strtoupper($d['name'][0]) : 'R'); ?>
                        </div>
                        <div class="fw-semibold text-dark"><?php echo sn_e($d['name']); ?></div>
                    </div>
                </td>
                <td><span class="font-monospace text-dark"><?php echo sn_e($d['room']); ?></span></td>
                <td><span class="text-dark"><?php echo sn_e($d['date']); ?></span></td>
                <td><span class="text-dark"><?php echo sn_e($d['reason']); ?></span></td>
                <td><span class="text-dark"><?php echo sn_e($d['handedTo']); ?></span></td>
                <td>
                    <span class="badge bg-<?php echo $badgeCls; ?>-subtle text-<?php echo $badgeCls; ?> rounded-pill px-2.5 py-1">
                        <?php echo sn_e($d['status']); ?>
                    </span>
                </td>
                <td class="pe-3 text-end">
                    <?php if ($d['status'] === 'Scheduled'): ?>
                        <button class="btn btn-sm btn-light text-success me-1 btn-approve-discharge" 
                                title="Approve Discharge" 
                                data-id="<?php echo (int)$d['id']; ?>"
                                data-name="<?php echo sn_e($d['name']); ?>">
                            <i class="bi bi-check-circle-fill"></i>
                        </button>
                        <button class="btn btn-sm btn-light text-danger btn-cancel-discharge" 
                                title="Cancel Discharge" 
                                data-id="<?php echo (int)$d['id']; ?>"
                                data-name="<?php echo sn_e($d['name']); ?>">
                            <i class="bi bi-x-circle-fill"></i>
                        </button>
                    <?php else: ?>
                        <button class="btn btn-sm btn-light text-primary" title="Discharge Summary">
                            <i class="bi bi-file-earmark-pdf-fill"></i>
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
        'count' => count($discharges)
    ]);
    exit;
}

// Initial fetch for page load
$discharges = fetch_discharges_list($pdo);
$residents = fetch_active_residents($pdo);

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'admin';
$currentPage   = 'discharge_management.php';
$sn_asset_root = "../../assets";
$base_path = '../../'; // Ensure correct path prefix
include '../../includes/sidebar.php';
?>

<main id="sn-main-content" role="main" aria-label="Discharge Management Content" class="p-4 flex-grow-1">
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
                <h3 class="fw-bold mb-0 text-dark">Discharge Management</h3>
                <small class="text-muted">Process and review resident discharges</small>
            </div>
            <button class="btn btn-primary fw-semibold" data-bs-toggle="modal" data-bs-target="#newDischargeModal">
                <i class="bi bi-box-arrow-right me-1"></i> Initiate Discharge
            </button>
        </div>

        <!-- Main Card -->
        <div class="card border-0 shadow-sm rounded-3 bg-white">
            <div class="card-header bg-white border-bottom border-light p-3">
                <div class="row align-items-center g-3">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-light text-muted"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control bg-light border-light" placeholder="Search by resident name..." data-table-search>
                        </div>
                    </div>
                    <div class="col-md-3 ms-auto">
                        <select class="form-select bg-light border-light" data-table-filter>
                            <option value="All">All statuses</option>
                            <option value="Scheduled">Scheduled</option>
                            <option value="Completed">Completed</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Table Block -->
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="dischargeTable" style="font-size: 0.9rem;">
                        <thead class="table-light text-muted" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em;">
                            <tr>
                                <th class="ps-3">Resident</th>
                                <th>Room</th>
                                <th>Discharge Date</th>
                                <th>Reason</th>
                                <th>Handed Over To</th>
                                <th>Status</th>
                                <th class="pe-3 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($discharges as $d): ?>
                                <?php $badgeCls = $statusBadge[$d['status']] ?? 'secondary'; ?>
                                <tr data-status="<?php echo sn_e($d['status']); ?>">
                                    <td class="ps-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center fw-bold text-primary" style="width: 40px; height: 40px; font-size: 0.95rem;">
                                                <?php echo sn_e(strtoupper($d['name'][0])); ?>
                                            </div>
                                            <div class="fw-semibold text-dark"><?php echo sn_e($d['name']); ?></div>
                                        </div>
                                    </td>
                                    <td><span class="font-monospace text-dark"><?php echo sn_e($d['room']); ?></span></td>
                                    <td><span class="text-dark"><?php echo sn_e($d['date']); ?></span></td>
                                    <td><span class="text-dark"><?php echo sn_e($d['reason']); ?></span></td>
                                    <td><span class="text-dark"><?php echo sn_e($d['handedTo']); ?></span></td>
                                    <td>
                                        <span class="badge bg-<?php echo $badgeCls; ?>-subtle text-<?php echo $badgeCls; ?> rounded-pill px-2.5 py-1">
                                            <?php echo sn_e($d['status']); ?>
                                        </span>
                                    </td>
                                    <td class="pe-3 text-end">
                                        <button class="btn btn-sm btn-light text-primary" title="Discharge Summary">
                                            <i class="bi bi-file-earmark-pdf-fill"></i>
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
                    <p class="mt-2 fw-semibold text-dark">No discharge records found</p>
                    <p class="text-muted small">Try a different name or filter option</p>
                </div>
            </div>
        </div>

    </div>
</main>

<!-- New Discharge Modal -->
<div class="modal fade" id="newDischargeModal" tabindex="-1" aria-labelledby="newDischargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header border-light">
                <h5 class="modal-title fw-bold text-dark" id="newDischargeModalLabel">Initiate Discharge</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="discharge_management.php" class="discharge-form">
                <input type="hidden" name="action" value="add">
                <div class="modal-body">
                    <p class="text-muted small mb-4">This starts the discharge workflow for a current resident.</p>
                    
                    <div class="mb-3">
                        <label for="discharge_resident" class="form-label text-dark fw-semibold small">Select Resident <span class="text-danger">*</span></label>
                        <select id="discharge_resident" name="resident_id" class="form-select" required>
                            <option value="">Select Resident</option>
                            <?php foreach ($residents as $r): ?>
                                <?php if ($r['status'] === 'Active'): ?>
                                    <option value="<?php echo sn_e($r['id']); ?>"><?php echo sn_e($r['name']); ?> — <?php echo sn_e($r['room']); ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="discharge_date" class="form-label text-dark fw-semibold small">Discharge Date <span class="text-danger">*</span></label>
                        <input type="date" id="discharge_date" name="discharge_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="discharge_reason" class="form-label text-dark fw-semibold small">Reason for Discharge <span class="text-danger">*</span></label>
                        <input type="text" id="discharge_reason" name="reason" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="handed_to" class="form-label text-dark fw-semibold small">Handed Over To <span class="text-danger">*</span></label>
                        <input type="text" id="handed_to" name="handed_to" class="form-control" placeholder="e.g. Ravi Devi (Son)" required>
                    </div>
                </div>
                <div class="modal-footer border-light">
                    <button type="button" class="btn btn-sm btn-secondary fw-semibold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary fw-semibold">Initiate</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.querySelector('[data-table-search]');
    const statusFilter = document.querySelector('[data-table-filter]');
    const tbody = document.querySelector('#dischargeTable tbody');
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
        
        fetch(`discharge_management.php?action=fetch&search=${encodeURIComponent(query)}&status=${encodeURIComponent(filterVal)}`)
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
            console.error('Error loading discharges:', err);
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

    // Delegate Approve click
    tbody.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-approve-discharge');
        if (btn) {
            const id = btn.getAttribute('data-id');
            const name = btn.getAttribute('data-name');
            if (confirm(`Are you sure you want to approve the discharge of "${name}"?`)) {
                const formData = new FormData();
                formData.append('action', 'approve');
                formData.append('id', id);
                formData.append('ajax', '1');

                fetch('discharge_management.php', {
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

    // Delegate Cancel click
    tbody.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-cancel-discharge');
        if (btn) {
            const id = btn.getAttribute('data-id');
            const name = btn.getAttribute('data-name');
            if (confirm(`Are you sure you want to cancel the discharge request for "${name}"?`)) {
                const formData = new FormData();
                formData.append('action', 'cancel');
                formData.append('id', id);
                formData.append('ajax', '1');

                fetch('discharge_management.php', {
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
                    showAlert('danger', 'An error occurred while cancelling the request.');
                });
            }
        }
    });

    // Intercept form submit inside modals
    const form = document.querySelector('form.discharge-form');
    if (form) {
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(form);
            formData.append('ajax', '1');

            fetch('discharge_management.php', {
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
