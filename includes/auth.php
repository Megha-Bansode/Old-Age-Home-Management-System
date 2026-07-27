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
            return BASE_URL . 'modules/super_admin/dashboard.php';
        case 'Admin':
        case 'Old Age Home Admin':
            return BASE_URL . 'modules/admin/dashboard.php';
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
            $_SESSION['logged_in'] = true;
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

        $user_role = $_SESSION['user_role'] ?? $_SESSION['role'] ?? '';
        if (!in_array($user_role, $allowedRoles)) {
            http_response_code(403);
            ?>
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Access Denied — SevaNest</title>
                <!-- Bootstrap 5 -->
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
                <!-- Bootstrap Icons -->
                <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
                <style>
                    body {
                        background-color: #F6F4EC;
                        font-family: 'Outfit', sans-serif;
                        height: 100vh;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        margin: 0;
                    }
                    .error-card {
                        background-color: #ffffff;
                        border: 1px solid #EAE7DC;
                        border-radius: 16px;
                        padding: 40px;
                        text-align: center;
                        max-width: 500px;
                        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
                    }
                    .error-icon {
                        font-size: 4rem;
                        color: #B5563F;
                        margin-bottom: 20px;
                    }
                    .btn-primary {
                        background-color: #2F3A3A;
                        border: none;
                        border-radius: 8px;
                        padding: 10px 24px;
                    }
                    .btn-primary:hover {
                        background-color: #1E2525;
                    }
                </style>
            </head>
            <body>
                <div class="error-card">
                    <i class="bi bi-shield-slash error-icon"></i>
                    <h1 class="h3 fw-bold mb-3 text-dark">Access Denied</h1>
                    <p class="text-muted mb-4">You do not have the required permissions to access this page.</p>
                    <a href="<?php echo BASE_URL; ?>modules/authentication/login.php" class="btn btn-primary">Return to Sign In</a>
                </div>
            </body>
            </html>
            <?php
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

/**
 * Get current authenticated user details
 */
if (!function_exists('current_user')) {
    function current_user() {
        if (!is_logged_in()) {
            return null;
        }
        return [
            'id'            => $_SESSION['user_id'] ?? null,
            'name'          => $_SESSION['user_name'] ?? null,
            'role'          => $_SESSION['user_role'] ?? $_SESSION['role'] ?? null,
            'email'         => $_SESSION['email'] ?? null,
            'profile_photo' => $_SESSION['profile_photo'] ?? null
        ];
    }
}

/**
 * Centralized logout helper
 */
if (!function_exists('logout')) {
    function logout() {
        start_secure_session();
        clear_remember_me_cookie();
        
        $_SESSION = array();
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        
        session_destroy();
    }
}
