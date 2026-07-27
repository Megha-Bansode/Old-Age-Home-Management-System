<?php
/**
 * SevaNest — Resident Directory
 * File     : modules/admin/residents.php
 * Version  : 1.0
 */

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/functions.php';

// Require Admin login
require_login();
require_role(['Admin', 'Old Age Home Admin']);

$base_path = '../../';
$page_title = 'Resident Directory | SevaNest';

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'admin';
$currentPage   = 'residents.php';
$sn_asset_root = "../../assets";
include '../../includes/sidebar.php';

// Fetch Residents Mock Data
$residents = sn_residents();

$healthBadge = [
    'Stable'     => 'success',
    'Needs Care' => 'warning',
    'Critical'   => 'danger'
];

// Mock assignments for demo
$doctorMap = [
    'RES-2001' => 'Dr. Priya Nair',
    'RES-2002' => 'Dr. Robert Watson',
    'RES-2003' => 'Dr. Priya Nair',
    'RES-2004' => 'Dr. Robert Watson'
];
$caretakerMap = [
    'RES-2001' => 'Meena Patil',
    'RES-2002' => 'Suresh Kumar',
    'RES-2003' => 'Meena Patil',
    'RES-2004' => 'Suresh Kumar'
];
?>

<main id="sn-main-content" role="main" aria-label="Residents Content" class="p-4 flex-grow-1">
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="mb-4">
            <h3 class="fw-bold mb-0 text-dark">Resident Directory</h3>
            <small class="text-muted">Browse current residents, room locations, caretaking, and health statuses</small>
        </div>

        <!-- Filter Toolbar -->
        <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
            <div class="card-body p-3">
                <div class="row g-3 align-items-center">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-light text-muted"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control bg-light border-light" id="directorySearch" placeholder="Search by name, room or doctor...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select bg-light border-light" id="healthFilter">
                            <option value="All">All Health Statuses</option>
                            <option value="Stable">Stable</option>
                            <option value="Needs Care">Needs Care</option>
                            <option value="Critical">Critical</option>
                        </select>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <a href="resident_registration.php" class="btn btn-primary fw-semibold"><i class="bi bi-person-plus me-1"></i> Register Resident</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Residents Grid -->
        <div class="row g-3" id="residentsGrid">
            <?php foreach ($residents as $r): ?>
                <?php 
                    $hBadge = $healthBadge[$r['health']] ?? 'secondary';
                    $doc = $doctorMap[$r['id']] ?? 'Dr. Priya Nair';
                    $carer = $caretakerMap[$r['id']] ?? 'Meena Patil';
                    $initials = strtoupper($r['name'][0]);
                ?>
                <div class="col-12 col-md-6 col-xl-4 resident-card-col" data-health="<?php echo sn_e($r['health']); ?>" data-search-term="<?php echo sn_e(strtolower($r['name'] . ' ' . $r['room'] . ' ' . $doc)); ?>">
                    <div class="card border-0 shadow-sm rounded-3 bg-white h-100 p-3">
                        <div class="d-flex align-items-start justify-content-between mb-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold" style="width: 54px; height: 54px; font-size: 1.25rem;">
                                    <?php echo sn_e($initials); ?>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1 text-dark"><?php echo sn_e($r['name']); ?></h6>
                                    <span class="text-muted font-monospace small" style="font-size: 0.75rem;"><?php echo sn_e($r['id']); ?></span>
                                </div>
                            </div>
                            <span class="badge bg-<?php echo $hBadge; ?>-subtle text-<?php echo $hBadge; ?> rounded-pill px-2.5 py-1">
                                <?php echo sn_e($r['health']); ?>
                            </span>
                        </div>

                        <!-- Card details -->
                        <div class="fs-6 d-flex flex-column gap-2 mb-3 pt-2 border-top border-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small">Current Room</span>
                                <span class="fw-semibold text-dark font-monospace"><?php echo sn_e($r['room']); ?></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small">Assigned Doctor</span>
                                <span class="fw-semibold text-dark"><?php echo sn_e($doc); ?></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small">Assigned Caretaker</span>
                                <span class="fw-semibold text-dark"><?php echo sn_e($carer); ?></span>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-primary w-100 fw-semibold" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#profileModal_<?php echo sn_e($r['id']); ?>">
                                <i class="bi bi-person-lines-fill me-1"></i> Quick Profile
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Profile Modal for each resident -->
                <div class="modal fade" id="profileModal_<?php echo sn_e($r['id']); ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow-lg rounded-3">
                            <div class="modal-header border-light">
                                <h5 class="modal-title fw-bold text-dark">Resident Quick Profile</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4">
                                <div class="text-center mb-4">
                                    <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold mx-auto mb-2" style="width: 80px; height: 80px; font-size: 2rem;">
                                        <?php echo sn_e($initials); ?>
                                    </div>
                                    <h5 class="fw-bold mb-1 text-dark"><?php echo sn_e($r['name']); ?></h5>
                                    <span class="badge bg-light text-dark font-monospace"><?php echo sn_e($r['id']); ?></span>
                                </div>

                                <div class="row g-3 fs-6">
                                    <div class="col-6">
                                        <span class="text-muted small d-block">Room No.</span>
                                        <strong class="text-dark font-monospace"><?php echo sn_e($r['room']); ?></strong>
                                    </div>
                                    <div class="col-6">
                                        <span class="text-muted small d-block">Admission Date</span>
                                        <strong class="text-dark"><?php echo sn_e($r['admission']); ?></strong>
                                    </div>
                                    <hr class="border-light my-2">
                                    <div class="col-12">
                                        <span class="text-muted small d-block">Guardian Contact</span>
                                        <strong class="text-dark"><?php echo sn_e($r['guardian']); ?> (<?php echo sn_e($r['phone']); ?>)</strong>
                                    </div>
                                    <hr class="border-light my-2">
                                    <div class="col-12">
                                        <span class="text-muted small d-block">Assigned Care Staff</span>
                                        <p class="mb-0 text-dark small"><i class="bi bi-hospital text-danger me-1"></i><?php echo sn_e($doc); ?> (Physician)</p>
                                        <p class="mb-0 text-dark small"><i class="bi bi-person-heart text-success me-1"></i><?php echo sn_e($carer); ?> (Caretaker)</p>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer border-light">
                                <button type="button" class="btn btn-sm btn-secondary fw-semibold" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Empty State -->
        <div class="text-center py-5" id="noResidentsAlert" style="display: none;">
            <i class="bi bi-people-fill text-muted display-3"></i>
            <p class="mt-3 fw-bold text-dark fs-5">No residents match your search</p>
            <p class="text-muted small">Try a different keyword or health filter status</p>
        </div>

    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('directorySearch');
    const healthFilter = document.getElementById('healthFilter');
    const cards = document.querySelectorAll('.resident-card-col');
    const noAlert = document.getElementById('noResidentsAlert');

    function filterCards() {
        const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const filterVal = healthFilter ? healthFilter.value : 'All';
        
        let visibleCount = 0;

        cards.forEach(card => {
            const searchTerm = card.getAttribute('data-search-term') || '';
            const health = card.getAttribute('data-health') || '';
            
            const matchesSearch = searchTerm.includes(query);
            
            let matchesFilter = true;
            if (filterVal !== 'All') {
                matchesFilter = (health === filterVal);
            }

            if (matchesSearch && matchesFilter) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        if (noAlert) {
            noAlert.style.display = (visibleCount === 0) ? 'block' : 'none';
        }
    }

    if (searchInput) searchInput.addEventListener('input', filterCards);
    if (healthFilter) healthFilter.addEventListener('change', filterCards);
});
</script>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
