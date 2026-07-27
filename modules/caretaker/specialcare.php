<?php
/**
 * SevaNest – Caretaker Special Care
 * File     : modules/caretaker/specialcare.php
 * Version  : 1.0
 */

require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../config/database.php';

// Require caretaker login
require_login();

$base_path = '../../';
$page_title = 'Special Care | SevaNest';
$extra_css = [
    'assets/css/caretaker.css'
];

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'caretaker';
$currentPage   = 'specialcare.php';
$sn_asset_root = "../../assets";
include '../../includes/sidebar.php';
?>

<!-- ═══════════════════════════════════════════════════════════════════════
     MAIN CONTENT AREA
     ═══════════════════════════════════════════════════════════════════════ -->
<main id="sn-main-content" role="main" aria-label="Caretaker special care content">
    <div class="pages">
        
        <div class="page-head animate-fade-in">
          <div>
            <h2 class="page-title">Special Care</h2>
            <p class="page-sub">Residents requiring close attention and medical priority.</p>
          </div>
          <div class="page-actions">
            <button class="btn primary" id="addCaseBtn">+ Add Case</button>
          </div>
        </div>

        <div class="toolbar">
          <div class="search inline"><span>🔎</span><input placeholder="Search by name or condition…"></div>
          <select class="select"><option>All Priorities</option><option>High</option><option>Medium</option><option>Low</option></select>
          <select class="select"><option>All Caretakers</option><option>Radhika</option><option>Anil</option><option>Priya</option></select>
        </div>

        <div class="card no-pad">
          <div class="table-wrap">
            <table class="tbl" id="specialTable">
              <thead>
                <tr>
                  <th>Resident</th><th>Age</th><th>Room</th><th>Condition</th>
                  <th>Care Instructions</th><th>Caretaker</th><th>Medicine</th>
                  <th>Priority</th><th>Status</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>
                    <div class="res-cell">
                      <div class="res-photo">MA</div>
                      <div><b>Mr. Arun Kapoor</b><em>Room 204</em></div>
                    </div>
                  </td>
                  <td>78</td>
                  <td>204</td>
                  <td>Post-fall recovery</td>
                  <td>Assisted mobility, 2h checks</td>
                  <td>Radhika</td>
                  <td>8AM · 2PM · 8PM</td>
                  <td><span class="badge red">High</span></td>
                  <td><span class="badge amber">Active</span></td>
                </tr>

                <tr>
                  <td>
                    <div class="res-cell">
                      <div class="res-photo">MM</div>
                      <div><b>Mrs. Meera Iyer</b><em>Room 118</em></div>
                    </div>
                  </td>
                  <td>72</td>
                  <td>118</td>
                  <td>Diabetes Type II</td>
                  <td>Sugar monitoring 3x/day</td>
                  <td>Priya</td>
                  <td>Before meals</td>
                  <td><span class="badge amber">Medium</span></td>
                  <td><span class="badge green">Stable</span></td>
                </tr>

                <tr>
                  <td>
                    <div class="res-cell">
                      <div class="res-photo">MV</div>
                      <div><b>Mr. Vijay Verma</b><em>Room 301</em></div>
                    </div>
                  </td>
                  <td>81</td>
                  <td>301</td>
                  <td>Hypertension</td>
                  <td>Low-salt diet, BP twice</td>
                  <td>Anil</td>
                  <td>9AM · 9PM</td>
                  <td><span class="badge amber">Medium</span></td>
                  <td><span class="badge green">Stable</span></td>
                </tr>

                <tr>
                  <td>
                    <div class="res-cell">
                      <div class="res-photo">ML</div>
                      <div><b>Mrs. Latha Rao</b><em>Room 302</em></div>
                    </div>
                  </td>
                  <td>75</td>
                  <td>302</td>
                  <td>Alzheimer's — mild</td>
                  <td>Memory support, escort</td>
                  <td>Radhika</td>
                  <td>Morning only</td>
                  <td><span class="badge red">High</span></td>
                  <td><span class="badge blue">Monitoring</span></td>
                </tr>

                <tr>
                  <td>
                    <div class="res-cell">
                      <div class="res-photo">MS</div>
                      <div><b>Mr. Suresh Nair</b><em>Room 210</em></div>
                    </div>
                  </td>
                  <td>69</td>
                  <td>210</td>
                  <td>Cardiac follow-up</td>
                  <td>Weekly ECG check-in</td>
                  <td>Priya</td>
                  <td>8AM · 8PM</td>
                  <td><span class="badge green">Low</span></td>
                  <td><span class="badge green">Stable</span></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

    </div>
</main>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
