<?php
/**
 * SevaNest — Super Admin Reports & Analytics
 * 
 * Generates tabbed system-wide reports for Residents, Donations, Medical, Attendance, and Visitors.
 */

$base_path = '../../../';
$page_title = 'System Reports';
$active_page = 'reports';
$module_name = 'Super Admin Module';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_name'])) {
    $_SESSION['user_name'] = 'Super Admin';
    $_SESSION['user_role'] = 'Super Admin';
}

require_once $base_path . 'includes/header.php';
require_once $base_path . 'includes/navbar.php';
?>

<div class="d-flex min-vh-100 position-relative">
    
    <!-- Sidebar Include -->
    <?php require_once $base_path . 'includes/sidebar.php'; ?>

    <!-- Main Content Body -->
    <main class="main-content flex-grow-1 bg-light p-4">
        
        <!-- Page Header Strip -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4 p-4 bg-white rounded-4 shadow-sm border-start border-4 border-primary">
            <div>
                <h2 class="fw-bold text-dark mb-0">System Reports & Data Analytics</h2>
                <p class="text-muted small mb-0 mt-1">Generate, audit, filter, and export comprehensive administrative logs and records.</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-danger d-flex align-items-center gap-1 rounded-3" onclick="alert('Exporting PDF report...');">
                    <i class="bi bi-file-earmark-pdf-fill"></i>
                    <span>Export PDF</span>
                </button>
                <button class="btn btn-outline-success d-flex align-items-center gap-1 rounded-3" onclick="alert('Exporting Excel spreadsheet...');">
                    <i class="bi bi-file-earmark-excel-fill"></i>
                    <span>Export Excel</span>
                </button>
                <button class="btn btn-secondary d-flex align-items-center gap-1 rounded-3" onclick="window.print();">
                    <i class="bi bi-printer-fill"></i>
                    <span>Print</span>
                </button>
            </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
            <div class="card-body p-3">
                <div class="row g-3 align-items-center">
                    <div class="col-12 col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" id="reportSearch" class="form-control bg-light border-start-0" placeholder="Search records in active report tab...">
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <select class="form-select bg-light border-0">
                            <option selected>All Time</option>
                            <option>This Month</option>
                            <option>Last 30 Days</option>
                            <option>This Quarter</option>
                            <option>This Year</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-4 text-end">
                        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">
                            <i class="bi bi-funnel-fill text-primary me-1"></i>Filters Active
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Report Tabs Navigation -->
        <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
            <div class="card-header bg-transparent border-bottom-0 p-3 pb-0">
                <ul class="nav nav-tabs card-header-tabs border-bottom-0" id="reportTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-semibold" id="tab-resident" data-bs-toggle="tab" data-bs-target="#panel-resident" type="button" role="tab" aria-controls="panel-resident" aria-selected="true">
                            <i class="bi bi-heart-pulse-fill me-2 text-danger"></i>Resident Report
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-semibold" id="tab-donation" data-bs-toggle="tab" data-bs-target="#panel-donation" type="button" role="tab" aria-controls="panel-donation" aria-selected="false">
                            <i class="bi bi-cash-coin me-2 text-success"></i>Donation Report
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-semibold" id="tab-medical" data-bs-toggle="tab" data-bs-target="#panel-medical" type="button" role="tab" aria-controls="panel-medical" aria-selected="false">
                            <i class="bi bi-hospital-fill me-2 text-info"></i>Medical Report
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-semibold" id="tab-attendance" data-bs-toggle="tab" data-bs-target="#panel-attendance" type="button" role="tab" aria-controls="panel-attendance" aria-selected="false">
                            <i class="bi bi-calendar-check-fill me-2 text-warning"></i>Attendance Report
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-semibold" id="tab-visitor" data-bs-toggle="tab" data-bs-target="#panel-visitor" type="button" role="tab" aria-controls="panel-visitor" aria-selected="false">
                            <i class="bi bi-people-fill me-2 text-primary"></i>Visitor Report
                        </button>
                    </li>
                </ul>
            </div>
            
            <div class="card-body p-0">
                <div class="tab-content" id="reportTabsContent">
                    
                    <!-- TAB 1: RESIDENT REPORT -->
                    <div class="tab-pane fade show active" id="panel-resident" role="tabpanel" aria-labelledby="tab-resident">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Resident ID</th>
                                        <th>Full Name</th>
                                        <th>Age / Gender</th>
                                        <th>Admitted Date</th>
                                        <th>Room No.</th>
                                        <th>Emergency Contact</th>
                                        <th class="pe-4 text-center">Care Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="ps-4 fw-bold">#RES-101</td>
                                        <td class="fw-semibold">Ramchandra Verma</td>
                                        <td>76 / Male</td>
                                        <td>15 Jan 2023</td>
                                        <td>A-204</td>
                                        <td>+91 98234 11223</td>
                                        <td class="pe-4 text-center"><span class="badge bg-success-subtle text-success px-3 py-1">Active</span></td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4 fw-bold">#RES-102</td>
                                        <td class="fw-semibold">Savitri Devi</td>
                                        <td>81 / Female</td>
                                        <td>04 Mar 2023</td>
                                        <td>B-102</td>
                                        <td>+91 98765 44321</td>
                                        <td class="pe-4 text-center"><span class="badge bg-success-subtle text-success px-3 py-1">Active</span></td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4 fw-bold">#RES-103</td>
                                        <td class="fw-semibold">Gopalrao Joshi</td>
                                        <td>69 / Male</td>
                                        <td>20 Aug 2023</td>
                                        <td>A-108</td>
                                        <td>+91 94221 88990</td>
                                        <td class="pe-4 text-center"><span class="badge bg-warning-subtle text-warning px-3 py-1">Under Medical Observation</span></td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4 fw-bold">#RES-104</td>
                                        <td class="fw-semibold">Kamla Sharma</td>
                                        <td>74 / Female</td>
                                        <td>11 Nov 2023</td>
                                        <td>C-301</td>
                                        <td>+91 91122 33445</td>
                                        <td class="pe-4 text-center"><span class="badge bg-success-subtle text-success px-3 py-1">Active</span></td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4 fw-bold">#RES-105</td>
                                        <td class="fw-semibold">Bhaskar Rao</td>
                                        <td>84 / Male</td>
                                        <td>02 Feb 2024</td>
                                        <td>A-110</td>
                                        <td>+91 97654 32109</td>
                                        <td class="pe-4 text-center"><span class="badge bg-success-subtle text-success px-3 py-1">Active</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TAB 2: DONATION REPORT -->
                    <div class="tab-pane fade" id="panel-donation" role="tabpanel" aria-labelledby="tab-donation">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Receipt No.</th>
                                        <th>Donor Name</th>
                                        <th>Category</th>
                                        <th>Amount</th>
                                        <th>Payment Mode</th>
                                        <th>Date</th>
                                        <th class="pe-4 text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="ps-4 fw-bold">#DON-5021</td>
                                        <td class="fw-semibold">Suresh Patel</td>
                                        <td>Healthcare Supplies</td>
                                        <td class="fw-bold text-success">₹15,000</td>
                                        <td>UPI / Online</td>
                                        <td>24 Jul 2026</td>
                                        <td class="pe-4 text-center"><span class="badge bg-success-subtle text-success">Completed</span></td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4 fw-bold">#DON-5022</td>
                                        <td class="fw-semibold">Sunita Kulkarni</td>
                                        <td>Meal Sponsorship</td>
                                        <td class="fw-bold text-success">₹25,000</td>
                                        <td>Bank Transfer</td>
                                        <td>22 Jul 2026</td>
                                        <td class="pe-4 text-center"><span class="badge bg-success-subtle text-success">Completed</span></td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4 fw-bold">#DON-5023</td>
                                        <td class="fw-semibold">Rotary Club Pune</td>
                                        <td>Infrastructure & Beds</td>
                                        <td class="fw-bold text-success">₹1,50,000</td>
                                        <td>Cheque</td>
                                        <td>18 Jul 2026</td>
                                        <td class="pe-4 text-center"><span class="badge bg-success-subtle text-success">Completed</span></td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4 fw-bold">#DON-5024</td>
                                        <td class="fw-semibold">Anil Deshmukh</td>
                                        <td>General Maintenance</td>
                                        <td class="fw-bold text-success">₹5,000</td>
                                        <td>Cash</td>
                                        <td>15 Jul 2026</td>
                                        <td class="pe-4 text-center"><span class="badge bg-success-subtle text-success">Completed</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TAB 3: MEDICAL REPORT -->
                    <div class="tab-pane fade" id="panel-medical" role="tabpanel" aria-labelledby="tab-medical">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Log ID</th>
                                        <th>Resident Name</th>
                                        <th>Attending Doctor</th>
                                        <th>Diagnosis / Checkup</th>
                                        <th>Prescription Summary</th>
                                        <th>Date</th>
                                        <th class="pe-4 text-center">Follow-up</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="ps-4 fw-bold">#MED-801</td>
                                        <td class="fw-semibold">Ramchandra Verma</td>
                                        <td>Dr. Rajesh Kumar</td>
                                        <td>Hypertension Check</td>
                                        <td>Amlodipine 5mg OD</td>
                                        <td>25 Jul 2026</td>
                                        <td class="pe-4 text-center"><span class="badge bg-info-subtle text-info">In 7 Days</span></td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4 fw-bold">#MED-802</td>
                                        <td class="fw-semibold">Savitri Devi</td>
                                        <td>Dr. Priya Nair</td>
                                        <td>Arthritis Routine Examination</td>
                                        <td>Physiotherapy & Calcium Tabs</td>
                                        <td>23 Jul 2026</td>
                                        <td class="pe-4 text-center"><span class="badge bg-secondary-subtle text-dark">Routine</span></td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4 fw-bold">#MED-803</td>
                                        <td class="fw-semibold">Gopalrao Joshi</td>
                                        <td>Dr. Rajesh Kumar</td>
                                        <td>Diabetic Sugar Level Check</td>
                                        <td>Insulin Dosage Adjusted</td>
                                        <td>21 Jul 2026</td>
                                        <td class="pe-4 text-center"><span class="badge bg-warning-subtle text-warning">Urgent (3 Days)</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TAB 4: ATTENDANCE REPORT -->
                    <div class="tab-pane fade" id="panel-attendance" role="tabpanel" aria-labelledby="tab-attendance">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Employee ID</th>
                                        <th>Staff Name</th>
                                        <th>Role</th>
                                        <th>Shift Timing</th>
                                        <th>Check-in</th>
                                        <th>Check-out</th>
                                        <th class="pe-4 text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="ps-4 fw-bold">#STF-014</td>
                                        <td class="fw-semibold">Ananya Verma</td>
                                        <td>Caretaker</td>
                                        <td>Morning (07:00 - 15:00)</td>
                                        <td>06:55 AM</td>
                                        <td>03:05 PM</td>
                                        <td class="pe-4 text-center"><span class="badge bg-success-subtle text-success">Present</span></td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4 fw-bold">#STF-019</td>
                                        <td class="fw-semibold">Vikram Singh</td>
                                        <td>Security Guard</td>
                                        <td>Night (23:00 - 07:00)</td>
                                        <td>10:50 PM</td>
                                        <td>07:00 AM</td>
                                        <td class="pe-4 text-center"><span class="badge bg-success-subtle text-success">Present</span></td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4 fw-bold">#STF-022</td>
                                        <td class="fw-semibold">Pooja Hegde</td>
                                        <td>Nurse</td>
                                        <td>Evening (15:00 - 23:00)</td>
                                        <td>02:58 PM</td>
                                        <td>—</td>
                                        <td class="pe-4 text-center"><span class="badge bg-info-subtle text-info">On Duty</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TAB 5: VISITOR REPORT -->
                    <div class="tab-pane fade" id="panel-visitor" role="tabpanel" aria-labelledby="tab-visitor">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Pass No.</th>
                                        <th>Visitor Name</th>
                                        <th>Resident Visited</th>
                                        <th>Relation</th>
                                        <th>Date & Time</th>
                                        <th>Purpose</th>
                                        <th class="pe-4 text-center">Approval</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="ps-4 fw-bold">#VIS-901</td>
                                        <td class="fw-semibold">Meera Sharma</td>
                                        <td>Kamla Sharma (#RES-104)</td>
                                        <td>Daughter</td>
                                        <td>24 Jul 2026, 04:30 PM</td>
                                        <td>Weekend Family Visit</td>
                                        <td class="pe-4 text-center"><span class="badge bg-success-subtle text-success">Approved</span></td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4 fw-bold">#VIS-902</td>
                                        <td class="fw-semibold">Rahul Joshi</td>
                                        <td>Gopalrao Joshi (#RES-103)</td>
                                        <td>Son</td>
                                        <td>23 Jul 2026, 02:00 PM</td>
                                        <td>Document Signatures</td>
                                        <td class="pe-4 text-center"><span class="badge bg-success-subtle text-success">Approved</span></td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4 fw-bold">#VIS-903</td>
                                        <td class="fw-semibold">Dr. Amit Varma</td>
                                        <td>Ramchandra Verma (#RES-101)</td>
                                        <td>External Specialist</td>
                                        <td>20 Jul 2026, 11:00 AM</td>
                                        <td>Special Consultation</td>
                                        <td class="pe-4 text-center"><span class="badge bg-success-subtle text-success">Approved</span></td>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('reportSearch');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const query = this.value.toLowerCase();
            const activeTab = document.querySelector('.tab-pane.active');
            if (activeTab) {
                const rows = activeTab.querySelectorAll('tbody tr');
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(query) ? '' : 'none';
                });
            }
        });
    }
});
</script>

<?php require_once $base_path . 'includes/footer.php'; ?>
