<?php
/**
 * SevaNest — Application Root Public Landing Page
 */

require_once 'includes/functions.php';
require_once 'includes/session.php';

$base_path = '';
$body_class = 'landing-body';
$page_title = 'SevaNest — Empowering Elder Care Through Compassion';
$extra_css = [
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
    'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css',
    'assets/css/landing.css'
];

// Define flag for landing page navigation menu in header
$is_landing_page = true;

require_once 'includes/header.php';
?>

<!-- Background Decorations -->
<div class="bg-decorations">
    <div class="bg-shape shape-1"></div>
    <div class="bg-shape shape-2"></div>
    <div class="bg-shape shape-3"></div>
    <div class="bg-shape shape-4"></div>
</div>

<!-- ============ HERO SECTION ============ -->
<section class="hero" id="home">
    <!-- Cinematic Background Slideshow -->
    <div class="hero-bg-slideshow" aria-hidden="true">
        <div class="hero-overlay"></div>
        <img src="assets/images/landing/slide-1.jpg" class="hero-slide active" alt="" />
        <img src="assets/images/landing/slide-2.jpg" class="hero-slide" alt="" loading="lazy" />
        <img src="assets/images/landing/slide-3.jpg" class="hero-slide" alt="" loading="lazy" />
        <img src="assets/images/landing/slide-4.jpg" class="hero-slide" alt="" loading="lazy" />
    </div>
    
    <div class="container">
        <div class="hero-content">
            <div class="hero-left">
                <h1>Empowering Elder Care Through Compassion & Technology</h1>
                <p>Supporting senior citizens with secure healthcare management, transparent donations, volunteer engagement, and seamless old age home administration.</p>
                
                <div class="hero-buttons">
                    <a href="#nearby" class="btn-primary large">
                        <i class="fas fa-home"></i> Explore Homes
                    </a>
                    <a href="#donate" class="btn-secondary large">
                        <i class="fas fa-heart"></i> Donate Now
                    </a>
                </div>

                <div class="hero-benefits">
                    <div class="benefit-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Trusted Platform</span>
                    </div>
                    <div class="benefit-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Verified Homes</span>
                    </div>
                    <div class="benefit-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Secure Management</span>
                    </div>
                </div>
            </div>

            <div class="hero-right">
                <div class="dashboard-mockup">
                    <div class="mockup-header">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                    <div class="mockup-content">
                        <div class="mockup-stat">
                            <div class="stat-icon"><i class="fas fa-users"></i></div>
                            <div class="stat-text">
                                <div class="stat-number">245</div>
                                <div class="stat-label">Residents</div>
                            </div>
                        </div>
                        <div class="mockup-stat">
                            <div class="stat-icon" style="background-color: #C9A86A;"><i class="fas fa-heart"></i></div>
                            <div class="stat-text">
                                <div class="stat-number">₹25L</div>
                                <div class="stat-label">Donations</div>
                            </div>
                        </div>
                        <div class="mockup-stat">
                            <div class="stat-icon" style="background-color: #6B8E4E;"><i class="fas fa-stethoscope"></i></div>
                            <div class="stat-text">
                                <div class="stat-number">512</div>
                                <div class="stat-label">Doctor Visits</div>
                            </div>
                        </div>
                        <div class="mockup-stat">
                            <div class="stat-icon" style="background-color: #8FAE8B;"><i class="fas fa-exclamation-triangle"></i></div>
                            <div class="stat-text">
                                <div class="stat-number">0</div>
                                <div class="stat-label">Alerts</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============ STATISTICS SECTION ============ -->
<section class="statistics">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number counter" data-target="50">0</div>
                <div class="stat-label">Partner Homes</div>
            </div>
            <div class="stat-card">
                <div class="stat-number counter" data-target="10000">0</div>
                <div class="stat-label">Residents Supported</div>
            </div>
            <div class="stat-card">
                <div class="stat-number counter" data-target="500">0</div>
                <div class="stat-label">Doctor Visits</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">₹25L+</div>
                <div class="stat-label">Donations Managed</div>
            </div>
        </div>
    </div>
</section>

<!-- ============ ABOUT SECTION ============ -->
<section class="about" id="about">
    <div class="container">
        <div class="about-content">
            <div class="about-image">
                <img src="assets/images/landing/about-elderly.jpg" alt="Elderly care and support" class="about-img fade-in-left" style="object-fit: cover;">
            </div>
            <div class="about-text">
                <h2>About SevaNest</h2>
                <p>SevaNest is a digital Old Age Home Management System that streamlines resident care, medical management, donations, volunteer coordination, and communication between administrators, families, and healthcare professionals.</p>
                <p>We believe that every senior citizen deserves dignity, compassionate care, and access to quality healthcare. Our platform connects stakeholders—administrators, families, donors, and volunteers—to create a comprehensive ecosystem of support.</p>
                <a href="#" class="btn-primary">Learn More</a>
            </div>
        </div>
    </div>
</section>

<!-- ============ MISSION & VISION ============ -->
<section class="mission-vision">
    <div class="container">
        <div class="mv-grid">
            <div class="mv-card">
                <div class="mv-icon"><i class="fas fa-bullseye"></i></div>
                <h3>Our Mission</h3>
                <p>Deliver compassionate elder care through technology, transparency, and community support.</p>
            </div>
            <div class="mv-card">
                <div class="mv-icon"><i class="fas fa-lightbulb"></i></div>
                <h3>Our Vision</h3>
                <p>Build a connected ecosystem where every senior citizen enjoys dignity, healthcare, and happiness.</p>
            </div>
        </div>
    </div>
</section>

<!-- ============ SERVICES SECTION ============ -->
<section class="services" id="services">
    <div class="container">
        <div class="section-title">
            <h2>Resident Care Services</h2>
            <p>Comprehensive solutions for modern elderly care management</p>
        </div>
        
        <div class="services-grid">
            <div class="service-card">
                <div class="service-icon"><i class="fas fa-users"></i></div>
                <h3>Resident Management</h3>
                <p>Track resident profiles, health records, and daily activities with ease.</p>
            </div>
            <div class="service-card">
                <div class="service-icon"><i class="fas fa-stethoscope"></i></div>
                <h3>Doctor Visit Scheduling</h3>
                <p>Manage medical appointments and specialist consultations seamlessly.</p>
            </div>
            <div class="service-card">
                <div class="service-icon"><i class="fas fa-map-location-dot"></i></div>
                <h3>Nearby Old Age Homes</h3>
                <p>Find and compare verified facilities with detailed information and reviews.</p>
            </div>
            <div class="service-card">
                <div class="service-icon"><i class="fas fa-heart"></i></div>
                <h3>Donation Management</h3>
                <p>Transparent donation tracking with real-time impact visibility.</p>
            </div>
            <div class="service-card">
                <div class="service-icon"><i class="fas fa-handshake"></i></div>
                <h3>Volunteer Coordination</h3>
                <p>Connect passionate volunteers with meaningful opportunities.</p>
            </div>
            <div class="service-card">
                <div class="service-icon"><i class="fas fa-chart-line"></i></div>
                <h3>Dashboard & Reports</h3>
                <p>Detailed analytics and insights for better decision making.</p>
            </div>
        </div>
    </div>
</section>

<!-- ============ NEARBY HOMES SECTION ============ -->
<section class="nearby-homes" id="nearby">
    <div class="container">
        <div class="section-title">
            <h2>Find Nearby Old Age Homes</h2>
            <p>Discover verified and trusted facilities in your area</p>
        </div>
        <div class="nearby-grid">
            <div class="nearby-left">
                <div class="search-section">
                    <div class="search-box">
                        <input type="text" placeholder="Search by city..." class="search-input">
                        <button class="btn-search"><i class="fas fa-search"></i></button>
                    </div>

                    <div class="filters">
                        <button class="filter-btn active" data-filter="all">All</button>
                        <button class="filter-btn" data-filter="government">Government</button>
                        <button class="filter-btn" data-filter="private">Private</button>
                        <button class="filter-btn" data-filter="medical">Medical Facility</button>
                        <button class="filter-btn" data-filter="ngo">NGO</button>
                        <button class="filter-btn" data-filter="accessible">Accessible</button>
                    </div>
                </div>

                <!-- Sample Results -->
                <div class="slider-wrapper">
                    <button class="slider-arrow prev-arrow" aria-label="Previous homes" disabled>
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <div class="results-grid" id="resultsGrid">
                        <div class="result-card" data-type="government ngo">
                            <div class="result-image">
                                <img src="assets/images/landing/home1.jpg" alt="Senior Care Home" loading="lazy">
                                <span class="result-badge">Government</span>
                            </div>
                            <div class="result-content">
                                <h4>Senior Care Home</h4>
                                <div class="result-rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                    <span>4.5</span>
                                </div>
                                <p class="result-location"><i class="fas fa-map-marker-alt"></i> Downtown, New Delhi</p>
                                <div class="result-features">
                                    <span><i class="fas fa-check"></i> Medical Facility</span>
                                    <span><i class="fas fa-check"></i> 24/7 Staff</span>
                                </div>
                                <a href="#" class="view-details-btn">View Details</a>
                            </div>
                        </div>

                        <div class="result-card" data-type="private medical">
                            <div class="result-image">
                                <img src="assets/images/landing/home2.jpg" alt="Golden Years Haven" loading="lazy">
                                <span class="result-badge" style="background-color: #557064;">Private</span>
                            </div>
                            <div class="result-content">
                                <h4>Golden Years Haven</h4>
                                <div class="result-rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <span>5.0</span>
                                </div>
                                <p class="result-location"><i class="fas fa-map-marker-alt"></i> South Delhi</p>
                                <div class="result-features">
                                    <span><i class="fas fa-check"></i> Medical Facility</span>
                                    <span><i class="fas fa-check"></i> WiFi Available</span>
                                </div>
                                <a href="#" class="view-details-btn">View Details</a>
                            </div>
                        </div>

                        <div class="result-card" data-type="ngo accessible">
                            <div class="result-image">
                                <img src="assets/images/landing/home3.jpg" alt="Compassionate Hearts" loading="lazy">
                                <span class="result-badge" style="background-color: #D4A373;">NGO</span>
                            </div>
                            <div class="result-content">
                                <h4>Compassionate Hearts</h4>
                                <div class="result-rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                    <span>4.8</span>
                                </div>
                                <p class="result-location"><i class="fas fa-map-marker-alt"></i> West Delhi</p>
                                <div class="result-features">
                                    <span><i class="fas fa-check"></i> Wheelchair Accessible</span>
                                    <span><i class="fas fa-check"></i> Yoga & Wellness</span>
                                </div>
                                <a href="#" class="view-details-btn">View Details</a>
                            </div>
                        </div>

                        <div class="result-card" data-type="private medical accessible">
                            <div class="result-image">
                                <img src="assets/images/landing/home4.jpg" alt="Harmony Senior Living" loading="lazy">
                                <span class="result-badge" style="background-color: #557064;">Private</span>
                            </div>
                            <div class="result-content">
                                <h4>Harmony Senior Living</h4>
                                <div class="result-rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <span>4.9</span>
                                </div>
                                <p class="result-location"><i class="fas fa-map-marker-alt"></i> East Delhi</p>
                                <div class="result-features">
                                    <span><i class="fas fa-check"></i> Physiotherapy</span>
                                    <span><i class="fas fa-check"></i> Wheelchair Accessible</span>
                                </div>
                                <a href="#" class="view-details-btn">View Details</a>
                            </div>
                        </div>

                        <div class="result-card" data-type="government ngo">
                            <div class="result-image">
                                <img src="assets/images/landing/home5.jpg" alt="Shantivan Elder Care" loading="lazy">
                                <span class="result-badge" style="background-color: #D4A373;">NGO</span>
                            </div>
                            <div class="result-content">
                                <h4>Shantivan Elder Care</h4>
                                <div class="result-rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="far fa-star"></i>
                                    <span>4.2</span>
                                </div>
                                <p class="result-location"><i class="fas fa-map-marker-alt"></i> North Delhi</p>
                                <div class="result-features">
                                    <span><i class="fas fa-check"></i> Green Courtyard</span>
                                    <span><i class="fas fa-check"></i> Library Access</span>
                                </div>
                                <a href="#" class="view-details-btn">View Details</a>
                            </div>
                        </div>
                    </div>
                    <button class="slider-arrow next-arrow" aria-label="Next homes">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>

            <!-- Right Column: Map -->
            <div class="nearby-right">
                <div class="map-container">
                    <div id="map" class="map-placeholder"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============ DOCTOR VISITS SECTION ============ -->
<section class="doctor-visits">
    <div class="container">
        <div class="section-title">
            <h2>Upcoming Doctor Visits</h2>
            <p>Schedule and track medical consultations</p>
        </div>

        <div class="timeline">
            <div class="timeline-item">
                <div class="timeline-date">25 July</div>
                <div class="timeline-content">
                    <h4><i class="fas fa-user-md"></i> General Physician</h4>
                    <p>Regular health checkup and vitals monitoring</p>
                </div>
            </div>

            <div class="timeline-item">
                <div class="timeline-date">28 July</div>
                <div class="timeline-content">
                    <h4><i class="fas fa-eye"></i> Eye Specialist</h4>
                    <p>Vision assessment and eyeglass prescription</p>
                </div>
            </div>

            <div class="timeline-item">
                <div class="timeline-date">30 July</div>
                <div class="timeline-content">
                    <h4><i class="fas fa-tooth"></i> Dental Camp</h4>
                    <p>Free dental screening and treatment</p>
                </div>
            </div>

            <div class="timeline-item">
                <div class="timeline-date">3 August</div>
                <div class="timeline-content">
                    <h4><i class="fas fa-heart"></i> Health Awareness Camp</h4>
                    <p>Nutrition and wellness education session</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============ DONATION SECTION ============ -->
<section class="donation" id="donate">
    <div class="container">
        <div class="donation-content">
            <div class="donation-left">
                <img src="assets/images/landing/donation-hands.jpg" alt="Your Donations Make a Difference" class="donation-img">
            </div>

            <div class="donation-right text-start">
                <h2>Support Our Mission</h2>
                <p>Transparent donation tracking for medical equipment, nutrition, medicines, and facilities.</p>

                <div class="progress-items">
                    <div class="progress-item">
                        <div class="progress-label">
                            <span>Medical Equipment</span>
                            <span class="progress-percentage">72%</span>
                        </div>
                        <div class="progress-bar-shell">
                            <div class="progress-bar-fill" style="width: 72%"></div>
                        </div>
                    </div>

                    <div class="progress-item mt-3">
                        <div class="progress-label">
                            <span>Nutrition & Food</span>
                            <span class="progress-percentage">65%</span>
                        </div>
                        <div class="progress-bar-shell">
                            <div class="progress-bar-fill" style="width: 65%"></div>
                        </div>
                    </div>

                    <div class="progress-item mt-3">
                        <div class="progress-label">
                            <span>Medicines & Healthcare</span>
                            <span class="progress-percentage">100%</span>
                        </div>
                        <div class="progress-bar-shell">
                            <div class="progress-bar-fill" style="width: 100%"></div>
                        </div>
                    </div>
                </div>

                <div class="donation-buttons mt-4">
                    <a href="#" class="btn-primary">Donate Now</a>
                    <a href="#" class="btn-secondary">View Transparency Report</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============ VOLUNTEER SECTION ============ -->
<section class="volunteer" id="volunteer">
    <div class="container">
        <div class="section-title">
            <h2>Become a Volunteer</h2>
            <p>Make a difference in the lives of senior citizens</p>
        </div>

        <div class="volunteer-grid">
            <div>
                <div class="volunteer-img">
                    <img src="assets/images/landing/volunteer-group.jpg" alt="Volunteers" class="img-fluid">
                </div>
                <div class="volunteer-text text-start">
                    <p>Our volunteers bring joy, companionship, and support to the residents of SevaNest partner homes. Whether you want to share a skill, read books, or just have a warm conversation, your time is invaluable.</p>
                </div>
            </div>

            <form class="volunteer-form text-start" id="volunteerForm">
                <div class="form-group">
                    <label for="vol-name">Full Name</label>
                    <input type="text" id="vol-name" placeholder="Your name" required>
                </div>

                <div class="form-group">
                    <label for="vol-email">Email Address</label>
                    <input type="email" id="vol-email" placeholder="your@email.com" required>
                </div>

                <div class="form-group">
                    <label for="vol-phone">Phone Number</label>
                    <input type="tel" id="vol-phone" placeholder="+91 XXXXX XXXXX" required>
                </div>

                <div class="form-group">
                    <label for="vol-area">Preferred Area</label>
                    <input type="text" id="vol-area" placeholder="e.g., South Delhi" required>
                </div>

                <div class="form-group">
                    <label for="vol-availability">Availability</label>
                    <select id="vol-availability" required>
                        <option value="">Select availability...</option>
                        <option value="weekday">Weekdays</option>
                        <option value="weekend">Weekends</option>
                        <option value="flexible">Flexible</option>
                    </select>
                </div>

                <button type="submit" class="submit-btn">Register as Volunteer</button>
            </form>
        </div>
    </div>
</section>

<!-- ============ VISITOR INFORMATION SECTION ============ -->
<section class="visitor-info">
    <div class="container">
        <div class="section-title">
            <h2>Visitor Information</h2>
            <p>Plan your visit to a senior citizen</p>
        </div>

        <div class="services-grid">
            <div class="service-card text-center">
                <div class="service-icon mx-auto"><i class="fas fa-clock"></i></div>
                <h3>Visiting Hours</h3>
                <p>Monday – Saturday</p>
                <p class="fw-bold text-success fs-5">10 AM – 5 PM</p>
            </div>

            <div class="service-card text-center">
                <div class="service-icon mx-auto"><i class="fas fa-list"></i></div>
                <h3>Visitor Guidelines</h3>
                <p>Carry valid photo identification card.</p>
                <p>Respect personal space and comfort of seniors.</p>
            </div>

            <div class="service-card text-center">
                <div class="service-icon mx-auto"><i class="fas fa-calendar"></i></div>
                <h3>Book a Visit</h3>
                <p>Schedule your visit in advance for better coordination.</p>
                <a href="#" class="view-details-btn mt-2">Schedule Visit</a>
            </div>
        </div>
    </div>
</section>

<!-- ============ TESTIMONIALS SECTION ============ -->
<section class="testimonials">
    <div class="container">
        <div class="section-title">
            <h2>What People Say</h2>
            <p>Real stories from our community</p>
        </div>

        <div class="testimonials-grid">
            <div class="testimonial-card">
                <i class="fas fa-quote-right quote-icon"></i>
                <p class="testimonial-text">"Managing residents has never been easier. The system is intuitive and saves us hours of paperwork every week."</p>
                <div class="testimonial-user">
                    <div class="user-avatar">RK</div>
                    <div class="user-info">
                        <h5>Rajesh Kumar</h5>
                        <span>Administrator</span>
                    </div>
                </div>
            </div>

            <div class="testimonial-card">
                <i class="fas fa-quote-right quote-icon"></i>
                <p class="testimonial-text">"The donation tracking system is completely transparent. I know exactly where my contribution goes and the impact it creates."</p>
                <div class="testimonial-user">
                    <div class="user-avatar">PS</div>
                    <div class="user-info">
                        <h5>Priya Sharma</h5>
                        <span>Donor</span>
                    </div>
                </div>
            </div>

            <div class="testimonial-card">
                <i class="fas fa-quote-right quote-icon"></i>
                <p class="testimonial-text">"I found a trusted old age home for my grandmother within minutes. The detailed information and reviews were incredibly helpful."</p>
                <div class="testimonial-user">
                    <div class="user-avatar">AS</div>
                    <div class="user-info">
                        <h5>Amar Singh</h5>
                        <span>Family Member</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============ CONTACT SECTION ============ -->
<section class="contact" id="contact">
    <div class="container">
        <div class="section-title">
            <h2>Get In Touch</h2>
            <p>We'd love to hear from you</p>
        </div>

        <div class="contact-grid">
            <div class="contact-info text-start">
                <div class="contact-info-card">
                    <div class="contact-icon"><i class="fas fa-map-marker-alt"></i></div>
                    <div class="contact-details">
                        <h4>Address</h4>
                        <p>123 Healthcare Street, New Delhi, India</p>
                    </div>
                </div>

                <div class="contact-info-card">
                    <div class="contact-icon"><i class="fas fa-phone"></i></div>
                    <div class="contact-details">
                        <h4>Phone</h4>
                        <p><a href="tel:+911112345678">+91 (11) 1234-5678</a></p>
                    </div>
                </div>

                <div class="contact-info-card">
                    <div class="contact-icon"><i class="fas fa-envelope"></i></div>
                    <div class="contact-details">
                        <h4>Email</h4>
                        <p><a href="mailto:support@sevanest.com">support@sevanest.com</a></p>
                    </div>
                </div>

                <div class="contact-info-card">
                    <div class="contact-icon"><i class="fas fa-globe"></i></div>
                    <div class="contact-details">
                        <h4>Website</h4>
                        <p><a href="https://www.sevanest.com" target="_blank">www.sevanest.com</a></p>
                    </div>
                </div>
            </div>

            <div class="contact-right">
                <form class="contact-form text-start" id="contactForm">
                    <div class="form-group">
                        <label for="con-name">Full Name</label>
                        <input type="text" id="con-name" placeholder="Your name" required>
                    </div>

                    <div class="form-group">
                        <label for="con-email">Email Address</label>
                        <input type="email" id="con-email" placeholder="your@email.com" required>
                    </div>

                    <div class="form-group">
                        <label for="con-subject">Subject</label>
                        <input type="text" id="con-subject" placeholder="Message subject" required>
                    </div>

                    <div class="form-group">
                        <label for="con-message">Message</label>
                        <textarea id="con-message" placeholder="Your message..." rows="5" required></textarea>
                    </div>

                    <button type="submit" class="submit-btn">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- ============ CTA SECTION ============ -->
<section class="cta-section" id="get-started">
    <div class="container">
        <div class="cta-content">
            <h2>Ready to Make a Difference?</h2>
            <p>Join SevaNest today and help create a better future for senior citizens.</p>
            <a href="#nearby" class="btn-secondary large">Find Homes</a>
        </div>
    </div>
</section>

<!-- ============ BACK TO TOP BUTTON ============ -->
<button class="back-to-top" id="backToTop" title="Back to top">
    <i class="fas fa-arrow-up"></i>
</button>

<!-- Leaflet JS CDN and Custom scripts -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script src="assets/js/landing.js"></script>

<?php
require_once 'includes/footer.php';
?>
