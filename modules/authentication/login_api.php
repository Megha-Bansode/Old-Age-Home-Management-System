<?php
// ============================================================
// SevaNest - Login Controller API
// ============================================================

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';

function send_response($success, $error_type, $title, $message, $redirectUrl = null) {
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode([
            'success'    => $success,
            'error_type' => $error_type,
            'title'      => $title,
            'message'    => $message,
            'redirect'   => $redirectUrl
        ]);
    } else {
        if ($success) {
            header("Location: " . $redirectUrl);
        } else {
            header("Location: login.php?error=" . urlencode($error_type));
        }
    }
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
$rememberMe = isset($_POST['remember_me']) && ($_POST['remember_me'] === '1' || $_POST['remember_me'] === 'true');

// 1. Role Check
if (empty($role)) {
    send_response(false, 'role_mismatch', 'Role Mismatch', 'Please select the correct role before logging in.');
}

// 2. Email Validation
if (empty($identifier)) {
    send_response(false, 'invalid_email', 'Invalid Email', 'Please enter a valid email address.');
}

// 3. Password Field Check
if (empty($password)) {
    send_response(false, 'wrong_password', 'Incorrect Password', 'The password you entered is incorrect.');
}

try {
    $db = getDBConnection();

    // Query user using distinct parameters for email and phone to avoid SQLSTATE[HY093]
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

    // 4. Verify Account Exists
    if (!$user) {
        send_response(false, 'email_not_found', 'Account Not Found', 'No account exists with this email.');
    }

    // 5. Verify Password
    if (!password_verify($password, $user['password'])) {
        send_response(false, 'wrong_password', 'Incorrect Password', 'The password you entered is incorrect.');
    }

    // Normalize incoming role name from radio input to DB enum format
    $normalized_role = $user['role'];
    $incoming_role = $role;
    // Map input display name (e.g. 'Old Age Home Admin') to DB enum (e.g. 'Admin') if necessary
    if ($incoming_role === 'Old Age Home Admin') {
         $incoming_role = 'Admin';
    }
    if ($incoming_role === 'Family') {
         $incoming_role = 'Family Member';
    }

    // 6. Verify Selected Role
    if ($user['role'] !== $incoming_role) {
        send_response(false, 'role_mismatch', 'Role Mismatch', 'Please select the correct role before logging in.');
    }

    // 7. Verify Account Status
    if ($user['status'] !== 'active') {
        send_response(false, 'account_disabled', 'Access Denied', 'Your account has been disabled. Please contact the administrator.');
    }

    // 8. Authentication Success - Create Session
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

    // Save Remember Me cookie if checked
    if ($rememberMe) {
        set_remember_me_cookie($user['id']);
    }

    $redirectUrl = get_dashboard_url($user['role']);

    send_response(true, 'success', 'Login Successful', 'Welcome back!', $redirectUrl);

} catch (Exception $e) {
    send_response(false, 'db_error', 'System Error', 'Database error: ' . $e->getMessage());
}
