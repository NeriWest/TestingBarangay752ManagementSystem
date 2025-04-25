<!DOCTYPE html>
<html lang="en">
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Determine if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);

// Determine the correct dashboard URL
$dashboardUrl = 'public/views/login.php'; // Default to login page

if ($isLoggedIn) {
    // Check if login_type exists in session (from the radio button selection)
    if (isset($_SESSION['login_type'])) {
        if ($_SESSION['login_type'] === 'official') {
            // For officials, verify they have official role
            $officialRoles = [1, 2, 3, 5]; // Admin, Chairman, Secretary, etc.
            $dashboardUrl = (isset($_SESSION['role_id']) && in_array($_SESSION['role_id'], $officialRoles))
                ? 'controller/admin/adminDashboardController.php'
                : 'controller/public/views/unauthorized.php'; // or back to login
        } else {
            // For residents, go to resident dashboard
            $dashboardUrl = 'controller/resident/residentDashboardController.php';
        }
    } else {
        // Default fallback if login_type not set
        $dashboardUrl = 'controller/resident/residentDashboardController.php';
    }
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Google Fonts: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Font Awesome for icons -->
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <!-- LINKING -->
    <link rel="stylesheet" href="css/MainStyle.css">



</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-custom navbar-expand-lg" id="mainNavbar">
        <div class="container">
            <!-- Mobile menu button - now in its own container -->
            <div class="navbar-toggler-container">
                <button class="navbar-toggler d-lg-none" type="button" id="mobileMenuButton">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>

            <!-- Desktop Navigation -->
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link-custom" href="index.php">
                            <span>Home</span>
                            <i class="bi bi-chevron-right nav-chevron"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-custom" href="public/views/AboutUsBootstrap.php">
                            <span>About Us</span>
                            <i class="bi bi-chevron-right nav-chevron"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-custom" href="public/views/serviceBootstrap.php">
                            <span>Services</span>
                            <i class="bi bi-chevron-right nav-chevron"></i>
                        </a>
                    </li>
                </ul><button class="btn btn-login">
    <a href="<?php echo $dashboardUrl; ?>">
        <?php echo $isLoggedIn ? 'Account' : 'Log In'; ?>
    </a>
</button>
            </div>
        </div>

        <!-- Mobile Navigation -->
        <div class="container d-lg-none mobile-menu" id="mobileMenu" style="display: none;">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="mobile-nav-link" href="index.php">
                        <span>Home</span>
                        <i class="bi bi-chevron-right ms-auto"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="mobile-nav-link" href="public/views/AboutUsBootstrap.php">
                        <span>About Us</span>
                        <i class="bi bi-chevron-right ms-auto"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="mobile-nav-link" href="public/views/serviceBootstrap.php">
                        <span>Services</span>
                        <i class="bi bi-chevron-right ms-auto"></i>
                    </a>
                </li>
                <li class="nav-item mt-2"><button class="btn btn-login">
    <?php if ($isLoggedIn): ?>
        <?php
        // Determine dashboard based on role_id
        $dashboard = 'resident/residentDashboardController.php'; // Default
        if (isset($_SESSION['role_id'])) {
            $officialRoles = [1, 2, 3, 5]; // Admin(1), Chairman(2), Secretary(3), + role 5
            if (in_array($_SESSION['role_id'], $officialRoles)) {
                $dashboard = 'admin/adminDashboardController.php';
            }
        }
        ?>
        <a href="<?php echo $dashboard; ?>">Account</a>
    <?php else: ?>
        <a href="public/views/login.php">Log In</a>
    <?php endif; ?>
</button>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <!-- Background Image with Overlay -->
        <div class="hero-background">
            <div class="hero-overlay"></div>
        </div>

        <!-- Content Container -->
        <div class="container h-100 hero-content">
            <div class="row h-100 align-items-center py-5">
                <!-- Text Content -->
                <div class="col-md-6 text-center text-md-start text-white mb-5 mb-md-0">
                    <h1 class="hero-title">Welcome to Barangay 752 <br>Zone-81 District V</h1>
                    <p class="hero-subtitle">1200 Espiritu Street, Corner Florentino Torres Street, Singalong, Malate, Manila, Metro Manila</p>
                    <?php if (!isset($_SESSION['role_id'])): ?>
                    <div class="pt-2">
                        <a href="public/views/register.php" class="btn btn-hero-cta">REGISTER NOW!</a>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Logo Container -->
                <div class="col-md-6 d-flex justify-content-center align-items-center">
                    <div class="hero-logo-container">
                        <img src="img/Barangay Logo.png" alt="Barangay 752 Zone-81 Logo" class="hero-logo">
                    </div>
                </div>
            </div>
        </div>

    </section>


 <!-- ANNOUNCEMENTS SECTION -->
 <section class="announcements-section" id="announcements">
        <div class="container">
            <div class="announcements-header">
                <h2>
                    <i data-lucide="megaphone"></i>
                    <span class="text-primary">ANNOUNCEMENTS</span>
                </h2>
                <button id="view-all-btn" class="view-all-btn">
                    View All <i data-lucide="chevron-down" class="chevron-down" width="14" height="14"></i>
                </button>
            </div>

            <div id="announcements-container" class="loading-container">
                <div class="loading-spinner"></div>
            </div>

            <div id="hidden-announcements" class="hidden-announcements"></div>

            <!-- Popup -->
            <div class="announcement-popup" id="announcement-popup">
                <div class="popup-content">
                    <div class="popup-header">
                        <h3 class="popup-title" id="popup-title"></h3>
                        <button class="close-btn" id="close-popup"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="popup-body">
                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                            <span class="announcement-badge" id="popup-badge">
                                <svg class="type-icon" id="popup-type-icon"></svg>
                                <span id="popup-type"></span>
                            </span>
                            <span class="announcement-date">
                                <i data-lucide="calendar" width="12" height="12"></i>
                                <span id="popup-date"></span>
                            </span>
                        </div>
                        <div class="popup-description" id="popup-description"></div>
                    </div>
                    <div class="popup-footer">
                        <button class="btn btn-primary btn-sm" id="popup-close-btn">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SVG Icons for Announcement Types -->
    <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
        <!-- Urgent Icon -->
        <symbol id="urgent-icon" viewBox="0 0 24 24">
            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
            <line x1="12" y1="9" x2="12" y2="13"></line>
            <line x1="12" y1="17" x2="12.01" y2="17"></line>
        </symbol>
        
        <!-- Event Icon -->
        <symbol id="event-icon" viewBox="0 0 24 24">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
            <line x1="16" y1="2" x2="16" y2="6"></line>
            <line x1="8" y1="2" x2="8" y2="6"></line>
            <line x1="3" y1="10" x2="21" y2="10"></line>
        </symbol>
        
        <!-- Notice Icon -->
        <symbol id="notice-icon" viewBox="0 0 24 24">
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
        </symbol>
    </svg>

    <!-- About Us Section -->
    <section class="about-section">
        <div class="container">
            <div class="row align-items-center">
                <!-- About Image -->
                <div class="col-lg-6 mb-4 mb-lg-0" data-aos="fade-right">
                    <img src="img/Background.jpg" alt="Barangay officials" class="about-img">
                </div>

                <!-- About Content -->
                <div class="col-lg-6" data-aos="fade-left">
                    <h2 class="fw-bold mb-4"><span class="text-primary">ABOUT</span> OUR BARANGAY</h2>
                    <p class="text-muted mb-4">
                        We are committed to providing excellent service to our community through transparent governance
                        and efficient management of resources.
                    </p>

                    <ul class="list-unstyled mb-4">
                        <li class="d-flex mb-3">
                            <div class="feature-icon me-3">
                                <i class="bi bi-people-fill"></i>
                            </div>
                            <div>
                                <h5 class="fw-medium">Community-Centered</h5>
                                <p class="text-muted mb-0">Focused on addressing the needs of all residents</p>
                            </div>
                        </li>
                        <li class="d-flex">
                            <div class="feature-icon me-3">
                                <i class="bi bi-house-door-fill"></i>
                            </div>
                            <div>
                                <h5 class="fw-medium">Modern Governance</h5>
                                <p class="text-muted mb-0">Utilizing technology to improve service delivery</p>
                            </div>
                        </li>
                    </ul>

                    <a href="public/views/AboutUsBootstrap.php"
                        class="btn btn-outline-primary d-inline-flex align-items-center" id="services">
                        Learn More About Us
                        <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>




    <!-- Services Section -->
    <section class="services-section py-5">
        <div class="container text-center">
            <div class="section-intro mb-5">
                <h2 class="display-5 fw-bold mt-3 mb-3">YOUR BARANGAY, <span class="text-primary">YOUR SERVICES</span>
                </h2>
                <p class="lead text-muted mx-auto">
                    We bring convenience and accessibility closer to every resident. Explore our digital-first solutions
                    for fast and secure transactions—no more waiting in line, just results.
                </p>
            </div>

            <!-- Services Grid -->
            <div class="row g-4">
                <!-- Service 1: Certificate Issuance -->
                <div class="col-md-6 col-lg-3">
                    <div class="service-card p-4 rounded-4 h-100 shadow-sm bg-white" data-aos="fade-up">
                        <div class="icon-box mb-3 bg-primary bg-gradient text-white mx-auto">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>
                        <h5 class="mb-2">Certificate Issuance</h5>
                        <p class="text-muted">Easily request official documents like residency, indigency, and barangay
                            clearance with minimal effort.</p>
                    </div>
                </div>

                <!-- Service 2: Online Application -->
                <div class="col-md-6 col-lg-3">
                    <div class="service-card p-4 rounded-4 h-100 shadow-sm bg-white" data-aos="fade-up"
                        data-aos-delay="100">
                        <div class="icon-box mb-3 bg-success bg-gradient text-white mx-auto">
                            <i class="bi bi-laptop"></i>
                        </div>
                        <h5 class="mb-2">Online Application</h5>
                        <p class="text-muted">Save time by submitting your requests or forms online—safe, secure, and
                            efficient for everyone.</p>
                    </div>
                </div>

                <!-- Service 3: Blotter Reports -->
                <div class="col-md-6 col-lg-3">
                    <div class="service-card p-4 rounded-4 h-100 shadow-sm bg-white" data-aos="fade-up"
                        data-aos-delay="200">
                        <div class="icon-box mb-3 bg-danger bg-gradient text-white mx-auto">
                            <i class="bi bi-shield-exclamation"></i>
                        </div>
                        <h5 class="mb-2">Incident Reports</h5>
                        <p class="text-muted">Report an incident to have it officially documented and addressed by the
                            barangay.</p>
                    </div>
                </div>

                <!-- Service 4: Complaints -->
                <div class="col-md-6 col-lg-3">
                    <div class="service-card p-4 rounded-4 h-100 shadow-sm bg-white" data-aos="fade-up"
                        data-aos-delay="300">
                        <div class="icon-box mb-3 bg-warning bg-gradient text-white mx-auto">
                            <i class="bi bi-megaphone"></i>
                        </div>
                        <h5 class="mb-2">Complaints</h5>
                        <p class="text-muted">We listen. Voice your concerns, report disturbances, or suggest
                            improvements anytime.</p>
                    </div>
                </div>
            </div>

            <!-- CTA Button -->
            <div class="cta-btn">
                <a href="public/views/serviceBootstrap.php" class="btn btn-lg btn-outline-primary mt-5 px-4">
                    Explore Full Services <i class="bi bi-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Modern Organization Section -->
    <section class="org-section">
        <div class="container">
            <div class="section-intro animate__animated animate__fadeIn">
                <span class="badge">BARANGAY OFFICIALS</span>
                <h2 class="display-5 fw-bold mt-3 mb-3">COUNCIL <span>LEADERSHIP TEAM</span></h2>
                <p class="lead text-muted mx-auto">
                    Meet the dedicated public servants working for the progress and welfare of our community
                </p>
            </div>

            <div class="council-grid" id="councilGrid">
                <!-- Members will be added dynamically -->
            </div>
        </div>
    </section>


    <!-- Footer Section -->
    <footer class="footer">
        <div class="container">
            <div class="row g-4 position-relative">
                <!-- Location Section -->
                <div class="col-md-6 col-lg-3">
                    <h3 class="footer-title">Our Location</h3>
                    <div class="footer-map">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d7723.078039433515!2d120.999401!3d14.568334!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397c99d33cee621%3A0xb5e2f10c0095d856!2s1194%20Florentino%20Torres%2C%20San%20Andres%20Bukid%2C%20Manila%2C%20Metro%20Manila!5e0!3m2!1sen!2sph!4v1743304696194!5m2!1sen!2sph"
                            allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                            title="Barangay Location"></iframe>
                    </div>
                    <div class="footer-contact-item">
                        <i class="bi bi-geo-alt footer-icon"></i>
                        <span>1200 Espiritu Street, Corner Florentino Torres Street, Sungalong, Malate, Manila, Metro Manila</span>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="col-md-6 col-lg-3">
                    <h3 class="footer-title">Contact Us</h3>
                    <div class="footer-contact-item">
                        <i class="bi bi-envelope footer-icon"></i>
                        <a href="mailto:barangay752@example.com" class="footer-link">barangay752@example.com</a>
                    </div>
                    <div class="footer-contact-item">
                        <i class="bi bi-telephone footer-icon"></i>
                        <a href="tel:+63281234567" class="footer-link">+63 9999 9999</a>
                    </div>

                    <h3 class="footer-title mt-4">Office Hours</h3>
                    <div class="footer-hours-item">
                        <i class="bi bi-clock footer-icon"></i>
                        <span>Monday - Friday: 8:00 AM - 5:00 PM</span>
                    </div>
                    <div class="footer-hours-item">
                        <i class="bi bi-clock footer-icon"></i>
                        <span>Saturday: 8:00 AM - 12:00 PM</span>
                    </div>
                    <div class="footer-hours-item">
                        <i class="bi bi-clock footer-icon"></i>
                        <span>Sunday: Closed</span>
                    </div>
                </div>

                <!-- Quick Links and Social Media -->
                <div class="col-md-6 col-lg-3">
                    <h3 class="footer-title">Quick Links</h3>
                    <ul class="footer-quick-links">
                        <li><a href="index.php" class="footer-link">Home</a></li>
                        <li><a href="public/views/AboutUsBootstrap.php" class="footer-link">About Us</a></li>
                        <li><a href="public/views/serviceBootstrap.php" class="footer-link">Services</a></li>
                    </ul>

                    <h3 class="footer-title">Connect With Us</h3>
                    <div class="social-links">
                        <a href="https://www.facebook.com/brgy752" class="social-link" target="_blank"
                            rel="noopener noreferrer">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="#" class="social-link" target="_blank" rel="noopener noreferrer">
                            <i class="bi bi-twitter"></i>
                        </a>
                        <a href="#" class="social-link" target="_blank" rel="noopener noreferrer">
                            <i class="bi bi-instagram"></i>
                        </a>
                    </div>
                </div>

                <!-- Logo Section -->
                <div class="footer-logo-container">
                    <div class="footer-logos">
                        <img src="img/lunsgodNgMayNilaLogo.png" class="footer-logo-img"
                            alt="Seal of Lungsod ng Maynila">
                        <img src="img/Barangay Logo.png" class="footer-logo-img" alt="Barangay Logo">
                    </div>
                </div>
            </div>

            <!-- Divider -->
            <div class="footer-divider"></div>

            <!-- Copyright -->
            <div class="footer-copyright">
                <p>© <span id="copyrightYear"></span> BARANGAY 752 ZONE-81. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button id="backToTop" class="back-to-top" aria-label="Back to Top">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- JS Linking  -->
    <script src="js/back-to-top.js"></script>
    <script src="js/MainStyle.js"></script>
    <script src="js/aboutus.js"></script>
    <script>
        // Initialize Lucide icons
        lucide.createIcons();
    </script>
</body>

</html>