<?php
/**
 * SevaNest – Session Management Helper
 * File     : includes/session.php
 * Version  : 1.0
 */

// ── TEMPORARY DEVELOPMENT MODE ──────────────────────────────────────────────
// Set to true to skip authentication and mock credentials for UI testing.
// Set to false for standard production database login checks.
if (!defined('DEV_MODE')) {
    define('DEV_MODE', true);
}
// ────────────────────────────────────────────────────────────────────────────

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'path' => '/',
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

// Auto-populate dummy session values in DEV_MODE based on the active path
if (defined('DEV_MODE') && DEV_MODE) {
    $_SESSION['user_id'] = 1;
    $_SESSION['username'] = 'Developer';
    
    $script_name = $_SERVER['SCRIPT_NAME'] ?? '';
    $role = 'admin';
    
    if (strpos($script_name, '/modules/doctor/') !== false) {
        $role = 'doctor';
    } elseif (strpos($script_name, '/modules/donor/') !== false) {
        $role = 'donor';
    } elseif (strpos($script_name, '/modules/caretaker/') !== false) {
        $role = 'caretaker';
    } elseif (strpos($script_name, '/modules/family/') !== false) {
        $role = 'family_member';
    } elseif (strpos($script_name, '/modules/super_admin/') !== false) {
        $role = 'super_admin';
    } elseif (strpos($script_name, '/modules/admin/') !== false) {
        $role = 'admin';
    }
    
    $_SESSION['role'] = $role;
}

/**
 * Checks if the current user is logged in.
 *
 * @return bool
 */
function is_logged_in() {
    if (defined('DEV_MODE') && DEV_MODE) {
        return true;
    }
    return isset($_SESSION['user_id']) || isset($_SESSION['user_name']);
}

/**
 * Restricts access to logged-in users. Redirects to login page if not authenticated.
 *
 * @param string $login_url
 */
function require_login($login_url = '../../modules/authentication/login.php') {
    if (!is_logged_in()) {
        if (function_exists('log_error')) {
            log_error("Authentication check failed. Session ID: " . session_id() . ", Session data: " . json_encode($_SESSION));
        }
        header("Location: $login_url");
        exit;
    }
}
