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

// Handle Form Submission Mock
$formSuccess = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formSuccess = 'New staff member registered successfully!';
}

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'admin';
$currentPage   = 'staff_management.php';
$sn_asset_root = "../../assets";
include '../../includes/sidebar.php';

// Fetch Staff Mock Data
$staff = sn_staff();
$statusBadge = [
    'On Duty'  => 'success',
    'Off Duty' => 'secondary',
    'On Leave' => 'warning'
];
?>

<main id="sn-main-content" role="main" aria-label="Staff Management Content" class="p-4 flex-grow-1">
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
            <form method="POST" action="staff_management.php">
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
    const tableRows = document.querySelectorAll('#staffTable tbody tr');
    const emptyRow = document.querySelector('.sn-empty');

    function filterTable() {
        const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const filterVal = statusFilter ? statusFilter.value : 'All';
        
        let visibleCount = 0;

        tableRows.forEach(row => {
            const name = row.querySelector('.fw-semibold')?.textContent.toLowerCase() || '';
            const status = row.getAttribute('data-status') || '';
            
            const matchesSearch = name.includes(query);
            
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
