<?php
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../config/database.php';

// Require login (using dummy login logic)
require_login();

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
                    echo "👋 {$greeting}, Kirti";
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
                        <p class="dn-card-value">Stable</p>
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
                        <p class="dn-card-value">22 July</p>
                        <p class="dn-card-sub-value">
                            <i class="bi bi-clock me-1" aria-hidden="true"></i>
                            4:00 PM
                        </p>
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
                        <p class="dn-card-value">3 New</p>
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
