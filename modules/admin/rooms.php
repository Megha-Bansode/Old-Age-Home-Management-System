<?php
/**
 * SevaNest — Room Allocation Management
 * File     : modules/admin/rooms.php
 * Version  : 1.0
 */

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/functions.php';

// Require Admin login
require_login();
require_role(['Admin', 'Old Age Home Admin']);

$base_path = '../../';
$page_title = 'Room Allocation | SevaNest';

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

// Fetch active unassigned residents helper
function fetch_active_unassigned_residents($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM residents ORDER BY full_name ASC");
        $db_residents = $stmt->fetchAll();
    } catch (Exception $e) {
        return [];
    }

    $residents = [];
    foreach ($db_residents as $db_r) {
        $hs = $db_r['health_status'] ?? '';
        $health_grade = get_health_grade($hs);

        $residents[] = [
            'id' => $db_r['resident_id'],
            'name' => $db_r['full_name'],
            'health' => $health_grade,
            'status' => $db_r['status']
        ];
    }
    return $residents;
}

// Handle POST actions (Assign Resident, Add Room, Edit Room, Delete Room)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'assign';
    $is_ajax = (isset($_POST['ajax']) && $_POST['ajax'] == '1');

    if ($action === 'assign') {
        $resident_id = $_POST['resident_id'] ?? '';
        $room_no = $_POST['room_number'] ?? '';

        if (empty($resident_id) || empty($room_no)) {
            $msg = 'Required fields missing.';
            if ($is_ajax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $msg]);
                exit;
            }
            $formError = $msg;
        } else {
            try {
                $pdo->beginTransaction();

                // Check room capacity
                $stmt = $pdo->prepare("SELECT capacity, occupancy FROM rooms WHERE room_number = ?");
                $stmt->execute([$room_no]);
                $room_data = $stmt->fetch();

                if (!$room_data) {
                    throw new Exception('Room does not exist.');
                }

                // Count active occupants
                $stmt_count = $pdo->prepare("SELECT COUNT(*) FROM residents WHERE room_number = ? AND status = 'Active'");
                $stmt_count->execute([$room_no]);
                $current_occupants = $stmt_count->fetchColumn();

                if ($current_occupants >= $room_data['capacity']) {
                    throw new Exception('Room is already at full capacity.');
                }

                // Update resident's room_number
                $stmt_update = $pdo->prepare("UPDATE residents SET room_number = ? WHERE resident_id = ?");
                $stmt_update->execute([$room_no, $resident_id]);

                // Update room occupancy count
                $stmt_room_occ = $pdo->prepare("UPDATE rooms SET occupancy = ? WHERE room_number = ?");
                $stmt_room_occ->execute([$current_occupants + 1, $room_no]);

                $pdo->commit();
                $msg = 'Resident successfully assigned to the room!';
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
    } elseif ($action === 'add') {
        $room_no = trim($_POST['room_number'] ?? '');
        $room_type = $_POST['room_type'] ?? 'Single';
        $capacity = (int)($_POST['capacity'] ?? 1);
        $status = $_POST['status'] ?? 'Available';

        if (empty($room_no)) {
            $msg = 'Room number is required.';
            if ($is_ajax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $msg]);
                exit;
            }
            $formError = $msg;
        } else {
            try {
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM rooms WHERE room_number = ?");
                $stmt->execute([$room_no]);
                if ($stmt->fetchColumn() > 0) {
                    throw new Exception('Room number already exists.');
                }

                $stmt_add = $pdo->prepare("INSERT INTO rooms (room_number, room_type, capacity, status) VALUES (?, ?, ?, ?)");
                $stmt_add->execute([$room_no, $room_type, $capacity, $status]);

                $msg = 'Room added successfully!';
                if ($is_ajax) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true, 'message' => $msg]);
                    exit;
                }
                $formSuccess = $msg;
            } catch (Exception $e) {
                $msg = $e->getMessage();
                if ($is_ajax) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => $msg]);
                    exit;
                }
                $formError = $msg;
            }
        }
    } elseif ($action === 'edit') {
        $room_id = $_POST['room_id'] ?? '';
        $room_no = trim($_POST['room_number'] ?? '');
        $room_type = $_POST['room_type'] ?? 'Single';
        $capacity = (int)($_POST['capacity'] ?? 1);
        $status = $_POST['status'] ?? 'Available';

        if (empty($room_id) || empty($room_no)) {
            $msg = 'Required fields missing.';
            if ($is_ajax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $msg]);
                exit;
            }
            $formError = $msg;
        } else {
            try {
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM rooms WHERE room_number = ? AND room_id != ?");
                $stmt->execute([$room_no, $room_id]);
                if ($stmt->fetchColumn() > 0) {
                    throw new Exception('Room number already exists.');
                }

                $stmt_edit = $pdo->prepare("UPDATE rooms SET room_number = ?, room_type = ?, capacity = ?, status = ? WHERE room_id = ?");
                $stmt_edit->execute([$room_no, $room_type, $capacity, $status, $room_id]);

                $msg = 'Room updated successfully!';
                if ($is_ajax) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true, 'message' => $msg]);
                    exit;
                }
                $formSuccess = $msg;
            } catch (Exception $e) {
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
        $room_id = $_POST['room_id'] ?? '';

        if (empty($room_id)) {
            $msg = 'Room ID is required.';
            if ($is_ajax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $msg]);
                exit;
            }
            $formError = $msg;
        } else {
            try {
                $stmt_del = $pdo->prepare("DELETE FROM rooms WHERE room_id = ?");
                $stmt_del->execute([$room_id]);

                $msg = 'Room deleted successfully!';
                if ($is_ajax) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true, 'message' => $msg]);
                    exit;
                }
                $formSuccess = $msg;
            } catch (Exception $e) {
                $msg = $e->getMessage();
                if ($is_ajax) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => $msg]);
                    exit;
                }
                $formError = $msg;
            }
        }
    }
}

// Fetch helper mapping logic
function fetch_rooms_list($pdo, $search = '', $status_filter = 'All') {
    $pdo->query("UPDATE rooms r SET occupancy = (SELECT COUNT(*) FROM residents res WHERE res.room_number = r.room_number AND res.status = 'Active')");

    $query_str = "SELECT * FROM rooms WHERE 1=1";
    $params = [];

    if ($status_filter !== 'All') {
        if ($status_filter === 'Occupied') {
            $query_str .= " AND occupancy > 0 AND status != 'Maintenance'";
        } elseif ($status_filter === 'Vacant') {
            $query_str .= " AND occupancy = 0 AND status = 'Available'";
        } elseif ($status_filter === 'Maintenance') {
            $query_str .= " AND status = 'Maintenance'";
        }
    }

    if (!empty($search)) {
        $query_str .= " AND (room_number LIKE ? OR room_type LIKE ? OR status LIKE ?)";
        $search_term = "%$search%";
        $params[] = $search_term;
        $params[] = $search_term;
        $params[] = $search_term;
    }

    $query_str .= " ORDER BY room_number ASC";
    $stmt = $pdo->prepare($query_str);
    $stmt->execute($params);
    $db_rooms = $stmt->fetchAll();

    $rooms = [];
    foreach ($db_rooms as $r) {
        $stmt_occ = $pdo->prepare("SELECT full_name FROM residents WHERE room_number = ? AND status = 'Active'");
        $stmt_occ->execute([$r['room_number']]);
        $occupants = $stmt_occ->fetchAll(PDO::FETCH_COLUMN);

        $status = 'Vacant';
        if ($r['status'] === 'Maintenance') {
            $status = 'Maintenance';
        } elseif (count($occupants) > 0) {
            $status = 'Occupied';
        }

        $rooms[] = [
            'id' => $r['room_id'],
            'no' => $r['room_number'],
            'type' => $r['room_type'] === 'Single' ? 'Single Sharing' : ($r['room_type'] === 'Shared' ? 'Double Sharing' : $r['room_type']),
            'capacity' => $r['capacity'],
            'occupants' => $occupants,
            'status' => $status
        ];
    }
    return $rooms;
}

$statusBadge = [
    'Occupied'    => 'success',
    'Vacant'      => 'secondary',
    'Maintenance' => 'warning'
];

// Handle AJAX Fetch Request
if (isset($_GET['action']) && $_GET['action'] === 'fetch') {
    $search = trim($_GET['search'] ?? '');
    $status_filter = trim($_GET['status'] ?? 'All');

    $rooms = fetch_rooms_list($pdo, $search, $status_filter);
    $residents = fetch_active_unassigned_residents($pdo);

    ob_start();
    if (empty($rooms)) {
        echo '<div class="col-12 text-center py-5 text-muted"><i class="bi bi-house-exclamation display-4 text-muted"></i><p class="mt-2 fw-semibold text-dark">No rooms found</p></div>';
    } else {
        foreach ($rooms as $room) {
            $badgeCls = $statusBadge[$room['status']] ?? 'secondary';
            $countOccupants = count($room['occupants']);
            $isFull = ($countOccupants >= $room['capacity']);
            ?>
            <div class="col-12 col-sm-6 col-md-4 col-xl-3 room-card-col" data-status="<?php echo sn_e($room['status']); ?>">
                <div class="card border-0 shadow-sm rounded-3 bg-white h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="fw-bold mb-0 text-dark font-monospace"><?php echo sn_e($room['no']); ?></h5>
                        <span class="badge bg-<?php echo $badgeCls; ?>-subtle text-<?php echo $badgeCls; ?> rounded-pill px-2.5 py-1">
                            <?php echo sn_e($room['status']); ?>
                        </span>
                    </div>

                    <div class="mb-3">
                        <span class="text-muted small d-block">Room Type</span>
                        <span class="text-dark fw-semibold"><?php echo sn_e($room['type']); ?></span>
                    </div>

                    <div class="mb-3">
                        <span class="text-muted small d-block">Occupants (<?php echo $countOccupants; ?> / <?php echo $room['capacity']; ?>)</span>
                        <div class="mt-1 d-flex flex-wrap gap-1">
                            <?php if ($countOccupants > 0): ?>
                                <?php foreach ($room['occupants'] as $occ): ?>
                                    <span class="badge bg-light text-dark border px-2 py-1"><i class="bi bi-person me-1"></i><?php echo sn_e($occ); ?></span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span class="text-muted small font-italic">No occupants assigned</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="mt-auto pt-2 border-top border-light">
                        <?php if ($room['status'] === 'Vacant' || ($room['status'] === 'Occupied' && !$isFull)): ?>
                            <button class="btn btn-sm btn-outline-primary w-100 fw-semibold" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#assignModal_<?php echo sn_e(str_replace('-', '_', $room['no'])); ?>">
                                <i class="bi bi-plus-circle me-1"></i> Assign Resident
                            </button>
                        <?php else: ?>
                            <button class="btn btn-sm btn-light w-100 fw-semibold" disabled>
                                <i class="bi bi-slash-circle me-1"></i> Allocation Locked
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Assignment Modal for each room -->
            <?php if ($room['status'] === 'Vacant' || ($room['status'] === 'Occupied' && !$isFull)): ?>
                <div class="modal fade" id="assignModal_<?php echo sn_e(str_replace('-', '_', $room['no'])); ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow-lg rounded-3">
                            <div class="modal-header border-light">
                                <h5 class="modal-title fw-bold text-dark text-uppercase font-monospace" style="font-size: 0.95rem;">Assign Resident to Room <?php echo sn_e($room['no']); ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form method="POST" action="rooms.php" class="assign-form">
                                <input type="hidden" name="room_number" value="<?php echo sn_e($room['no']); ?>">
                                <input type="hidden" name="action" value="assign">
                                <div class="modal-body p-4">
                                    <div class="mb-3">
                                        <label for="assign_resident_<?php echo sn_e($room['no']); ?>" class="form-label text-dark fw-semibold small">Select Unassigned Resident <span class="text-danger">*</span></label>
                                        <select id="assign_resident_<?php echo sn_e($room['no']); ?>" name="resident_id" class="form-select" required>
                                            <option value="">Select Resident</option>
                                            <?php foreach ($residents as $r): ?>
                                                <?php if ($r['status'] === 'Active' && !in_array($r['name'], $room['occupants'])): ?>
                                                    <option value="<?php echo sn_e($r['id']); ?>"><?php echo sn_e($r['name']); ?> (<?php echo sn_e($r['health']); ?>)</option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer border-light">
                                    <button type="button" class="btn btn-sm btn-secondary fw-semibold" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-sm btn-primary fw-semibold">Assign Resident</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <?php
        }
    }
    $html = ob_get_clean();

    header('Content-Type: application/json');
    echo json_encode([
        'html' => $html,
        'count' => count($rooms)
    ]);
    exit;
}

// Initial fetch for page load
$rooms = fetch_rooms_list($pdo);
$residents = fetch_active_unassigned_residents($pdo);

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'admin';
$currentPage   = 'rooms.php';
$sn_asset_root = "../../assets";
$base_path = '../../'; // Ensure correct path prefix
include '../../includes/sidebar.php';
?>

<main id="sn-main-content" role="main" aria-label="Rooms Content" class="p-4 flex-grow-1">
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
            <h3 class="fw-bold mb-0 text-dark">Room Allocation</h3>
            <small class="text-muted">Monitor facility rooms occupancy, cleanings, and assign wings</small>
        </div>

        <!-- Filter Toolbar -->
        <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
            <div class="card-body p-3">
                <div class="row g-3 align-items-center">
                    <div class="col-md-5">
                        <select class="form-select bg-light border-light" id="statusFilter">
                            <option value="All">All Room Statuses</option>
                            <option value="Occupied">Occupied</option>
                            <option value="Vacant">Vacant</option>
                            <option value="Maintenance">Under Maintenance</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Room Cards Grid -->
        <div class="row g-3" id="roomsGrid">
            <?php foreach ($rooms as $room): ?>
                <?php 
                    $badgeCls = $statusBadge[$room['status']] ?? 'secondary';
                    $countOccupants = count($room['occupants']);
                    $isFull = ($countOccupants >= $room['capacity']);
                ?>
                <div class="col-12 col-sm-6 col-md-4 col-xl-3 room-card-col" data-status="<?php echo sn_e($room['status']); ?>">
                    <div class="card border-0 shadow-sm rounded-3 bg-white h-100 p-3">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="fw-bold mb-0 text-dark font-monospace"><?php echo sn_e($room['no']); ?></h5>
                            <span class="badge bg-<?php echo $badgeCls; ?>-subtle text-<?php echo $badgeCls; ?> rounded-pill px-2.5 py-1">
                                <?php echo sn_e($room['status']); ?>
                            </span>
                        </div>

                        <div class="mb-3">
                            <span class="text-muted small d-block">Room Type</span>
                            <span class="text-dark fw-semibold"><?php echo sn_e($room['type']); ?></span>
                        </div>

                        <div class="mb-3">
                            <span class="text-muted small d-block">Occupants (<?php echo $countOccupants; ?> / <?php echo $room['capacity']; ?>)</span>
                            <div class="mt-1 d-flex flex-wrap gap-1">
                                <?php if ($countOccupants > 0): ?>
                                    <?php foreach ($room['occupants'] as $occ): ?>
                                        <span class="badge bg-light text-dark border px-2 py-1"><i class="bi bi-person me-1"></i><?php echo sn_e($occ); ?></span>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <span class="text-muted small font-italic">No occupants assigned</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="mt-auto pt-2 border-top border-light">
                            <?php if ($room['status'] === 'Vacant' || ($room['status'] === 'Occupied' && !$isFull)): ?>
                                <button class="btn btn-sm btn-outline-primary w-100 fw-semibold" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#assignModal_<?php echo sn_e(str_replace('-', '_', $room['no'])); ?>">
                                    <i class="bi bi-plus-circle me-1"></i> Assign Resident
                                </button>
                            <?php else: ?>
                                <button class="btn btn-sm btn-light w-100 fw-semibold" disabled>
                                    <i class="bi bi-slash-circle me-1"></i> Allocation Locked
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Assignment Modal for each room -->
                <?php if ($room['status'] === 'Vacant' || ($room['status'] === 'Occupied' && !$isFull)): ?>
                    <div class="modal fade" id="assignModal_<?php echo sn_e(str_replace('-', '_', $room['no'])); ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg rounded-3">
                                <div class="modal-header border-light">
                                    <h5 class="modal-title fw-bold text-dark text-uppercase font-monospace" style="font-size: 0.95rem;">Assign Resident to Room <?php echo sn_e($room['no']); ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form method="POST" action="rooms.php" class="assign-form">
                                    <input type="hidden" name="room_number" value="<?php echo sn_e($room['no']); ?>">
                                    <input type="hidden" name="action" value="assign">
                                    <div class="modal-body p-4">
                                        <div class="mb-3">
                                            <label for="assign_resident_<?php echo sn_e($room['no']); ?>" class="form-label text-dark fw-semibold small">Select Unassigned Resident <span class="text-danger">*</span></label>
                                            <select id="assign_resident_<?php echo sn_e($room['no']); ?>" name="resident_id" class="form-select" required>
                                                <option value="">Select Resident</option>
                                                <?php foreach ($residents as $r): ?>
                                                    <?php if ($r['status'] === 'Active' && !in_array($r['name'], $room['occupants'])): ?>
                                                        <option value="<?php echo sn_e($r['id']); ?>"><?php echo sn_e($r['name']); ?> (<?php echo sn_e($r['health']); ?>)</option>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-light">
                                        <button type="button" class="btn btn-sm btn-secondary fw-semibold" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-sm btn-primary fw-semibold">Assign Resident</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const statusFilter = document.getElementById('statusFilter');
    const roomsGrid = document.getElementById('roomsGrid');

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

    function loadGrid() {
        const filterVal = statusFilter ? statusFilter.value : 'All';
        
        fetch(`rooms.php?action=fetch&status=${encodeURIComponent(filterVal)}`)
        .then(res => res.json())
        .then(data => {
            roomsGrid.innerHTML = data.html;
        })
        .catch(err => {
            console.error('Error loading rooms:', err);
        });
    }

    if (statusFilter) {
        statusFilter.addEventListener('change', loadGrid);
    }

    // Intercept form submit using event delegation
    document.addEventListener('submit', (e) => {
        const form = e.target.closest('.assign-form') || e.target.closest('form');
        if (form && form.action.includes('rooms.php')) {
            e.preventDefault();
            const formData = new FormData(form);
            formData.append('ajax', '1');

            fetch('rooms.php', {
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
                    
                    // Manually remove modal backdrop and fix body scrolling
                    const backdrop = document.querySelector('.modal-backdrop');
                    if (backdrop) backdrop.remove();
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                    document.body.style.paddingRight = '';
                }

                if (data.success) {
                    showAlert('success', data.message);
                    loadGrid();
                } else {
                    showAlert('danger', data.message);
                }
            })
            .catch(err => {
                showAlert('danger', 'An error occurred while saving.');
            });
        }
    });
});
</script>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
