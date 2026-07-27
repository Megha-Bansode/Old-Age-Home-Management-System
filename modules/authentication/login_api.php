<?php
// ============================================================
// SevaNest - Login Controller API (Frontend Mock Routing)
// ============================================================

header('Content-Type: application/json');

$role       = $_POST['role'] ?? '';
$identifier = $_POST['email'] ?? '';
$password   = $_POST['password'] ?? '';

// Basic checks to match JavaScript expectations
if (empty($role)) {
    echo json_encode([
        'success'    => false,
        'error_type' => 'role_mismatch',
        'title'      => 'Role Mismatch',
        'message'    => 'Please select the correct role before logging in.'
    ]);
    exit();
}

if (empty($identifier)) {
    echo json_encode([
        'success'    => false,
        'error_type' => 'invalid_email',
        'title'      => 'Invalid Email',
        'message'    => 'Please enter a valid email address.'
    ]);
    exit();
}

if (empty($password)) {
    echo json_encode([
        'success'    => false,
        'error_type' => 'wrong_password',
        'title'      => 'Incorrect Password',
        'message'    => 'The password you entered is incorrect.'
    ]);
    exit();
}

// Map the role value to the correct folder prefix
$redirect = '';
if ($role === 'Super Admin') {
    $redirect = '../super_admin/index.php';
} elseif ($role === 'Old Age Home Admin') {
    $redirect = '../admin/index.php';
} elseif ($role === 'Doctor') {
    $redirect = '../doctor/index.php';
} elseif ($role === 'Caretaker') {
    $redirect = '../caretaker/index.php';
} elseif ($role === 'Family Member') {
    $redirect = '../family/index.php';
} elseif ($role === 'Donor') {
    $redirect = '../donor/index.php';
} else {
    $redirect = '../admin/index.php'; // fallback
}

echo json_encode([
    'success'  => true,
    'title'    => 'Login Successful',
    'message'  => 'Welcome to SevaNest dashboard!',
    'redirect' => $redirect
]);
exit();
