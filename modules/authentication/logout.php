<?php
// ============================================================
// SevaNest - Logout Controller
// ============================================================

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';

logout();

// Redirect to login page with success message
header("Location: " . BASE_URL . "modules/authentication/login.php?msg=logged_out");
exit();
