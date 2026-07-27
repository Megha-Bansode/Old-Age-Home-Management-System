<?php
// ============================================================
// SevaNest - Authentication & Role Helpers
// ============================================================

require_once __DIR__ . '/session.php';
require_once __DIR__ . '/../config/database.php';

/**
 * Sanitize raw input string
 */
function sanitize_input($data) {
    if (is_array($data)) {
        return array_map('sanitize_input', $data);
    }
    return htmlspecialchars(trim((string)$data), ENT_QUOTES, 'UTF-8');
}

/**
 * Get dashboard URL based on user role
 */
function get_dashboard_url($role) {
    switch ($role) {
        case 'Super Admin':
            return BASE_URL . 'modules/super_admin/index.php';
        case 'Admin':
        case 'Old Age Home Admin':
            return BASE_URL . 'modules/admin/index.php';
        case 'Doctor':
            return BASE_URL . 'modules/doctor/dashboard.php';
        case 'Caretaker':
            return BASE_URL . 'modules/caretaker/dashboard.php';
        case 'Family Member':
            return BASE_URL . 'modules/family/dashboard.php';
        case 'Donor':
            return BASE_URL . 'modules/donor/dashboard.php';
        default:
            return BASE_URL . 'index.php';
    }
}

/**
 * Check if a user is currently logged in (Session or Remember Me cookie)
 */
if (!function_exists('is_logged_in')) {
    function is_logged_in() {
        if (defined('DEV_MODE') && DEV_MODE) {
            return true;
        }

        start_secure_session();

        if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
            return true;
        }

        // Try auto-login via Remember Me cookie
        if (isset($_COOKIE[REMEMBER_COOKIE_NAME])) {
            $token = $_COOKIE[REMEMBER_COOKIE_NAME];
            $tokenHash = hash('sha256', $token);

            try {
                $db = getDBConnection();
                $stmt = $db->prepare("
                    SELECT rt.user_id, u.full_name, u.email, u.role, u.status, u.profile_photo 
                    FROM remember_tokens rt
                    JOIN users u ON rt.user_id = u.id
                    WHERE rt.token_hash = :hash AND rt.expires_at > NOW() AND u.status = 'active'
                    LIMIT 1
                ");
                $stmt->execute([':hash' => $tokenHash]);
                $user = $stmt->fetch();

                if ($user) {
                    // Auto-login successful
                    session_regenerate_id(true);
                    $_SESSION['user_id']       = $user['user_id'];
                    $_SESSION['full_name']     = $user['full_name'];
                    $_SESSION['email']         = $user['email'];
                    $_SESSION['role']          = $user['role'];
                    $_SESSION['profile_photo'] = $user['profile_photo'];
                    $_SESSION['last_activity'] = time();

                    return true;
                } else {
                    clear_remember_me_cookie();
                }
            } catch (Exception $e) {
                clear_remember_me_cookie();
            }
        }

        return false;
    }
}

/**
 * Require active user login for protected pages
 */
if (!function_exists('require_login')) {
    function require_login($login_url = null) {
        if ($login_url === null) {
            $login_url = BASE_URL . "modules/authentication/login.php";
        }
        if (!is_logged_in()) {
            header("Location: " . $login_url . "?error=unauthorized");
            exit();
        }
    }
}

/**
 * Require specific role for role-based dashboard access
 */
if (!function_exists('require_role')) {
    function require_role($allowedRoles) {
        if (defined('DEV_MODE') && DEV_MODE) {
            return;
        }

        require_login();

        if (is_string($allowedRoles)) {
            $allowedRoles = [$allowedRoles];
        }

        if (!in_array($_SESSION['role'], $allowedRoles)) {
            $targetUrl = get_dashboard_url($_SESSION['role']);
            header("Location: " . $targetUrl . "?error=role_mismatch");
            exit();
        }
    }
}

/**
 * Save Remember Me token to database and set cookie
 */
function set_remember_me_cookie($userId) {
    try {
        $db = getDBConnection();
        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);
        $expiresAt = date('Y-m-d H:i:s', time() + REMEMBER_COOKIE_LIFETIME);

        $delStmt = $db->prepare("DELETE FROM remember_tokens WHERE user_id = :uid");
        $delStmt->execute([':uid' => $userId]);

        $stmt = $db->prepare("INSERT INTO remember_tokens (user_id, token_hash, expires_at) VALUES (:uid, :hash, :expires)");
        $stmt->execute([
            ':uid'     => $userId,
            ':hash'    => $tokenHash,
            ':expires' => $expiresAt
        ]);

        setcookie(
            REMEMBER_COOKIE_NAME,
            $token,
            [
                'expires'  => time() + REMEMBER_COOKIE_LIFETIME,
                'path'     => '/',
                'domain'   => '',
                'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
                'httponly' => true,
                'samesite' => 'Lax'
            ]
        );
    } catch (Exception $e) {
        // Silent fail if cookie setting encounters issue
    }
}

/**
 * Clear Remember Me cookie & delete token from DB
 */
function clear_remember_me_cookie() {
    if (isset($_COOKIE[REMEMBER_COOKIE_NAME])) {
        $token = $_COOKIE[REMEMBER_COOKIE_NAME];
        $tokenHash = hash('sha256', $token);

        try {
            $db = getDBConnection();
            $stmt = $db->prepare("DELETE FROM remember_tokens WHERE token_hash = :hash");
            $stmt->execute([':hash' => $tokenHash]);
        } catch (Exception $e) {
            // Ignore DB error during logout
        }

        setcookie(REMEMBER_COOKIE_NAME, '', [
            'expires'  => time() - 3600,
            'path'     => '/',
            'domain'   => '',
            'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    }
}
