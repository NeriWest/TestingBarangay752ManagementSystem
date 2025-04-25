<?php
session_start();

require_once '../../model/dbconnection.php';

if (isset($_SESSION['username'])) {
    header('Location: ../../controller/admin/adminDashboardController.php');
    exit();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$csrfToken = $_SESSION['csrf_token'];
$formData = $_SESSION['form_data'] ?? [];

// Handle duplicate check requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['field'], $_POST['value'])) {
    header('Content-Type: application/json');
    $field = $_POST['field'] ?? '';
    $value = $_POST['value'] ?? '';

    if (empty($field) || empty($value)) {
        echo json_encode(['exists' => false, 'message' => 'Invalid input']);
        exit;
    }

    try {
        $db = new dbcon();
        $conn = $db->getConnection();
        $exists = false;
        $message = '';

        if ($field === 'email') {
            $email_table = 'accounts';
            $email_column = 'email';
            $email_stmt = $conn->prepare("SELECT COUNT(*) as count FROM $email_table WHERE $email_column = ?");
            if (!$email_stmt) {
                throw new Exception('Email prepare failed: ' . $conn->error);
            }
            $email_stmt->bind_param('s', $value);
            if (!$email_stmt->execute()) {
                throw new Exception('Email execute failed: ' . $email_stmt->error);
            }
            $email_result = $email_stmt->get_result();
            $email_row = $email_result->fetch_assoc();
            $email_count = $email_row['count'];
            error_log("Email check: value=$value, count=$email_count");
            $exists = $email_count > 0;
            $message = $exists ? 'Email already exists' : '';
            $email_stmt->close();
        } elseif ($field === 'cellphone_number') {
            $phone_table = 'residents';
            $phone_column = 'cellphone_number';
            $phone_stmt = $conn->prepare("SELECT COUNT(*) as count FROM $phone_table WHERE $phone_column = ?");
            if (!$phone_stmt) {
                throw new Exception('Phone prepare failed: ' . $conn->error);
            }
            $phone_stmt->bind_param('s', $value);
            if (!$phone_stmt->execute()) {
                throw new Exception('Phone execute failed: ' . $phone_stmt->error);
            }
            $phone_result = $phone_stmt->get_result();
            $phone_row = $phone_result->fetch_assoc();
            $phone_count = $phone_row['count'];
            error_log("Phone check: value=$value, count=$phone_count");
            $exists = $phone_count > 0;
            $message = $exists ? 'Cellphone number already exists' : '';
            $phone_stmt->close();
        } else {
            echo json_encode(['exists' => false, 'message' => 'Invalid field']);
            $conn->close();
            exit;
        }

        echo json_encode(['exists' => $exists, 'message' => $message]);
        $conn->close();
        exit;
    } catch (Exception $e) {
        error_log('Check duplicate error: ' . $e->getMessage());
        echo json_encode(['exists' => false, 'message' => 'Database error']);
        exit;
    }
}

// Fetch residents for other purposes
try {
    $db = new dbcon();
    $conn = $db->getConnection();
    $result = $conn->query("SELECT id, cellphone_number FROM residents");
    $residents = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $residents[] = $row;
        }
    }
    $conn->close();
} catch (Exception $e) {
    error_log("Error fetching residents: " . $e->getMessage());
    $residents = [];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay 752 | Official Website</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        :root {
            --primary-blue: #1E3A8A;
            --light-blue: #EFF6FF;
            --dark-gray: #1e293b;
            --border-gray: #d1d5db;
            --error-red: #dc3545;
            --text-gray: #4b5563;
        }

        body {
            background-color: #f8f9fa;
            padding-top: 70px;
            color: var(--text-gray);
        }

        /* Navbar Styles */
        .navbar-custom {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            background-color: #1E3A8A;
            transition: all 0.3s ease;
            padding: 1rem 0;
        }

        .navbar-custom.scrolled {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            padding: 0.5rem 0;
        }

        .navbar-brand-custom {
            display: flex;
            align-items: center;
            text-decoration: none;
        }

        .navbar-logo {
            height: 40px;
            width: auto;
        }

        .navbar-title {
            margin-left: 0.5rem;
            color: white;
            font-weight: 700;
            font-size: 1.25rem;
        }

        .nav-link-custom {
            position: relative;
            color: white;
            padding: 0.5rem 1rem;
            margin: 0 0.25rem;
            transition: color 0.3s ease;
            text-decoration: none;
        }

        .nav-link-custom:hover {
            color: #BFDBFE;
        }

        .nav-link-custom::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background-color: white;
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s ease;
        }

        .nav-link-custom:hover::after {
            transform: scaleX(1);
        }

        .nav-link-custom:hover .nav-chevron {
            opacity: 1;
        }

        .nav-chevron {
            opacity: 0;
            transition: opacity 0.3s ease;
            margin-left: 0.25rem;
        }

        .btn-login {
            background-color: white;
            color: #1E3A8A;
            font-weight: 500;
            margin-left: 1rem;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            transition: all 0.3s ease;
            text-decoration: none !important;
            display: inline-block;
        }

        .btn-login:hover {
            background-color: #EFF6FF;
            text-decoration: none !important;
        }

        .btn-login a {
            color: inherit;
            text-decoration: none !important;
            display: block;
            width: 100%;
            height: 100%;
        }

        .mobile-nav-link {
            display: flex;
            align-items: center;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            transition: background-color 0.3s ease;
            text-decoration: none;
        }

        .mobile-nav-link:hover {
            background-color: #3B82F6;
            text-decoration: none;
        }

        /* Registration Container */
        .registration-container {
            min-height: calc(100vh - 70px - 150px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 0;
        }

        .registration-card {
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border: none;
            background: #fff;
        }

        .card-body {
            padding: 2rem;
        }

        .registration-header {
            text-align: center;
            padding: 1rem 0 1.5rem;
            margin-bottom: 0.5rem;
        }

        .registration-header img {
            height: 70px;
            margin-bottom: 0.75rem;
        }

        .registration-header h1 {
            font-weight: 600;
            color: var(--primary-blue);
            font-size: 1.75rem;
            margin-bottom: 0.5rem;
        }

        .form-note {
            font-size: 0.875rem;
            color: var(--text-gray);
            margin-bottom: 0.5rem;
            line-height: 1.4;
        }

        .required-field::after {
            content: " *";
            color: var(--error-red);
        }

        /* Section Title */
        .section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--primary-blue);
            margin: 1.5rem 0 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--light-blue);
        }

        /* Form Group (Improved Floating Labels) */
        .form-group {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .form-control,
        .form-select {
            border: 1px solid var(--border-gray);
            border-radius: 6px;
            padding: 0.75rem 1rem;
            font-size: 0.9375rem;
            height: auto;
            color: var(--dark-gray);
            background: #fff;
            transition: all 0.2s ease;
            width: 100%;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 2px rgba(30, 58, 138, 0.2);
            outline: none;
        }

        .form-label {
            position: absolute;
            top: -0.6rem;
            left: 0.8rem;
            font-size: 0.8125rem;
            color: var(--primary-blue);
            background: #fff;
            padding: 0 0.3rem;
            z-index: 1;
            transition: all 0.2s ease;
            font-weight: 500;
        }

        /* Radio Button Groups */
        .form-check-group {
            margin-top: 0.5rem;
        }

        .form-check-group-horizontal {
            display: flex;
            gap: 1.5rem;
            margin-top: 0.5rem;
        }

        .form-check {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .form-check-input {
            margin-right: 0.5rem;
        }

        .form-check-label {
            font-size: 0.9375rem;
            color: var(--dark-gray);
        }

        /* Name Input Group */
        .name-input-group {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 0.5rem;
        }

        @media (max-width: 768px) {
            .name-input-group {
                grid-template-columns: 1fr;
            }
        }

        /* File Upload */
        .file-upload-container {
            text-align: center;
            margin: 1.5rem 0;
            padding: 1.5rem;
            border: 2px dashed var(--border-gray);
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .file-upload-container:hover {
            border-color: var(--primary-blue);
            background-color: rgba(239, 246, 255, 0.3);
        }

        .file-upload-label {
            display: block;
            cursor: pointer;
        }

        .file-upload-label strong {
            font-weight: 600;
            color: var(--primary-blue);
            font-size: 0.9375rem;
        }

        .file-upload-label img {
            margin: 0.5rem 0;
            width: 36px;
            opacity: 0.8;
        }

        .file-info {
            background-color: #f8f9fa;
            padding: 0.5rem 0.75rem;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            margin-top: 0.75rem;
        }

        .image-preview-container {
            margin-top: 0.75rem;
        }

        .preview-image {
            max-width: 100%;
            max-height: 150px;
            border-radius: 6px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Buttons */
        .btn-primary,
        .btn-danger {
            padding: 0.75rem 1.5rem;
            font-size: 0.9375rem;
            font-weight: 500;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background-color: var(--primary-blue);
            border-color: var(--primary-blue);
        }

        .btn-primary:hover {
            background-color: #162c6a;
            transform: translateY(-1px);
        }

        .btn-danger {
            background-color: var(--error-red);
            border-color: var(--error-red);
        }

        .btn-danger:hover {
            background-color: #c82333;
            transform: translateY(-1px);
        }

        .d-grid {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 1.5rem;
        }

        /* Error Messages */
        .invalid-feedback {
            color: var(--error-red);
            font-size: 0.8125rem;
            margin-top: 0.25rem;
            padding-left: 0.5rem;
            display: none;
        }

        .form-control.is-invalid {
            border-color: var(--error-red);
        }

        .form-control.is-invalid~.invalid-feedback {
            display: block;
        }

        /* Form Links */
        .form-links {
            text-align: center;
            margin: 1.5rem 0;
        }

        .form-links p {
            margin: 0;
            font-size: 0.9375rem;
        }

        .form-links a {
            color: var(--primary-blue);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .form-links a:hover {
            color: var(--error-red);
            text-decoration: underline;
        }

        /* Alert */
        .alert {
            margin-bottom: 1.5rem;
            font-size: 0.9375rem;
            border-radius: 6px;
        }

        /* Modal Styles */
        .modal-content {
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
        }

        .modal-header {
            background-color: var(--primary-blue);
            color: white;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }

        .modal-title {
            font-weight: 600;
            font-size: 1.25rem;
        }

        .modal-body {
            padding: 1.5rem;
            color: var(--text-gray);
            font-size: 0.9375rem;
        }

        .modal-footer {
            border-top: none;
            padding: 1rem 1.5rem;
        }

        .modal-footer .btn-primary {
            background-color: var(--primary-blue);
            border-color: var(--primary-blue);
        }

        .modal-footer .btn-primary:hover {
            background-color: #162c6a;
        }

        .modal-footer .btn-danger {
            background-color: var(--error-red);
            border-color: var(--error-red);
        }

        .modal-footer .btn-danger:hover {
            background-color: #c82333;
        }

        /* Footer Styles */
        .footer {
            background-color: #1e293b;
            color: white;
            padding: 3rem 1rem;
            position: relative;
            overflow: hidden;
        }

        .footer-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: white;
        }

        .footer-map {
            height: 12rem;
            width: 100%;
            border-radius: 0.5rem;
            overflow: hidden;
            background-color: #334155;
            margin-bottom: 1rem;
            transition: height 0.3s ease;
        }

        .footer-map iframe {
            width: 100%;
            height: 100%;
            border: none;
        }

        .footer-contact-item,
        .footer-hours-item {
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
            margin-bottom: 0.75rem;
        }

        .footer-icon {
            color: #60a5fa;
            margin-top: 0.25rem;
            flex-shrink: 0;
        }

        .footer-link {
            color: white;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .footer-link:hover {
            color: #60a5fa;
        }

        .footer-quick-links {
            list-style: none;
            padding-left: 0;
        }

        .footer-quick-links li {
            margin-bottom: 0.5rem;
        }

        .footer-quick-links a {
            color: white;
            text-decoration: none;
            transition: color 0.2s ease;
            display: flex;
            align-items: center;
        }

        .footer-quick-links a:hover {
            color: #60a5fa;
        }

        .footer-quick-links a::before {
            content: "›";
            color: #60a5fa;
            margin-right: 0.5rem;
            transition: margin-right 0.2s ease;
        }

        .footer-quick-links a:hover::before {
            margin-right: 0.7rem;
        }

        .social-links {
            display: flex;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .social-link {
            background-color: #1e40af;
            color: white;
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .social-link:hover {
            background-color: #1e3a8a;
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .footer-divider {
            height: 1px;
            background-color: #334155;
            margin: 2rem 0;
            transition: margin 0.3s ease;
        }

        .footer-copyright {
            color: #94a3b8;
            text-align: center;
            transition: font-size 0.3s ease;
        }

        .footer-logo-container {
            margin-top: 2rem;
            width: 100%;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .footer-logos {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            flex-wrap: wrap;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .footer-logo-img {
            height: auto;
            max-height: 120px;
            width: auto;
            max-width: 120px;
            object-fit: contain;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
        }

        @media (min-width: 1200px) {
            .footer-logo-container {
                position: absolute;
                right: 1rem;
                top: 1rem;
                width: auto;
                margin-top: 0;
            }

            .footer-logos {
                justify-content: flex-end;
            }

            .footer-logo-img {
                max-height: 150px;
                max-width: 150px;
            }
        }

        @media (min-width: 992px) and (max-width: 1199.98px) {
            .footer-logo-container {
                position: static;
                margin: 2rem auto 0;
                max-width: 300px;
            }

            .footer-logos {
                justify-content: center;
            }

            .footer-logo-img {
                max-height: 110px;
                max-width: 110px;
            }

            .footer-map {
                height: 10rem;
            }
        }

        @media (max-width: 991.98px) and (min-width: 768px) {
            .footer-logo-img {
                max-height: 100px;
                max-width: 100px;
            }

            .footer-map {
                height: 9rem;
            }

            .footer-divider {
                margin: 1.5rem 0;
            }
        }

        @media (max-width: 767.98px) {
            .footer-logo-img {
                max-height: 90px;
                max-width: 90px;
            }

            .footer-map {
                height: 8rem;
            }

            .footer-title {
                font-size: 1.1rem;
            }

            .footer-copyright {
                font-size: 0.9rem;
            }

            .footer-divider {
                margin: 1rem 0;
            }
        }

        @media (max-width: 399.98px) {
            .footer-logos {
                gap: 1rem;
            }

            .footer-logo-img {
                max-height: 80px;
                max-width: 80px;
            }
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-custom navbar-expand-lg" id="mainNavbar">
        <div class="container">
            <div class="navbar-brand-custom" href="#">
                <img src="../../img/Barangay Logo.png" alt="Barangay 752 Logo" class="navbar-logo">
                <span class="navbar-title">BARANGAY 752 ZONE-81</span>
            </div>
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
            <div class="navbar-toggler-container">
                <button class="navbar-toggler d-lg-none" type="button" id="mobileMenuButton">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
        </div>
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

    <!-- Registration Form -->
    <div class="registration-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-10">
                    <div class="card registration-card">
                        <div class="card-body">
                            <div class="registration-header">
                                <img src="../../img/Barangay Logo.png" alt="Barangay Logo" class="img-fluid">
                                <h1>REGISTRATION MODULE</h1>
                                <p class="form-note">Fill out the form for registration. All fields marked with <span class="required-field"></span> are required.</p>
                            </div>

                            <?php if (isset($_SESSION['errorMessages'])) : ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php
                                foreach ($_SESSION['errorMessages'] as $errorMessage) {
                                    echo $errorMessage . '<br>';
                                }
                                unset($_SESSION['errorMessages']);
                                ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php endif; ?>

                            <form action="../../controller/registerController.php" method="POST" enctype="multipart/form-data" id="registrationForm">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">

                                <!-- Personal Info -->
                                <h5 class="section-title">Personal Information</h5>
                                <div class="name-input-group">
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="fName" name="first-name" minlength="2" maxlength="32" pattern="[A-Za-z\s]+" title="Please enter letters only (no numbers or special characters)" oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '')" value="<?php echo htmlspecialchars($formData['first-name'] ?? ''); ?>" placeholder="Juan" required>
                                        <label for="fName" class="form-label required-field">First Name</label>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="mName" name="middle-name" minlength="2" oninput="this.value = this.value.replace(/[^A-Za-z\s\-]/g, '')" maxlength="32" value="<?php echo htmlspecialchars($formData['middle-name'] ?? ''); ?>" placeholder="Dela">
                                        <label for="mName" class="form-label">Middle Name</label>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="lName" name="last-name" minlength="2" maxlength="32" pattern="[A-Za-z\s]+" title="Please enter letters only (no numbers or special characters)" oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '')" value="<?php echo htmlspecialchars($formData['last-name'] ?? ''); ?>" placeholder="Cruz" required>
                                        <label for="lName" class="form-label required-field">Last Name</label>
                                    </div>
                                    <div class="form-group">
                                        <select class="form-select" id="suffix" name="suffix" onchange="toggleOtherSuffixInput(this)">
                                            <option value="" selected>Select Suffix</option>
                                            <option value="Jr." <?php echo ($formData['suffix'] ?? '') === 'Jr.' ? 'selected' : ''; ?>>Jr.</option>
                                            <option value="Sr." <?php echo ($formData['suffix'] ?? '') === 'Sr.' ? 'selected' : ''; ?>>Sr.</option>
                                            <option value="I" <?php echo ($formData['suffix'] ?? '') === 'I' ? 'selected' : ''; ?>>I</option>
                                            <option value="II" <?php echo ($formData['suffix'] ?? '') === 'II' ? 'selected' : ''; ?>>II</option>
                                            <option value="III" <?php echo ($formData['suffix'] ?? '') === 'III' ? 'selected' : ''; ?>>III</option>
                                            <option value="IV" <?php echo ($formData['suffix'] ?? '') === 'IV' ? 'selected' : ''; ?>>IV</option>
                                            <option value="V" <?php echo ($formData['suffix'] ?? '') === 'V' ? 'selected' : ''; ?>>V</option>
                                            <option value="Others" <?php echo !in_array($formData['suffix'] ?? '', ['Jr.', 'Sr.', 'I', 'II', 'III', 'IV', 'V', '']) ? 'selected' : ''; ?>>Others</option>
                                        </select>
                                        <label for="suffix" class="form-label">Suffix</label>
                                        <p class="form-note">ex. Jr., Sr., I, III, etc.</p>
                                        <div id="other-suffix-container" style="display: <?php echo !in_array($formData['suffix'] ?? '', ['Jr.', 'Sr.', 'I', 'II', 'III', 'IV', 'V', '']) ? 'block' : 'none'; ?>;">
                                            <input type="text" class="form-control" id="other-suffix" name="other_suffix" placeholder="Specify other suffix" minlength="1" maxlength="32" value="<?php echo !in_array($formData['suffix'] ?? '', ['Jr.', 'Sr.', 'I', 'II', 'III', 'IV', 'V', '']) ? htmlspecialchars($formData['suffix'] ?? '') : ''; ?>">
                                        </div>
                                    </div>
                                </div>

                                <!-- Demographic Info -->
                                <h5 class="section-title">Demographic Information</h5>
                                <div class="name-input-group">
                                    <div class="form-group">
                                        <select class="form-select" id="sex" name="sex" required>
                                            <option value="" selected disabled>Select Sex</option>
                                            <option value="Male" <?php echo ($formData['sex'] ?? '') === 'Male' ? 'selected' : ''; ?>>Male</option>
                                            <option value="Female" <?php echo ($formData['sex'] ?? '') === 'Female' ? 'selected' : ''; ?>>Female</option>
                                        </select>
                                        <label for="sex" class="form-label required-field">Sex</label>
                                    </div>
                                    <div class="form-group">
                                        <input type="date" class="form-control" id="birth-date" name="birth-date" min="<?php echo date('Y-m-d', strtotime('-120 years')); ?>" max="<?php echo date('Y-m-d', strtotime('-1 year')); ?>" value="<?php echo htmlspecialchars($formData['birth-date'] ?? $formData['date-of-birth'] ?? ''); ?>" required>
                                        <label for="birth-date" class="form-label required-field">Birthdate</label>
                                    </div>
                                    <div class="form-group">
                                        <select class="form-select" id="civil-status" name="civil_status" required>
                                            <option value="" selected disabled>Select Civil Status</option>
                                            <option value="Single" <?php echo ($formData['civil_status'] ?? '') === 'Single' ? 'selected' : ''; ?>>Single</option>
                                            <option value="Married" <?php echo ($formData['civil_status'] ?? '') === 'Married' ? 'selected' : ''; ?>>Married</option>
                                            <option value="Widowed" <?php echo ($formData['civil_status'] ?? '') === 'Widowed' ? 'selected' : ''; ?>>Widowed</option>
                                            <option value="Separated" <?php echo ($formData['civil_status'] ?? '') === 'Separated' ? 'selected' : ''; ?>>Separated</option>
                                        </select>
                                        <label for="civil-status" class="form-label required-field">Civil Status</label>
                                    </div>
                                </div>

                                <!-- Senior Citizen Information -->
                                <div id="osca-section" style="display: none;">
                                    <h5 class="section-title">Senior Citizen Information</h5>
                                    <div class="name-input-group">
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="osca-id" name="osca_id" maxlength="20" oninput="this.value = this.value.replace(/[^0-9]/g, '');" value="<?php echo htmlspecialchars($formData['osca_id'] ?? ''); ?>" placeholder="1234567890">
                                            <label for="osca-id" class="form-label required-field">OSCA ID</label>
                                        </div>
                                        <div class="form-group">
                                            <input type="date" class="form-control" id="osca-date-issued" name="osca_date_issued" max="<?php echo date('Y-m-d'); ?>" value="<?php echo htmlspecialchars($formData['osca_date_issued'] ?? ''); ?>">
                                            <label for="osca-date-issued" class="form-label required-field">OSCA Date Issued</label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Contact Info -->
                                <h5 class="section-title">Contact Information</h5>
                                <div class="name-input-group">
                                    <div class="form-group">
                                        <input type="tel" class="form-control" id="contactNo" name="cellphone_number" maxlength="11" pattern="09[0-9]{9}" inputmode="numeric" oninput="validatePhoneNumber(this)" value="<?php echo htmlspecialchars($formData['cellphone_number'] ?? ''); ?>" placeholder="09123456789" required>
                                        <label for="contactNo" class="form-label required-field">Contact Number</label>
                                        <p class="form-note">ex. 09093729483 (Globe, Smart, TNT, TM)</p>
                                        <div id="contactNo-error" class="invalid-feedback"></div>
                                    </div>
                                    <div class="form-group">
                                        <input type="email" class="form-control" id="email" name="email" maxlength="32" oninput="this.setCustomValidity('')" pattern="[^\s@]+@[^\s@]+\.[^\s@]+" value="<?php echo htmlspecialchars($formData['email'] ?? ''); ?>" placeholder="juancruz@example.com">
                                        <label for="email" class="form-label">Email</label>
                                        <p class="form-note">ex. Juancruz@domain.com</p>
                                        <div id="email-error" class="invalid-feedback"></div>
                                    </div>
                                </div>

                                <!-- Location Info -->
                                <h5 class="section-title">Location Information</h5>
                                <div class="name-input-group">
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="house-number" name="house_number" maxlength="20" placeholder="123-A" value="<?php echo htmlspecialchars($formData['house_number'] ?? ''); ?>" required>
                                        <label for="house-number" class="form-label required-field">House Number</label>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="street" name="street" maxlength="100" value="<?php echo htmlspecialchars($formData['street'] ?? ''); ?>" placeholder="Florentino Torres St." required>
                                        <label for="street" class="form-label required-field">Street</label>
                                    </div>
                                </div>

                                <!-- Citizenship Info -->
                                <h5 class="section-title">Citizenship Information</h5>
                                <div class="name-input-group">
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="citizenship" name="citizenship" maxlength="100" pattern="[A-Za-z\s]+" oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '')" value="<?php echo htmlspecialchars($formData['citizenship'] ?? ''); ?>" placeholder="Filipino" required>
                                        <label for="citizenship" class="form-label required-field">Citizenship</label>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label required-field">Voter Status</label>
                                        <div class="form-check-group-horizontal">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="voter_status" id="voter" value="voter" <?php echo ($formData['voter_status'] ?? '') === 'voter' ? 'checked' : ''; ?> required>
                                                <label class="form-check-label" for="voter">Voter</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="voter_status" id="non-voter" value="non-voter" <?php echo ($formData['voter_status'] ?? '') === 'non-voter' ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="non-voter">Non-voter</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Details -->
                                <h5 class="section-title">Details</h5>
                                <div class="name-input-group">
                                    <div class="form-group">
                                        <input type="number" class="form-control" id="salary" name="salary" min="0" step="0.01" placeholder="15000.00" value="<?php echo htmlspecialchars($formData['salary'] ?? ''); ?>">
                                        <label for="salary" class="form-label">Monthly Salary</label>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="occupation" name="occupation" oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '')" maxlength="100" pattern="[A-Za-z\s]+" value="<?php echo htmlspecialchars($formData['occupation'] ?? ''); ?>" placeholder="Teacher" required>
                                        <label for="occupation" class="form-label required-field">Occupation</label>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label required-field">Person with Disability (PWD)?</label>
                                        <div class="form-check-group-horizontal">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="disability" id="without-disability" value="Without Disability" <?php echo ($formData['disability'] ?? '') === 'Without Disability' ? 'checked' : ''; ?> required>
                                                <label class="form-check-label" for="without-disability">No</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="disability" id="with-disability" value="With Disability" <?php echo ($formData['disability'] ?? '') === 'With Disability' ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="with-disability">Yes</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label required-field">Bedridden</label>
                                        <div class="form-check-group-horizontal">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="bedridden" id="bedridden-no" value="No" <?php echo ($formData['bedridden'] ?? '') === 'No' ? 'checked' : ''; ?> required>
                                                <label class="form-check-label" for="bedridden-no">No</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="bedridden" id="bedridden-yes" value="Yes" <?php echo ($formData['bedridden'] ?? '') === 'Yes' ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="bedridden-yes">Yes</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Assistance Requirement -->
                                <div id="need-assistance-section" style="display: none;">
                                    <h5 class="section-title">Assistance Requirement</h5>
                                    <div class="name-input-group">
                                        <div class="form-group">
                                            <label class="form-label required-field">Need Assistance?</label>
                                            <div class="form-check-group-horizontal">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="need_assistance" id="need-assistance-no" value="No" <?php echo ($formData['need_assistance'] ?? '') === 'No' ? 'checked' : ''; ?> required>
                                                    <label class="form-check-label" for="need-assistance-no">No</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="need_assistance" id="need-assistance-yes" value="Yes" <?php echo ($formData['need_assistance'] ?? '') === 'Yes' ? 'checked' : ''; ?>>
                                                    <label class="form-check-label" for="need-assistance-yes">Yes</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Assistance Information -->
                                <div id="assistance-section" style="display: none;">
                                    <h5 class="section-title">Assistance Information</h5>
                                    <div class="name-input-group">
                                        <div class="form-group">
                                            <select class="form-select" id="relationship" name="relationship" onchange="toggleGuidedByFields()">
                                                <option value="" selected disabled>Select Relationship</option>
                                                <option value="parent" <?php echo ($formData['relationship'] ?? '') === 'parent' ? 'selected' : ''; ?>>Parent</option>
                                                <option value="guardian" <?php echo ($formData['relationship'] ?? '') === 'guardian' ? 'selected' : ''; ?>>Guardian</option>
                                                <option value="representative" <?php echo ($formData['relationship'] ?? '') === 'representative' ? 'selected' : ''; ?>>Representative</option>
                                            </select>
                                            <label for="relationship" class="form-label required-field">Guided By</label>
                                        </div>
                                        <div class="form-group" id="assisted-by-field" style="display: <?php echo !empty($formData['relationship']) ? 'block' : 'none'; ?>;">
                                            <input type="text" class="form-control" id="assisted-by" name="assisted_by" maxlength="100" pattern="[A-Za-z\s]+" oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '')" value="<?php echo htmlspecialchars($formData['assisted_by'] ?? ''); ?>" placeholder="Maria Dela Cruz">
                                            <label for="assisted-by" class="form-label required-field">Assisted By Full Name</label>
                                            <p class="form-note">e.g., Juan Dela Cruz</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- File Upload Section: Proof of Identification -->
                                <h5 class="section-title">Identification</h5>
                                <div class="file-upload-container">
                                    <label class="file-upload-label" for="proofOfIdentification">
                                        <strong>Proof of Identification</strong>
                                        <span class="required-field"></span>
                                        <p class="form-note">Upload a photo with your valid ID (face visible).</p>
                                        <img src="../../img/icons/attachIMG.png" alt="Upload Image">
                                    </label>
                                    <input id="proofOfIdentification" type="file" name="id_image" style="display: none;" accept=".jpg, .jpeg, .png" required>
                                    <div id="filePreview" class="image-preview-container"></div>
                                    <?php if (isset($_SESSION['form_data']['id_image']) && !empty($_SESSION['form_data']['id_image'])) : ?>
                                    <p class="form-note text-warning">Previously uploaded. Re-upload if needed.</p>
                                    <?php endif; ?>
                                </div>

                                <!-- File Upload Section: Guided By ID -->
                                <div id="guided-by-id-fields" class="file-upload-container" style="display: none;">
                                    <label class="file-upload-label" for="guidedById">
                                        <strong>Guided By Government ID</strong>
                                        <span class="required-field"></span>
                                        <p class="form-note">Upload a valid government-issued ID.</p>
                                        <img src="../../img/icons/attachIMG.png" alt="Upload Image">
                                    </label>
                                    <input id="guidedById" type="file" name="guided_by_id" style="display: none;" accept=".jpg, .jpeg, .png">
                                    <div id="guidedByIdPreview" class="image-preview-container"></div>
                                    <?php if (isset($_SESSION['form_data']['guided_by_id']) && !empty($_SESSION['form_data']['guided_by_id'])) : ?>
                                    <p class="form-note text-warning">Previously uploaded. Re-upload if needed.</p>
                                    <?php endif; ?>
                                </div>

                                <!-- Form Links -->
                                <div class="form-links">
                                    <p>Already have an account? <a href="login.php">LOGIN</a></p>
                                </div>

                                <!-- Buttons -->
                                <div class="d-grid">
                                    <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#submitFormModal">SUBMIT</button>
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#clearFormModal">CLEAR ALL ENTRIES</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Clear Form Confirmation Modal -->
    <div class="modal fade" id="clearFormModal" tabindex="-1" aria-labelledby="clearFormModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="clearFormModalLabel">Confirm Clear Form</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to clear all the form entries? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmClearBtn">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Form Confirmation Modal -->
    <div class="modal fade" id="submitFormModal" tabindex="-1" aria-labelledby="submitFormModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="submitFormModalLabel">Confirm Submission</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure all the information you’ve entered is correct? Please review your entries before submitting.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Go Back</button>
                    <button type="button" class="btn btn-primary" id="confirmSubmitBtn">Proceed with Submission</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row g-4 position-relative">
                <div class="col-md-6 col-lg-3">
                    <h3 class="footer-title">Our Location</h3>
                    <div class="footer-map">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d7723.078039433515!2d120.999401!3d14.568334!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397c99d33cee621%3A0xb5e2f10c0095d856!2s1194%20Florentino%20Torres%2C%20San%20Andres%20Bukid%2C%20Manila%2C%20Metro%20Manila!5e0!3m2!1sen!2sph!4v1743304696194!5m2!1sen!2sph" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Barangay Location"></iframe>
                    </div>
                    <div class="footer-contact-item">
                        <i class="bi bi-geo-alt footer-icon"></i>
                        <span>1200 Espiritu Street, Corner Florentino Torres Street, Sungalong, Malate, Manila, Metro Manila</span>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <h3 class="footer-title">Contact Us</h3>
                    <div class="footer-contact-item">
                        <i class="bi bi-envelope footer-icon"></i>
                        <a href="mailto:barangay752@example.com" class="footer-link">barangay752@example.com</a>
                    </div>
                    <div class="footer-contact-item">
                        <i class="bi bi-telephone footer-icon"></i>
                        <a href="tel:+63281234567" class="footer-link">+63 (2) 8123 4567</a>
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
                <div class="footer-logo-container">
                    <div class="footer-logos">
                        <img src="../../img/lunsgodNgMayNilaLogo.png" class="footer-logo-img" alt="Seal of Lungsod ng Maynila">
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

    <script>
        function calculateAge(birthDate) {
            const today = new Date();
            const birth = new Date(birthDate);
            let age = today.getFullYear() - birth.getFullYear();
            const monthDiff = today.getMonth() - birth.getMonth();
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
                age--;
            }
            return age;
        }

        function validatePhoneNumber(input) {
            console.log('validatePhoneNumber called');
            let value = input.value.replace(/[^0-9]/g, '');
            if (!value.startsWith('09')) {
                value = '09' + value.substring(2);
            }
            if (value.length > 11) {
                value = value.substring(0, 11);
            }
            input.value = value;
            input.setCustomValidity('');
            if (value.length === 11) {
                console.log('Checking phone duplicate for:', value);
                checkPhoneDuplicate(value);
            } else {
                const errorDiv = document.getElementById('contactNo-error');
                input.classList.remove('is-invalid');
                errorDiv.textContent = '';
                errorDiv.style.display = 'none';
                console.log('Phone input less than 11 digits, clearing error');
            }
        }

        function checkPhoneDuplicate(value) {
            if (!value) {
                console.log('No phone value provided, skipping check');
                return;
            }
            console.log('Sending AJAX for cellphone_number:', value);
            $.ajax({
                url: '',
                method: 'POST',
                data: { field: 'cellphone_number', value: value },
                dataType: 'json',
                success: function(response) {
                    console.log('Phone AJAX response:', response);
                    const input = document.getElementById('contactNo');
                    const errorDiv = document.getElementById('contactNo-error');
                    console.log('Phone error div found:', errorDiv);
                    if (response && response.exists) {
                        console.log('Phone duplicate found, showing error:', response.message);
                        input.classList.add('is-invalid');
                        errorDiv.textContent = response.message || 'Cellphone number already exists';
                        errorDiv.style.display = 'block';
                        errorDiv.style.color = '#dc3545';
                        errorDiv.style.fontSize = '0.875em';
                        errorDiv.style.marginTop = '0.25rem';
                    } else {
                        console.log('No phone duplicate, clearing error');
                        input.classList.remove('is-invalid');
                        errorDiv.textContent = '';
                        errorDiv.style.display = 'none';
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Phone AJAX error:', status, error, xhr.responseText);
                }
            });
        }

        function checkEmailDuplicate(value) {
            if (!value) {
                console.log('No email value provided, skipping check');
                return;
            }
            console.log('Sending AJAX for email:', value);
            $.ajax({
                url: '',
                method: 'POST',
                data: { field: 'email', value: value },
                dataType: 'json',
                success: function(response) {
                    console.log('Email AJAX response:', response);
                    const input = document.getElementById('email');
                    const errorDiv = document.getElementById('email-error');
                    console.log('Email error div found:', errorDiv);
                    if (response && response.exists) {
                        console.log('Email duplicate found, showing error:', response.message);
                        input.classList.add('is-invalid');
                        errorDiv.textContent = response.message || 'Email already exists';
                        errorDiv.style.display = 'block';
                        errorDiv.style.color = '#dc3545';
                        errorDiv.style.fontSize = '0.875em';
                        errorDiv.style.marginTop = '0.25rem';
                    } else {
                        console.log('No email duplicate, clearing error');
                        input.classList.remove('is-invalid');
                        errorDiv.textContent = '';
                        errorDiv.style.display = 'none';
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Email AJAX error:', status, error, xhr.responseText);
                }
            });
        }

        function toggleGuidedByFields() {
            const relationship = document.getElementById('relationship').value;
            const assistedByField = document.getElementById('assisted-by-field');
            assistedByField.style.display = relationship ? 'block' : 'none';
        }

        function toggleFields() {
            const disabilityRadios = document.getElementsByName('disability');
            const needAssistanceRadios = document.getElementsByName('need_assistance');
            const bedriddenRadios = document.getElementsByName('bedridden');
            const birthDate = document.getElementById('birth-date').value;
            const assistanceSection = document.getElementById('assistance-section');
            const guidedByIdFields = document.getElementById('guided-by-id-fields');
            const oscaSection = document.getElementById('osca-section');
            const needAssistanceSection = document.getElementById('need-assistance-section');

            let disability = '';
            let needAssistance = '';
            let bedridden = '';
            let age = null;

            for (const radio of disabilityRadios) {
                if (radio.checked) {
                    disability = radio.value;
                    break;
                }
            }
            for (const radio of needAssistanceRadios) {
                if (radio.checked) {
                    needAssistance = radio.value;
                    break;
                }
            }
            for (const radio of bedriddenRadios) {
                if (radio.checked) {
                    bedridden = radio.value;
                    break;
                }
            }
            if (birthDate) {
                age = calculateAge(birthDate);
            }

            oscaSection.style.display = age !== null && age >= 60 ? 'block' : 'none';
            needAssistanceSection.style.display = (disability === 'With Disability' && age !== null && age > 12 && bedridden !== 'Yes') ? 'block' : 'none';

            let showAssistance = false;
            if (age !== null && age <= 12) {
                showAssistance = true;
            } else if (bedridden === 'Yes') {
                showAssistance = true;
            } else if (disability === 'With Disability' && needAssistance === 'Yes') {
                showAssistance = true;
            }

            assistanceSection.style.display = showAssistance ? 'block' : 'none';
            guidedByIdFields.style.display = showAssistance ? 'block' : 'none';
        }

        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded');
            toggleFields();
            document.getElementById('birth-date').addEventListener('change', toggleFields);
            document.getElementById('relationship').addEventListener('change', toggleGuidedByFields);
            const disabilityRadios = document.getElementsByName('disability');
            const needAssistanceRadios = document.getElementsByName('need_assistance');
            const bedriddenRadios = document.getElementsByName('bedridden');
            for (const radio of disabilityRadios) {
                radio.addEventListener('change', toggleFields);
            }
            for (const radio of needAssistanceRadios) {
                radio.addEventListener('change', toggleFields);
            }
            for (const radio of bedriddenRadios) {
                radio.addEventListener('change', toggleFields);
            }

            document.getElementById('email').addEventListener('blur', function() {
                console.log('Email blur event triggered');
                checkEmailDuplicate(this.value);
            });
            document.getElementById('contactNo').addEventListener('input', function() {
                console.log('ContactNo input event triggered');
                validatePhoneNumber(this);
            });

            // Modal Button Handlers
            document.getElementById('confirmClearBtn').addEventListener('click', function() {
                const form = document.getElementById('registrationForm');
                form.reset();
                toggleFields(); // Re-evaluate field visibility after reset
                document.getElementById('filePreview').innerHTML = '';
                document.getElementById('guidedByIdPreview').innerHTML = '';
                const clearModal = bootstrap.Modal.getInstance(document.getElementById('clearFormModal'));
                clearModal.hide();
            });

            document.getElementById('confirmSubmitBtn').addEventListener('click', function() {
                document.getElementById('registrationForm').submit();
                const submitModal = bootstrap.Modal.getInstance(document.getElementById('submitFormModal'));
                submitModal.hide();
            });

            // Navbar and mobile menu
            const navbar = document.getElementById('mainNavbar');
            const mobileMenuButton = document.getElementById('mobileMenuButton');
            const mobileMenu = document.getElementById('mobileMenu');

            window.addEventListener('scroll', function() {
                if (window.scrollY > 10) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            });

            mobileMenuButton.addEventListener('click', function() {
                if (mobileMenu.style.display === 'none' || !mobileMenu.style.display) {
                    mobileMenu.style.display = 'block';
                    this.innerHTML = '<i class="bi bi-x-lg"></i>';
                } else {
                    mobileMenu.style.display = 'none';
                    this.innerHTML = '<span class="navbar-toggler-icon"></span>';
                }
            });

            const mobileLinks = document.querySelectorAll('.mobile-nav-link');
            mobileLinks.forEach(link => {
                link.addEventListener('click', function() {
                    mobileMenu.style.display = 'none';
                    mobileMenuButton.innerHTML = '<span class="navbar-toggler-icon"></span>';
                });
            });

            // Set copyright year
            document.getElementById('copyrightYear').textContent = new Date().getFullYear();
        });

        document.getElementById('proofOfIdentification').addEventListener('change', function(e) {
            const preview = document.getElementById('filePreview');
            preview.innerHTML = '';
            if (e.target.files[0]) {
                const file = e.target.files[0];
                const fileNameDisplay = document.createElement('div');
                fileNameDisplay.className = 'file-info';
                fileNameDisplay.innerHTML = `
                    <i class="bi bi-file-earmark-image"></i>
                    <span class="file-name">${file.name}</span>
                    <span class="file-size">(${(file.size / 1024).toFixed(1)} KB)</span>
                `;
                preview.appendChild(fileNameDisplay);
                if (file.type.startsWith('image/')) {
                    const img = document.createElement('img');
                    img.src = URL.createObjectURL(file);
                    img.className = 'preview-image';
                    img.style.maxHeight = '150px';
                    preview.appendChild(img);
                }
            }
        });

        document.getElementById('guidedById').addEventListener('change', function(e) {
            const preview = document.getElementById('guidedByIdPreview');
            preview.innerHTML = '';
            if (e.target.files[0]) {
                const file = e.target.files[0];
                const fileNameDisplay = document.createElement('div');
                fileNameDisplay.className = 'file-info';
                fileNameDisplay.innerHTML = `
                    <i class="bi bi-file-earmark-image"></i>
                    <span class="file-name">${file.name}</span>
                    <span class="file-size">(${(file.size / 1024).toFixed(1)} KB)</span>
                `;
                preview.appendChild(fileNameDisplay);
                if (file.type.startsWith('image/')) {
                    const img = document.createElement('img');
                    img.src = URL.createObjectURL(file);
                    img.className = 'preview-image';
                    img.style.maxHeight = '150px';
                    preview.appendChild(img);
                }
            }
        });

        function toggleOtherSuffixInput(select) {
            const otherSuffixContainer = document.getElementById('other-suffix-container');
            const otherSuffixInput = document.getElementById('other-suffix');
            if (select.value === 'Others') {
                otherSuffixContainer.style.display = 'block';
                otherSuffixInput.name = 'suffix';
            } else {
                otherSuffixContainer.style.display = 'none';
                otherSuffixInput.name = 'other_suffix';
            }
        }
    </script>
</body>

</html>
<?php
if (!isset($_SESSION['errorMessages'])) {
    unset($_SESSION['form_data']);
}
?>