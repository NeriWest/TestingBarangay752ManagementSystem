<!DOCTYPE html>
<html lang="en">
<?php
// Check if the user is logged in by verifying the 'username' session variable
if (!isset($_SESSION['username'])) {
    // Generate a CSRF token if not already set
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    // Redirect to the login page
    header('Location: ../../public/views/login.php');
    exit(); // Ensure the script stops executing after redirection
}

function getAddress($houseNumber, $street)
{
    $address = $houseNumber . " " .  $street . " Singalong, Malate, Manila";
    return $address;
}
?>

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Profile</title>
    <script src="https://kit.fontawesome.com/4907458c0c.js" crossorigin="anonymous"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Metrophobic&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../css/main.css">
    <link rel="stylesheet" href="../../css/admin/table.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="../../js/add-modal.js" defer></script>
    <script src="../../js/dashBoard.js" defer></script>
    <script src="../../js/main.js" defer></script>
    <script src="../../js/update-modal.js" defer></script>
</head>

<body>
    <!-- MESSAGE -->
    <?php if (isset($_SESSION['message'])): ?>
        <div id="message-box" class="message-box">
            <img src="../../img/icons/checked.png" alt="Info Icon" class="message-icon">
            <p class="message-text"><?php echo htmlspecialchars($_SESSION['message']); ?></p>
            <button class="close-button" onclick="closeMessageBox()">×</button>
        </div>
        <div id="screen-overlay" class="screen-overlay"></div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <style>
        * {
            font-family: Poppins, sans-serif;
        }

        .message-box {
            position: fixed;
            top: 20%;
            left: 50%;
            transform: translate(-50%, -20%);
            background: linear-gradient(135deg, #007BFF, #0056b3);
            color: #f8f9fa;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            gap: 20px;
            z-index: 1001;
            transition: opacity 0.5s ease, transform 0.3s ease;
            font-family: 'Metrophobic', sans-serif;
        }

        .message-box .message-icon {
            width: 36px;
            height: 36px;
        }

        .message-box .message-text {
            margin: 0;
            font-size: 20px;
        }

        .message-box .close-button {
            background: none;
            border: none;
            color: #dee2e6;
            font-size: 26px;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .message-box .close-button:hover {
            color: #f8f9fa;
        }

        .screen-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            opacity: 1;
            transition: opacity 0.5s ease;
        }

        .screen-overlay.hidden {
            opacity: 0;
            pointer-events: none;
        }

        .showcase {
            color: white;
            padding-top: 2rem;
            height: 0px;
        }

        .showcase h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            text-align: center;
        }

        .profile-container {
            max-width: 1000px;
            margin: 2rem auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .tab-buttons {
            display: flex;
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }

        .tab-btn {
            flex: 1;
            padding: 1rem;
            text-align: center;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            color: #495057;
        }

        .tab-btn.active {
            background: white;
            color: #0874ac;
            border-bottom: 3px solid #0874ac;
        }

        .tab-btn:hover:not(.active) {
            background: #e9ecef;
        }

        .tab-content {
            display: none;
            padding: 2rem;
        }

        .tab-content.active {
            display: block;
        }

        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 2rem;
        }

        .profile-image {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid #f0f0f0;
            margin-right: 2rem;
        }

        .profile-info h2 {
            text-align: left;
            margin-bottom: 0.5rem;
            color: #333;
        }

        .profile-info p {
            text-align: left;
            color: #666;
            margin-bottom: 0.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #495057;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 1rem;
            transition: border-color 0.15s ease-in-out;
        }

        .form-control:focus {
            border-color: #6e8efb;
            outline: none;
            box-shadow: 0 0 0 0.2rem rgba(110, 142, 251, 0.25);
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -1rem;
        }

        .col-md-6 {
            flex: 0 0 50%;
            max-width: 50%;
            padding: 0 1rem;
        }

        .col-md-5 {
            flex: 0 0 30%;
            max-width: 30%;
            padding: 0 1rem;
        }

        .col-md-3 {
            flex: 0 0 10%;
            max-width: 50%;
            padding: 0 1rem;
        }


        .status-badge {
            display: inline-block;
            padding: 0.35rem 0.75rem;
            border-radius: 50px;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .status-active {
            background-color: #d4edda;
            color: #155724;
        }

        .status-inactive {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-pending {
            background-color: #cce5ff;
            color: #004085;
        }

        .status-disapproved {
            background-color: #f8d7da;
            color: #721c24;
        }

        .error-message {
            color: red;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: none;
        }

        @media (max-width: 768px) {
            .col-md-6, .col-md-5, .col-md-3 {
                flex: 0 0 100%;
                max-width: 100%;
            }

            .profile-header {
                flex-direction: column;
                text-align: center;
            }

            .profile-image {
                margin-right: 0;
                margin-bottom: 1rem;
            }
        }
    </style>

    <script>
        // Fade out the screen overlay after 5 seconds
        const screenOverlay = document.getElementById('screen-overlay');
        const messageBox = document.getElementById('message-box');

        if (messageBox) {
            setTimeout(() => {
                closeMessageBox();
            }, 5000); // 5 seconds delay
        }

        function closeMessageBox() {
            if (messageBox) {
                messageBox.style.opacity = '0';
            }
            if (screenOverlay) {
                screenOverlay.style.opacity = '0';
            }
            setTimeout(() => {
                if (messageBox) messageBox.remove();
                if (screenOverlay) screenOverlay.remove();
            }, 500); // Remove after fade-out
        }
    </script>

    <main class="main-wrap">
    <header class="main-head active">
                <div class="main-nav">
                    <button class="hamburger">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <nav class="navbar">
                        <div class="navbar-nav">
                            <div class="title">
                                <h1>BRGY. 752</h1>
                            </div>
                            <ul class="nav-list">

                                <!-- HOME -->
                                <a href="../../index.php" class="nav-link">
                                    <li class="nav-list-item">
                                        <i class="fa-solid fa-house"></i>
                                        <span class="link-text">HOME</span>
                                    </li>
                                </a>

                                <!-- DASHBOARD -->
                                <a href="adminDashboardController.php" class="nav-link">
                                    <li class="nav-list-item">
                                        <i class="fa-solid fa-gauge"></i>
                                        <span class="link-text">DASHBOARD</span>
                                    </li>
                                </a>


                                <!-- PEOPLE DROPDOWN -->
                                <li class="nav-list-item" id="resident-link">
                                    <a href="#" class="nav-link">
                                        <i class="fa-solid fa-user"></i>
                                        <span class="link-text">PEOPLE</span>
                                        <span class="link-text">
                                            <i class="fa-solid fa-caret-down" id="resident-dropdown-logo"></i>
                                        </span>
                                        <ul class="resident-dropdown">
                                        <?php if (!empty($_SESSION['permissions']['can_manage_residents'])) : ?>
                                            <li class="dropdown-item"><a href="adminResidentController.php">Resident</a></li>
                                        <?php endif; ?>
                                        <?php if (!empty($_SESSION['permissions']['can_manage_officials'])) : ?>
                                            <li class="dropdown-item"><a href="adminOfficialController.php">Officials</a></li>
                                        <?php endif; ?>
                                            <li class="dropdown-item"><a href="adminFamilyController.php">Family</a></li>     
                                        </ul>
                                    </a>
                                </li>

                                <!-- ACCOUNTS DROPDOWN -->
                                <a href="#" class="nav-link">
                                    <li class="nav-list-item" id="accounts-link">
                                        <i class="fa-solid fa-user-pen"></i>
                                        <span class="link-text">ACCOUNTS</span><span class="link-text"><i
                                                class="fa-solid fa-caret-down" id="accounts-dropdown-logo"></i></span>
                                        <ul class="accounts-dropdown">
                                            <?php if (!empty($_SESSION['permissions']['can_manage_residents'])) : ?>
                                            <li class="dropdown-item"><a href="adminResidentAccountsController.php">Resident Accounts</a></li>
                                            <?php endif; ?>
                                            <?php if (!empty($_SESSION['permissions']['can_manage_officials'])) : ?>
                                            <li class="dropdown-item"><a href="adminOfficialAccountsController.php">Official Accounts</a></li>   
                                            <?php endif; ?>                                 
                                        </ul>
                                    </li>
                                </a>

                                <!-- REQUESTS DROPDOWN -->
                                <a href="#" class="nav-link">
                                    <li class="nav-list-item" id="request-link">
                                        <i class="fa-solid fa-print"></i>
                                        <span class="link-text">REQUEST</span><span class="link-text"><i
                                                class="fa-solid fa-caret-down" id="request-dropdown-logo"></i></span>
                                        <ul class="request-dropdown">
                                            <?php if (!empty($_SESSION['permissions']['can_process_clearances'])) : ?>
                                            <li class="dropdown-item"><a href="adminClearanceController.php">Clearance</a></li>
                                            <?php endif; ?>
                                            <?php if (!empty($_SESSION['permissions']['can_process_certificates'])) : ?>
                                            <li class="dropdown-item"><a href="adminCertificateController.php">Certificate</a></li>
                                            <?php endif; ?>
                                            <?php if (!empty($_SESSION['permissions']['can_process_permits'])) : ?>
                                            <li class="dropdown-item"><a href="adminPermitController.php">Permits</a></li>
                                            <?php endif; ?>
                                        </ul>
                                    </li>
                                </a>

                            <!-- REPORTS DROPDOWN -->
                            <a href="#" class="nav-link">
                                <li class="nav-list-item" id="report-link">
                                    <i class="fa-solid fa-flag"></i>
                                    <span class="link-text">REPORT</span><span class="link-text"><i
                                            class="fa-solid fa-caret-down" id="report-dropdown-logo"></i></span>
                                    <ul class="report-dropdown">
                                        <?php if (!empty($_SESSION['permissions']['can_manage_blotters'])) : ?>
                                        <li class="dropdown-item"><a href="adminBlotterController.php">Blotters</a></li>
                                        <?php endif; ?>
                                        <?php if (!empty($_SESSION['permissions']['can_manage_complaints'])) : ?>
                                        <li class="dropdown-item"><a href="adminComplaintsController.php">Complaints</a></li>
                                        <?php endif; ?>
                                        <?php if (!empty($_SESSION['permissions']['can_manage_incidents'])) : ?>
                                        <li class="dropdown-item"><a href="adminIncidentController.php">Incident</a></li>
                                        <?php endif; ?>
                                    </ul>
                                </li>
                            </a>

                            <!-- ANNOUNCEMENT -->
                            <?php if (!empty($_SESSION['permissions']['can_create_announcements'])) : ?>
                            <a href="adminAnnouncementController.php" class="nav-link">
                                <li class="nav-list-item">
                                    <i class="fa-solid fa-bullhorn"></i>
                                    <span class="link-text">ANNOUNCEMENT</span>
                                </li>
                            </a>
                            <?php endif; ?>

                            <!-- ACTIVITY LOG -->
                            <?php if (!empty($_SESSION['permissions']['can_view_activity_log'])) : ?>
                            <a href="adminActivityLogController.php" class="nav-link">
                                <li class="nav-list-item">
                                    <i class="fa-regular fa-clock"></i>
                                    <span class="link-text">ACTIVITY LOG</span>
                                </li>
                            </a>
                            <?php endif; ?>
                        </ul>

                        <!-- SETTINGS -->
                    <div class="footer-nav-bar"></div>
                        <ul class="nav-list">
                            <li class="nav-list-item">
                                <a href="adminSettingsController.php" class="nav-link">
                                    <i class="fa-solid fa-gear"></i>
                                    <span class="link-text">SETTINGS</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </header>
        <section class="showcase">
            <div class="overlay">
                <nav class="header-nav">
                    <div class="datetime">
                        <div class="day">DAY</div>
                        <div class="date">Date</div>
                        <div class="time">Time</div>
                    </div>
                    <div class="head">
                        <button class="account">
                            <p>
                                <?php
                                if (isset($_SESSION['first_name']) && isset($_SESSION['last_name'])) {
                                    echo htmlspecialchars($_SESSION['first_name']) . " " . htmlspecialchars($_SESSION['last_name']);
                                } else {
                                    echo "Guest!";
                                } ?>
                            </p>
                            <i class="fa-solid fa-user"></i>
                        </button>
                        <div class="floating-menu">
                            <p>You're logged in as</p>
                            <?php echo '<h3>' . htmlspecialchars($personalAccount['username']) . '</h3>' ?>
                            <a href="adminViewProfileController.php">View Profile</a>
                            <a href="../logoutController.php">Log Out</a>
                        </div>
                    </div>
                </nav>
            </div>

            <h1 style="color: black; margin-top: 20px;">Profile Account</h1>

            <div class="profile-container">
                <div class="tab-buttons">
                    <button class="tab-btn active" onclick="openTab('account')">Account Information</button>
                    <button class="tab-btn" onclick="openTab('resident')">Resident Information</button>
                </div>

                <!-- Account Information Tab -->
                <div id="account" class="tab-content active">
                    <div class="profile-header">
                        <?php
                        if (!empty($personalAccount['profile_image_name'])) {
                            echo "<a href='../../img/profile_images/" . htmlspecialchars($personalAccount['profile_image_name'], ENT_QUOTES) . "' target='_blank'>";
                            echo "<img src='../../img/profile_images/" . htmlspecialchars($personalAccount['profile_image_name'], ENT_QUOTES) . "' alt='Profile Image' class='profile-image' id='profileImage'>";
                            echo "</a>";
                        } else {
                            echo "<a href='../../img/id_images/default.jpg' target='_blank'>";
                            echo "<img src='../../img/id_images/default.jpg' alt='Default Profile Image' class='profile-image' id='profileImage'>";
                            echo "</a>";
                        }
                        ?>
                        <div class="profile-info">
                            <?php echo "<h2>" . htmlspecialchars($personalAccount['username']) . "</h2>" ?>
                            <?php echo "<p>" . htmlspecialchars($personalAccount['first_name']) . " " . htmlspecialchars($personalAccount['middle_name']) . " " . htmlspecialchars($personalAccount['last_name']) . "</p>" ?>
                            <?php echo "<p>" . htmlspecialchars($personalAccount['email']) . "</p>" ?>
                            <?php
                            if (isset($personalAccount['account_status'])) {
                                if ($personalAccount['account_status'] === 'active') {
                                    echo '<span class="status-badge status-active">Active</span>';
                                } elseif ($personalAccount['account_status'] === 'inactive') {
                                    echo '<span class="status-badge status-inactive">Inactive</span>';
                                } elseif ($personalAccount['account_status'] === 'pending') {
                                    echo '<span class="status-badge status-pending">Pending</span>';
                                } elseif ($personalAccount['account_status'] === 'disapproved') {
                                    echo '<span class="status-badge status-disapproved">Disapproved</span>';
                                } else {
                                    echo '<span class="status-badge">Unknown</span>';
                                }
                            }
                            ?>
                        </div>
                    </div>

                    <form id="accountForm" action="adminViewProfileController.php" method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="username">Username</label>
                                    <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($personalAccount['username']) ?>" required>
                                    <label id="usernameError" class="error-message" style="color:red;"></label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($personalAccount['email']) ?>">
                                    <label id="emailError" class="error-message" style="color:red;"></label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="currentPassword">Current Password</label>
                                    <div style="position: relative;">
                                        <input type="password" class="form-control" id="currentPassword" name="current-password">
                                        <i class="fa-solid fa-eye" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; color: black;" onclick="togglePasswordVisibility('currentPassword', this)"></i>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="newPassword">New Password</label>
                                    <div style="position: relative;">
                                        <input type="password" class="form-control" id="newPassword" name="new-password">
                                        <i class="fa-solid fa-eye" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; color: black;" onclick="togglePasswordVisibility('newPassword', this)"></i>
                                    </div>
                                    <label id="passwordHelp" style="color: red; display: none;">Your password must be at least 8 characters long and contain at least one number, one uppercase letter, and one lowercase letter.</label>
                                    </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <?php
                                            if (isset($_SESSION['errorMessage'])) {
                                                echo '<label style="color: red;">' . htmlspecialchars($_SESSION['errorMessage']) . '</label>';
                                                unset($_SESSION['errorMessage']);
                                            }
                            ?>
                            <label for="profileImageUpload">Profile Image</label>
                            <input type="file" class="form-control" id="profileImageUpload" name="profileImageUpload" accept="image/*">
                        </div>

                        <div style="margin-top: 2rem; text-align: right;">
                            <button type="submit" class="button" id="accountSubmitBtn">Save Changes</button>
                        </div>
                    </form>
                </div>

                <!-- Resident Information Tab -->
                <div id="resident" class="tab-content">
                    <form id="residentForm" action="adminViewProfileController.php" method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="firstName">First Name</label>
                                    <input type="text" class="form-control" id="firstName" name="firstName" value="<?php echo htmlspecialchars($personalAccount['first_name']) ?>" required readonly>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="middleName">Middle Name</label>
                                    <input type="text" class="form-control" id="middleName" name="middleName" value="<?php echo htmlspecialchars($personalAccount['middle_name']) ?>" required readonly>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="lastName">Last Name</label>
                                    <input type="text" class="form-control" id="lastName" name="lastName" value="<?php echo htmlspecialchars($personalAccount['last_name']) ?>" required readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="suffix">Suffix</label>
                                    <input type="text" class="form-control" id="suffix" name="suffix" value="<?php echo htmlspecialchars($personalAccount['suffix']) ?>" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="civil-status">Civil Status</label>
                                    <select id="civil-status" class="form-control" name="civil-status" required>
                                        <option value="" disabled>Select Civil Status</option>
                                        <option value="Single" <?php echo ($personalAccount['civil_status'] === 'Single') ? 'selected' : ''; ?>>Single</option>
                                        <option value="Married" <?php echo ($personalAccount['civil_status'] === 'Married') ? 'selected' : ''; ?>>Married</option>
                                        <option value="Widowed" <?php echo ($personalAccount['civil_status'] === 'Widowed') ? 'selected' : ''; ?>>Widowed</option>
                                        <option value="Divorced" <?php echo ($personalAccount['civil_status'] === 'Divorced') ? 'selected' : ''; ?>>Divorced</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="citizenship">Citizenship</label>
                                    <input type="text" class="form-control" id="citizenship" name="citizenship" value="<?php echo htmlspecialchars($personalAccount['citizenship']) ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="birthdate">Birthdate</label>
                                    <input type="date" class="form-control" id="birthdate" name="birthdate" value="<?php echo htmlspecialchars($personalAccount['date_of_birth']) ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sex">Sex</label>
                                    <select class="form-control" id="sex" name="sex" required>
                                        <option value="" disabled>Select Sex</option>
                                        <option value="Male" <?php echo ($personalAccount['sex'] === 'Male') ? 'selected' : ''; ?>>Male</option>
                                        <option value="Female" <?php echo ($personalAccount['sex'] === 'Female') ? 'selected' : ''; ?>>Female</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($personalAccount['cellphone_number']) ?>" maxlength="11" onkeypress="return event.charCode >= 48 && event.charCode <= 57" required>
                                    <label id="phoneError" class="error-message" style="color:red;"></label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="occupation">Occupation</label>
                                    <input type="text" class="form-control" id="occupation" name="occupation" value="<?php echo htmlspecialchars($personalAccount['occupation']) ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="houseNumber">House no.</label>
                                    <input type="text" class="form-control" id="houseNumber" name="houseNumber" value="<?php echo htmlspecialchars($personalAccount['house_number']) ?>" max="32">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="street">Street</label>
                                    <input type="text" class="form-control" id="street" name="street" value="<?php echo htmlspecialchars($personalAccount['street']) ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="disability">Disability</label>
                                    <select id="disability" class="form-control" name="disability" required>
                                        <option value="" disabled>Select Disability</option>
                                        <option value="W/O Disability" <?php echo ($personalAccount['disability'] === 'W/O Disability') ? 'selected' : ''; ?>>W/O Disability</option>
                                        <option value="W/ Disability" <?php echo ($personalAccount['disability'] === 'W/ Disability') ? 'selected' : ''; ?>>W/ Disability</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="voterStatus">Voter Status</label>
                                    <select id="voterStatus" class="form-control" name="voter-status" required>
                                        <option value="" disabled>Select Voter Status</option>
                                        <option value="voter" <?php echo ($personalAccount['voter_status'] === 'voter') ? 'selected' : ''; ?>>Voter</option>
                                        <option value="non-voter" <?php echo ($personalAccount['voter_status'] === 'non-voter') ? 'selected' : ''; ?>>Non-Voter</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select id="status" class="form-control" name="status" required>
                                        <option value="" disabled>Select Status</option>
                                        <option value="active" <?php echo ($personalAccount['status'] === 'active') ? 'selected' : ''; ?>>Active</option>
                                        <option value="bedridden" <?php echo ($personalAccount['status'] === 'bedridden') ? 'selected' : ''; ?>>Bedridden</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3" readonly style="resize: none;"><?php echo htmlspecialchars(getAddress($personalAccount['house_number'], $personalAccount['street'])); ?></textarea>
                        </div>

                        <div style="margin-top: 2rem; text-align: right;">
                            <button type="submit" class="button" id="residentSubmitBtn">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <script>
        // Tab switching functionality with saved state
        function openTab(tabName) {
            localStorage.setItem('selectedTab', tabName);
            const tabContents = document.getElementsByClassName('tab-content');
            for (let i = 0; i < tabContents.length; i++) {
                tabContents[i].classList.remove('active');
            }
            const tabButtons = document.getElementsByClassName('tab-btn');
            for (let i = 0; i < tabButtons.length; i++) {
                tabButtons[i].classList.remove('active');
            }
            document.getElementById(tabName).classList.add('active');
            event.currentTarget.classList.add('active');
        }

        // Load the saved tab on page load
        document.addEventListener('DOMContentLoaded', function() {
            const savedTab = localStorage.getItem('selectedTab') || 'account';
            const savedTabButton = document.querySelector(`.tab-btn[onclick="openTab('${savedTab}')"]`);
            if (savedTabButton) {
                savedTabButton.click();
            }
        });

        // Image preview functionality for profile image
        document.getElementById('profileImageUpload').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    document.getElementById('profileImage').src = event.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

        // Password visibility toggle
        function togglePasswordVisibility(inputId, icon) {
            const input = document.getElementById(inputId);
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                input.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }

        // Password validation
        document.getElementById('newPassword').addEventListener('input', function() {
            const password = this.value;
            const passwordHelp = document.getElementById('passwordHelp');
            const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;

            if (password && !passwordPattern.test(password)) {
                passwordHelp.style.display = 'block';
            } else {
                passwordHelp.style.display = 'none';
            }
        });

        // Duplicate checker for username, email, and phone
        let usernameValid = true;
        let emailValid = true;
        let phoneValid = true;

        function checkDuplicate(field, value, errorElementId, submitBtnId) {
            if (!value) return;
            $.ajax({
                url: '../check_duplicate_view_profile.php',
                type: 'POST',
                data: { field: field, value: value },
                dataType: 'json',
                success: function(response) {
                    const errorElement = document.getElementById(errorElementId);
                    const submitBtn = document.getElementById(submitBtnId);
                    if (response.exists) {
                        errorElement.textContent = response.message;
                        errorElement.style.display = 'block';
                        submitBtn.disabled = true;
                        if (field === 'username') usernameValid = false;
                        if (field === 'email') emailValid = false;
                        if (field === 'cellphone_number') phoneValid = false;
                    } else {
                        errorElement.textContent = '';
                        errorElement.style.display = 'none';
                        if (field === 'username') usernameValid = true;
                        if (field === 'email') emailValid = true;
                        if (field === 'cellphone_number') phoneValid = true;
                        if (usernameValid && emailValid && phoneValid) {
                            submitBtn.disabled = false;
                        }
                    }
                },
                error: function() {
                    const errorElement = document.getElementById(errorElementId);
                    errorElement.textContent = 'Error checking duplicate';
                    errorElement.style.display = 'block';
                }
            });
        }

        // Username duplicate check
        let originalUsername = '<?php echo htmlspecialchars($personalAccount['username']); ?>';
        document.getElementById('username').addEventListener('blur', function() {
            const username = this.value;
            if (username && username !== originalUsername) {
                checkDuplicate('username', username, 'usernameError', 'accountSubmitBtn');
            } else {
                document.getElementById('usernameError').style.display = 'none';
                usernameValid = true;
                if (usernameValid && emailValid && phoneValid) {
                    document.getElementById('accountSubmitBtn').disabled = false;
                }
            }
        });

        // Email duplicate check
        let originalEmail = '<?php echo htmlspecialchars($personalAccount['email']); ?>';
        document.getElementById('email').addEventListener('blur', function() {
            const email = this.value;
            if (email && email !== originalEmail) {
                checkDuplicate('email', email, 'emailError', 'accountSubmitBtn');
            } else {
                document.getElementById('emailError').style.display = 'none';
                emailValid = true;
                if (usernameValid && emailValid && phoneValid) {
                    document.getElementById('accountSubmitBtn').disabled = false;
                }
            }
        });

        // Phone duplicate check
        let originalPhone = '<?php echo htmlspecialchars($personalAccount['cellphone_number']); ?>';
        document.getElementById('phone').addEventListener('blur', function() {
            const phone = this.value;
            if (phone && phone !== originalPhone) {
                checkDuplicate('cellphone_number', phone, 'phoneError', 'residentSubmitBtn');
            } else {
                document.getElementById('phoneError').style.display = 'none';
                phoneValid = true;
                if (usernameValid && emailValid && phoneValid) {
                    document.getElementById('residentSubmitBtn').disabled = false;
                }
            }
        });

        // Form submission validation
        document.getElementById('accountForm').addEventListener('submit', function(e) {
            const password = document.getElementById('newPassword').value;
            const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;
            if (password && !passwordPattern.test(password)) {
                e.preventDefault();
                alert('Please ensure your password meets the requirements.');
                return;
            }
            if (!usernameValid || !emailValid) {
                e.preventDefault();
                alert('Please resolve the username or email duplication issue.');
            }
        });

        document.getElementById('residentForm').addEventListener('submit', function(e) {
            if (!phoneValid) {
                e.preventDefault();
                alert('Please resolve the phone number duplication issue.');
            }
        });
    </script>
</body>
</html>