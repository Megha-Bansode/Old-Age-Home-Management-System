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

// Handle Form Submission Mock
$formSuccess = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formSuccess = 'Resident successfully assigned to the room!';
}

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'admin';
$currentPage   = 'rooms.php';
$sn_asset_root = "../../assets";
include '../../includes/sidebar.php';

// Mock Room Data
$rooms = [
    ['no' => 'A-101', 'type' => 'Deluxe Suite', 'capacity' => 1, 'occupants' => ['Kamala Devi'], 'status' => 'Occupied'],
    ['no' => 'A-102', 'type' => 'Single Sharing', 'capacity' => 1, 'occupants' => [], 'status' => 'Vacant'],
    ['no' => 'A-103', 'type' => 'Double Sharing', 'capacity' => 2, 'occupants' => ['Harish Mehta'], 'status' => 'Occupied'],
    ['no' => 'B-101', 'type' => 'Double Sharing', 'capacity' => 2, 'occupants' => ['Devaki Amma', 'Gopal Prasad'], 'status' => 'Occupied'],
    ['no' => 'B-102', 'type' => 'Single Sharing', 'capacity' => 1, 'occupants' => [], 'status' => 'Maintenance'],
    ['no' => 'C-101', 'type' => 'Single Sharing', 'capacity' => 1, 'occupants' => [], 'status' => 'Vacant'],
];

$residents = sn_residents();
$statusBadge = [
    'Occupied'    => 'success',
    'Vacant'      => 'secondary',
    'Maintenance' => 'warning'
];
?>

<main id="sn-main-content" role="main" aria-label="Rooms Content" class="p-4 flex-grow-1">
    <div class="container-fluid">

        <?php if ($formSuccess): ?>
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> <?php echo sn_e($formSuccess); ?>
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
                                <form method="POST" action="rooms.php">
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
    const cards = document.querySelectorAll('.room-card-col');

    function filterCards() {
        const filterVal = statusFilter ? statusFilter.value : 'All';
        
        cards.forEach(card => {
            const status = card.getAttribute('data-status') || '';
            
            let matchesFilter = true;
            if (filterVal !== 'All') {
                matchesFilter = (status === filterVal);
            }

            if (matchesFilter) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    }

    if (statusFilter) statusFilter.addEventListener('change', filterCards);
});
</script>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
