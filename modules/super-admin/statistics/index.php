<?php
$page_title = "Analytical Statistics";
$active_page = "statistics";
$path_to_root = "../../../";

include $path_to_root . 'includes/header.php';
include $path_to_root . 'includes/sidebar.php';
?>

<div class="main-content">
    <header class="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="toggle-sidebar-btn d-lg-none" aria-label="Toggle Navigation">
                <i class="bi bi-list"></i>
            </button>
            <h1 class="page-title m-0 h4 font-medium text-slate">Demographics & Analytics</h1>
        </div>
    </header>
    
    <div class="content-body container-fluid py-4 px-4">
        <div class="custom-card mb-4">
            <h2 class="h5 mb-4 text-slate border-bottom pb-3">SevaNest Analytics Dashboard</h2>
            
            <div class="row g-4 justify-content-center">
                <!-- Age Demographic Pie Chart -->
                <div class="col-12 col-md-5 d-flex flex-column align-items-center text-center">
                    <h3 class="h6 mb-3 text-slate font-medium">Residents Age Distribution</h3>
                    
                    <svg width="220" height="220" viewBox="0 0 36 36" style="transform: rotate(-90deg);">
                        <!-- Background empty circle -->
                        <circle cx="18" cy="18" r="15.915" fill="none" stroke="#F6F4EC" stroke-width="3.5"></circle>
                        <!-- Segment 1: Age 60-70 (40%) -> var(--primary-color) -->
                        <circle cx="18" cy="18" r="15.915" fill="none" stroke="#6B9080" stroke-width="3.5" stroke-dasharray="40 100" stroke-dashoffset="0"></circle>
                        <!-- Segment 2: Age 70-80 (35%) -> var(--accent-color) -->
                        <circle cx="18" cy="18" r="15.915" fill="none" stroke="#D4A373" stroke-width="3.5" stroke-dasharray="35 100" stroke-dashoffset="-40"></circle>
                        <!-- Segment 3: Age 80+ (25%) -> var(--success-color) -->
                        <circle cx="18" cy="18" r="15.915" fill="none" stroke="#7DA27D" stroke-width="3.5" stroke-dasharray="25 100" stroke-dashoffset="-75"></circle>
                    </svg>
                    
                    <div class="d-flex flex-column gap-1 align-items-start mt-3" style="font-size: 13px;">
                        <span class="d-flex align-items-center gap-2">
                            <span class="d-inline-block rounded-circle" style="width: 10px; height: 10px; background-color: #6B9080;"></span>
                            <strong>60-70 Years</strong> (40%)
                        </span>
                        <span class="d-flex align-items-center gap-2">
                            <span class="d-inline-block rounded-circle" style="width: 10px; height: 10px; background-color: #D4A373;"></span>
                            <strong>70-80 Years</strong> (35%)
                        </span>
                        <span class="d-flex align-items-center gap-2">
                            <span class="d-inline-block rounded-circle" style="width: 10px; height: 10px; background-color: #7DA27D;"></span>
                            <strong>80+ Years</strong> (25%)
                        </span>
                    </div>
                </div>
                
                <div class="col-12 col-md-7">
                    <h3 class="h6 mb-3 text-slate font-medium text-center text-md-start">Monthly Contributions (Year 2026)</h3>
                    
                    <!-- Bar Chart simulation -->
                    <div class="d-flex align-items-end justify-content-between border-start border-bottom px-2 py-3" style="height: 220px; border-color: rgba(0,0,0,0.1) !important;">
                        <div class="d-flex flex-column align-items-center w-15">
                            <div class="w-100 rounded-top" style="height: 70px; background-color: #6B9080;"></div>
                            <span class="text-muted mt-2" style="font-size: 11px;">April</span>
                        </div>
                        <div class="d-flex flex-column align-items-center w-15">
                            <div class="w-100 rounded-top" style="height: 120px; background-color: #D4A373;"></div>
                            <span class="text-muted mt-2" style="font-size: 11px;">May</span>
                        </div>
                        <div class="d-flex flex-column align-items-center w-15">
                            <div class="w-100 rounded-top" style="height: 160px; background-color: #6B9080;"></div>
                            <span class="text-muted mt-2" style="font-size: 11px;">June</span>
                        </div>
                        <div class="d-flex flex-column align-items-center w-15">
                            <div class="w-100 rounded-top" style="height: 190px; background-color: #7DA27D;"></div>
                            <span class="text-muted mt-2" style="font-size: 11px;">July</span>
                        </div>
                    </div>
                    
                    <p class="text-muted mt-4" style="font-size: 12px; text-align: justify; line-height: 1.5;">
                        Analytics are dynamically retrieved based on database registration entries. Contributions grew by <strong>18%</strong> in July, mostly generated through online transfers.
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <footer class="app-footer text-center py-3 bg-white border-top mt-auto" style="font-size: 13px; color: var(--text-color); font-weight: 500;">
        &copy; <?= date('Y'); ?> <strong>SevaNest</strong> - Old Age Home Management System. All rights reserved.
    </footer>
</div>

<?php include $path_to_root . 'includes/footer.php'; ?>
