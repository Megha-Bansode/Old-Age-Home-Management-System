// ============================================================
// SevaNest - Core Frontend JavaScript & SweetAlert2 Popups
// ============================================================

(function () {
    const $ = (id) => document.getElementById(id);
    const getCsrfToken = () => {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    };

    const loginView = $('view-login');
    const forgotView = $('view-forgot');
    const verifyOtpView = $('view-verify-otp');
    const resetPwView = $('view-reset-password');

    function showView(targetView) {
        [loginView, forgotView, verifyOtpView, resetPwView].forEach((v) => {
            if (v) v.classList.remove('active');
        });
        if (targetView) targetView.classList.add('active');
    }

    // View Navigation Listeners
    if ($('go-forgot')) $('go-forgot').addEventListener('click', () => showView(forgotView));
    if ($('go-login')) $('go-login').addEventListener('click', () => showView(loginView));
    if ($('go-login-2')) $('go-login-2').addEventListener('click', () => showView(loginView));
    if ($('go-login-3')) $('go-login-3').addEventListener('click', () => showView(loginView));
    if ($('go-forgot-back')) $('go-forgot-back').addEventListener('click', () => showView(forgotView));

    // ---------- Single Eye / Eye-Off Toggle ----------
    const EYE_OPEN = '<path d="M2 12C2 12 5.5 5.5 12 5.5C18.5 5.5 22 12 22 12C22 12 18.5 18.5 12 18.5C5.5 18.5 2 12 2 12Z" stroke="currentColor" stroke-width="1.4"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.4"/>';
    const EYE_CLOSED = '<path d="M3 3L21 21" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/><path d="M9.9 5.1C10.6 5 11.3 4.9 12 4.9C18.5 4.9 22 11.4 22 11.4C21.6 12.2 20.9 13.2 20 14.2M6.3 6.6C3.6 8.3 2 11.4 2 11.4C2 11.4 5.5 17.9 12 17.9C13.4 17.9 14.6 17.6 15.7 17.1" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/><path d="M9.9 12.9C9.6 12.5 9.5 12 9.5 11.5C9.5 10.1 10.6 9 12 9C12.6 9 13.1 9.2 13.5 9.5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>';

    function setupPasswordToggle(inputId, toggleId, iconId) {
        const input = $(inputId);
        const toggle = $(toggleId);
        const icon = $(iconId);

        if (toggle && input && icon) {
            toggle.addEventListener('click', (e) => {
                e.preventDefault();
                const isShowing = input.type === 'text';
                input.type = isShowing ? 'password' : 'text';
                icon.innerHTML = isShowing ? EYE_OPEN : EYE_CLOSED;
                toggle.setAttribute('aria-pressed', String(!isShowing));
                toggle.setAttribute('aria-label', isShowing ? 'Show password' : 'Hide password');
            });
        }
    }

    setupPasswordToggle('login-password', 'toggle-password', 'eye-icon');
    setupPasswordToggle('new-password', 'toggle-new-password', 'new-eye-icon');

    // ---------- Helper Functions ----------
    const EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    function setFieldError(shellId, errorId, hasError, message) {
        const shell = $(shellId);
        const err = $(errorId);
        if (shell) shell.classList.toggle('has-error', hasError);
        if (err) {
            if (message) err.textContent = message;
            err.style.display = hasError ? 'block' : 'none';
        }
    }

    function setLoading(btn, isLoading, idleLabel) {
        if (!btn) return;
        btn.classList.toggle('loading', isLoading);
        btn.disabled = isLoading;
        const labelNode = btn.querySelector('.btn-label');
        if (labelNode) labelNode.textContent = isLoading ? 'Please wait…' : idleLabel;
    }

    // ---------- Real-time Password Strength ----------
    function checkStrength(val) {
        const isLength = val.length >= 8;
        const isUpper  = /[A-Z]/.test(val);
        const isLower  = /[a-z]/.test(val);
        const isNumber = /[0-9]/.test(val);
        const isSpecial = /[^a-zA-Z0-9]/.test(val);

        let score = 0;
        if (isLength) score++;
        if (isUpper) score++;
        if (isLower) score++;
        if (isNumber) score++;
        if (isSpecial) score++;

        const isStrong = isLength && isUpper && isLower && isNumber && isSpecial;
        return { score, isStrong };
    }

    // Login Password Strength Listener
    const loginPw = $('login-password');
    const loginStrContainer = $('login-strength-container');
    const loginStrFill = $('login-strength-fill');
    const loginStrText = $('login-strength-text');

    if (loginPw && loginStrContainer && loginStrFill && loginStrText) {
        loginPw.addEventListener('input', () => {
            const val = loginPw.value;
            if (val.length === 0) {
                loginStrContainer.style.display = 'none';
                return;
            }
            loginStrContainer.style.display = 'block';
            const { score, isStrong } = checkStrength(val);

            loginStrFill.className = 'strength-fill';
            if (isStrong) {
                loginStrFill.classList.add('strong');
                loginStrText.textContent = 'Strong Password';
                loginStrText.style.color = '#3E5C3E';
            } else if (score >= 3) {
                loginStrFill.classList.add('fair');
                loginStrText.textContent = 'Medium Password';
                loginStrText.style.color = '#B5824F';
            } else {
                loginStrFill.classList.add('weak');
                loginStrText.textContent = 'Weak Password';
                loginStrText.style.color = '#B5563F';
            }
        });
    }

    // Reset Password Strength Listener
    const newPwInput = $('new-password');
    if (newPwInput) {
        newPwInput.addEventListener('input', function () {
            const val = newPwInput.value;
            const fill = $('strength-fill');
            const label = $('strength-label-text');

            const reqLength = $('req-length');
            const reqUpper  = $('req-upper');
            const reqLower  = $('req-lower');
            const reqNumber = $('req-number');
            const reqSpecial = $('req-special');

            const isLength = val.length >= 8;
            const isUpper  = /[A-Z]/.test(val);
            const isLower  = /[a-z]/.test(val);
            const isNumber = /[0-9]/.test(val);
            const isSpecial = /[^a-zA-Z0-9]/.test(val);

            if (reqLength) reqLength.className = 'pw-rule ' + (isLength ? 'valid' : 'invalid');
            if (reqUpper) reqUpper.className = 'pw-rule ' + (isUpper ? 'valid' : 'invalid');
            if (reqLower) reqLower.className = 'pw-rule ' + (isLower ? 'valid' : 'invalid');
            if (reqNumber) reqNumber.className = 'pw-rule ' + (isNumber ? 'valid' : 'invalid');
            if (reqSpecial) reqSpecial.className = 'pw-rule ' + (isSpecial ? 'valid' : 'invalid');

            const { score, isStrong } = checkStrength(val);

            if (fill && label) {
                fill.className = 'strength-fill';
                if (val.length === 0) {
                    fill.style.width = '0%';
                    label.textContent = 'Password Strength';
                } else if (isStrong) {
                    fill.classList.add('strong');
                    label.textContent = 'Strong Password';
                    label.style.color = '#3E5C3E';
                } else if (score >= 3) {
                    fill.classList.add('fair');
                    label.textContent = 'Medium Password';
                    label.style.color = '#B5824F';
                } else {
                    fill.classList.add('weak');
                    label.textContent = 'Weak Password';
                    label.style.color = '#B5563F';
                }
            }
        });
    }

    // ---------- LOGIN FORM SUBMIT (AJAX + SweetAlert2) ----------
    const loginForm = $('login-form');
    const roleError = $('role-error');
    const loginSubmit = $('login-submit');

    if (loginForm) {
        loginForm.addEventListener('submit', function (e) {
            const selectedRole = loginForm.querySelector('input[name="role"]:checked');
            if (!selectedRole) {
                e.preventDefault();
                if (roleError) roleError.style.display = 'block';
                Swal.fire({
                    icon: 'warning',
                    title: 'Role Mismatch',
                    text: 'Please select the correct role before logging in.'
                });
                return;
            } else {
                if (roleError) roleError.style.display = 'none';
            }

            const emailVal = $('login-email').value.trim();
            if (!emailVal) {
                e.preventDefault();
                setFieldError('login-email-shell', 'login-email-error', true, 'Please enter your email address.');
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Email',
                    text: 'Please enter a valid email address.'
                });
                return;
            } else if (emailVal.includes('@') && !EMAIL_RE.test(emailVal)) {
                e.preventDefault();
                setFieldError('login-email-shell', 'login-email-error', true, 'Please enter a valid email address.');
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Email',
                    text: 'Please enter a valid email address.'
                });
                return;
            } else {
                setFieldError('login-email-shell', 'login-email-error', false);
            }

            const pwVal = $('login-password').value;
            if (!pwVal) {
                e.preventDefault();
                setFieldError('login-password-shell', 'login-password-error', true, 'Enter your password.');
                Swal.fire({
                    icon: 'error',
                    title: 'Incorrect Password',
                    text: 'The password you entered is incorrect.'
                });
                return;
            } else {
                setFieldError('login-password-shell', 'login-password-error', false);
            }

            setLoading(loginSubmit, true, 'Sign in');
        });
    }

    // ---------- FORGOT PASSWORD FORM SUBMIT ----------
    const forgotForm = $('forgot-form');
    const forgotSubmit = $('forgot-submit');

    if (forgotForm) {
        forgotForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const emailVal = $('forgot-email').value.trim();
            if (!emailVal || (!EMAIL_RE.test(emailVal) && !emailVal.includes('@'))) {
                setFieldError('forgot-email-shell', 'forgot-email-error', true, 'Please enter a valid email.');
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Email',
                    text: 'Please enter a valid email address.'
                });
                return;
            }

            setFieldError('forgot-email-shell', 'forgot-email-error', false);
            setLoading(forgotSubmit, true, 'Send OTP');

            const formData = new FormData();
            formData.append('email', emailVal);
            formData.append('action', 'send_otp');
            formData.append('csrf_token', getCsrfToken());

            fetch('forgot_password_api.php', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                setLoading(forgotSubmit, false, 'Send OTP');
                if (data.success) {
                    if ($('otp-registered-email')) $('otp-registered-email').textContent = emailVal;
                    showView(verifyOtpView);
                    Swal.fire({
                        icon: 'success',
                        title: 'OTP Sent',
                        text: data.message
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Account Not Found',
                        text: data.message
                    });
                }
            })
            .catch(err => {
                setLoading(forgotSubmit, false, 'Send OTP');
                Swal.fire({
                    icon: 'error',
                    title: 'System Error',
                    text: 'Error sending OTP. Please try again.'
                });
            });
        });
    }

    // ---------- OTP 6-BOX INPUT AUTO FOCUS ----------
    const otpBoxes = document.querySelectorAll('.otp-box');
    if (otpBoxes.length > 0) {
        otpBoxes.forEach((box, idx) => {
            box.addEventListener('input', () => {
                box.value = box.value.replace(/[^0-9]/g, '');
                if (box.value && idx < otpBoxes.length - 1) {
                    otpBoxes[idx + 1].focus();
                }
            });
            box.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !box.value && idx > 0) {
                    otpBoxes[idx - 1].focus();
                }
            });
        });
    }

    // ---------- VERIFY OTP FORM SUBMIT ----------
    const otpForm = $('otp-form');
    const otpSubmit = $('otp-submit');

    if (otpForm) {
        otpForm.addEventListener('submit', function (e) {
            e.preventDefault();

            let otpCode = '';
            otpBoxes.forEach((b) => { otpCode += b.value; });

            if (otpCode.length !== 6) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Incomplete OTP',
                    text: 'Please enter the complete 6-digit OTP code.'
                });
                return;
            }

            const emailVal = $('forgot-email') ? $('forgot-email').value.trim() : '';
            setLoading(otpSubmit, true, 'Verify OTP');

            const formData = new FormData();
            formData.append('email', emailVal);
            formData.append('otp', otpCode);
            formData.append('action', 'verify_otp');
            formData.append('csrf_token', getCsrfToken());

            fetch('forgot_password_api.php', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                setLoading(otpSubmit, false, 'Verify OTP');
                if (data.success) {
                    showView(resetPwView);
                    Swal.fire({
                        icon: 'success',
                        title: 'OTP Verified',
                        text: 'OTP verified successfully! Create your new password.'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'OTP Verification Failed',
                        text: data.message
                    });
                }
            })
            .catch(err => {
                setLoading(otpSubmit, false, 'Verify OTP');
                Swal.fire({
                    icon: 'error',
                    title: 'System Error',
                    text: 'Error verifying OTP.'
                });
            });
        });
    }

    // ---------- RESET PASSWORD FORM SUBMIT ----------
    const resetForm = $('reset-form');
    const resetSubmit = $('reset-submit');

    if (resetForm) {
        resetForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const newPw = $('new-password').value;
            const confirmPw = $('confirm-password').value;

            if (newPw !== confirmPw) {
                setFieldError('confirm-password-shell', 'confirm-password-error', true, 'Passwords do not match.');
                Swal.fire({
                    icon: 'error',
                    title: 'Password Mismatch',
                    text: 'The password confirmation does not match.'
                });
                return;
            } else {
                setFieldError('confirm-password-shell', 'confirm-password-error', false);
            }

            setLoading(resetSubmit, true, 'Reset Password');

            const formData = new FormData();
            formData.append('new_password', newPw);
            formData.append('confirm_password', confirmPw);
            formData.append('csrf_token', getCsrfToken());

            fetch('reset_password_api.php', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                setLoading(resetSubmit, false, 'Reset Password');
                if (data.success) {
                    showView(loginView);
                    Swal.fire({
                        icon: 'success',
                        title: 'Password Reset',
                        text: data.message
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Reset Failed',
                        text: data.message
                    });
                }
            })
            .catch(err => {
                setLoading(resetSubmit, false, 'Reset Password');
                Swal.fire({
                    icon: 'error',
                    title: 'System Error',
                    text: 'Error resetting password.'
                });
            });
        });
    }

    // ---------- BACKGROUND SLIDESHOW ----------
    document.addEventListener('DOMContentLoaded', () => {
        const slides = document.querySelectorAll('.brand-slideshow .slide');
        if (slides.length > 0) {
            let currentSlide = 0;
            setInterval(() => {
                slides[currentSlide].classList.remove('active');
                currentSlide = (currentSlide + 1) % slides.length;
                slides[currentSlide].classList.add('active');
            }, 5000);
        }
    });
})();
