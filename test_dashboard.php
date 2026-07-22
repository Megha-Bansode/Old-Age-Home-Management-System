<?php
/**
 * OAHMS Test Dashboard and Verification Page
 *
 * This file demonstrates how to include the header and footer, 
 * and provides interactive controls to test different user sessions.
 */

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Handle login/logout simulation via query parameters
if (isset($_GET['action'])) {
    if ($_GET['action'] === 'login') {
        $_SESSION['user_name'] = isset($_GET['name']) ? $_GET['name'] : 'Dr. Anjali Sharma';
        $_SESSION['user_role'] = isset($_GET['role']) ? $_GET['role'] : 'Doctor';
        header("Location: test_dashboard.php");
        exit();
    } elseif ($_GET['action'] === 'logout') {
        session_unset();
        session_destroy();
        header("Location: test_dashboard.php");
        exit();
    }
}

// Define the page title before including the header
$page_title = "Verification Dashboard — OAHMS";
include 'includes/header.php';

// Check if user is logged in
$is_logged_in = isset($_SESSION['user_name']);
$current_user = $is_logged_in ? $_SESSION['user_name'] : 'Guest';
$current_role = $is_logged_in ? $_SESSION['user_role'] : 'None';
?>

<!-- Custom CSS for the dashboard content -->
<style>
    .demo-card {
        background-color: #FFFFFF;
        border: 1px solid rgba(107, 144, 128, 0.2);
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(47, 58, 58, 0.05);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .demo-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(47, 58, 58, 0.08);
    }
    .role-badge {
        background-color: var(--oahms-primary);
        color: var(--oahms-secondary);
        font-weight: 600;
        padding: 0.35rem 0.75rem;
        border-radius: 50px;
    }
    .state-badge {
        background-color: var(--oahms-success);
        color: #FFFFFF;
        font-weight: 600;
        padding: 0.35rem 0.75rem;
        border-radius: 50px;
    }
    .accent-btn {
        background-color: var(--oahms-accent) !important;
        border-color: var(--oahms-accent) !important;
        color: #FFFFFF !important;
        font-weight: 600;
        transition: all 0.2s ease;
    }
    .accent-btn:hover {
        background-color: var(--oahms-primary) !important;
        border-color: var(--oahms-primary) !important;
        color: var(--oahms-secondary) !important;
    }
</style>

<div class="container py-5">
    <!-- Hero / Header Section -->
    <div class="row mb-5 text-center text-md-start align-items-center">
        <div class="col-md-8">
            <h1 class="display-5 fw-bold text-dark mb-2">Verification Dashboard</h1>
            <p class="lead text-muted">Test page demonstrating OAHMS reusable headers, footers, responsiveness, and session support.</p>
        </div>
        <div class="col-md-4 text-center text-md-end mt-3 mt-md-0">
            <span class="fs-6 me-2">Current Auth State:</span>
            <?php if ($is_logged_in): ?>
                <span class="state-badge"><i class="bi bi-shield-check-fill me-1"></i>Logged In</span>
            <?php else: ?>
                <span class="badge bg-secondary py-2 px-3"><i class="bi bi-shield-lock-fill me-1"></i>Guest Mode</span>
            <?php endif; ?>
        </div>
    </div>

    <div class="row g-4">
        <!-- Sidebar Controls to simulate login states -->
        <div class="col-lg-4">
            <div class="demo-card p-4 h-100">
                <h4 class="fw-bold mb-3 text-dark"><i class="bi bi-sliders me-2"></i>Session Controls</h4>
                <p class="text-muted small mb-4">Click any option below to dynamically write variables to the PHP session and test layout changes.</p>
                
                <div class="d-grid gap-2">
                    <a href="test_dashboard.php?action=login&name=Dr.+Rahul+Sharma&role=Doctor" 
                       class="btn btn-outline-dark text-start py-2.5 <?php echo ($current_role === 'Doctor') ? 'active' : ''; ?>">
                        <i class="bi bi-heart-pulse-fill me-2 text-danger"></i> Simulate Doctor Login
                    </a>
                    
                    <a href="test_dashboard.php?action=login&name=Sarah+Jenkinson&role=Caretaker" 
                       class="btn btn-outline-dark text-start py-2.5 <?php echo ($current_role === 'Caretaker') ? 'active' : ''; ?>">
                        <i class="bi bi-person-heart me-2 text-warning"></i> Simulate Caretaker Login
                    </a>
                    
                    <a href="test_dashboard.php?action=login&name=Golden+Trusts&role=Donor" 
                       class="btn btn-outline-dark text-start py-2.5 <?php echo ($current_role === 'Donor') ? 'active' : ''; ?>">
                        <i class="bi bi-cash-coin me-2 text-success"></i> Simulate Donor Login
                    </a>
                    
                    <a href="test_dashboard.php?action=login&name=Super+Administrator&role=Super+Admin" 
                       class="btn btn-outline-dark text-start py-2.5 <?php echo ($current_role === 'Super Admin') ? 'active' : ''; ?>">
                        <i class="bi bi-shield-lock-fill me-2 text-primary"></i> Simulate Super Admin Login
                    </a>
                    
                    <a href="test_dashboard.php?action=login&name=Megha+Bansode&role=Old+Age+Home+Admin" 
                       class="btn btn-outline-dark text-start py-2.5 <?php echo ($current_role === 'Old Age Home Admin') ? 'active' : ''; ?>">
                        <i class="bi bi-houses-fill me-2 text-info"></i> Simulate Home Admin Login
                    </a>
                    
                    <a href="test_dashboard.php?action=login&name=John+Doe&role=Family+Member" 
                       class="btn btn-outline-dark text-start py-2.5 <?php echo ($current_role === 'Family Member') ? 'active' : ''; ?>">
                        <i class="bi bi-people-fill me-2 text-secondary"></i> Simulate Family Login
                    </a>

                    <div class="hr my-2 border-top border-light"></div>
                    
                    <?php if ($is_logged_in): ?>
                        <a href="test_dashboard.php?action=logout" class="btn btn-danger py-2.5">
                            <i class="bi bi-box-arrow-right me-2"></i> Clear Session (Logout)
                        </a>
                    <?php else: ?>
                        <button class="btn btn-secondary py-2.5" disabled>
                            <i class="bi bi-dash-circle me-2"></i> Session is Empty
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Main Display Pane -->
        <div class="col-lg-8">
            <div class="demo-card p-4 h-100">
                <h4 class="fw-bold mb-3 text-dark"><i class="bi bi-file-earmark-code me-2"></i>Session Data View</h4>
                
                <table class="table table-bordered mb-4">
                    <thead class="table-light">
                        <tr>
                            <th>Session Variable</th>
                            <th>Current Value</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>$_SESSION['user_name']</code></td>
                            <td>
                                <?php if ($is_logged_in): ?>
                                    <span class="fw-semibold text-primary"><?php echo htmlspecialchars($current_user); ?></span>
                                <?php else: ?>
                                    <span class="text-muted"><em>Not Set</em></span>
                                <?php endif; ?>
                            </td>
                            <td class="small text-muted">Full name of the authenticated user.</td>
                        </tr>
                        <tr>
                            <td><code>$_SESSION['user_role']</code></td>
                            <td>
                                <?php if ($is_logged_in): ?>
                                    <span class="role-badge"><?php echo htmlspecialchars($current_role); ?></span>
                                <?php else: ?>
                                    <span class="text-muted"><em>Not Set</em></span>
                                <?php endif; ?>
                            </td>
                            <td class="small text-muted">Access role determining user dashboard home.</td>
                        </tr>
                    </tbody>
                </table>

                <h5 class="fw-bold text-dark mb-3">Verification Checklist</h5>
                <div class="row row-cols-1 row-cols-md-2 g-3">
                    <div class="col">
                        <div class="p-3 border rounded-3 h-100 bg-light">
                            <h6 class="fw-bold text-dark"><i class="bi bi-clock-fill me-1 text-primary"></i> Live Clock</h6>
                            <p class="small text-muted mb-0">Check navbar center: clock must update seconds smoothly without page refreshes.</p>
                        </div>
                    </div>
                    <div class="col">
                        <div class="p-3 border rounded-3 h-100 bg-light">
                            <h6 class="fw-bold text-dark"><i class="bi bi-bell-fill me-1 text-warning"></i> Notifications</h6>
                            <p class="small text-muted mb-0">Verify bell shows unread badge when logged in, opening notification summaries on click.</p>
                        </div>
                    </div>
                    <div class="col">
                        <div class="p-3 border rounded-3 h-100 bg-light">
                            <h6 class="fw-bold text-dark"><i class="bi bi-person-circle me-1 text-info"></i> Responsive Header</h6>
                            <p class="small text-muted mb-0">Resize browser to mobile size: user avatar dropdown persists while labels collapse gracefully.</p>
                        </div>
                    </div>
                    <div class="col">
                        <div class="p-3 border rounded-3 h-100 bg-light">
                            <h6 class="fw-bold text-dark"><i class="bi bi-layout-wtf me-1 text-success"></i> Sticky Footer</h6>
                            <p class="small text-muted mb-0">On short content pages, layout flexbox prevents the footer from floating mid-screen.</p>
                        </div>
                    </div>
                </div>

                <div class="mt-4 p-3 bg-secondary bg-opacity-10 border rounded-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold mb-1">Simulate Landing Page View</h6>
                        <p class="small text-muted mb-0">Clicking logout transitions navbar to public visitor header layout.</p>
                    </div>
                    <a href="test_dashboard.php?action=logout" class="btn accent-btn btn-sm">
                        <i class="bi bi-eye"></i> Guest View
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
// Include the shared footer component
include 'includes/footer.php'; 
?>
