<?php
/*=============================================================================
    OLD AGE HOME MANAGEMENT SYSTEM
    Module: Super Admin - User Management API Endpoint
    File: user_api.php
    Description: PDO Database Backend for User Management CRUD, Searching,
                 Filtering, Image Uploads, and Statistics.
=============================================================================*/

header('Content-Type: application/json; charset=utf-8');

// Suppress raw errors in production JSON output
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', '0');

// Database Connection Helper
function get_pdo_connection() {
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    $host = 'localhost';
    $dbname = 'sevanest';
    $username = 'root';
    $password = '';
    $charset = 'utf8mb4';

    try {
        $dsn = "mysql:host={$host};dbname={$dbname};charset={$charset}";
        $pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        // Fallback: Attempt without dbname to create database if not exists
        try {
            $dsn_no_db = "mysql:host={$host};charset={$charset}";
            $pdo_init = new PDO($dsn_no_db, $username, $password);
            $pdo_init->exec("CREATE DATABASE IF NOT EXISTS `{$dbname}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
            
            $dsn = "mysql:host={$host};dbname={$dbname};charset={$charset}";
            $pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
            return $pdo;
        } catch (PDOException $ex) {
            echo json_encode([
                'success' => false,
                'message' => 'Database connection failed: ' . $ex->getMessage()
            ]);
            exit;
        }
    }
}

$pdo = get_pdo_connection();

// Ensure users table exists with proper schema
function ensure_users_table($pdo) {
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(150) NOT NULL,
        email VARCHAR(150) NOT NULL UNIQUE,
        phone VARCHAR(50) DEFAULT NULL,
        gender VARCHAR(20) DEFAULT 'Male',
        role VARCHAR(50) NOT NULL DEFAULT 'Staff',
        status VARCHAR(20) NOT NULL DEFAULT 'Active',
        address TEXT DEFAULT NULL,
        password_hash VARCHAR(255) NOT NULL,
        photo VARCHAR(255) DEFAULT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    $pdo->exec($sql);

    // Insert sample seed users if table is freshly created and empty
    $count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    if ($count == 0) {
        $seed_password = password_hash('Admin@123', PASSWORD_BCRYPT);
        $seed_sql = "INSERT INTO users (full_name, email, phone, gender, role, status, address, password_hash, created_at) VALUES
        ('Dr. Rajesh Sharma', 'rajesh.sharma@oldcare.org', '9876543210', 'Male', 'Doctor', 'Active', 'Suite 4B, Care Avenue, Mumbai, MH', '{$seed_password}', '2024-01-15 10:00:00'),
        ('Sunita Verma', 'sunita.v@oldcare.org', '9812345678', 'Female', 'Nurse', 'Active', 'Block C, Green Park, New Delhi', '{$seed_password}', '2024-02-01 11:30:00'),
        ('Vikramaditya Roy', 'admin@sevanest.org', '9900011223', 'Male', 'Super Admin', 'Active', '7th Floor, Admin Tower, Bangalore, KA', '{$seed_password}', '2023-11-10 09:15:00'),
        ('Ananya Deshmukh', 'ananya.d@oldcare.org', '9765432109', 'Female', 'Caretaker', 'Active', 'House 24, Sunshine Colony, Pune, MH', '{$seed_password}', '2024-03-12 14:20:00'),
        ('Ramesh Pawar', 'ramesh.p@oldcare.org', '9123456789', 'Male', 'Staff', 'Inactive', 'Plot 12, Riverbed Road, Nagpur, MH', '{$seed_password}', '2024-03-20 16:45:00'),
        ('Meenakshi Sundaram', 'meenakshi.s@oldcare.org', '9444455555', 'Female', 'Admin', 'Active', '55 Temple Street, Chennai, TN', '{$seed_password}', '2024-04-05 12:10:00');";
        $pdo->exec($seed_sql);
    }
}

ensure_users_table($pdo);

// Upload helper function
function handle_profile_upload($file_input_name) {
    if (!isset($_FILES[$file_input_name]) || $_FILES[$file_input_name]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $file = $_FILES[$file_input_name];
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];
    $max_size = 2 * 1024 * 1024; // 2MB

    if ($file['size'] > $max_size) {
        throw new Exception('Uploaded image size must be less than 2MB.');
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_extensions)) {
        throw new Exception('Only JPG, JPEG, PNG, and WEBP image formats are supported.');
    }

    $upload_dir = __DIR__ . '/../../../uploads/users/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $filename = 'user_' . time() . '_' . uniqid() . '.' . $ext;
    $target_filepath = $upload_dir . $filename;

    if (!move_uploaded_file($file['tmp_path'] ?? $file['tmp_name'], $target_filepath)) {
        throw new Exception('Failed to save uploaded image file.');
    }

    return '../../../uploads/users/' . $filename;
}

$action = $_GET['action'] ?? $_POST['action'] ?? 'list';

try {
    switch ($action) {

        /* ---------------------------------------------------------------------
           ACTION 1: FETCH / LIST USERS (WITH SEARCH, FILTERS & STATS)
        --------------------------------------------------------------------- */
        case 'list':
            $search = trim($_GET['search'] ?? '');
            $role   = trim($_GET['role'] ?? 'All');
            $status = trim($_GET['status'] ?? 'All');
            $page   = max(1, (int)($_GET['page'] ?? 1));
            $limit  = max(1, (int)($_GET['limit'] ?? 10));
            $offset = ($page - 1) * $limit;

            $where_clauses = [];
            $params = [];

            if ($search !== '') {
                $where_clauses[] = "(full_name LIKE ? OR email LIKE ? OR phone LIKE ?)";
                $search_param = '%' . $search . '%';
                $params[] = $search_param;
                $params[] = $search_param;
                $params[] = $search_param;
            }

            if ($role !== 'All' && $role !== '') {
                $where_clauses[] = "role = ?";
                $params[] = $role;
            }

            if ($status !== 'All' && $status !== '') {
                $where_clauses[] = "status = ?";
                $params[] = $status;
            }

            $where_sql = count($where_clauses) > 0 ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

            // Total filtered records
            $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM users {$where_sql}");
            $count_stmt->execute($params);
            $total_records = (int)$count_stmt->fetchColumn();

            // Fetch page records
            $data_stmt = $pdo->prepare("SELECT id, full_name AS name, email, phone, gender, role, status, address, photo, DATE_FORMAT(created_at, '%Y-%m-%d') AS created_at FROM users {$where_sql} ORDER BY id DESC LIMIT {$limit} OFFSET {$offset}");
            $data_stmt->execute($params);
            $users = $data_stmt->fetchAll();

            // Overall Stats calculation
            $total_users    = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
            $active_users   = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE status = 'Active'")->fetchColumn();
            $inactive_users = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE status = 'Inactive'")->fetchColumn();
            $total_roles    = (int)$pdo->query("SELECT COUNT(DISTINCT role) FROM users")->fetchColumn();

            echo json_encode([
                'success' => true,
                'data'    => $users,
                'total'   => $total_records,
                'page'    => $page,
                'limit'   => $limit,
                'stats'   => [
                    'total_users'    => $total_users,
                    'active_users'   => $active_users,
                    'inactive_users' => $inactive_users,
                    'total_roles'    => $total_roles
                ]
            ]);
            break;

        /* ---------------------------------------------------------------------
           ACTION 2: GET SINGLE USER BY ID
        --------------------------------------------------------------------- */
        case 'get':
            $id = (int)($_GET['id'] ?? 0);
            if ($id <= 0) {
                throw new Exception('Invalid user ID provided.');
            }

            $stmt = $pdo->prepare("SELECT id, full_name AS name, email, phone, gender, role, status, address, photo, DATE_FORMAT(created_at, '%Y-%m-%d') AS created_at FROM users WHERE id = ?");
            $stmt->execute([$id]);
            $user = $stmt->fetch();

            if (!$user) {
                throw new Exception('User record not found.');
            }

            echo json_encode([
                'success' => true,
                'data'    => $user
            ]);
            break;

        /* ---------------------------------------------------------------------
           ACTION 3: ADD NEW USER
        --------------------------------------------------------------------- */
        case 'add':
            $full_name = trim($_POST['full_name'] ?? '');
            $email     = trim($_POST['email'] ?? '');
            $phone     = trim($_POST['phone'] ?? '');
            $gender    = trim($_POST['gender'] ?? 'Male');
            $role      = trim($_POST['role'] ?? '');
            $status    = trim($_POST['status'] ?? 'Active');
            $address   = trim($_POST['address'] ?? '');
            $password  = $_POST['password'] ?? 'User@123';

            if (empty($full_name)) throw new Exception('Full Name is required.');
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) throw new Exception('Please enter a valid email address.');
            if (empty($phone)) throw new Exception('Phone number is required.');
            if (empty($role)) throw new Exception('System role is required.');

            // Check duplicate email
            $dup_stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
            $dup_stmt->execute([$email]);
            if ($dup_stmt->fetch()) {
                throw new Exception('A user with this email address already exists.');
            }

            // Upload profile photo if present
            $photo_path = handle_profile_upload('photo_file');

            $password_hash = password_hash($password, PASSWORD_BCRYPT);

            $insert_stmt = $pdo->prepare("INSERT INTO users (full_name, email, phone, gender, role, status, address, password_hash, photo, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $insert_stmt->execute([$full_name, $email, $phone, $gender, $role, $status, $address, $password_hash, $photo_path]);
            
            $new_id = $pdo->lastInsertId();

            echo json_encode([
                'success' => true,
                'message' => "User {$full_name} created successfully!",
                'user_id' => $new_id
            ]);
            break;

        /* ---------------------------------------------------------------------
           ACTION 4: EDIT / UPDATE USER
        --------------------------------------------------------------------- */
        case 'edit':
            $id        = (int)($_POST['id'] ?? 0);
            $full_name = trim($_POST['full_name'] ?? '');
            $email     = trim($_POST['email'] ?? '');
            $phone     = trim($_POST['phone'] ?? '');
            $gender    = trim($_POST['gender'] ?? 'Male');
            $role      = trim($_POST['role'] ?? '');
            $status    = trim($_POST['status'] ?? 'Active');
            $address   = trim($_POST['address'] ?? '');
            $password  = $_POST['password'] ?? '';

            if ($id <= 0) throw new Exception('Invalid user ID for update.');
            if (empty($full_name)) throw new Exception('Full Name is required.');
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) throw new Exception('Please enter a valid email address.');
            if (empty($phone)) throw new Exception('Phone number is required.');
            if (empty($role)) throw new Exception('System role is required.');

            // Check duplicate email for other user ID
            $dup_stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ? LIMIT 1");
            $dup_stmt->execute([$email, $id]);
            if ($dup_stmt->fetch()) {
                throw new Exception('Another user is already registered with this email address.');
            }

            // Check for new uploaded profile photo
            $photo_path = handle_profile_upload('photo_file');

            if ($photo_path !== null) {
                if (!empty($password)) {
                    $password_hash = password_hash($password, PASSWORD_BCRYPT);
                    $update_stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, gender = ?, role = ?, status = ?, address = ?, password_hash = ?, photo = ? WHERE id = ?");
                    $update_stmt->execute([$full_name, $email, $phone, $gender, $role, $status, $address, $password_hash, $photo_path, $id]);
                } else {
                    $update_stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, gender = ?, role = ?, status = ?, address = ?, photo = ? WHERE id = ?");
                    $update_stmt->execute([$full_name, $email, $phone, $gender, $role, $status, $address, $photo_path, $id]);
                }
            } else {
                if (!empty($password)) {
                    $password_hash = password_hash($password, PASSWORD_BCRYPT);
                    $update_stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, gender = ?, role = ?, status = ?, address = ?, password_hash = ? WHERE id = ?");
                    $update_stmt->execute([$full_name, $email, $phone, $gender, $role, $status, $address, $password_hash, $id]);
                } else {
                    $update_stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, gender = ?, role = ?, status = ?, address = ? WHERE id = ?");
                    $update_stmt->execute([$full_name, $email, $phone, $gender, $role, $status, $address, $id]);
                }
            }

            echo json_encode([
                'success' => true,
                'message' => "User profile updated successfully!"
            ]);
            break;

        /* ---------------------------------------------------------------------
           ACTION 5: TOGGLE USER STATUS (ACTIVE / INACTIVE)
        --------------------------------------------------------------------- */
        case 'toggle_status':
            $id = (int)($_POST['id'] ?? 0);
            if ($id <= 0) throw new Exception('Invalid user ID.');

            $stmt = $pdo->prepare("SELECT status, full_name FROM users WHERE id = ?");
            $stmt->execute([$id]);
            $user = $stmt->fetch();

            if (!$user) throw new Exception('User not found.');

            $new_status = ($user['status'] === 'Active') ? 'Inactive' : 'Active';
            $update_stmt = $pdo->prepare("UPDATE users SET status = ? WHERE id = ?");
            $update_stmt->execute([$new_status, $id]);

            echo json_encode([
                'success'    => true,
                'new_status' => $new_status,
                'message'    => "User status updated to {$new_status}"
            ]);
            break;

        /* ---------------------------------------------------------------------
           ACTION 6: DELETE USER
        --------------------------------------------------------------------- */
        case 'delete':
            $id = (int)($_POST['id'] ?? 0);
            if ($id <= 0) throw new Exception('Invalid user ID for deletion.');

            $delete_stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $delete_stmt->execute([$id]);

            echo json_encode([
                'success' => true,
                'message' => 'User account has been permanently deleted.'
            ]);
            break;

        default:
            throw new Exception('Unsupported API action.');
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
