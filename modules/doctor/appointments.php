<?php
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../config/database.php';

// Require Doctor login
require_login();
require_role('Doctor');

$pdo = get_db_connection();
$user_id = $_SESSION['user_id'] ?? 3; // Default to Dr. Priya Nair

$formSuccess = '';
$formError = '';

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $resident_id = (int)($_POST['resident_id'] ?? 0);
        $appointment_date = trim($_POST['appointment_date'] ?? '');
        $reason = trim($_POST['reason'] ?? '');
        $notes = trim($_POST['notes'] ?? '');
        
        if ($resident_id > 0 && !empty($appointment_date) && !empty($reason)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO appointments (resident_id, doctor_id, appointment_date, reason, status, notes) VALUES (?, ?, ?, ?, 'Scheduled', ?)");
                $stmt->execute([$resident_id, $user_id, $appointment_date, $reason, $notes]);
                $formSuccess = "Appointment scheduled successfully!";
            } catch (Exception $e) {
                $formError = "Error scheduling appointment: " . $e->getMessage();
            }
        } else {
            $formError = "Please fill in all required fields.";
        }
    } elseif ($action === 'cancel') {
        $appointment_id = (int)($_POST['appointment_id'] ?? 0);
        try {
            $stmt = $pdo->prepare("UPDATE appointments SET status = 'Cancelled' WHERE appointment_id = ? AND doctor_id = ?");
            $stmt->execute([$appointment_id, $user_id]);
            $formSuccess = "Appointment cancelled successfully!";
        } catch (Exception $e) {
            $formError = "Error cancelling appointment: " . $e->getMessage();
        }
    } elseif ($action === 'reschedule') {
        $appointment_id = (int)($_POST['appointment_id'] ?? 0);
        $appointment_date = trim($_POST['appointment_date'] ?? '');
        if ($appointment_id > 0 && !empty($appointment_date)) {
            try {
                $stmt = $pdo->prepare("UPDATE appointments SET appointment_date = ?, status = 'Scheduled' WHERE appointment_id = ? AND doctor_id = ?");
                $stmt->execute([$appointment_date, $appointment_id, $user_id]);
                $formSuccess = "Appointment rescheduled successfully!";
            } catch (Exception $e) {
                $formError = "Error rescheduling appointment: " . $e->getMessage();
            }
        }
    }
}

// Fetch all active residents for dropdown selection
$residents_list = $pdo->query("SELECT resident_id, full_name, room_number FROM residents WHERE status = 'Active' ORDER BY full_name ASC")->fetchAll();

// Fetch appointments list
$search = trim($_GET['search'] ?? '');
$type = trim($_GET['type'] ?? '');
$status = trim($_GET['status'] ?? '');
$date = trim($_GET['date'] ?? '');

$sql = "SELECT a.*, r.full_name AS resident_name, r.date_of_birth, r.gender, r.room_number 
        FROM appointments a 
        JOIN residents r ON a.resident_id = r.resident_id 
        WHERE a.doctor_id = ?";
$params = [$user_id];

if ($search !== '') {
    $sql .= " AND r.full_name LIKE ?";
    $params[] = '%' . $search . '%';
}
if ($type !== '' && $type !== 'All Types') {
    $sql .= " AND a.reason = ?";
    $params[] = $type;
}
if ($status !== '' && $status !== 'All Statuses') {
    $sql .= " AND a.status = ?";
    $params[] = $status;
}
if ($date !== '') {
    $sql .= " AND DATE(a.appointment_date) = ?";
    $params[] = $date;
}

$sql .= " ORDER BY a.appointment_date DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$appointments = $stmt->fetchAll();

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
$base_path = '../../'; // Ensure correct path prefix
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
                        <?php if (empty($appointments)): ?>
                            <tr><td colspan="6" class="text-center text-muted py-4">No appointments found matching filters.</td></tr>
                        <?php else: ?>
                            <?php foreach ($appointments as $appt): ?>
                                <?php
                                    $initials = '';
                                    $parts = explode(' ', $appt['resident_name']);
                                    foreach ($parts as $p) {
                                        $initials .= strtoupper(substr($p, 0, 1));
                                    }
                                    $initials = substr($initials, 0, 2);
                                    
                                    // Calculate Age
                                    $dob = new DateTime($appt['date_of_birth']);
                                    $now = new DateTime();
                                    $age = $now->diff($dob)->y;
                                    
                                    $badge = 'green';
                                    if ($appt['status'] === 'Pending') $badge = 'amber';
                                    elseif ($appt['status'] === 'Completed') $badge = 'blue';
                                    elseif ($appt['status'] === 'Cancelled') $badge = 'red';
                                    
                                    $is_disabled = ($appt['status'] === 'Completed' || $appt['status'] === 'Cancelled') ? 'disabled' : '';
                                ?>
                                <tr>
                                    <td>
                                        <div class="res-cell">
                                            <div class="res-photo"><?php echo sn_e($initials); ?></div>
                                            <div><b><?php echo sn_e($appt['resident_name']); ?></b><em>Room <?php echo sn_e($appt['room_number'] ?? 'N/A'); ?></em></div>
                                        </div>
                                    </td>
                                    <td><?php echo $age; ?> / <?php echo sn_e($appt['gender']); ?></td>
                                    <td><?php echo date('Y-m-d h:i A', strtotime($appt['appointment_date'])); ?></td>
                                    <td><?php echo sn_e($appt['reason']); ?></td>
                                    <td><span class="badge <?php echo $badge; ?>"><?php echo sn_e($appt['status']); ?></span></td>
                                    <td>
                                        <button class="btn tiny btn-outline-primary me-1 btn-view-notes" data-notes="<?php echo sn_e($appt['notes'] ?? 'No clinical notes provided.'); ?>" data-resident="<?php echo sn_e($appt['resident_name']); ?>"><i class="bi bi-eye"></i> View</button>
                                        <button class="btn tiny btn-outline-warning me-1 btn-reschedule" data-id="<?php echo $appt['appointment_id']; ?>" <?php echo $is_disabled; ?>><i class="bi bi-calendar-event"></i> Reschedule</button>
                                        <form method="POST" action="appointments.php" class="d-inline cancel-appointment-form">
                                            <input type="hidden" name="action" value="cancel">
                                            <input type="hidden" name="appointment_id" value="<?php echo $appt['appointment_id']; ?>">
                                            <button type="submit" class="btn tiny btn-outline-danger" <?php echo $is_disabled; ?>><i class="bi bi-x-circle"></i> Cancel</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
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

        <?php if ($formSuccess): ?>
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: <?php echo json_encode($formSuccess); ?>,
                            confirmButtonColor: '#2b4c3f'
                        });
                    }
                });
            </script>
        <?php endif; ?>
        <?php if ($formError): ?>
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: <?php echo json_encode($formError); ?>,
                            confirmButtonColor: '#2b4c3f'
                        });
                    }
                });
            </script>
        <?php endif; ?>

    </div>
</main>

<!-- Modal for scheduling appointment -->
<div class="modal fade" id="appointmentModal" tabindex="-1" aria-labelledby="appointmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <form method="POST" action="appointments.php">
                <input type="hidden" name="action" value="add">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="appointmentModalLabel">Schedule New Appointment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Resident Patient <span class="text-danger">*</span></label>
                        <select name="resident_id" class="form-select select" required style="width:100%; border: 1px solid var(--color-border); border-radius: var(--radius-medium); padding: 8px;">
                            <option value="">Select Resident...</option>
                            <?php foreach ($residents_list as $res): ?>
                                <option value="<?php echo $res['resident_id']; ?>">
                                    <?php echo sn_e($res['full_name']); ?> (Room <?php echo sn_e($res['room_number'] ?? 'N/A'); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Appointment Date & Time <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="appointment_date" class="form-control select" required style="width:100%; border: 1px solid var(--color-border); border-radius: var(--radius-medium); padding: 8px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Appointment Type <span class="text-danger">*</span></label>
                        <select name="reason" class="form-select select" required style="width:100%; border: 1px solid var(--color-border); border-radius: var(--radius-medium); padding: 8px;">
                            <option value="">Select Type...</option>
                            <option value="Routine Checkup">Routine Checkup</option>
                            <option value="Diabetic Review">Diabetic Review</option>
                            <option value="BP Monitoring">BP Monitoring</option>
                            <option value="Cardiac Review">Cardiac Review</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Clinical Notes (Optional)</label>
                        <textarea name="notes" class="form-control" rows="3" style="width:100%; border: 1px solid var(--color-border); border-radius: var(--radius-medium); padding: 8px;" placeholder="Add details..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-sm">Schedule Appointment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal for rescheduling appointment -->
<div class="modal fade" id="rescheduleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <form method="POST" action="appointments.php">
                <input type="hidden" name="action" value="reschedule">
                <input type="hidden" name="appointment_id" id="reschedule_appt_id">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Reschedule Appointment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">New Date & Time <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="appointment_date" class="form-control select" required style="width:100%; border: 1px solid var(--color-border); border-radius: var(--radius-medium); padding: 8px;">
                    </div>
                </div>
                <div class="modal-footer border-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-sm">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Connect addAppointmentBtn to show modal
    const addBtn = document.getElementById('addAppointmentBtn');
    if (addBtn) {
        addBtn.addEventListener('click', () => {
            const myModal = new bootstrap.Modal(document.getElementById('appointmentModal'));
            myModal.show();
        });
    }

    // Connect reschedule buttons
    document.querySelectorAll('.btn-reschedule').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-id');
            document.getElementById('reschedule_appt_id').value = id;
            const myModal = new bootstrap.Modal(document.getElementById('rescheduleModal'));
            myModal.show();
        });
    });

    // View notes button delegation
    document.querySelectorAll('.btn-view-notes').forEach(btn => {
        btn.addEventListener('click', () => {
            const notes = btn.getAttribute('data-notes');
            const resident = btn.getAttribute('data-resident');
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: `Notes: ${resident}`,
                    text: notes,
                    icon: 'info',
                    confirmButtonColor: '#2b4c3f'
                });
            } else {
                alert(notes);
            }
        });
    });

    // Intercept cancel form to confirm
    document.querySelectorAll('.cancel-appointment-form').forEach(form => {
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Do you want to cancel this appointment?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, cancel it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            } else {
                if (confirm('Cancel this appointment?')) {
                    form.submit();
                }
            }
        });
    });
});
</script>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
