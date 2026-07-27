<?php
// ============================================================
// SevaNest - Login Controller API
// ============================================================

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success'    => false,
        'error_type' => 'invalid_request',
        'title'      => 'Invalid Request',
        'message'    => 'Invalid request method.'
    ]);
    exit();
}

$csrfToken = $_POST['csrf_token'] ?? '';
if (!verify_csrf_token($csrfToken)) {
    echo json_encode([
        'success'    => false,
        'error_type' => 'csrf_error',
        'title'      => 'Session Security',
        'message'    => 'CSRF validation failed. Please refresh the page and try again.'
    ]);
    exit();
}

$role       = sanitize_input($_POST['role'] ?? '');
$identifier = sanitize_input($_POST['email'] ?? '');
$password   = $_POST['password'] ?? '';
$rememberMe = isset($_POST['remember_me']) && ($_POST['remember_me'] === '1' || $_POST['remember_me'] === 'true');

// 1. Role Check
if (empty($role)) {
    echo json_encode([
        'success'    => false,
        'error_type' => 'role_mismatch',
        'title'      => 'Role Mismatch',
        'message'    => 'Please select the correct role before logging in.'
    ]);
    exit();
}

// 2. Email Validation
if (empty($identifier)) {
    echo json_encode([
        'success'    => false,
        'error_type' => 'invalid_email',
        'title'      => 'Invalid Email',
        'message'    => 'Please enter a valid email address.'
    ]);
    exit();
}

if (strpos($identifier, '@') !== false && !filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success'    => false,
        'error_type' => 'invalid_email',
        'title'      => 'Invalid Email',
        'message'    => 'Please enter a valid email address.'
    ]);
    exit();
}

// 3. Password Field Check
if (empty($password)) {
    echo json_encode([
        'success'    => false,
        'error_type' => 'wrong_password',
        'title'      => 'Incorrect Password',
        'message'    => 'The password you entered is incorrect.'
    ]);
    exit();
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
        echo json_encode([
            'success'    => false,
            'error_type' => 'email_not_found',
            'title'      => 'Account Not Found',
            'message'    => 'No account exists with this email.'
        ]);
        exit();
    }

    // 5. Verify Password
    if (!password_verify($password, $user['password'])) {
        echo json_encode([
            'success'    => false,
            'error_type' => 'wrong_password',
            'title'      => 'Incorrect Password',
            'message'    => 'The password you entered is incorrect.'
        ]);
        exit();
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
        echo json_encode([
            'success'    => false,
            'error_type' => 'role_mismatch',
            'title'      => 'Role Mismatch',
            'message'    => 'Please select the correct role before logging in.'
        ]);
        exit();
    }

    // 7. Verify Account Status
    if ($user['status'] !== 'active') {
        echo json_encode([
            'success'    => false,
            'error_type' => 'account_disabled',
            'title'      => 'Access Denied',
            'message'    => 'Your account has been disabled. Please contact the administrator.'
        ]);
        exit();
    }

    // 8. Authentication Success - Create Session
    session_regenerate_id(true);
    $_SESSION['user_id']       = $user['id'];
    $_SESSION['user_name']     = $user['full_name'];
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

    echo json_encode([
        'success'    => true,
        'error_type' => 'success',
        'title'      => 'Login Successful',
        'message'    => 'Welcome back!',
        'redirect'   => $redirectUrl
    ]);
    exit();

} catch (Exception $e) {
    echo json_encode([
        'success'    => false,
        'error_type' => 'db_error',
        'title'      => 'System Error',
        'message'    => 'Database error: ' . $e->getMessage()
    ]);
    exit();
}
