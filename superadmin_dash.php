<?php
// OAHMS Super Admin Dashboard
session_start();

// Strict Access Control Check
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'super_admin') {
    header("Location: login.php");
    exit;
}

// Database Connection
require_once 'includes/db_connect.php';

// Retrieve dynamic stats from the MySQL database
try {
    // Total Users count
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $totalUsers = $stmt->fetchColumn();

    // Total Staff (Caretakers) count
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role = :role");
    $stmt->execute(['role' => 'caretaker']);
    $totalStaff = $stmt->fetchColumn();
} catch (PDOException $e) {
    // Fallback counts in case of DB issues
    $totalUsers = 0;
    $totalStaff = 0;
}

// Hardcoded sample data for other categories (can be connected to other tables later)
$totalResidents = 254;
$totalDonations = "₹4.2 Lakh";
$totalVisitors = 185;

$admin_name = $_SESSION['user_name'] ?? 'Super Admin';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Dashboard | OAHMS</title>
    <link rel="stylesheet" href="superadmin_dash.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
</head>

<body>

<!-- ==========================
     HEADER
=========================== -->
<?php include 'includes/header.php'; ?>

<div class="dashboard-container">

    <!-- ==========================
         SIDEBAR (Resolved Redirection Links)
    =========================== -->
    <aside class="sidebar">
        <div>
            <div class="admin-profile">
                <div class="profile-image">
                    <i class="fa-solid fa-user-shield"></i>
                </div>
                <h3><?php echo htmlspecialchars($admin_name); ?></h3>
                <p>System Administrator</p>
            </div>

            <ul class="menu">
                <li class="active" onclick="location.href='superadmin_dash.php'">
                    <i class="fa-solid fa-house"></i>
                    Dashboard
                </li>
                <li onclick="location.href='user_management.php'">
                    <i class="fa-solid fa-users"></i>
                    User Management
                </li>
                <li onclick="location.href='#'">
                    <i class="fa-solid fa-user-lock"></i>
                    Role Management
                </li>
                <li onclick="location.href='#'">
                    <i class="fa-solid fa-file-lines"></i>
                    Reports
                </li>
                <li onclick="location.href='#'">
                    <i class="fa-solid fa-chart-column"></i>
                    Statistics
                </li>
            </ul>
        </div>

        <div class="bottom-menu">
            <button onclick="location.href='logout.php'" style="background: none; border: none; color: white; cursor: pointer; text-align: left; width: 100%; display: flex; align-items: center; gap: 10px; padding: 10px 0;">
                <i class="fa-solid fa-right-from-bracket"></i>
                Logout
            </button>
        </div>
    </aside>

    <!-- ==========================
         CONTENT
    =========================== -->
    <main class="content">
        <section class="welcome">
            <div>
                <h1>Welcome Back, <?php echo htmlspecialchars(explode(' ', $admin_name)[0]); ?></h1>
                <p>
                    Monitor and manage the complete Old Age Home Management System from one place.
                </p>
            </div>

            <div class="today-date">
                <i class="fa-solid fa-calendar-days"></i>
                <span id="currentDate"></span>
            </div>
        </section>

        <!-- Statistics -->
        <section class="stats-grid">
            <div class="stat-card">
                <i class="fa-solid fa-house-user"></i>
                <h2 id="residentCount"><?= htmlspecialchars($totalResidents) ?></h2>
                <p>Total Residents</p>
            </div>

            <div class="stat-card">
                <i class="fa-solid fa-user-nurse"></i>
                <h2 id="staffCount"><?= htmlspecialchars($totalStaff) ?></h2>
                <p>Total Staff</p>
            </div>

            <div class="stat-card">
                <i class="fa-solid fa-hand-holding-heart"></i>
                <h2 id="donationCount"><?= htmlspecialchars($totalDonations) ?></h2>
                <p>Total Donations</p>
            </div>

            <div class="stat-card">
                <i class="fa-solid fa-people-group"></i>
                <h2 id="visitorCount"><?= htmlspecialchars($totalVisitors) ?></h2>
                <p>Total Visitors</p>
            </div>

            <div class="stat-card">
                <i class="fa-solid fa-user-shield"></i>
                <h2 id="userCount"><?= htmlspecialchars($totalUsers) ?></h2>
                <p>Total Users</p>
            </div>
        </section>

        <!-- Quick Access (Resolved Redirection Links) -->
        <section class="quick-access">
            <h2>Quick Access</h2>
            <div class="quick-grid">
                <div class="quick-card" onclick="location.href='user_management.php'">
                    <i class="fa-solid fa-users"></i>
                    <h3>User Management</h3>
                    <p>Manage system users.</p>
                </div>

                <div class="quick-card" onclick="location.href='#'">
                    <i class="fa-solid fa-user-lock"></i>
                    <h3>Role Management</h3>
                    <p>Assign permissions.</p>
                </div>

                <div class="quick-card" onclick="location.href='#'">
                    <i class="fa-solid fa-file-lines"></i>
                    <h3>Reports</h3>
                    <p>Generate reports.</p>
                </div>

                <div class="quick-card" onclick="location.href='#'">
                    <i class="fa-solid fa-chart-column"></i>
                    <h3>Statistics</h3>
                    <p>View complete analytics.</p>
                </div>
            </div>
        </section>

        <!-- Bottom Section -->
        <section class="bottom-grid">
            <div class="activity-panel">
                <h2>Recent Activities</h2>
                <ul>
                    <li><i class="fa-solid fa-circle-check"></i> New Resident Added</li>
                    <li><i class="fa-solid fa-circle-check"></i> Donation Received</li>
                    <li><i class="fa-solid fa-circle-check"></i> Visitor Registered</li>
                    <li><i class="fa-solid fa-circle-check"></i> Staff Updated</li>
                    <li><i class="fa-solid fa-circle-check"></i> Monthly Report Generated</li>
                </ul>
            </div>

            <div class="overview-panel">
                <h2>System Overview</h2>
                <div class="status">
                    <span>Database</span>
                    <strong>Online</strong>
                </div>
                <div class="status">
                    <span>Server</span>
                    <strong>Healthy</strong>
                </div>
                <div class="status">
                    <span>Storage</span>
                    <strong>68%</strong>
                </div>
                <div class="status">
                    <span>Active Users</span>
                    <strong>12</strong>
                </div>
            </div>
        </section>
    </main>

</div>

<!-- ==========================
     FOOTER
=========================== -->
<?php include 'includes/footer.php'; ?>

<script src="superadmin_dash.js"></script>

</body>
</html>