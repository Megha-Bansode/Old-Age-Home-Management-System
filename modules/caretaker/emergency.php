<?php
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../config/database.php';

// Require caretaker login
require_login();
require_role('Caretaker');

$pdo = get_db_connection();
$caretaker_id = $_SESSION['user_id'] ?? 4; // Default to Radhika

$formSuccess = '';
$formError = '';

// Seed mock cases if empty
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM emergency_cases");
    if ($stmt->fetchColumn() == 0) {
        $mocks = [
            ['resident_id' => 1, 'description' => '{"type":"Medical","location":"Room 204","severity":"Critical","details":"Fall detected — Room 204"}', 'status' => 'Active', 'doctor_id' => null],
            ['resident_id' => 2, 'description' => '{"type":"Medication","location":"Room 118","severity":"Medium","details":"Medication delay — Room 118"}', 'status' => 'Resolved', 'doctor_id' => null],
            ['resident_id' => 3, 'description' => '{"type":"Injury","location":"Garden","severity":"Low","details":"Minor injury during walk"}', 'status' => 'Resolved', 'doctor_id' => null],
            ['resident_id' => 4, 'description' => '{"type":"Medical","location":"Room 302","severity":"High","details":"Blood pressure spike"}', 'status' => 'Resolved', 'doctor_id' => null]
        ];
        foreach ($mocks as $m) {
            $stmt_ins = $pdo->prepare("INSERT INTO emergency_cases (resident_id, incident_description, status, doctor_id) VALUES (?, ?, ?, ?)");
            $stmt_ins->execute([$m['resident_id'], $m['description'], $m['status'], $m['doctor_id']]);
        }
    }
} catch (Exception $e) {}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resident_id = (int)($_POST['resident'] ?? 0);
    $type = trim($_POST['type'] ?? 'Medical');
    $location = trim($_POST['location'] ?? '');
    $severity = trim($_POST['severity'] ?? 'High');
    $details = trim($_POST['desc'] ?? '');
    
    if (!empty($details) && $resident_id > 0) {
        try {
            $desc_json = json_encode([
                'type' => $type,
                'location' => $location,
                'severity' => $severity,
                'details' => $details
            ]);
            
            $stmt = $pdo->prepare("INSERT INTO emergency_cases (resident_id, incident_description, status) VALUES (?, ?, 'Active')");
            $stmt->execute([$resident_id, $desc_json]);
            
            // Add notification for caretakers/doctors
            $res_stmt = $pdo->prepare("SELECT full_name FROM residents WHERE resident_id = ?");
            $res_stmt->execute([$resident_id]);
            $res_name = $res_stmt->fetchColumn();
            
            // Insert notification for admin/doctors
            $notif_stmt = $pdo->prepare("INSERT INTO notifications (user_id, title, message, type, priority, status) VALUES (?, 'Emergency Reported', ?, 'emergency', 'high', 'unread')");
            // Notify current caretaker
            $notif_stmt->execute([$caretaker_id, "Critical {$type} emergency reported for {$res_name} in {$location}."]);
            
            // Log activity
            $log_stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action, description) VALUES (?, 'Reported Emergency', ?)");
            $log_stmt->execute([$caretaker_id, "Reported emergency case for {$res_name}"]);
            
            $formSuccess = "Emergency case reported successfully! Incident response team notified.";
        } catch (Exception $e) {
            $formError = "Failed to submit emergency report: " . $e->getMessage();
        }
    } else {
        $formError = "Please select a resident and describe the incident.";
    }
}

// Query all emergencies
$emergencies = $pdo->query("
    SELECT ec.*, r.full_name AS resident_name 
    FROM emergency_cases ec 
    LEFT JOIN residents r ON ec.resident_id = r.resident_id 
    ORDER BY ec.created_at DESC
")->fetchAll();

$all_residents = $pdo->query("SELECT resident_id, full_name FROM residents WHERE status = 'Active' ORDER BY full_name ASC")->fetchAll();

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
$base_path = '../../';
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
            <form class="form" id="emergencyForm" method="POST" action="emergency.php">
              <div class="row">
                <label>Emergency Type
                  <select name="type"><option>Medical</option><option>Fire</option><option>Injury</option><option>Other</option></select>
                </label>
                <label>Resident
                  <select name="resident" required style="width:100%; border: 1px solid var(--color-border); border-radius: var(--radius-medium); padding: 8px;">
                    <option value="">Select Resident...</option>
                    <?php foreach ($all_residents as $r): ?>
                        <option value="<?php echo $r['resident_id']; ?>"><?php echo sn_e($r['full_name']); ?></option>
                    <?php endforeach; ?>
                  </select>
                </label>
              </div>
              <div class="row">
                <label>Location <input name="location" placeholder="Room / Area" required></label>
                <label>Reported Time <input type="datetime-local" name="time" value="<?php echo date('Y-m-d\TH:i'); ?>"></label>
              </div>
              <div class="row">
                <label>Severity
                  <select name="severity"><option>Critical</option><option>High</option><option>Medium</option><option>Low</option></select>
                </label>
                <label>Assigned Staff <input name="staff" placeholder="Caretaker / Doctor" value="On-Duty Team" readonly></label>
              </div>
              <label>Description <textarea name="desc" rows="4" placeholder="Describe the situation…" required></textarea></label>
              <div class="form-actions">
                <button class="btn ghost" type="reset">Reset</button>
                <button class="btn primary" type="submit">Submit Report</button>
              </div>
            </form>
          </div>

          <div class="card">
            <div class="card-head"><h3>Emergency Timeline</h3></div>
            <ul class="timeline dense">
              <?php if (empty($emergencies)): ?>
                <li><span class="dot"></span><div><strong>No active incidents</strong><em>All quiet.</em></div></li>
              <?php else: ?>
                <?php 
                $limit = 0;
                foreach ($emergencies as $emg): 
                    if ($limit++ >= 4) break;
                    $decoded = json_decode($emg['incident_description'] ?? '', true);
                    $type = $decoded['type'] ?? 'Medical';
                    $loc = $decoded['location'] ?? 'N/A';
                    $severity = $decoded['severity'] ?? 'High';
                    $details = $decoded['details'] ?? ($emg['incident_description'] ?? '');
                    
                    $dot = 'dot';
                    if (strcasecmp($severity, 'Critical') === 0 || strcasecmp($severity, 'High') === 0) $dot = 'dot alert';
                    elseif (strcasecmp($severity, 'Medium') === 0) $dot = 'dot gold';
                ?>
                  <li><span class="<?php echo $dot; ?>"></span><div><strong><?php echo sn_e($type); ?> — <?php echo sn_e($loc); ?></strong><em><?php echo date('d M, H:i', strtotime($emg['created_at'])); ?> · <?php echo sn_e($severity); ?></em></div></li>
                <?php endforeach; ?>
              <?php endif; ?>
            </ul>
          </div>
        </div>

        <div class="card no-pad">
          <div class="card-head pad"><h3>Incident History</h3><a class="link">View all</a></div>
          <div class="table-wrap">
            <table class="tbl">
              <thead><tr><th>ID</th><th>Type</th><th>Resident</th><th>Location</th><th>Severity</th><th>Status</th><th>Time</th></tr></thead>
              <tbody>
                <?php if (empty($emergencies)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">No emergency incidents logged.</td></tr>
                <?php else: ?>
                    <?php foreach ($emergencies as $emg): ?>
                        <?php
                            $decoded = json_decode($emg['incident_description'] ?? '', true);
                            $type = $decoded['type'] ?? 'Medical';
                            $loc = $decoded['location'] ?? 'N/A';
                            $severity = $decoded['severity'] ?? 'High';
                            $details = $decoded['details'] ?? ($emg['incident_description'] ?? '');
                            
                            $sev_class = '';
                            if (strcasecmp($severity, 'Critical') === 0 || strcasecmp($severity, 'High') === 0) $sev_class = 'red';
                            elseif (strcasecmp($severity, 'Medium') === 0) $sev_class = 'amber';
                            
                            $status_class = (strcasecmp($emg['status'], 'Active') === 0) ? 'amber' : 'green';
                        ?>
                        <tr>
                          <td>#EMG-<?php echo 1000 + $emg['case_id']; ?></td>
                          <td><?php echo sn_e($type); ?></td>
                          <td><?php echo sn_e($emg['resident_name']); ?></td>
                          <td><?php echo sn_e($loc); ?></td>
                          <td><span class="badge <?php echo $sev_class; ?>"><?php echo sn_e($severity); ?></span></td>
                          <td><span class="badge <?php echo $status_class; ?>"><?php echo sn_e($emg['status']); ?></span></td>
                          <td><?php echo date('H:i', strtotime($emg['created_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

    </div>
</main>

<?php if ($formSuccess): ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Emergency Reported',
                    text: <?php echo json_encode($formSuccess); ?>,
                    confirmButtonColor: '#d33'
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
    // Quick emergency buttons mapping
    const emgButtons = document.querySelectorAll('.emg-btn');
    const typeSelect = document.querySelector('select[name="type"]');
    
    emgButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const labelText = btn.textContent.replace(/[^\w]/g, '').trim(); // Extract Medical, Fire, Injury etc.
            if (typeSelect) {
                // Find option matching or select "Other"
                let found = false;
                for (let i = 0; i < typeSelect.options.length; i++) {
                    if (typeSelect.options[i].text.toLowerCase() === labelText.toLowerCase()) {
                        typeSelect.selectedIndex = i;
                        found = true;
                        break;
                    }
                }
                if (!found) {
                    typeSelect.value = 'Other';
                }
            }
            
            // Highlight active button briefly
            btn.style.transform = 'scale(0.95)';
            setTimeout(() => btn.style.transform = '', 150);
        });
    });
});
</script>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
