<?php
/**
 * SevaNest — Login & Unified Auth Interface
 * Authentication Module
 *
 * Reuses the design system CSS and JavaScript libraries.
 */

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';

// Redirect logged-in users (unless in DEV_MODE, to allow testing the login interface)
if (is_logged_in() && (!defined('DEV_MODE') || !DEV_MODE)) {
    header("Location: " . get_dashboard_url($_SESSION['role']));
    exit();
}

// Config variables for header template
$base_path = '../../';
$body_class = 'auth-page';
$page_title = 'Sign In — SevaNest';
$extra_css = ['assets/css/style.css'];
$is_public_page = true;

require_once('../../includes/header.php');

// Generate CSRF token for forms
$csrf_token = generate_csrf_token();
?>

<!-- HTML Meta CSRF Token for JavaScript -->
<meta name="csrf-token" content="<?php echo htmlspecialchars($csrf_token); ?>">

<!-- Include SweetAlert2 CDN for modern alerts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
  .swal2-popup {
    font-family: 'Inter', sans-serif !important;
    border-radius: 16px !important;
    padding: 24px !important;
  }
  .swal2-title {
    font-family: 'Outfit', sans-serif !important;
    color: #2F3A3A !important;
  }
  .swal2-styled.swal2-confirm {
    background-color: var(--color-primary-dark) !important;
    border-radius: 10px !important;
    padding: 10px 24px !important;
    font-weight: 600 !important;
  }
</style>

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

        <form id="login-form" action="login_api.php" method="POST" novalidate>
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
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
                <input type="radio" name="role" id="role-admin" value="Admin" data-dash="Admin Dashboard">
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
                  Family Member
                </label>
              </div>
            </div>
            <span class="role-error" id="role-error">Please select a role to continue.</span>
          </div>

          <div class="field">
            <label for="login-email">Email or phone number</label>
            <div class="input-shell" id="login-email-shell">
              <svg class="leading" viewBox="0 0 24 24" fill="none"><path d="M4 6H20V18H4V6Z" stroke="currentColor" stroke-width="1.4"/><path d="M4 6L12 13L20 6" stroke="currentColor" stroke-width="1.4"/></svg>
              <input type="text" id="login-email" name="email" placeholder="you@sevanest.org" autocomplete="username">
            </div>
            <span class="field-error" id="login-email-error">Enter your email or phone number to continue.</span>
          </div>

          <div class="field">
            <label for="login-password">Password</label>
            <div class="input-shell" id="login-password-shell">
              <svg class="leading" viewBox="0 0 24 24" fill="none"><rect x="5" y="10" width="14" height="10" rx="2" stroke="currentColor" stroke-width="1.4"/><path d="M8 10V7C8 4.8 9.8 3 12 3C14.2 3 16 4.8 16 7V10" stroke="currentColor" stroke-width="1.4"/></svg>
              <input type="password" id="login-password" name="password" placeholder="Enter your password" autocomplete="current-password">
              <button type="button" class="toggle-visibility" id="toggle-password" aria-label="Show password" aria-pressed="false">
                <svg id="eye-icon" viewBox="0 0 24 24" fill="none"><path d="M2 12C2 12 5.5 5.5 12 5.5C18.5 5.5 22 12 22 12C22 12 18.5 18.5 12 18.5C5.5 18.5 2 12 2 12Z" stroke="currentColor" stroke-width="1.4"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.4"/></svg>
              </button>
            </div>
            
            <!-- Real-time Password Strength Indicator -->
            <div class="strength-meter" id="login-strength-container" style="display:none; margin-top:8px;">
              <div class="strength-track">
                <div class="strength-fill" id="login-strength-fill"></div>
              </div>
              <div class="strength-label">
                <span>Password Strength</span>
                <strong id="login-strength-text">Weak</strong>
              </div>
            </div>
            
            <span class="field-error" id="login-password-error">Enter your password to continue.</span>
          </div>

          <div class="row-between">
            <label class="remember"><input type="checkbox" id="remember-me" name="remember_me"> Remember me</label>
            <button type="button" class="link-btn" id="go-forgot">Forgot password?</button>
          </div>

          <button type="submit" class="btn-primary-auth" id="login-submit">
            <span class="spin"></span>
            <span class="btn-label">Sign in</span>
          </button>
        </form>

        <p class="foot-note">SevaNest Old Age Home Management System — Secure Authentication.</p>
      </section>

      <!-- ---------- FORGOT PASSWORD VIEW ---------- -->
      <section class="view" id="view-forgot">
        <div class="card-logo mobile-only">
          <?php if (file_exists($path_prefix . 'assets/images/logo/logo.jpeg')): ?>
            <img src="<?php echo htmlspecialchars($path_prefix); ?>assets/images/logo/logo.jpeg" alt="SevaNest Logo">
          <?php endif; ?>
          <span>SevaNest</span>
        </div>
        <div class="card-head">
          <span class="eyebrow">
            <span class="back-arrow" id="go-login" style="display:inline-flex; cursor: pointer; align-items: center; vertical-align: middle;">
              <svg viewBox="0 0 24 24" fill="none" style="width: 18px; height: 18px; margin-right: 4px;"><path d="M15 5L8 12L15 19" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
            Back to sign in
          </span>
          <h2>Reset your password</h2>
          <p>Enter your registered account email and we'll send a 6-digit verification OTP code.</p>
        </div>

        <form id="forgot-form" novalidate>
          <div class="field">
            <label for="forgot-email">Email address</label>
            <div class="input-shell" id="forgot-email-shell">
              <svg class="leading" viewBox="0 0 24 24" fill="none"><path d="M4 6H20V18H4V6Z" stroke="currentColor" stroke-width="1.4"/><path d="M4 6L12 13L20 6" stroke="currentColor" stroke-width="1.4"/></svg>
              <input type="email" id="forgot-email" name="email" placeholder="you@sevanest.com" autocomplete="email">
            </div>
            <span class="field-error" id="forgot-email-error">Enter the email on your account.</span>
          </div>

          <button type="submit" class="btn-primary-auth" id="forgot-submit">
            <span class="spin"></span>
            <span class="btn-label">Send OTP Code</span>
          </button>
        </form>

        <p class="foot-note">Remembered your password? <button type="button" class="link-btn" id="go-login-2">Return to sign in</button></p>
      </section>

      <!-- ---------- VERIFY OTP VIEW ---------- -->
      <section class="view" id="view-verify-otp">
        <div class="card-head">
          <span class="eyebrow">
            <span class="back-arrow" id="go-forgot-back" style="display:inline-flex; cursor: pointer; align-items: center; vertical-align: middle;">
              <svg viewBox="0 0 24 24" fill="none" style="width: 18px; height: 18px; margin-right: 4px;"><path d="M15 5L8 12L15 19" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
            Change email
          </span>
          <h2>Enter Verification OTP</h2>
          <p>We've sent a 6-digit OTP code to <strong id="otp-registered-email">your email</strong>. Enter it below to proceed.</p>
        </div>

        <form id="otp-form" novalidate>
          <div class="field">
            <label>6-Digit Security Code</label>
            <div class="otp-group">
              <input type="text" class="otp-box" maxlength="1" pattern="[0-9]" autofocus>
              <input type="text" class="otp-box" maxlength="1" pattern="[0-9]">
              <input type="text" class="otp-box" maxlength="1" pattern="[0-9]">
              <input type="text" class="otp-box" maxlength="1" pattern="[0-9]">
              <input type="text" class="otp-box" maxlength="1" pattern="[0-9]">
              <input type="text" class="otp-box" maxlength="1" pattern="[0-9]">
            </div>
          </div>

          <button type="submit" class="btn-primary-auth" id="otp-submit">
            <span class="spin"></span>
            <span class="btn-label">Verify OTP Code</span>
          </button>
        </form>
      </section>

      <!-- ---------- RESET PASSWORD VIEW ---------- -->
      <section class="view" id="view-reset-password">
        <div class="card-head">
          <span class="eyebrow">Secure Account</span>
          <h2>Create New Password</h2>
          <p>Set a strong password meeting all security requirements below.</p>
        </div>

        <form id="reset-form" novalidate>
          <div class="field">
            <label for="new-password">New Password</label>
            <div class="input-shell" id="new-password-shell">
              <svg class="leading" viewBox="0 0 24 24" fill="none"><rect x="5" y="10" width="14" height="10" rx="2" stroke="currentColor" stroke-width="1.4"/><path d="M8 10V7C8 4.8 9.8 3 12 3C14.2 3 16 4.8 16 7V10" stroke="currentColor" stroke-width="1.4"/></svg>
              <input type="password" id="new-password" placeholder="Create strong password" autocomplete="new-password">
              <button type="button" class="toggle-visibility" id="toggle-new-password" aria-label="Show password">
                <svg id="new-eye-icon" viewBox="0 0 24 24" fill="none"><path d="M2 12C2 12 5.5 5.5 12 5.5C18.5 5.5 22 12 22 12C22 12 18.5 18.5 12 18.5C5.5 18.5 2 12 2 12Z" stroke="currentColor" stroke-width="1.4"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.4"/></svg>
              </button>
            </div>
          </div>

          <!-- Password Strength Meter -->
          <div class="strength-meter">
            <div class="strength-track">
              <div class="strength-fill" id="strength-fill"></div>
            </div>
            <div class="strength-label">
              <span>Password Quality</span>
              <strong id="strength-label-text">Weak</strong>
            </div>
          </div>

          <!-- Password Rules Checklist -->
          <ul class="pw-rules">
            <li class="pw-rule" id="req-length">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>
              At least 8 characters
            </li>
            <li class="pw-rule" id="req-upper">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>
              Uppercase letter (A-Z)
            </li>
            <li class="pw-rule" id="req-lower">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>
              Lowercase letter (a-z)
            </li>
            <li class="pw-rule" id="req-number">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>
              Number (0-9)
            </li>
            <li class="pw-rule" id="req-special">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>
              Special character (!@#$)
            </li>
          </ul>

          <div class="field">
            <label for="confirm-password">Confirm New Password</label>
            <div class="input-shell" id="confirm-password-shell">
              <svg class="leading" viewBox="0 0 24 24" fill="none"><rect x="5" y="10" width="14" height="10" rx="2" stroke="currentColor" stroke-width="1.4"/><path d="M8 10V7C8 4.8 9.8 3 12 3C14.2 3 16 4.8 16 7V10" stroke="currentColor" stroke-width="1.4"/></svg>
              <input type="password" id="confirm-password" placeholder="Repeat new password" autocomplete="new-password">
            </div>
            <span class="field-error" id="confirm-password-error">Passwords must match.</span>
          </div>

          <button type="submit" class="btn-primary-auth" id="reset-submit">
            <span class="spin"></span>
            <span class="btn-label">Update Password</span>
          </button>
        </form>

        <p class="foot-note">Done resetting? <button type="button" class="link-btn" id="go-login-3">Return to sign in</button></p>
      </section>

    </div>
  </main>
</div>

<!-- Load Main Application Script -->
<script src="<?php echo htmlspecialchars($path_prefix); ?>assets/js/main.js"></script>

<script>
window.addEventListener('DOMContentLoaded', () => {
  const urlParams = new URLSearchParams(window.location.search);
  const msg = urlParams.get('msg');
  const error = urlParams.get('error');

  if (msg === 'logged_out') {
    Swal.fire({
      icon: 'success',
      title: 'Logged Out',
      text: 'You have logged out successfully.',
      confirmButtonText: 'OK'
    });
  } else if (error === 'unauthorized') {
    Swal.fire({
      icon: 'warning',
      title: 'Access Required',
      text: 'Please log in to access your dashboard.',
      confirmButtonText: 'OK'
    });
  } else if (error === 'wrong_password') {
    Swal.fire({
      icon: 'error',
      title: 'Incorrect Password',
      text: 'The password you entered is incorrect.',
      confirmButtonText: 'OK'
    });
  } else if (error === 'email_not_found') {
    Swal.fire({
      icon: 'warning',
      title: 'Account Not Found',
      text: 'No account exists with this email or phone number.',
      confirmButtonText: 'OK'
    });
  } else if (error === 'role_mismatch') {
    Swal.fire({
      icon: 'warning',
      title: 'Role Mismatch',
      text: 'Please select the correct role before logging in.',
      confirmButtonText: 'OK'
    });
  } else if (error === 'account_disabled') {
    Swal.fire({
      icon: 'error',
      title: 'Access Denied',
      text: 'Your account has been disabled. Please contact the administrator.',
      confirmButtonText: 'OK'
    });
  } else if (error === 'csrf_error') {
    Swal.fire({
      icon: 'error',
      title: 'Security Session Timeout',
      text: 'CSRF validation failed. Please refresh the page and try again.',
      confirmButtonText: 'OK'
    });
  } else if (error === 'invalid_email') {
     Swal.fire({
      icon: 'warning',
      title: 'Invalid Email',
      text: 'Please enter a valid email address.',
      confirmButtonText: 'OK'
    });
  }
});
</script>

<?php
require_once('../../includes/footer.php');
?>
