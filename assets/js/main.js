/**
 * SevaNest Old Age Home Management System
 * Login & Authentication Page Interactivity
 */

(function(){
  const $ = (id) => document.getElementById(id);
  
  function hideAllBanners(){
    ['login-banner-error','login-banner-success']
      .forEach(id => {
        const el = $(id);
        if (el) el.classList.remove('show');
      });
  }

  // ---------- password visibility toggle ----------
  const pwInput = $('login-password');
  const pwToggle = $('toggle-password');
  const eyeIcon = $('eye-icon');
  
  if (pwInput && pwToggle && eyeIcon) {
    const EYE_OPEN = eyeIcon.innerHTML;
    const EYE_CLOSED = '<path d="M3 3L21 21" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/><path d="M9.9 5.1C10.6 5 11.3 4.9C12 4.9C18.5 4.9 22 11.4 22 11.4C21.6 12.2 20.9 13.2 20 14.2M6.3 6.6C3.6 8.3 2 11.4 2 11.4C2 11.4 5.5 17.9 12 17.9C13.4 17.9 14.6 17.6 15.7 17.1" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/><path d="M9.9 12.9C9.6 12.5 9.5 12 9.5 11.5C9.5 10.1 10.6 9 12 9C12.6 9 13.1 9.2 13.5 9.5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>';

    pwToggle.addEventListener('click', () => {
      const showing = pwInput.type === 'text';
      pwInput.type = showing ? 'password' : 'text';
      eyeIcon.innerHTML = showing ? EYE_OPEN : EYE_CLOSED;
      pwToggle.setAttribute('aria-pressed', String(!showing));
      pwToggle.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
    });
  }

  // ---------- helpers ----------
  function setFieldError(shellId, errorId, hasError, message){
    const shell = $(shellId);
    const err = $(errorId);
    if (!shell || !err) return;
    
    shell.classList.toggle('has-error', hasError);
    if(message) err.textContent = message;
    err.style.display = hasError ? 'block' : 'none';
  }

  function setLoading(btn, isLoading, idleLabel){
    if (!btn) return;
    btn.classList.toggle('loading', isLoading);
    btn.disabled = isLoading;
    const labelNode = btn.querySelector('.btn-label');
    if (labelNode) {
      labelNode.textContent = isLoading ? 'Please wait…' : idleLabel;
    }
  }

  // ---------- login form ----------
  const loginForm = $('login-form');
  const roleGrid = $('role-grid');
  const roleError = $('role-error');
  const loginSubmit = $('login-submit');

  if (loginForm) {
    loginForm.addEventListener('submit', function(e){
      e.preventDefault();
      hideAllBanners();

      let valid = true;

      const selectedRole = loginForm.querySelector('input[name="role"]:checked');
      if(!selectedRole){
        if (roleError) roleError.style.display = 'block';
        valid = false;
      } else {
        if (roleError) roleError.style.display = 'none';
      }

      const emailVal = $('login-email') ? $('login-email').value.trim() : '';
      if(!emailVal){
        setFieldError('login-email-shell','login-email-error', true, 'Enter your email or username to continue.');
        valid = false;
      } else if(emailVal.includes('@') && typeof validateEmail === 'function' && !validateEmail(emailVal)){
        setFieldError('login-email-shell','login-email-error', true, 'That email address doesn\'t look right.');
        valid = false;
      } else {
        setFieldError('login-email-shell','login-email-error', false);
      }

      const pwVal = $('login-password') ? $('login-password').value : '';
      if(!pwVal){
        setFieldError('login-password-shell','login-password-error', true, 'Enter your password to continue.');
        valid = false;
      } else if(pwVal.length < 8){
        setFieldError('login-password-shell','login-password-error', true, 'Password must be at least 8 characters.');
        valid = false;
      } else {
        setFieldError('login-password-shell','login-password-error', false);
      }

      if(!valid){
        const errText = $('login-error-text');
        const errBanner = $('login-banner-error');
        if (errText) errText.textContent = 'Please fix the highlighted fields and try again.';
        if (errBanner) errBanner.classList.add('show');
        return;
      }

      // ── START TEMPORARY UI INTEGRATION ──────────────────────────────────────────
      // NOTE: This role-based routing is temporary for local UI testing and runs
      // purely on the frontend. It must be replaced with real PHP+MySQL database
      // authentication later.
      setLoading(loginSubmit, true, 'Sign in');
      
      const role = selectedRole.value.toLowerCase().trim();
      
      // Routing dictionary mapping role input values to actual existing dashboard files
      const routes = {
        'super admin': '../super_admin/index.php',
        'super_admin': '../super_admin/index.php',
        'old age home admin': '../admin/index.php',
        'admin': '../admin/index.php',
        'caretaker': '../caretaker/dashboard.php',
        'doctor': '../doctor/dashboard.php',
        'donor': '../donor/dashboard.php',
        'family member': '../family/dashboard.php',
        'family_member': '../family/dashboard.php',
        'family': '../family/dashboard.php'
      };
      
      const successText = $('login-success-text');
      const successBanner = $('login-banner-success');
      
      if (successText) {
        successText.textContent = `Signed in as ${selectedRole.value}. Redirecting…`;
      }
      if (successBanner) successBanner.classList.add('show');
      
      setTimeout(() => {
        setLoading(loginSubmit, false, 'Sign in');
        
        const target = routes[role];
        
        if (target) {
          window.location.href = target;
        } else {
          window.location.href = 'under_development.php?role=' + encodeURIComponent(selectedRole.value);
        }
      }, 1000);
      // ── END TEMPORARY UI INTEGRATION ────────────────────────────────────────────
    });
  }

  // clear role error as soon as one is picked
  if (roleGrid && roleError) {
    roleGrid.addEventListener('change', () => { roleError.style.display = 'none'; });
  }

  // ---------- background slideshow ----------
  const slides = document.querySelectorAll('.brand-slideshow .slide');
  if (slides.length > 0) {
    let currentSlide = 0;
    function nextSlide() {
      slides[currentSlide].classList.remove('active');
      currentSlide = (currentSlide + 1) % slides.length;
      slides[currentSlide].classList.add('active');
    }
    setInterval(nextSlide, 5000);
  }

})();
