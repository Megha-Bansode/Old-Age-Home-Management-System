<?php
/**
 * SevaNest — Admin Settings
 * File     : modules/admin/settings.php
 * Version  : 1.0
 */

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/functions.php';

// Require Admin login
require_login();
require_role(['Admin', 'Old Age Home Admin']);

$base_path = '../../';
$page_title = 'Admin Settings | SevaNest';

// Database Connection
require_once __DIR__ . '/../../config/database.php';
$pdo = get_db_connection();

$settings_file = __DIR__ . '/../../config/settings.json';
$default_settings = [
    'home_name' => 'SevaNest Old Age Home Office',
    'max_occupancy' => 100,
    'session_timeout' => '30 Minutes',
    'timezone' => 'IST (Indian Standard Time)',
    'mfa_enabled' => true,
    'notify_admissions' => true,
    'notify_visitors' => true,
    'notify_meds' => false
];

$settings = $default_settings;
if (file_exists($settings_file)) {
    $file_content = json_decode(file_get_contents($settings_file), true);
    if (is_array($file_content)) {
        $settings = array_merge($default_settings, $file_content);
    }
}

$formSuccess = '';
$formError = '';

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $is_ajax = (isset($_POST['ajax']) && $_POST['ajax'] == '1');

    if ($action === 'general') {
        $home_name = trim($_POST['home_name'] ?? '');
        $max_occupancy = (int)($_POST['max_occupancy'] ?? 100);
        $session_timeout = trim($_POST['session_timeout'] ?? '');
        $timezone = trim($_POST['timezone'] ?? '');

        if (empty($home_name)) {
            $msg = 'Old Age Home Name is required.';
            if ($is_ajax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $msg]);
                exit;
            }
            $formError = $msg;
        } else {
            $settings['home_name'] = $home_name;
            $settings['max_occupancy'] = $max_occupancy;
            $settings['session_timeout'] = $session_timeout;
            $settings['timezone'] = $timezone;

            file_put_contents($settings_file, json_encode($settings, JSON_PRETTY_PRINT));
            $msg = 'General settings updated successfully!';
            if ($is_ajax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => $msg]);
                exit;
            }
            $formSuccess = $msg;
        }
    } elseif ($action === 'security') {
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $mfa_enabled = isset($_POST['mfa_enabled']) ? true : false;

        $user_id = $_SESSION['user_id'] ?? 2;

        // Verify current password from database
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $hash = $stmt->fetchColumn();

        if (!$hash || !password_verify($current_password, $hash)) {
            $msg = 'Incorrect current password.';
            if ($is_ajax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $msg]);
                exit;
            }
            $formError = $msg;
        } elseif ($new_password !== $confirm_password) {
            $msg = 'New passwords do not match.';
            if ($is_ajax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $msg]);
                exit;
            }
            $formError = $msg;
        } else {
            // Update password in database
            $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$new_hash, $user_id]);

            // Save MFA preference
            $settings['mfa_enabled'] = $mfa_enabled;
            file_put_contents($settings_file, json_encode($settings, JSON_PRETTY_PRINT));

            $msg = 'Security credentials updated successfully!';
            if ($is_ajax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => $msg]);
                exit;
            }
            $formSuccess = $msg;
        }
    } elseif ($action === 'notifications') {
        $settings['notify_admissions'] = isset($_POST['notify_admissions']) ? true : false;
        $settings['notify_visitors'] = isset($_POST['notify_visitors']) ? true : false;
        $settings['notify_meds'] = isset($_POST['notify_meds']) ? true : false;

        file_put_contents($settings_file, json_encode($settings, JSON_PRETTY_PRINT));
        $msg = 'Notification routing settings updated successfully!';
        if ($is_ajax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => $msg]);
            exit;
        }
        $formSuccess = $msg;
    }
}

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'admin';
$currentPage   = 'settings.php';
$sn_asset_root = "../../assets";
$base_path = '../../'; // Ensure correct path prefix
include '../../includes/sidebar.php';
?>

<main id="sn-main-content" role="main" aria-label="Admin Settings Content" class="p-4 flex-grow-1">
    <div class="container-fluid">

        <?php if ($formSuccess): ?>
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> <?php echo sn_e($formSuccess); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Page Header -->
        <div class="mb-4">
            <h3 class="fw-bold mb-0 text-dark">System Settings</h3>
            <small class="text-muted">Configure facility options, alert notifications, and security defaults</small>
        </div>

        <div class="row g-4">
            <!-- Left Side: Navigation Quick Tabs -->
            <div class="col-lg-3">
                <div class="card border-0 shadow-sm rounded-3 bg-white p-3">
                    <div class="nav flex-column nav-pills" id="settings-tab" role="tablist" aria-orientation="vertical">
                        <button class="nav-link active text-start py-2.5 px-3 mb-1" id="tab-general" data-bs-toggle="pill" data-bs-target="#panel-general" type="button" role="tab"><i class="bi bi-sliders me-2"></i>General Config</button>
                        <button class="nav-link text-start py-2.5 px-3 mb-1" id="tab-security" data-bs-toggle="pill" data-bs-target="#panel-security" type="button" role="tab"><i class="bi bi-shield-lock me-2"></i>Security</button>
                        <button class="nav-link text-start py-2.5 px-3" id="tab-notifications" data-bs-toggle="pill" data-bs-target="#panel-notifications" type="button" role="tab"><i class="bi bi-bell me-2"></i>Notifications</button>
                    </div>
                </div>
            </div>

            <!-- Right Side: Configuration Panels -->
            <div class="col-lg-9">
                <div class="tab-content" id="settings-tabContent">
                    
                    <!-- Panel 1: General Config -->
                    <div class="tab-pane fade show active" id="panel-general" role="tabpanel" aria-labelledby="tab-general">
                        <div class="card border-0 shadow-sm rounded-3 bg-white p-4">
                            <h5 class="fw-bold mb-1 text-dark">General Facility Configuration</h5>
                            <p class="text-muted small mb-4">Set up global system behaviors and wing occupancy levels.</p>
                            
                            <form method="POST" action="settings.php" class="settings-form">
                                <input type="hidden" name="action" value="general">
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label for="home_name" class="form-label fw-semibold text-dark small">Old Age Home Name</label>
                                        <input type="text" id="home_name" name="home_name" class="form-control" value="<?php echo sn_e($settings['home_name']); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="max_occupancy" class="form-label fw-semibold text-dark small">Maximum Bed Occupancy</label>
                                        <input type="number" id="max_occupancy" name="max_occupancy" class="form-control" value="<?php echo (int)$settings['max_occupancy']; ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="session_timeout" class="form-label fw-semibold text-dark small">Automatic Session Timeout</label>
                                        <select id="session_timeout" name="session_timeout" class="form-select">
                                            <option value="15 Minutes" <?php echo $settings['session_timeout'] === '15 Minutes' ? 'selected' : ''; ?>>15 Minutes</option>
                                            <option value="30 Minutes" <?php echo $settings['session_timeout'] === '30 Minutes' ? 'selected' : ''; ?>>30 Minutes</option>
                                            <option value="1 Hour" <?php echo $settings['session_timeout'] === '1 Hour' ? 'selected' : ''; ?>>1 Hour</option>
                                            <option value="2 Hours" <?php echo $settings['session_timeout'] === '2 Hours' ? 'selected' : ''; ?>>2 Hours</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="timezone" class="form-label fw-semibold text-dark small">System Timezone</label>
                                        <select id="timezone" name="timezone" class="form-select">
                                            <option value="UTC (Coordinated Universal Time)" <?php echo $settings['timezone'] === 'UTC (Coordinated Universal Time)' ? 'selected' : ''; ?>>UTC (Coordinated Universal Time)</option>
                                            <option value="IST (Indian Standard Time)" <?php echo $settings['timezone'] === 'IST (Indian Standard Time)' ? 'selected' : ''; ?>>IST (Indian Standard Time)</option>
                                            <option value="EST (Eastern Standard Time)" <?php echo $settings['timezone'] === 'EST (Eastern Standard Time)' ? 'selected' : ''; ?>>EST (Eastern Standard Time)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end gap-2 border-top pt-3">
                                    <button type="submit" class="btn btn-sm btn-primary fw-semibold">Save Settings</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Panel 2: Security -->
                    <div class="tab-pane fade" id="panel-security" role="tabpanel" aria-labelledby="tab-security">
                        <div class="card border-0 shadow-sm rounded-3 bg-white p-4">
                            <h5 class="fw-bold mb-1 text-dark">Security and Account Access</h5>
                            <p class="text-muted small mb-4">Update your administrative credentials and toggle multi-factor settings.</p>
                            
                            <form method="POST" action="settings.php" class="settings-form">
                                <input type="hidden" name="action" value="security">
                                <div class="row g-3 mb-4">
                                    <div class="col-md-12">
                                        <label for="current_password" class="form-label fw-semibold text-dark small">Current Password</label>
                                        <input type="password" id="current_password" name="current_password" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="new_password" class="form-label fw-semibold text-dark small">New Password</label>
                                        <input type="password" id="new_password" name="new_password" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="confirm_password" class="form-label fw-semibold text-dark small">Confirm New Password</label>
                                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                                    </div>
                                    <div class="col-12 mt-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="mfa_switch" name="mfa_enabled" value="1" <?php echo $settings['mfa_enabled'] ? 'checked' : ''; ?> style="cursor: pointer;">
                                            <label class="form-check-label fw-semibold text-dark small" for="mfa_switch" style="cursor: pointer;">Enable Multi-Factor OTP on Login</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end gap-2 border-top pt-3">
                                    <button type="submit" class="btn btn-sm btn-primary fw-semibold">Change Password</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Panel 3: Notifications -->
                    <div class="tab-pane fade" id="panel-notifications" role="tabpanel" aria-labelledby="tab-notifications">
                        <div class="card border-0 shadow-sm rounded-3 bg-white p-4">
                            <h5 class="fw-bold mb-1 text-dark">Alert and Notification Routing</h5>
                            <p class="text-muted small mb-4">Select which notification logs are enabled or dispatched to emails.</p>
                            
                            <form method="POST" action="settings.php" class="settings-form">
                                <input type="hidden" name="action" value="notifications">
                                <div class="d-flex flex-column gap-3 mb-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="notify_admissions" name="notify_admissions" value="1" <?php echo $settings['notify_admissions'] ? 'checked' : ''; ?> style="cursor: pointer;">
                                        <label class="form-check-label text-dark small fw-semibold" for="notify_admissions" style="cursor: pointer;">New Admission Request Alerts</label>
                                        <span class="text-muted d-block small">Get real-time notification alerts when families submit an intake booking.</span>
                                    </div>
                                    <hr class="border-light my-1">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="notify_visitors" name="notify_visitors" value="1" <?php echo $settings['notify_visitors'] ? 'checked' : ''; ?> style="cursor: pointer;">
                                        <label class="form-check-label text-dark small fw-semibold" for="notify_visitors" style="cursor: pointer;">Visitor Entry/Exit Alerts</label>
                                        <span class="text-muted d-block small">Get logging notifications when visitors check in or out.</span>
                                    </div>
                                    <hr class="border-light my-1">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="notify_meds" name="notify_meds" value="1" <?php echo $settings['notify_meds'] ? 'checked' : ''; ?> style="cursor: pointer;">
                                        <label class="form-check-label text-dark small fw-semibold" for="notify_meds" style="cursor: pointer;">Critical Medical Logs</label>
                                        <span class="text-muted d-block small">Forward critical health status updates from doctor portal to administrator mail.</span>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end gap-2 border-top pt-3">
                                    <button type="submit" class="btn btn-sm btn-primary fw-semibold">Save Routing Settings</button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const forms = document.querySelectorAll('form.settings-form');

    function showFeedback(type, message) {
        const existingAlerts = document.querySelectorAll('.container-fluid > .alert');
        existingAlerts.forEach(a => a.remove());

        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4`;
        alertDiv.setAttribute('role', 'alert');
        
        const icon = type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill';
        alertDiv.innerHTML = `
            <i class="bi ${icon} me-2"></i> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        const container = document.querySelector('.container-fluid');
        container.insertBefore(alertDiv, container.firstChild);
        alertDiv.scrollIntoView({ behavior: 'smooth' });

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: type === 'success' ? 'success' : 'error',
                title: type === 'success' ? 'Settings Saved' : 'Error',
                text: message,
                confirmButtonColor: '#2b4c3f'
            });
        }
    }

    forms.forEach(form => {
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(form);
            formData.append('ajax', '1');

            fetch('settings.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showFeedback('success', data.message);
                    const action = formData.get('action');
                    if (action === 'security') {
                        const mfaCheck = form.querySelector('#mfa_switch');
                        const mfaWasChecked = mfaCheck ? mfaCheck.checked : false;
                        form.reset();
                        if (mfaCheck) {
                            mfaCheck.checked = mfaWasChecked;
                        }
                    }
                } else {
                    showFeedback('error', data.message);
                }
            })
            .catch(err => {
                showFeedback('error', 'An error occurred while saving configuration settings.');
            });
        });
    });
});
</script>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
