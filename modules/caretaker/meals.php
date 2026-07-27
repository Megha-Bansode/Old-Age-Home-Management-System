<?php
/**
 * SevaNest – Caretaker Meal Schedule
 * File     : modules/caretaker/meals.php
 * Version  : 1.0
 */

require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../config/database.php';

// Require caretaker login
require_login();

$base_path = '../../';
$page_title = 'Meal Schedule | SevaNest';
$extra_css = [
    'assets/css/caretaker.css'
];

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'caretaker';
$currentPage   = 'meals.php';
$sn_asset_root = "../../assets";
include '../../includes/sidebar.php';
?>

<!-- ═══════════════════════════════════════════════════════════════════════
     MAIN CONTENT AREA
     ═══════════════════════════════════════════════════════════════════════ -->
<main id="sn-main-content" role="main" aria-label="Caretaker meals content">
    <div class="pages">
        
        <div class="page-head animate-fade-in">
          <div>
            <h2 class="page-title">Meal Schedule</h2>
            <p class="page-sub">Balanced nutrition tailored to every resident's needs.</p>
          </div>
          <div class="page-actions"></div>
        </div>

        <!-- Nutri Info Grid -->
        <div class="grid four-col nutri">
          <div class="nutri-card"><span>🥗</span><strong>1,850</strong><em>Avg. Calories</em></div>
          <div class="nutri-card"><span>💧</span><strong>2.4 L</strong><em>Hydration</em></div>
          <div class="nutri-card"><span>🍚</span><strong>210 g</strong><em>Carbs</em></div>
          <div class="nutri-card"><span>🥚</span><strong>72 g</strong><em>Protein</em></div>
        </div>

        <!-- Meals Grid -->
        <div class="grid two-col" id="mealsGrid">
          <div class="meal-card">
            <div class="meal-head">
              <div>
                <h4>Breakfast</h4>
                <em>07:30 AM · Anil Kumar</em>
              </div>
              <span class="meal-tag">Served</span>
            </div>
            <div class="meal-info">
              <div><strong>Time</strong>07:30 AM</div>
              <div><strong>Assigned Staff</strong>Anil Kumar</div>
              <div><strong>Resident Count</strong>48</div>
              <div><strong>Status</strong>Served</div>
            </div>
            <div class="diet-tags"><span>Diabetic</span><span>Soft Diet</span><span>Vegetarian</span></div>
            <div class="meal-foot">
              <span class="muted">Special diets tracked</span>
              <div><button class="btn tiny">Edit</button> <button class="btn tiny">View</button></div>
            </div>
          </div>

          <div class="meal-card">
            <div class="meal-head">
              <div>
                <h4>Lunch</h4>
                <em>12:30 PM · Priya Menon</em>
              </div>
              <span class="meal-tag">Served</span>
            </div>
            <div class="meal-info">
              <div><strong>Time</strong>12:30 PM</div>
              <div><strong>Assigned Staff</strong>Priya Menon</div>
              <div><strong>Resident Count</strong>50</div>
              <div><strong>Status</strong>Served</div>
            </div>
            <div class="diet-tags"><span>Low Salt</span><span>Diabetic</span><span>Vegetarian</span></div>
            <div class="meal-foot">
              <span class="muted">Special diets tracked</span>
              <div><button class="btn tiny">Edit</button> <button class="btn tiny">View</button></div>
            </div>
          </div>

          <div class="meal-card">
            <div class="meal-head">
              <div>
                <h4>Snacks</h4>
                <em>04:30 PM · Radhika S.</em>
              </div>
              <span class="meal-tag">Scheduled</span>
            </div>
            <div class="meal-info">
              <div><strong>Time</strong>04:30 PM</div>
              <div><strong>Assigned Staff</strong>Radhika S.</div>
              <div><strong>Resident Count</strong>47</div>
              <div><strong>Status</strong>Scheduled</div>
            </div>
            <div class="diet-tags"><span>Soft Diet</span><span>Vegetarian</span></div>
            <div class="meal-foot">
              <span class="muted">Special diets tracked</span>
              <div><button class="btn tiny">Edit</button> <button class="btn tiny">View</button></div>
            </div>
          </div>

          <div class="meal-card">
            <div class="meal-head">
              <div>
                <h4>Dinner</h4>
                <em>07:30 PM · Anil Kumar</em>
              </div>
              <span class="meal-tag">Scheduled</span>
            </div>
            <div class="meal-info">
              <div><strong>Time</strong>07:30 PM</div>
              <div><strong>Assigned Staff</strong>Anil Kumar</div>
              <div><strong>Resident Count</strong>50</div>
              <div><strong>Status</strong>Scheduled</div>
            </div>
            <div class="diet-tags"><span>Low Salt</span><span>Soft Diet</span><span>Vegetarian</span></div>
            <div class="meal-foot">
              <span class="muted">Special diets tracked</span>
              <div><button class="btn tiny">Edit</button> <button class="btn tiny">View</button></div>
            </div>
          </div>
        </div>

    </div>
</main>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
