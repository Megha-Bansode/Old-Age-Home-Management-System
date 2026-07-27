<?php
/**
 * SevaNest – Caretaker Dashboard
 * File     : modules/caretaker/dashboard.php
 * Version  : 1.0
 */

require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../config/database.php';

// Require caretaker login
require_login();
require_role('Caretaker');

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
include '../../includes/sidebar.php';
?>

<!-- ═══════════════════════════════════════════════════════════════════════
     MAIN CONTENT AREA
     ═══════════════════════════════════════════════════════════════════════ -->
<main id="sn-main-content" role="main" aria-label="Caretaker dashboard content">
    <div class="pages">
        
        <div class="page-head animate-fade-in">
          <div>
            <h2 class="page-title">Good morning, Radhika 🌿</h2>
            <p class="page-sub">Here's what's happening at SevaNest today.</p>
          </div>
          <div class="page-actions">
            <button class="btn ghost" id="exportReportBtn">Export Report</button>
            <button class="btn primary" id="quickActionBtn">+ Quick Action</button>
          </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid stats">
          <div class="stat-card gradient">
            <div class="stat-ico">👥</div>
            <div>
              <span>Today's Attendance</span>
              <h3>48 / 52</h3>
              <small class="up">▲ 92% present</small>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-ico green">✅</div>
            <div><span>Residents Present</span><h3>48</h3><small class="up">▲ +3 vs yesterday</small></div>
          </div>
          <div class="stat-card">
            <div class="stat-ico red">⛔</div>
            <div><span>Residents Absent</span><h3>04</h3><small class="down">▼ 2 on medical leave</small></div>
          </div>
          <div class="stat-card">
            <div class="stat-ico gold">🍽</div>
            <div><span>Today's Meals</span><h3>4 Served</h3><small>Breakfast · Lunch · Snacks · Dinner</small></div>
          </div>
          <div class="stat-card">
            <div class="stat-ico pink">❤️</div>
            <div><span>Special Care</span><h3>12</h3><small>Under close monitoring</small></div>
          </div>
          <div class="stat-card">
            <div class="stat-ico alert">🚨</div>
            <div><span>Emergency Cases</span><h3>01</h3><small class="down">Active — Room 204</small></div>
          </div>
        </div>

        <!-- Weekly & Activities Grid -->
        <div class="grid two-col">
          <div class="card">
            <div class="card-head">
              <h3>Weekly Attendance Overview</h3>
              <select class="select"><option>This Week</option><option>Last Week</option><option>This Month</option></select>
            </div>
            <div class="chart-placeholder">
              <div class="bars">
                <span style="height:60%"></span><span style="height:75%"></span>
                <span style="height:85%"></span><span style="height:70%"></span>
                <span style="height:90%"></span><span style="height:80%"></span>
                <span style="height:65%"></span>
              </div>
              <div class="chart-legend"><em>Chart placeholder — connect via PHP/MySQL</em></div>
            </div>
          </div>

          <div class="card">
            <div class="card-head">
              <h3>Upcoming Activities</h3>
              <a href="activities.php" class="link">View all</a>
            </div>
            <ul class="timeline">
              <li><span class="dot"></span><div><strong>Morning Yoga</strong><em>7:00 AM · Garden Hall</em></div></li>
              <li><span class="dot gold"></span><div><strong>Health Checkup</strong><em>10:30 AM · Clinic Room</em></div></li>
              <li><span class="dot pink"></span><div><strong>Music Therapy</strong><em>3:00 PM · Community Hall</em></div></li>
              <li><span class="dot"></span><div><strong>Evening Prayer</strong><em>6:30 PM · Prayer Room</em></div></li>
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
            <div class="card-head"><h3>Recent Notifications</h3><a class="link">Mark all read</a></div>
            <ul class="notif-list">
              <li><span class="dot alert"></span><div><strong>Emergency reported</strong> in Room 204 — Fall detected. <em>2m ago</em></div></li>
              <li><span class="dot gold"></span><div><strong>Medicine reminder</strong> for Mr. Verma at 2 PM. <em>15m ago</em></div></li>
              <li><span class="dot"></span><div><strong>Family visit</strong> scheduled for Mrs. Iyer at 5 PM. <em>1h ago</em></div></li>
              <li><span class="dot pink"></span><div><strong>Meal plan updated</strong> — Low salt diet for 4 residents. <em>3h ago</em></div></li>
            </ul>
          </div>
        </div>

        <!-- Recent Activity Log -->
        <div class="card">
          <div class="card-head"><h3>Recent Activity Timeline</h3></div>
          <ul class="tl">
            <li><b>08:12</b> Radhika marked attendance for Wing A (24 residents).</li>
            <li><b>09:00</b> Breakfast served — nutrition summary logged.</li>
            <li><b>10:45</b> Dr. Nair completed weekly checkup for Mr. Kapoor.</li>
            <li><b>12:30</b> Lunch served with 3 special diet plates.</li>
            <li><b>14:05</b> Emergency drill completed in Wing B.</li>
          </ul>
        </div>

    </div>
</main>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
