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

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../config/database.php';

try {
    $pdo = getDBConnection();
} catch (Exception $ex) {
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed: ' . $ex->getMessage()
    ]);
    exit;
}

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
                $params[] = ($status === 'Active') ? 'active' : 'disabled';
            }

            $where_sql = count($where_clauses) > 0 ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

            // Total filtered records
            $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM users {$where_sql}");
            $count_stmt->execute($params);
            $total_records = (int)$count_stmt->fetchColumn();

            // Fetch page records
            $data_stmt = $pdo->prepare("SELECT id, full_name AS name, email, phone, gender, role, status, address, profile_photo, DATE_FORMAT(created_at, '%Y-%m-%d') AS created_at FROM users {$where_sql} ORDER BY id DESC LIMIT {$limit} OFFSET {$offset}");
            $data_stmt->execute($params);
            $users = $data_stmt->fetchAll();

            foreach ($users as &$u) {
                $u['status'] = ($u['status'] === 'active') ? 'Active' : 'Inactive';
                $u['photo']  = $u['profile_photo']; // Align frontend javascript expectations
            }

            // Overall Stats calculation
            $total_users    = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
            $active_users   = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE status = 'active'")->fetchColumn();
            $inactive_users = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE status = 'disabled'")->fetchColumn();
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

            $stmt = $pdo->prepare("SELECT id, full_name AS name, email, phone, gender, role, status, address, profile_photo, DATE_FORMAT(created_at, '%Y-%m-%d') AS created_at FROM users WHERE id = ?");
            $stmt->execute([$id]);
            $user = $stmt->fetch();

            if (!$user) {
                throw new Exception('User record not found.');
            }

            $user['status'] = ($user['status'] === 'active') ? 'Active' : 'Inactive';
            $user['photo']  = $user['profile_photo']; // Align frontend javascript expectations

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
            $photo_path = handle_profile_upload('photo_file') ?? 'default_avatar.png';

            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $db_status = ($status === 'Active') ? 'active' : 'disabled';

            $insert_stmt = $pdo->prepare("INSERT INTO users (full_name, email, phone, gender, role, status, address, password, profile_photo, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $insert_stmt->execute([$full_name, $email, $phone, $gender, $role, $db_status, $address, $hashed_password, $photo_path]);
            
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
            $db_status = ($status === 'Active') ? 'active' : 'disabled';

            if ($photo_path !== null) {
                if (!empty($password)) {
                    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                    $update_stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, gender = ?, role = ?, status = ?, address = ?, password = ?, profile_photo = ? WHERE id = ?");
                    $update_stmt->execute([$full_name, $email, $phone, $gender, $role, $db_status, $address, $hashed_password, $photo_path, $id]);
                } else {
                    $update_stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, gender = ?, role = ?, status = ?, address = ?, profile_photo = ? WHERE id = ?");
                    $update_stmt->execute([$full_name, $email, $phone, $gender, $role, $db_status, $address, $photo_path, $id]);
                }
            } else {
                if (!empty($password)) {
                    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                    $update_stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, gender = ?, role = ?, status = ?, address = ?, password = ? WHERE id = ?");
                    $update_stmt->execute([$full_name, $email, $phone, $gender, $role, $db_status, $address, $hashed_password, $id]);
                } else {
                    $update_stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, gender = ?, role = ?, status = ?, address = ? WHERE id = ?");
                    $update_stmt->execute([$full_name, $email, $phone, $gender, $role, $db_status, $address, $id]);
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

            $new_db_status = ($user['status'] === 'active') ? 'disabled' : 'active';
            $update_stmt = $pdo->prepare("UPDATE users SET status = ? WHERE id = ?");
            $update_stmt->execute([$new_db_status, $id]);

            $new_status = ($new_db_status === 'active') ? 'Active' : 'Inactive';

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
