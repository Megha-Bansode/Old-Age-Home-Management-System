<?php
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../config/database.php';

// Require caretaker login
require_login();
require_role('Caretaker');

$pdo = get_db_connection();
$caretaker_id = $_SESSION['user_id'] ?? 4; // Default to Radhika
$caretaker_name = $_SESSION['user_full_name'] ?? 'Radhika S.';

$formSuccess = '';
$formError = '';

// Seed mock activities if empty
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM activities");
    if ($stmt->fetchColumn() == 0) {
        $mocks = [
            ['title' => 'Morning Yoga', 'date' => date('Y-m-d'), 'start' => '07:00:00', 'end' => '08:00:00', 'loc' => 'Garden Hall', 'desc' => '{"staff":"Radhika","residents":24,"status":"Ongoing","notes":"Garden hall — chair yoga variation."}'],
            ['title' => 'Breakfast Service', 'date' => date('Y-m-d'), 'start' => '08:00:00', 'end' => '09:00:00', 'loc' => 'Dining Room', 'desc' => '{"staff":"Anil","residents":48,"status":"Completed","notes":"Idli, oats & fruit juice served."}'],
            ['title' => 'Physiotherapy', 'date' => date('Y-m-d'), 'start' => '10:00:00', 'end' => '11:30:00', 'loc' => 'Physio Room', 'desc' => '{"staff":"Priya","residents":6,"status":"Scheduled","notes":"Individual sessions in Wing A."}'],
            ['title' => 'Health Checkup', 'date' => date('Y-m-d'), 'start' => '13:00:00', 'end' => '14:30:00', 'loc' => 'Clinic Room', 'desc' => '{"staff":"Dr. Nair","residents":12,"status":"Ongoing","notes":"Weekly vitals & BP monitoring."}'],
            ['title' => 'Reading Circle', 'date' => date('Y-m-d'), 'start' => '15:00:00', 'end' => '16:00:00', 'loc' => 'Library', 'desc' => '{"staff":"Radhika","residents":15,"status":"Scheduled","notes":"Regional literature session."}'],
            ['title' => 'Music Therapy', 'date' => date('Y-m-d'), 'start' => '16:30:00', 'end' => '17:30:00', 'loc' => 'Community Hall', 'desc' => '{"staff":"Priya","residents":18,"status":"Scheduled","notes":"Classical instrumental therapy."}'],
            ['title' => 'Evening Walk', 'date' => date('Y-m-d'), 'start' => '18:00:00', 'end' => '19:00:00', 'loc' => 'Garden Path', 'desc' => '{"staff":"Anil","residents":22,"status":"Scheduled","notes":"Guided walk in the garden."}'],
            ['title' => 'Prayer Session', 'date' => date('Y-m-d'), 'start' => '19:15:00', 'end' => '20:00:00', 'loc' => 'Prayer Hall', 'desc' => '{"staff":"Radhika","residents":30,"status":"Scheduled","notes":"Multi-faith prayer room."}'],
            ['title' => 'Movie Night', 'date' => date('Y-m-d'), 'start' => '20:30:00', 'end' => '22:00:00', 'loc' => 'Community Hall', 'desc' => '{"staff":"Priya","residents":20,"status":"Scheduled","notes":"Community hall — vintage films."}']
        ];
        foreach ($mocks as $m) {
            $stmt_ins = $pdo->prepare("INSERT INTO activities (title, activity_date, start_time, end_time, location, description) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt_ins->execute([$m['title'], $m['date'], $m['start'], $m['end'], $m['loc'], $m['desc']]);
        }
    }
} catch (Exception $e) {}

// Handle POST Requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $title = trim($_POST['title'] ?? '');
        $date = trim($_POST['date'] ?? date('Y-m-d'));
        $start = trim($_POST['start_time'] ?? '08:00:00');
        $end = trim($_POST['end_time'] ?? '09:00:00');
        $location = trim($_POST['location'] ?? 'Community Hall');
        $staff = trim($_POST['staff'] ?? $caretaker_name);
        $residents_cnt = (int)($_POST['residents'] ?? 10);
        $status = trim($_POST['status'] ?? 'Scheduled');
        $notes = trim($_POST['notes'] ?? '');
        
        if (!empty($title)) {
            try {
                $desc_json = json_encode([
                    'staff' => $staff,
                    'residents' => $residents_cnt,
                    'status' => $status,
                    'notes' => $notes
                ]);
                
                $stmt = $pdo->prepare("INSERT INTO activities (title, activity_date, start_time, end_time, location, description) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$title, $date, $start, $end, $location, $desc_json]);
                
                // Add activity log
                $log_stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action, description) VALUES (?, 'Scheduled Activity', ?)");
                $log_stmt->execute([$caretaker_id, "Scheduled new activity: {$title}"]);
                
                $formSuccess = "Activity scheduled successfully!";
            } catch (Exception $e) {
                $formError = "Failed to schedule activity: " . $e->getMessage();
            }
        } else {
            $formError = "Activity title is required.";
        }
    } elseif ($action === 'delete') {
        $activity_id = (int)($_POST['activity_id'] ?? 0);
        try {
            $stmt = $pdo->prepare("DELETE FROM activities WHERE activity_id = ?");
            $stmt->execute([$activity_id]);
            $formSuccess = "Activity deleted successfully!";
        } catch (Exception $e) {
            $formError = "Error deleting activity: " . $e->getMessage();
        }
    }
}

// Fetch activities for today
$activities = $pdo->query("SELECT * FROM activities WHERE activity_date = CURDATE() ORDER BY start_time ASC")->fetchAll();

$morning_acts = [];
$afternoon_acts = [];
$evening_acts = [];

foreach ($activities as $act) {
    $hour = (int)date('H', strtotime($act['start_time']));
    if ($hour < 12) {
        $morning_acts[] = $act;
    } elseif ($hour < 17) {
        $afternoon_acts[] = $act;
    } else {
        $evening_acts[] = $act;
    }
}

$base_path = '../../';
$page_title = 'Daily Activities | SevaNest';
$extra_css = [
    'assets/css/caretaker.css'
];

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'caretaker';
$currentPage   = 'activities.php';
$sn_asset_root = "../../assets";
$base_path = '../../';
include '../../includes/sidebar.php';
?>

<!-- ═══════════════════════════════════════════════════════════════════════
     MAIN CONTENT AREA
     ═══════════════════════════════════════════════════════════════════════ -->
<main id="sn-main-content" role="main" aria-label="Caretaker activities content">
    <div class="pages">
        
        <div class="page-head animate-fade-in">
          <div>
            <h2 class="page-title">Daily Activities</h2>
            <p class="page-sub">Plan and monitor engaging activities for residents.</p>
          </div>
          <div class="page-actions">
            <button class="btn ghost">📅 Calendar</button>
            <button class="btn primary" id="addActivityBtn">+ Add Activity</button>
          </div>
        </div>

        <div class="activity-block">
          <h3 class="sec-title">🌅 Morning Activities</h3>
          <div class="grid three-col" id="morningActs">
            <?php if (empty($morning_acts)): ?>
                <div class="card p-3 text-center text-muted w-100">No morning activities scheduled.</div>
            <?php else: ?>
                <?php foreach ($morning_acts as $act): ?>
                    <?php
                        $decoded = json_decode($act['description'], true);
                        $staff = $decoded['staff'] ?? 'Staff';
                        $residents = $decoded['residents'] ?? 0;
                        $status = $decoded['status'] ?? 'Scheduled';
                        $notes = $decoded['notes'] ?? $act['description'];
                        
                        $status_badge = 'blue';
                        if ($status === 'Ongoing') $status_badge = 'amber';
                        elseif ($status === 'Completed') $status_badge = 'green';
                    ?>
                    <div class="act-card">
                      <h4><?php echo sn_e($act['title']); ?></h4>
                      <div class="act-meta">
                        <span>👤 <?php echo sn_e($staff); ?></span>
                        <span>👥 <?php echo $residents; ?> residents</span>
                        <span><span class="badge <?php echo $status_badge; ?>"><?php echo sn_e($status); ?></span></span>
                      </div>
                      <div class="act-notes"><?php echo sn_e($notes); ?></div>
                      <div class="act-actions">
                        <form method="POST" action="activities.php" class="d-inline delete-activity-form">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="activity_id" value="<?php echo $act['activity_id']; ?>">
                            <button class="btn tiny btn-outline-danger" type="submit">Delete</button>
                        </form>
                      </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>

        <div class="activity-block">
          <h3 class="sec-title">☀️ Afternoon Activities</h3>
          <div class="grid three-col" id="afternoonActs">
            <?php if (empty($afternoon_acts)): ?>
                <div class="card p-3 text-center text-muted w-100">No afternoon activities scheduled.</div>
            <?php else: ?>
                <?php foreach ($afternoon_acts as $act): ?>
                    <?php
                        $decoded = json_decode($act['description'], true);
                        $staff = $decoded['staff'] ?? 'Staff';
                        $residents = $decoded['residents'] ?? 0;
                        $status = $decoded['status'] ?? 'Scheduled';
                        $notes = $decoded['notes'] ?? $act['description'];
                        
                        $status_badge = 'blue';
                        if ($status === 'Ongoing') $status_badge = 'amber';
                        elseif ($status === 'Completed') $status_badge = 'green';
                    ?>
                    <div class="act-card gold">
                      <h4><?php echo sn_e($act['title']); ?></h4>
                      <div class="act-meta">
                        <span>👤 <?php echo sn_e($staff); ?></span>
                        <span>👥 <?php echo $residents; ?> residents</span>
                        <span><span class="badge <?php echo $status_badge; ?>"><?php echo sn_e($status); ?></span></span>
                      </div>
                      <div class="act-notes"><?php echo sn_e($notes); ?></div>
                      <div class="act-actions">
                        <form method="POST" action="activities.php" class="d-inline delete-activity-form">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="activity_id" value="<?php echo $act['activity_id']; ?>">
                            <button class="btn tiny btn-outline-danger" type="submit">Delete</button>
                        </form>
                      </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>

        <div class="activity-block">
          <h3 class="sec-title">🌙 Evening Activities</h3>
          <div class="grid three-col" id="eveningActs">
            <?php if (empty($evening_acts)): ?>
                <div class="card p-3 text-center text-muted w-100">No evening activities scheduled.</div>
            <?php else: ?>
                <?php foreach ($evening_acts as $act): ?>
                    <?php
                        $decoded = json_decode($act['description'], true);
                        $staff = $decoded['staff'] ?? 'Staff';
                        $residents = $decoded['residents'] ?? 0;
                        $status = $decoded['status'] ?? 'Scheduled';
                        $notes = $decoded['notes'] ?? $act['description'];
                        
                        $status_badge = 'blue';
                        if ($status === 'Ongoing') $status_badge = 'amber';
                        elseif ($status === 'Completed') $status_badge = 'green';
                    ?>
                    <div class="act-card pink">
                      <h4><?php echo sn_e($act['title']); ?></h4>
                      <div class="act-meta">
                        <span>👤 <?php echo sn_e($staff); ?></span>
                        <span>👥 <?php echo $residents; ?> residents</span>
                        <span><span class="badge <?php echo $status_badge; ?>"><?php echo sn_e($status); ?></span></span>
                      </div>
                      <div class="act-notes"><?php echo sn_e($notes); ?></div>
                      <div class="act-actions">
                        <form method="POST" action="activities.php" class="d-inline delete-activity-form">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="activity_id" value="<?php echo $act['activity_id']; ?>">
                            <button class="btn tiny btn-outline-danger" type="submit">Delete</button>
                        </form>
                      </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>

        <!-- Monthly Calendar View -->
        <div class="card">
          <div class="card-head"><h3>Calendar</h3><em class="muted">Placeholder — connect to PHP calendar API</em></div>
          <div class="calendar-placeholder">
            <div class="cal-grid" id="calGrid">
              <div class="day" style="background:transparent;color:var(--muted);font-weight:700">S</div>
              <div class="day" style="background:transparent;color:var(--muted);font-weight:700">M</div>
              <div class="day" style="background:transparent;color:var(--muted);font-weight:700">T</div>
              <div class="day" style="background:transparent;color:var(--muted);font-weight:700">W</div>
              <div class="day" style="background:transparent;color:var(--muted);font-weight:700">T</div>
              <div class="day" style="background:transparent;color:var(--muted);font-weight:700">F</div>
              <div class="day" style="background:transparent;color:var(--muted);font-weight:700">S</div>
              <div class="day ">1</div><div class="day ">2</div><div class="day has">3</div><div class="day ">4</div>
              <div class="day ">5</div><div class="day ">6</div><div class="day has">7</div><div class="day ">8</div>
              <div class="day ">9</div><div class="day ">10</div><div class="day ">11</div><div class="day has">12</div>
              <div class="day ">13</div><div class="day ">14</div><div class="day ">15</div><div class="day ">16</div>
              <div class="day ">17</div><div class="day has">18</div><div class="day ">19</div><div class="day today">20</div>
              <div class="day ">21</div><div class="day ">22</div><div class="day ">23</div><div class="day has">24</div>
              <div class="day ">25</div><div class="day ">26</div><div class="day ">27</div><div class="day ">28</div>
              <div class="day ">29</div><div class="day ">30</div>
            </div>
          </div>
        </div>

    </div>
</main>

<!-- Modal to Add Activity -->
<div class="modal fade" id="activityModal" tabindex="-1" aria-labelledby="activityModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <form method="POST" action="activities.php">
                <input type="hidden" name="action" value="add">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="activityModalLabel">Schedule Daily Activity</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Activity Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control select" required style="width:100%; border: 1px solid var(--color-border); border-radius: var(--radius-medium); padding: 8px;" placeholder="e.g. Evening Walk">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Activity Date</label>
                        <input type="date" name="date" class="form-control select" value="<?php echo date('Y-m-d'); ?>" style="width:100%; border: 1px solid var(--color-border); border-radius: var(--radius-medium); padding: 8px;">
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold text-dark">Start Time</label>
                            <input type="time" name="start_time" class="form-control select" value="09:00" style="width:100%; border: 1px solid var(--color-border); border-radius: var(--radius-medium); padding: 8px;">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold text-dark">End Time</label>
                            <input type="time" name="end_time" class="form-control select" value="10:00" style="width:100%; border: 1px solid var(--color-border); border-radius: var(--radius-medium); padding: 8px;">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Location</label>
                        <input type="text" name="location" class="form-control select" placeholder="e.g. Garden Hall" style="width:100%; border: 1px solid var(--color-border); border-radius: var(--radius-medium); padding: 8px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Assigned Staff</label>
                        <input type="text" name="staff" class="form-control select" value="<?php echo sn_e($caretaker_name); ?>" style="width:100%; border: 1px solid var(--color-border); border-radius: var(--radius-medium); padding: 8px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Resident Count</label>
                        <input type="number" name="residents" class="form-control select" value="15" style="width:100%; border: 1px solid var(--color-border); border-radius: var(--radius-medium); padding: 8px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Status</label>
                        <select name="status" class="form-select select" style="width:100%; border: 1px solid var(--color-border); border-radius: var(--radius-medium); padding: 8px;">
                            <option value="Scheduled">Scheduled</option>
                            <option value="Ongoing">Ongoing</option>
                            <option value="Completed">Completed</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Activity Notes</label>
                        <textarea name="notes" class="form-control select" rows="2" style="width:100%; border: 1px solid var(--color-border); border-radius: var(--radius-medium); padding: 8px;" placeholder="e.g. Chair yoga variation..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-sm">Schedule Activity</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if ($formSuccess): ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: <?php echo json_encode($formSuccess); ?>,
                    confirmButtonColor: '#2b4c3f'
                });
            }
        });
    </script>
<?php endif; ?>
<?php if ($formError): ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: <?php echo json_encode($formError); ?>,
                    confirmButtonColor: '#2b4c3f'
                });
            }
        });
    </script>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const addActivityBtn = document.getElementById('addActivityBtn');
    if (addActivityBtn) {
        addActivityBtn.addEventListener('click', () => {
            const myModal = new bootstrap.Modal(document.getElementById('activityModal'));
            myModal.show();
        });
    }

    document.querySelectorAll('.delete-activity-form').forEach(form => {
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Delete scheduled activity?',
                    text: 'This action will remove the activity log.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            } else {
                if (confirm('Delete this activity entry?')) {
                    form.submit();
                }
            }
        });
    });
});
</script>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
