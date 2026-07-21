<?php
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
    <title>Follow-up Schedule - SevaNest OAHMS</title>
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
                <h2 class="h4 mb-0" style="color: var(--doctor-dark); font-family: 'Playfair Display', serif;">Follow-up Schedule</h2>
                <button class="btn d-md-none" id="sidebarToggleBtn"><i class="bi bi-list"></i></button>
            </div>
            
            <div class="row g-4">
                <div class="col-md-12">
                    <div class="card doctor-card">
                        <div class="card-header bg-white border-0 pt-4 pb-2 d-flex justify-content-between align-items-center">
                            <h5 class="mb-0" style="color: var(--doctor-dark);">Upcoming Appointments</h5>
                            <button class="btn btn-sm btn-doctor-primary"><i class="bi bi-plus-lg"></i> Schedule Follow-up</button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table doctor-table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Time</th>
                                            <th>Resident</th>
                                            <th>Reason for Visit</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>Tomorrow</strong></td>
                                            <td>09:00 AM</td>
                                            <td>Sita Devi</td>
                                            <td>Physiotherapy Review</td>
                                            <td><span class="badge badge-soft-warning">Scheduled</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>24 Jul 2026</strong></td>
                                            <td>11:30 AM</td>
                                            <td>Ramesh Kumar</td>
                                            <td>Blood Sugar Fasting Test</td>
                                            <td><span class="badge badge-soft-warning">Scheduled</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>28 Jul 2026</strong></td>
                                            <td>10:00 AM</td>
                                            <td>Anil Kapoor</td>
                                            <td>Post-Flu Routine Check</td>
                                            <td><span class="badge badge-soft-warning">Scheduled</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
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
