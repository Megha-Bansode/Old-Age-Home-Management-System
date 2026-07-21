<?php
// OAHMS Database Connection File using PDO

if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
if (!defined('DB_USER')) define('DB_USER', 'root');
if (!defined('DB_PASS')) define('DB_PASS', '');
if (!defined('DB_NAME')) define('DB_NAME', 'oahms');

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Return json error if it is an API call, otherwise output message
    if (defined('API_MODE')) {
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode(["error" => "Database connection failed: " . $e->getMessage()]);
        exit;
    } else {
        die("<div style='padding: 20px; background: #fee; border: 1px solid #fcc; color: #a00; font-family: sans-serif; border-radius: 6px;'>
            <strong>Database Connection Error!</strong><br>
            Could not connect to the database. Please ensure XAMPP MySQL server is started and the database <code>oahms</code> exists.
            <br><br>Error details: " . htmlspecialchars($e->getMessage()) . "
        </div>");
    }
}
?>
