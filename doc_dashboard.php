<?php
// MOCK DATA SETUP
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$_SESSION['user_name'] = "Dr. Amit Sharma";
$_SESSION['user_role'] = "Doctor";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard - SevaNest OAHMS</title>
    <!-- Include Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- Existing Site CSS -->
    <link href="assets/css/layout.css" rel="stylesheet">
    <!-- Doctor Module specific CSS -->
    <link href="assets/css/doctor_module.css" rel="stylesheet">
</head>
<body class="doctor-module">
    <?php include 'includes/header.php'; ?>
    
    <div class="doctor-layout">
        <!-- Sidebar -->
        <?php include 'includes/doctor_sidebar.php'; ?>
        
        <!-- Main Content -->
        <main class="doctor-main-content p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h4 mb-0" style="color: var(--doctor-dark); font-family: 'Playfair Display', serif;">Welcome, Dr. Sharma</h2>
                <button class="btn d-md-none" id="sidebarToggleBtn"><i class="bi bi-list"></i></button>
            </div>
            
            <!-- Summary Cards -->
            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="card doctor-card h-100 p-3">
                        <div class="d-flex align-items-center">
                            <div class="doctor-card-icon icon-bg-primary me-3">
                                <i class="bi bi-person-check"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1">Today's Check-ups</h6>
                                <h3 class="mb-0">12</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card doctor-card h-100 p-3">
                        <div class="d-flex align-items-center">
                            <div class="doctor-card-icon icon-bg-danger me-3">
                                <i class="bi bi-capsule"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1">Pending Prescriptions</h6>
                                <h3 class="mb-0">5</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card doctor-card h-100 p-3">
                        <div class="d-flex align-items-center">
                            <div class="doctor-card-icon icon-bg-accent me-3">
                                <i class="bi bi-calendar-event"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1">Upcoming Follow-ups</h6>
                                <h3 class="mb-0">8</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Activity Table -->
            <div class="card doctor-card">
                <div class="card-header bg-white border-0 pt-4 pb-2">
                    <h5 class="mb-0" style="color: var(--doctor-dark);">Recent Activity</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table doctor-table table-hover">
                            <thead>
                                <tr>
                                    <th>Resident Name</th>
                                    <th>Activity</th>
                                    <th>Date/Time</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Ramesh Kumar</td>
                                    <td>Routine Check-up Completed</td>
                                    <td>Today, 10:30 AM</td>
                                    <td><span class="badge badge-soft-success">Done</span></td>
                                </tr>
                                <tr>
                                    <td>Sita Devi</td>
                                    <td>Prescription Renewed</td>
                                    <td>Today, 09:15 AM</td>
                                    <td><span class="badge badge-soft-success">Done</span></td>
                                </tr>
                                <tr>
                                    <td>Anil Kapoor</td>
                                    <td>Blood Test Required</td>
                                    <td>Yesterday</td>
                                    <td><span class="badge badge-soft-warning">Pending</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/doctor_module.js"></script>
</body>
</html>
