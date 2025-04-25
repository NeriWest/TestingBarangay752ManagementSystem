<?php
session_start();

// Check if the user is logged in by verifying the 'username' session variable
if (isset($_SESSION['username'])) {
    // If the user is logged in, redirect them to the admin dashboard
    header('Location: ../../controller/admin/adminDashboardController.php');
    exit(); // Ensure the script stops executing after redirection
}

// If the user is not logged in, generate a CSRF token if it doesn't already exist
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Assign the CSRF token to a variable to use in the form
$csrfToken = $_SESSION['csrf_token'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Barangay 752 Zone-81</title>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../css/forgotPasswordBootstrap.css">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-custom navbar-expand-lg" id="mainNavbar">
        <div class="container">
            <!-- Logo -->
            <div class="navbar-brand-custom" href="#">
                <img src="../../img/Barangay Logo.png" alt="Barangay Logo" class="navbar-logo">
                <span class="navbar-title">BARANGAY 752 ZONE-81</span>
            </div>

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
                </ul>
                <button class="btn btn-login"><a href="login.php">Log In</a></button>
            </div>

            <!-- Mobile menu button -->
            <div class="navbar-toggler-container">
                <button class="navbar-toggler d-lg-none" type="button" id="mobileMenuButton">
                    <span class="navbar-toggler-icon"></span>
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
                    <button class="btn btn-login w-100"><a href="login.php">Log In</a></button>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Reset Password Form -->
    <div class="forgot-container">
        <div class="card forgot-card">
            <div class="forgot-header">
                <img src="../../img/Barangay Logo.png" alt="Barangay Logo" class="img-fluid">
                <h1>RESET PASSWORD</h1>
                <p class="text-muted">Enter your new password and confirm <br>it to reset your account.</p>
            </div>
            
            <div class="card-body">
                <?php if(isset($_SESSION['errorMessage'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php 
                            echo htmlspecialchars($_SESSION['errorMessage']);
                            unset($_SESSION['errorMessage']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <form class="forgot-form" action="../../controller/resetPasswordController.php" method="POST" id="resetPasswordForm">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                    
                    <div class="mb-3 form-group password-input-group">
                        <input type="password" class="form-control" id="newPassword" name="new-password" 
                               placeholder=" " required>
                        <label for="newPassword" class="form-label">New Password</label>
                        <button type="button" class="password-toggle-btn" onclick="togglePassword('newPassword')">
                            <i class="fas fa-eye"></i>
                        </button>
                        <div class="invalid-feedback">
                            Please enter a valid password (minimum 8 characters).
                        </div>
                    </div>
                    
                    <div class="mb-3 form-group password-input-group">
                        <input type="password" class="form-control" id="confirmPassword" name="confirm-password" 
                               placeholder=" " required>
                        <label for="confirmPassword" class="form-label">Confirm Password</label>
                        <button type="button" class="password-toggle-btn" onclick="togglePassword('confirmPassword')">
                            <i class="fas fa-eye"></i>
                        </button>
                        <div class="invalid-feedback">
                            Passwords do not match.
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary">SUBMIT</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
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

            <div class="footer-divider"></div>

            <div class="footer-copyright">
                <p>© <span id="copyrightYear"></span> BARANGAY 752 ZONE-81. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Custom JS -->
    <script>
        // Password toggle functionality
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = input.nextElementSibling.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Form validation
        function validateForm() {
            const newPassword = document.getElementById('newPassword');
            const confirmPassword = document.getElementById('confirmPassword');
            let isValid = true;

            // Reset validation states
            newPassword.classList.remove('is-invalid');
            confirmPassword.classList.remove('is-invalid');

            // Password length validation
            if (newPassword.value.length < 8) {
                newPassword.classList.add('is-invalid');
                isValid = false;
            }

            // Password match validation
            if (newPassword.value !== confirmPassword.value) {
                confirmPassword.classList.add('is-invalid');
                isValid = false;
            }

            return isValid;
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Navbar scroll effect
            const navbar = document.getElementById('mainNavbar');
            window.addEventListener('scroll', function() {
                if (window.scrollY > 10) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            });

            // Mobile menu toggle
            const mobileMenuButton = document.getElementById('mobileMenuButton');
            const mobileMenu = document.getElementById('mobileMenu');
            mobileMenuButton.addEventListener('click', function() {
                if (mobileMenu.style.display === 'none' || !mobileMenu.style.display) {
                    mobileMenu.style.display = 'block';
                    this.innerHTML = '<i class="bi bi-x-lg"></i>';
                } else {
                    mobileMenu.style.display = 'none';
                    this.innerHTML = '<span class="navbar-toggler-icon"></span>';
                }
            });

            // Close mobile menu on link click
            const mobileLinks = document.querySelectorAll('.mobile-nav-link');
            mobileLinks.forEach(link => {
                link.addEventListener('click', function() {
                    mobileMenu.style.display = 'none';
                    mobileMenuButton.innerHTML = '<span class="navbar-toggler-icon"></span>';
                });
            });

            // Update copyright year
            document.getElementById('copyrightYear').textContent = new Date().getFullYear();

            // Data Privacy Modal
            const privacyModal = new bootstrap.Modal(document.getElementById('privacyModal'), {
                backdrop: 'static',
                keyboard: false
            });
            const privacyContent = document.getElementById('privacyContent');
            const agreeButton = document.getElementById('agreeButton');
            const privacyAgree = document.getElementById('privacyAgree');
            const resetForm = document.getElementById('resetPasswordForm');

            // Show modal on page load
            privacyModal.show();

            // Enable checkbox when user scrolls to bottom
            privacyContent.addEventListener('scroll', function() {
                if (privacyContent.scrollTop + privacyContent.clientHeight >= privacyContent.scrollHeight - 10) {
                    privacyAgree.disabled = false;
                }
            });

            // Enable proceed button when checkbox is checked
            privacyAgree.addEventListener('change', function() {
                agreeButton.disabled = !this.checked;
            });

            // Show form when user agrees
            agreeButton.addEventListener('click', function() {
                if (privacyAgree.checked) {
                    privacyModal.hide();
                    resetForm.style.display = 'block';
                }
            });

            // Modal Button Handlers
            document.getElementById('confirmClearBtn').addEventListener('click', function() {
                const form = document.getElementById('resetPasswordForm');
                form.reset();
                form.querySelectorAll('.form-control').forEach(input => {
                    input.classList.remove('is-invalid');
                });
                const clearModal = bootstrap.Modal.getInstance(document.getElementById('clearFormModal'));
                clearModal.hide();
            });

            document.getElementById('confirmSubmitBtn').addEventListener('click', function() {
                if (validateForm()) {
                    document.getElementById('resetPasswordForm').submit();
                    const submitModal = bootstrap.Modal.getInstance(document.getElementById('submitFormModal'));
                    submitModal.hide();
                }
            });

            // Real-time password validation
            const newPassword = document.getElementById('newPassword');
            const confirmPassword = document.getElementById('confirmPassword');

            newPassword.addEventListener('input', validateForm);
            confirmPassword.addEventListener('input', validateForm);
        });



        
    </script>
</body>
</html>