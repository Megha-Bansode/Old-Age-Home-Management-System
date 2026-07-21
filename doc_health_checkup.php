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
    <title>Health Check-up - SevaNest OAHMS</title>
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
                <h2 class="h4 mb-0" style="color: var(--doctor-dark); font-family: 'Playfair Display', serif;">Health Check-up</h2>
                <button class="btn d-md-none" id="sidebarToggleBtn"><i class="bi bi-list"></i></button>
            </div>
            
            <div class="row g-4">
                <!-- Log Checkup Form -->
                <div class="col-lg-5">
                    <div class="card doctor-card">
                        <div class="card-header bg-white border-0 pt-4 pb-2">
                            <h5 class="mb-0" style="color: var(--doctor-dark);">Log New Check-up</h5>
                        </div>
                        <div class="card-body">
                            <form>
                                <div class="mb-3">
                                    <label class="form-label">Select Resident</label>
                                    <select class="form-select doctor-form-control">
                                        <option>Choose resident...</option>
                                        <option>Ramesh Kumar (#RES001)</option>
                                        <option>Sita Devi (#RES002)</option>
                                    </select>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Blood Pressure</label>
                                        <input type="text" class="form-control doctor-form-control" placeholder="e.g. 120/80">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Sugar Level</label>
                                        <input type="text" class="form-control doctor-form-control" placeholder="e.g. 110 mg/dL">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Weight (kg)</label>
                                        <input type="number" class="form-control doctor-form-control" placeholder="e.g. 65">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Temperature (°F)</label>
                                        <input type="text" class="form-control doctor-form-control" placeholder="e.g. 98.6">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Symptoms & Notes</label>
                                    <textarea class="form-control doctor-form-control" rows="3" placeholder="Describe symptoms or diagnosis..."></textarea>
                                </div>
                                <button type="button" class="btn btn-doctor-primary w-100">Save Check-up Record</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Past Checkups Table -->
                <div class="col-lg-7">
                    <div class="card doctor-card">
                        <div class="card-header bg-white border-0 pt-4 pb-2">
                            <h5 class="mb-0" style="color: var(--doctor-dark);">Recent Check-ups</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table doctor-table table-hover text-sm">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Resident</th>
                                            <th>BP</th>
                                            <th>Sugar</th>
                                            <th>Vitals</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Today</td>
                                            <td>Ramesh Kumar</td>
                                            <td>125/82</td>
                                            <td>105</td>
                                            <td>65kg / 98.2°F</td>
                                        </tr>
                                        <tr>
                                            <td>Yesterday</td>
                                            <td>Sita Devi</td>
                                            <td>130/85</td>
                                            <td>115</td>
                                            <td>58kg / 98.4°F</td>
                                        </tr>
                                        <tr>
                                            <td>15 Jul 2026</td>
                                            <td>Anil Kapoor</td>
                                            <td>140/90 <i class="bi bi-exclamation-circle text-danger"></i></td>
                                            <td>145</td>
                                            <td>72kg / 99.0°F</td>
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
