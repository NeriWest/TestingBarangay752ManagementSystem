<!DOCTYPE html>
<html lang="en">
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Determine if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);

// Determine the correct dashboard URL
$dashboardUrl = 'login.php'; // Default to login page

if ($isLoggedIn) {
    // Check if login_type exists in session (from the radio button selection)
    if (isset($_SESSION['login_type'])) {
        if ($_SESSION['login_type'] === 'official') {
            // For officials, verify they have official role
            $officialRoles = [1, 2, 3, 5]; // Admin, Chairman, Secretary, etc.
            $dashboardUrl = (isset($_SESSION['role_id']) && in_array($_SESSION['role_id'], $officialRoles))
                ? '../../controller/admin/adminDashboardController.php'
                : '../../controller/public/views/unauthorized.php'; // or back to login
        } else {
            // For residents, go to resident dashboard
            $dashboardUrl = '../../controller/resident/residentDashboardController.php';
        }
    } else {
        // Default fallback if login_type not set
        $dashboardUrl = '../../controller/resident/residentDashboardController.php';
    }
}
?>


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay Services</title>
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

    <!-- LINKINGS -->
    <link rel="stylesheet" href="../../css/services.css">    <link rel="stylesheet" href="../../css/aboutUs.css">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-custom navbar-expand-lg" id="mainNavbar">
        <div class="container">
            <!-- Logo -->
            <div class="navbar-brand-custom" href="#">
                <img src="../../img/Barangay Logo.png" alt="Barangay 752 Logo" class="navbar-logo">
                <span class="navbar-title">BARANGAY 752 ZONE-81</span>
            </div>

            <!-- Mobile menu button -->
            <button class="navbar-toggler d-lg-none" type="button" id="mobileMenuButton">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Desktop Navigation -->
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link-custom" href="../../index.php">
                            <span>Home</span>
                            <i class="bi bi-chevron-right nav-chevron"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-custom" href="AboutUsBootstrap.php">
                            <span>About Us</span>
                            <i class="bi bi-chevron-right nav-chevron"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-custom" href="serviceBootstrap.php">
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
                    <a class="mobile-nav-link" href="../../index.php">
                        <span>Home</span>
                        <i class="bi bi-chevron-right ms-auto"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="mobile-nav-link" href="AboutUsBootstrap.php">
                        <span>About Us</span>
                        <i class="bi bi-chevron-right ms-auto"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="mobile-nav-link" href="serviceBootstrap.php">
                        <span>Services</span>
                        <i class="bi bi-chevron-right ms-auto"></i>
                    </a>
                </li>
                <li class="nav-item mt-2">
                    <a href="login.php" class="btn login-btn w-100">Log In</a>
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
        <div class="container h-100">
            <div class="hero-content">
                <h1 class="hero-title">Our Barangay Services</h1>
                <p class="hero-subtitle">Discover the various services we offer to assist residents with their needs. Apply online or visit our office for assistance.</p>
            </div>
        </div>
    
    </section>

    <!-- Services Section -->
    <section class="services-section" id="services">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title display-5 fw-bold"><span>Online</span> Services</h2>
                <p class="section-subtitle">
                    Barangay 752 Zone-81 offers various services to assist residents with their needs. Explore our
                    services below.
                </p>
            </div>

            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <div class="service-card">
                        <div class="service-icon-container">
                            <i class="bi bi-file-text service-icon"></i>
                        </div>
                        <h3 class="service-title">Barangay Clearance</h3>
                        <p class="service-description">
                            Official document certifying that you are a resident of good standing in the barangay.
                            Required for various transactions.
                        </p>
                        <a href="login.php" class="btn btn-outline-primary service-btn">
                            Learn More <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="service-card">
                        <div class="service-icon-container">
                            <i class="bi bi-file-check service-icon"></i>
                        </div>
                        <h3 class="service-title">Certificate of Residency</h3>
                        <p class="service-description">
                            Confirms your residency status within the barangay. Useful for school enrollment,
                            employment, and other official purposes.
                        </p>
                        <a href="login.php" class="btn btn-outline-primary service-btn">
                            Learn More <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="service-card">
                        <div class="service-icon-container">
                            <i class="bi bi-file-earmark-text service-icon"></i>
                        </div>
                        <h3 class="service-title">Business Permit</h3>
                        <p class="service-description">
                            Required document for operating a business within the barangay jurisdiction. Part of the
                            business registration process.
                        </p>
                        <a href="login.php" class="btn btn-outline-primary service-btn">
                            Learn More <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="service-card">
                        <div class="service-icon-container">
                            <i class="bi bi-clock-history service-icon"></i>
                        </div>
                        <h3 class="service-title">Document Processing</h3>
                        <p class="service-description">
                            Assistance with processing various documents and forms required for government transactions.
                        </p>
                        <a href="login.php" class="btn btn-outline-primary service-btn">
                            Learn More <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="service-card">
                        <div class="service-icon-container">
                            <i class="bi bi-search service-icon"></i>
                        </div>
                        <h3 class="service-title">Dispute Resolution</h3>
                        <p class="service-description">
                            Mediation services for resolving conflicts between residents through the Lupong
                            Tagapamayapa.
                        </p>
                        <a href="login.php" class="btn btn-outline-primary service-btn">
                            Learn More <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="service-card">
                        <div class="service-icon-container">
                            <i class="bi bi-exclamation-triangle service-icon"></i>
                        </div>
                        <h3 class="service-title">Emergency Assistance</h3>
                        <p class="service-description">
                            Immediate response and support during emergencies, disasters, and other crisis situations.
                        </p>
                        <a href="login.php" class="btn btn-outline-primary service-btn">
                            Learn More <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <br><br><br>

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
                        <li><a href="../../index.php" class="footer-link">Home</a></li>
                        <li><a href="AboutUsBootstrap.php" class="footer-link">About Us</a></li>
                        <li><a href="serviceBootstrap.php" class="footer-link">Services</a></li>
                    </ul>

                    <h3 class="footer-title">Connect With Us</h3>
                    <div class="social-links">
                        <a href="https://facebook.com" class="social-link" target="_blank" rel="noopener noreferrer">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="https://twitter.com" class="social-link" target="_blank" rel="noopener noreferrer">
                            <i class="bi bi-twitter"></i>
                        </a>
                        <a href="https://instagram.com" class="social-link" target="_blank" rel="noopener noreferrer">
                            <i class="bi bi-instagram"></i>
                        </a>
                    </div>
                </div>

                <!-- Logo Section -->
                <div class="footer-logo-container">
                    <div class="footer-logos">
                        <img src="../../img/lunsgodNgMayNilaLogo.png" class="footer-logo-img"
                            alt="Seal of Lungsod ng Maynila">
                        <img src="../../img/Barangay Logo.png" class="footer-logo-img" alt="Barangay Logo">
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

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="../../js/back-to-top.js"></script>
    <script src="../../js/services.js"></script>
</body>

</html>