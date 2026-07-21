<?php
// ==========================================
// OAHMS - USER MANAGEMENT (SUPER ADMIN)
// ==========================================

session_start();

/*
|--------------------------------------------------------------------------
| TEMPORARY LOGIN CHECK
|--------------------------------------------------------------------------
| Replace this with your real authentication once the login module
| is connected.
*/

if (!isset($_SESSION['user_role'])) {
    $_SESSION['user_role'] = 'super_admin';
    $_SESSION['username'] = 'Super Admin';
}

if ($_SESSION['user_role'] !== 'super_admin') {
    header("Location: login.php");
    exit();
}

// Database Connection (keep if available)
if (file_exists("includes/db_connect.php")) {
    require_once "includes/db_connect.php";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>User Management | OAHMS</title>

    <link rel="stylesheet"
          href="user_management.css">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Poppins:wght@500;600;700&display=swap"
          rel="stylesheet">

</head>

<body>

<header class="top-header">

    <h2>Old Age Home Management System</h2>

</header>

<div class="dashboard-container">

<!-- ==========================
        SIDEBAR
========================== -->

<aside class="sidebar">

    <div>

        <div class="sidebar-logo">

            <img src="images/logo.png"
                 alt="Logo">

            <h2>OAHMS</h2>

            <p>Management System</p>

        </div>

        <div class="admin-profile">

            <div class="profile-image">

                <i class="fa-solid fa-user-shield"></i>

            </div>

            <h3>

                <?php echo $_SESSION['username']; ?>

            </h3>

            <p>Super Administrator</p>

        </div>

        <ul class="menu">

            <li onclick="location.href='superadmin_dash.php'">

                <i class="fa-solid fa-house"></i>

                Dashboard

            </li>

            <li class="active">

                <i class="fa-solid fa-users"></i>

                User Management

            </li>

            <li>

                <i class="fa-solid fa-user-lock"></i>

                Role Management

            </li>

            <li>

                <i class="fa-solid fa-chart-column"></i>

                Reports

            </li>

            <li>

                <i class="fa-solid fa-chart-pie"></i>

                Statistics

            </li>

        </ul>

    </div>

    <div class="bottom-menu">

        <button>

            <i class="fa-solid fa-gear"></i>

            Settings

        </button>

    </div>

</aside>

<!-- ==========================
        CONTENT
========================== -->

<main class="content">

<div class="page-header">

    <div>

        <h1>User Management</h1>

        <p>

            Manage all users of the Old Age Home
            Management System from one place.

        </p>

    </div>

    <button class="add-user-btn">

        <i class="fa-solid fa-user-plus"></i>

        Add User

    </button>

</div>

<!-- ==========================
        STATISTICS
========================== -->

<section class="stats-grid">

<div class="stat-card">

<i class="fa-solid fa-users"></i>

<h2>158</h2>

<p>Total Users</p>

</div>

<div class="stat-card">

<i class="fa-solid fa-user-check"></i>

<h2>145</h2>

<p>Active Users</p>

</div>

<div class="stat-card">

<i class="fa-solid fa-user-slash"></i>

<h2>13</h2>

<p>Inactive Users</p>

</div>

<div class="stat-card">

<i class="fa-solid fa-user-shield"></i>

<h2>6</h2>

<p>Administrators</p>

</div>

</section>
<!-- ======================================================
                    SEARCH & FILTER BAR
======================================================= -->

<section class="toolbar">

    <div class="search-box">

        <i class="fa-solid fa-magnifying-glass"></i>

        <input
            type="text"
            id="searchUser"
            placeholder="Search by name, username, email or phone...">

    </div>

    <div class="toolbar-actions">

        <select id="roleFilter">

            <option value="">All Roles</option>

            <option>Old Age Home Admin</option>

            <option>Caretaker</option>

            <option>Doctor</option>

            <option>Donor</option>

            <option>Family Member</option>

        </select>

        <select id="statusFilter">

            <option value="">All Status</option>

            <option>Active</option>

            <option>Inactive</option>

        </select>

        <button class="export-btn">

            <i class="fa-solid fa-download"></i>

            Export

        </button>

    </div>

</section>

<!-- ======================================================
                    USER TABLE
======================================================= -->

<section class="table-card">

    <table>

        <thead>

            <tr>

                <th>ID</th>

                <th>Name</th>

                <th>Email</th>

                <th>Phone</th>

                <th>Role</th>

                <th>Status</th>

                <th>Created</th>

                <th>Actions</th>

            </tr>

        </thead>

        <tbody id="userTable">

            <tr>

                <td>001</td>

                <td>Rahul Sharma</td>

                <td>rahul@gmail.com</td>

                <td>9876543210</td>

                <td>

                    <span class="role admin">

                        Old Age Home Admin

                    </span>

                </td>

                <td>

                    <span class="status active">

                        Active

                    </span>

                </td>

                <td>12 Jun 2026</td>

                <td>

                    <button class="action-btn view">

                        <i class="fa-solid fa-eye"></i>

                    </button>

                    <button class="action-btn edit">

                        <i class="fa-solid fa-pen"></i>

                    </button>

                    <button class="action-btn delete">

                        <i class="fa-solid fa-trash"></i>

                    </button>

                </td>

            </tr>

            <tr>

                <td>002</td>

                <td>Priya Patel</td>

                <td>priya@gmail.com</td>

                <td>9876501234</td>

                <td>

                    <span class="role doctor">

                        Doctor

                    </span>

                </td>

                <td>

                    <span class="status active">

                        Active

                    </span>

                </td>

                <td>18 Jun 2026</td>

                <td>

                    <button class="action-btn view">

                        <i class="fa-solid fa-eye"></i>

                    </button>

                    <button class="action-btn edit">

                        <i class="fa-solid fa-pen"></i>

                    </button>

                    <button class="action-btn delete">

                        <i class="fa-solid fa-trash"></i>

                    </button>

                </td>

            </tr>

            <tr>

                <td>003</td>

                <td>Arjun Mehta</td>

                <td>arjun@gmail.com</td>

                <td>9988776655</td>

                <td>

                    <span class="role caretaker">

                        Caretaker

                    </span>

                </td>

                <td>

                    <span class="status inactive">

                        Inactive

                    </span>

                </td>

                <td>20 Jun 2026</td>

                <td>

                    <button class="action-btn view">

                        <i class="fa-solid fa-eye"></i>

                    </button>

                    <button class="action-btn edit">

                        <i class="fa-solid fa-pen"></i>

                    </button>

                    <button class="action-btn delete">

                        <i class="fa-solid fa-trash"></i>

                    </button>

                </td>

            </tr>

            <tr>

                <td>004</td>

                <td>Sneha Verma</td>

                <td>sneha@gmail.com</td>

                <td>9123456780</td>

                <td>

                    <span class="role donor">

                        Donor

                    </span>

                </td>

                <td>

                    <span class="status active">

                        Active

                    </span>

                </td>

                <td>21 Jun 2026</td>

                <td>

                    <button class="action-btn view">

                        <i class="fa-solid fa-eye"></i>

                    </button>

                    <button class="action-btn edit">

                        <i class="fa-solid fa-pen"></i>

                    </button>

                    <button class="action-btn delete">

                        <i class="fa-solid fa-trash"></i>

                    </button>

                </td>

            </tr>

        </tbody>

    </table>

</section>

<!-- ======================================================
                    PAGINATION
======================================================= -->

<div class="pagination">

    <span>

        Showing 1 - 4 of 158 Users

    </span>

    <div>

        <button>

            Previous

        </button>

        <button class="current">

            1

        </button>

        <button>

            2

        </button>

        <button>

            3

        </button>

        <button>

            Next

        </button>

    </div>

</div>
<!-- ======================================================
                    ADD USER MODAL
======================================================= -->

<div class="modal" id="addUserModal">

    <div class="modal-content">

        <div class="modal-header">

            <h2>

                <i class="fa-solid fa-user-plus"></i>

                Add New User

            </h2>

            <button class="close-modal">&times;</button>

        </div>

        <form id="addUserForm">

            <div class="form-grid">

                <div class="form-group">

                    <label>First Name *</label>

                    <input type="text"
                           placeholder="Enter first name"
                           required>

                </div>

                <div class="form-group">

                    <label>Last Name *</label>

                    <input type="text"
                           placeholder="Enter last name"
                           required>

                </div>

                <div class="form-group">

                    <label>Email *</label>

                    <input type="email"
                           placeholder="Enter email"
                           required>

                </div>

                <div class="form-group">

                    <label>Phone *</label>

                    <input type="text"
                           placeholder="Enter phone number"
                           required>

                </div>

                <div class="form-group">

                    <label>Password *</label>

                    <input type="password"
                           placeholder="Password"
                           required>

                </div>

                <div class="form-group">

                    <label>Confirm Password *</label>

                    <input type="password"
                           placeholder="Confirm Password"
                           required>

                </div>

                <div class="form-group">

                    <label>User Role *</label>

                    <select required>

                        <option value="">Select Role</option>

                        <option>Old Age Home Admin</option>

                        <option>Caretaker</option>

                        <option>Doctor</option>

                        <option>Donor</option>

                        <option>Family Member</option>

                    </select>

                </div>

                <div class="form-group">

                    <label>Status *</label>

                    <select>

                        <option>Active</option>

                        <option>Inactive</option>

                    </select>

                </div>

            </div>

            <div class="form-group">

                <label>Address</label>

                <textarea rows="4"
                          placeholder="Enter address"></textarea>

            </div>

            <div class="modal-footer">

                <button type="button"
                        class="cancel-btn">

                    Cancel

                </button>

                <button type="submit"
                        class="save-btn">

                    <i class="fa-solid fa-floppy-disk"></i>

                    Save User

                </button>

            </div>

        </form>

    </div>

</div>

<!-- ======================================================
                    DELETE CONFIRMATION
======================================================= -->

<div class="modal" id="deleteModal">

    <div class="delete-box">

        <i class="fa-solid fa-circle-exclamation warning-icon"></i>

        <h2>Delete User?</h2>

        <p>

            This action cannot be undone.

        </p>

        <div class="delete-buttons">

            <button class="cancel-delete">

                Cancel

            </button>

            <button class="confirm-delete">

                Delete

            </button>

        </div>

    </div>

</div>

<!-- ======================================================
                    TOAST MESSAGE
======================================================= -->

<div id="toast">

    User saved successfully.

</div>

</main>

</div>

<footer class="footer">

    © 2026 Old Age Home Management System

</footer>

<script src="user_management.js"></script>

</body>

</html>