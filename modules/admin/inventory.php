<?php
/**
 * SevaNest — Facility Inventory Control
 * File     : modules/admin/inventory.php
 * Version  : 1.0
 */

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/functions.php';

// Require Admin login
require_login();
require_role(['Admin', 'Old Age Home Admin']);

$base_path = '../../';
$page_title = 'Inventory Roster | SevaNest';

// Handle Form Submission Mock
$formSuccess = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formSuccess = 'Inventory stocks updated successfully!';
}

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'admin';
$currentPage   = 'inventory.php';
$sn_asset_root = "../../assets";
include '../../includes/sidebar.php';

// Mock Inventory Data
$inventory = [
    ['id' => 'INV-3001', 'name' => 'Vitamin C 500mg', 'category' => 'Medicine', 'qty' => 120, 'unit' => 'Tablets', 'reorder' => 200, 'status' => 'Low Stock'],
    ['id' => 'INV-3002', 'name' => 'Paracetamol 650mg', 'category' => 'Medicine', 'qty' => 450, 'unit' => 'Tablets', 'reorder' => 150, 'status' => 'In Stock'],
    ['id' => 'INV-3003', 'name' => 'Basmati Rice', 'category' => 'Food', 'qty' => 15, 'unit' => 'Kgs', 'reorder' => 30, 'status' => 'Low Stock'],
    ['id' => 'INV-3004', 'name' => 'Cooking Oil (Sunflower)', 'category' => 'Food', 'qty' => 45, 'unit' => 'Litres', 'reorder' => 10, 'status' => 'In Stock'],
    ['id' => 'INV-3005', 'name' => 'Hand Sanitizer', 'category' => 'Daily Essentials', 'qty' => 12, 'unit' => 'Bottles', 'reorder' => 5, 'status' => 'In Stock'],
    ['id' => 'INV-3006', 'name' => 'Adult Diapers (Large)', 'category' => 'Daily Essentials', 'qty' => 0, 'unit' => 'Packs', 'reorder' => 8, 'status' => 'Out of Stock'],
];

$counts = ['Total' => count($inventory), 'Low' => 0, 'Out' => 0];
foreach ($inventory as $item) {
    if ($item['status'] === 'Low Stock') $counts['Low']++;
    if ($item['status'] === 'Out of Stock') $counts['Out']++;
}
?>

<main id="sn-main-content" role="main" aria-label="Inventory Content" class="p-4 flex-grow-1">
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
                <h3 class="fw-bold mb-0 text-dark">Inventory Roster</h3>
                <small class="text-muted">Monitor facility medicine, food, and daily essentials stock status</small>
            </div>
            <button class="btn btn-primary fw-semibold" data-bs-toggle="modal" data-bs-target="#newItemModal">
                <i class="bi bi-plus-circle me-1"></i> Add Stock Item
            </button>
        </div>

        <!-- 3 KPI Cards -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-4 border-primary">
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.75rem;">Total Items Cataloged</span>
                    <h3 class="fw-bold mb-0 text-dark"><?php echo $counts['Total']; ?></h3>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-4 border-warning">
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.75rem;">Low Stock Items</span>
                    <h3 class="fw-bold mb-0 text-warning"><?php echo $counts['Low']; ?></h3>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-4 border-danger">
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.75rem;">Out of Stock Items</span>
                    <h3 class="fw-bold mb-0 text-danger"><?php echo $counts['Out']; ?></h3>
                </div>
            </div>
        </div>

        <!-- Inventory Table Card -->
        <div class="card border-0 shadow-sm rounded-3 bg-white">
            <div class="card-header bg-white border-bottom border-light p-3">
                <div class="row align-items-center g-3">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-light text-muted"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control bg-light border-light" placeholder="Search inventory items..." data-table-search>
                        </div>
                    </div>
                    <div class="col-md-3 ms-auto">
                        <select class="form-select bg-light border-light" data-table-filter>
                            <option value="All">All Categories</option>
                            <option value="Medicine">Medicine</option>
                            <option value="Food">Food</option>
                            <option value="Daily Essentials">Daily Essentials</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="inventoryTable" style="font-size: 0.9rem;">
                        <thead class="table-light text-muted" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em;">
                            <tr>
                                <th class="ps-3">Item ID</th>
                                <th>Item Name</th>
                                <th>Category</th>
                                <th>Quantity In Stock</th>
                                <th>Reorder Level</th>
                                <th>Status</th>
                                <th class="pe-3 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($inventory as $item): ?>
                                <?php 
                                    $badge = 'secondary';
                                    if ($item['status'] === 'In Stock') $badge = 'success';
                                    if ($item['status'] === 'Low Stock') $badge = 'warning';
                                    if ($item['status'] === 'Out of Stock') $badge = 'danger';
                                ?>
                                <tr data-category="<?php echo sn_e($item['category']); ?>">
                                    <td class="ps-3"><span class="font-monospace text-muted"><?php echo sn_e($item['id']); ?></span></td>
                                    <td><span class="fw-semibold text-dark"><?php echo sn_e($item['name']); ?></span></td>
                                    <td><span class="text-dark"><?php echo sn_e($item['category']); ?></span></td>
                                    <td><span class="text-dark fw-bold"><?php echo sn_e($item['qty'] . ' ' . $item['unit']); ?></span></td>
                                    <td><span class="text-muted"><?php echo sn_e($item['reorder'] . ' ' . $item['unit']); ?></span></td>
                                    <td>
                                        <span class="badge bg-<?php echo $badge; ?>-subtle text-<?php echo $badge; ?> rounded-pill px-2.5 py-1">
                                            <?php echo sn_e($item['status']); ?>
                                        </span>
                                    </td>
                                    <td class="pe-3 text-end">
                                        <button class="btn btn-sm btn-light text-primary me-1" title="Update Quantity">
                                            <i class="bi bi-arrow-repeat"></i>
                                        </button>
                                        <button class="btn btn-sm btn-light text-danger" title="Delete Item">
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
                    <i class="bi bi-box-x text-muted display-4"></i>
                    <p class="mt-2 fw-semibold text-dark">No inventory items found</p>
                    <p class="text-muted small">Try adjusting your filters or search criteria</p>
                </div>
            </div>
        </div>

    </div>
</main>

<!-- Add Item Modal -->
<div class="modal fade" id="newItemModal" tabindex="-1" aria-labelledby="newItemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header border-light">
                <h5 class="modal-title fw-bold text-dark" id="newItemModalLabel">Add Stock Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="inventory.php">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="inv_name" class="form-label text-dark fw-semibold small">Item Name <span class="text-danger">*</span></label>
                        <input type="text" id="inv_name" name="name" class="form-control" placeholder="e.g. Paracetamol 650mg" required>
                    </div>
                    <div class="mb-3">
                        <label for="inv_category" class="form-label text-dark fw-semibold small">Category <span class="text-danger">*</span></label>
                        <select id="inv_category" name="category" class="form-select" required>
                            <option value="">Select Category</option>
                            <option>Medicine</option>
                            <option>Food</option>
                            <option>Daily Essentials</option>
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label for="inv_qty" class="form-label text-dark fw-semibold small">Quantity <span class="text-danger">*</span></label>
                            <input type="number" id="inv_qty" name="qty" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label for="inv_unit" class="form-label text-dark fw-semibold small">Unit <span class="text-danger">*</span></label>
                            <input type="text" id="inv_unit" name="unit" class="form-control" placeholder="e.g. Tablets, Kgs" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="inv_reorder" class="form-label text-dark fw-semibold small">Reorder Threshold <span class="text-danger">*</span></label>
                        <input type="number" id="inv_reorder" name="reorder" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer border-light">
                    <button type="button" class="btn btn-sm btn-secondary fw-semibold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary fw-semibold">Add Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.querySelector('[data-table-search]');
    const categoryFilter = document.querySelector('[data-table-filter]');
    const tableRows = document.querySelectorAll('#inventoryTable tbody tr');
    const emptyRow = document.querySelector('.sn-empty');

    function filterTable() {
        const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const filterVal = categoryFilter ? categoryFilter.value : 'All';
        
        let visibleCount = 0;

        tableRows.forEach(row => {
            const name = row.querySelector('.fw-semibold')?.textContent.toLowerCase() || '';
            const category = row.getAttribute('data-category') || '';
            
            const matchesSearch = name.includes(query);
            
            let matchesFilter = true;
            if (filterVal !== 'All') {
                matchesFilter = (category === filterVal);
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
    if (categoryFilter) categoryFilter.addEventListener('change', filterTable);
});
</script>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
