<?php
/**
 * SevaNest – Session Management Helper
 * File     : includes/session.php
 * Version  : 1.0
 */

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'path' => '/',
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

/**
 * Checks if the current user is logged in.
 *
 * @return bool
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) || isset($_SESSION['user_name']);
}

/**
 * Restricts access to logged-in users. Redirects to login page if not authenticated.
 *
 * @param string $login_url
 */
function require_login($login_url = '../../modules/authentication/login.php') {
    if (!is_logged_in()) {
        if (function_exists('log_error')) {
            log_error("Authentication check failed. Session ID: " . session_id() . ", Session data: " . json_encode($_SESSION));
        }
        header("Location: $login_url");
        exit;
    }
}
