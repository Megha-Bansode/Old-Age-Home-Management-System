<?php
$page_title = "Super Admin Dashboard";
$active_page = "dashboard";
$path_to_root = "../../../";

// Include Header
include $path_to_root . 'includes/header.php';
// Include Sidebar
include $path_to_root . 'includes/sidebar.php';
?>

<div class="main-content">
    <!-- Topbar -->
    <header class="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="toggle-sidebar-btn d-lg-none" aria-label="Toggle Navigation">
                <i class="bi bi-list"></i>
            </button>
            <h1 class="page-title m-0 h4 font-medium text-slate">Dashboard Overview</h1>
        </div>
        
        <div class="d-flex align-items-center gap-3">
            <div class="d-none d-md-flex align-items-center text-muted me-2" style="font-size: 14px;">
                <i class="bi bi-calendar3 me-2"></i>
                <span><?php echo date('l, F j, Y'); ?></span>
            </div>
            
            <div class="vr d-none d-md-block" style="height: 24px; color: #ccc;"></div>
            
            <!-- Quick Notification Icon -->
            <button class="btn btn-light rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; position: relative;">
                <i class="bi bi-bell text-slate" style="font-size: 18px;"></i>
                <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                    <span class="visually-hidden">New alerts</span>
                </span>
            </button>
        </div>
    </header>
    
    <!-- Content Body -->
    <div class="content-body container-fluid py-4 px-4">
        
        <!-- Welcome Banner -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="custom-card text-white p-4 d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, var(--primary-color), #58796a); border: none;">
                    <div class="py-2">
                        <h2 class="h3 mb-2 text-white">Welcome Back, Administrator!</h2>
                        <p class="mb-0 opacity-90" style="font-size: 15px; max-width: 600px;">
                            Here is the system overview for <strong>SevaNest - Old Age Home</strong> today. You have 3 pending visitor approvals and 2 custom reports generated this morning.
                        </p>
                    </div>
                    <div class="d-none d-lg-block">
                        <i class="bi bi-sun-fill text-warning opacity-75" style="font-size: 64px;"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Stats Widgets -->
        <div class="row g-4 mb-4">
            <!-- Card 1: Residents -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="custom-card d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted d-block mb-1 font-medium" style="font-size: 14px;">Total Residents</span>
                        <span class="h2 mb-0 font-heading fw-bold" style="color: var(--text-color);">48</span>
                    </div>
                    <div class="card-icon primary">
                        <i class="bi bi-house-heart"></i>
                    </div>
                </div>
            </div>
            
            <!-- Card 2: Staff on Duty -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="custom-card d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted d-block mb-1 font-medium" style="font-size: 14px;">Staff Members</span>
                        <span class="h2 mb-0 font-heading fw-bold" style="color: var(--text-color);">12</span>
                    </div>
                    <div class="card-icon accent">
                        <i class="bi bi-person-badge"></i>
                    </div>
                </div>
            </div>
            
            <!-- Card 3: Monthly Donations -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="custom-card d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted d-block mb-1 font-medium" style="font-size: 14px;">Donations (This Month)</span>
                        <span class="h2 mb-0 font-heading fw-bold" style="color: var(--text-color);">₹ 85,250</span>
                    </div>
                    <div class="card-icon success">
                        <i class="bi bi-wallet2"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row g-4">
            <!-- Recent Activity Table -->
            <div class="col-12 col-xl-8">
                <div class="custom-card h-100">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="h5 mb-0 text-slate">Recent Operations Activity</h3>
                        <a href="<?php echo $path_to_root; ?>modules/super-admin/reports/index.php" class="btn btn-link text-decoration-none p-0" style="color: var(--primary-color); font-weight: 600;">
                            View Audit Logs <i class="bi bi-arrow-right-short"></i>
                        </a>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr style="font-size: 12px; text-transform: uppercase; color: var(--text-color); font-weight: 700;">
                                    <th scope="col" class="ps-3">Audit ID</th>
                                    <th scope="col">User Account</th>
                                    <th scope="col">Action Occurred</th>
                                    <th scope="col">Status</th>
                                    <th scope="col" class="pe-3">Date Time</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 14px; color: var(--text-color);">
                                <tr>
                                    <td class="ps-3 font-medium text-muted">#AUD-104</td>
                                    <td>admin@sevanest.com</td>
                                    <td>Updated system notification gates</td>
                                    <td><span class="badge" style="background-color: var(--primary-light); color: var(--primary-color);">Success</span></td>
                                    <td class="pe-3 text-muted">Today, 11:32 AM</td>
                                </tr>
                                <tr>
                                    <td class="ps-3 font-medium text-muted">#AUD-103</td>
                                    <td>manager@sevanest.com</td>
                                    <td>Registered new resident (Devendra Nath)</td>
                                    <td><span class="badge" style="background-color: var(--primary-light); color: var(--primary-color);">Success</span></td>
                                    <td class="pe-3 text-muted">Today, 10:15 AM</td>
                                </tr>
                                <tr>
                                    <td class="ps-3 font-medium text-muted">#AUD-102</td>
                                    <td>finance@sevanest.com</td>
                                    <td>Recorded donation receipt (₹ 50,000)</td>
                                    <td><span class="badge" style="background-color: var(--primary-light); color: var(--primary-color);">Success</span></td>
                                    <td class="pe-3 text-muted">Yesterday, 4:20 PM</td>
                                </tr>
                                <tr>
                                    <td class="ps-3 font-medium text-muted">#AUD-101</td>
                                    <td>staff@sevanest.com</td>
                                    <td>Modified room allocation Wing A-102</td>
                                    <td><span class="badge" style="background-color: var(--primary-light); color: var(--primary-color);">Success</span></td>
                                    <td class="pe-3 text-muted">Yesterday, 1:45 PM</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions Panel -->
            <div class="col-12 col-xl-4">
                <div class="custom-card h-100">
                    <h3 class="h5 mb-4 text-slate">Quick Actions Portal</h3>
                    
                    <div class="d-flex flex-column gap-3">
                        <a href="<?php echo $path_to_root; ?>modules/super-admin/users/add-user.php" class="btn btn-secondary-custom d-flex align-items-center gap-3 text-start p-3 w-100 border">
                            <span class="card-icon primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-size: 18px;">
                                <i class="bi bi-person-plus-fill"></i>
                            </span>
                            <div>
                                <span class="d-block text-slate font-medium" style="font-size: 15px;">Create System User</span>
                                <small class="text-muted" style="font-size: 12px;">Add managers or volunteers</small>
                            </div>
                        </a>
                        
                        <a href="<?php echo $path_to_root; ?>modules/super-admin/roles/add-role.php" class="btn btn-secondary-custom d-flex align-items-center gap-3 text-start p-3 w-100 border">
                            <span class="card-icon accent rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-size: 18px;">
                                <i class="bi bi-shield-plus"></i>
                            </span>
                            <div>
                                <span class="d-block text-slate font-medium" style="font-size: 15px;">Define Access Role</span>
                                <small class="text-muted" style="font-size: 12px;">Configure modules permissions</small>
                            </div>
                        </a>
                        
                        <a href="<?php echo $path_to_root; ?>modules/super-admin/settings/general.php" class="btn btn-secondary-custom d-flex align-items-center gap-3 text-start p-3 w-100 border">
                            <span class="card-icon success rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-size: 18px;">
                                <i class="bi bi-sliders"></i>
                            </span>
                            <div>
                                <span class="d-block text-slate font-medium" style="font-size: 15px;">Configure SevaNest</span>
                                <small class="text-muted" style="font-size: 12px;">Adjust general system values</small>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
    
    <!-- Footer -->
    <footer class="app-footer text-center py-3 bg-white border-top mt-auto" style="font-size: 13px; color: var(--text-color); font-weight: 500;">
        &copy; <?php echo date('Y'); ?> <strong>SevaNest</strong> - Old Age Home Management System. All rights reserved.
    </footer>
</div>

<?php
// Include Footer
include $path_to_root . 'includes/footer.php';
?>
