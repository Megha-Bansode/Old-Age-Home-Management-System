<?php
/**
 * Admin dashboard - lists the data submitted through the public site:
 * volunteer sign-ups, contact messages, donations, and visit bookings.
 */
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../config/database.php';

if (!is_logged_in() || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: login.php');
    exit;
}

$pdo = get_db_connection();

$volunteers = $pdo->query('SELECT * FROM volunteers ORDER BY created_at DESC LIMIT 25')->fetchAll();
$messages   = $pdo->query('SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 25')->fetchAll();
$donations  = $pdo->query('SELECT * FROM donations ORDER BY created_at DESC LIMIT 25')->fetchAll();
$bookings   = $pdo->query(
    'SELECT vb.*, h.name AS home_name FROM visit_bookings vb
     LEFT JOIN homes h ON h.id = vb.home_id ORDER BY vb.created_at DESC LIMIT 25'
)->fetchAll();

$totalVolunteers = $pdo->query('SELECT COUNT(*) FROM volunteers')->fetchColumn();
$totalMessages   = $pdo->query('SELECT COUNT(*) FROM contact_messages')->fetchColumn();
$totalDonations  = $pdo->query("SELECT COALESCE(SUM(amount),0) FROM donations WHERE payment_status='success'")->fetchColumn();
$totalBookings   = $pdo->query('SELECT COUNT(*) FROM visit_bookings')->fetchColumn();

function e($v) { return htmlspecialchars((string) $v, ENT_QUOTES); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard — SevaNest</title>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@500;600&family=Manrope:wght@400;600;700;800&display=swap" rel="stylesheet">
<style>
  *{box-sizing:border-box; margin:0; padding:0;}
  body{font-family:'Manrope',sans-serif; background:#F6F4EC; color:#2F3A3A;}
  .topbar{background:#2F3A3A; color:#fff; padding:18px 32px; display:flex; justify-content:space-between; align-items:center;}
  .topbar .brand{font-family:'Fraunces',serif; font-size:19px; font-weight:600;}
  .topbar a{color:rgba(255,255,255,.7); font-size:13.5px; font-weight:700;}
  .topbar a:hover{color:#D4A373;}
  .wrap{max-width:1180px; margin:0 auto; padding:36px 32px 60px;}
  h1{font-family:'Fraunces',serif; font-weight:600; font-size:26px; margin-bottom:26px;}
  .stat-grid{display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:40px;}
  .stat-card{background:#fff; border-radius:16px; padding:22px; border:1px solid rgba(47,58,58,.1);}
  .stat-card .n{font-family:'Fraunces',serif; font-size:28px; font-weight:600; color:#4F7161;}
  .stat-card .l{font-size:12.5px; color:#5C6B64; font-weight:700; margin-top:4px;}
  .panel{background:#fff; border-radius:18px; border:1px solid rgba(47,58,58,.1); margin-bottom:28px; overflow:hidden;}
  .panel h2{font-family:'Fraunces',serif; font-size:17px; font-weight:600; padding:20px 22px; border-bottom:1px solid rgba(47,58,58,.08);}
  table{width:100%; border-collapse:collapse; font-size:13.5px;}
  th{text-align:left; font-size:11.5px; text-transform:uppercase; letter-spacing:.05em; color:#8b968f; padding:12px 22px; border-bottom:1px solid rgba(47,58,58,.08);}
  td{padding:12px 22px; border-bottom:1px solid rgba(47,58,58,.06); color:#2F3A3A;}
  tr:last-child td{border-bottom:none;}
  .empty{padding:26px 22px; color:#8b968f; font-size:13.5px;}
  .badge{display:inline-block; font-size:11px; font-weight:800; padding:4px 10px; border-radius:100px; background:rgba(107,144,128,.15); color:#4F7161;}
  @media (max-width:900px){ .stat-grid{grid-template-columns:1fr 1fr;} table{display:block; overflow-x:auto;} }
</style>
</head>
<body>
  <div class="topbar">
    <div class="brand">SevaNest — Admin</div>
    <div style="display:flex; gap:20px; align-items:center;">
      <span>Hi, <?= e($_SESSION['full_name'] ?? 'Admin') ?></span>
      <a href="logout.php">Log out</a>
    </div>
  </div>

  <div class="wrap">
    <h1>Overview</h1>
    <div class="stat-grid">
      <div class="stat-card"><div class="n"><?= (int) $totalVolunteers ?></div><div class="l">Volunteer sign-ups</div></div>
      <div class="stat-card"><div class="n"><?= (int) $totalMessages ?></div><div class="l">Contact messages</div></div>
      <div class="stat-card"><div class="n">₹<?= number_format((float) $totalDonations) ?></div><div class="l">Donations received</div></div>
      <div class="stat-card"><div class="n"><?= (int) $totalBookings ?></div><div class="l">Visit bookings</div></div>
    </div>

    <div class="panel">
      <h2>Volunteer sign-ups</h2>
      <?php if ($volunteers): ?>
      <table>
        <tr><th>Name</th><th>Email</th><th>Phone</th><th>Area</th><th>Availability</th><th>Status</th><th>Received</th></tr>
        <?php foreach ($volunteers as $v): ?>
        <tr>
          <td><?= e($v['full_name']) ?></td><td><?= e($v['email']) ?></td><td><?= e($v['phone']) ?></td>
          <td><?= e($v['area']) ?></td><td><?= e($v['availability']) ?></td>
          <td><span class="badge"><?= e($v['status']) ?></span></td>
          <td><?= e(date('d M Y', strtotime($v['created_at']))) ?></td>
        </tr>
        <?php endforeach; ?>
      </table>
      <?php else: ?><div class="empty">No volunteer sign-ups yet.</div><?php endif; ?>
    </div>

    <div class="panel">
      <h2>Contact messages</h2>
      <?php if ($messages): ?>
      <table>
        <tr><th>Name</th><th>Email</th><th>Subject</th><th>Message</th><th>Received</th></tr>
        <?php foreach ($messages as $m): ?>
        <tr>
          <td><?= e($m['name']) ?></td><td><?= e($m['email']) ?></td><td><?= e($m['subject']) ?></td>
          <td style="max-width:280px;"><?= e(mb_strimwidth($m['message'], 0, 80, '…')) ?></td>
          <td><?= e(date('d M Y', strtotime($m['created_at']))) ?></td>
        </tr>
        <?php endforeach; ?>
      </table>
      <?php else: ?><div class="empty">No messages yet.</div><?php endif; ?>
    </div>

    <div class="panel">
      <h2>Donations</h2>
      <?php if ($donations): ?>
      <table>
        <tr><th>Donor</th><th>Amount</th><th>Category</th><th>Status</th><th>Reference</th><th>Received</th></tr>
        <?php foreach ($donations as $d): ?>
        <tr>
          <td><?= e($d['donor_name']) ?></td><td>₹<?= number_format((float) $d['amount']) ?></td>
          <td><?= e($d['category']) ?></td><td><span class="badge"><?= e($d['payment_status']) ?></span></td>
          <td><?= e($d['transaction_ref']) ?></td><td><?= e(date('d M Y', strtotime($d['created_at']))) ?></td>
        </tr>
        <?php endforeach; ?>
      </table>
      <?php else: ?><div class="empty">No donations yet.</div><?php endif; ?>
    </div>

    <div class="panel">
      <h2>Visit bookings</h2>
      <?php if ($bookings): ?>
      <table>
        <tr><th>Visitor</th><th>Home</th><th>Visit date</th><th>Status</th><th>Requested</th></tr>
        <?php foreach ($bookings as $b): ?>
        <tr>
          <td><?= e($b['visitor_name']) ?></td><td><?= e($b['home_name'] ?? '—') ?></td>
          <td><?= e(date('d M Y', strtotime($b['visit_date']))) ?></td>
          <td><span class="badge"><?= e($b['status']) ?></span></td>
          <td><?= e(date('d M Y', strtotime($b['created_at']))) ?></td>
        </tr>
        <?php endforeach; ?>
      </table>
      <?php else: ?><div class="empty">No visit bookings yet.</div><?php endif; ?>
    </div>
  </div>
</body>
</html>
