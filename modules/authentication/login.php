<?php
/**
 * SevaNest — Login & Forgot Password Interface
 * Authentication Module
 *
 * Reuses the design system CSS and JavaScript libraries.
 */

require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../config/database.php';

// ── START TEMPORARY UI INTEGRATION ──────────────────────────────────────────
// NOTE: This role-based routing is temporary for local UI testing and runs
// without database validation. It must be replaced with real, database-backed
// authentication in the production phase.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $role     = clean_str($_POST['role'] ?? '');
    $email    = clean_str($_POST['email'] ?? '');
    $password = (string)($_POST['password'] ?? '');

    // Dummy authentication for local UI testing - no DB checks required
    if (!empty($role) && !empty($email) && strlen($password) >= 8) {
        $_SESSION['user_id']   = 999; // Dummy session ID
        $_SESSION['user_name'] = ($role === 'Family Member') ? 'Kirti' : 'Staff User';
        $_SESSION['user_role'] = $role;
        $_SESSION['role']      = ($role === 'Family Member') ? 'family_member' : 'admin';

        $roleRoutes = [
            'Super Admin'        => '../../modules/super_admin/index.php',
            'Old Age Home Admin' => '../../modules/admin/index.php',
            'Caretaker'          => '../../modules/caretaker/index.php',
            'Doctor'             => '../../modules/doctor/index.php',
            'Donor'              => '../../modules/donor/index.php',
            'Family Member'      => '../../modules/family/dashboard.php'
        ];

        $target = $roleRoutes[$role] ?? '../../index.php';
        
        // Resolve path relative to this script directory to check file state
        $resolved_path = __DIR__ . '/' . $target;
        
        // If file is missing or exists but is empty (0 bytes), route to development page
        if (!file_exists($resolved_path) || filesize($resolved_path) === 0) {
            $redirect = 'under_development.php?role=' . urlencode($role);
        } else {
            $redirect = $target;
        }

        echo json_encode(['success' => true, 'redirect' => $redirect]);
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid credentials or password must be at least 8 characters.']);
        exit;
    }
}
// ── END TEMPORARY UI INTEGRATION ────────────────────────────────────────────

// Config variables for header template
$base_path = '../../';
$body_class = 'auth-page';
$page_title = 'Sign In — SevaNest';
$extra_css = ['assets/css/style.css'];

require_once('../../includes/header.php');
?>

<div class="wrap">

  <!-- ===================== LEFT BRAND PANEL ===================== -->
  <aside class="brand">
    <div class="brand-slideshow">
      <div class="slide active" style="background-image: url('<?php echo htmlspecialchars($path_prefix); ?>assets/images/login/login1.jpg');"></div>
      <div class="slide" style="background-image: url('<?php echo htmlspecialchars($path_prefix); ?>assets/images/login/login2.jpg');"></div>
      <div class="slide" style="background-image: url('<?php echo htmlspecialchars($path_prefix); ?>assets/images/login/login3.jpg');"></div>
    </div>
    <div class="brand-overlay"></div>
    <svg class="arcs" viewBox="0 0 460 460" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
      <circle cx="230" cy="460" r="220" stroke="#D4A373" stroke-opacity="0.35" stroke-width="1.5"/>
      <circle cx="230" cy="460" r="165" stroke="#D4A373" stroke-opacity="0.45" stroke-width="1.5"/>
      <circle cx="230" cy="460" r="110" stroke="#F6F4EC" stroke-opacity="0.25" stroke-width="1.5"/>
      <circle cx="230" cy="460" r="55" fill="#D4A373" fill-opacity="0.18"/>
    </svg>

    <div class="brand-mark">
      <?php if (file_exists($path_prefix . 'assets/images/logo/logo.jpeg')): ?>
        <img src="<?php echo htmlspecialchars($path_prefix); ?>assets/images/logo/logo.jpeg" alt="SevaNest Logo" class="brand-logo">
      <?php else: ?>
        <span class="auth-logo-text" style="color: var(--color-primary); font-weight: 800; font-size: 1.45rem;">SevaNest</span>
      <?php endif; ?>
    </div>

    <div class="brand-copy">
      <h1>One quiet nest for every person who cares for them.</h1>
      <p>Sign in to reach the dashboard built for your role — from resident care and medical records, to visits, donations, and daily wellbeing.</p>

      <div class="roles-strip">
        <div class="role-pill"><svg viewBox="0 0 24 24" fill="none"><path d="M12 4L14 9L19 8L17 13L20 15L15 17L15 21H9V17L4 15L7 13L5 8L10 9L12 4Z" fill="#D4A373"/></svg>Super Admin</div>
        <div class="role-pill"><svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="4" fill="#F6F4EC"/><path d="M4 20C4 16 7.5 14 12 14C16.5 14 20 16 20 20" stroke="#F6F4EC" stroke-width="1.6"/></svg>Admin</div>
        <div class="role-pill"><svg viewBox="0 0 24 24" fill="none"><path d="M9 11L11 13L15 9" stroke="#F6F4EC" stroke-width="1.8" stroke-linecap="round"/><rect x="4" y="4" width="16" height="16" rx="3" stroke="#F6F4EC" stroke-width="1.4"/></svg>Caretaker</div>
        <div class="role-pill"><svg viewBox="0 0 24 24" fill="none"><path d="M8 3V8C8 10.2 9.8 12 12 12C14.2 12 16 10.2 16 8V3" stroke="#F6F4EC" stroke-width="1.5"/><circle cx="18" cy="14" r="2.4" stroke="#F6F4EC" stroke-width="1.4"/><path d="M12 12V16C12 18.2 13.8 20 16 20" stroke="#F6F4EC" stroke-width="1.4"/></svg>Doctor</div>
        <div class="role-pill"><svg viewBox="0 0 24 24" fill="none"><path d="M12 20C12 20 4 15 4 9.5C4 6.5 6.2 4.5 9 4.5C10.5 4.5 11.5 5.2 12 6C12.5 5.2 13.5 4.5 15 4.5C17.8 4.5 20 6.5 20 9.5C20 15 12 20 12 20Z" fill="#F6F4EC"/></svg>Donor</div>
        <div class="role-pill"><svg viewBox="0 0 24 24" fill="none"><circle cx="9" cy="8" r="3" stroke="#F6F4EC" stroke-width="1.4"/><circle cx="17" cy="9" r="2.4" stroke="#F6F4EC" stroke-width="1.4"/><path d="M4 19C4 15.8 6.2 14 9 14C11.8 14 14 15.8 14 19" stroke="#F6F4EC" stroke-width="1.4"/><path d="M15 14.5C17.4 14.7 19 16.2 19 19" stroke="#F6F4EC" stroke-width="1.4"/></svg>Family</div>
      </div>
    </div>
  </aside>

  <!-- ===================== RIGHT FORM PANEL ===================== -->
  <main class="panel">
    <div class="card">

      <!-- ---------- LOGIN VIEW ---------- -->
      <section class="view active" id="view-login">
        <div class="card-logo mobile-only">
          <?php if (file_exists($path_prefix . 'assets/images/logo/logo.jpeg')): ?>
            <img src="<?php echo htmlspecialchars($path_prefix); ?>assets/images/logo/logo.jpeg" alt="SevaNest Logo">
          <?php endif; ?>
          <span>SevaNest</span>
        </div>
        <div class="card-head">
          <span class="eyebrow">Welcome back</span>
          <h2>Sign in to your dashboard</h2>
          <p>Choose your role, then enter your credentials to continue.</p>
        </div>

        <div class="banner error" id="login-banner-error">
          <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="#8A3B29" stroke-width="1.6"/><path d="M12 8V13" stroke="#8A3B29" stroke-width="1.6" stroke-linecap="round"/><circle cx="12" cy="16" r="1" fill="#8A3B29"/></svg>
          <span id="login-error-text">Something went wrong. Please try again.</span>
        </div>
        <div class="banner success" id="login-banner-success">
          <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="#3E5C3E" stroke-width="1.6"/><path d="M8 12.5L10.5 15L16 9" stroke="#3E5C3E" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
          <span id="login-success-text">Signed in. Redirecting to your dashboard…</span>
        </div>

        <form id="login-form" novalidate>
          <div class="field">
            <label>I am signing in as</label>
            <div class="role-grid" id="role-grid" role="radiogroup" aria-label="Select your role">
              <div class="role-opt" data-role="superadmin">
                <input type="radio" name="role" id="role-superadmin" value="Super Admin" data-dash="Super Admin Dashboard">
                <label for="role-superadmin">
                  <span class="r-icon"><svg viewBox="0 0 24 24" fill="none"><path d="M12 4L14 9L19 8L17 13L20 15L15 17L15 21H9V17L4 15L7 13L5 8L10 9L12 4Z" fill="#fff"/></svg></span>
                  Super Admin
                </label>
              </div>
              <div class="role-opt" data-role="admin">
                <input type="radio" name="role" id="role-admin" value="Old Age Home Admin" data-dash="Admin Dashboard">
                <label for="role-admin">
                  <span class="r-icon"><svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="4" fill="#fff"/><path d="M4 20C4 16 7.5 14 12 14C16.5 14 20 16 20 20" stroke="#fff" stroke-width="1.6"/></svg></span>
                  Admin
                </label>
              </div>
              <div class="role-opt" data-role="caretaker">
                <input type="radio" name="role" id="role-caretaker" value="Caretaker" data-dash="Caretaker Dashboard">
                <label for="role-caretaker">
                  <span class="r-icon"><svg viewBox="0 0 24 24" fill="none"><path d="M9 11L11 13L15 9" stroke="#fff" stroke-width="1.8" stroke-linecap="round"/><rect x="4" y="4" width="16" height="16" rx="3" stroke="#fff" stroke-width="1.4"/></svg></span>
                  Caretaker
                </label>
              </div>
              <div class="role-opt" data-role="doctor">
                <input type="radio" name="role" id="role-doctor" value="Doctor" data-dash="Doctor Dashboard">
                <label for="role-doctor">
                  <span class="r-icon"><svg viewBox="0 0 24 24" fill="none"><path d="M8 3V8C8 10.2 9.8 12 12 12C14.2 12 16 10.2 16 8V3" stroke="#fff" stroke-width="1.5"/><circle cx="18" cy="14" r="2.4" stroke="#fff" stroke-width="1.4"/><path d="M12 12V16C12 18.2 13.8 20 16 20" stroke="#fff" stroke-width="1.4"/></svg></span>
                  Doctor
                </label>
              </div>
              <div class="role-opt" data-role="donor">
                <input type="radio" name="role" id="role-donor" value="Donor" data-dash="Donor Dashboard">
                <label for="role-donor">
                  <span class="r-icon"><svg viewBox="0 0 24 24" fill="none"><path d="M12 20C12 20 4 15 4 9.5C4 6.5 6.2 4.5 9 4.5C10.5 4.5 11.5 5.2 12 6C12.5 5.2 13.5 4.5 15 4.5C17.8 4.5 20 6.5 20 9.5C20 15 12 20 12 20Z" fill="#fff"/></svg></span>
                  Donor
                </label>
              </div>
              <div class="role-opt" data-role="family">
                <input type="radio" name="role" id="role-family" value="Family Member" data-dash="Family Member Dashboard">
                <label for="role-family">
                  <span class="r-icon"><svg viewBox="0 0 24 24" fill="none"><circle cx="9" cy="8" r="3" stroke="#fff" stroke-width="1.4"/><circle cx="17" cy="9" r="2.4" stroke="#fff" stroke-width="1.4"/><path d="M4 19C4 15.8 6.2 14 9 14C11.8 14 14 15.8 14 19" stroke="#fff" stroke-width="1.4"/><path d="M15 14.5C17.4 14.7 19 16.2 19 19" stroke="#fff" stroke-width="1.4"/></svg></span>
                  Family
                </label>
              </div>
            </div>
            <span class="role-error" id="role-error">Please select a role to continue.</span>
          </div>

          <div class="field">
            <label for="login-email">Email or username</label>
            <div class="input-shell" id="login-email-shell">
              <svg class="leading" viewBox="0 0 24 24" fill="none"><path d="M4 6H20V18H4V6Z" stroke="currentColor" stroke-width="1.4"/><path d="M4 6L12 13L20 6" stroke="currentColor" stroke-width="1.4"/></svg>
              <input type="text" id="login-email" placeholder="you@sevanest.org" autocomplete="username">
            </div>
            <span class="field-error" id="login-email-error">Enter your email or username to continue.</span>
          </div>

          <div class="field">
            <label for="login-password">Password</label>
            <div class="input-shell" id="login-password-shell">
              <svg class="leading" viewBox="0 0 24 24" fill="none"><rect x="5" y="10" width="14" height="10" rx="2" stroke="currentColor" stroke-width="1.4"/><path d="M8 10V7C8 4.8 9.8 3 12 3C14.2 3 16 4.8 16 7V10" stroke="currentColor" stroke-width="1.4"/></svg>
              <input type="password" id="login-password" placeholder="Enter your password" autocomplete="current-password">
              <button type="button" class="toggle-visibility" id="toggle-password" aria-label="Show password" aria-pressed="false">
                <svg id="eye-icon" viewBox="0 0 24 24" fill="none"><path d="M2 12C2 12 5.5 5.5 12 5.5C18.5 5.5 22 12 22 12C22 12 18.5 18.5 12 18.5C5.5 18.5 2 12 2 12Z" stroke="currentColor" stroke-width="1.4"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.4"/></svg>
              </button>
            </div>
            <span class="field-error" id="login-password-error">Enter your password to continue.</span>
          </div>

          <div class="row-between">
            <label class="remember"><input type="checkbox" id="remember-me"> Remember me</label>
            <a href="forgot_password.php" class="link-btn">Forgot password?</a>
          </div>

          <button type="submit" class="btn-primary-auth" id="login-submit">
            <span class="spin"></span>
            <span class="btn-label">Sign in</span>
          </button>
        </form>

        <p class="foot-note">Demo only — hooks up to your authentication API. Try any email + password 8+ characters, or leave a field blank to see validation.</p>
      </section>

    </div>
  </main>
</div>

<!-- Load Main Application Script -->
<script src="<?php echo htmlspecialchars($path_prefix); ?>assets/js/main.js"></script>

<?php
require_once('../../includes/footer.php');
?>
