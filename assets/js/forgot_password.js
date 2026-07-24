/**
 * SevaNest Old Age Home Management System
 * Forgot Password Page Logic
 */

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('forgot-password-form');
    const emailInput = document.getElementById('email');
    const alertContainer = document.getElementById('alert-container');
    const submitBtn = document.getElementById('send-otp-btn');

    if (!form || !emailInput || !alertContainer || !submitBtn) return;

    // Reset error styling on input change
    emailInput.addEventListener('input', () => {
        if (typeof clearValidation === 'function') {
            clearValidation(emailInput);
        }
    });

    form.addEventListener('submit', (e) => {
        e.preventDefault();

        // 1. Reset states
        alertContainer.innerHTML = '';
        if (typeof clearValidation === 'function') {
            clearValidation(emailInput);
        }

        const emailValue = emailInput.value.trim();

        // 2. Perform Validation
        let isValid = true;
        let errorMessage = '';

        // Checking Required field
        if (typeof validateRequired === 'function') {
            if (!validateRequired(emailValue)) {
                isValid = false;
                errorMessage = 'Email address is required.';
            } else if (typeof validateEmail === 'function' && !validateEmail(emailValue)) {
                isValid = false;
                errorMessage = 'Please enter a valid email address.';
            }
        } else {
            // Fallback validation if validation.js is not loaded
            if (emailValue === '') {
                isValid = false;
                errorMessage = 'Email address is required.';
            } else {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(emailValue)) {
                    isValid = false;
                    errorMessage = 'Please enter a valid email address.';
                }
            }
        }

        // 3. Handle errors if any
        if (!isValid) {
            if (typeof handleError === 'function') {
                handleError(emailInput, errorMessage);
            } else {
                emailInput.classList.add('is-invalid');
            }
            showAlert('danger', 'bi-exclamation-triangle-fill', errorMessage);
            return;
        }

        // Mark input as valid
        if (typeof handleSuccess === 'function') {
            handleSuccess(emailInput);
        }

        // 4. Show Loading State on Submit Button
        const originalBtnHTML = submitBtn.innerHTML;
        setButtonLoading(submitBtn, true);

        // 5. Simulate OTP delivery delay (1.5 seconds)
        setTimeout(() => {
            // Restore button state
            setButtonLoading(submitBtn, false, originalBtnHTML);

            // Display success message
            showAlert('success', 'bi-check-circle-fill', 'An OTP has been successfully sent to your registered email address. Redirecting to OTP verification page...');
            
            // Clear input field on success
            emailInput.value = '';
            if (typeof clearValidation === 'function') {
                clearValidation(emailInput);
            }

            // Redirect to verify_otp.php after success
            setTimeout(() => {
                window.location.href = 'verify_otp.php';
            }, 1500);
        }, 1500);
    });

    /**
     * Helper to render alert alerts dynamically in the DOM
     * @param {string} type - Alert type ('success', 'danger', 'warning', 'info')
     * @param {string} iconClass - Bootstrap Icons class name
     * @param {string} message - Content message
     */
    function showAlert(type, iconClass, message) {
        alertContainer.innerHTML = `
            <div class="alert alert-${type} animate-fade-in" role="alert">
                <i class="bi ${iconClass}" aria-hidden="true"></i>
                <div>${message}</div>
            </div>
        `;
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
                <span>Sending...</span>
            `;
        } else {
            button.disabled = false;
            button.innerHTML = originalHTML || 'Send OTP';
        }
    }
});
