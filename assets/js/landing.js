/**
 * SevaNest Old Age Home Management System
 * Landing Page Custom Interactions
 */

document.addEventListener("DOMContentLoaded", () => {
    /* ==========================================================================
       2. Leaflet Map Widget Setup
       ========================================================================== */
    const mapContainer = document.getElementById("map");
    if (mapContainer) {
        // Initialize map focused on Delhi
        const map = L.map("map", {
            scrollWheelZoom: false
        }).setView([28.6139, 77.2090], 11);

        // Add OpenStreetMap tiles
        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Invalidate map size to ensure correct sizing inside grid layout
        setTimeout(() => {
            map.invalidateSize();
        }, 200);

        // Defined Delhi partner homes markers data
        const homes = [
            {
                name: "Senior Care Home",
                lat: 28.6150,
                lng: 77.2090,
                desc: "Downtown, New Delhi | Government & NGO Support",
                rating: "4.8"
            },
            {
                name: "Harmony Senior Living",
                lat: 28.5272,
                lng: 77.2200,
                desc: "South Delhi | Private & Medical Care",
                rating: "4.9"
            },
            {
                name: "Shantivan Elder Care",
                lat: 28.6300,
                lng: 77.1000,
                desc: "West Delhi | NGO & Wheelchair Accessible",
                rating: "4.7"
            },
            {
                name: "Aarogyam Senior Care",
                lat: 28.6200,
                lng: 77.3000,
                desc: "East Delhi | Private & Medical Care",
                rating: "4.6"
            },
            {
                name: "Caring Hearts Home",
                lat: 28.7000,
                lng: 77.2100,
                desc: "North Delhi | Government & NGO Care",
                rating: "4.8"
            }
        ];

        // Add markers with custom popups
        homes.forEach(home => {
            const popupContent = `
                <div class="map-popup">
                    <h5>${home.name}</h5>
                    <p>${home.desc}</p>
                    <span class="rating"><i class="fas fa-star" style="color:#D4A373;"></i> ${home.rating}</span>
                </div>
            `;
            L.marker([home.lat, home.lng]).addTo(map).bindPopup(popupContent);
        });
    }

    /* ==========================================================================
       3. Interactive Filtering & Search
       ========================================================================== */
    const filterButtons = document.querySelectorAll(".filter-btn");
    const cards = document.querySelectorAll(".results-grid .result-card");
    const searchInput = document.querySelector(".search-input");
    const searchBtn = document.querySelector(".btn-search");

    let activeFilter = "all";
    let activeSearchQuery = "";

    function applyFilters() {
        cards.forEach(card => {
            const cardTags = card.dataset.type.toLowerCase();
            const cardName = card.querySelector("h4").textContent.toLowerCase();
            const cardDesc = card.querySelector(".result-content p") ? card.querySelector(".result-content p").textContent.toLowerCase() : "";

            const matchesCategory = (activeFilter === "all" || cardTags.includes(activeFilter));
            const matchesSearch = (activeSearchQuery === "" || cardName.includes(activeSearchQuery) || cardDesc.includes(activeSearchQuery));

            if (matchesCategory && matchesSearch) {
                card.style.display = "block";
            } else {
                card.style.display = "none";
            }
        });

        // Reset carousel slider translation position
        const grid = document.getElementById("resultsGrid");
        if (grid) {
            grid.scrollTo({ left: 0, behavior: "smooth" });
        }
    }

    filterButtons.forEach(btn => {
        btn.addEventListener("click", () => {
            filterButtons.forEach(b => b.classList.remove("active"));
            btn.classList.add("active");
            activeFilter = btn.dataset.filter.toLowerCase();
            applyFilters();
        });
    });

    if (searchInput) {
        searchInput.addEventListener("input", () => {
            activeSearchQuery = searchInput.value.toLowerCase().trim();
            applyFilters();
        });
    }

    if (searchBtn && searchInput) {
        searchBtn.addEventListener("click", () => {
            activeSearchQuery = searchInput.value.toLowerCase().trim();
            applyFilters();
        });
    }

    /* ==========================================================================
       4. Partner Homes Carousel Slider
       ========================================================================== */
    const resultsGrid = document.getElementById("resultsGrid");
    const prevArrow = document.querySelector(".prev-arrow");
    const nextArrow = document.querySelector(".next-arrow");

    if (resultsGrid && prevArrow && nextArrow) {
        const getScrollVal = () => {
            // Width of one card (320px) + gap (24px)
            return 344;
        };

        nextArrow.addEventListener("click", () => {
            resultsGrid.scrollBy({ left: getScrollVal(), behavior: "smooth" });
        });

        prevArrow.addEventListener("click", () => {
            resultsGrid.scrollBy({ left: -getScrollVal(), behavior: "smooth" });
        });

        const updateArrows = () => {
            const scrollLeft = resultsGrid.scrollLeft;
            const maxScroll = resultsGrid.scrollWidth - resultsGrid.clientWidth;

            prevArrow.disabled = scrollLeft <= 5;
            nextArrow.disabled = scrollLeft >= maxScroll - 5;
        };

        resultsGrid.addEventListener("scroll", updateArrows);
        window.addEventListener("resize", updateArrows);
        setTimeout(updateArrows, 100); // Initial check
    }

    /* ==========================================================================
       5. Animated Stats Counters
       ========================================================================== */
    const counters = document.querySelectorAll(".stat-card .counter");

    const animateCounter = (counter) => {
        const target = parseInt(counter.dataset.target, 10);
        const hasComma = target >= 10000;
        let count = 0;
        const duration = 1500; // 1.5 seconds animation
        const steps = 60;
        const increment = Math.ceil(target / steps);
        const stepTime = duration / steps;

        const timer = setInterval(() => {
            count += increment;
            if (count >= target) {
                counter.textContent = hasComma ? target.toLocaleString() : target;
                clearInterval(timer);
            } else {
                counter.textContent = hasComma ? count.toLocaleString() : count;
            }
        }, stepTime);
    };

    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounter(entry.target);
                counterObserver.unobserve(entry.target); // Animate once only
            }
        });
    }, { threshold: 0.5 });

    counters.forEach(counter => counterObserver.observe(counter));

    /* ==========================================================================
       6. Scroll Progress & Back to Top Button
       ========================================================================== */
    const backToTop = document.getElementById("backToTop");
    const progressFill = document.getElementById("scrollProgress");

    window.addEventListener("scroll", () => {
        const scrollTop = window.scrollY;
        const docHeight = document.documentElement.scrollHeight - window.innerHeight;
        const scrollPercent = docHeight > 0 ? (scrollTop / docHeight) * 100 : 0;

        // Progress bar fill width
        if (progressFill) {
            progressFill.style.width = scrollPercent + "%";
        }

        // Show/hide back to top
        if (backToTop) {
            if (scrollTop > 400) {
                backToTop.classList.add("show");
            } else {
                backToTop.classList.remove("show");
            }
        }
    });

    if (backToTop) {
        backToTop.addEventListener("click", () => {
            window.scrollTo({ top: 0, behavior: "smooth" });
        });
    }

    /* ==========================================================================
       7. Scroll Reveal Fades
       ========================================================================== */
    const revealElements = document.querySelectorAll(".fade-in-left, .reveal, .service-card, .testimonial-card");
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add("show");
                revealObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.15 });

    revealElements.forEach(el => {
        el.classList.add("reveal-hidden");
        revealObserver.observe(el);
    });

    // CSS styling helper for reveal fade-in (in case not in stylesheet)
    const styleNode = document.createElement("style");
    styleNode.innerHTML = `
        .reveal-hidden {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.8s ease, transform 0.8s var(--transition-slow);
        }
        .fade-in-left.reveal-hidden {
            transform: translateX(-40px);
        }
        .reveal-hidden.show {
            opacity: 1;
            transform: translate(0) !important;
        }
    `;
    document.head.appendChild(styleNode);
});
