/**
 * SevaNest – Sidebar JavaScript
 * Version  : 1.0
 * Author   : SevaNest Dev Team
 * Desc     : Handles sidebar toggle (desktop collapse / mobile drawer),
 *            active navigation state, keyboard accessibility, and
 *            overlay management. No inline scripts. Pure vanilla JS.
 *
 * Dependencies : Bootstrap 5 (for utility classes only, no JS needed)
 *
 * TABLE OF CONTENTS
 * -----------------
 * 1. Constants & DOM References
 * 2. Utility Helpers
 * 3. Desktop Collapse Toggle
 * 4. Mobile Drawer Toggle
 * 5. Active Navigation State
 * 6. Keyboard Navigation
 * 7. Resize Handler
 * 8. Initialisation
 */

'use strict';

/* ─── 1. Constants & DOM References ─────────────────────────────────────── */

/** Breakpoint (px) below which mobile behaviour is active */
const SN_MOBILE_BP = 992;

/** localStorage key for persisting collapse state across page loads */
const SN_STORAGE_KEY = 'sn_sidebar_collapsed';

const sidebar     = document.getElementById('sn-sidebar');
const overlay     = document.getElementById('sn-overlay');
const toggleBtn   = document.getElementById('sn-toggle-btn');
const mainContent = document.getElementById('sn-main-content');

/** All clickable navigation links inside the sidebar */
const navLinks = document.querySelectorAll('#sn-sidebar .sn-nav-link');


/* ─── 2. Utility Helpers ─────────────────────────────────────────────────── */

/**
 * Returns true when the viewport is in mobile range.
 * @returns {boolean}
 */
function isMobile() {
    return window.innerWidth < SN_MOBILE_BP;
}

/**
 * Reads the persisted collapse preference from localStorage.
 * @returns {boolean}
 */
function isPersistedCollapsed() {
    return localStorage.getItem(SN_STORAGE_KEY) === 'true';
}

/**
 * Persists the collapse state to localStorage.
 * @param {boolean} collapsed
 */
function persistCollapse(collapsed) {
    localStorage.setItem(SN_STORAGE_KEY, collapsed);
}

/**
 * Adds an ARIA attribute to the toggle button to communicate the current state.
 * @param {boolean} collapsed
 */
function updateToggleAria(collapsed) {
    if (!toggleBtn) return;
    toggleBtn.setAttribute('aria-expanded', String(!collapsed));
    toggleBtn.setAttribute(
        'aria-label',
        collapsed ? 'Expand sidebar navigation' : 'Collapse sidebar navigation'
    );
}


/* ─── 3. Desktop Collapse Toggle ─────────────────────────────────────────── */

/**
 * Collapses the desktop sidebar to icon-only mode.
 * Persists the preference and updates ARIA.
 */
function collapseDesktop() {
    document.body.classList.add('sn-collapsed');
    persistCollapse(true);
    updateToggleAria(true);
}

/**
 * Expands the desktop sidebar to full-width mode.
 * Persists the preference and updates ARIA.
 */
function expandDesktop() {
    document.body.classList.remove('sn-collapsed');
    persistCollapse(false);
    updateToggleAria(false);
}

/**
 * Toggles the desktop collapse state.
 */
function toggleDesktop() {
    if (document.body.classList.contains('sn-collapsed')) {
        expandDesktop();
    } else {
        collapseDesktop();
    }
}


/* ─── 4. Mobile Drawer Toggle ────────────────────────────────────────────── */

/**
 * Opens the mobile sidebar drawer and shows the overlay.
 */
function openMobile() {
    if (!sidebar || !overlay) return;

    sidebar.classList.add('sn-mobile-open');
    overlay.style.display = 'block';

    /* Trigger reflow so the opacity transition fires */
    void overlay.offsetWidth;
    overlay.classList.add('sn-overlay-visible');

    document.body.style.overflow = 'hidden';   /* prevent background scroll */
    sidebar.setAttribute('aria-hidden', 'false');
    toggleBtn && toggleBtn.setAttribute('aria-expanded', 'true');
}

/**
 * Closes the mobile sidebar drawer and hides the overlay.
 */
function closeMobile() {
    if (!sidebar || !overlay) return;

    sidebar.classList.remove('sn-mobile-open');
    overlay.classList.remove('sn-overlay-visible');

    /* Wait for the transition to finish before hiding the overlay */
    setTimeout(() => {
        overlay.style.display = 'none';
    }, 300);

    document.body.style.overflow = '';
    sidebar.setAttribute('aria-hidden', 'true');
    toggleBtn && toggleBtn.setAttribute('aria-expanded', 'false');
}

/**
 * Toggles the mobile drawer.
 */
function toggleMobile() {
    if (sidebar.classList.contains('sn-mobile-open')) {
        closeMobile();
    } else {
        openMobile();
    }
}


/* ─── 5. Active Navigation State ─────────────────────────────────────────── */

/**
 * Marks a given nav link as active and removes the active class from all others.
 * Uses smooth CSS transitions defined in sidebar.css.
 * @param {HTMLElement} activeLink – the link element to mark as active
 */
function setActiveNav(activeLink) {
    navLinks.forEach((link) => {
        link.classList.remove('active');
        link.removeAttribute('aria-current');
    });

    activeLink.classList.add('active');
    activeLink.setAttribute('aria-current', 'page');
}

/**
 * Detects the active page from the current URL and highlights the matching link.
 * Falls back to the first nav link if no match is found.
 */
function syncActiveFromUrl() {
    const currentPath = window.location.pathname;
    const currentFile = currentPath.split('/').pop() || '';

    let matched = false;

    navLinks.forEach((link) => {
        const href     = link.getAttribute('href') || '';
        const linkFile = href.split('/').pop();

        /* Match by filename (e.g. residents.php) or full path */
        if (linkFile && currentFile && linkFile === currentFile) {
            setActiveNav(link);
            matched = true;
        }
    });

    /* If no URL match, honour any pre-existing active class in the PHP markup */
    if (!matched) {
        const preActive = document.querySelector('#sn-sidebar .sn-nav-link.active');
        if (preActive) {
            setActiveNav(preActive);
        }
    }
}

/**
 * Attaches click listeners to all nav links so clicking updates the active state.
 * The actual page navigation is handled normally by the browser.
 */
function bindNavClicks() {
    navLinks.forEach((link) => {
        link.addEventListener('click', (e) => {
            /* Do not prevent default – allow real navigation */
            setActiveNav(link);

            /* Close mobile drawer after selection */
            if (isMobile()) {
                closeMobile();
            }
        });
    });
}


/* ─── 6. Keyboard Navigation ─────────────────────────────────────────────── */

/**
 * Allows closing the mobile drawer with the Escape key.
 * @param {KeyboardEvent} e
 */
function handleKeydown(e) {
    if (e.key === 'Escape' && isMobile()) {
        closeMobile();
        toggleBtn && toggleBtn.focus();
    }
}

/**
 * Enables arrow-key navigation within the sidebar nav list for accessibility.
 * @param {KeyboardEvent} e
 */
function handleNavKeydown(e) {
    const items    = Array.from(navLinks);
    const idx      = items.indexOf(document.activeElement);

    if (idx === -1) return;

    if (e.key === 'ArrowDown') {
        e.preventDefault();
        const next = items[idx + 1];
        if (next) next.focus();
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        const prev = items[idx - 1];
        if (prev) prev.focus();
    } else if (e.key === 'Home') {
        e.preventDefault();
        items[0] && items[0].focus();
    } else if (e.key === 'End') {
        e.preventDefault();
        items[items.length - 1] && items[items.length - 1].focus();
    }
}


/* ─── 7. Resize Handler ──────────────────────────────────────────────────── */

/**
 * Handles viewport resize events.
 * - Closes the mobile drawer when switching to desktop.
 * - Restores desktop collapse state when coming back from mobile.
 */
function handleResize() {
    if (!isMobile()) {
        /* Switching to desktop: close mobile drawer cleanly */
        if (sidebar) sidebar.classList.remove('sn-mobile-open');
        if (overlay) {
            overlay.classList.remove('sn-overlay-visible');
            overlay.style.display = 'none';
        }
        document.body.style.overflow = '';

        /* Restore persisted desktop collapse state */
        if (isPersistedCollapsed()) {
            collapseDesktop();
        } else {
            expandDesktop();
        }
    } else {
        /* Switching to mobile: always expand sidebar (it will be hidden via CSS) */
        document.body.classList.remove('sn-collapsed');
        sidebar && sidebar.setAttribute('aria-hidden', 'true');
    }
}


/* ─── 8. Initialisation ──────────────────────────────────────────────────── */

/**
 * Main initialisation function – runs after the DOM is ready.
 */
function initSidebar() {

    /* ── Guard: required elements must exist ── */
    if (!sidebar || !toggleBtn) {
        console.warn('SevaNest Sidebar: Required elements not found (#sn-sidebar, #sn-toggle-btn).');
        return;
    }

    /* ── Initial state ── */
    if (!isMobile()) {
        /* Desktop: restore last-known collapse preference */
        if (isPersistedCollapsed()) {
            collapseDesktop();
        } else {
            expandDesktop();
        }
        sidebar.setAttribute('aria-hidden', 'false');
    } else {
        /* Mobile: sidebar starts hidden */
        sidebar.setAttribute('aria-hidden', 'true');
        toggleBtn.setAttribute('aria-expanded', 'false');
    }

    /* ── Toggle button click ── */
    toggleBtn.addEventListener('click', () => {
        if (isMobile()) {
            toggleMobile();
        } else {
            toggleDesktop();
        }
    });

    /* ── Overlay click closes mobile drawer ── */
    if (overlay) {
        overlay.addEventListener('click', closeMobile);
    }

    /* ── Nav link clicks & active state ── */
    bindNavClicks();
    syncActiveFromUrl();

    /* ── Keyboard listeners ── */
    document.addEventListener('keydown', handleKeydown);
    navLinks.forEach((link) => {
        link.addEventListener('keydown', handleNavKeydown);
    });

    /* ── Resize listener (debounced) ── */
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(handleResize, 120);
    });

    /* ── ARIA role on nav ── */
    sidebar.setAttribute('role', 'navigation');
    sidebar.setAttribute('aria-label', 'Main sidebar navigation');
}

/* Run once DOM is fully parsed */
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSidebar);
} else {
    initSidebar();
}
