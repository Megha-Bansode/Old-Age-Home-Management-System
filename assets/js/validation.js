/**
 * SevaNest Old Age Home Management System
 * Reusable Frontend Validation Utilities
 */

/**
 * Checks if a value is completely empty (null, undefined, empty string, or whitespace only)
 * @param {*} value - The value to check
 * @returns {boolean} - True if empty, false otherwise
 */
function isEmpty(value) {
    if (value === null || value === undefined) return true;
    if (typeof value === 'string') return value.trim() === '';
    if (Array.isArray(value)) return value.length === 0;
    return false;
}

/**
 * Validates that a required field is not empty
 * @param {string} value - The input value
 * @returns {boolean} - True if valid, false otherwise
 */
function validateRequired(value) {
    return !isEmpty(value);
}

/**
 * Validates if the email matches a standard format pattern
 * @param {string} email - The email address to check
 * @returns {boolean} - True if valid format, false otherwise
 */
function validateEmail(email) {
    if (isEmpty(email)) return false;
    const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    return emailRegex.test(email.trim());
}

/**
 * Validates if the phone number matches standard patterns (allows digits, spaces, hyphens, and + sign)
 * @param {string} phone - The phone number to validate
 * @returns {boolean} - True if valid format, false otherwise
 */
function validatePhone(phone) {
    if (isEmpty(phone)) return false;
    // Allows optional '+' followed by 7 to 15 digits (spaces/hyphens allowed inside number)
    const phoneRegex = /^\+?[0-9\s\-()]{7,15}$/;
    return phoneRegex.test(phone.trim());
}

/**
 * Checks password strength based on standard rules
 * - At least 8 characters
 * - At least one uppercase letter
 * - At least one lowercase letter
 * - At least one digit
 * - At least one special character
 * @param {string} password - The password to analyze
 * @returns {Object} - Object containing { isValid: boolean, score: number, feedback: string[] }
 */
function validatePasswordStrength(password) {
    const feedback = [];
    let score = 0;

    if (isEmpty(password)) {
        return { isValid: false, score: 0, feedback: ['Password is required'] };
    }

    if (password.length >= 8) {
        score++;
    } else {
        feedback.push('Must be at least 8 characters long');
    }

    if (/[A-Z]/.test(password)) {
        score++;
    } else {
        feedback.push('Must contain at least one uppercase letter');
    }

    if (/[a-z]/.test(password)) {
        score++;
    } else {
        feedback.push('Must contain at least one lowercase letter');
    }

    if (/[0-9]/.test(password)) {
        score++;
    } else {
        feedback.push('Must contain at least one number');
    }

    if (/[^A-Za-z0-9]/.test(password)) {
        score++;
    } else {
        feedback.push('Must contain at least one special character');
    }

    return {
        isValid: score === 5,
        score: score, // Scale of 1 to 5
        feedback: feedback
    };
}

/**
 * Validates if two password values match exactly
 * @param {string} password - Original password
 * @param {string} confirmPassword - Password confirmation
 * @returns {boolean} - True if matching, false otherwise
 */
function validateConfirmPassword(password, confirmPassword) {
    return password === confirmPassword;
}

/**
 * Formats and displays an error message for an input field
 * Adds '.is-invalid' class and creates/updates an error message element underneath
 * @param {HTMLElement} inputElement - The DOM element under validation
 * @param {string} errorMessage - The message to show
 */
function handleError(inputElement, errorMessage) {
    if (!inputElement) return;

    // Add invalid class to trigger CSS styles
    inputElement.classList.add('is-invalid');
    inputElement.classList.remove('is-valid');

    // Find or create validation error message sibling
    let errorContainer = inputElement.parentElement.querySelector('.validation-error-msg');
    if (!errorContainer) {
        errorContainer = document.createElement('div');
        errorContainer.className = 'validation-error-msg';
        errorContainer.style.color = 'var(--color-danger)';
        errorContainer.style.fontSize = 'var(--font-size-xs)';
        errorContainer.style.marginTop = '4px';
        inputElement.parentElement.appendChild(errorContainer);
    }
    errorContainer.textContent = errorMessage;
    errorContainer.style.display = 'block';
}

/**
 * Formats and marks an input field as valid
 * Adds '.is-valid' class and hides any existing error message elements
 * @param {HTMLElement} inputElement - The DOM element under validation
 */
function handleSuccess(inputElement) {
    if (!inputElement) return;

    inputElement.classList.add('is-valid');
    inputElement.classList.remove('is-invalid');

    const errorContainer = inputElement.parentElement.querySelector('.validation-error-msg');
    if (errorContainer) {
        errorContainer.textContent = '';
        errorContainer.style.display = 'none';
    }
}

/**
 * Resets the validation state of an input field back to neutral
 * @param {HTMLElement} inputElement - The DOM element to clear
 */
function clearValidation(inputElement) {
    if (!inputElement) return;

    inputElement.classList.remove('is-valid', 'is-invalid');
    const errorContainer = inputElement.parentElement.querySelector('.validation-error-msg');
    if (errorContainer) {
        errorContainer.textContent = '';
        errorContainer.style.display = 'none';
    }
}

/**
 * Helper function to validate an entire form based on a selector rules schema
 * @param {HTMLFormElement} formElement - The form element to validate
 * @param {Object} schema - Object containing selector mapping to validation functions and messages
 * @example
 * const schema = {
 *    '#email': { validate: validateEmail, message: 'Invalid email address' },
 *    '#name': { validate: validateRequired, message: 'Name is required' }
 * };
 * @returns {boolean} - True if the entire form is valid, false otherwise
 */
function validateForm(formElement, schema) {
    if (!formElement || !schema) return false;

    let isFormValid = true;

    for (const selector in schema) {
        const inputElement = formElement.querySelector(selector);
        if (!inputElement) continue;

        const rule = schema[selector];
        const value = inputElement.value;

        // Custom validation function check
        let isFieldValid = true;
        if (typeof rule.validate === 'function') {
            isFieldValid = rule.validate(value);
        }

        if (!isFieldValid) {
            handleError(inputElement, rule.message);
            isFormValid = false;
        } else {
            handleSuccess(inputElement);
        }
    }

    return isFormValid;
}
