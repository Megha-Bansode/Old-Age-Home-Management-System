/**
 * SevaNest – Family Member Dashboard JavaScript
 * File     : assets/js/dashboard.js
 * Version  : 1.0
 * Author   : SevaNest Dev Team
 *
 * Features
 * --------
 * 1. Live clock / date display in the topbar and page header.
 * 2. Entrance animations for cards on DOMContentLoaded.
 * 3. Subtle keyboard accessibility for summary cards.
 */

'use strict';

/* ─────────────────────────────────────────────────────────────────────────
 * 1. Live date / time helpers
 * ───────────────────────────────────────────────────────────────────────── */

/**
 * Format a Date object as "Sunday, 20 July 2026".
 * @param {Date} d
 * @returns {string}
 */
function formatDate(d) {
    const days   = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
    const months = ['January','February','March','April','May','June',
                    'July','August','September','October','November','December'];

    return `${days[d.getDay()]}, ${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
}

/**
 * Update all elements that display the current date.
 * Targets: [data-dn-live-date]
 */
function updateDate() {
    const now   = new Date();
    const text  = formatDate(now);
    document.querySelectorAll('[data-dn-live-date]').forEach(el => {
        el.textContent = text;
    });
}


/* ─────────────────────────────────────────────────────────────────────────
 * 2. Entrance animation trigger
 *    Cards that carry the .dn-animate class have their animation driven by
 *    CSS; this JS simply ensures no element is missed when JS loads late.
 * ───────────────────────────────────────────────────────────────────────── */

function triggerAnimations() {
    // Elements are already given .dn-animate in the HTML; nothing extra needed.
    // This hook exists for future progressive enhancement (e.g. IntersectionObserver).
}


/* ─────────────────────────────────────────────────────────────────────────
 * 3. Keyboard navigation for summary cards
 *    Summary cards are <a> elements so they are natively focusable.
 *    Add role="button" semantics for screen readers on any non-anchor cards.
 * ───────────────────────────────────────────────────────────────────────── */

function initCardAccessibility() {
    document.querySelectorAll('.dn-summary-card').forEach(card => {
        // Cards rendered as <a> are already accessible.
        if (card.tagName.toLowerCase() === 'a') return;

        // Fallback for div-based cards (make them keyboard-activatable).
        card.setAttribute('tabindex', '0');
        card.setAttribute('role', 'button');

        card.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                card.click();
            }
        });
    });
}


/* ─────────────────────────────────────────────────────────────────────────
 * 4. Tooltip-like hover status on Health cards (optional micro-interaction)
 *    On hover, briefly pulse the check icon colour to reassure the user.
 * ───────────────────────────────────────────────────────────────────────── */

function initHealthItemHover() {
    document.querySelectorAll('.dn-health-item').forEach(item => {
        const icon = item.querySelector('i');
        if (!icon) return;

        item.addEventListener('mouseenter', () => {
            icon.style.transform  = 'scale(1.15)';
            icon.style.transition = 'transform 150ms ease';
        });
        item.addEventListener('mouseleave', () => {
            icon.style.transform = '';
        });
    });
}


/* ─────────────────────────────────────────────────────────────────────────
 * 5. Bootstrap Tooltip initialisation (for cards that use data-bs-toggle)
 * ───────────────────────────────────────────────────────────────────────── */

function initBootstrapTooltips() {
    const tooltipEls = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        tooltipEls.forEach(el => new bootstrap.Tooltip(el, { trigger: 'hover focus' }));
    }
}


/* ─────────────────────────────────────────────────────────────────────────
 * 6. Init – runs after DOM is ready
 * ───────────────────────────────────────────────────────────────────────── */

document.addEventListener('DOMContentLoaded', function () {
    // Date display
    updateDate();

    // Animations
    triggerAnimations();

    // Accessibility
    initCardAccessibility();

    // Micro-interactions
    initHealthItemHover();

    // Bootstrap tooltips
    initBootstrapTooltips();
});
