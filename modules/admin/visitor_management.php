<?php
/**
 * SevaNest — Visitor Management
 * File     : modules/admin/visitor_management.php
 * Version  : 1.0
 */

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/functions.php';

// Require Admin login
require_login();
require_role(['Admin', 'Old Age Home Admin']);

$base_path = '../../';
$page_title = 'Visitor Management | SevaNest';

// Database Connection
require_once __DIR__ . '/../../config/database.php';
$pdo = get_db_connection();

$formSuccess = '';
$formError = '';

// Summary serializer/parser helpers
if (!function_exists('serialize_visitor_purpose')) {
    function serialize_visitor_purpose($name, $relation, $checkin, $checkout, $status) {
        return "Name: " . str_replace('|', '', $name) . " | Relation: " . str_replace('|', '', $relation) . " | CheckIn: " . $checkin . " | CheckOut: " . $checkout . " | Status: " . $status;
    }
}

if (!function_exists('parse_visitor_purpose')) {
    function parse_visitor_purpose($purpose) {
        $data = [
            'name' => 'Unknown',
            'relation' => 'Visitor',
            'checkin' => '—',
            'checkout' => '—',
            'status' => 'Checked In'
        ];
        if (empty($purpose)) {
            return $data;
        }
        $parts = explode('|', $purpose);
        foreach ($parts as $part) {
            $subparts = explode(':', $part, 2);
            if (count($subparts) === 2) {
                $key = strtolower(trim($subparts[0]));
                $val = trim($subparts[1]);
                if ($key === 'name') {
                    $data['name'] = $val;
                } elseif ($key === 'relation') {
                    $data['relation'] = $val;
                } elseif ($key === 'checkin') {
                    $data['checkin'] = $val;
                } elseif ($key === 'checkout') {
                    $data['checkout'] = $val;
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

// Handle POST actions (New Check-in, Check-out)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'add';
    $is_ajax = (isset($_POST['ajax']) && $_POST['ajax'] == '1');

    if ($action === 'add') {
        $name = trim($_POST['name'] ?? '');
        $resident_id = (int)($_POST['resident_id'] ?? 0);
        $relation = trim($_POST['relation'] ?? '');
        $date = $_POST['date'] ?? '';
        $checkin_time = $_POST['checkin_time'] ?? '';

        if (empty($name) || empty($resident_id) || empty($relation) || empty($date) || empty($checkin_time)) {
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

                // 1. Verify resident exists
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM residents WHERE resident_id = ?");
                $stmt->execute([$resident_id]);
                if ($stmt->fetchColumn() == 0) {
                    throw new Exception('Resident not found.');
                }

                // 2. Prevent duplicate active visits (Checked In) for same visitor & resident
                $stmt_chk = $pdo->prepare("SELECT COUNT(*) FROM visit_requests WHERE resident_id = ? AND purpose LIKE ?");
                $stmt_chk->execute([$resident_id, "%Name: $name%Status: Checked In%"]);
                if ($stmt_chk->fetchColumn() > 0) {
                    throw new Exception('This visitor is already checked in for the resident.');
                }

                // 3. Insert record
                $family_member_id = $_SESSION['user_id'] ?? 1;
                $visit_datetime = $date . ' ' . $checkin_time . ':00';
                $purpose = serialize_visitor_purpose($name, $relation, $checkin_time, '—', 'Checked In');

                $stmt_ins = $pdo->prepare("INSERT INTO visit_requests (
                    family_member_id, resident_id, visit_date, purpose, status
                ) VALUES (?, ?, ?, ?, 'Approved')");
                $stmt_ins->execute([$family_member_id, $resident_id, $visit_datetime, $purpose]);

                $pdo->commit();
                $msg = 'Visitor check-in logged successfully!';
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
    } elseif ($action === 'checkout') {
        $request_id = (int)($_POST['id'] ?? 0);
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("SELECT * FROM visit_requests WHERE request_id = ?");
            $stmt->execute([$request_id]);
            $vr = $stmt->fetch();
            if (!$vr) {
                throw new Exception('Visit record not found.');
            }

            $parsed = parse_visitor_purpose($vr['purpose']);
            if ($parsed['status'] === 'Checked Out') {
                throw new Exception('Visitor is already checked out.');
            }

            $checkout_time = date('H:i');
            $new_purpose = serialize_visitor_purpose($parsed['name'], $parsed['relation'], $parsed['checkin'], $checkout_time, 'Checked Out');

            $stmt_up = $pdo->prepare("UPDATE visit_requests SET purpose = ? WHERE request_id = ?");
            $stmt_up->execute([$new_purpose, $request_id]);

            $pdo->commit();
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Visitor checked out successfully!']);
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
function fetch_visitors_list($pdo, $search = '', $status_filter = 'All') {
    $query_str = "SELECT vr.*, r.full_name AS resident_name 
                  FROM visit_requests vr 
                  INNER JOIN residents r ON vr.resident_id = r.resident_id 
                  WHERE 1=1";
    $params = [];

    if (!empty($search)) {
        $query_str .= " AND (r.full_name LIKE ? OR vr.purpose LIKE ?)";
        $search_term = "%$search%";
        $params[] = $search_term;
        $params[] = $search_term;
    }

    $query_str .= " ORDER BY vr.request_id DESC";
    $stmt = $pdo->prepare($query_str);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();

    $visitors = [];
    foreach ($rows as $row) {
        $parsed = parse_visitor_purpose($row['purpose']);
        
        if ($status_filter !== 'All' && $parsed['status'] !== $status_filter) {
            continue;
        }

        $visitors[] = [
            'id' => $row['request_id'],
            'name' => $parsed['name'],
            'visiting' => $row['resident_name'],
            'relation' => $parsed['relation'],
            'date' => date('Y-m-d', strtotime($row['visit_date'])),
            'checkin' => $parsed['checkin'],
            'checkout' => $parsed['checkout'],
            'status' => $parsed['status']
        ];
    }
    return $visitors;
}

$statusBadge = [
    'Checked In'  => 'success',
    'Checked Out' => 'secondary',
    'Scheduled'   => 'warning'
];

// Handle AJAX Fetch Request
if (isset($_GET['action']) && $_GET['action'] === 'fetch') {
    $search = trim($_GET['search'] ?? '');
    $status_filter = trim($_GET['status'] ?? 'All');

    $visitors = fetch_visitors_list($pdo, $search, $status_filter);

    ob_start();
    if (empty($visitors)) {
        echo '<tr><td colspan="8" class="text-center py-4 text-muted">No visitors logged</td></tr>';
    } else {
        foreach ($visitors as $v) {
            $badgeCls = $statusBadge[$v['status']] ?? 'secondary';
            ?>
            <tr data-status="<?php echo sn_e($v['status']); ?>">
                <td class="ps-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center fw-bold text-primary" style="width: 40px; height: 40px; font-size: 0.95rem;">
                            <?php echo sn_e(!empty($v['name']) ? strtoupper($v['name'][0]) : 'V'); ?>
                        </div>
                        <div class="fw-semibold text-dark"><?php echo sn_e($v['name']); ?></div>
                    </div>
                </td>
                <td><span class="text-dark fw-semibold"><?php echo sn_e($v['visiting']); ?></span></td>
                <td><span class="text-dark"><?php echo sn_e($v['relation']); ?></span></td>
                <td><span class="text-dark"><?php echo sn_e($v['date']); ?></span></td>
                <td><span class="text-dark"><?php echo sn_e($v['checkin']); ?></span></td>
                <td><span class="text-dark"><?php echo sn_e($v['checkout']); ?></span></td>
                <td>
                    <span class="badge bg-<?php echo $badgeCls; ?>-subtle text-<?php echo $badgeCls; ?> rounded-pill px-2.5 py-1">
                        <?php echo sn_e($v['status']); ?>
                    </span>
                </td>
                <td class="pe-3 text-end">
                    <?php if ($v['status'] === 'Checked In'): ?>
                        <button class="btn btn-sm btn-outline-danger py-1 px-2.5 small fw-semibold btn-checkout-visitor" 
                                title="Check Out"
                                data-id="<?php echo (int)$v['id']; ?>"
                                data-name="<?php echo sn_e($v['name']); ?>">
                            Check Out
                        </button>
                    <?php else: ?>
                        <span class="text-muted">—</span>
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
        'count' => count($visitors)
    ]);
    exit;
}

// Initial fetch for page load
$visitors = fetch_visitors_list($pdo);
$residents = fetch_active_residents($pdo);

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'admin';
$currentPage   = 'visitor_management.php';
$sn_asset_root = "../../assets";
$base_path = '../../'; // Ensure correct path prefix
include '../../includes/sidebar.php';
?>

<main id="sn-main-content" role="main" aria-label="Visitor Management Content" class="p-4 flex-grow-1">
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
                <h3 class="fw-bold mb-0 text-dark">Visitor Management</h3>
                <small class="text-muted">Log visitor check-ins, check-outs and schedules</small>
            </div>
            <button class="btn btn-primary fw-semibold" data-bs-toggle="modal" data-bs-target="#newVisitorModal">
                <i class="bi bi-person-bounding-box me-1"></i> Log Visitor Check-in
            </button>
        </div>

        <!-- Main Card -->
        <div class="card border-0 shadow-sm rounded-3 bg-white">
            <div class="card-header bg-white border-bottom border-light p-3">
                <div class="row align-items-center g-3">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-light text-muted"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control bg-light border-light" placeholder="Search visitors..." data-table-search>
                        </div>
                    </div>
                    <div class="col-md-3 ms-auto">
                        <select class="form-select bg-light border-light" data-table-filter>
                            <option value="All">All statuses</option>
                            <option value="Checked In">Checked In</option>
                            <option value="Checked Out">Checked Out</option>
                            <option value="Scheduled">Scheduled</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Table Block -->
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="visitorTable" style="font-size: 0.9rem;">
                        <thead class="table-light text-muted" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em;">
                            <tr>
                                <th class="ps-3">Visitor Name</th>
                                <th>Visiting Resident</th>
                                <th>Relationship</th>
                                <th>Date</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Status</th>
                                <th class="pe-3 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($visitors as $v): ?>
                                <?php $badgeCls = $statusBadge[$v['status']] ?? 'secondary'; ?>
                                <tr data-status="<?php echo sn_e($v['status']); ?>">
                                    <td class="ps-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center fw-bold text-primary" style="width: 40px; height: 40px; font-size: 0.95rem;">
                                                <?php echo sn_e(strtoupper($v['name'][0])); ?>
                                            </div>
                                            <div class="fw-semibold text-dark"><?php echo sn_e($v['name']); ?></div>
                                        </div>
                                    </td>
                                    <td><span class="text-dark fw-semibold"><?php echo sn_e($v['visiting']); ?></span></td>
                                    <td><span class="text-dark"><?php echo sn_e($v['relation']); ?></span></td>
                                    <td><span class="text-dark"><?php echo sn_e($v['date']); ?></span></td>
                                    <td><span class="text-dark"><?php echo sn_e($v['checkin']); ?></span></td>
                                    <td><span class="text-dark"><?php echo sn_e($v['checkout']); ?></span></td>
                                    <td>
                                        <span class="badge bg-<?php echo $badgeCls; ?>-subtle text-<?php echo $badgeCls; ?> rounded-pill px-2.5 py-1">
                                            <?php echo sn_e($v['status']); ?>
                                        </span>
                                    </td>
                                    <td class="pe-3 text-end">
                                        <?php if ($v['status'] === 'Checked In'): ?>
                                            <button class="btn btn-sm btn-outline-danger py-1 px-2.5 small fw-semibold" title="Check Out">
                                                Check Out
                                            </button>
                                        <?php else: ?>
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Empty State -->
                <div class="sn-empty text-center py-5" style="display: none;">
                    <i class="bi bi-person-x text-muted display-4"></i>
                    <p class="mt-2 fw-semibold text-dark">No visitors logged</p>
                    <p class="text-muted small">Try a different name or filter option</p>
                </div>
            </div>
        </div>

    </div>
</main>

<!-- New Visitor Modal -->
<div class="modal fade" id="newVisitorModal" tabindex="-1" aria-labelledby="newVisitorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header border-light">
                <h5 class="modal-title fw-bold text-dark" id="newVisitorModalLabel">Log Visitor Check-in</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="visitor_management.php" class="visitor-form">
                <input type="hidden" name="action" value="add">
                <div class="modal-body">
                    <p class="text-muted small mb-4">Log visitor details, relations, and select their visiting resident wing.</p>
                    
                    <div class="mb-3">
                        <label for="v_name" class="form-label text-dark fw-semibold small">Visitor Name <span class="text-danger">*</span></label>
                        <input type="text" id="v_name" name="name" class="form-control" placeholder="e.g. Ravi Devi" required>
                    </div>
                    <div class="mb-3">
                        <label for="v_resident" class="form-label text-dark fw-semibold small">Select Resident to Visit <span class="text-danger">*</span></label>
                        <select id="v_resident" name="resident_id" class="form-select" required>
                            <option value="">Select Resident</option>
                            <?php foreach ($residents as $r): ?>
                                <?php if ($r['status'] === 'Active'): ?>
                                    <option value="<?php echo sn_e($r['id']); ?>"><?php echo sn_e($r['name']); ?> — <?php echo sn_e($r['room']); ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="v_relation" class="form-label text-dark fw-semibold small">Relationship <span class="text-danger">*</span></label>
                        <input type="text" id="v_relation" name="relation" class="form-control" placeholder="e.g. Son" required>
                    </div>
                    <div class="mb-3">
                        <label for="v_date" class="form-label text-dark fw-semibold small">Date <span class="text-danger">*</span></label>
                        <input type="date" id="v_date" name="date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="v_time" class="form-label text-dark fw-semibold small">Check-in Time <span class="text-danger">*</span></label>
                        <input type="time" id="v_time" name="checkin_time" class="form-control" value="<?php echo date('H:i'); ?>" required>
                    </div>
                </div>
                <div class="modal-footer border-light">
                    <button type="button" class="btn btn-sm btn-secondary fw-semibold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary fw-semibold">Log Check-in</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.querySelector('[data-table-search]');
    const statusFilter = document.querySelector('[data-table-filter]');
    const tbody = document.querySelector('#visitorTable tbody');
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
        
        fetch(`visitor_management.php?action=fetch&search=${encodeURIComponent(query)}&status=${encodeURIComponent(filterVal)}`)
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
            console.error('Error loading visitors:', err);
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

    // Delegate Checkout click
    tbody.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-checkout-visitor');
        if (btn) {
            const id = btn.getAttribute('data-id');
            const name = btn.getAttribute('data-name');
            if (confirm(`Are you sure you want to check out visitor "${name}"?`)) {
                const formData = new FormData();
                formData.append('action', 'checkout');
                formData.append('id', id);
                formData.append('ajax', '1');

                fetch('visitor_management.php', {
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
                    showAlert('danger', 'An error occurred while checking out the visitor.');
                });
            }
        }
    });

    // Intercept form submit inside modals
    const form = document.querySelector('form.visitor-form');
    if (form) {
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(form);
            formData.append('ajax', '1');

            fetch('visitor_management.php', {
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
