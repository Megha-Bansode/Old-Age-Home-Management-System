/**
 * SevaNest Old Age Home Management System
 * Reset Password Page Logic
 */

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('reset-password-form');
    const newPasswordInput = document.getElementById('new-password');
    const confirmPasswordInput = document.getElementById('confirm-password');
    const strengthBar = document.getElementById('strength-bar');
    const strengthText = document.getElementById('strength-text');
    const toggleButtons = document.querySelectorAll('.password-toggle-btn');
    const alertContainer = document.getElementById('alert-container');
    const submitBtn = document.getElementById('reset-password-btn');

    if (!form || !newPasswordInput || !confirmPasswordInput || !strengthBar || !strengthText || !alertContainer || !submitBtn) return;

    // 1. Password Visibility Toggle functionality
    toggleButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const input = btn.parentElement.querySelector('input');
            const icon = btn.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
                btn.setAttribute('aria-label', 'Hide password');
            } else {
                input.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
                btn.setAttribute('aria-label', 'Show password');
            }
        });
    });

    // 2. Real-time Password Strength Meter
    newPasswordInput.addEventListener('input', () => {
        const passwordValue = newPasswordInput.value;
        updatePasswordStrength(passwordValue);
    });

    // Reset confirm password errors on input
    confirmPasswordInput.addEventListener('input', () => {
        if (typeof clearValidation === 'function') {
            clearValidation(confirmPasswordInput);
        }
    });

    // Reset new password errors on input
    newPasswordInput.addEventListener('input', () => {
        if (typeof clearValidation === 'function') {
            clearValidation(newPasswordInput);
        }
    });

    // 3. Form Submit Validation
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        alertContainer.innerHTML = '';
        
        if (typeof clearValidation === 'function') {
            clearValidation(newPasswordInput);
            clearValidation(confirmPasswordInput);
        }

        const newPassword = newPasswordInput.value;
        const confirmPassword = confirmPasswordInput.value;

        let isValid = true;
        let errors = [];

        // Validate Minimum password length
        if (newPassword.length < 8) {
            isValid = false;
            const msg = 'Password must be at least 8 characters long.';
            errors.push(msg);
            if (typeof handleError === 'function') {
                handleError(newPasswordInput, msg);
            }
        }

        // Validate Confirm Password matches
        if (newPassword !== confirmPassword) {
            isValid = false;
            const msg = 'Passwords do not match.';
            errors.push(msg);
            if (typeof handleError === 'function') {
                handleError(confirmPasswordInput, msg);
            }
        }

        // Empty checks
        if (newPassword === '') {
            isValid = false;
            if (typeof handleError === 'function') {
                handleError(newPasswordInput, 'Password is required.');
            }
        }
        if (confirmPassword === '') {
            isValid = false;
            if (typeof handleError === 'function') {
                handleError(confirmPasswordInput, 'Confirm password is required.');
            }
        }

        if (!isValid) {
            showAlert('danger', 'bi-exclamation-triangle-fill', errors.length > 0 ? errors[0] : 'Please verify all inputs.');
            return;
        }

        // Mark valid
        if (typeof handleSuccess === 'function') {
            handleSuccess(newPasswordInput);
            handleSuccess(confirmPasswordInput);
        }

        // Show Loading State on Button
        const originalBtnHTML = submitBtn.innerHTML;
        setButtonLoading(submitBtn, true);

        // Simulate password reset delay (1.5 seconds)
        setTimeout(() => {
            setButtonLoading(submitBtn, false, originalBtnHTML);

            showAlert('success', 'bi-check-circle-fill', 'Your password has been successfully reset! Redirecting to login page...');

            // Redirect to login.php after success
            setTimeout(() => {
                window.location.href = 'login.php';
            }, 1500);
        }, 1500);
    });

    /**
     * Analyzes password strength and updates the UI indicators
     * @param {string} password - The password string to test
     */
    function updatePasswordStrength(password) {
        // Reset state on empty password
        if (password === '') {
            strengthBar.style.width = '0%';
            strengthBar.className = 'strength-bar';
            strengthText.textContent = 'Empty';
            strengthText.className = 'strength-text';
            return;
        }

        let strength = { score: 0, text: 'Weak', class: 'weak' };

        // Utilize global password strength checker if present
        if (typeof validatePasswordStrength === 'function') {
            const result = validatePasswordStrength(password);
            const score = result.score;
            
            if (score <= 2) {
                strength = { score: score, text: 'Weak', class: 'weak' };
            } else if (score <= 4) {
                strength = { score: score, text: 'Medium', class: 'medium' };
            } else {
                strength = { score: score, text: 'Strong', class: 'strong' };
            }
        } else {
            // Fallback rating logic
            let score = 0;
            if (password.length >= 8) score++;
            if (/[A-Z]/.test(password)) score++;
            if (/[a-z]/.test(password)) score++;
            if (/[0-9]/.test(password)) score++;
            if (/[^A-Za-z0-9]/.test(password)) score++;

            if (score <= 2) {
                strength = { score: score, text: 'Weak', class: 'weak' };
            } else if (score <= 4) {
                strength = { score: score, text: 'Medium', class: 'medium' };
            } else {
                strength = { score: score, text: 'Strong', class: 'strong' };
            }
        }

        // Map score to percentages
        const percentage = Math.min((strength.score / 5) * 100, 100);

        // Update DOM elements
        strengthBar.style.width = `${percentage}%`;
        strengthBar.className = `strength-bar strength-${strength.class}`;
        strengthText.textContent = strength.text;
        strengthText.className = `strength-text text-${strength.class}`;
    }

    /**
     * Toggles button loading state
     * @param {HTMLButtonElement} button - Button DOM node
     * @param {boolean} isLoading - Active state
     * @param {string} [originalHTML] - Backup HTML to restore
     */
    function setButtonLoading(button, isLoading, originalHTML) {
        if (isLoading) {
            button.disabled = true;
            button.innerHTML = `
                <span class="loader-spinner" style="width: 18px; height: 18px; border-width: 2px; margin-right: 8px; display: inline-block; vertical-align: middle;"></span>
                <span>Resetting...</span>
            `;
        } else {
            button.disabled = false;
            button.innerHTML = originalHTML || 'Reset Password';
        }
    }

    /**
     * Displays a dynamic alert component in the alert container
     * @param {string} type - Alert type ('success', 'danger')
     * @param {string} iconClass - Bootstrap icon class name
     * @param {string} message - Text inside alert
     */
    function showAlert(type, iconClass, message) {
        alertContainer.innerHTML = `
            <div class="alert alert-${type} animate-fade-in" role="alert">
                <i class="bi ${iconClass}" aria-hidden="true"></i>
                <div>${message}</div>
            </div>
        `;
    }
});
