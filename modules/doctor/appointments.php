<?php
/**
 * SevaNest – Doctor Appointments Page
 * File     : modules/doctor/appointments.php
 * Version  : 1.0
 */

require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../config/database.php';

// Require Doctor login
require_login();

$base_path = '../../';
$page_title = 'Appointments | SevaNest';
$extra_css = [
    'assets/css/doctor.css'
];

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'doctor';
$currentPage   = 'appointments.php';
$sn_asset_root = "../../assets";
include '../../includes/sidebar.php';
?>

<!-- ═══════════════════════════════════════════════════════════════════════
     MAIN CONTENT AREA
     ═══════════════════════════════════════════════════════════════════════ -->
<main id="sn-main-content" role="main" aria-label="Doctor appointments content">
    <div class="doctor-page-wrapper">
        
        <!-- Header Strip -->
        <div class="dr-header-strip animate-fade-in">
            <div>
                <h2 class="dr-header-strip__title">Manage Appointments</h2>
                <p style="color: var(--color-text-muted-team); margin: 4px 0 0;">View, schedule or modify patient consultation slots.</p>
            </div>
            <div>
                <button class="btn btn-primary" id="addAppointmentBtn"><i class="bi bi-plus-circle me-2"></i>Add Appointment</button>
            </div>
        </div>

        <!-- Filter Toolbar -->
        <div class="toolbar">
            <div class="search inline">
                <span>🔎</span>
                <input placeholder="Search resident by name..." id="attSearch">
            </div>
            <select class="select">
                <option>All Types</option>
                <option>Routine Checkup</option>
                <option>Diabetic Review</option>
                <option>BP Monitoring</option>
                <option>Cardiac Review</option>
            </select>
            <select class="select">
                <option>All Statuses</option>
                <option>Confirmed</option>
                <option>Pending</option>
                <option>Completed</option>
                <option>Cancelled</option>
            </select>
            <input type="date" class="select">
        </div>

        <!-- Appointments Table Card -->
        <div class="card no-pad">
            <div class="table-wrap">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Resident</th>
                            <th>Age/Gender</th>
                            <th>Time Slot</th>
                            <th>Appointment Type</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="res-cell">
                                    <div class="res-photo">AK</div>
                                    <div><b>Mr. Arun Kapoor</b><em>Room 204</em></div>
                                </div>
                            </td>
                            <td>78 / Male</td>
                            <td>10:00 AM</td>
                            <td>Routine Checkup</td>
                            <td><span class="badge green">Confirmed</span></td>
                            <td>
                                <button class="btn tiny btn-outline-primary me-1"><i class="bi bi-eye"></i> View</button>
                                <button class="btn tiny btn-outline-warning me-1"><i class="bi bi-calendar-event"></i> Reschedule</button>
                                <button class="btn tiny btn-outline-danger"><i class="bi bi-x-circle"></i> Cancel</button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="res-cell">
                                    <div class="res-photo">MI</div>
                                    <div><b>Mrs. Meera Iyer</b><em>Room 118</em></div>
                                </div>
                            </td>
                            <td>72 / Female</td>
                            <td>11:30 AM</td>
                            <td>Diabetic Review</td>
                            <td><span class="badge amber">Pending</span></td>
                            <td>
                                <button class="btn tiny btn-outline-primary me-1"><i class="bi bi-eye"></i> View</button>
                                <button class="btn tiny btn-outline-warning me-1"><i class="bi bi-calendar-event"></i> Reschedule</button>
                                <button class="btn tiny btn-outline-danger"><i class="bi bi-x-circle"></i> Cancel</button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="res-cell">
                                    <div class="res-photo">VV</div>
                                    <div><b>Mr. Vijay Verma</b><em>Room 301</em></div>
                                </div>
                            </td>
                            <td>81 / Male</td>
                            <td>02:15 PM</td>
                            <td>BP Monitoring</td>
                            <td><span class="badge blue">Completed</span></td>
                            <td>
                                <button class="btn tiny btn-outline-primary me-1"><i class="bi bi-eye"></i> View</button>
                                <button class="btn tiny btn-outline-warning me-1" disabled><i class="bi bi-calendar-event"></i> Reschedule</button>
                                <button class="btn tiny btn-outline-danger" disabled><i class="bi bi-x-circle"></i> Cancel</button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="res-cell">
                                    <div class="res-photo">LR</div>
                                    <div><b>Mrs. Latha Rao</b><em>Room 302</em></div>
                                </div>
                            </td>
                            <td>75 / Female</td>
                            <td>03:45 PM</td>
                            <td>Cognitive Review</td>
                            <td><span class="badge green">Confirmed</span></td>
                            <td>
                                <button class="btn tiny btn-outline-primary me-1"><i class="bi bi-eye"></i> View</button>
                                <button class="btn tiny btn-outline-warning me-1"><i class="bi bi-calendar-event"></i> Reschedule</button>
                                <button class="btn tiny btn-outline-danger"><i class="bi bi-x-circle"></i> Cancel</button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="res-cell">
                                    <div class="res-photo">SN</div>
                                    <div><b>Mr. Suresh Nair</b><em>Room 210</em></div>
                                </div>
                            </td>
                            <td>69 / Male</td>
                            <td>04:30 PM</td>
                            <td>Cardiac Follow-up</td>
                            <td><span class="badge red">Cancelled</span></td>
                            <td>
                                <button class="btn tiny btn-outline-primary me-1"><i class="bi bi-eye"></i> View</button>
                                <button class="btn tiny btn-outline-warning me-1" disabled><i class="bi bi-calendar-event"></i> Reschedule</button>
                                <button class="btn tiny btn-outline-danger" disabled><i class="bi bi-x-circle"></i> Cancel</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="pagination">
                <span>Showing 1–5 of 14 entries</span>
                <div>
                    <button class="btn tiny">‹</button>
                    <button class="btn tiny active">1</button>
                    <button class="btn tiny">2</button>
                    <button class="btn tiny">3</button>
                    <button class="btn tiny">›</button>
                </div>
            </div>
        </div>

    </div>
</main>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
