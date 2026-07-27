<?php
/**
 * SevaNest — Super Admin Module Entry Point
 */
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';

require_login();
require_role('Super Admin');

header('Location: dashboard.php');
exit;
