<?php
/**
 * SevaNest – Shared Core Functions & Utilities
 * File     : includes/functions.php
 * Version  : 1.0
 */

/**
 * Sanitizes input string to prevent XSS.
 *
 * @param string $str
 * @return string
 */
function clean_str($str) {
    return htmlspecialchars(trim((string)$str), ENT_QUOTES, 'UTF-8');
}

/**
 * Logs errors to the custom log file or system log.
 *
 * @param string $msg
 */
function log_error($msg) {
    $log_dir = __DIR__ . '/../logs';
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0777, true);
    }
    $log_file = $log_dir . '/error.log';
    $timestamp = date('[Y-m-d H:i:s]');
    error_log("$timestamp $msg\n", 3, $log_file);
}
