<?php
/**
 * SevaNest – Session Management Helper
 * File     : includes/session.php
 * Version  : 1.0
 */

require_once __DIR__ . '/../config/config.php';

// ── TEMPORARY DEVELOPMENT MODE ──────────────────────────────────────────────
// Set to true to skip authentication and mock credentials for UI testing.
// Set to false for standard production database login checks.
if (!defined('DEV_MODE')) {
    define('DEV_MODE', false);
}
// ────────────────────────────────────────────────────────────────────────────

if (!function_exists('start_secure_session')) {
    function start_secure_session() {
        if (session_status() === PHP_SESSION_NONE) {
            $cookieParams = [
                'lifetime' => 0, // Until browser closes (or handled by remember me)
                'path' => '/',
                'domain' => '',
                'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
                'httponly' => true,
                'samesite' => 'Lax'
            ];
            
            session_set_cookie_params($cookieParams);
            session_start();
        }

        // Skip timeout check in DEV_MODE
        if (defined('DEV_MODE') && DEV_MODE) {
            return true;
        }

        // Check for session timeout
        if (isset($_SESSION['user_id']) && isset($_SESSION['last_activity'])) {
            if ((time() - $_SESSION['last_activity']) > SESSION_LIFETIME) {
                // Session expired due to inactivity
                session_unset();
                session_destroy();
                session_start();
                $_SESSION['session_expired_msg'] = "Session expired due to inactivity. Please sign in again.";
                return false;
            }
        }

        // Update last activity timestamp
        if (isset($_SESSION['user_id'])) {
            $_SESSION['last_activity'] = time();
        }

        // Periodic session ID regeneration (every 15 mins)
        if (isset($_SESSION['user_id']) && !isset($_SESSION['created_at'])) {
            $_SESSION['created_at'] = time();
        } elseif (isset($_SESSION['created_at']) && (time() - $_SESSION['created_at']) > 900) {
            session_regenerate_id(true);
            $_SESSION['created_at'] = time();
        }

        return true;
    }
}

// Auto-run secure session start
start_secure_session();

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
if (!function_exists('is_logged_in')) {
    function is_logged_in() {
        if (defined('DEV_MODE') && DEV_MODE) {
            return true;
        }
        return isset($_SESSION['user_id']) && isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
}

// Centralized Path-Based Authorization Gate & Browser Cache Disable
if (!defined('DEV_MODE') || !DEV_MODE) {
    $script_name = $_SERVER['SCRIPT_NAME'] ?? '';
    $restricted_roles = null;
    
    if (strpos($script_name, '/modules/super_admin/') !== false) {
        $restricted_roles = ['Super Admin'];
    } elseif (strpos($script_name, '/modules/admin/') !== false) {
        $restricted_roles = ['Admin', 'Old Age Home Admin'];
    } elseif (strpos($script_name, '/modules/doctor/') !== false) {
        $restricted_roles = ['Doctor'];
    } elseif (strpos($script_name, '/modules/caretaker/') !== false) {
        $restricted_roles = ['Caretaker'];
    } elseif (strpos($script_name, '/modules/donor/') !== false) {
        $restricted_roles = ['Donor'];
    } elseif (strpos($script_name, '/modules/family/') !== false) {
        $restricted_roles = ['Family Member'];
    }
    
    if ($restricted_roles !== null) {
        // Enforce cache-control headers to prevent Back-button dashboard recovery
        header("Cache-Control: no-cache, no-store, must-revalidate");
        header("Pragma: no-cache");
        header("Expires: 0");
        
        start_secure_session();
        if (!is_logged_in()) {
            $login_url = BASE_URL . "modules/authentication/login.php";
            header("Location: " . $login_url . "?error=unauthorized");
            exit();
        }
        
        $user_role = $_SESSION['user_role'] ?? $_SESSION['role'] ?? '';
        if (!in_array($user_role, $restricted_roles)) {
            require_once __DIR__ . '/auth.php';
            $targetUrl = get_dashboard_url($user_role);
            header("Location: " . $targetUrl . "?error=role_mismatch");
            exit();
        }
    }
}

/**
 * Restricts access to logged-in users. Redirects to login page if not authenticated.
 *
 * @param string $login_url
 */
if (!function_exists('require_login')) {
    function require_login($login_url = null) {
        if (defined('DEV_MODE') && DEV_MODE) {
            return;
        }
        if ($login_url === null) {
            $login_url = BASE_URL . "modules/authentication/login.php";
        }
        if (!is_logged_in()) {
            if (function_exists('log_error')) {
                log_error("Authentication check failed. Session ID: " . session_id() . ", Session data: " . json_encode($_SESSION));
            }
            header("Location: $login_url?error=unauthorized");
            exit;
        }
    }
}

/**
 * Generate or get existing CSRF Token
 */
if (!function_exists('generate_csrf_token')) {
    function generate_csrf_token() {
        start_secure_session();
        if (empty($_SESSION[CSRF_TOKEN_KEY])) {
            $_SESSION[CSRF_TOKEN_KEY] = bin2hex(random_bytes(32));
        }
        return $_SESSION[CSRF_TOKEN_KEY];
    }
}

/**
 * Verify CSRF Token
 */
if (!function_exists('verify_csrf_token')) {
    function verify_csrf_token($token) {
        start_secure_session();
        if (!isset($_SESSION[CSRF_TOKEN_KEY]) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION[CSRF_TOKEN_KEY], $token);
    }
}
