<?php
// ============================================================
// SevaNest - Database Connection & Auto-Setup (PDO)
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

        // 1. Connect to MySQL server and ensure database exists
        $dsnHost = sprintf("mysql:host=%s;port=%s;charset=%s", DB_HOST, DB_PORT, DB_CHARSET);
        try {
            $pdo = new PDO($dsnHost, DB_USER, DB_PASS, $options);
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo->exec("USE `" . DB_NAME . "`");
        } catch (PDOException $e) {
            $dsnDb = sprintf("mysql:host=%s;port=%s;dbname=%s;charset=%s", DB_HOST, DB_PORT, DB_NAME, DB_CHARSET);
            try {
                $pdo = new PDO($dsnDb, DB_USER, DB_PASS, $options);
            } catch (PDOException $ex) {
                die(json_encode([
                    'success'    => false,
                    'error_type' => 'db_error',
                    'title'      => 'Database Error',
                    'message'    => 'Database connection failed: ' . $ex->getMessage()
                ]));
            }
        }

        // 2. Automatically ensure tables exist and demo users are inserted
        ensure_database_seeded($pdo);
    }

    return $pdo;
}

/**
 * Backward compatibility wrapper for existing pages
 */
function get_db_connection() {
    return getDBConnection();
}

function ensure_database_seeded($pdo) {
    try {
        // Create users table if not exists
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `users` (
              `id` INT AUTO_INCREMENT PRIMARY KEY,
              `full_name` VARCHAR(100) NOT NULL,
              `email` VARCHAR(150) NOT NULL UNIQUE,
              `phone` VARCHAR(20) DEFAULT NULL,
              `password` VARCHAR(255) NOT NULL,
              `role` ENUM('Super Admin', 'Admin', 'Caretaker', 'Doctor', 'Donor', 'Family Member') NOT NULL,
              `status` ENUM('active', 'disabled') NOT NULL DEFAULT 'active',
              `profile_photo` VARCHAR(255) DEFAULT 'default_avatar.png',
              `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
              `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              INDEX `idx_email_role` (`email`, `role`),
              INDEX `idx_status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // Create password_resets table if not exists
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `password_resets` (
              `id` INT AUTO_INCREMENT PRIMARY KEY,
              `user_id` INT NOT NULL,
              `email` VARCHAR(150) NOT NULL,
              `otp` VARCHAR(10) NOT NULL,
              `expires_at` DATETIME NOT NULL,
              `is_used` TINYINT(1) NOT NULL DEFAULT 0,
              `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
              FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
              INDEX `idx_email_otp` (`email`, `otp`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // Create remember_tokens table if not exists
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `remember_tokens` (
              `id` INT AUTO_INCREMENT PRIMARY KEY,
              `user_id` INT NOT NULL,
              `token_hash` VARCHAR(64) NOT NULL,
              `expires_at` DATETIME NOT NULL,
              `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
              FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
              INDEX `idx_token` (`token_hash`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // List of mandatory demo accounts
        $demoUsers = [
            [
                'full_name' => 'Rajesh Sharma',
                'email'     => 'superadmin@sevanest.com',
                'phone'     => '9876543210',
                'password'  => 'Super@123',
                'role'      => 'Super Admin',
                'status'    => 'active'
            ],
            [
                'full_name' => 'Anita Verma',
                'email'     => 'admin@sevanest.com',
                'phone'     => '9876543211',
                'password'  => 'Admin@123',
                'role'      => 'Admin',
                'status'    => 'active'
            ],
            [
                'full_name' => 'Dr. Priya Nair',
                'email'     => 'doctor@sevanest.com',
                'phone'     => '9876543213',
                'password'  => 'Doctor@123',
                'role'      => 'Doctor',
                'status'    => 'active'
            ],
            [
                'full_name' => 'Suresh Kumar',
                'email'     => 'caretaker@sevanest.com',
                'phone'     => '9876543212',
                'password'  => 'Care@123',
                'role'      => 'Caretaker',
                'status'    => 'active'
            ],
            [
                'full_name' => 'Sunita Deshmukh',
                'email'     => 'family@sevanest.com',
                'phone'     => '9876543215',
                'password'  => 'Family@123',
                'role'      => 'Family Member',
                'status'    => 'active'
            ],
            [
                'full_name' => 'Vikramaditya Mehta',
                'email'     => 'donor@sevanest.com',
                'phone'     => '9876543214',
                'password'  => 'Donor@123',
                'role'      => 'Donor',
                'status'    => 'active'
            ],
            [
                'full_name' => 'Ramesh Chandra (Disabled)',
                'email'     => 'disabled@sevanest.com',
                'phone'     => '9876543216',
                'password'  => 'Disabled@123',
                'role'      => 'Caretaker',
                'status'    => 'disabled'
            ]
        ];

        $checkStmt  = $pdo->prepare("SELECT id, password FROM users WHERE email = :email LIMIT 1");
        $insertStmt = $pdo->prepare("
            INSERT INTO users (full_name, email, phone, password, role, status) 
            VALUES (:full_name, :email, :phone, :password, :role, :status)
        ");
        $updateStmt = $pdo->prepare("UPDATE users SET password = :password, role = :role, status = :status WHERE email = :email");

        foreach ($demoUsers as $u) {
            $checkStmt->execute([':email' => $u['email']]);
            $existing = $checkStmt->fetch();

            $hash = password_hash($u['password'], PASSWORD_BCRYPT);

            if (!$existing) {
                $insertStmt->execute([
                    ':full_name' => $u['full_name'],
                    ':email'     => $u['email'],
                    ':phone'     => $u['phone'],
                    ':password'  => $hash,
                    ':role'      => $u['role'],
                    ':status'    => $u['status']
                ]);
            } else {
                // Ensure password hash matches
                if (!password_verify($u['password'], $existing['password'])) {
                    $updateStmt->execute([
                        ':password' => $hash,
                        ':role'     => $u['role'],
                        ':status'   => $u['status'],
                        ':email'    => $u['email']
                    ]);
                }
            }
        }
    } catch (Exception $e) {
        // Suppress background setup errors if database is already configured
    }
}
