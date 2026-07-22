<?php
/**
 * SevaNest – Family Member Resident Profile
 * File     : resident-profile.php
 * Version  : 1.0
 * Author   : SevaNest Dev Team
 *
 * Description:
 *   Resident profile page for the Family Member role.
 *   Displays Resident Information, Guardian Information, and Personal Background.
 *   All values use PHP placeholders ready for MySQL integration.
 *
 * Sidebar integration:
 *   $userRole = 'family_member' ensures correct role-aware navigation.
 */

/* ── Role & Page ──────────────────────────────────────────────────────────── */
$userRole    = 'family_member';
$currentPage = 'residents.php';

/* ── PHP Placeholders (replace with DB fetch) ────────────────────────────── */
$resident_name        = '{{resident_name}}';
$resident_age         = '{{age}}';
$resident_gender      = '{{gender}}';
$resident_blood_group = '{{blood_group}}';
$resident_room        = '{{room_number}}';
$resident_admission   = '{{admission_date}}';
$resident_photo       = '';   // set to image path when available

$guardian_name        = '{{guardian_name}}';
$guardian_age         = '{{guardian_age}}';
$guardian_relation    = '{{relationship}}';
$guardian_phone       = '{{phone_number}}';
$guardian_address     = '{{address}}';

$occupation           = '{{previous_occupation}}';
$preferred_language   = '{{preferred_language}}';
// Hobbies & allergies as arrays; replace with DB-driven arrays
$hobbies   = ['{{hobby_1}}', '{{hobby_2}}', '{{hobby_3}}'];
$allergies = ['{{allergy_1}}', '{{allergy_2}}'];
?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Resident Profile | SevaNest</title>
    <meta name="description"
          content="View and manage the resident profile including personal details, guardian information, and background on SevaNest.">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Google Fonts – Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap"
          rel="stylesheet">

    <!-- Sidebar CSS -->
    <link rel="stylesheet" href="assets/css/sidebar.css">

    <!-- Dashboard base (shared body / layout tokens) -->
    <link rel="stylesheet" href="assets/css/dashboard.css">

    <!-- Resident Profile specific styles -->
    <link rel="stylesheet" href="assets/css/resident-profile.css">

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
<header class="sn-topbar" id="sn-topbar" role="banner" aria-label="Top bar">

    <!-- Hamburger -->
    <button id="sn-toggle-btn"
            type="button"
            aria-label="Toggle sidebar navigation"
            aria-expanded="true"
            aria-controls="sn-sidebar"
            title="Toggle Sidebar">
        <i class="bi bi-list" aria-hidden="true"></i>
    </button>

    <!-- Profile icon -->
    <div class="sn-profile">
        <a href="profile.php" title="Profile" class="top-profile-btn">
            <i class="bi bi-person-circle"></i>
        </a>
    </div>

</header>
<!-- ── End Top Header Bar ─────────────────────────────────────────────── -->


<!-- ═══════════════════════════════════════════════════════════════════════
     MAIN CONTENT AREA
     ═══════════════════════════════════════════════════════════════════════ -->
<main id="sn-main-content" role="main" aria-label="Resident profile content">

    <div class="rp-page-wrapper">


        <!-- ── 1. PAGE HEADER STRIP ──────────────────────────────────── -->
        <section class="rp-header-strip rp-animate" aria-labelledby="rp-page-heading">
            <div class="rp-header-strip__left">
                <h1 class="rp-header-strip__title" id="rp-page-heading">
                    Resident Profile
                </h1>
            </div>
        </section>
        <!-- ── End Page Header Strip ─────────────────────────────────── -->


        <!-- ── 2. RESIDENT INFORMATION CARD ─────────────────────────── -->
        <article class="rp-card rp-animate rp-animate-d1"
                 aria-labelledby="rp-resident-heading"
                 id="rp-resident-info">

            <div class="rp-card__header">
                <h2 class="rp-card__title" id="rp-resident-heading">
                    <i class="bi bi-person-badge" aria-hidden="true"></i>
                    Resident Information
                </h2>
                <a href="edit-resident.php?section=info"
                   class="rp-btn-edit"
                   id="rp-edit-resident-btn"
                   aria-label="Edit resident information">
                    <i class="bi bi-pencil-square" aria-hidden="true"></i>
                    Edit
                </a>
            </div>

            <div class="rp-profile-layout">

                <!-- Photo column -->
                <div class="rp-photo-col">
                    <div class="rp-photo-wrap"
                         id="rp-photo-wrap"
                         role="button"
                         tabindex="0"
                         aria-label="Change resident photo"
                         title="Click to change photo">

                        <?php if (!empty($resident_photo)): ?>
                            <img src="<?php echo htmlspecialchars($resident_photo); ?>"
                                 alt="Photo of <?php echo htmlspecialchars($resident_name); ?>"
                                 id="rp-resident-img">
                        <?php else: ?>
                            <div class="rp-photo-placeholder" id="rp-photo-placeholder">
                                <i class="bi bi-person-circle" aria-hidden="true"></i>
                                <span>No Photo<br>Upload Image</span>
                            </div>
                        <?php endif; ?>

                        <div class="rp-photo-overlay" aria-hidden="true">
                            <i class="bi bi-camera-fill"></i>
                        </div>
                    </div>
                    <p class="rp-photo-hint">Click to upload<br>resident photo</p>
                    <!-- Hidden file input for photo upload -->
                    <input type="file"
                           id="rp-photo-input"
                           name="resident_photo"
                           accept="image/*"
                           style="display:none"
                           aria-label="Upload resident photo">
                </div>
                <!-- ── End photo column ── -->

                <!-- Fields column -->
                <div class="rp-fields-col">
                    <div class="rp-field-grid">

                        <!-- Resident Name -->
                        <div class="rp-field-group">
                            <span class="rp-field-label">
                                <i class="bi bi-person" aria-hidden="true"></i>
                                Resident Name
                            </span>
                            <div class="rp-field-value"
                                 id="rp-val-name"
                                 aria-label="Resident name: <?php echo htmlspecialchars($resident_name); ?>">
                                <?php echo htmlspecialchars($resident_name); ?>
                            </div>
                        </div>

                        <!-- Age -->
                        <div class="rp-field-group">
                            <span class="rp-field-label">
                                <i class="bi bi-calendar2-heart" aria-hidden="true"></i>
                                Age
                            </span>
                            <div class="rp-field-value"
                                 id="rp-val-age"
                                 aria-label="Age: <?php echo htmlspecialchars($resident_age); ?>">
                                <?php echo htmlspecialchars($resident_age); ?>
                            </div>
                        </div>

                        <!-- Gender -->
                        <div class="rp-field-group">
                            <span class="rp-field-label">
                                <i class="bi bi-gender-ambiguous" aria-hidden="true"></i>
                                Gender
                            </span>
                            <div class="rp-field-value"
                                 id="rp-val-gender"
                                 aria-label="Gender: <?php echo htmlspecialchars($resident_gender); ?>">
                                <?php echo htmlspecialchars($resident_gender); ?>
                            </div>
                        </div>

                        <!-- Blood Group -->
                        <div class="rp-field-group">
                            <span class="rp-field-label">
                                <i class="bi bi-droplet-half" aria-hidden="true"></i>
                                Blood Group
                            </span>
                            <div class="rp-field-value"
                                 id="rp-val-blood"
                                 aria-label="Blood group: <?php echo htmlspecialchars($resident_blood_group); ?>">
                                <?php echo htmlspecialchars($resident_blood_group); ?>
                            </div>
                        </div>

                        <!-- Room Number -->
                        <div class="rp-field-group">
                            <span class="rp-field-label">
                                <i class="bi bi-door-open" aria-hidden="true"></i>
                                Room Number
                            </span>
                            <div class="rp-field-value"
                                 id="rp-val-room"
                                 aria-label="Room number: <?php echo htmlspecialchars($resident_room); ?>">
                                <?php echo htmlspecialchars($resident_room); ?>
                            </div>
                        </div>

                        <!-- Admission Date -->
                        <div class="rp-field-group">
                            <span class="rp-field-label">
                                <i class="bi bi-calendar-check" aria-hidden="true"></i>
                                Admission Date
                            </span>
                            <div class="rp-field-value"
                                 id="rp-val-admission"
                                 aria-label="Admission date: <?php echo htmlspecialchars($resident_admission); ?>">
                                <?php echo htmlspecialchars($resident_admission); ?>
                            </div>
                        </div>

                    </div><!-- /.rp-field-grid -->
                </div><!-- /.rp-fields-col -->

            </div><!-- /.rp-profile-layout -->

        </article>
        <!-- ── End Resident Information Card ──────────────────────────── -->


        <!-- ── 3. GUARDIAN INFORMATION CARD ─────────────────────────── -->
        <article class="rp-card rp-animate rp-animate-d2"
                 aria-labelledby="rp-guardian-heading"
                 id="rp-guardian-info">

            <div class="rp-card__header">
                <h2 class="rp-card__title" id="rp-guardian-heading">
                    <i class="bi bi-people" aria-hidden="true"></i>
                    Guardian Information
                </h2>
                <a href="edit-resident.php?section=guardian"
                   class="rp-btn-edit"
                   id="rp-edit-guardian-btn"
                   aria-label="Edit guardian information">
                    <i class="bi bi-pencil-square" aria-hidden="true"></i>
                    Edit
                </a>
            </div>

            <div class="rp-field-grid">

                <!-- Guardian Name -->
                <div class="rp-field-group">
                    <span class="rp-field-label">
                        <i class="bi bi-person-check" aria-hidden="true"></i>
                        Guardian Name
                    </span>
                    <div class="rp-field-value"
                         id="rp-val-guardian-name"
                         aria-label="Guardian name: <?php echo htmlspecialchars($guardian_name); ?>">
                        <?php echo htmlspecialchars($guardian_name); ?>
                    </div>
                </div>

                <!-- Guardian Age -->
                <div class="rp-field-group">
                    <span class="rp-field-label">
                        <i class="bi bi-calendar2" aria-hidden="true"></i>
                        Guardian Age
                    </span>
                    <div class="rp-field-value"
                         id="rp-val-guardian-age"
                         aria-label="Guardian age: <?php echo htmlspecialchars($guardian_age); ?>">
                        <?php echo htmlspecialchars($guardian_age); ?>
                    </div>
                </div>

                <!-- Relationship -->
                <div class="rp-field-group">
                    <span class="rp-field-label">
                        <i class="bi bi-diagram-2" aria-hidden="true"></i>
                        Relationship
                    </span>
                    <div class="rp-field-value"
                         id="rp-val-relation"
                         aria-label="Relationship: <?php echo htmlspecialchars($guardian_relation); ?>">
                        <?php echo htmlspecialchars($guardian_relation); ?>
                    </div>
                </div>

                <!-- Phone Number -->
                <div class="rp-field-group">
                    <span class="rp-field-label">
                        <i class="bi bi-telephone" aria-hidden="true"></i>
                        Phone Number
                    </span>
                    <div class="rp-field-value"
                         id="rp-val-phone"
                         aria-label="Phone: <?php echo htmlspecialchars($guardian_phone); ?>">
                        <?php echo htmlspecialchars($guardian_phone); ?>
                    </div>
                </div>

                <!-- Address – full width -->
                <div class="rp-field-group rp-field-group--full">
                    <span class="rp-field-label">
                        <i class="bi bi-geo-alt" aria-hidden="true"></i>
                        Address
                    </span>
                    <div class="rp-field-value"
                         id="rp-val-address"
                         style="min-height:54px; align-items:flex-start; padding-top:12px;"
                         aria-label="Address: <?php echo htmlspecialchars($guardian_address); ?>">
                        <?php echo htmlspecialchars($guardian_address); ?>
                    </div>
                </div>

            </div><!-- /.rp-field-grid -->

        </article>
        <!-- ── End Guardian Information Card ──────────────────────────── -->


        <!-- ── 4. PERSONAL BACKGROUND CARD ──────────────────────────── -->
        <article class="rp-card rp-animate rp-animate-d3"
                 aria-labelledby="rp-background-heading"
                 id="rp-personal-background">

            <div class="rp-card__header">
                <h2 class="rp-card__title" id="rp-background-heading">
                    <i class="bi bi-journal-text" aria-hidden="true"></i>
                    Personal Background
                </h2>
                <a href="edit-resident.php?section=background"
                   class="rp-btn-edit"
                   id="rp-edit-background-btn"
                   aria-label="Edit personal background">
                    <i class="bi bi-pencil-square" aria-hidden="true"></i>
                    Edit
                </a>
            </div>

            <div class="rp-field-grid">

                <!-- Previous Occupation -->
                <div class="rp-field-group">
                    <span class="rp-field-label">
                        <i class="bi bi-briefcase" aria-hidden="true"></i>
                        Previous Occupation
                    </span>
                    <div class="rp-field-value"
                         id="rp-val-occupation"
                         aria-label="Occupation: <?php echo htmlspecialchars($occupation); ?>">
                        <?php echo htmlspecialchars($occupation); ?>
                    </div>
                </div>

                <!-- Preferred Language -->
                <div class="rp-field-group">
                    <span class="rp-field-label">
                        <i class="bi bi-translate" aria-hidden="true"></i>
                        Preferred Language
                    </span>
                    <div class="rp-field-value"
                         id="rp-val-language"
                         aria-label="Language: <?php echo htmlspecialchars($preferred_language); ?>">
                        <?php echo htmlspecialchars($preferred_language); ?>
                    </div>
                </div>

                <!-- Hobbies – full width with green tags -->
                <div class="rp-field-group rp-field-group--full">
                    <span class="rp-field-label">
                        <i class="bi bi-emoji-smile" aria-hidden="true"></i>
                        Hobbies
                    </span>
                    <div class="rp-tags-wrap"
                         id="rp-val-hobbies"
                         role="list"
                         aria-label="Hobbies">
                        <?php foreach ($hobbies as $hobby): ?>
                            <span class="rp-tag--hobby" role="listitem">
                                <i class="bi bi-star-fill" aria-hidden="true"></i>
                                <?php echo htmlspecialchars($hobby); ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Allergies – full width with orange warning pills -->
                <div class="rp-field-group rp-field-group--full">
                    <span class="rp-field-label">
                        <i class="bi bi-exclamation-triangle" aria-hidden="true"></i>
                        Allergies
                    </span>
                    <div class="rp-tags-wrap"
                         id="rp-val-allergies"
                         role="list"
                         aria-label="Allergies">
                        <?php foreach ($allergies as $allergy): ?>
                            <span class="rp-tag--allergy" role="listitem">
                                <i class="bi bi-exclamation-circle-fill" aria-hidden="true"></i>
                                <?php echo htmlspecialchars($allergy); ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div><!-- /.rp-field-grid -->

        </article>
        <!-- ── End Personal Background Card ───────────────────────────── -->


        <!-- ── BOTTOM – Back to Dashboard ────────────────────────────── -->
        <div class="rp-bottom-bar rp-animate rp-animate-d4">
            <a href="dashboard.php"
               class="rp-btn-back"
               id="rp-back-btn"
               aria-label="Back to Family Dashboard">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Back to Dashboard
            </a>
        </div>
        <!-- ── End Bottom Bar ─────────────────────────────────────────── -->


    </div><!-- /.rp-page-wrapper -->
</main>
<!-- ── End Main Content ───────────────────────────────────────────────── -->


<!-- Bootstrap 5 JS bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Sidebar JS -->
<script src="assets/js/sidebar.js" defer></script>

<!-- Resident Profile JS -->
<script src="assets/js/resident-profile.js" defer></script>

</body>
</html>
