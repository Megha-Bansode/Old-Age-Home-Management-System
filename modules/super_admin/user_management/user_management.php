<?php
/*=============================================================================
    OLD AGE HOME MANAGEMENT SYSTEM
    Module: Super Admin - User Management
    File: user_management.php
    Description: Frontend interface for managing system users, roles, and access.
=============================================================================*/

$base_path = '../../../';
$page_title = 'User Management';
$active_page = 'user_management';
$module_name = 'Super Admin Module';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_name'])) {
    $_SESSION['user_name'] = 'Super Admin';
    $_SESSION['user_role'] = 'Super Admin';
}

$extra_css = ['modules/super_admin/user_management/user_management.css'];

require_once $base_path . 'includes/header.php';
require_once $base_path . 'includes/navbar.php';
?>

<!-- Direct Module CSS Link -->
<link rel="stylesheet" href="user_management.css">

<div class="d-flex min-vh-100 position-relative">
    
    <!-- Sidebar Include -->
    <?php require_once $base_path . 'includes/sidebar.php'; ?>

    <!-- Main Content Body -->
    <main class="main-content flex-grow-1 bg-light p-2">

        <!-- Toast Notification Container -->
        <div class="toast-container" id="toastContainer"></div>

        <!-- Main Container -->
        <div class="user-management-container">

            <!-- Page Header -->
            <header class="page-header animate-fade-in">
                <div class="page-title">
                    <div class="title-with-badge">
                        <h1>User Management</h1>
                        <span class="badge-module">
                            <span class="badge-dot"></span>
                            Super Admin
                        </span>
                    </div>
                    <p>Manage, monitor, and configure system user access, roles, and account permissions.</p>
                </div>
                
                <div class="header-actions">
                    <a href="../index.php" class="add-user-btn" style="background: rgba(107, 144, 128, 0.15); color: #4F7161; text-decoration: none; border: 1px solid rgba(107, 144, 128, 0.3);">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="19" y1="12" x2="5" y2="12"></line>
                            <polyline points="12 19 5 12 12 5"></polyline>
                        </svg>
                        <span>Dashboard</span>
                    </a>
                    <button class="add-user-btn" id="btnAddUser">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        <span>Add New User</span>
                    </button>
                </div>
            </header>

            <!-- Dashboard Statistics Cards -->
            <section class="dashboard-cards" aria-label="User Statistics">
                
                <!-- Total Users Card -->
                <div class="card card-total animate-card-1" id="cardTotalUsers">
                    <div class="card-bg-glow"></div>
                    <div class="card-icon">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                    <div class="card-info">
                        <h2 id="statTotalUsers">0</h2>
                        <span>Total System Users</span>
                    </div>
                </div>

                <!-- Active Users Card -->
                <div class="card card-active animate-card-2" id="cardActiveUsers">
                    <div class="card-bg-glow"></div>
                    <div class="card-icon">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                    </div>
                    <div class="card-info">
                        <h2 id="statActiveUsers">0</h2>
                        <span>Active Accounts</span>
                    </div>
                </div>

                <!-- Inactive Users Card -->
                <div class="card card-inactive animate-card-3" id="cardInactiveUsers">
                    <div class="card-bg-glow"></div>
                    <div class="card-icon">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="15" y1="9" x2="9" y2="15"></line>
                            <line x1="9" y1="9" x2="15" y2="15"></line>
                        </svg>
                    </div>
                    <div class="card-info">
                        <h2 id="statInactiveUsers">0</h2>
                        <span>Inactive / Suspended</span>
                    </div>
                </div>

                <!-- Total Roles Card -->
                <div class="card card-roles animate-card-4" id="cardTotalRoles">
                    <div class="card-bg-glow"></div>
                    <div class="card-icon">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                        </svg>
                    </div>
                    <div class="card-info">
                        <h2 id="statTotalRoles">0</h2>
                        <span>Configured Roles</span>
                    </div>
                </div>

            </section>

            <!-- Controls Section: Search & Filters -->
            <section class="controls-card animate-fade-in-delayed">
                
                <!-- Search Box -->
                <div class="search-box">
                    <svg class="search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" id="searchInput" placeholder="Search by Name, Email, or Phone..." autocomplete="off">
                    <button class="clear-search-btn" id="btnClearSearch" title="Clear Search" style="display: none;">
                        &times;
                    </button>
                </div>

                <!-- Filters Group -->
                <div class="filter-group">
                    
                    <!-- Role Filter -->
                    <div class="filter-item">
                        <label for="roleFilter">Role:</label>
                        <select id="roleFilter">
                            <option value="All">All Roles</option>
                            <option value="Super Admin">Super Admin</option>
                            <option value="Admin">Admin</option>
                            <option value="Doctor">Doctor</option>
                            <option value="Nurse">Nurse</option>
                            <option value="Caretaker">Caretaker</option>
                            <option value="Staff">Staff</option>
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div class="filter-item">
                        <label for="statusFilter">Status:</label>
                        <select id="statusFilter">
                            <option value="All">All Status</option>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>

                    <!-- Reset Filters Button -->
                    <button class="btn-reset-filters" id="btnResetFilters">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="23 4 23 10 17 10"></polyline>
                            <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path>
                        </svg>
                        <span>Reset Filters</span>
                    </button>

                </div>

            </section>

            <!-- Main Data Table Container -->
            <section class="table-card animate-fade-in-table">
                
                <div class="table-responsive">
                    <table class="user-table" id="usersTable">
                        <thead>
                            <tr>
                                <th class="col-user">User Details</th>
                                <th class="col-contact">Contact Info</th>
                                <th class="col-role">Role</th>
                                <th class="col-status">Status</th>
                                <th class="col-date">Created Date</th>
                                <th class="col-actions">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="userTableBody">
                            <!-- Rows injected via user_management.js -->
                        </tbody>
                    </table>
                </div>

                <!-- Empty State Block -->
                <div class="empty-state" id="emptyState" style="display: none;">
                    <div class="empty-icon-wrapper">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            <line x1="8" y1="11" x2="14" y2="11"></line>
                        </svg>
                    </div>
                    <h3>No Users Found</h3>
                    <p>We couldn't find any users matching your current search criteria or active filters.</p>
                    <button class="btn-secondary" id="btnEmptyReset">Reset Search & Filters</button>
                </div>

                <!-- Pagination Footer -->
                <footer class="table-footer">
                    
                    <div class="pagination-info">
                        Showing <strong id="pageStart">0</strong> to <strong id="pageEnd">0</strong> of <strong id="totalRecords">0</strong> system users
                    </div>

                    <div class="pagination-controls">
                        <button class="page-nav-btn" id="btnPrevPage" disabled aria-label="Previous Page">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="15 18 9 12 15 6"></polyline>
                            </svg>
                        </button>
                        
                        <div class="page-numbers" id="pageNumbers">
                            <!-- Dynamic Page Buttons -->
                        </div>

                        <button class="page-nav-btn" id="btnNextPage" disabled aria-label="Next Page">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                        </button>
                    </div>

                </footer>

            </section>

        </div><!-- /.user-management-container -->

    </main>

</div>

<!-- =========================================================================
    MODAL 1: ADD USER MODAL
========================================================================== -->
<div class="modal-overlay" id="addUserModal" aria-hidden="true">
    <div class="modal-container" role="dialog" aria-labelledby="modalAddTitle">
        
        <div class="modal-header">
            <h3 id="modalAddTitle">Create New System User</h3>
            <button type="button" class="modal-close" data-close="addUserModal" aria-label="Close modal">&times;</button>
        </div>

        <form id="addUserForm" novalidate>
            <div class="modal-body">
                
                <div class="form-grid">
                    
                    <!-- Full Name -->
                    <div class="form-group">
                        <label for="addFullName">Full Name <span class="required">*</span></label>
                        <input type="text" id="addFullName" placeholder="e.g. Ramesh Chandra" required>
                        <span class="field-error" id="errAddFullName">Please enter a valid full name.</span>
                    </div>

                    <!-- Email Address -->
                    <div class="form-group">
                        <label for="addEmail">Email Address <span class="required">*</span></label>
                        <input type="email" id="addEmail" placeholder="e.g. ramesh@example.com" required>
                        <span class="field-error" id="errAddEmail">Please enter a valid email address.</span>
                    </div>

                    <!-- Phone Number -->
                    <div class="form-group">
                        <label for="addPhone">Phone Number <span class="required">*</span></label>
                        <input type="tel" id="addPhone" placeholder="e.g. 9876543210" required>
                        <span class="field-error" id="errAddPhone">Enter a valid 10-digit mobile number.</span>
                    </div>

                    <!-- Role -->
                    <div class="form-group">
                        <label for="addRole">System Role <span class="required">*</span></label>
                        <select id="addRole" required>
                            <option value="" disabled selected>Select Access Role</option>
                            <option value="Super Admin">Super Admin</option>
                            <option value="Admin">Admin</option>
                            <option value="Doctor">Doctor</option>
                            <option value="Nurse">Nurse</option>
                            <option value="Caretaker">Caretaker</option>
                            <option value="Staff">Staff</option>
                        </select>
                        <span class="field-error" id="errAddRole">Please assign a user role.</span>
                    </div>

                    <!-- Gender -->
                    <div class="form-group">
                        <label for="addGender">Gender</label>
                        <select id="addGender">
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <!-- Status -->
                    <div class="form-group">
                        <label for="addStatus">Account Status</label>
                        <select id="addStatus">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label for="addPassword">Password <span class="required">*</span></label>
                        <input type="password" id="addPassword" placeholder="Minimum 6 characters" required>
                        <span class="field-error" id="errAddPassword">Password must be at least 6 characters.</span>
                    </div>

                    <!-- Profile Photo -->
                    <div class="form-group">
                        <label for="addProfileImg">Profile Photo (Max 2MB)</label>
                        <input type="file" id="addProfileImg" accept="image/jpeg,image/png,image/webp">
                    </div>

                    <!-- Address (Full Width) -->
                    <div class="form-group full-width">
                        <label for="addAddress">Residential Address</label>
                        <textarea id="addAddress" rows="2" placeholder="Street, City, Zip Code..."></textarea>
                    </div>

                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn-secondary" data-close="addUserModal">Cancel</button>
                <button type="submit" class="btn-primary" id="btnSubmitAdd">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    <span>Save User Account</span>
                </button>
            </div>
        </form>

    </div>
</div>


<!-- =========================================================================
    MODAL 2: EDIT USER MODAL
========================================================================== -->
<div class="modal-overlay" id="editUserModal" aria-hidden="true">
    <div class="modal-container" role="dialog" aria-labelledby="modalEditTitle">
        
        <div class="modal-header">
            <h3 id="modalEditTitle">Edit User Details</h3>
            <button type="button" class="modal-close" data-close="editUserModal" aria-label="Close modal">&times;</button>
        </div>

        <form id="editUserForm" novalidate>
            <input type="hidden" id="editUserId">

            <div class="modal-body">
                
                <div class="form-grid">
                    
                    <!-- Full Name -->
                    <div class="form-group">
                        <label for="editFullName">Full Name <span class="required">*</span></label>
                        <input type="text" id="editFullName" required>
                        <span class="field-error" id="errEditFullName">Please enter a valid full name.</span>
                    </div>

                    <!-- Email Address -->
                    <div class="form-group">
                        <label for="editEmail">Email Address <span class="required">*</span></label>
                        <input type="email" id="editEmail" required>
                        <span class="field-error" id="errEditEmail">Please enter a valid email address.</span>
                    </div>

                    <!-- Phone Number -->
                    <div class="form-group">
                        <label for="editPhone">Phone Number <span class="required">*</span></label>
                        <input type="tel" id="editPhone" required>
                        <span class="field-error" id="errEditPhone">Enter a valid 10-digit mobile number.</span>
                    </div>

                    <!-- Role -->
                    <div class="form-group">
                        <label for="editRole">System Role <span class="required">*</span></label>
                        <select id="editRole" required>
                            <option value="Super Admin">Super Admin</option>
                            <option value="Admin">Admin</option>
                            <option value="Doctor">Doctor</option>
                            <option value="Nurse">Nurse</option>
                            <option value="Caretaker">Caretaker</option>
                            <option value="Staff">Staff</option>
                        </select>
                        <span class="field-error" id="errEditRole">Please assign a user role.</span>
                    </div>

                    <!-- Gender -->
                    <div class="form-group">
                        <label for="editGender">Gender</label>
                        <select id="editGender">
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <!-- Status -->
                    <div class="form-group">
                        <label for="editStatus">Account Status</label>
                        <select id="editStatus">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>

                    <!-- Password (Optional) -->
                    <div class="form-group">
                        <label for="editPassword">New Password (Optional)</label>
                        <input type="password" id="editPassword" placeholder="Leave blank to keep current">
                        <span class="field-error" id="errEditPassword">Password must be at least 6 characters.</span>
                    </div>

                    <!-- Profile Photo -->
                    <div class="form-group">
                        <label for="editProfileImg">Update Photo (Max 2MB)</label>
                        <input type="file" id="editProfileImg" accept="image/jpeg,image/png,image/webp">
                    </div>

                    <!-- Address (Full Width) -->
                    <div class="form-group full-width">
                        <label for="editAddress">Residential Address</label>
                        <textarea id="editAddress" rows="2"></textarea>
                    </div>

                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn-secondary" data-close="editUserModal">Cancel</button>
                <button type="submit" class="btn-primary" id="btnSubmitEdit">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    <span>Update Account</span>
                </button>
            </div>
        </form>

    </div>
</div>


<!-- =========================================================================
    MODAL 3: VIEW USER PROFILE MODAL
========================================================================== -->
<div class="modal-overlay" id="viewUserModal" aria-hidden="true">
    <div class="modal-container modal-md" role="dialog" aria-labelledby="modalViewTitle">
        
        <div class="modal-header">
            <h3 id="modalViewTitle">User Profile Summary</h3>
            <button type="button" class="modal-close" data-close="viewUserModal" aria-label="Close modal">&times;</button>
        </div>

        <div class="modal-body">
            
            <!-- View Profile Header Box -->
            <div class="profile-header-card">
                <img id="viewImg" src="" alt="User Avatar" class="profile-header-avatar">
                <div class="profile-header-meta">
                    <h2 id="viewFullName">User Name</h2>
                    <div class="badge-group">
                        <span class="role-badge" id="viewRole">Role</span>
                        <span class="status-badge" id="viewStatus">Status</span>
                    </div>
                </div>
            </div>

            <!-- Detail Fields Grid -->
            <div class="detail-grid">
                <div class="detail-item">
                    <span class="detail-label">System ID</span>
                    <span class="detail-value" id="viewUserId">#000</span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Email Address</span>
                    <span class="detail-value" id="viewEmail">user@example.com</span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Phone Number</span>
                    <span class="detail-value" id="viewPhone">+91 0000000000</span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Gender</span>
                    <span class="detail-value" id="viewGender">Unspecified</span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Created Date</span>
                    <span class="detail-value" id="viewCreatedDate">YYYY-MM-DD</span>
                </div>

                <div class="detail-item full-width">
                    <span class="detail-label">Residential Address</span>
                    <span class="detail-value" id="viewAddress">No address provided.</span>
                </div>
            </div>

        </div>

        <div class="modal-footer">
            <button type="button" class="btn-secondary" data-close="viewUserModal">Close</button>
        </div>

    </div>
</div>


<!-- =========================================================================
    MODAL 4: DELETE CONFIRMATION MODAL
========================================================================== -->
<div class="modal-overlay" id="deleteModal" aria-hidden="true">
    <div class="modal-container modal-sm" role="dialog" aria-labelledby="modalDeleteTitle">
        
        <div class="modal-header header-danger">
            <h3 id="modalDeleteTitle">Confirm User Deletion</h3>
            <button type="button" class="modal-close" data-close="deleteModal" aria-label="Close modal">&times;</button>
        </div>

        <div class="modal-body text-center">
            <div class="danger-icon-wrapper">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#E76F51" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                    <line x1="12" y1="9" x2="12" y2="13"></line>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
            </div>

            <h4 class="delete-prompt-title">Are you sure you want to delete this user?</h4>
            <p class="delete-prompt-desc">
                You are about to remove <strong id="deleteUserName">Target User</strong> from the system. This action cannot be undone and will revoke all access privileges.
            </p>
        </div>

        <div class="modal-footer footer-centered">
            <button type="button" class="btn-secondary" data-close="deleteModal">Cancel</button>
            <button type="button" class="btn-danger" id="btnConfirmDelete">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                </svg>
                <span>Yes, Delete User</span>
            </button>
        </div>

    </div>
</div>

<!-- Module Specific Script -->
<script src="user_management.js?v=<?php echo time(); ?>"></script>

<?php require_once $base_path . 'includes/footer.php'; ?>