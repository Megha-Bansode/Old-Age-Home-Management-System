<?php
/**
 * SevaNest – Family Member Visit Schedule
 * File     : visitors.php
 * Version  : 1.0
 * Author   : SevaNest Dev Team
 *
 * Description:
 *   Premium, responsive Visit Schedule page for SevaNest family members.
 *   Contains Month Calendar, Upcoming Visit Detail Card, Live status Tracker,
 *   Visiting Guidelines, Request Form, and Past Visit History Table.
 *   Designed to easily bind database data at the PHP / JavaScript level.
 */

/* ── Role & Page ─────────────────────────────────────────────────────────── */
$userRole    = 'family_member';
$currentPage = 'visitors.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Visit Schedule | SevaNest</title>
    <meta name="description" content="Schedule, request, and monitor your visits to SevaNest Old Age Home.">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Google Fonts – Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap"
          rel="stylesheet">

    <!-- Sidebar CSS (required for logo/nav shell layout) -->
    <link rel="stylesheet" href="assets/css/sidebar.css">

    <!-- Dashboard Layout base CSS -->
    <link rel="stylesheet" href="assets/css/dashboard.css">

    <!-- Visit Schedule custom stylesheet -->
    <link rel="stylesheet" href="assets/css/visitors.css">

</head>
<body>

<?php
/* ── Sidebar Component ───────────────────────────────────────────────────── */
$sn_asset_root = "assets";
include 'includes/sidebar.php';
?>

<!-- ═══════════════════════════════════════════════════════════════════════
     TOP HEADER BAR
     ═══════════════════════════════════════════════════════════════════════ -->
<header class="sn-topbar" id="sn-topbar" role="banner" aria-label="Dashboard top bar">

    <!-- Hamburger toggle -->
    <button id="sn-toggle-btn"
            type="button"
            aria-label="Toggle sidebar navigation"
            aria-expanded="true"
            aria-controls="sn-sidebar"
            title="Toggle Sidebar">
        <i class="bi bi-list" aria-hidden="true"></i>
    </button>

    <!-- Profile button -->
    <div class="sn-profile">
        <a href="resident-profile.php" title="Profile">
            <i class="bi bi-person-circle"></i>
        </a>
    </div>

</header>
<!-- ── End Top Header Bar ─────────────────────────────────────────────── -->


<!-- ═══════════════════════════════════════════════════════════════════════
     MAIN CONTENT AREA
     ═══════════════════════════════════════════════════════════════════════ -->
<main id="sn-main-content" role="main" aria-label="Family member visit schedule content">

    <div class="vs-page-wrapper">

        <!-- ── Page Header Strip ────────────────────────────────────────── -->
        <section class="vs-header-strip vs-animate" aria-labelledby="vs-page-heading">
            <div>
                <h1 class="vs-header-strip__title" id="vs-page-heading">
                    Visit Schedule
                </h1>
            </div>
        </section>
        <!-- ── End Page Header Strip ──────────────────────────────────── -->


        <!-- ── Section 1: Monthly Calendar (Full Width) ────────────────── -->
        <section class="vs-section vs-animate vs-animate-delay-1" aria-label="Monthly Visit Calendar">
            <div class="vs-card">
                <div class="vs-calendar-header">
                    <h2 class="vs-calendar-month-year" id="vs-month-year">July 2026</h2>
                    <div class="vs-calendar-nav">
                        <button type="button" class="vs-calendar-btn" id="vs-prev-month" aria-label="Previous Month">
                            <i class="bi bi-chevron-left" aria-hidden="true"></i>
                        </button>
                        <button type="button" class="vs-calendar-btn" id="vs-next-month" aria-label="Next Month">
                            <i class="bi bi-chevron-right" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
                
                <div class="vs-calendar-grid mb-1">
                    <div class="vs-calendar-day-header">Sun</div>
                    <div class="vs-calendar-day-header">Mon</div>
                    <div class="vs-calendar-day-header">Tue</div>
                    <div class="vs-calendar-day-header">Wed</div>
                    <div class="vs-calendar-day-header">Thu</div>
                    <div class="vs-calendar-day-header">Fri</div>
                    <div class="vs-calendar-day-header">Sat</div>
                </div>
                <div class="vs-calendar-grid" id="vs-calendar-days">
                    <!-- Dynamic Month Cells Injected by JavaScript -->
                </div>
                
                <!-- Legend -->
                <div class="vs-calendar-legend">
                    <div class="vs-calendar-legend-item">
                        <span class="vs-calendar-dot vs-calendar-dot--scheduled" aria-hidden="true"></span>
                        <span>Green Dot = Scheduled Visit</span>
                    </div>
                    <div class="vs-calendar-legend-item">
                        <span class="vs-calendar-dot vs-calendar-dot--requested" aria-hidden="true"></span>
                        <span>Gold Dot = Requested Visit</span>
                    </div>
                    <div class="vs-calendar-legend-item">
                        <span class="vs-calendar-dot vs-calendar-dot--completed" aria-hidden="true"></span>
                        <span>Grey Dot = No Visit</span>
                    </div>
                </div>
            </div>
        </section>
        <!-- ── End Section 1 ───────────────────────────────────────────── -->


        <!-- ── Section 2: Two Column Detail & Tracker ─────────────────── -->
        <section class="vs-two-col vs-animate vs-animate-delay-2" aria-label="Selected Visit Details and Tracker">
            
            <!-- Left Card: Upcoming Visit -->
            <article class="vs-card">
                <h2 class="vs-card__title">
                    <i class="bi bi-calendar-check-fill" aria-hidden="true"></i> Upcoming Visit
                </h2>
                
                <div class="vs-info-list" role="list">
                    <div class="vs-info-item" role="listitem">
                        <div class="vs-info-icon" aria-hidden="true"><i class="bi bi-calendar-event"></i></div>
                        <div class="vs-info-content">
                            <p class="vs-info-label">Date</p>
                            <p class="vs-info-value" id="vs-up-date">24 July 2026</p>
                        </div>
                    </div>
                    
                    <div class="vs-info-item" role="listitem">
                        <div class="vs-info-icon" aria-hidden="true"><i class="bi bi-clock"></i></div>
                        <div class="vs-info-content">
                            <p class="vs-info-label">Time</p>
                            <p class="vs-info-value" id="vs-up-time">04:00 PM</p>
                        </div>
                    </div>
                    
                    <div class="vs-info-item" role="listitem">
                        <div class="vs-info-icon" aria-hidden="true"><i class="bi bi-person"></i></div>
                        <div class="vs-info-content">
                            <p class="vs-info-label">Visitor Name</p>
                            <p class="vs-info-value" id="vs-up-visitor">Kirti Bansode</p>
                        </div>
                    </div>
                    
                    <div class="vs-info-item" role="listitem">
                        <div class="vs-info-icon" aria-hidden="true"><i class="bi bi-chat-right-text"></i></div>
                        <div class="vs-info-content">
                            <p class="vs-info-label">Purpose</p>
                            <p class="vs-info-value" id="vs-up-purpose">Doctor Check-in</p>
                        </div>
                    </div>
                    
                    <div class="vs-info-item" role="listitem">
                        <div class="vs-info-icon" aria-hidden="true"><i class="bi bi-shield-check"></i></div>
                        <div class="vs-info-content">
                            <p class="vs-info-label">Visit Status</p>
                            <p class="vs-info-value" id="vs-up-status">Scheduled</p>
                        </div>
                    </div>
                </div>
                
                <div class="vs-btn-group" id="vs-upcoming-actions">
                    <button type="button" class="vs-btn vs-btn--reschedule" disabled aria-label="Reschedule current visit">
                        <i class="bi bi-arrow-repeat" aria-hidden="true"></i> Reschedule
                    </button>
                    <button type="button" class="vs-btn vs-btn--cancel" disabled aria-label="Cancel current visit">
                        <i class="bi bi-x-circle" aria-hidden="true"></i> Cancel Visit
                    </button>
                </div>
            </article>

            <!-- Right Card: Guidelines -->
            <article class="vs-card">
                <h2 class="vs-card__title">
                    <i class="bi bi-shield-fill-exclamation" aria-hidden="true"></i> Visiting Guidelines
                </h2>
                
                <div class="vs-guidelines-box">
                    <ul class="vs-guide-list" role="list">
                        <li class="vs-guide-item" role="listitem">
                            <i class="bi bi-clock-fill" aria-hidden="true"></i>
                            <div class="vs-guide-text">
                                <strong>Visiting Hours:</strong> 10:00 AM – 12:00 PM & 4:00 PM – 7:00 PM daily. Please respect these periods for resident rest.
                            </div>
                        </li>
                        <li class="vs-guide-item" role="listitem">
                            <i class="bi bi-people-fill" aria-hidden="true"></i>
                            <div class="vs-guide-text">
                                <strong>Maximum Visitors:</strong> A maximum of 3 visitors per resident are allowed at one time to avoid room crowding.
                            </div>
                        </li>
                        <li class="vs-guide-item" role="listitem">
                            <i class="bi bi-card-image" aria-hidden="true"></i>
                            <div class="vs-guide-text">
                                <strong>Carry Valid ID:</strong> All visitors must carry a valid photo ID card (Aadhar, driving license, etc.) for check-in.
                            </div>
                        </li>
                        <li class="vs-guide-item" role="listitem">
                            <i class="bi bi-droplet-fill" aria-hidden="true"></i>
                            <div class="vs-guide-text">
                                <strong>Follow Hygiene Protocols:</strong> Please sanitize hands at the entry gates. Stay home if feeling unwell.
                            </div>
                        </li>
                        <li class="vs-guide-item" role="listitem">
                            <i class="bi bi-journal-check" aria-hidden="true"></i>
                            <div class="vs-guide-text">
                                <strong>Report at Reception:</strong> Kindly register and log entry details at the front lobby desk upon arrival.
                            </div>
                        </li>
                    </ul>
                </div>
            </article>
            
        </section>
        <!-- ── End Section 2 ───────────────────────────────────────────── -->


        <!-- ── Section 3: Two Column Guidelines & Form ──────────────────── -->
        <section class="vs-two-col vs-animate vs-animate-delay-3" aria-label="Guidelines and Request Form">
            
           <!-- left Card: Visit Status Tracker -->
            <article class="vs-card">
                <h2 class="vs-card__title">
                    <i class="bi bi-activity" aria-hidden="true"></i> Visit Status
                </h2>
                
                <div class="vs-tracker-outer">
                    <div class="vs-tracker-wrapper" id="vs-status-tracker" role="region" aria-label="Status progress tracker">
                        <!-- Line fills -->
                        <div class="vs-tracker-line-bg"></div>
                        <div class="vs-tracker-line-fill" id="vs-tracker-line-fill"></div>
                        
                        <!-- Step 1: Requested -->
                        <div class="vs-tracker-step" data-step="1">
                            <div class="vs-tracker-circle">
                                <i class="bi bi-1-circle" aria-hidden="true"></i>
                            </div>
                            <span class="vs-tracker-label">Requested</span>
                        </div>
                        
                        <!-- Step 2: Approved -->
                        <div class="vs-tracker-step" data-step="2">
                            <div class="vs-tracker-circle">
                                <i class="bi bi-2-circle" aria-hidden="true"></i>
                            </div>
                            <span class="vs-tracker-label">Approved</span>
                        </div>
                        
                        <!-- Step 3: Scheduled -->
                        <div class="vs-tracker-step" data-step="3">
                            <div class="vs-tracker-circle">
                                <i class="bi bi-3-circle" aria-hidden="true"></i>
                            </div>
                            <span class="vs-tracker-label">Scheduled</span>
                        </div>
                        
                        <!-- Step 4: Completed -->
                        <div class="vs-tracker-step" data-step="4">
                            <div class="vs-tracker-circle">
                                <i class="bi bi-4-circle" aria-hidden="true"></i>
                            </div>
                            <span class="vs-tracker-label">Completed</span>
                        </div>
                    </div>
                    
                    <div class="vs-tracker-status-display" id="vs-status-display-card">
                        <p class="vs-tracker-status-title">Current Status</p>
                        <span class="vs-tracker-status-val" id="vs-status-val">Scheduled</span>
                    </div>
                </div>
            </article>

            <!-- Right Card: Request Form -->
            <article class="vs-card">
                <h2 class="vs-card__title">
                    <i class="bi bi-calendar-plus-fill" aria-hidden="true"></i> Request New Visit
                </h2>
                
                <form class="vs-form" id="vs-request-form">
                    <div class="vs-form-row">
                        <div class="vs-form-group">
                            <label for="vs-input-date" class="vs-label">Visit Date <span class="text-danger">*</span></label>
                            <input type="date" class="vs-input" id="vs-input-date" required>
                        </div>
                        <div class="vs-form-group">
                            <label for="vs-input-time" class="vs-label">Visit Time <span class="text-danger">*</span></label>
                            <input type="time" class="vs-input" id="vs-input-time" required>
                        </div>
                    </div>
                    
                    <div class="vs-form-group">
                        <label for="vs-input-purpose" class="vs-label">Purpose <span class="text-danger">*</span></label>
                        <select class="vs-select" id="vs-input-purpose" required>
                            <option value="" disabled selected>Select a purpose...</option>
                            <option value="Regular Visit">Regular Visit</option>
                            <option value="Medical Check-in">Medical Check-in</option>
                            <option value="Festival Celebration">Festival Celebration</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    
                    <div class="vs-form-group">
                        <label for="vs-input-notes" class="vs-label">Additional Notes</label>
                        <textarea class="vs-textarea" id="vs-input-notes" placeholder="Notes, special arrangements, or requests..."></textarea>
                    </div>
                    
                    <button type="submit" class="vs-btn vs-btn--submit">
                        <i class="bi bi-send-fill" aria-hidden="true"></i> Request Visit
                    </button>
                </form>
            </article>
            
        </section>
        <!-- ── End Section 3 ───────────────────────────────────────────── -->


        <!-- ── Section 4: History Table (Full Width) ────────────────────── -->
        <section class="vs-section vs-animate vs-animate-delay-4" aria-labelledby="vs-history-heading">
            <div class="vs-card">
                <h2 class="vs-card__title" id="vs-history-heading">
                    <i class="bi bi-clock-history" aria-hidden="true"></i> Visit History
                </h2>
                
                <div class="vs-table-toolbar">
                    <div class="vs-table-search">
                        <i class="bi bi-search" aria-hidden="true"></i>
                        <input type="text" class="vs-table-search-input" id="vs-table-search" placeholder="Search by purpose, visitor, remarks...">
                    </div>
                    <select class="vs-table-filter" id="vs-table-filter">
                        <option value="">All Statuses</option>
                        <option value="completed">Completed</option>
                        <option value="scheduled">Scheduled</option>
                        <option value="requested">Requested</option>
                        <option value="approved">Approved</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                
                <div class="vs-table-container">
                    <table class="vs-table" aria-label="Visit History Log Table">
                        <thead>
                            <tr>
                                <th>Visit Date</th>
                                <th>Time</th>
                                <th>Purpose</th>
                                <th>Status</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody id="vs-history-tbody">
                            <!-- Injected by JavaScript -->
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination elements -->
                <div class="vs-pagination" aria-label="Table navigation pagination">
                    <div class="vs-pagination-info" id="vs-table-pagination-info">
                        Showing 1 to 5 of 5 entries (Visual Pagination Only)
                    </div>
                    <div class="vs-pagination-btns">
                        <button type="button" class="vs-page-btn" id="vs-page-prev" disabled aria-label="Previous Page">
                            <i class="bi bi-chevron-left" aria-hidden="true"></i>
                        </button>
                        <button type="button" class="vs-page-btn active" aria-current="page">1</button>
                        <button type="button" class="vs-page-btn" id="vs-page-next" disabled aria-label="Next Page">
                            <i class="bi bi-chevron-right" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
            </div>
        </section>
        <!-- ── End Section 4 ───────────────────────────────────────────── -->

    </div><!-- /.vs-page-wrapper -->
</main>
<!-- ── End Main Content Area ──────────────────────────────────────────── -->


<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

<!-- Visit Schedule custom JS -->
<script src="assets/js/visitors.js" defer></script>

</body>
</html>
