<?php
// ============================================================
// SevaNest - Reset Password Controller API
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

// Check reset session authorization
if (empty($_SESSION['reset_authorized']) || empty($_SESSION['reset_user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Session Expired. Please request a new OTP reset link.']);
    exit();
}

$newPassword     = $_POST['new_password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';
$userId          = $_SESSION['reset_user_id'];

// Password Rules Validation
if (strlen($newPassword) < 8) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters long.']);
    exit();
}

if (!preg_match('/[A-Z]/', $newPassword)) {
    echo json_encode(['success' => false, 'message' => 'Password must contain at least one uppercase letter (A-Z).']);
    exit();
}

if (!preg_match('/[a-z]/', $newPassword)) {
    echo json_encode(['success' => false, 'message' => 'Password must contain at least one lowercase letter (a-z).']);
    exit();
}

if (!preg_match('/[0-9]/', $newPassword)) {
    echo json_encode(['success' => false, 'message' => 'Password must contain at least one number (0-9).']);
    exit();
}

if (!preg_match('/[^a-zA-Z0-9]/', $newPassword)) {
    echo json_encode(['success' => false, 'message' => 'Password must contain at least one special character (!@#$%^&*).']);
    exit();
}

if ($newPassword !== $confirmPassword) {
    echo json_encode(['success' => false, 'message' => 'Confirm Password does not match New Password.']);
    exit();
}

try {
    $db = getDBConnection();

    // Hash new password using password_hash BCRYPT
    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

    // Update users database table
    $stmt = $db->prepare("UPDATE users SET password = :hash, updated_at = NOW() WHERE id = :uid");
    $stmt->execute([
        ':hash' => $hashedPassword,
        ':uid'  => $userId
    ]);

    // Invalidate OTP in password_resets
    if (!empty($_SESSION['reset_record_id'])) {
        $upRecord = $db->prepare("UPDATE password_resets SET is_used = 1 WHERE id = :rid");
        $upRecord->execute([':rid' => $_SESSION['reset_record_id']]);
    }

    // Clear reset session
    unset($_SESSION['reset_authorized']);
    unset($_SESSION['reset_record_id']);
    unset($_SESSION['reset_user_id']);
    unset($_SESSION['reset_email']);

    echo json_encode([
        'success' => true,
        'message' => 'Password Updated Successfully! You can now sign in with your new password.'
    ]);
    exit();

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error updating password: ' . $e->getMessage()]);
    exit();
}
