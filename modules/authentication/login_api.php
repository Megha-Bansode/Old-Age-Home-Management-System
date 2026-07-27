<?php
// ============================================================
// SevaNest - Login Controller API
// ============================================================

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';

header('Content-Type: application/json; charset=utf-8');

function send_response($success, $error_type, $title, $message, $redirectUrl = null) {
    echo json_encode([
        'success'    => $success,
        'error_type' => $error_type,
        'title'      => $title,
        'message'    => $message,
        'redirect'   => $redirectUrl
    ]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_response(false, 'invalid_request', 'Invalid Request', 'Invalid request method.');
}

$csrfToken = $_POST['csrf_token'] ?? '';
if (!verify_csrf_token($csrfToken)) {
    send_response(false, 'csrf_error', 'Session Security', 'CSRF validation failed. Please refresh the page and try again.');
}

$role       = sanitize_input($_POST['role'] ?? '');
$identifier = sanitize_input($_POST['email'] ?? '');
$password   = $_POST['password'] ?? '';
$rememberMe = isset($_POST['remember_me']) && ($_POST['remember_me'] === '1' || $_POST['remember_me'] === 'true' || $_POST['remember_me'] === 'on');

// 1. Inputs validation
if (empty($role)) {
    send_response(false, 'role_mismatch', 'Role Required', 'Please select your role before signing in.');
}

if (empty($identifier)) {
    send_response(false, 'invalid_email', 'Identifier Required', 'Please enter your email address or phone number.');
}

if (empty($password)) {
    send_response(false, 'wrong_password', 'Password Required', 'Please enter your password.');
}

try {
    $db = getDBConnection();

    // 2. Fetch User
    $stmt = $db->prepare("
        SELECT id, full_name, email, phone, password, role, status, profile_photo 
        FROM users 
        WHERE (email = :email OR phone = :phone) 
        LIMIT 1
    ");
    $stmt->execute([
        ':email' => $identifier,
        ':phone' => $identifier
    ]);
    $user = $stmt->fetch();

    if (!$user) {
        send_response(false, 'email_not_found', 'Account Not Found', 'No account exists with this email.');
    }

    // 3. Password Verification
    if (!password_verify($password, $user['password'])) {
        send_response(false, 'wrong_password', 'Incorrect Password', 'The password you entered is incorrect.');
    }

    // 4. Role Comparison (Direct string match)
    if ($user['role'] !== $role) {
        send_response(false, 'role_mismatch', 'Role Mismatch', 'Please select the correct role before logging in.');
    }

    // 5. Account Status Check
    if ($user['status'] !== 'active') {
        send_response(false, 'account_disabled', 'Access Denied', 'Your account has been disabled. Please contact the administrator.');
    }

    // 6. Session Creation
    session_regenerate_id(true);
    $_SESSION['user_id']       = $user['id'];
    $_SESSION['user_name']     = $user['full_name'];
    $_SESSION['user_role']     = $user['role'];
    $_SESSION['logged_in']      = true;
    $_SESSION['full_name']     = $user['full_name'];
    $_SESSION['email']         = $user['email'];
    $_SESSION['role']          = $user['role'];
    $_SESSION['profile_photo'] = $user['profile_photo'];
    $_SESSION['last_activity'] = time();
    $_SESSION['created_at']    = time();

    // 7. Remember Me Cookie
    if ($rememberMe) {
        set_remember_me_cookie($user['id']);
    }

    $redirectUrl = get_dashboard_url($user['role']);
    send_response(true, 'success', 'Login Successful', 'Welcome back!', $redirectUrl);

} catch (Exception $e) {
    send_response(false, 'db_error', 'System Error', 'Database error: ' . $e->getMessage());
}
