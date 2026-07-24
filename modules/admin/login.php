<?php
/**
 * Admin dashboard login (page-based, separate from auth/login.php which
 * is the JSON API used by the public "Log in" button on the homepage).
 */
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../config/database.php';

if (is_logged_in() && ($_SESSION['role'] ?? '') === 'admin') {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = clean_str($_POST['email'] ?? '');
    $password = (string) ($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $error = 'Please enter both email and password.';
    } else {
        try {
            $pdo = get_db_connection();
            $stmt = $pdo->prepare('SELECT id, full_name, email, password_hash, role FROM users WHERE email = ?');
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash']) && $user['role'] === 'admin') {
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['role']      = $user['role'];
                $_SESSION['full_name'] = $user['full_name'];
                header('Location: index.php');
                exit;
            } else {
                $error = 'Invalid credentials, or this account does not have admin access.';
            }
        } catch (PDOException $e) {
            log_error('dashboard/login.php: ' . $e->getMessage());
            $error = 'Something went wrong. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login — SevaNest</title>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@500;600&family=Manrope:wght@400;600;700&display=swap" rel="stylesheet">
<style>
  *{box-sizing:border-box; margin:0; padding:0;}
  body{font-family:'Manrope',sans-serif; background:#F6F4EC; color:#2F3A3A; min-height:100vh; display:flex; align-items:center; justify-content:center;}
  .card{background:#fff; border-radius:20px; padding:44px; width:100%; max-width:380px; box-shadow:0 20px 50px -25px rgba(47,58,58,.35); border:1px solid rgba(47,58,58,.1);}
  h1{font-family:'Fraunces',serif; font-weight:600; font-size:24px; margin-bottom:6px;}
  p.sub{font-size:13.5px; color:#5C6B64; margin-bottom:26px;}
  label{font-size:12.5px; font-weight:700; color:#5C6B64; display:block; margin:16px 0 7px;}
  label:first-of-type{margin-top:0;}
  input{width:100%; padding:13px 15px; border-radius:12px; border:1.5px solid rgba(47,58,58,.1); font-family:inherit; font-size:14px;}
  input:focus{outline:none; border-color:#6B9080;}
  button{width:100%; margin-top:24px; padding:14px; border-radius:100px; border:none; background:#6B9080; color:#fff; font-weight:700; font-size:14.5px; cursor:pointer;}
  button:hover{background:#4F7161;}
  .error{background:rgba(196,90,80,.1); color:#a8443a; padding:12px 14px; border-radius:10px; font-size:13.5px; margin-bottom:16px;}
  .hint{font-size:12px; color:#8b968f; margin-top:20px; text-align:center;}
</style>
</head>
<body>
  <div class="card">
    <h1>Admin Dashboard</h1>
    <p class="sub">Sign in to manage volunteers, messages, and donations.</p>
    <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="POST">
      <label for="email">Email</label>
      <input type="email" id="email" name="email" placeholder="admin@sevanest.org" required>
      <label for="password">Password</label>
      <input type="password" id="password" name="password" placeholder="••••••••" required>
      <button type="submit">Log in</button>
    </form>
    <p class="hint">Default seed login: admin@sevanest.org / Admin@123</p>
  </div>
</body>
</html>
