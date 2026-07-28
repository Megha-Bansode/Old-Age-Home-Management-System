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

require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../config/database.php';
// Require login
require_login();
require_role('Family Member');

/* ── Role & Page ──────────────────────────────────────────────────────────── */
$userRole    = 'family_member';
$currentPage = 'resident-profile.php';

// Database binding hook
$pdo = get_db_connection();
$user_id = $_SESSION['user_id'] ?? 5;

// Default fallbacks
$resident_name        = 'Devendra Joshi';
$resident_age         = '74';
$resident_gender      = 'Male';
$resident_blood_group = 'O+';
$resident_room        = 'Room 104 (A Wing)';
$resident_admission   = '12 Oct 2024';
$resident_photo       = '';

$guardian_name        = 'Sunita Deshmukh';
$guardian_age         = '42';
$guardian_relation    = 'Guardian';
$guardian_phone       = '+91 98765 43215';
$guardian_address     = 'Flat 302, Sunrise Apartments, Baner, Pune - 411045';

$occupation           = 'Retired High School Principal';
$preferred_language   = 'Marathi, English';
$hobbies   = ['Reading Biographies', 'Morning Walks in the Garden', 'Listening to Indian Classical Music'];
$allergies = ['Peanuts', 'Penicillin'];

if ($pdo) {
    try {
        // Fetch resident details associated with the family member user ID
        $stmt = $pdo->prepare("SELECT * FROM residents WHERE family_member_id = ? AND status = 'Active' LIMIT 1");
        $stmt->execute([$user_id]);
        $row = $stmt->fetch();
        
        if ($row) {
            $resident_name        = $row['full_name'];
            $resident_age         = $row['age'];
            $resident_gender      = $row['gender'];
            $resident_blood_group = $row['blood_group'] ?? 'O+';
            $resident_room        = $row['room_number'] ?? 'Room 104 (A Wing)';
            $resident_admission   = date('d M Y', strtotime($row['admission_date']));
            
            // Guardian details from users table
            $stmt_u = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt_u->execute([$user_id]);
            $family_user = $stmt_u->fetch();
            if ($family_user) {
                $guardian_name    = $family_user['full_name'];
                $guardian_phone   = $family_user['phone'] ?? '+91 98765 43215';
                $guardian_address = $family_user['address'] ?? 'Pune, Maharashtra';
            }
        }
    } catch (PDOException $e) {
        log_error("DB resident profile fetch failed: " . $e->getMessage());
    }
}
?>
<?php
$base_path = '../../';
$page_title = 'Resident Profile | SevaNest';
$extra_css = [
    'assets/css/resident-profile.css'
];

require_once __DIR__ . '/../../includes/header.php';

/* ── Sidebar Component ───────────────────────────────────────────────────── */
$userRole      = 'family_member';
$currentPage   = 'resident-profile.php';
$sn_asset_root = "../../assets";
include '../../includes/sidebar.php';
?>

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

        <!-- ── End Bottom Bar ─────────────────────────────────────────── -->


    </div><!-- /.rp-page-wrapper -->
</main>
<!-- ── End Main Content ───────────────────────────────────────────────── -->

<!-- Resident Profile JS -->
<script src="../../assets/js/resident-profile.js" defer></script>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
