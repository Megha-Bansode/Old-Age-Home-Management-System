/**
 * SevaNest Old Age Home Management System
 * Reusable Dashboard Interactions & UI Logic
 */

document.addEventListener('DOMContentLoaded', () => {
    // Initialize common dashboard interface hooks
    initSidebarCollapse();
    initCardExpand();
    initNotificationPanel();
    initUserMenu();
    initDashboardWidgets();
    initThemeSwitch();
});

/**
 * Initializes Sidebar Collapse/Expand functionality
 * Toggles class '.collapsed' on '.sidebar-wrapper' and stores state in localStorage
 */
function initSidebarCollapse() {
    const toggleBtn = document.querySelector('.sidebar-toggle');
    const sidebar = document.querySelector('.sidebar-wrapper');
    
    if (!toggleBtn || !sidebar) return;

    // Check saved state from localStorage
    const isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
    if (isCollapsed) {
        sidebar.classList.add('collapsed');
    }

    toggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');
        
        // Also support overlay sidebar toggle on tablet/mobile screens
        sidebar.classList.toggle('mobile-active');
        
        // Save state to localStorage
        localStorage.setItem('sidebar-collapsed', sidebar.classList.contains('collapsed'));
    });
}

/**
 * Initializes Card Expand/Collapse functionality (Full Screen Mode)
 * Toggles a '.card-fullscreen' class on card elements to overlay them on the screen
 */
function initCardExpand() {
    const expandButtons = document.querySelectorAll('.card-expand-btn');
    
    expandButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            const card = e.target.closest('.card');
            if (card) {
                card.classList.toggle('card-fullscreen');
            }
        });
    });
}

/**
 * Initializes Notification Panel toggle handler
 * Displays notification panel dropdown when toggle button is clicked
 * and dismisses it when the user clicks anywhere outside of the panel
 */
function initNotificationPanel() {
    const toggleBtn = document.querySelector('.notification-toggle');
    const panel = document.querySelector('.notification-panel');

    if (!toggleBtn || !panel) return;

    toggleBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        panel.classList.toggle('active');
        
        // Close user profile dropdown if active
        const userMenu = document.querySelector('.user-menu-dropdown');
        if (userMenu) userMenu.classList.remove('active');
    });

    // Close notification panel when clicking outside
    document.addEventListener('click', (e) => {
        if (!panel.contains(e.target) && e.target !== toggleBtn) {
            panel.classList.remove('active');
        }
    });
}

/**
 * Initializes User Profile Dropdown Menu toggle
 * Displays dropdown list when user avatar/name is clicked
 * and dismisses it when the user clicks anywhere outside of the menu
 */
function initUserMenu() {
    const toggleBtn = document.querySelector('.user-menu-toggle');
    const menu = document.querySelector('.user-menu-dropdown');

    if (!toggleBtn || !menu) return;

    toggleBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        menu.classList.toggle('active');
        
        // Close notification panel if active
        const panel = document.querySelector('.notification-panel');
        if (panel) panel.classList.remove('active');
    });

    // Close user menu when clicking outside
    document.addEventListener('click', (e) => {
        if (!menu.contains(e.target) && e.target !== toggleBtn) {
            menu.classList.remove('active');
        }
    });
}

/**
 * Placeholder for Dashboard Widgets initialization
 * Hook for initializing cards, loading data dynamically, or setting up responsive gauges/charts
 */
function initDashboardWidgets() {
    // Select placeholder container nodes if present
    const widgetContainers = document.querySelectorAll('.widget-placeholder');
    
    if (widgetContainers.length === 0) return;

    widgetContainers.forEach(container => {
        // Simulating widget async content loading
        const widgetType = container.getAttribute('data-widget-type');
        console.log(`[SevaNest Dashboard] Initializing widget: ${widgetType}`);
        
        // Custom widget load routines can be appended here
    });
}

/**
 * Placeholder/Baseline for Light & Dark Theme Switch
 * Toggles a body attribute or class and saves selection in localStorage
 */
function initThemeSwitch() {
    const themeToggleBtn = document.querySelector('.theme-toggle');
    if (!themeToggleBtn) return;

    // Load initial theme from localStorage
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', savedTheme);
    updateThemeToggleUI(themeToggleBtn, savedTheme);

    themeToggleBtn.addEventListener('click', () => {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        
        document.documentElement.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        updateThemeToggleUI(themeToggleBtn, newTheme);
    });
}

/**
 * Utility to update theme switch UI indicators
 * @param {HTMLElement} btn - The toggle element
 * @param {string} theme - Active theme ('light' or 'dark')
 */
function updateThemeToggleUI(btn, theme) {
    if (theme === 'dark') {
        btn.classList.add('dark-active');
        btn.setAttribute('aria-label', 'Switch to light mode');
    } else {
        btn.classList.remove('dark-active');
        btn.setAttribute('aria-label', 'Switch to dark mode');
    }
}
