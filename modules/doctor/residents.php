<?php
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../config/database.php';

// Require Doctor login
require_login();
require_role('Doctor');

$pdo = get_db_connection();
$user_id = $_SESSION['user_id'] ?? 3; // Default to Dr. Priya Nair

$search = trim($_GET['search'] ?? '');
$wing = trim($_GET['wing'] ?? '');
$health_status = trim($_GET['status'] ?? '');
$doctor = trim($_GET['doctor'] ?? '');

$sql = "SELECT r.* FROM residents r WHERE r.status = 'Active'";
$params = [];

if ($search !== '') {
    $sql .= " AND (r.full_name LIKE ? OR r.room_number LIKE ?)";
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
}

if ($wing === 'Wing A') {
    $sql .= " AND (r.room_number LIKE '1%' OR r.room_number = '204')";
} elseif ($wing === 'Wing B') {
    $sql .= " AND (r.room_number LIKE '2%' AND r.room_number != '204')";
} elseif ($wing === 'Wing C') {
    $sql .= " AND r.room_number LIKE '3%'";
}

// Map health status filter (Stable / Monitoring / Critical)
// We can check the special_care or basic text from residents or room assignment.
// Wait! Let's check residents table columns: it has medical_conditions.
if ($health_status === 'Stable') {
    $sql .= " AND (r.medical_conditions NOT LIKE '%critical%' AND r.medical_conditions NOT LIKE '%monitoring%' OR r.medical_conditions IS NULL)";
} elseif ($health_status === 'Monitoring') {
    $sql .= " AND r.medical_conditions LIKE '%monitoring%'";
} elseif ($health_status === 'Critical') {
    $sql .= " AND r.medical_conditions LIKE '%critical%'";
}

if ($doctor !== '' && $doctor !== 'All Assigned Doctors') {
    $sql .= " AND r.resident_id IN (SELECT DISTINCT resident_id FROM appointments a JOIN users u ON a.doctor_id = u.id WHERE u.full_name LIKE ?)";
    $params[] = '%' . $doctor . '%';
}

$sql .= " ORDER BY r.full_name ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$residents = $stmt->fetchAll();

// Helper to get assigned doctor name
if (!function_exists('get_assigned_doctor')) {
    function get_assigned_doctor($resident_id, $pdo) {
        $stmt = $pdo->prepare("SELECT u.full_name FROM appointments a JOIN users u ON a.doctor_id = u.id WHERE a.resident_id = ? ORDER BY a.appointment_date DESC LIMIT 1");
        $stmt->execute([$resident_id]);
        return $stmt->fetchColumn() ?: 'Dr. Robert Watson'; // Fallback default
    }
}

$base_path = '../../';
$page_title = 'Residents | SevaNest';
$extra_css = [
    'assets/css/doctor.css'
];

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'doctor';
$currentPage   = 'residents.php';
$sn_asset_root = "../../assets";
$base_path = '../../';
include '../../includes/sidebar.php';
?>

<!-- ═══════════════════════════════════════════════════════════════════════
     MAIN CONTENT AREA
     ═══════════════════════════════════════════════════════════════════════ -->
<main id="sn-main-content" role="main" aria-label="Doctor residents content">
    <div class="doctor-page-wrapper">
        
        <!-- Header Strip -->
        <div class="dr-header-strip animate-fade-in">
            <div>
                <h2 class="dr-header-strip__title">Resident Patient Profiles</h2>
                <p style="color: var(--color-text-muted-team); margin: 4px 0 0;">View health status updates and medical profiles for all residents.</p>
            </div>
        </div>

        <!-- Filter Toolbar -->
        <div class="toolbar">
            <div class="search inline">
                <span>🔎</span>
                <input placeholder="Search patient by name or room..." id="attSearch">
            </div>
            <select class="select">
                <option>All Wings</option>
                <option>Wing A</option>
                <option>Wing B</option>
                <option>Wing C</option>
            </select>
            <select class="select">
                <option>All Health Statuses</option>
                <option>Stable</option>
                <option>Monitoring</option>
                <option>Critical</option>
            </select>
            <select class="select">
                <option>All Assigned Doctors</option>
                <option>Dr. Watson</option>
                <option>Dr. Nair</option>
                <option>Dr. Kulkarni</option>
            </select>
        </div>

        <!-- Resident Grid (using statistical/profile card styles) -->
        <div class="grid three-col animate-fade-in">
            <?php if (empty($residents)): ?>
                <div class="card p-4 text-center text-muted w-100">No active resident profiles found matching filters.</div>
            <?php else: ?>
                <?php foreach ($residents as $res): ?>
                    <?php
                        $initials = '';
                        $parts = explode(' ', $res['full_name']);
                        foreach ($parts as $p) {
                            $initials .= strtoupper(substr($p, 0, 1));
                        }
                        $initials = substr($initials, 0, 2);
                        
                        // Calculate Age
                        $dob = new DateTime($res['date_of_birth']);
                        $now = new DateTime();
                        $age = $now->diff($dob)->y;
                        
                        // Wing deduction
                        $room = $res['room_number'] ?? '';
                        $wing_name = 'Wing A';
                        if ($room === '204') $wing_name = 'Wing A';
                        elseif (strpos($room, '1') === 0) $wing_name = 'Wing A';
                        elseif (strpos($room, '2') === 0) $wing_name = 'Wing B';
                        elseif (strpos($room, '3') === 0) $wing_name = 'Wing C';
                        
                        // Health status badge
                        $cond = strtolower($res['medical_conditions'] ?? '');
                        $badge_class = 'green';
                        $status_label = 'Stable';
                        if (strpos($cond, 'critical') !== false) {
                            $badge_class = 'red';
                            $status_label = 'Critical Recovery';
                        } elseif (strpos($cond, 'monitoring') !== false) {
                            $badge_class = 'blue';
                            $status_label = 'Monitoring';
                        }
                        
                        $assigned_dr = get_assigned_doctor($res['resident_id'], $pdo);
                    ?>
                    <!-- Card -->
                    <div class="card d-flex flex-column gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="res-photo" style="width: 48px; height: 48px; font-size: 1.1rem;"><?php echo sn_e($initials); ?></div>
                            <div>
                                <h4 style="margin: 0; color: var(--color-text); font-weight: 700;"><?php echo sn_e($res['full_name']); ?></h4>
                                <span style="font-size: var(--font-size-xs); color: var(--color-text-muted-team);">Age: <?php echo $age; ?> · Room <?php echo sn_e($room); ?> (<?php echo $wing_name; ?>)</span>
                            </div>
                        </div>
                        <hr style="margin: 0; border-top: 1px solid var(--color-border);">
                        <div>
                            <span style="font-size: var(--font-size-xs); color: var(--color-text-muted-team); display: block; margin-bottom: 2px;">Assigned Doctor:</span>
                            <strong style="font-size: var(--font-size-sm); color: var(--color-text);"><?php echo sn_e($assigned_dr); ?></strong>
                        </div>
                        <div>
                            <span style="font-size: var(--font-size-xs); color: var(--color-text-muted-team); display: block; margin-bottom: 4px;">Health Status:</span>
                            <span class="badge <?php echo $badge_class; ?>"><i class="bi bi-circle-fill me-1" style="font-size: 6px;"></i> <?php echo $status_label; ?></span>
                        </div>
                        <div class="d-flex gap-2 mt-2">
                            <a href="medical_records.php?id=<?php echo $res['resident_id']; ?>" class="btn btn-outline-primary btn-tiny w-100"><i class="bi bi-file-earmark-medical me-1"></i> Medical History</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>
</main>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
