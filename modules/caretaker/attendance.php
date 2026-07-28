<?php
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../config/database.php';

// Require caretaker login
require_login();
require_role('Caretaker');

$pdo = get_db_connection();
$caretaker_id = $_SESSION['user_id'] ?? 4; // Default to Radhika

// Handle AJAX POST requests to toggle resident attendance
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'mark_attendance') {
    header('Content-Type: application/json');
    $resident_id = (int)($_POST['resident_id'] ?? 0);
    $status = trim($_POST['status'] ?? '');
    $date = trim($_POST['date'] ?? date('Y-m-d'));
    
    $status = ucfirst(strtolower($status));
    if (!in_array($status, ['Present', 'Absent', 'Late'])) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid status value.']);
        exit;
    }
    
    if ($resident_id > 0) {
        try {
            $stmt = $pdo->prepare("SELECT count(*) FROM resident_attendance WHERE resident_id = ? AND attendance_date = ?");
            $stmt->execute([$resident_id, $date]);
            $exists = $stmt->fetchColumn() > 0;
            
            $check_in = ($status === 'Present' || $status === 'Late') ? date('H:i:s') : null;
            if ($exists) {
                $stmt = $pdo->prepare("UPDATE resident_attendance SET status = ?, check_in = ? WHERE resident_id = ? AND attendance_date = ?");
                $stmt->execute([$status, $check_in, $resident_id, $date]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO resident_attendance (resident_id, attendance_date, check_in, status) VALUES (?, ?, ?, ?)");
                $stmt->execute([$resident_id, $date, $check_in, $status]);
            }
            
            // Add activity log
            $stmt_res = $pdo->prepare("SELECT full_name FROM residents WHERE resident_id = ?");
            $stmt_res->execute([$resident_id]);
            $res_name = $stmt_res->fetchColumn();
            
            $log_stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action, description) VALUES (?, 'Marked Attendance', ?)");
            $log_stmt->execute([$caretaker_id, "Marked {$res_name} as {$status} on {$date}"]);
            
            echo json_encode(['status' => 'success', 'message' => 'Attendance recorded successfully!']);
            exit;
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            exit;
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid resident ID.']);
        exit;
    }
}

// Fetch search and filters
$search = trim($_GET['search'] ?? '');
$wing = trim($_GET['wing'] ?? '');
$status_filter = trim($_GET['status'] ?? '');
$date_filter = trim($_GET['date'] ?? date('Y-m-d'));

$sql = "SELECT r.*, ra.status AS today_status, ra.check_in, ra.check_out 
        FROM residents r 
        LEFT JOIN resident_attendance ra ON r.resident_id = ra.resident_id AND ra.attendance_date = ? 
        WHERE r.status = 'Active'";
$params = [$date_filter];

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

if ($status_filter === 'Present') {
    $sql .= " AND ra.status = 'Present'";
} elseif ($status_filter === 'Absent') {
    $sql .= " AND (ra.status = 'Absent' OR ra.status IS NULL)";
} elseif ($status_filter === 'Late') {
    $sql .= " AND ra.status = 'Late'";
}

$sql .= " ORDER BY r.full_name ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$residents = $stmt->fetchAll();

$base_path = '../../';
$page_title = 'Resident Attendance | SevaNest';
$extra_css = [
    'assets/css/caretaker.css'
];

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'caretaker';
$currentPage   = 'attendance.php';
$sn_asset_root = "../../assets";
$base_path = '../../';
include '../../includes/sidebar.php';
?>

<!-- ═══════════════════════════════════════════════════════════════════════
     MAIN CONTENT AREA
     ═══════════════════════════════════════════════════════════════════════ -->
<main id="sn-main-content" role="main" aria-label="Caretaker attendance content">
    <div class="pages">
        
        <div class="page-head animate-fade-in">
          <div>
            <h2 class="page-title">Resident Attendance</h2>
            <p class="page-sub">Track daily presence, check-in and check-out times.</p>
          </div>
          <div class="page-actions">
            <button class="btn primary" id="addMemberBtn">+ Add Member</button>
          </div>
        </div>

        <div class="toolbar">
          <div class="search inline">
            <span>🔎</span>
            <input placeholder="Search resident by name or room…" id="attSearch" value="<?php echo sn_e($search); ?>" onkeyup="if(event.key === 'Enter') location.href = '?date=<?php echo $date_filter; ?>&wing=<?php echo $wing; ?>&status=<?php echo $status_filter; ?>&search=' + encodeURIComponent(this.value)">
          </div>
          <select class="select" id="wingSelect" onchange="location.href = '?date=<?php echo $date_filter; ?>&status=<?php echo $status_filter; ?>&search=<?php echo urlencode($search); ?>&wing=' + this.value">
            <option value="" <?php echo ($wing === '') ? 'selected' : ''; ?>>All Wings</option>
            <option value="Wing A" <?php echo ($wing === 'Wing A') ? 'selected' : ''; ?>>Wing A</option>
            <option value="Wing B" <?php echo ($wing === 'Wing B') ? 'selected' : ''; ?>>Wing B</option>
            <option value="Wing C" <?php echo ($wing === 'Wing C') ? 'selected' : ''; ?>>Wing C</option>
          </select>
          <select class="select" id="statusSelect" onchange="location.href = '?date=<?php echo $date_filter; ?>&wing=<?php echo $wing; ?>&search=<?php echo urlencode($search); ?>&status=' + this.value">
            <option value="" <?php echo ($status_filter === '') ? 'selected' : ''; ?>>All Status</option>
            <option value="Present" <?php echo ($status_filter === 'Present') ? 'selected' : ''; ?>>Present</option>
            <option value="Absent" <?php echo ($status_filter === 'Absent') ? 'selected' : ''; ?>>Absent</option>
            <option value="Late" <?php echo ($status_filter === 'Late') ? 'selected' : ''; ?>>Late</option>
          </select>
          <input type="date" class="select" id="dateInput" value="<?php echo sn_e($date_filter); ?>" onchange="location.href = '?wing=<?php echo $wing; ?>&status=<?php echo $status_filter; ?>&search=<?php echo urlencode($search); ?>&date=' + this.value">
        </div>

        <div class="card no-pad">
          <div class="table-wrap">
            <table class="tbl" id="attendanceTable">
              <thead>
                <tr>
                  <th>Resident</th><th>Room</th><th>Status</th>
                  <th>Check-in</th><th>Check-out</th><th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($residents)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">No residents found.</td></tr>
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
                            
                            $today_status = $res['today_status'] ?? 'Absent';
                            $badge_class = 'red';
                            if ($today_status === 'Present') $badge_class = 'green';
                            elseif ($today_status === 'Late') $badge_class = 'amber';
                        ?>
                        <tr data-id="<?php echo $res['resident_id']; ?>">
                          <td>
                            <div class="res-cell">
                              <div class="res-photo"><?php echo sn_e($initials); ?></div>
                              <div><b><?php echo sn_e($res['full_name']); ?></b><em>Age <?php echo $age; ?></em></div>
                            </div>
                          </td>
                          <td><?php echo sn_e($res['room_number']); ?></td>
                          <td><span class="badge <?php echo $badge_class; ?> status-badge"><?php echo sn_e($today_status); ?></span></td>
                          <td class="check-in-time"><?php echo $res['check_in'] ? date('H:i', strtotime($res['check_in'])) : '—'; ?></td>
                          <td><?php echo $res['check_out'] ? date('H:i', strtotime($res['check_out'])) : '—'; ?></td>
                          <td>
                            <div class="chip-group">
                              <button class="btn chip present <?php echo ($today_status === 'Present') ? 'on' : ''; ?>" data-set="present">Present</button>
                              <button class="btn chip absent <?php echo ($today_status === 'Absent') ? 'on' : ''; ?>" data-set="absent">Absent</button>
                              <button class="btn chip late <?php echo ($today_status === 'Late') ? 'on' : ''; ?>" data-set="late">Late</button>
                            </div>
                          </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
          <div class="pagination">
            <span>Showing <?php echo count($residents); ?> entries</span>
            <div><button class="btn tiny">‹</button><button class="btn tiny active">1</button><button class="btn tiny">›</button></div>
          </div>
        </div>

    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const table = document.getElementById('attendanceTable');
    const dateInput = document.getElementById('dateInput');
    
    if (table) {
        table.addEventListener('click', (e) => {
            const btn = e.target.closest('.chip');
            if (!btn) return;
            
            e.preventDefault();
            const row = btn.closest('tr');
            const residentId = row.getAttribute('data-id');
            const status = btn.getAttribute('data-set');
            const dateVal = dateInput ? dateInput.value : new Date().toISOString().slice(0, 10);
            
            // Highlight chip immediately
            const group = btn.closest('.chip-group');
            group.querySelectorAll('.chip').forEach(c => c.classList.remove('on'));
            btn.classList.add('on');
            
            // Send AJAX request
            const formData = new FormData();
            formData.append('action', 'mark_attendance');
            formData.append('resident_id', residentId);
            formData.append('status', status);
            formData.append('date', dateVal);
            
            fetch('attendance.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    // Update badge color
                    const badge = row.querySelector('.status-badge');
                    if (badge) {
                        badge.className = 'badge status-badge';
                        badge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
                        if (status === 'present') badge.classList.add('green');
                        elseif (status === 'absent') badge.classList.add('red');
                        elseif (status === 'late') badge.classList.add('amber');
                    }
                    
                    // Update check-in time
                    const checkInCell = row.querySelector('.check-in-time');
                    if (checkInCell) {
                        if (status === 'present' || status === 'late') {
                            const now = new Date();
                            checkInCell.textContent = String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');
                        } else {
                            checkInCell.textContent = '—';
                        }
                    }
                    
                    if (typeof Swal !== 'undefined') {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true
                        });
                        Toast.fire({
                            icon: 'success',
                            title: data.message
                        });
                    }
                } else {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message
                        });
                    } else {
                        alert('Error: ' + data.message);
                    }
                    // Reset chip highlight
                    location.reload();
                }
            })
            .catch(err => {
                console.error(err);
                location.reload();
            });
        });
    }
});
</script>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
