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

// Programmatic seeder
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM meals");
    if ($stmt->fetchColumn() == 0) {
        $mocks = [
            ['meal_name' => 'Oats Porridge & Fruits (Breakfast)', 'meal_type' => 'Diabetic-Friendly', 'description' => 'Served at 07:30 AM. Assigned Staff: Anil Kumar. Resident Count: 48. Status: Served.'],
            ['meal_name' => 'Rice, Dal & Sabji (Lunch)', 'meal_type' => 'Veg', 'description' => 'Served at 12:30 PM. Assigned Staff: Priya Menon. Resident Count: 50. Status: Served.'],
            ['meal_name' => 'Tea & Rusk (Snacks)', 'meal_type' => 'Soft', 'description' => 'Scheduled at 04:30 PM. Assigned Staff: Radhika S. Resident Count: 47. Status: Scheduled.'],
            ['meal_name' => 'Khichdi & Soup (Dinner)', 'meal_type' => 'Liquid', 'description' => 'Scheduled at 07:30 PM. Assigned Staff: Anil Kumar. Resident Count: 50. Status: Scheduled.']
        ];
        foreach ($mocks as $m) {
            $stmt_ins = $pdo->prepare("INSERT INTO meals (meal_name, meal_type, description) VALUES (?, ?, ?)");
            $stmt_ins->execute([$m['meal_name'], $m['meal_type'], $m['description']]);
        }
    }
} catch (Exception $e) {
    // Fail silently
}

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $meal_name = trim($_POST['meal_name'] ?? '');
        $meal_type = trim($_POST['meal_type'] ?? '');
        $time = trim($_POST['time'] ?? '08:00 AM');
        $staff = trim($_POST['staff'] ?? $caretaker_name);
        $count = (int)($_POST['resident_count'] ?? 50);
        $status = trim($_POST['status'] ?? 'Scheduled');
        
        if (!empty($meal_name) && !empty($meal_type)) {
            try {
                $description = "Scheduled at {$time}. Assigned Staff: {$staff}. Resident Count: {$count}. Status: {$status}.";
                $stmt = $pdo->prepare("INSERT INTO meals (meal_name, meal_type, description) VALUES (?, ?, ?)");
                $stmt->execute([$meal_name, $meal_type, $description]);
                
                // Log activity
                $log_stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action, description) VALUES (?, 'Added Meal', ?)");
                $log_stmt->execute([$caretaker_id, "Scheduled {$meal_name} meal"]);
                
                $formSuccess = "Meal scheduled successfully!";
            } catch (Exception $e) {
                $formError = "Error saving meal: " . $e->getMessage();
            }
        } else {
            $formError = "Please fill in all required fields.";
        }
    } elseif ($action === 'delete') {
        $meal_id = (int)($_POST['meal_id'] ?? 0);
        try {
            $stmt = $pdo->prepare("DELETE FROM meals WHERE meal_id = ?");
            $stmt->execute([$meal_id]);
            $formSuccess = "Meal deleted successfully!";
        } catch (Exception $e) {
            $formError = "Error deleting meal: " . $e->getMessage();
        }
    }
}

// Fetch meals list
$meals = $pdo->query("SELECT * FROM meals ORDER BY meal_id ASC")->fetchAll();

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
$base_path = '../../';
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
          <div class="page-actions">
            <button class="btn primary" id="addMealBtn">+ Add Meal</button>
          </div>
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
            <?php if (empty($meals)): ?>
                <div class="card p-4 text-center text-muted w-100">No scheduled meals found.</div>
            <?php else: ?>
                <?php foreach ($meals as $meal): ?>
                    <?php
                        $desc = $meal['description'] ?? '';
                        $time = '08:00 AM';
                        $staff = 'Staff On-Duty';
                        $count = '50';
                        $status = 'Scheduled';
                        
                        if (preg_match('/Scheduled at ([0-9]+:[0-9]+ [A-Z]+)/i', $desc, $matches)) $time = $matches[1];
                        elseif (preg_match('/Served at ([0-9]+:[0-9]+ [A-Z]+)/i', $desc, $matches)) $time = $matches[1];
                        
                        if (preg_match('/Assigned Staff: ([A-Za-z0-9. ]+)/i', $desc, $matches)) $staff = trim($matches[1]);
                        if (preg_match('/Resident Count: ([0-9]+)/i', $desc, $matches)) $count = $matches[1];
                        if (preg_match('/Status: ([A-Za-z]+)/i', $desc, $matches)) $status = trim($matches[1]);
                        
                        $meal_type = $meal['meal_type'];
                    ?>
                    <div class="meal-card">
                      <div class="meal-head">
                        <div>
                          <h4><?php echo sn_e($meal['meal_name']); ?></h4>
                          <em><?php echo $time; ?> · <?php echo sn_e($staff); ?></em>
                        </div>
                        <span class="meal-tag"><?php echo $status; ?></span>
                      </div>
                      <div class="meal-info">
                        <div><strong>Time</strong><?php echo $time; ?></div>
                        <div><strong>Assigned Staff</strong><?php echo sn_e($staff); ?></div>
                        <div><strong>Resident Count</strong><?php echo $count; ?></div>
                        <div><strong>Status</strong><?php echo $status; ?></div>
                      </div>
                      <div class="diet-tags">
                        <span><?php echo sn_e($meal_type); ?></span>
                        <span>Vegetarian</span>
                      </div>
                      <div class="meal-foot">
                        <span class="muted">Nutrition summary logged</span>
                        <div>
                            <form method="POST" action="meals.php" class="d-inline delete-meal-form">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="meal_id" value="<?php echo $meal['meal_id']; ?>">
                                <button class="btn tiny btn-outline-danger" type="submit">Delete</button>
                            </form>
                        </div>
                      </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>
</main>

<!-- Modal for adding new meal -->
<div class="modal fade" id="mealModal" tabindex="-1" aria-labelledby="mealModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <form method="POST" action="meals.php">
                <input type="hidden" name="action" value="add">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="mealModalLabel">Schedule New Meal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Meal Name <span class="text-danger">*</span></label>
                        <input type="text" name="meal_name" class="form-control select" required style="width:100%; border: 1px solid var(--color-border); border-radius: var(--radius-medium); padding: 8px;" placeholder="e.g. Roti, Sabji & Rice (Dinner)">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Meal Type <span class="text-danger">*</span></label>
                        <select name="meal_type" class="form-select select" required style="width:100%; border: 1px solid var(--color-border); border-radius: var(--radius-medium); padding: 8px;">
                            <option value="">Select Type...</option>
                            <option value="Veg">Veg</option>
                            <option value="Non-Veg">Non-Veg</option>
                            <option value="Diabetic-Friendly">Diabetic-Friendly</option>
                            <option value="Liquid">Liquid</option>
                            <option value="Soft">Soft</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Scheduled TimeSlot <span class="text-danger">*</span></label>
                        <input type="text" name="time" class="form-control select" required style="width:100%; border: 1px solid var(--color-border); border-radius: var(--radius-medium); padding: 8px;" placeholder="e.g. 07:30 PM">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Resident Count</label>
                        <input type="number" name="resident_count" class="form-control select" value="50" style="width:100%; border: 1px solid var(--color-border); border-radius: var(--radius-medium); padding: 8px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Status</label>
                        <select name="status" class="form-select select" style="width:100%; border: 1px solid var(--color-border); border-radius: var(--radius-medium); padding: 8px;">
                            <option value="Scheduled">Scheduled</option>
                            <option value="Served">Served</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-sm">Schedule Meal</button>
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
    const addMealBtn = document.getElementById('addMealBtn');
    if (addMealBtn) {
        addMealBtn.addEventListener('click', () => {
            const myModal = new bootstrap.Modal(document.getElementById('mealModal'));
            myModal.show();
        });
    }

    document.querySelectorAll('.delete-meal-form').forEach(form => {
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Delete scheduled meal?',
                    text: 'This action will remove the meal log.',
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
                if (confirm('Delete this meal entry?')) {
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
