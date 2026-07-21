/*=========================================================
    SUPER ADMIN DASHBOARD
    Old Age Home Management System
=========================================================*/

document.addEventListener("DOMContentLoaded", () => {

    showCurrentDate();

    animateCounters();

    initializeMenu();

    initializeQuickCards();

    initializeScrollAnimation();

});


/*=========================================================
    CURRENT DATE
=========================================================*/

function showCurrentDate() {

    const dateElement = document.getElementById("currentDate");

    if (!dateElement) return;

    const options = {
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric"
    };

    const today = new Date();

    dateElement.textContent = today.toLocaleDateString("en-IN", options);

}


/*=========================================================
    COUNTER ANIMATION
=========================================================*/

function animateCounters() {

    const counters = document.querySelectorAll(".stat-card h2");

    counters.forEach(counter => {

        let text = counter.innerText;

        // Skip donation amount (₹4.2L etc.)
        if (text.includes("₹")) return;

        let target = parseInt(text.replace(/,/g, ""));

        if (isNaN(target)) return;

        let current = 0;

        let increment = Math.max(1, Math.ceil(target / 60));

        const update = () => {

            current += increment;

            if (current >= target) {

                counter.innerText = target;

                return;

            }

            counter.innerText = current;

            requestAnimationFrame(update);

        };

        update();

    });

}


/*=========================================================
    SIDEBAR ACTIVE MENU
=========================================================*/

function initializeMenu() {

    const menuItems = document.querySelectorAll(".menu li");

    menuItems.forEach(item => {

        item.addEventListener("click", () => {

            menuItems.forEach(menu => {

                menu.classList.remove("active");

            });

            item.classList.add("active");

        });

    });

}


/*=========================================================
    QUICK ACCESS CLICK EFFECT
=========================================================*/

function initializeQuickCards() {

    const cards = document.querySelectorAll(".quick-card");

    cards.forEach(card => {

        card.addEventListener("mouseenter", () => {

            card.style.transform = "translateY(-8px)";

        });

        card.addEventListener("mouseleave", () => {

            card.style.transform = "";

        });

        card.addEventListener("click", () => {

            const title = card.querySelector("h3").innerText;

            console.log(title + " clicked");

            // Future:
            // window.location.href = "user_management.php";
        });

    });

}


/*=========================================================
    SCROLL REVEAL ANIMATION
=========================================================*/

function initializeScrollAnimation() {

    const elements = document.querySelectorAll(

        ".stat-card, .quick-card, .activity-panel, .overview-panel"

    );

    const observer = new IntersectionObserver(entries => {

        entries.forEach(entry => {

            if (entry.isIntersecting) {

                entry.target.style.opacity = "1";

                entry.target.style.transform = "translateY(0)";

            }

        });

    }, {

        threshold: 0.15

    });

    elements.forEach(element => {

        element.style.opacity = "0";

        element.style.transform = "translateY(25px)";

        element.style.transition = "all .6s ease";

        observer.observe(element);

    });

}


/*=========================================================
    OPTIONAL FUTURE FUNCTIONS
=========================================================*/

/*

Example backend integration:

fetch("dashboard_api.php")

.then(response => response.json())

.then(data => {

    document.getElementById("residentCount").innerHTML = data.residents;

    document.getElementById("staffCount").innerHTML = data.staff;

    document.getElementById("donationCount").innerHTML = data.donations;

});

*/