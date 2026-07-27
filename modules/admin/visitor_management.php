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

// Handle Form Submission Mock
$formSuccess = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formSuccess = 'Visitor check-in logged successfully!';
}

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'admin';
$currentPage   = 'visitor_management.php';
$sn_asset_root = "../../assets";
include '../../includes/sidebar.php';

// Fetch Visitors & Residents Mock Data
$visitors = sn_visitors();
$residents = sn_residents();
$statusBadge = [
    'Checked In'  => 'success',
    'Checked Out' => 'secondary',
    'Scheduled'   => 'warning'
];
?>

<main id="sn-main-content" role="main" aria-label="Visitor Management Content" class="p-4 flex-grow-1">
    <div class="container-fluid">

        <?php if ($formSuccess): ?>
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> <?php echo sn_e($formSuccess); ?>
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
            <form method="POST" action="visitor_management.php">
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
    const tableRows = document.querySelectorAll('#visitorTable tbody tr');
    const emptyRow = document.querySelector('.sn-empty');

    function filterTable() {
        const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const filterVal = statusFilter ? statusFilter.value : 'All';
        
        let visibleCount = 0;

        tableRows.forEach(row => {
            const name = row.querySelector('.fw-semibold')?.textContent.toLowerCase() || '';
            const resident = row.cells[1]?.textContent.toLowerCase() || '';
            const status = row.getAttribute('data-status') || '';
            
            const matchesSearch = name.includes(query) || resident.includes(query);
            
            let matchesFilter = true;
            if (filterVal !== 'All') {
                matchesFilter = (status === filterVal);
            }

            if (matchesSearch && matchesFilter) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        if (emptyRow) {
            emptyRow.style.display = (visibleCount === 0) ? 'block' : 'none';
        }
    }

    if (searchInput) searchInput.addEventListener('input', filterTable);
    if (statusFilter) statusFilter.addEventListener('change', filterTable);
});
</script>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
