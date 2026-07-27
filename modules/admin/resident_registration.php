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

// Handle Form Submission Mock
$formSuccess = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formSuccess = 'Resident registered successfully!';
}

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'admin';
$currentPage   = 'resident_registration.php';
$sn_asset_root = "../../assets";
include '../../includes/sidebar.php';

// Fetch Residents Mock Data
$residents = sn_residents();
$healthBadge = ['Stable' => 'success', 'Needs Care' => 'warning', 'Critical' => 'danger'];
$statusBadge = ['Active' => 'success', 'Discharged' => 'secondary'];
?>

<main id="sn-main-content" role="main" aria-label="Resident Registration Content" class="p-4 flex-grow-1">
    <div class="container-fluid">

        <?php if ($formSuccess): ?>
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> <?php echo sn_e($formSuccess); ?>
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
                                    $initials = strtoupper($r['name'][0]);
                                ?>
                                <tr data-status="<?php echo sn_e($r['status']); ?>">
                                    <td class="ps-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center fw-bold text-primary" style="width: 40px; height: 40px; font-size: 0.95rem;">
                                                <?php echo sn_e($initials); ?>
                                            </div>
                                            <div>
                                                <div class="fw-semibold text-dark"><?php echo sn_e($r['name']); ?></div>
                                                <small class="text-muted"><?php echo sn_e($r['id']); ?> · Age <?php echo (int)$r['age']; ?></small>
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
                                        <button class="btn btn-sm btn-light text-primary me-1" title="Edit">
                                            <i class="bi bi-pencil-fill"></i>
                                        </button>
                                        <button class="btn btn-sm btn-light text-danger" title="Delete">
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
    const tableRows = document.querySelectorAll('#residentsTable tbody tr');
    const emptyRow = document.querySelector('.sn-empty');

    function filterTable() {
        const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const filterVal = statusFilter ? statusFilter.value : 'All';
        
        let visibleCount = 0;

        tableRows.forEach(row => {
            const name = row.querySelector('.fw-semibold')?.textContent.toLowerCase() || '';
            const guardian = row.cells[3]?.querySelector('.fw-semibold')?.textContent.toLowerCase() || '';
            const status = row.getAttribute('data-status') || '';
            
            const matchesSearch = name.includes(query) || guardian.includes(query);
            
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
