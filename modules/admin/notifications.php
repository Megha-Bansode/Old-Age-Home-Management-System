<?php
/**
 * SevaNest — Notifications & Alerts Center
 * File     : modules/admin/notifications.php
 * Version  : 1.0
 */

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/functions.php';

// Require Admin login
require_login();
require_role(['Admin', 'Old Age Home Admin']);

$base_path = '../../';
$page_title = 'System Alerts | SevaNest';

// Database Connection
require_once __DIR__ . '/../../config/database.php';
$pdo = get_db_connection();

$user_id = $_SESSION['user_id'] ?? 2; // Default to Anita Verma (Admin)

$formSuccess = '';
$formError = '';

// Programmatically seed mock notifications if DB is empty
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ?");
    $stmt->execute([$user_id]);
    if ($stmt->fetchColumn() == 0) {
        $mocks = [
            ['[Emergency] Critical: Power Failure in Wing C Corridor', 'Wing C corridor lighting power was cut. Main generator kicked in, but maintenance team needs to inspect grid A-4.', 0],
            ['[Inventory] Inventory Warning: Vitamin C Stock Low', 'Pharmacy report: Vitamin C 500mg tablets quantity (120 tablets) is below reorder threshold (200). Please reorder.', 0],
            ['[Admission] New Intake Request Submitted', 'Guardian Ravi Devi submitted an admission intake file for Kamala Devi (Age 74) under Wing A.', 0],
            ['[Discharge] Discharge Clearance Scheduled', 'Ram Sharan (Room B-105) has completed medical and billing clearances. Ready for final handover at 05:00 PM.', 0],
            ['[Visitor] Visitor Verification Request', 'Checkout approval request received for Sunita Mehta visiting Harish Mehta in Wing B.', 1]
        ];
        
        foreach ($mocks as $m) {
            $stmt_ins = $pdo->prepare("INSERT INTO notifications (user_id, title, message, is_read, created_at) VALUES (?, ?, ?, ?, DATE_SUB(NOW(), INTERVAL ? MINUTE))");
            $interval = rand(5, 300);
            $stmt_ins->execute([$user_id, $m[0], $m[1], $m[2], $interval]);
        }
    }
} catch (Exception $e) {
    // Fail silently
}

// Type parsing helpers
if (!function_exists('get_notification_type')) {
    function get_notification_type($title) {
        if (preg_match('/^\[(.*?)\]/', $title, $matches)) {
            return $matches[1];
        }
        $title_lower = strtolower($title);
        if (strpos($title_lower, 'emergency') !== false || strpos($title_lower, 'critical') !== false) {
            return 'Emergency';
        }
        if (strpos($title_lower, 'inventory') !== false || strpos($title_lower, 'stock') !== false) {
            return 'Inventory';
        }
        if (strpos($title_lower, 'admission') !== false || strpos($title_lower, 'intake') !== false) {
            return 'Admission';
        }
        if (strpos($title_lower, 'discharge') !== false) {
            return 'Discharge';
        }
        if (strpos($title_lower, 'visitor') !== false) {
            return 'Visitor';
        }
        return 'System';
    }
}

if (!function_exists('clean_notification_title')) {
    function clean_notification_title($title) {
        return preg_replace('/^\[.*?\]\s*/', '', $title);
    }
}

// Fetch helper mapping logic
function fetch_notifications_list($pdo, $user_id, $filter = 'All') {
    $query_str = "SELECT * FROM notifications WHERE user_id = ?";
    $params = [$user_id];
    
    if ($filter !== 'All') {
        $query_str .= " AND title LIKE ?";
        $params[] = "%[" . $filter . "]%";
    }
    
    $query_str .= " ORDER BY notification_id DESC";
    $stmt = $pdo->prepare($query_str);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();
    
    $notifs = [];
    foreach ($rows as $row) {
        $type = get_notification_type($row['title']);
        $cleaned_title = clean_notification_title($row['title']);
        
        $diff = time() - strtotime($row['created_at']);
        if ($diff < 60) {
            $time_str = 'Just now';
        } elseif ($diff < 3600) {
            $time_str = round($diff / 60) . ' minutes ago';
        } elseif ($diff < 86400) {
            $time_str = round($diff / 3600) . ' hours ago';
        } else {
            $time_str = round($diff / 86400) . ' days ago';
        }

        $notifs[] = [
            'id' => $row['notification_id'],
            'type' => $type,
            'title' => $cleaned_title,
            'desc' => $row['message'],
            'time' => $time_str,
            'read' => (bool)$row['is_read']
        ];
    }
    return $notifs;
}

// Handle POST actions (Mark all read, Mark single read)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'mark_all_read';
    $is_ajax = (isset($_POST['ajax']) && $_POST['ajax'] == '1');

    if ($action === 'mark_all_read') {
        try {
            $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
            $stmt->execute([$user_id]);
            
            $msg = 'All notifications marked as read!';
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
    } elseif ($action === 'mark_read') {
        $notif_id = (int)($_POST['id'] ?? 0);
        try {
            $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE notification_id = ? AND user_id = ?");
            $stmt->execute([$notif_id, $user_id]);
            
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Notification marked as read!']);
            exit;
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            exit;
        }
    }
}

// Handle AJAX Fetch Request
if (isset($_GET['action']) && $_GET['action'] === 'fetch') {
    $filter = trim($_GET['filter'] ?? 'All');
    $notifications = fetch_notifications_list($pdo, $user_id, $filter);

    ob_start();
    if (empty($notifications)) {
        ?>
        <div class="text-center py-5" id="noAlertsMsg">
            <i class="bi bi-bell-slash-fill text-muted display-3"></i>
            <p class="mt-3 fw-bold text-dark fs-5">No notifications found</p>
            <p class="text-muted small">You are all caught up!</p>
        </div>
        <?php
    } else {
        foreach ($notifications as $n) {
            $borderCls = 'border-primary';
            $bgCls = 'bg-primary-subtle';
            $icon = 'bi-bell-fill';
            
            if ($n['type'] === 'Emergency') {
                $borderCls = 'border-danger';
                $bgCls = 'bg-danger-subtle';
                $icon = 'bi-exclamation-triangle-fill';
            } elseif ($n['type'] === 'Inventory') {
                $borderCls = 'border-warning';
                $bgCls = 'bg-warning-subtle';
                $icon = 'bi-box-seam-fill';
            } elseif ($n['type'] === 'Discharge') {
                $borderCls = 'border-secondary';
                $bgCls = 'bg-secondary-subtle';
                $icon = 'bi-box-arrow-right';
            } elseif ($n['type'] === 'Visitor') {
                $borderCls = 'border-info';
                $bgCls = 'bg-info-subtle';
                $icon = 'bi-person-badge-fill';
            }
            ?>
            <div class="card border-0 border-start border-4 <?php echo $borderCls; ?> shadow-sm rounded-3 p-3 bg-white notif-card <?php echo ($n['read'] ?? false) ? 'opacity-75' : ''; ?>" data-type="<?php echo sn_e($n['type'] ?? ''); ?>">
                <div class="d-flex align-items-start justify-content-between gap-3">
                    <div class="d-flex align-items-start gap-3">
                        <div class="p-2 rounded-3 text-dark <?php echo $bgCls; ?>" style="margin-top: 2px;">
                            <i class="bi <?php echo $icon; ?> fs-5"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1 text-dark"><?php echo sn_e($n['title']); ?></h6>
                            <p class="text-muted mb-0 small"><?php echo sn_e($n['desc'] ?? $n['message'] ?? ''); ?></p>
                            <small class="text-muted d-block mt-2 font-monospace" style="font-size: 0.7rem;"><i class="bi bi-clock me-1"></i><?php echo sn_e($n['time'] ?? $n['created_at'] ?? ''); ?></small>
                        </div>
                    </div>
                    <?php if (!$n['read']): ?>
                        <button class="btn btn-sm btn-light text-primary btn-mark-read" title="Mark as Read" data-id="<?php echo (int)$n['id']; ?>">
                            <i class="bi bi-check2"></i>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
            <?php
        }
    }
    $html = ob_get_clean();

    header('Content-Type: application/json');
    echo json_encode([
        'html' => $html,
        'count' => count($notifications)
    ]);
    exit;
}

// Initial fetch for page load
$notifications = fetch_notifications_list($pdo, $user_id);

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'admin';
$currentPage   = 'notifications.php';
$sn_asset_root = "../../assets";
$base_path = '../../'; // Ensure correct path prefix
include '../../includes/sidebar.php';
?>

<main id="sn-main-content" role="main" aria-label="Notifications Content" class="p-4 flex-grow-1">
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
                <h3 class="fw-bold mb-0 text-dark">Alerts &amp; Notifications</h3>
                <small class="text-muted">System log of operations, emergencies, intakes, and stock checklists</small>
            </div>
            <form method="POST" action="notifications.php" class="mark-all-read-form">
                <button type="submit" class="btn btn-outline-primary fw-semibold btn-sm">
                    <i class="bi bi-check2-all me-1"></i> Mark All as Read
                </button>
            </form>
        </div>

        <!-- Filter tabs -->
        <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
            <div class="card-body p-2">
                <div class="d-flex flex-wrap gap-1" id="notifFilters">
                    <button class="btn btn-sm btn-primary active px-3" data-filter="All">All Alerts</button>
                    <button class="btn btn-sm btn-outline-danger px-3" data-filter="Emergency">Emergencies</button>
                    <button class="btn btn-sm btn-outline-warning px-3" data-filter="Inventory">Inventory</button>
                    <button class="btn btn-sm btn-outline-primary px-3" data-filter="Admission">Admissions</button>
                    <button class="btn btn-sm btn-outline-info px-3" data-filter="Visitor">Visitors</button>
                </div>
            </div>
        </div>

        <!-- Notifications Stack -->
        <div class="d-flex flex-column gap-3" id="notificationsStack">
            <?php foreach ($notifications as $n): ?>
                <?php 
                    $borderCls = 'border-primary';
                    $bgCls = 'bg-primary-subtle';
                    $icon = 'bi-bell-fill';
                    
                    $n_type = $n['type'] ?? '';
                    if (empty($n_type)) {
                        if (stripos($n['title'], 'emergency') !== false) {
                            $n_type = 'Emergency';
                        } elseif (stripos($n['title'], 'inventory') !== false) {
                            $n_type = 'Inventory';
                        } elseif (stripos($n['title'], 'discharge') !== false) {
                            $n_type = 'Discharge';
                        } elseif (stripos($n['title'], 'visitor') !== false) {
                            $n_type = 'Visitor';
                        } else {
                            $n_type = 'System';
                        }
                    }

                    if ($n_type === 'Emergency') {
                        $borderCls = 'border-danger';
                        $bgCls = 'bg-danger-subtle';
                        $icon = 'bi-exclamation-triangle-fill';
                    } elseif ($n_type === 'Inventory') {
                        $borderCls = 'border-warning';
                        $bgCls = 'bg-warning-subtle';
                        $icon = 'bi-box-seam-fill';
                    } elseif ($n_type === 'Discharge') {
                        $borderCls = 'border-secondary';
                        $bgCls = 'bg-secondary-subtle';
                        $icon = 'bi-box-arrow-right';
                    } elseif ($n_type === 'Visitor') {
                        $borderCls = 'border-info';
                        $bgCls = 'bg-info-subtle';
                        $icon = 'bi-person-badge-fill';
                    }
                ?>
                <div class="card border-0 border-start border-4 <?php echo $borderCls; ?> shadow-sm rounded-3 p-3 bg-white notif-card <?php echo ($n['read'] ?? false) ? 'opacity-75' : ''; ?>" data-type="<?php echo sn_e($n['type'] ?? ''); ?>">
                    <div class="d-flex align-items-start justify-content-between gap-3">
                        <div class="d-flex align-items-start gap-3">
                            <div class="p-2 rounded-3 text-dark <?php echo $bgCls; ?>" style="margin-top: 2px;">
                                <i class="bi <?php echo $icon; ?> fs-5"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1 text-dark"><?php echo sn_e($n['title']); ?></h6>
                                <p class="text-muted mb-0 small"><?php echo sn_e($n['desc'] ?? $n['message'] ?? ''); ?></p>
                                <small class="text-muted d-block mt-2 font-monospace" style="font-size: 0.7rem;"><i class="bi bi-clock me-1"></i><?php echo sn_e($n['time'] ?? $n['created_at'] ?? ''); ?></small>
                            </div>
                        </div>
                        <?php if (!($n['read'] ?? false)): ?>
                            <button class="btn btn-sm btn-light text-primary btn-mark-read" title="Mark as Read" data-id="<?php echo (int)($n['id'] ?? $n['notification_id'] ?? 0); ?>">
                                <i class="bi bi-check2"></i>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Empty Alert State -->
        <div class="text-center py-5" id="noAlertsMsg" style="display: none;">
            <i class="bi bi-bell-slash-fill text-muted display-3"></i>
            <p class="mt-3 fw-bold text-dark fs-5">No notifications found</p>
            <p class="text-muted small">You are all caught up!</p>
        </div>

    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const filterButtons = document.querySelectorAll('#notifFilters button');
    const stack = document.getElementById('notificationsStack');

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

    function getActiveFilter() {
        const activeBtn = document.querySelector('#notifFilters button.active');
        return activeBtn ? activeBtn.getAttribute('data-filter') : 'All';
    }

    function loadNotifications() {
        const filterVal = getActiveFilter();
        
        fetch(`notifications.php?action=fetch&filter=${encodeURIComponent(filterVal)}`)
        .then(res => res.json())
        .then(data => {
            stack.innerHTML = data.html;
        })
        .catch(err => {
            console.error('Error loading notifications:', err);
        });
    }

    filterButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            filterButtons.forEach(b => {
                b.classList.remove('active', 'btn-primary');
                b.classList.add('btn-outline-primary', 'btn-outline-danger', 'btn-outline-warning', 'btn-outline-info');
            });
            
            btn.classList.add('active', 'btn-primary');
            btn.classList.remove('btn-outline-primary', 'btn-outline-danger', 'btn-outline-warning', 'btn-outline-info');
            
            loadNotifications();
        });
    });

    // Delegate Mark as Read click
    stack.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-mark-read');
        if (btn) {
            const id = btn.getAttribute('data-id');
            const formData = new FormData();
            formData.append('action', 'mark_read');
            formData.append('id', id);
            formData.append('ajax', '1');

            fetch('notifications.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    loadNotifications();
                } else {
                    showAlert('danger', data.message);
                }
            })
            .catch(err => {
                showAlert('danger', 'An error occurred while marking notification as read.');
            });
        }
    });

    // Mark all as read form submit intercept
    const markAllForm = document.querySelector('form.mark-all-read-form');
    if (markAllForm) {
        markAllForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData();
            formData.append('action', 'mark_all_read');
            formData.append('ajax', '1');

            fetch('notifications.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    loadNotifications();
                } else {
                    showAlert('danger', data.message);
                }
            })
            .catch(err => {
                showAlert('danger', 'An error occurred while marking all notifications as read.');
            });
        });
    }
});
</script>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
