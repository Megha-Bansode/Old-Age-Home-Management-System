<?php
/**
 * SevaNest – Caretaker Emergency Report
 * File     : modules/caretaker/emergency.php
 * Version  : 1.0
 */

require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../config/database.php';

// Require caretaker login
require_login();

$base_path = '../../';
$page_title = 'Emergency Report | SevaNest';
$extra_css = [
    'assets/css/caretaker.css'
];

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'caretaker';
$currentPage   = 'emergency.php';
$sn_asset_root = "../../assets";
include '../../includes/sidebar.php';
?>

<!-- ═══════════════════════════════════════════════════════════════════════
     MAIN CONTENT AREA
     ═══════════════════════════════════════════════════════════════════════ -->
<main id="sn-main-content" role="main" aria-label="Caretaker emergency report content">
    <div class="pages">
        
        <div class="page-head animate-fade-in">
          <div>
            <h2 class="page-title">Emergency Report</h2>
            <p class="page-sub">Log, track and respond to critical incidents in real time.</p>
          </div>
          <div class="page-actions">
            <button class="btn danger" id="reportEmergencyBtn">🚨 Report Emergency</button>
          </div>
        </div>

        <div class="grid quick-emg">
          <button class="emg-btn"><span>🚑</span>Medical</button>
          <button class="emg-btn"><span>🔥</span>Fire</button>
          <button class="emg-btn"><span>🩹</span>Injury</button>
          <button class="emg-btn"><span>💊</span>Medication</button>
          <button class="emg-btn"><span>🧠</span>Mental Health</button>
          <button class="emg-btn"><span>⚠️</span>Other</button>
        </div>

        <div class="grid two-col">
          <div class="card">
            <div class="card-head"><h3>New Emergency Report</h3></div>
            <form class="form" id="emergencyForm">
              <div class="row">
                <label>Emergency Type
                  <select name="type"><option>Medical</option><option>Fire</option><option>Injury</option><option>Other</option></select>
                </label>
                <label>Resident
                  <input name="resident" placeholder="Select resident">
                </label>
              </div>
              <div class="row">
                <label>Location <input name="location" placeholder="Room / Area"></label>
                <label>Reported Time <input type="datetime-local" name="time"></label>
              </div>
              <div class="row">
                <label>Severity
                  <select name="severity"><option>Low</option><option>Medium</option><option>High</option><option>Critical</option></select>
                </label>
                <label>Assigned Staff <input name="staff" placeholder="Caretaker / Doctor"></label>
              </div>
              <label>Description <textarea name="desc" rows="4" placeholder="Describe the situation…"></textarea></label>
              <div class="form-actions">
                <button class="btn ghost" type="reset">Reset</button>
                <button class="btn primary" type="submit">Submit Report</button>
              </div>
            </form>
          </div>

          <div class="card">
            <div class="card-head"><h3>Emergency Timeline</h3></div>
            <ul class="timeline dense">
              <li><span class="dot alert"></span><div><strong>Fall detected — Room 204</strong><em>Today · 14:05 · Critical</em></div></li>
              <li><span class="dot gold"></span><div><strong>Medication delay — Room 118</strong><em>Today · 11:20 · Medium</em></div></li>
              <li><span class="dot"></span><div><strong>Blood pressure spike</strong><em>Yesterday · 19:40 · High</em></div></li>
              <li><span class="dot pink"></span><div><strong>Minor injury during walk</strong><em>Yesterday · 07:15 · Low</em></div></li>
            </ul>
          </div>
        </div>

        <div class="card no-pad">
          <div class="card-head pad"><h3>Incident History</h3><a class="link">View all</a></div>
          <div class="table-wrap">
            <table class="tbl">
              <thead><tr><th>ID</th><th>Type</th><th>Resident</th><th>Location</th><th>Severity</th><th>Status</th><th>Time</th></tr></thead>
              <tbody>
                <tr><td>#EMG-1042</td><td>Medical</td><td>Mr. Kapoor</td><td>Room 204</td><td><span class="badge red">Critical</span></td><td><span class="badge amber">Active</span></td><td>14:05</td></tr>
                <tr><td>#EMG-1041</td><td>Medication</td><td>Mrs. Iyer</td><td>Room 118</td><td><span class="badge amber">Medium</span></td><td><span class="badge green">Resolved</span></td><td>11:20</td></tr>
                <tr><td>#EMG-1040</td><td>Injury</td><td>Mr. Verma</td><td>Garden</td><td><span class="badge">Low</span></td><td><span class="badge green">Resolved</span></td><td>Yesterday</td></tr>
                <tr><td>#EMG-1039</td><td>Medical</td><td>Mrs. Rao</td><td>Room 302</td><td><span class="badge red">High</span></td><td><span class="badge green">Resolved</span></td><td>2 days ago</td></tr>
              </tbody>
            </table>
          </div>
        </div>

    </div>
</main>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
