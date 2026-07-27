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

// Handle Clear Notifications
$formSuccess = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formSuccess = 'All notifications marked as read!';
}

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'admin';
$currentPage   = 'notifications.php';
$sn_asset_root = "../../assets";
include '../../includes/sidebar.php';

// Mock Notifications list
$notifications = [
    [
        'id' => 'ALRT-101',
        'type' => 'Emergency',
        'title' => 'Critical: Power Failure in Wing C Corridor',
        'desc' => 'Wing C corridor lighting power was cut. Main generator kicked in, but maintenance team needs to inspect grid A-4.',
        'time' => '5 minutes ago',
        'read' => false
    ],
    [
        'id' => 'ALRT-102',
        'type' => 'Inventory',
        'title' => 'Inventory Warning: Vitamin C Stock Low',
        'desc' => 'Pharmacy report: Vitamin C 500mg tablets quantity (120 tablets) is below reorder threshold (200). Please reorder.',
        'time' => '10 minutes ago',
        'read' => false
    ],
    [
        'id' => 'ALRT-103',
        'type' => 'Admission',
        'title' => 'New Intake Request Submitted',
        'desc' => 'Guardian Ravi Devi submitted an admission intake file for Kamala Devi (Age 74) under Wing A.',
        'time' => '1 hour ago',
        'read' => false
    ],
    [
        'id' => 'ALRT-104',
        'type' => 'Discharge',
        'title' => 'Discharge Clearance Scheduled',
        'desc' => 'Ram Sharan (Room B-105) has completed medical and billing clearances. Ready for final handover at 05:00 PM.',
        'time' => '3 hours ago',
        'read' => false
    ],
    [
        'id' => 'ALRT-105',
        'type' => 'Visitor',
        'title' => 'Visitor Verification Request',
        'desc' => 'Checkout approval request received for Sunita Mehta visiting Harish Mehta in Wing B.',
        'time' => '4 hours ago',
        'read' => true
    ]
];
?>

<main id="sn-main-content" role="main" aria-label="Notifications Content" class="p-4 flex-grow-1">
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
                <h3 class="fw-bold mb-0 text-dark">Alerts &amp; Notifications</h3>
                <small class="text-muted">System log of operations, emergencies, intakes, and stock checklists</small>
            </div>
            <form method="POST" action="notifications.php">
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
                <div class="card border-0 border-start border-4 <?php echo $borderCls; ?> shadow-sm rounded-3 p-3 bg-white notif-card <?php echo $n['read'] ? 'opacity-75' : ''; ?>" data-type="<?php echo sn_e($n['type']); ?>">
                    <div class="d-flex align-items-start justify-content-between gap-3">
                        <div class="d-flex align-items-start gap-3">
                            <div class="p-2 rounded-3 text-dark <?php echo $bgCls; ?>" style="margin-top: 2px;">
                                <i class="bi <?php echo $icon; ?> fs-5"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1 text-dark"><?php echo sn_e($n['title']); ?></h6>
                                <p class="text-muted mb-0 small"><?php echo sn_e($n['desc']); ?></p>
                                <small class="text-muted d-block mt-2 font-monospace" style="font-size: 0.7rem;"><i class="bi bi-clock me-1"></i><?php echo sn_e($n['time']); ?></small>
                            </div>
                        </div>
                        <?php if (!$n['read']): ?>
                            <button class="btn btn-sm btn-light text-primary" title="Mark as Read">
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
    const cards = document.querySelectorAll('.notif-card');
    const noAlertsMsg = document.getElementById('noAlertsMsg');

    filterButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            filterButtons.forEach(b => b.classList.remove('active', 'btn-primary'));
            filterButtons.forEach(b => b.classList.add('btn-outline-primary', 'btn-outline-danger', 'btn-outline-warning', 'btn-outline-info'));
            
            // Reapply proper outline coloring
            btn.classList.add('active', 'btn-primary');
            btn.classList.remove('btn-outline-primary', 'btn-outline-danger', 'btn-outline-warning', 'btn-outline-info');
            
            const filterVal = btn.getAttribute('data-filter');
            let visibleCount = 0;

            cards.forEach(card => {
                const type = card.getAttribute('data-type');
                if (filterVal === 'All' || type === filterVal) {
                    card.style.display = '';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            if (noAlertsMsg) {
                noAlertsMsg.style.display = (visibleCount === 0) ? 'block' : 'none';
            }
        });
    });
});
</script>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
