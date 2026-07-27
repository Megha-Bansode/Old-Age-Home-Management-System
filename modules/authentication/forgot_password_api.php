<?php
// ============================================================
// SevaNest - Forgot Password & OTP Controller API
// ============================================================

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit();
}

$csrfToken = $_POST['csrf_token'] ?? '';
if (!verify_csrf_token($csrfToken)) {
    echo json_encode(['success' => false, 'message' => 'CSRF validation failed. Refresh and try again.']);
    exit();
}

$action = sanitize_input($_POST['action'] ?? '');
$email  = sanitize_input($_POST['email'] ?? '');

$db = getDBConnection();

if ($action === 'send_otp') {
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
        exit();
    }

    // Verify user exists and is active
    $stmt = $db->prepare("SELECT id, full_name, status FROM users WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'Email Not Found. No account associated with this email.']);
        exit();
    }

    if ($user['status'] !== 'active') {
        echo json_encode(['success' => false, 'message' => 'Account Disabled. Contact administration for password reset.']);
        exit();
    }

    // Generate 6-digit OTP
    $otp = sprintf('%06d', random_int(100000, 999999));
    $expiresAt = date('Y-m-d H:i:s', time() + (15 * 60)); // 15 mins expiry

    // Invalidate old OTPs for user
    $delStmt = $db->prepare("DELETE FROM password_resets WHERE user_id = :uid");
    $delStmt->execute([':uid' => $user['id']]);

    // Save new OTP
    $insStmt = $db->prepare("
        INSERT INTO password_resets (user_id, email, otp, expires_at, is_used) 
        VALUES (:uid, :email, :otp, :expires, 0)
    ");
    $insStmt->execute([
        ':uid'     => $user['id'],
        ':email'   => $email,
        ':otp'     => $otp,
        ':expires' => $expiresAt
    ]);

    // Store temporary session data
    $_SESSION['reset_user_id'] = $user['id'];
    $_SESSION['reset_email']   = $email;

    // Mailer notice / Debug display
    $demoNote = (APP_ENV === 'development') ? " (Demo OTP: {$otp})" : "";

    echo json_encode([
        'success' => true,
        'message' => "6-digit OTP code sent to {$email}.{$demoNote}",
        'otp_demo' => $otp
    ]);
    exit();
}

if ($action === 'verify_otp') {
    $otpInput = sanitize_input($_POST['otp'] ?? '');

    if (empty($otpInput) || strlen($otpInput) !== 6) {
        echo json_encode(['success' => false, 'message' => 'Please enter the valid 6-digit OTP.']);
        exit();
    }

    $stmt = $db->prepare("
        SELECT id, user_id, expires_at, is_used 
        FROM password_resets 
        WHERE email = :email AND otp = :otp AND is_used = 0 
        ORDER BY id DESC LIMIT 1
    ");
    $stmt->execute([':email' => $email, ':otp' => $otpInput]);
    $resetRecord = $stmt->fetch();

    if (!$resetRecord) {
        echo json_encode(['success' => false, 'message' => 'OTP Incorrect. Please check the 6-digit code and try again.']);
        exit();
    }

    if (strtotime($resetRecord['expires_at']) < time()) {
        echo json_encode(['success' => false, 'message' => 'OTP Expired. Please request a new password reset link.']);
        exit();
    }

    // OTP Verified successfully
    $_SESSION['reset_authorized'] = true;
    $_SESSION['reset_record_id']  = $resetRecord['id'];
    $_SESSION['reset_user_id']     = $resetRecord['user_id'];
    $_SESSION['reset_email']       = $email;

    echo json_encode([
        'success' => true,
        'message' => 'OTP verified successfully! Please enter your new password.'
    ]);
    exit();
}

echo json_encode(['success' => false, 'message' => 'Invalid action specified.']);
exit();
