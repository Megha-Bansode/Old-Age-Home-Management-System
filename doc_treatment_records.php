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
    <title>Treatment Records - SevaNest OAHMS</title>
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
                <h2 class="h4 mb-0" style="color: var(--doctor-dark); font-family: 'Playfair Display', serif;">Treatment Records</h2>
                <button class="btn d-md-none" id="sidebarToggleBtn"><i class="bi bi-list"></i></button>
            </div>
            
            <div class="card doctor-card">
                <div class="card-header bg-white border-0 pt-4 pb-2">
                    <h5 class="mb-0" style="color: var(--doctor-dark);">Ongoing & Completed Treatments</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table doctor-table table-hover">
                            <thead>
                                <tr>
                                    <th>Resident</th>
                                    <th>Diagnosis</th>
                                    <th>Treatment Plan</th>
                                    <th>Started On</th>
                                    <th>Status</th>
                                    <th>Progress Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Ramesh Kumar</td>
                                    <td>Type 2 Diabetes</td>
                                    <td>Insulin Therapy & Diet Control</td>
                                    <td>15 Jan 2026</td>
                                    <td><span class="badge badge-soft-warning">Ongoing</span></td>
                                    <td>Sugar levels stabilizing. Continue current dosage.</td>
                                </tr>
                                <tr>
                                    <td>Sita Devi</td>
                                    <td>Osteoarthritis</td>
                                    <td>Physiotherapy (2x/week)</td>
                                    <td>10 Feb 2026</td>
                                    <td><span class="badge badge-soft-warning">Ongoing</span></td>
                                    <td>Improved mobility in right knee.</td>
                                </tr>
                                <tr>
                                    <td>Anil Kapoor</td>
                                    <td>Seasonal Flu</td>
                                    <td>Rest, Hydration, Antivirals</td>
                                    <td>01 Jul 2026</td>
                                    <td><span class="badge badge-soft-success">Completed</span></td>
                                    <td>Fully recovered.</td>
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
