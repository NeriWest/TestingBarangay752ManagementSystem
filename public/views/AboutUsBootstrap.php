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
    <title>About Us</title>
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

    <link rel="stylesheet" href="../../css/aboutUs.css">


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
                    <button class="btn btn-login"><a href="login.php">Log In</a></button>
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
                <h1 class="hero-title">About Our Barangay</h1>
                <p class="hero-subtitle">Learn about our history, mission, and the people who make our community special
                </p>
            </div>
        </div>
    </section>

    <!-- History Section -->
    <section class="history-gallery py-5 py-md-4 py-lg-5">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="history col-lg-6 animate-opacity-x-left">
                    <div class="icon-container bg-primary-light mx-auto mx-lg-0">
                        <i class="bi bi-clock-history fs-3 text-primary"></i>
                    </div>
                    <h2 class="fw-bold mb-4 display-5">OUR <span>HISTORY</span></h2>
                    <div class="text-muted-foreground">
                        <p class="mb-4">
                            Located at #1200 Espiritu Street, corner Florentino Torres Street, Singalong, Manila,
                            Barangay 752 is one of the 10 proud barangays in Zone 81, District V. We proudly offer
                            services like the Manila Child Development Center (commonly known as Daycare), which caters
                            to children aged 3 to 5 years old.

                        </p>
                        <p class="mb-4">
                            Since I assumed office in June 2018, our barangay has undergone significant transformation,
                            guided by the principle that “Public service is a public trust.” In June 2022, we received a
                            Rescue Van from the office of Congressman Irwin Tieng, officially turned over in September
                            2023. Later that year, we were awarded the construction of a brand-new Multi-Purpose
                            Barangay Hall and Daycare Center, which was completed and turned over to us in February
                            2024.
                        </p>
                        <p class="mb-0">
                            With the many recognitions and accomplishments our barangay has received, I am proud to say
                            we remain committed to building a peaceful, safe, clean, healthy, and drug-free community.
                            Looking ahead, I am embracing digitalization to ensure more efficient and effective services
                            for our beloved residents.
                        </p>
                    </div>
                </div>
                <div class="col-lg-6 animate-opacity-x-right">
                    <img src="../../img/AboutUsCarousel2.jpg" alt="Historical photo of barangay"
                        class="img-fluid rounded-3 shadow-lg w-100"
                        style="height: 550px; object-fit: cover; margin-top: 5rem;">
                </div>
            </div>
        </div>
    </section>

    <!-- Vision and Mission Section -->
    <section class="history-gallery py-5 py-md-4 py-lg-5 bg-muted">
        <div class="container">
            <div class="text-center mb-5">
                <div class="icon-container bg-primary-light mx-auto">
                    <i class="bi bi-bullseye fs-3 text-primary"></i>
                </div>
                <h2 class="fw-bold mb-3 display-5">OUR <span>VISION & MISSION</span></h2>
                <p class="fs-5 text-muted-foreground mx-auto" style="max-width: 42rem">
                    Guiding principles that drive our community forward
                </p>
            </div>

            <div class="row g-4 justify-content-center">
                <div class="col-md-10 col-lg-6 animate-opacity-y">
                    <div class="bg-card p-4 p-md-5 rounded-3 shadow-sm border border-border h-100">
                        <h3 class="fw-bold text-center mb-4 display-6">Our Vision</h3>
                        <ul class="list-unstyled text-muted-foreground">
                            <li class="mb-3 d-flex align-items-start">
                                <div class="bg-primary-light rounded-circle p-2 me-3 flex-shrink-0">
                                    <i class="bi bi-arrow-right text-primary"></i>
                                </div>
                                <span>To live in a clean, peaceful, drug free community where families can be safe and
                                    secured inside and outside their homes.
                                </span>
                            </li>
                            <li class="mb-3 d-flex align-items-start">
                                <div class="bg-primary-light rounded-circle p-2 me-3 flex-shrink-0">
                                    <i class="bi bi-arrow-right text-primary"></i>
                                </div>
                                <span>A community of God fearing, disciplined and law abiding individuals caring for the
                                    elderlies.
                                </span>
                            </li>
                            <li class="d-flex align-items-start">
                                <div class="bg-primary-light rounded-circle p-2 me-3 flex-shrink-0">
                                    <i class="bi bi-arrow-right text-primary"></i>
                                </div>
                                <span>To work together collectively in the rest of the majority thus promoting quality
                                    and proactive service to its constituents.
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="col-md-10 col-lg-6 animate-opacity-y" style="transition-delay: 0.2s">
                    <div class="bg-card p-4 p-md-5 rounded-3 shadow-sm border border-border h-100">
                        <h3 class="fw-bold text-center mb-4 display-6">Our Mission</h3>
                        <ul class="list-unstyled text-muted-foreground">
                            <li class="mb-3 d-flex align-items-start">
                                <div class="bg-primary-light rounded-circle p-2 me-3 flex-shrink-0">
                                    <i class="bi bi-arrow-right text-primary"></i>
                                </div>
                                <span>To strictly implement the law and ordinances governing the community at all times.
                                </span>
                            </li>
                            <li class="mb-3 d-flex align-items-start">
                                <div class="bg-primary-light rounded-circle p-2 me-3 flex-shrink-0">
                                    <i class="bi bi-arrow-right text-primary"></i>
                                </div>
                                <span>To assist the people of the community in whatever problems or grievances the
                                    barangay offer.
                                </span>
                            </li>
                            <li class="mb-3 d-flex align-items-start">
                                <div class="bg-primary-light rounded-circle p-2 me-3 flex-shrink-0">
                                    <i class="bi bi-arrow-right text-primary"></i>
                                </div>
                                <span>To promote a healthy and a sanitary environment for its inhabitants. </span>
                            </li>
                            <li class="mb-3 d-flex align-items-start">
                                <div class="bg-primary-light rounded-circle p-2 me-3 flex-shrink-0">
                                    <i class="bi bi-arrow-right text-primary"></i>
                                </div>
                                <span>To support the welfare of the youth promoting their active participation in the
                                    sports activities, trainings, and seminar.
                                </span>
                            </li>
                            <li class="mb-3 d-flex align-items-start">
                                <div class="bg-primary-light rounded-circle p-2 me-3 flex-shrink-0">
                                    <i class="bi bi-arrow-right text-primary"></i>
                                </div>
                                <span>To provide an effective and efficiency of basic social services the government
                                    offer
                                </span>
                            </li>
                            <li class="d-flex align-items-start">
                                <div class="bg-primary-light rounded-circle p-2 me-3 flex-shrink-0">
                                    <i class="bi bi-arrow-right text-primary"></i>
                                </div>
                                <span>To practice good governance to set the transparency, responsiveness,
                                    accountability, and participation.
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="text-center mt-5 pt-3">
                <p class="fs-5 text-muted-foreground mx-auto" style="max-width: 42rem">
                    Our core values include integrity, excellence, innovation,
                    inclusivity, and compassion. These values guide our decisions and
                    actions as we serve our community.
                </p>
            </div>
        </div>
    </section>

    <!-- Photo Gallery Section -->
    <section class="history-gallery py-5 py-md-4 py-lg-5">
        <div class="container">
            <div class="text-center mb-5">
                <div class="icon-container bg-primary-light mx-auto">
                    <i class="bi bi-images fs-3 text-primary"></i>
                </div>
                <h2 class="fw-bold mb-3 display-5"><span>PHOTO</span> GALLERY</h2>
                <p class="fs-5 text-muted-foreground mx-auto" style="max-width: 42rem">
                    Capturing moments from our community events and activities
                </p>
            </div>

            <div class="row g-4">
                <div class="col-md-6 col-lg-4 animate-opacity-y" style="transition-delay: 0.1s">
                    <div class="gallery-item">
                        <img src="../../img/AboutUsCarousel1.jpg" alt="Community gathering" class="gallery-image">
                        <div class="gallery-caption">
                            <p class="text-white fw-medium mb-0">Community Cleanup Initiative</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 animate-opacity-y" style="transition-delay: 0.2s">
                    <div class="gallery-item">
                        <img src="../../img/AboutUsCarousel2.jpg" alt="Barangay cleanup drive" class="gallery-image">
                        <div class="gallery-caption">
                            <p class="text-white fw-medium mb-0">Barangay Councils</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 animate-opacity-y" style="transition-delay: 0.3s">
                    <div class="gallery-item">
                        <img src="../../img/AboutUsCarousel3.jpg" alt="Community celebration" class="gallery-image">
                        <div class="gallery-caption">
                            <p class="text-white fw-medium mb-0">Give & Share: Community Donation</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 animate-opacity-y" style="transition-delay: 0.4s">
                    <div class="gallery-item">
                        <img src="../../img/AboutUsCarousel4.jpg" alt="Health outreach program" class="gallery-image">
                        <div class="gallery-caption">
                            <p class="text-white fw-medium mb-0">Daycare Program</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 animate-opacity-y" style="transition-delay: 0.5s">
                    <div class="gallery-item">
                        <img src="../../img/HomeBackgroundBrgy.jpg" alt="Youth development program" class="gallery-image">
                        <div class="gallery-caption">
                            <p class="text-white fw-medium mb-0">Daycare Flag Ceremony</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 animate-opacity-y" style="transition-delay: 0.6s">
                    <div class="gallery-item">
                        <img src="../../img/Background.jpg" alt="Barangay infrastructure" class="gallery-image">
                        <div class="gallery-caption">
                            <p class="text-white fw-medium mb-0">Barangay Community</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <br><br><br>


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



    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../js/back-to-top.js"></script>
    <script src="../../js/MainStyle.js"></script>
    <script src="../../js/aboutus.js"></script>

</body>

</html>