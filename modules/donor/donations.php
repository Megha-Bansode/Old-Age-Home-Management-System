<?php
/**
 * SevaNest – Donor Donation History
 * File     : modules/donor/donations.php
 * Version  : 1.0
 */

require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../config/database.php';

// Require Donor login
require_login();
require_role('Donor');

$pdo = get_db_connection();
$donor_id = $_SESSION['user_id'] ?? 6;

// Filters
$search = trim($_GET['search'] ?? '');
$category = trim($_GET['category'] ?? '');
$status = trim($_GET['status'] ?? '');

$limit = 5;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

$where = ["donor_id = :donor_id"];
$params = [':donor_id' => $donor_id];

if (!empty($search)) {
    $where[] = "(purpose LIKE :search OR transaction_id LIKE :search OR receipt_number LIKE :search)";
    $params[':search'] = '%' . $search . '%';
}

if (!empty($category)) {
    $where[] = "purpose = :category";
    $params[':category'] = $category;
}

$where_clause = implode(" AND ", $where);

// Count total
$count_stmt = $pdo->prepare("SELECT COUNT(*) FROM donations WHERE $where_clause");
$count_stmt->execute($params);
$total_records = (int)$count_stmt->fetchColumn();
$total_pages = max(1, ceil($total_records / $limit));

// Fetch paginated results
$stmt = $pdo->prepare("SELECT * FROM donations WHERE $where_clause ORDER BY donation_date DESC LIMIT $limit OFFSET $offset");
$stmt->execute($params);
$donations = $stmt->fetchAll();

// Get list of distinct purposes for category filter dropdown
$purposes = $pdo->query("SELECT DISTINCT purpose FROM donations WHERE purpose IS NOT NULL")->fetchAll(PDO::FETCH_COLUMN);

$base_path = '../../';
$page_title = 'Donations | SevaNest';
$extra_css = [
    'assets/css/donor.css'
];

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'donor';
$currentPage   = 'donations.php';
$sn_asset_root = "../../assets";
$base_path = '../../';
include '../../includes/sidebar.php';
?>

<!-- ═══════════════════════════════════════════════════════════════════════
     MAIN CONTENT AREA
     ═══════════════════════════════════════════════════════════════════════ -->
<main id="sn-main-content" role="main" aria-label="Donor donations content">
    <div class="donor-page-wrapper">
        
        <!-- Header Strip -->
        <div class="dn-header-strip animate-fade-in">
            <div>
                <h2 class="dn-header-strip__title">My Donation History</h2>
                <p style="color: var(--color-text-muted-team); margin: 4px 0 0;">Review your contributions, track payment statuses and access tax receipts.</p>
            </div>
            <div>
                <a href="campaigns.php" class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>Make New Donation</a>
            </div>
        </div>

        <!-- Toolbar Filters -->
        <form method="GET" action="donations.php" class="toolbar" id="filterForm">
            <div class="search inline">
                <span>🔎</span>
                <input name="search" placeholder="Search donations..." id="attSearch" value="<?php echo sn_e($search); ?>" onkeyup="if(event.key === 'Enter') this.form.submit()">
            </div>
            <select name="category" class="select" onchange="this.form.submit()">
                <option value="">All Categories</option>
                <?php foreach ($purposes as $purp): ?>
                    <option value="<?php echo sn_e($purp); ?>" <?php if ($category === $purp) echo 'selected'; ?>><?php echo sn_e($purp); ?></option>
                <?php endforeach; ?>
            </select>
            <select name="status" class="select" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="Successful" <?php if ($status === 'Successful') echo 'selected'; ?>>Successful</option>
            </select>
        </form>

        <!-- Donations Table -->
        <div class="card no-pad animate-fade-in">
            <div class="table-wrap">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Donation ID</th>
                            <th>Campaign/Category</th>
                            <th>Amount</th>
                            <th>Donation Type</th>
                            <th>Payment Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($donations)): ?>
                            <tr><td colspan="6" class="text-center text-muted py-3">No donations found matching criteria.</td></tr>
                        <?php else: ?>
                            <?php foreach ($donations as $don): ?>
                                <tr>
                                    <td><b>#DON-<?php echo $don['donation_id']; ?></b></td>
                                    <td><?php echo sn_e($don['purpose'] ?? 'General Welfare Fund'); ?></td>
                                    <td><strong>$<?php echo number_format($don['amount'], 2); ?></strong></td>
                                    <td>One-time</td>
                                    <td><span class="badge green">Successful</span></td>
                                    <td>
                                        <a href="receipts.php" class="btn btn-outline-primary btn-tiny me-1"><i class="bi bi-file-earmark-pdf"></i> Receipt</a>
                                        <button class="btn btn-outline-secondary btn-tiny btn-details" data-id="<?php echo $don['donation_id']; ?>" data-amount="<?php echo $don['amount']; ?>" data-method="<?php echo $don['payment_method']; ?>" data-txn="<?php echo $don['transaction_id']; ?>" data-date="<?php echo $don['donation_date']; ?>"><i class="bi bi-eye"></i> Details</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pagination">
                <span>Showing <?php echo $offset + 1; ?>-<?php echo min($total_records, $offset + $limit); ?> of <?php echo $total_records; ?> donations</span>
                <div>
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>&status=<?php echo urlencode($status); ?>" class="btn tiny" style="text-decoration:none;">‹</a>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>&status=<?php echo urlencode($status); ?>" class="btn tiny <?php if ($i == $page) echo 'active'; ?>" style="text-decoration:none;"><?php echo $i; ?></a>
                    <?php endfor; ?>
                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>&status=<?php echo urlencode($status); ?>" class="btn tiny" style="text-decoration:none;">›</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.btn-details').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-id');
            const amount = btn.getAttribute('data-amount');
            const method = btn.getAttribute('data-method');
            const txn = btn.getAttribute('data-txn') || 'N/A';
            const date = btn.getAttribute('data-date');
            
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Donation Details',
                    html: `
                        <div class="text-start" style="font-size: 0.9rem;">
                            <p><strong>Donation ID:</strong> #DON-${id}</p>
                            <p><strong>Amount:</strong> $${amount}</p>
                            <p><strong>Payment Method:</strong> ${method}</p>
                            <p><strong>Transaction ID:</strong> ${txn}</p>
                            <p><strong>Date & Time:</strong> ${date}</p>
                        </div>
                    `,
                    icon: 'info',
                    confirmButtonColor: '#2b4c3f'
                });
            } else {
                alert(`Donation ID: #DON-${id}\nAmount: $${amount}\nMethod: ${method}\nTxn ID: ${txn}\nDate: ${date}`);
            }
        });
    });
});
</script>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
