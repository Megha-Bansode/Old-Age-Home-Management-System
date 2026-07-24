/**
 * SevaNest Old Age Home Management System
 * Verify OTP Page Logic
 */

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('otp-verification-form');
    const inputs = document.querySelectorAll('.otp-digit-input');
    const verifyBtn = document.getElementById('verify-otp-btn');
    const resendLink = document.getElementById('resend-otp-link');
    const timerText = document.getElementById('countdown-timer-text');
    const alertContainer = document.getElementById('alert-container');

    if (!form || inputs.length === 0 || !verifyBtn || !resendLink || !timerText || !alertContainer) return;

    // Start 30 seconds countdown timer initially
    let countdown;
    startCountdown(30);

    // 1. Focus on the first input on load
    inputs[0].focus();

    // 2. Add input listeners to handle auto-focus direction, numeric restriction, and backspace
    inputs.forEach((input, index) => {
        // Enforce numeric only and auto-advance
        input.addEventListener('input', (e) => {
            const val = e.target.value;
            
            // Allow only numbers
            if (!/^[0-9]$/.test(val)) {
                e.target.value = '';
                return;
            }

            // Auto focus next input
            if (val !== '' && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }
        });

        // Handle Backspace deletion and focus reversing
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace') {
                if (input.value === '') {
                    // If current input is empty, focus and clear the previous input
                    if (index > 0) {
                        inputs[index - 1].focus();
                        inputs[index - 1].value = '';
                    }
                } else {
                    // Just clear the current input value
                    input.value = '';
                }
                e.preventDefault();
            }
        });

        // Highlight input text when focusing for user experience
        input.addEventListener('focus', () => {
            input.select();
        });
    });

    // 3. Handle Clipboard Paste logic (e.g. split "123456" into all boxes)
    inputs[0].addEventListener('paste', (e) => {
        const pasteData = (e.clipboardData || window.clipboardData).getData('text').trim();
        
        // Check if pasted content is exactly 6 digits
        if (/^[0-9]{6}$/.test(pasteData)) {
            const digits = pasteData.split('');
            inputs.forEach((input, index) => {
                input.value = digits[index];
            });
            // Focus the last input box
            inputs[inputs.length - 1].focus();
            e.preventDefault();
        }
    });

    // 4. Form Submit & Validation
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        alertContainer.innerHTML = '';

        // Combine inputs to form OTP string
        let otpCode = '';
        let hasEmptyFields = false;

        inputs.forEach(input => {
            const val = input.value.trim();
            if (val === '') {
                hasEmptyFields = true;
                input.classList.add('is-invalid');
            } else {
                input.classList.remove('is-invalid');
                otpCode += val;
            }
        });

        // Validation checking
        if (hasEmptyFields || otpCode.length < 6) {
            showAlert('danger', 'bi-exclamation-triangle-fill', 'Please enter the complete 6-digit verification code.');
            return;
        }

        // Set Loading state on Verify OTP button
        const originalBtnHTML = verifyBtn.innerHTML;
        setButtonLoading(verifyBtn, true);

        // Simulate verification validation delay (1.5 seconds)
        setTimeout(() => {
            setButtonLoading(verifyBtn, false, originalBtnHTML);

            // Mock check (e.g. if code is '123456' or simple check)
            if (otpCode === '000000') {
                showAlert('danger', 'bi-exclamation-triangle-fill', 'The OTP code is invalid or has expired. Please try again.');
            } else {
                showAlert('success', 'bi-check-circle-fill', 'OTP verified successfully! Redirecting to password reset page...');
                
                // Simulate redirect to reset_password.php
                setTimeout(() => {
                    window.location.href = 'reset_password.php';
                }, 1000);
            }
        }, 1500);
    });

    // 5. Resend OTP button handler
    resendLink.addEventListener('click', (e) => {
        e.preventDefault();
        
        // Prevent click if disabled
        if (resendLink.classList.contains('disabled-link')) return;

        alertContainer.innerHTML = '';
        
        // Show mock resend alert
        showAlert('success', 'bi-check-circle-fill', 'A new 6-digit verification code has been sent to your email.');

        // Restart countdown timer
        startCountdown(30);
        
        // Clear digits
        inputs.forEach(input => {
            input.value = '';
            input.classList.remove('is-invalid', 'is-valid');
        });
        inputs[0].focus();
    });

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
                <span>Verifying...</span>
            `;
        } else {
            button.disabled = false;
            button.innerHTML = originalHTML || 'Verify OTP';
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

    /**
     * Starts the countdown timer for the Resend link
     * @param {number} seconds - Duration of timer in seconds
     */
    function startCountdown(seconds) {
        // Clear existing interval if any
        if (countdown) clearInterval(countdown);

        resendLink.classList.add('disabled-link');
        timerText.style.display = 'inline';
        timerText.textContent = `(${seconds}s)`;

        let remaining = seconds;
        countdown = setInterval(() => {
            remaining--;
            timerText.textContent = `(${remaining}s)`;

            if (remaining <= 0) {
                clearInterval(countdown);
                resendLink.classList.remove('disabled-link');
                timerText.style.display = 'none';
            }
        }, 1000);
    }
});
