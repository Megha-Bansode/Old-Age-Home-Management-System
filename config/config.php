<?php
// ============================================================
// SevaNest - Configuration File
// ============================================================

if (!defined('SEVANEST_APP')) {
    define('SEVANEST_APP', true);
}

// Environment & Debug mode
define('APP_ENV', 'development'); // 'development' or 'production'
define('APP_NAME', 'SevaNest');
define('APP_TAGLINE', 'Old Age Home Management System');

// Base URL configuration (Auto-detect for Apache XAMPP or CLI)
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
$protocol = $isHttps ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

// Dynamically check folder name in path
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$dirName = 'Old-Age-Home-Management-System';
if (strpos($scriptName, '/Old-Age-Home-Management-System/') !== false) {
    $dirName = 'Old-Age-Home-Management-System';
} elseif (strpos($scriptName, '/project1/') !== false) {
    $dirName = 'project1';
}

define('BASE_URL', $protocol . $host . '/' . $dirName . '/');

// Database credentials
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'sevanest');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Session & Security Settings
define('SESSION_LIFETIME', 1800); // 30 minutes in seconds
define('REMEMBER_COOKIE_NAME', 'sevanest_remember');
define('REMEMBER_COOKIE_LIFETIME', 2592000); // 30 days in seconds
define('CSRF_TOKEN_KEY', 'sevanest_csrf_token');

// Timezone setup
date_default_timezone_set('Asia/Kolkata');

// Error reporting settings based on environment
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}
