<?php
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../config/database.php';

// Require caretaker login
require_login();
require_role('Caretaker');

$pdo = get_db_connection();
$caretaker_id = $_SESSION['user_id'] ?? 4; // Default to Radhika
$caretaker_name = $_SESSION['user_full_name'] ?? 'Radhika';

// Query counts
$total_residents = (int)$pdo->query("SELECT COUNT(*) FROM residents WHERE status = 'Active'")->fetchColumn();
$present_count = (int)$pdo->query("SELECT COUNT(*) FROM resident_attendance WHERE attendance_date = CURDATE() AND status = 'Present'")->fetchColumn();
$absent_count = (int)$pdo->query("SELECT COUNT(*) FROM resident_attendance WHERE attendance_date = CURDATE() AND status = 'Absent'")->fetchColumn();
$meals_count = (int)$pdo->query("SELECT COUNT(*) FROM meals WHERE DATE(created_at) = CURDATE()")->fetchColumn();
$special_care_count = (int)$pdo->query("SELECT COUNT(*) FROM special_care WHERE status = 'Active'")->fetchColumn();
$emergency_count = (int)$pdo->query("SELECT COUNT(*) FROM emergency_cases WHERE status = 'Active'")->fetchColumn();

// Query weekly attendance dataset (last 7 days)
$weekly_attendance = $pdo->query("
    SELECT attendance_date,
           SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) AS present_cnt,
           COUNT(*) AS total_cnt
    FROM resident_attendance
    WHERE attendance_date >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
    GROUP BY attendance_date
    ORDER BY attendance_date ASC
")->fetchAll();

// Query upcoming activities (today and future)
$upcoming_activities = $pdo->query("
    SELECT * FROM activities 
    WHERE activity_date >= CURDATE() 
    ORDER BY activity_date ASC, start_time ASC 
    LIMIT 4
")->fetchAll();

// Query recent notifications
$stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 4");
$stmt->execute([$caretaker_id]);
$notifications = $stmt->fetchAll();

// Query recent caretaker activity logs
$activity_logs = $pdo->query("
    SELECT al.*, u.full_name AS caretaker_name 
    FROM activity_logs al 
    LEFT JOIN users u ON al.user_id = u.id 
    ORDER BY al.created_at DESC LIMIT 5
")->fetchAll();

$base_path = '../../';
$page_title = 'Caretaker Dashboard | SevaNest';
$extra_css = [
    'assets/css/caretaker.css'
];

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'caretaker';
$currentPage   = 'dashboard.php';
$sn_asset_root = "../../assets";
$base_path = '../../';
include '../../includes/sidebar.php';
?>

<!-- ═══════════════════════════════════════════════════════════════════════
     MAIN CONTENT AREA
     ═══════════════════════════════════════════════════════════════════════ -->
<main id="sn-main-content" role="main" aria-label="Caretaker dashboard content">
    <div class="pages">
        
        <div class="page-head animate-fade-in">
          <div>
            <h2 class="page-title">Good morning, <?php echo sn_e($caretaker_name); ?> 🌿</h2>
            <p class="page-sub">Here's what's happening at SevaNest today.</p>
          </div>
          <div class="page-actions">
            <button class="btn ghost" id="exportReportBtn" onclick="window.print()">Export Report</button>
            <button class="btn primary" id="quickActionBtn" onclick="location.href='attendance.php'">+ Quick Action</button>
          </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid stats">
          <div class="stat-card gradient">
            <div class="stat-ico">👥</div>
            <div>
              <span>Today's Attendance</span>
              <h3><?php echo $present_count; ?> / <?php echo $total_residents; ?></h3>
              <small class="up">▲ <?php echo $total_residents > 0 ? round(($present_count / $total_residents) * 100) : 0; ?>% present</small>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-ico green">✅</div>
            <div><span>Residents Present</span><h3><?php echo $present_count; ?></h3><small class="up">▲ Live count</small></div>
          </div>
          <div class="stat-card">
            <div class="stat-ico red">⛔</div>
            <div><span>Residents Absent</span><h3><?php echo $absent_count; ?></h3><small class="down">▼ Absent / Leave</small></div>
          </div>
          <div class="stat-card">
            <div class="stat-ico gold">🍽</div>
            <div><span>Today's Meals</span><h3><?php echo $meals_count; ?> Served</h3><small>Recorded today</small></div>
          </div>
          <div class="stat-card">
            <div class="stat-ico pink">❤️</div>
            <div><span>Special Care</span><h3><?php echo $special_care_count; ?></h3><small>Under close monitoring</small></div>
          </div>
          <div class="stat-card">
            <div class="stat-ico alert">🚨</div>
            <div><span>Emergency Cases</span><h3><?php echo $emergency_count; ?></h3><small class="down">Active cases</small></div>
          </div>
        </div>

        <!-- Weekly & Activities Grid -->
        <div class="grid two-col">
          <div class="card">
            <div class="card-head">
              <h3>Weekly Attendance Overview</h3>
              <select class="select"><option>This Week</option></select>
            </div>
            <div class="chart-placeholder">
              <div class="bars">
                <?php foreach ($weekly_attendance as $day): ?>
                    <?php 
                        $pct = $day['total_cnt'] > 0 ? round(($day['present_cnt'] / $day['total_cnt']) * 100) : 0;
                    ?>
                    <span style="height:<?php echo $pct; ?>%" title="<?php echo date('d M', strtotime($day['attendance_date'])); ?>: <?php echo $pct; ?>% present"></span>
                <?php endforeach; ?>
              </div>
              <div class="chart-legend"><em>Last 7 days dynamic attendance rate</em></div>
            </div>
          </div>

          <div class="card">
            <div class="card-head">
              <h3>Upcoming Activities</h3>
              <a href="activities.php" class="link">View all</a>
            </div>
            <ul class="timeline">
              <?php if (empty($upcoming_activities)): ?>
                <li><span class="dot"></span><div><strong>No upcoming activities</strong><em>All activities completed.</em></div></li>
              <?php else: ?>
                <?php foreach ($upcoming_activities as $act): ?>
                    <li>
                        <span class="dot"></span>
                        <div><strong><?php echo sn_e($act['title']); ?></strong><em><?php echo date('h:i A', strtotime($act['start_time'])); ?> · <?php echo sn_e($act['location']); ?></em></div>
                    </li>
                <?php endforeach; ?>
              <?php endif; ?>
            </ul>
          </div>
        </div>

        <!-- Actions & Notifications Grid -->
        <div class="grid two-col">
          <div class="card">
            <div class="card-head"><h3>Quick Actions</h3></div>
            <div class="quick-grid">
              <a href="attendance.php" class="quick"><span>📝</span>Mark Attendance</a>
              <a href="meals.php" class="quick"><span>🍽</span>Add Meal</a>
              <a href="specialcare.php" class="quick"><span>💊</span>Medicine Log</a>
              <a href="emergency.php" class="quick"><span>🚨</span>Report Emergency</a>
              <a href="activities.php" class="quick"><span>📅</span>New Activity</a>
              <a href="specialcare.php" class="quick"><span>❤️</span>Special Care</a>
            </div>
          </div>

          <div class="card">
            <div class="card-head"><h3>Recent Notifications</h3><a class="link" href="#">Mark all read</a></div>
            <ul class="notif-list">
              <?php if (empty($notifications)): ?>
                <li><span class="dot alert"></span><div><strong>Emergency reported</strong> in Room 204 — Fall detected. <em>2m ago</em></div></li>
                <li><span class="dot gold"></span><div><strong>Medicine reminder</strong> for Mr. Verma at 2 PM. <em>15m ago</em></div></li>
                <li><span class="dot"></span><div><strong>Family visit</strong> scheduled for Mrs. Iyer at 5 PM. <em>1h ago</em></div></li>
                <li><span class="dot pink"></span><div><strong>Meal plan updated</strong> — Low salt diet for 4 residents. <em>3h ago</em></div></li>
              <?php else: ?>
                <?php foreach ($notifications as $notif): ?>
                    <?php
                        $notif_dot = 'dot';
                        if (stripos($notif['title'], 'emergency') !== false) $notif_dot = 'dot alert';
                        elseif (stripos($notif['title'], 'medicine') !== false) $notif_dot = 'dot gold';
                        elseif (stripos($notif['title'], 'meal') !== false) $notif_dot = 'dot pink';
                    ?>
                    <li><span class="<?php echo $notif_dot; ?>"></span><div><strong><?php echo sn_e($notif['title'] ?? ''); ?></strong> <?php echo sn_e($notif['message'] ?? ''); ?> <em><?php echo date('h:i A', strtotime($notif['created_at'] ?? 'now')); ?></em></div></li>
                <?php endforeach; ?>
              <?php endif; ?>
            </ul>
          </div>
        </div>

        <!-- Recent Activity Log -->
        <div class="card">
          <div class="card-head"><h3>Recent Activity Timeline</h3></div>
          <ul class="tl">
            <?php if (empty($activity_logs)): ?>
              <li><b>08:12</b> Radhika marked attendance for Wing A (24 residents).</li>
              <li><b>09:00</b> Breakfast served — nutrition summary logged.</li>
              <li><b>10:45</b> Dr. Nair completed weekly checkup for Mr. Kapoor.</li>
              <li><b>12:30</b> Lunch served with 3 special diet plates.</li>
              <li><b>14:05</b> Emergency drill completed in Wing B.</li>
            <?php else: ?>
                <?php foreach ($activity_logs as $log): ?>
                    <li><b><?php echo date('H:i', strtotime($log['created_at'])); ?></b> <?php echo sn_e($log['caretaker_name'] ?? 'System'); ?>: <?php echo sn_e($log['action']); ?> — <?php echo sn_e($log['description']); ?></li>
                <?php endforeach; ?>
            <?php endif; ?>
          </ul>
        </div>

    </div>
</main>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
