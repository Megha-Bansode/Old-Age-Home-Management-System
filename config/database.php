<?php
// ============================================================
// SevaNest - Database Connection (PDO)
// ============================================================

require_once __DIR__ . '/config.php';

function getDBConnection() {
    static $pdo = null;

    if ($pdo === null) {
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ];

        $dsnDb = sprintf("mysql:host=%s;port=%s;dbname=%s;charset=%s", DB_HOST, DB_PORT, DB_NAME, DB_CHARSET);
        try {
            $pdo = new PDO($dsnDb, DB_USER, DB_PASS, $options);
        } catch (PDOException $ex) {
            throw new Exception('Database connection failed: ' . $ex->getMessage());
        }
    }

    return $pdo;
}

/**
 * Backward compatibility wrapper for existing pages
 */
if (!function_exists('get_db_connection')) {
    function get_db_connection() {
        return getDBConnection();
    }
}
