// ================================
// Smooth Scroll for Navigation
// ================================

document.querySelectorAll('a[href^="#"]').forEach(anchor => {

    anchor.addEventListener('click', function (e) {

        e.preventDefault();

        const target = document.querySelector(this.getAttribute('href'));

        if (target) {

            target.scrollIntoView({

                behavior: 'smooth'

            });

        }

    });

});

// ================================
// Sticky Header Shadow
// ================================

const header = document.querySelector(".header");

window.addEventListener("scroll", () => {

    if (window.scrollY > 50) {

        header.style.boxShadow = "0 10px 30px rgba(0,0,0,0.15)";

    } else {

        header.style.boxShadow = "0 5px 20px rgba(0,0,0,0.08)";

    }

});

// ================================
// Statistics Counter Animation
// ================================

const counters = document.querySelectorAll(".stat-card h2");

const speed = 200;

counters.forEach(counter => {

    const updateCount = () => {

        const target = parseInt(counter.innerText);

        const count = parseInt(counter.getAttribute("data-count")) || 0;

        const increment = Math.ceil(target / speed);

        if (count < target) {

            counter.setAttribute("data-count", count + increment);

            counter.innerText = Math.min(count + increment, target) + "+";

            setTimeout(updateCount, 10);

        }

    };

    updateCount();

});

// ================================
// Fade-in Animation on Scroll
// ================================

const observer = new IntersectionObserver((entries) => {

    entries.forEach(entry => {

        if (entry.isIntersecting) {

            entry.target.classList.add("show");

        }

    });

}, {

    threshold: 0.15

});

document.querySelectorAll("section").forEach(section => {

    section.classList.add("hidden");

    observer.observe(section);

});
const topBtn = document.getElementById("topBtn");

window.addEventListener("scroll", () => {
    if (window.scrollY > 300) {
        topBtn.style.display = "block";
    } else {
        topBtn.style.display = "none";
    }
});

topBtn.addEventListener("click", () => {
    window.scrollTo({
        top: 0,
        behavior: "smooth"
    });
});