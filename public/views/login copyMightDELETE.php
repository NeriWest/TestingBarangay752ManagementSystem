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
    <title>Barangay 752 | Official Website of Barangay 752</title>
    <link rel="stylesheet" href="../../css/loginRegistration.css">
    <link rel="stylesheet" href="../../css/homepage.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Ephesis&family=Lexend:wght@100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Ephesis&family=Lexend:wght@100..900&family=Metrophobic&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

</head>
<body>
    <!-- HEADER -->
    <div class="header">
            <div class="headerLogo">
                <img src="../../img/Barangay Logo.png" alt="Baranggay Logo">
            </div>
            <div class="barangayNumber">
                <h1>BARANGAY 752 ZONE-81</h1>
            </div>

        <div class="topNav">
            <div class="topNavAnchors">
                <a href="../../index.php" class="nav-link">Home</a>
                <a href="../views/aboutUs.php" class="nav-link">About Us</a>
                <a href="#" class="nav-link" id="navAnnouncement">Announcement</a>
                <a href="../views/services.php" class="nav-link">Services</a>
                <a class="loginAnchor nav-link" href="../views/login.php">Login</a>
            </div>
        </div>
    </div>

    <div class="loginForm">
        <div class="loginLogo" style="margin-bottom: -40px;">
            <img src="../../img/Barangay Logo.png" alt="">
            <h1>LOGIN MODULE</h1>
        </div>
        <?php if (isset($_SESSION['message']) || isset($_SESSION['errorMessage']) || isset($_SESSION['successMessage'])) : ?>
            <div style="padding: 15px; margin-bottom: 20px; border-radius: 5px; font-family: Arial, sans-serif; font-size: 18px; color: #fff; background-color: 
            <?php 
            if (isset($_SESSION['errorMessage'])) {
            echo '#FF4D4D'; // Red for error
            } elseif (isset($_SESSION['successMessage'])) {
            echo '#4CAF50'; // Green for success
            }


            ?>;">
            <span>
            <?php 
            if (isset($_SESSION['errorMessage'])) {
                echo $_SESSION['errorMessage'];
                unset($_SESSION['errorMessage']);
            } elseif (isset($_SESSION['successMessage'])) {
                echo $_SESSION['successMessage'];
                unset($_SESSION['successMessage']);
            }
            ?>
            </span>
            <button onclick="this.parentElement.style.display='none';" style="background: none; border: none; color: #fff; font-size: 20px; float: right; cursor: pointer;">&times;</button>
            </div>
        <?php endif; ?>


        

        <form class="loginLabel" action="../../controller/loginController.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
            <label for="username">Username or Phone number</label><br>
            <input type="text" id="username" name="username" placeholder="Enter your username or phone number" maxlength="32"><br>
            <label for="password">Password: </label><br>
            <input type="password" id="password" name="password" placeholder="Enter your password" maxlength="32"><br>
            <div class="registerContainer">
                <p>Don't have an account yet? <a href="register.php">REGISTER</a></p>
                <p><a href="forgotPassword.php">FORGOT PASSWORD?</a></p>
            </div>
            <div class="loginButton">
                <br>
                <button class="button" type="submit">LOG IN</button>
            </div>
        </form>
    </div>

    <!-- FOOTER -->
    <div class="footer">
        <hr class="footerHR">
        <div class="footerContent">
            <!-- Address & Map -->
            <div class="first">

                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d444.4205693398933!2d120.99933729844516!3d14.5683702225061!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397c99d33cee621%3A0xb5e2f10c0095d856!2s1194%20Florentino%20Torres%2C%20San%20Andres%20Bukid%2C%20Manila%2C%20Metro%20Manila!5e1!3m2!1sen!2sph!4v1740668301724!5m2!1sen!2sph"
                    width="350" height="200" style="border:0;" allowfullscreen="" loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"></iframe>

            </div>

            <!-- Contact Section -->
            <div class="contact">
                <h3>Contact</h3>
                <p><i class="fas fa-phone phone-icon"></i> +63 912 345 6789</p>
                <p><a href="https://www.facebook.com/brgy752" target="_blank" class="facebook-link"><i
                            class="fab fa-facebook facebook-icon"></i> Barangay 752 Official</a></p>
            </div>

            <!-- More Section -->
            <div class="more">
                <h3>More</h3>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="services.php">Services</a></li>
                </ul>
            </div>

            <!-- Logos & Copyright -->
            <div class="footerLogo">
                <div class="logos">
                    <img src="../../img/lunsgodNgMayNilaLogo.png" alt="Seal of Lungsod ng Maynila">
                    <img src="../../img/Barangay Logo.png" alt="Barangay Logo">
                </div>
                <p class="footerParagraph">© 2024 | COPYRIGHT: BARANGAY 752</p>
            </div>
        </div>
    </div>

</body>
</html>
