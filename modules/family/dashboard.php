<?php
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/database.php';

require_login();
require_role('Family Member');

$pdo = get_db_connection();
$user_id = $_SESSION['user_id'] ?? 5;

// Fetch family user details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$family_user = $stmt->fetch();
$family_name = $family_user['full_name'] ?? 'Sunita Deshmukh';
$first_name = explode(' ', $family_name)[0];

// Fetch associated resident
$stmt = $pdo->prepare("SELECT * FROM residents WHERE family_member_id = ? AND status = 'Active' LIMIT 1");
$stmt->execute([$user_id]);
$resident = $stmt->fetch();
$resident_id = $resident ? (int)$resident['resident_id'] : 0;
$resident_name = $resident['full_name'] ?? 'No Resident Connected';
$health_status = $resident['health_status'] ?? 'Stable';

// Counters for family dashboard
$stmt = $pdo->prepare("SELECT COUNT(*) FROM visit_requests WHERE family_member_id = ?");
$stmt->execute([$user_id]);
$total_visits = (int)$stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM visit_requests WHERE family_member_id = ? AND status = 'Approved'");
$stmt->execute([$user_id]);
$approved_visits = (int)$stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM visit_requests WHERE family_member_id = ? AND status = 'Pending'");
$stmt->execute([$user_id]);
$pending_visits = (int)$stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM visit_requests WHERE family_member_id = ? AND status = 'Approved' AND visit_date < NOW()");
$stmt->execute([$user_id]);
$completed_visits = (int)$stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM messages WHERE sender_id = ?");
$stmt->execute([$user_id]);
$messages_sent = (int)$stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT SUM(amount) FROM donations WHERE donor_id = ? AND purpose NOT LIKE '[Fee:%'");
$stmt->execute([$user_id]);
$donations_made = (float)($stmt->fetchColumn() ?: 0.0);

// Next approved visit
$stmt = $pdo->prepare("SELECT visit_date FROM visit_requests WHERE family_member_id = ? AND status = 'Approved' AND visit_date >= NOW() ORDER BY visit_date ASC LIMIT 1");
$stmt->execute([$user_id]);
$next_visit = $stmt->fetchColumn();

// Unread notifications count
$stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
$stmt->execute([$user_id]);
$unread_notifs = (int)$stmt->fetchColumn();

// Recent health updates
$health_logs = [];
if ($resident_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM health_records WHERE resident_id = ? ORDER BY record_date DESC LIMIT 4");
    $stmt->execute([$resident_id]);
    $health_logs = $stmt->fetchAll();
}

// Today's activities
$today_activities = $pdo->query("SELECT * FROM activities WHERE activity_date = CURDATE() ORDER BY start_time ASC LIMIT 4")->fetchAll();

$base_path = '../../';
$page_title = 'Family Dashboard | SevaNest';
$extra_css = [
    'assets/css/dashboard.css'
];

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'family_member';
$currentPage   = 'dashboard.php';
$sn_asset_root = "../../assets";
include '../../includes/sidebar.php';
?>

<!-- ═══════════════════════════════════════════════════════════════════════
     MAIN CONTENT AREA
     ═══════════════════════════════════════════════════════════════════════ -->
<main id="sn-main-content" role="main" aria-label="Family member dashboard content">

    <div class="dn-page-wrapper">


        <!-- ── 1. PAGE HEADER STRIP ──────────────────────────────────── -->
        <section class="dn-header-strip dn-animate" aria-labelledby="dn-page-heading">
            <h1 class="dn-header-strip__title" id="dn-page-heading">
                Dashboard Overview
            </h1>
        </section>
        <!-- ── End Page Header Strip ──────────────────────────────────── -->


        <!-- ── 2. WELCOME BANNER ─────────────────────────────────────── -->
        <section class="dn-welcome-banner dn-animate dn-animate-delay-1"
                 aria-label="Welcome message">
            <div>
                <h2 class="dn-welcome__greeting">
                    <?php
                    /* Determine greeting based on server hour */
                    $hour = (int) date('H');
                    if ($hour < 12)      $greeting = 'Good Morning';
                    elseif ($hour < 17)  $greeting = 'Good Afternoon';
                    else                 $greeting = 'Good Evening';
                    echo "👋 {$greeting}, " . sn_e($first_name);
                    ?>
                </h2>
                <p class="dn-welcome__sub">
                    Here's how your loved one is doing today.
                </p>
            </div>
            
        </section>
        <!-- ── End Welcome Banner ─────────────────────────────────────── -->


        <!-- ── 3. SUMMARY CARDS ──────────────────────────────────────── -->
        <section aria-labelledby="dn-summary-heading">
            <h2 class="visually-hidden" id="dn-summary-heading">At a Glance</h2>

            <div class="dn-summary-cards">

                <!-- Card 1 · Health Status -->
                <a href="health-updates.php"
                   class="dn-summary-card dn-animate dn-animate-delay-2"
                   aria-label="Health status: Stable. View health updates."
                   id="dn-card-health">
                    <div class="dn-card-icon-wrap" aria-hidden="true">
                        <i class="bi bi-heart-pulse-fill"></i>
                    </div>
                    <div class="dn-card-body">
                        <p class="dn-card-label">Health Status</p>
                        <p class="dn-card-value"><?php echo sn_e($health_status); ?></p>
                        <span class="dn-status-pill stable">
                            <i class="bi bi-circle-fill"></i>
                            All Clear
                        </span>
                    </div>
                </a>

                <!-- Card 2 · Upcoming Visit -->
                <a href="visitors.php"
                   class="dn-summary-card dn-animate dn-animate-delay-3"
                   aria-label="Upcoming visit on 22 July at 4:00 PM. View visit schedule."
                   id="dn-card-visit">
                    <div class="dn-card-icon-wrap" aria-hidden="true">
                        <i class="bi bi-calendar-check-fill"></i>
                    </div>
                    <div class="dn-card-body">
                        <p class="dn-card-label">Upcoming Visit</p>
                        <?php if ($next_visit): 
                            $visit_ts = strtotime($next_visit);
                        ?>
                            <p class="dn-card-value"><?php echo date('d F', $visit_ts); ?></p>
                            <p class="dn-card-sub-value">
                                <i class="bi bi-clock me-1" aria-hidden="true"></i>
                                <?php echo date('h:i A', $visit_ts); ?>
                            </p>
                        <?php else: ?>
                            <p class="dn-card-value">No Visit</p>
                            <p class="dn-card-sub-value">
                                <i class="bi bi-clock me-1" aria-hidden="true"></i>
                                -- : --
                            </p>
                        <?php endif; ?>
                    </div>
                </a>

                <!-- Card 3 · Notifications -->
                <a href="notifications.php"
                   class="dn-summary-card dn-animate dn-animate-delay-4"
                   aria-label="3 new notifications. View notifications."
                   id="dn-card-notifications">
                    <div class="dn-card-icon-wrap" aria-hidden="true">
                        <i class="bi bi-bell-fill"></i>
                    </div>
                    <div class="dn-card-body">
                        <p class="dn-card-label">Notifications</p>
                        <p class="dn-card-value"><?php echo (int)$unread_notifs; ?> New</p>
                        <span class="dn-notif-badge">
                            <i class="bi bi-arrow-right-circle-fill" aria-hidden="true"></i>
                            View All
                        </span>
                    </div>
                </a>

            </div>
        </section>
        <!-- ── End Summary Cards ──────────────────────────────────────── -->


        <!-- ── 4. TWO-COLUMN SECTION ──────────────────────────────────── -->
        <section class="dn-two-col" aria-label="Health updates and today's schedule">

            <!-- LEFT · Recent Health Updates -->
            <article class="dn-content-card dn-animate dn-animate-delay-5"
                     aria-labelledby="dn-health-heading"
                     id="dn-health-updates">
                <h2 class="dn-content-card__title" id="dn-health-heading">
                    <i class="bi bi-clipboard2-heart-fill" aria-hidden="true"></i>
                    Recent Health Updates
                </h2>

                <ul class="dn-health-list" role="list">
                    <?php if (!empty($health_logs)): 
                        $hl = $health_logs[0];
                    ?>
                        <li class="dn-health-item">
                            <i class="bi bi-check-circle-fill" aria-label="Completed"></i>
                            <span class="dn-health-item__text">Blood Pressure: <?php echo sn_e($hl['systolic_bp'] . '/' . $hl['diastolic_bp']); ?> mmHg</span>
                            <span class="dn-health-item__tag"><?php echo ($hl['systolic_bp'] < 130 && $hl['diastolic_bp'] < 85) ? 'Normal' : 'High'; ?></span>
                        </li>
                        <li class="dn-health-item">
                            <i class="bi bi-check-circle-fill" aria-label="Completed"></i>
                            <span class="dn-health-item__text">Pulse / Heart Rate: <?php echo sn_e($hl['pulse']); ?> bpm</span>
                            <span class="dn-health-item__tag">Stable</span>
                        </li>
                        <?php if ($hl['temperature']): ?>
                        <li class="dn-health-item">
                            <i class="bi bi-check-circle-fill" aria-label="Completed"></i>
                            <span class="dn-health-item__text">Temperature: <?php echo sn_e($hl['temperature']); ?> °F</span>
                            <span class="dn-health-item__tag">Normal</span>
                        </li>
                        <?php endif; ?>
                        <?php if ($hl['blood_sugar']): ?>
                        <li class="dn-health-item">
                            <i class="bi bi-check-circle-fill" aria-label="Completed"></i>
                            <span class="dn-health-item__text">Blood Sugar: <?php echo sn_e($hl['blood_sugar']); ?> mg/dL</span>
                            <span class="dn-health-item__tag">Stable</span>
                        </li>
                        <?php endif; ?>
                    <?php else: ?>
                        <li class="dn-health-item">
                            <i class="bi bi-check-circle-fill" aria-label="Completed"></i>
                            <span class="dn-health-item__text">Blood Pressure Normal</span>
                            <span class="dn-health-item__tag">Normal</span>
                        </li>

                        <li class="dn-health-item">
                            <i class="bi bi-check-circle-fill" aria-label="Completed"></i>
                            <span class="dn-health-item__text">Sugar Level Stable</span>
                            <span class="dn-health-item__tag">Stable</span>
                        </li>

                        <li class="dn-health-item">
                            <i class="bi bi-check-circle-fill" aria-label="Completed"></i>
                            <span class="dn-health-item__text">Doctor Visit Completed</span>
                            <span class="dn-health-item__tag">Done</span>
                        </li>

                        <li class="dn-health-item">
                            <i class="bi bi-check-circle-fill" aria-label="Completed"></i>
                            <span class="dn-health-item__text">Medications Taken</span>
                            <span class="dn-health-item__tag">On Time</span>
                        </li>
                    <?php endif; ?>
                </ul>
            </article>
            <!-- ── End Health Updates ─────────────────────────────────── -->


            <!-- RIGHT · Today's Schedule (Timeline) -->
            <article class="dn-content-card dn-animate dn-animate-delay-6"
                     aria-labelledby="dn-schedule-heading"
                     id="dn-schedule">
                <h2 class="dn-content-card__title" id="dn-schedule-heading">
                    <i class="bi bi-clock-history" aria-hidden="true"></i>
                    Today's Schedule
                </h2>

                <ol class="dn-timeline" role="list" aria-label="Daily schedule timeline">
                    <?php if (!empty($today_activities)): ?>
                        <?php foreach ($today_activities as $act): 
                            $act_time = date('h:i A', strtotime($act['start_time']));
                            $is_past = strtotime($act['start_time']) < time();
                        ?>
                        <li class="dn-timeline-item <?php echo $is_past ? 'completed' : 'upcoming'; ?>">
                            <span class="dn-timeline-dot <?php echo $is_past ? 'completed' : 'upcoming'; ?>"
                                  aria-label="<?php echo $is_past ? 'Completed' : 'Upcoming'; ?> event" role="img"></span>
                            <p class="dn-timeline-time"><?php echo sn_e($act_time); ?></p>
                            <p class="dn-timeline-event"><?php echo sn_e($act['title']); ?></p>
                            <span class="dn-tl-tag <?php echo $is_past ? 'done' : 'next'; ?>">
                                <i class="bi <?php echo $is_past ? 'bi-check2' : 'bi-clock'; ?> me-1" aria-hidden="true"></i><?php echo $is_past ? 'Completed' : 'Upcoming'; ?>
                            </span>
                        </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- 8:00 AM – Breakfast (completed) -->
                        <li class="dn-timeline-item completed">
                            <span class="dn-timeline-dot completed"
                                  aria-label="Completed event" role="img"></span>
                            <p class="dn-timeline-time">8:00 AM</p>
                            <p class="dn-timeline-event">Breakfast</p>
                            <span class="dn-tl-tag done">
                                <i class="bi bi-check2 me-1" aria-hidden="true"></i>Completed
                            </span>
                        </li>

                        <!-- 10:00 AM – Exercise (completed) -->
                        <li class="dn-timeline-item completed">
                            <span class="dn-timeline-dot completed"
                                  aria-label="Completed event" role="img"></span>
                            <p class="dn-timeline-time">10:00 AM</p>
                            <p class="dn-timeline-event">Exercise Session</p>
                            <span class="dn-tl-tag done">
                                <i class="bi bi-check2 me-1" aria-hidden="true"></i>Completed
                            </span>
                        </li>

                        <!-- 2:00 PM – Doctor Visit (upcoming) -->
                        <li class="dn-timeline-item upcoming">
                            <span class="dn-timeline-dot upcoming"
                                  aria-label="Upcoming event" role="img"></span>
                            <p class="dn-timeline-time">2:00 PM</p>
                            <p class="dn-timeline-event">Doctor Visit</p>
                            <span class="dn-tl-tag next">
                                <i class="bi bi-clock me-1" aria-hidden="true"></i>Upcoming
                            </span>
                        </li>

                        <!-- 8:00 PM – Evening Medication (upcoming) -->
                        <li class="dn-timeline-item upcoming">
                            <span class="dn-timeline-dot upcoming"
                                  aria-label="Upcoming event" role="img"></span>
                            <p class="dn-timeline-time">8:00 PM</p>
                            <p class="dn-timeline-event">Evening Medication</p>
                            <span class="dn-tl-tag next">
                                <i class="bi bi-clock me-1" aria-hidden="true"></i>Upcoming
                            </span>
                        </li>
                    <?php endif; ?>
                </ol>
            </article>
            <!-- ── End Schedule Timeline ───────────────────────────────── -->

        </section>
        <!-- ── End Two-Column Section ─────────────────────────────────── -->


    </div><!-- /.dn-page-wrapper -->
</main>
<!-- ── End Main Content ───────────────────────────────────────────────── -->

<!-- Dashboard JS (deferred for performance) -->
<script src="../../assets/js/dashboard.js" defer></script>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
