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

// Handle Form Submission Mock
$formSuccess = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formSuccess = 'Admission request submitted successfully!';
}

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'admin';
$currentPage   = 'admission_management.php';
$sn_asset_root = "../../assets";
include '../../includes/sidebar.php';

// Fetch Admissions Mock Data
$admissions = sn_admissions();
$statusBadge = [
    'Pending'      => 'warning',
    'Under Review' => 'secondary',
    'Approved'     => 'success',
    'Rejected'     => 'danger'
];

$counts = ['All' => count($admissions)];
foreach ($admissions as $a) {
    $counts[$a['status']] = ($counts[$a['status']] ?? 0) + 1;
}
?>

<main id="sn-main-content" role="main" aria-label="Admission Management Content" class="p-4 flex-grow-1">
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
            <form method="POST" action="admission_management.php">
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
    const tableRows = document.querySelectorAll('#admissionsTable tbody tr');
    const emptyRow = document.querySelector('.sn-empty');

    function filterTable() {
        const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const activeTab = document.querySelector('.sn-tabs button.is-active')?.getAttribute('data-tab-target') || 'All';
        
        let visibleCount = 0;

        tableRows.forEach(row => {
            const name = row.querySelector('.fw-semibold')?.textContent.toLowerCase() || '';
            const guardian = row.cells[3]?.textContent.toLowerCase() || '';
            const status = row.getAttribute('data-status') || '';
            
            const matchesSearch = name.includes(query) || guardian.includes(query);
            
            let matchesTab = true;
            if (activeTab !== 'All') {
                matchesTab = (status === activeTab);
            }

            if (matchesSearch && matchesTab) {
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

    if (searchInput) {
        searchInput.addEventListener('input', filterTable);
    }

    if (tabButtons) {
        tabButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                tabButtons.forEach(b => b.classList.remove('is-active', 'btn-primary'));
                tabButtons.forEach(b => b.classList.add('btn-outline-primary'));
                btn.classList.add('is-active', 'btn-primary');
                btn.classList.remove('btn-outline-primary');
                filterTable();
            });
        });
    }
});
</script>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
