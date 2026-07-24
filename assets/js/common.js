/**
 * SevaNest Old Age Home Management System
 * Shared Reusable Frontend Scripts
 */

document.addEventListener('DOMContentLoaded', () => {
    // Initialize common layout interaction elements
    initLiveClock();
    initStickyHeader();
    initDropdownAccessibility();
    initHeroSlideshow();
});

/**
 * Live Clock functionality
 * Updates the text content of any clock element on the page every second
 */
function initLiveClock() {
    const clockElements = document.querySelectorAll('.oahms-clock-time, .live-clock');
    if (clockElements.length === 0) return;

    function updateTime() {
        const now = new Date();
        
        // Format time: HH:MM:SS AM/PM
        let hours = now.getHours();
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        const ampm = hours >= 12 ? 'PM' : 'AM';
        
        hours = hours % 12;
        hours = hours ? hours : 12; // the hour '0' should be '12'
        const formattedHours = String(hours).padStart(2, '0');
        
        const timeString = `${formattedHours}:${minutes}:${seconds} ${ampm}`;

        clockElements.forEach(el => {
            el.textContent = timeString;
        });
    }

    // Run immediately and then schedule interval
    updateTime();
    setInterval(updateTime, 1000);
}

/**
 * Sticky Header Interaction
 * Adds a shadow/background scroll class to the header element when the page is scrolled
 */
function initStickyHeader() {
    const header = document.querySelector('.oahms-header');
    if (!header) return;

    window.addEventListener('scroll', () => {
        if (window.scrollY > 10) {
            header.classList.add('oahms-header-scrolled');
        } else {
            header.classList.remove('oahms-header-scrolled');
        }
    });
}

/**
 * Custom Dropdown and Toggle Accessibility Helpers
 * Standard interactions for accessibility or custom behaviors (if needed)
 */
function initDropdownAccessibility() {
    // Helper to log or clean up dropdown behaviors
    const dropdowns = document.querySelectorAll('.dropdown');
    dropdowns.forEach(dropdown => {
        dropdown.addEventListener('show.bs.dropdown', () => {
            // Perform actions when dropdown opens
        });
    });
}

/**
 * Hero Background Slideshow logic
 * Cycles slides every 6 seconds with a smooth fade
 */
function initHeroSlideshow() {
    const slides = document.querySelectorAll(".hero-slide");
    if (slides.length <= 1) return;

    let currentSlide = 0;
    setInterval(() => {
        slides[currentSlide].classList.remove("active");
        currentSlide = (currentSlide + 1) % slides.length;
        slides[currentSlide].classList.add("active");
    }, 6000); // 6 seconds duration
}
