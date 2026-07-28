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

// Database Connection
require_once __DIR__ . '/../../config/database.php';
$pdo = get_db_connection();

$formSuccess = '';
$formError = '';

// Handle POST actions (Add, Update Quantity, Delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'add';
    $is_ajax = (isset($_POST['ajax']) && $_POST['ajax'] == '1');

    if ($action === 'add') {
        $name = trim($_POST['name'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $qty = (int)($_POST['qty'] ?? 0);
        $unit = trim($_POST['unit'] ?? '');
        $reorder = (int)($_POST['reorder'] ?? 0);

        if (empty($name) || empty($category) || empty($unit)) {
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

                // Map UI category to DB ENUM
                $db_cat = 'Other';
                if ($category === 'Medicine') $db_cat = 'Medical';
                elseif ($category === 'Food') $db_cat = 'Food';
                elseif ($category === 'Daily Essentials') $db_cat = 'Hygiene';

                // Prevent duplicate inventory items
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM inventory WHERE item_name = ? AND item_category = ?");
                $stmt->execute([$name, $db_cat]);
                if ($stmt->fetchColumn() > 0) {
                    throw new Exception('An item with this name and category is already cataloged.');
                }

                $stmt_ins = $pdo->prepare("INSERT INTO inventory (
                    item_name, item_category, quantity, unit, min_quantity, last_restocked
                ) VALUES (?, ?, ?, ?, ?, NOW())");
                $stmt_ins->execute([$name, $db_cat, $qty, $unit, $reorder]);

                $pdo->commit();
                $msg = 'Inventory stock item registered successfully!';
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
    } elseif ($action === 'update_qty') {
        $item_id = (int)($_POST['id'] ?? 0);
        $qty = (int)($_POST['qty'] ?? 0);
        try {
            if ($qty < 0) {
                throw new Exception('Quantity cannot be negative.');
            }
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("UPDATE inventory SET quantity = ?, last_restocked = NOW() WHERE item_id = ?");
            $stmt->execute([$qty, $item_id]);
            $pdo->commit();
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Stock quantity updated successfully!']);
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            exit;
        }
    } elseif ($action === 'delete') {
        $item_id = (int)($_POST['id'] ?? 0);
        try {
            $stmt = $pdo->prepare("DELETE FROM inventory WHERE item_id = ?");
            $stmt->execute([$item_id]);
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Inventory item removed successfully!']);
            exit;
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            exit;
        }
    }
}

// Fetch helper mapping logic
function fetch_inventory_list($pdo, $search = '', $category_filter = 'All') {
    $query_str = "SELECT * FROM inventory WHERE 1=1";
    $params = [];

    if ($category_filter !== 'All') {
        $db_cat = 'Other';
        if ($category_filter === 'Medicine') $db_cat = 'Medical';
        elseif ($category_filter === 'Food') $db_cat = 'Food';
        elseif ($category_filter === 'Daily Essentials') $db_cat = 'Hygiene';
        
        $query_str .= " AND item_category = ?";
        $params[] = $db_cat;
    }

    if (!empty($search)) {
        $query_str .= " AND (item_name LIKE ?)";
        $search_term = "%$search%";
        $params[] = $search_term;
    }

    $query_str .= " ORDER BY item_id ASC";
    $stmt = $pdo->prepare($query_str);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();

    $inventory_list = [];
    foreach ($rows as $row) {
        $category = 'Other';
        if ($row['item_category'] === 'Medical') $category = 'Medicine';
        elseif ($row['item_category'] === 'Food') $category = 'Food';
        elseif ($row['item_category'] === 'Hygiene') $category = 'Daily Essentials';

        $status = 'In Stock';
        if ($row['quantity'] <= 0) {
            $status = 'Out of Stock';
        } elseif ($row['quantity'] <= $row['min_quantity']) {
            $status = 'Low Stock';
        }

        $inventory_list[] = [
            'id' => 'INV-' . str_pad($row['item_id'], 4, '0', STR_PAD_LEFT),
            'item_id' => $row['item_id'],
            'name' => $row['item_name'],
            'category' => $category,
            'qty' => $row['quantity'],
            'unit' => $row['unit'],
            'reorder' => $row['min_quantity'],
            'status' => $status
        ];
    }
    return $inventory_list;
}

// Fetch all counts for dynamic KPI cards
function fetch_inventory_counts($pdo) {
    $counts = ['Total' => 0, 'Low' => 0, 'Out' => 0];
    $stmt = $pdo->query("SELECT quantity, min_quantity FROM inventory");
    $items = $stmt->fetchAll();
    $counts['Total'] = count($items);
    foreach ($items as $item) {
        if ($item['quantity'] <= 0) {
            $counts['Out']++;
        } elseif ($item['quantity'] <= $item['min_quantity']) {
            $counts['Low']++;
        }
    }
    return $counts;
}

// Handle AJAX Fetch Request
if (isset($_GET['action']) && $_GET['action'] === 'fetch') {
    $search = trim($_GET['search'] ?? '');
    $category_filter = trim($_GET['category'] ?? 'All');

    $inventory = fetch_inventory_list($pdo, $search, $category_filter);
    $kpis = fetch_inventory_counts($pdo);

    ob_start();
    if (empty($inventory)) {
        echo '<tr><td colspan="7" class="text-center py-4 text-muted">No inventory items found</td></tr>';
    } else {
        foreach ($inventory as $item) {
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
                    <button class="btn btn-sm btn-light text-primary me-1 btn-update-qty" 
                            title="Update Quantity"
                            data-id="<?php echo (int)$item['item_id']; ?>"
                            data-qty="<?php echo (int)$item['qty']; ?>"
                            data-name="<?php echo sn_e($item['name']); ?>">
                        <i class="bi bi-arrow-repeat"></i>
                    </button>
                    <button class="btn btn-sm btn-light text-danger btn-delete-item" 
                            title="Delete Item"
                            data-id="<?php echo (int)$item['item_id']; ?>"
                            data-name="<?php echo sn_e($item['name']); ?>">
                        <i class="bi bi-trash3-fill"></i>
                    </button>
                </td>
            </tr>
            <?php
        }
    }
    $html = ob_get_clean();

    header('Content-Type: application/json');
    echo json_encode([
        'html' => $html,
        'count' => count($inventory),
        'total' => $kpis['Total'],
        'low' => $kpis['Low'],
        'out' => $kpis['Out']
    ]);
    exit;
}

// Initial fetch for page load
$inventory = fetch_inventory_list($pdo);
$counts = fetch_inventory_counts($pdo);

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'admin';
$currentPage   = 'inventory.php';
$sn_asset_root = "../../assets";
$base_path = '../../'; // Ensure correct path prefix
include '../../includes/sidebar.php';
?>

<main id="sn-main-content" role="main" aria-label="Inventory Content" class="p-4 flex-grow-1">
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
                                        <button class="btn btn-sm btn-light text-primary me-1 btn-update-qty" 
                                                title="Update Quantity"
                                                data-id="<?php echo (int)$item['item_id']; ?>"
                                                data-qty="<?php echo (int)$item['qty']; ?>"
                                                data-name="<?php echo sn_e($item['name']); ?>">
                                            <i class="bi bi-arrow-repeat"></i>
                                        </button>
                                        <button class="btn btn-sm btn-light text-danger btn-delete-item" 
                                                title="Delete Item"
                                                data-id="<?php echo (int)$item['item_id']; ?>"
                                                data-name="<?php echo sn_e($item['name']); ?>">
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
            <form method="POST" action="inventory.php" class="inventory-form">
                <input type="hidden" name="action" value="add">
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
    const tbody = document.querySelector('#inventoryTable tbody');
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
        const filterVal = categoryFilter ? categoryFilter.value : 'All';
        
        fetch(`inventory.php?action=fetch&search=${encodeURIComponent(query)}&category=${encodeURIComponent(filterVal)}`)
        .then(res => res.json())
        .then(data => {
            tbody.innerHTML = data.html;
            
            // Check empty state
            const hasRows = tbody.querySelectorAll('tr').length > 0 && !tbody.querySelector('td[colspan]');
            if (emptyRow) {
                emptyRow.style.display = hasRows ? 'none' : 'block';
            }

            // Update KPI cards
            const kpiTotal = document.querySelector('.border-primary h3');
            if (kpiTotal) kpiTotal.textContent = data.total;

            const kpiLow = document.querySelector('.border-warning h3');
            if (kpiLow) kpiLow.textContent = data.low;

            const kpiOut = document.querySelector('.border-danger h3');
            if (kpiOut) kpiOut.textContent = data.out;
        })
        .catch(err => {
            console.error('Error loading inventory:', err);
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

    if (categoryFilter) {
        categoryFilter.addEventListener('change', loadTable);
    }

    // Delegate Update Quantity click
    tbody.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-update-qty');
        if (btn) {
            const id = btn.getAttribute('data-id');
            const name = btn.getAttribute('data-name');
            const currentQty = btn.getAttribute('data-qty');
            const newQty = prompt(`Enter new stock quantity for "${name}":`, currentQty);
            
            if (newQty !== null) {
                const parsedQty = parseInt(newQty.trim(), 10);
                if (isNaN(parsedQty) || parsedQty < 0) {
                    alert('Please enter a valid non-negative quantity number.');
                    return;
                }

                const formData = new FormData();
                formData.append('action', 'update_qty');
                formData.append('id', id);
                formData.append('qty', parsedQty);
                formData.append('ajax', '1');

                fetch('inventory.php', {
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
                    showAlert('danger', 'An error occurred while updating the quantity.');
                });
            }
        }
    });

    // Delegate Delete click
    tbody.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-delete-item');
        if (btn) {
            const id = btn.getAttribute('data-id');
            const name = btn.getAttribute('data-name');
            if (confirm(`Are you sure you want to remove item "${name}" from inventory?`)) {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('id', id);
                formData.append('ajax', '1');

                fetch('inventory.php', {
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
                    showAlert('danger', 'An error occurred while deleting the item.');
                });
            }
        }
    });

    // Intercept form submit inside modals
    const form = document.querySelector('form.inventory-form');
    if (form) {
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(form);
            formData.append('ajax', '1');

            fetch('inventory.php', {
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
