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
    <title>Medical History - SevaNest OAHMS</title>
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
                <h2 class="h4 mb-0" style="color: var(--doctor-dark); font-family: 'Playfair Display', serif;">Medical History</h2>
                <button class="btn d-md-none" id="sidebarToggleBtn"><i class="bi bi-list"></i></button>
            </div>
            
            <div class="card doctor-card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <input type="text" class="form-control doctor-form-control" placeholder="Search resident by name or ID...">
                        </div>
                        <div class="col-md-3">
                            <select class="form-select doctor-form-control">
                                <option value="">Filter by Condition</option>
                                <option value="diabetes">Diabetes</option>
                                <option value="hypertension">Hypertension</option>
                                <option value="arthritis">Arthritis</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table doctor-table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Resident Name</th>
                                    <th>Age</th>
                                    <th>Known Conditions</th>
                                    <th>Last Check-up</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>#RES001</td>
                                    <td>Ramesh Kumar</td>
                                    <td>72</td>
                                    <td>Hypertension, Diabetes</td>
                                    <td>Today</td>
                                    <td>
                                        <button class="btn btn-sm btn-doctor-primary view-history-btn" data-id="RES001" data-name="Ramesh Kumar" data-bs-toggle="modal" data-bs-target="#historyModal">
                                            <i class="bi bi-eye"></i> View Timeline
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>#RES002</td>
                                    <td>Sita Devi</td>
                                    <td>68</td>
                                    <td>Arthritis</td>
                                    <td>10 Jan 2026</td>
                                    <td>
                                        <button class="btn btn-sm btn-doctor-primary view-history-btn" data-id="RES002" data-name="Sita Devi" data-bs-toggle="modal" data-bs-target="#historyModal">
                                            <i class="bi bi-eye"></i> View Timeline
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- History Modal -->
    <div class="modal fade" id="historyModal" tabindex="-1" aria-labelledby="historyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header" style="background-color: var(--doctor-primary); color: white;">
                    <h5 class="modal-title" id="historyModalLabel">Medical History</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="background-color: var(--doctor-light);">
                    <div class="medical-timeline" id="historyTimelineContent">
                        <!-- Populated by JS -->
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/doctor_module.js"></script>
</body>
</html>
