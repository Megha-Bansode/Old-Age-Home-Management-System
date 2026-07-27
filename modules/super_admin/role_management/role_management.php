<?php
/**
 * SevaNest — Super Admin Role Management
 * 
 * Provides interface for viewing, creating, editing, and managing system access roles and permissions.
 */

$base_path = '../../../';
require_once $base_path . 'includes/session.php';
require_once $base_path . 'includes/auth.php';

require_login();
require_role('Super Admin');

$page_title = 'Role Management';
$active_page = 'role_management';
$module_name = 'Super Admin Module';

require_once $base_path . 'includes/header.php';
require_once $base_path . 'includes/navbar.php';
?>

<?php
/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'super_admin';
$currentPage   = 'role_management.php';
$sn_asset_root = "../../../assets";
require_once $base_path . 'includes/sidebar.php';
?>

<main id="sn-main-content" role="main" class="p-4 flex-grow-1">
        
        <!-- Page Header Strip -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4 p-4 bg-white rounded-4 shadow-sm border-start border-4 border-success">
            <div>
                <div class="d-flex align-items-center gap-2">
                    <h2 class="fw-bold text-dark mb-0">Role & Access Control Management</h2>
                    <span class="badge bg-primary-subtle text-primary px-3 py-1 rounded-pill">6 Active System Roles</span>
                </div>
                <p class="text-muted small mb-0 mt-1">Configure role permissions, access levels, and security policies for system users.</p>
            </div>
            <button class="btn btn-primary d-flex align-items-center gap-2 px-3 py-2 rounded-3" data-bs-toggle="modal" data-bs-target="#addRoleModal">
                <i class="bi bi-plus-circle-fill"></i>
                <span>Add New Role</span>
            </button>
        </div>

        <!-- Search & Filter Controls -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
            <div class="card-body p-3">
                <div class="row g-3 align-items-center">
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" id="roleSearchInput" class="form-control bg-light border-start-0" placeholder="Search roles by title or keyword...">
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3 ms-auto text-end">
                        <span class="text-muted small me-2">Sort by:</span>
                        <select class="form-select form-select-sm d-inline-block w-auto bg-light border-0">
                            <option selected>User Count (High to Low)</option>
                            <option>Role Name (A-Z)</option>
                            <option>Date Created</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Roles Cards Grid -->
        <div class="row g-4 mb-4" id="rolesGrid">
            
            <!-- Role Card 1: Super Admin -->
            <div class="col-12 col-md-6 col-xl-4 role-item">
                <div class="card border-0 shadow-sm rounded-4 h-100 bg-white border-top border-4 border-primary">
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="badge bg-primary-subtle text-primary px-3 py-1 rounded-pill fw-bold">Full Access</span>
                            <span class="text-muted small"><i class="bi bi-people-fill me-1"></i>3 Assigned Users</span>
                        </div>
                        <h4 class="fw-bold text-dark mb-2">Super Admin</h4>
                        <p class="text-muted small mb-3 flex-grow-1">
                            Complete system control, role assignment, user management, audit logs, and global platform configuration.
                        </p>
                        
                        <div class="bg-light p-3 rounded-3 mb-3">
                            <div class="text-muted small fw-semibold mb-2">Granted Permissions:</div>
                            <div class="d-flex flex-wrap gap-1">
                                <span class="badge bg-white text-dark border">User Manage</span>
                                <span class="badge bg-white text-dark border">Role Manage</span>
                                <span class="badge bg-white text-dark border">Reports</span>
                                <span class="badge bg-white text-dark border">Settings</span>
                                <span class="badge bg-white text-dark border">Audit Logs</span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-end gap-2 pt-2 border-top">
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editRoleModal" data-role="Super Admin">
                                <i class="bi bi-pencil-square me-1"></i>Edit Role
                            </button>
                            <button class="btn btn-sm btn-outline-secondary opacity-50" disabled title="System Core Role cannot be deleted">
                                <i class="bi bi-lock-fill me-1"></i>System Protected
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Role Card 2: Old Age Home Admin -->
            <div class="col-12 col-md-6 col-xl-4 role-item">
                <div class="card border-0 shadow-sm rounded-4 h-100 bg-white border-top border-4 border-success">
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="badge bg-success-subtle text-success px-3 py-1 rounded-pill fw-bold">Home Operations</span>
                            <span class="text-muted small"><i class="bi bi-people-fill me-1"></i>12 Assigned Users</span>
                        </div>
                        <h4 class="fw-bold text-dark mb-2">Old Age Home Admin</h4>
                        <p class="text-muted small mb-3 flex-grow-1">
                            Manages daily old age home operations, resident admissions, staff shifts, visitor requests, and donations.
                        </p>
                        
                        <div class="bg-light p-3 rounded-3 mb-3">
                            <div class="text-muted small fw-semibold mb-2">Granted Permissions:</div>
                            <div class="d-flex flex-wrap gap-1">
                                <span class="badge bg-white text-dark border">Resident Admissions</span>
                                <span class="badge bg-white text-dark border">Shift Duty</span>
                                <span class="badge bg-white text-dark border">Donations</span>
                                <span class="badge bg-white text-dark border">Visits</span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-end gap-2 pt-2 border-top">
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editRoleModal" data-role="Old Age Home Admin">
                                <i class="bi bi-pencil-square me-1"></i>Edit Role
                            </button>
                            <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteRoleModal" data-role="Old Age Home Admin">
                                <i class="bi bi-trash me-1"></i>Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Role Card 3: Doctor -->
            <div class="col-12 col-md-6 col-xl-4 role-item">
                <div class="card border-0 shadow-sm rounded-4 h-100 bg-white border-top border-4 border-info">
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="badge bg-info-subtle text-info px-3 py-1 rounded-pill fw-bold">Healthcare</span>
                            <span class="text-muted small"><i class="bi bi-people-fill me-1"></i>14 Assigned Users</span>
                        </div>
                        <h4 class="fw-bold text-dark mb-2">Doctor</h4>
                        <p class="text-muted small mb-3 flex-grow-1">
                            Access resident medical histories, prescribe medications, schedule health checkups, and record diagnosis notes.
                        </p>
                        
                        <div class="bg-light p-3 rounded-3 mb-3">
                            <div class="text-muted small fw-semibold mb-2">Granted Permissions:</div>
                            <div class="d-flex flex-wrap gap-1">
                                <span class="badge bg-white text-dark border">Medical Records</span>
                                <span class="badge bg-white text-dark border">Prescriptions</span>
                                <span class="badge bg-white text-dark border">Health Log</span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-end gap-2 pt-2 border-top">
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editRoleModal" data-role="Doctor">
                                <i class="bi bi-pencil-square me-1"></i>Edit Role
                            </button>
                            <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteRoleModal" data-role="Doctor">
                                <i class="bi bi-trash me-1"></i>Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Role Card 4: Caretaker -->
            <div class="col-12 col-md-6 col-xl-4 role-item">
                <div class="card border-0 shadow-sm rounded-4 h-100 bg-white border-top border-4 border-warning">
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="badge bg-warning-subtle text-warning px-3 py-1 rounded-pill fw-bold">Care Operations</span>
                            <span class="text-muted small"><i class="bi bi-people-fill me-1"></i>28 Assigned Users</span>
                        </div>
                        <h4 class="fw-bold text-dark mb-2">Caretaker</h4>
                        <p class="text-muted small mb-3 flex-grow-1">
                            Monitors daily vital signs, resident meals, medication distribution, and room assistance logs.
                        </p>
                        
                        <div class="bg-light p-3 rounded-3 mb-3">
                            <div class="text-muted small fw-semibold mb-2">Granted Permissions:</div>
                            <div class="d-flex flex-wrap gap-1">
                                <span class="badge bg-white text-dark border">Daily Care Log</span>
                                <span class="badge bg-white text-dark border">Vital Check</span>
                                <span class="badge bg-white text-dark border">Meal Schedule</span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-end gap-2 pt-2 border-top">
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editRoleModal" data-role="Caretaker">
                                <i class="bi bi-pencil-square me-1"></i>Edit Role
                            </button>
                            <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteRoleModal" data-role="Caretaker">
                                <i class="bi bi-trash me-1"></i>Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Role Card 5: Donor -->
            <div class="col-12 col-md-6 col-xl-4 role-item">
                <div class="card border-0 shadow-sm rounded-4 h-100 bg-white border-top border-4 border-danger">
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="badge bg-danger-subtle text-danger px-3 py-1 rounded-pill fw-bold">External</span>
                            <span class="text-muted small"><i class="bi bi-people-fill me-1"></i>94 Assigned Users</span>
                        </div>
                        <h4 class="fw-bold text-dark mb-2">Donor</h4>
                        <p class="text-muted small mb-3 flex-grow-1">
                            Make financial or material donations, track donation receipts, view project impact summaries, and manage donor profile.
                        </p>
                        
                        <div class="bg-light p-3 rounded-3 mb-3">
                            <div class="text-muted small fw-semibold mb-2">Granted Permissions:</div>
                            <div class="d-flex flex-wrap gap-1">
                                <span class="badge bg-white text-dark border">Make Donation</span>
                                <span class="badge bg-white text-dark border">View Receipts</span>
                                <span class="badge bg-white text-dark border">Impact Logs</span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-end gap-2 pt-2 border-top">
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editRoleModal" data-role="Donor">
                                <i class="bi bi-pencil-square me-1"></i>Edit Role
                            </button>
                            <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteRoleModal" data-role="Donor">
                                <i class="bi bi-trash me-1"></i>Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Role Card 6: Family Member -->
            <div class="col-12 col-md-6 col-xl-4 role-item">
                <div class="card border-0 shadow-sm rounded-4 h-100 bg-white border-top border-4 border-secondary">
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="badge bg-secondary-subtle text-dark px-3 py-1 rounded-pill fw-bold">Relative Care</span>
                            <span class="text-muted small"><i class="bi bi-people-fill me-1"></i>186 Assigned Users</span>
                        </div>
                        <h4 class="fw-bold text-dark mb-2">Family Member</h4>
                        <p class="text-muted small mb-3 flex-grow-1">
                            View resident status updates, medical visit schedules, request visit slots, and communicate with home administration.
                        </p>
                        
                        <div class="bg-light p-3 rounded-3 mb-3">
                            <div class="text-muted small fw-semibold mb-2">Granted Permissions:</div>
                            <div class="d-flex flex-wrap gap-1">
                                <span class="badge bg-white text-dark border">Resident Status</span>
                                <span class="badge bg-white text-dark border">Visit Booking</span>
                                <span class="badge bg-white text-dark border">Messages</span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-end gap-2 pt-2 border-top">
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editRoleModal" data-role="Family Member">
                                <i class="bi bi-pencil-square me-1"></i>Edit Role
                            </button>
                            <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteRoleModal" data-role="Family Member">
                                <i class="bi bi-trash me-1"></i>Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </main>

<!-- =========================================================================
    MODAL 1: ADD ROLE MODAL
========================================================================== -->
<div class="modal fade" id="addRoleModal" tabindex="-1" aria-labelledby="addRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white p-4 rounded-top-4">
                <h5 class="modal-title fw-bold" id="addRoleModalLabel">
                    <i class="bi bi-shield-plus me-2"></i>Create New System Role
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="addRoleForm">
                    <div class="mb-3">
                        <label for="newRoleTitle" class="form-label fw-semibold">Role Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded-3" id="newRoleTitle" placeholder="e.g. Senior Medical Inspector" required>
                    </div>
                    <div class="mb-3">
                        <label for="newRoleDesc" class="form-label fw-semibold">Description</label>
                        <textarea class="form-control rounded-3" id="newRoleDesc" rows="3" placeholder="Describe the responsibilities and scope of this role..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold mb-2">Module Permissions</label>
                        <div class="row g-3 bg-light p-3 rounded-3 border">
                            <div class="col-6 col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="permUser" checked>
                                    <label class="form-check-label small" for="permUser">User Management</label>
                                </div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="permRole">
                                    <label class="form-check-label small" for="permRole">Role Management</label>
                                </div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="permResident" checked>
                                    <label class="form-check-label small" for="permResident">Resident Records</label>
                                </div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="permMedical">
                                    <label class="form-check-label small" for="permMedical">Medical Logs</label>
                                </div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="permDonations">
                                    <label class="form-check-label small" for="permDonations">Donation Portal</label>
                                </div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="permReports">
                                    <label class="form-check-label small" for="permReports">Reports & Analytics</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light p-3 rounded-bottom-4">
                <button type="button" class="btn btn-secondary px-4 rounded-3" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary px-4 rounded-3" onclick="alert('Role successfully created (Demo view).'); $('#addRoleModal').modal('hide');">
                    <i class="bi bi-check-circle me-1"></i>Save Role
                </button>
            </div>
        </div>
    </div>
</div>

<!-- =========================================================================
    MODAL 2: EDIT ROLE MODAL
========================================================================== -->
<div class="modal fade" id="editRoleModal" tabindex="-1" aria-labelledby="editRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white p-4 rounded-top-4">
                <h5 class="modal-title fw-bold" id="editRoleModalLabel">
                    <i class="bi bi-pencil-square me-2"></i>Edit Role & Permissions
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="editRoleForm">
                    <div class="mb-3">
                        <label for="editRoleTitle" class="form-label fw-semibold">Role Title</label>
                        <input type="text" class="form-control rounded-3" id="editRoleTitle" value="Doctor" required>
                    </div>
                    <div class="mb-3">
                        <label for="editRoleDesc" class="form-label fw-semibold">Description</label>
                        <textarea class="form-control rounded-3" id="editRoleDesc" rows="3">Access resident medical histories, prescribe medications, schedule health checkups, and record diagnosis notes.</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold mb-2">Active Permissions</label>
                        <div class="row g-3 bg-light p-3 rounded-3 border">
                            <div class="col-6 col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="editPerm1" checked>
                                    <label class="form-check-label small" for="editPerm1">Medical Records</label>
                                </div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="editPerm2" checked>
                                    <label class="form-check-label small" for="editPerm2">Prescriptions</label>
                                </div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="editPerm3" checked>
                                    <label class="form-check-label small" for="editPerm3">Health Log</label>
                                </div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="editPerm4">
                                    <label class="form-check-label small" for="editPerm4">Financial Data</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light p-3 rounded-bottom-4">
                <button type="button" class="btn btn-secondary px-4 rounded-3" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary px-4 rounded-3" onclick="alert('Role permissions updated (Demo view).');">
                    <i class="bi bi-check-circle me-1"></i>Update Role
                </button>
            </div>
        </div>
    </div>
</div>

<!-- =========================================================================
    MODAL 3: DELETE ROLE MODAL
========================================================================== -->
<div class="modal fade" id="deleteRoleModal" tabindex="-1" aria-labelledby="deleteRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-danger text-white p-4 rounded-top-4">
                <h5 class="modal-title fw-bold" id="deleteRoleModalLabel">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Confirm Role Deletion
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="text-danger mb-3">
                    <i class="bi bi-trash3-fill display-3"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2">Are you sure you want to delete this role?</h5>
                <p class="text-muted small mb-0">
                    Deleting this role will require reassigning all users currently assigned to it. This action cannot be undone.
                </p>
            </div>
            <div class="modal-footer bg-light p-3 rounded-bottom-4 justify-content-center">
                <button type="button" class="btn btn-secondary px-4 rounded-3" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger px-4 rounded-3" onclick="alert('Role deletion confirmed (Demo view).');">
                    <i class="bi bi-trash me-1"></i>Yes, Delete Role
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('roleSearchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const query = this.value.toLowerCase();
            const cards = document.querySelectorAll('.role-item');
            cards.forEach(card => {
                const text = card.textContent.toLowerCase();
                card.style.display = text.includes(query) ? 'block' : 'none';
            });
        });
    }
});
</script>

<?php require_once $base_path . 'includes/footer.php'; ?>
