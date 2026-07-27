<?php
/**
 * SevaNest – Caretaker Resident Attendance
 * File     : modules/caretaker/attendance.php
 * Version  : 1.0
 */

require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../config/database.php';

// Require caretaker login
require_login();

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
          <div class="search inline"><span>🔎</span><input placeholder="Search resident by name or room…" id="attSearch"></div>
          <select class="select"><option>All Wings</option><option>Wing A</option><option>Wing B</option><option>Wing C</option></select>
          <select class="select"><option>All Status</option><option>Present</option><option>Absent</option><option>Late</option></select>
          <input type="date" class="select">
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
                <tr data-id="1">
                  <td>
                    <div class="res-cell">
                      <div class="res-photo">AK</div>
                      <div><b>Mr. Arun Kapoor</b><em>Age 78</em></div>
                    </div>
                  </td>
                  <td>204</td>
                  <td><span class="badge green">Present</span></td>
                  <td>08:15</td>
                  <td>—</td>
                  <td>
                    <div class="chip-group">
                      <button class="btn chip present on" data-set="present">Present</button>
                      <button class="btn chip absent " data-set="absent">Absent</button>
                      <button class="btn chip late " data-set="late">Late</button>
                    </div>
                  </td>
                </tr>
                <tr data-id="2">
                  <td>
                    <div class="res-cell">
                      <div class="res-photo">MI</div>
                      <div><b>Mrs. Meera Iyer</b><em>Age 72</em></div>
                    </div>
                  </td>
                  <td>118</td>
                  <td><span class="badge green">Present</span></td>
                  <td>08:15</td>
                  <td>—</td>
                  <td>
                    <div class="chip-group">
                      <button class="btn chip present on" data-set="present">Present</button>
                      <button class="btn chip absent " data-set="absent">Absent</button>
                      <button class="btn chip late " data-set="late">Late</button>
                    </div>
                  </td>
                </tr>
                <tr data-id="3">
                  <td>
                    <div class="res-cell">
                      <div class="res-photo">VV</div>
                      <div><b>Mr. Vijay Verma</b><em>Age 81</em></div>
                    </div>
                  </td>
                  <td>301</td>
                  <td><span class="badge green">Present</span></td>
                  <td>08:15</td>
                  <td>—</td>
                  <td>
                    <div class="chip-group">
                      <button class="btn chip present on" data-set="present">Present</button>
                      <button class="btn chip absent " data-set="absent">Absent</button>
                      <button class="btn chip late " data-set="late">Late</button>
                    </div>
                  </td>
                </tr>
                <tr data-id="4">
                  <td>
                    <div class="res-cell">
                      <div class="res-photo">LR</div>
                      <div><b>Mrs. Latha Rao</b><em>Age 75</em></div>
                    </div>
                  </td>
                  <td>302</td>
                  <td><span class="badge green">Present</span></td>
                  <td>08:15</td>
                  <td>—</td>
                  <td>
                    <div class="chip-group">
                      <button class="btn chip present on" data-set="present">Present</button>
                      <button class="btn chip absent " data-set="absent">Absent</button>
                      <button class="btn chip late " data-set="late">Late</button>
                    </div>
                  </td>
                </tr>
                <tr data-id="5">
                  <td>
                    <div class="res-cell">
                      <div class="res-photo">SN</div>
                      <div><b>Mr. Suresh Nair</b><em>Age 69</em></div>
                    </div>
                  </td>
                  <td>210</td>
                  <td><span class="badge green">Present</span></td>
                  <td>08:15</td>
                  <td>—</td>
                  <td>
                    <div class="chip-group">
                      <button class="btn chip present on" data-set="present">Present</button>
                      <button class="btn chip absent " data-set="absent">Absent</button>
                      <button class="btn chip late " data-set="late">Late</button>
                    </div>
                  </td>
                </tr>
                <tr data-id="6">
                  <td>
                    <div class="res-cell">
                      <div class="res-photo">KS</div>
                      <div><b>Mrs. Kamla Singh</b><em>Age 84</em></div>
                    </div>
                  </td>
                  <td>112</td>
                  <td><span class="badge green">Present</span></td>
                  <td>08:15</td>
                  <td>—</td>
                  <td>
                    <div class="chip-group">
                      <button class="btn chip present on" data-set="present">Present</button>
                      <button class="btn chip absent " data-set="absent">Absent</button>
                      <button class="btn chip late " data-set="late">Late</button>
                    </div>
                  </td>
                </tr>
                <tr data-id="7">
                  <td>
                    <div class="res-cell">
                      <div class="res-photo">RJ</div>
                      <div><b>Mr. Ramesh Joshi</b><em>Age 77</em></div>
                    </div>
                  </td>
                  <td>225</td>
                  <td><span class="badge green">Present</span></td>
                  <td>08:15</td>
                  <td>—</td>
                  <td>
                    <div class="chip-group">
                      <button class="btn chip present on" data-set="present">Present</button>
                      <button class="btn chip absent " data-set="absent">Absent</button>
                      <button class="btn chip late " data-set="late">Late</button>
                    </div>
                  </td>
                </tr>
                <tr data-id="8">
                  <td>
                    <div class="res-cell">
                      <div class="res-photo">AD</div>
                      <div><b>Mrs. Anita Desai</b><em>Age 80</em></div>
                    </div>
                  </td>
                  <td>130</td>
                  <td><span class="badge green">Present</span></td>
                  <td>08:15</td>
                  <td>—</td>
                  <td>
                    <div class="chip-group">
                      <button class="btn chip present on" data-set="present">Present</button>
                      <button class="btn chip absent " data-set="absent">Absent</button>
                      <button class="btn chip late " data-set="late">Late</button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="pagination">
            <span>Showing 1–8 of 52</span>
            <div><button class="btn tiny">‹</button><button class="btn tiny active">1</button><button class="btn tiny">2</button><button class="btn tiny">3</button><button class="btn tiny">›</button></div>
          </div>
        </div>

    </div>
</main>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
