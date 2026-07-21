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
    <title>Prescriptions - SevaNest OAHMS</title>
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
                <h2 class="h4 mb-0" style="color: var(--doctor-dark); font-family: 'Playfair Display', serif;">Prescription Management</h2>
                <button class="btn d-md-none" id="sidebarToggleBtn"><i class="bi bi-list"></i></button>
            </div>
            
            <div class="row g-4">
                <!-- Add Prescription Form -->
                <div class="col-lg-4">
                    <div class="card doctor-card">
                        <div class="card-header bg-white border-0 pt-4 pb-2">
                            <h5 class="mb-0" style="color: var(--doctor-dark);">New Prescription</h5>
                        </div>
                        <div class="card-body">
                            <form>
                                <div class="mb-3">
                                    <label class="form-label">Resident</label>
                                    <select class="form-select doctor-form-control">
                                        <option>Choose resident...</option>
                                        <option>Ramesh Kumar</option>
                                        <option>Sita Devi</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Medicine Name</label>
                                    <input type="text" class="form-control doctor-form-control" placeholder="e.g. Paracetamol 500mg">
                                </div>
                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <label class="form-label">Dosage</label>
                                        <select class="form-select doctor-form-control">
                                            <option>1-0-1</option>
                                            <option>1-1-1</option>
                                            <option>1-0-0</option>
                                            <option>0-0-1</option>
                                        </select>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label class="form-label">Duration</label>
                                        <input type="text" class="form-control doctor-form-control" placeholder="e.g. 5 Days">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Instructions</label>
                                    <textarea class="form-control doctor-form-control" rows="2" placeholder="e.g. After meals"></textarea>
                                </div>
                                <button type="button" class="btn btn-doctor-primary w-100">Add Prescription</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Existing Prescriptions Table -->
                <div class="col-lg-8">
                    <div class="card doctor-card">
                        <div class="card-header bg-white border-0 pt-4 pb-2">
                            <h5 class="mb-0" style="color: var(--doctor-dark);">Active Prescriptions</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <input type="text" class="form-control doctor-form-control" placeholder="Search prescriptions...">
                            </div>
                            <div class="table-responsive">
                                <table class="table doctor-table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Resident</th>
                                            <th>Medicine</th>
                                            <th>Dosage</th>
                                            <th>Duration</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Ramesh Kumar</td>
                                            <td>Telmisartan 40mg</td>
                                            <td>1-0-0 (Morning)</td>
                                            <td>30 Days</td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></button>
                                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Sita Devi</td>
                                            <td>Calcium + Vit D3</td>
                                            <td>1-0-1 (After Meals)</td>
                                            <td>60 Days</td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></button>
                                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                            </td>
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
