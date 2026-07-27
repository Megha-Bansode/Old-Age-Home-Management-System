<?php
/**
 * SevaNest – Caretaker Daily Activities
 * File     : modules/caretaker/activities.php
 * Version  : 1.0
 */

require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../config/database.php';

// Require caretaker login
require_login();

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
            <div class="act-card ">
              <h4>Morning Yoga</h4>
              <div class="act-meta">
                <span>👤 Radhika</span>
                <span>👥 24 residents</span>
                <span><span class="badge amber">Ongoing</span></span>
              </div>
              <div class="act-notes">Garden hall — chair yoga variation.</div>
              <div class="act-actions">
                <button class="btn tiny">Edit</button>
                <button class="btn tiny">Delete</button>
              </div>
            </div>
            <div class="act-card gold">
              <h4>Breakfast Service</h4>
              <div class="act-meta">
                <span>👤 Anil</span>
                <span>👥 48 residents</span>
                <span><span class="badge green">Completed</span></span>
              </div>
              <div class="act-notes">Idli, oats &amp; fruit juice served.</div>
              <div class="act-actions">
                <button class="btn tiny">Edit</button>
                <button class="btn tiny">Delete</button>
              </div>
            </div>
            <div class="act-card pink">
              <h4>Physiotherapy</h4>
              <div class="act-meta">
                <span>👤 Priya</span>
                <span>👥 6 residents</span>
                <span><span class="badge blue">Scheduled</span></span>
              </div>
              <div class="act-notes">Individual sessions in Wing A.</div>
              <div class="act-actions">
                <button class="btn tiny">Edit</button>
                <button class="btn tiny">Delete</button>
              </div>
            </div>
          </div>
        </div>

        <div class="activity-block">
          <h3 class="sec-title">☀️ Afternoon Activities</h3>
          <div class="grid three-col" id="afternoonActs">
            <div class="act-card gold">
              <h4>Health Checkup</h4>
              <div class="act-meta">
                <span>👤 Dr. Nair</span>
                <span>👥 12 residents</span>
                <span><span class="badge amber">Ongoing</span></span>
              </div>
              <div class="act-notes">Weekly vitals &amp; BP monitoring.</div>
              <div class="act-actions">
                <button class="btn tiny">Edit</button>
                <button class="btn tiny">Delete</button>
              </div>
            </div>
            <div class="act-card ">
              <h4>Reading Circle</h4>
              <div class="act-meta">
                <span>👤 Radhika</span>
                <span>👥 15 residents</span>
                <span><span class="badge blue">Scheduled</span></span>
              </div>
              <div class="act-notes">Regional literature session.</div>
              <div class="act-actions">
                <button class="btn tiny">Edit</button>
                <button class="btn tiny">Delete</button>
              </div>
            </div>
            <div class="act-card pink">
              <h4>Music Therapy</h4>
              <div class="act-meta">
                <span>👤 Priya</span>
                <span>👥 18 residents</span>
                <span><span class="badge blue">Scheduled</span></span>
              </div>
              <div class="act-notes">Classical instrumental therapy.</div>
              <div class="act-actions">
                <button class="btn tiny">Edit</button>
                <button class="btn tiny">Delete</button>
              </div>
            </div>
          </div>
        </div>

        <div class="activity-block">
          <h3 class="sec-title">🌙 Evening Activities</h3>
          <div class="grid three-col" id="eveningActs">
            <div class="act-card ">
              <h4>Evening Walk</h4>
              <div class="act-meta">
                <span>👤 Anil</span>
                <span>👥 22 residents</span>
                <span><span class="badge blue">Scheduled</span></span>
              </div>
              <div class="act-notes">Guided walk in the garden.</div>
              <div class="act-actions">
                <button class="btn tiny">Edit</button>
                <button class="btn tiny">Delete</button>
              </div>
            </div>
            <div class="act-card gold">
              <h4>Prayer Session</h4>
              <div class="act-meta">
                <span>👤 Radhika</span>
                <span>👥 30 residents</span>
                <span><span class="badge blue">Scheduled</span></span>
              </div>
              <div class="act-notes">Multi-faith prayer room.</div>
              <div class="act-actions">
                <button class="btn tiny">Edit</button>
                <button class="btn tiny">Delete</button>
              </div>
            </div>
            <div class="act-card pink">
              <h4>Movie Night</h4>
              <div class="act-meta">
                <span>👤 Priya</span>
                <span>👥 20 residents</span>
                <span><span class="badge blue">Scheduled</span></span>
              </div>
              <div class="act-notes">Community hall — vintage films.</div>
              <div class="act-actions">
                <button class="btn tiny">Edit</button>
                <button class="btn tiny">Delete</button>
              </div>
            </div>
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

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
