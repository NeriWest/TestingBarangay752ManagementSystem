<!DOCTYPE html>
<html lang="en">
<?php
session_start();
require_once '../../config/sessionCheck.php';

$allowedRoles = ['Admin', 'Chairman', 'Secretary', 'Official'];
$roleName = $_SESSION['role_name'] ?? null;
if (!in_array($roleName, $allowedRoles)) {
    header('Location: ../../public/views/login.php');
    exit();
}
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

// If the user is logged in, proceed with the rest of your code
?>

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Resident Accounts</title>
    <script src="https://kit.fontawesome.com/4907458c0c.js" crossorigin="anonymous"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Metrophobic&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../../css/main.css">
    <link rel="stylesheet" href="../../css/admin/table.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="../../js/main.js" defer></script>
    <script src="../../js/add-modal.js" defer></script>
    <script src="../../js/update-modal.js" defer></script>
    <script src="../../js/dashBoard.js" defer></script>
    <style>
        table {
            border-collapse: collapse;
            font-size: 15px;
            width: auto;
            table-layout: auto;
            width: 100%;
        }
        /* FORM WIDTH DESIGN/TEMPORARY */
        .modal-add {
            max-width: 750px;
            width: 100%;
        }

        .modal {
            max-width: 750px;
            width: 100%;
        }
    </style>
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
        .message-box {
            position: fixed;
            top: 20%;
            left: 50%;
            transform: translate(-50%, -20%);
            background-color: rgba(178, 176, 192, 0.9);
            color: white;
            padding: 30px 40px;
            border-radius: 12px;
            background: linear-gradient(135deg, #007BFF, #0056b3);
            color: #f8f9fa;
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
                                <a href="/Barangay752ManagementSystem/controller/admin/adminSettingsController.php" class="nav-link">
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
                                <i class="fa-solid fa-user"></i>
                                <?php
                                if (isset($_SESSION['first_name']) && isset($_SESSION['last_name'])) {
                                    echo htmlspecialchars($_SESSION['first_name']) . " " . htmlspecialchars($_SESSION['last_name']);
                                } else {
                                    echo "Guest!";
                                } ?>
                            </p>
                        </button>
                        <div class="floating-menu">
                            <p>You're logged in as</p>
                            <?php echo '<h3>' . ($_SESSION['username']) . '</h3>' ?>
                            <a href="adminViewProfileController.php">View Profile</a>
                            <a href="../logoutController.php">Log Out</a>
                        </div>
                    </nav>
                </div>
                <div class="content" id="table-content">
                    <h1 id="table-title" style="font-size: 30px; margin-bottom: 20px">Resident Accounts List</h1>
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <td colspan="13" class="search-column">
                                        <div class="search-bar">
                                            <p>Search</p>
                                            <input type="text" placeholder="Search Resident" id="searchInput">
                                            <?php
                                            $pendingCount = 0;
                                            $activeCount = 0;
                                            $disapprovedCount = 0;
                                            $inactiveCount = 0;

                                            if (!empty($accounts)) {
                                                foreach ($accounts as $account) {
                                                    switch ($account['status']) {
                                                        case 'pending':
                                                            $pendingCount++;
                                                            break;
                                                        case 'active':
                                                            $activeCount++;
                                                            break;
                                                        case 'disapproved':
                                                            $disapprovedCount++;
                                                            break;
                                                        case 'inactive':
                                                            $inactiveCount++;
                                                            break;
                                                    }
                                                }
                                            }
                                            ?>

                                            <div class="status-summary" style="display: flex; gap: 20px; align-items: center; margin-left: 20px;">
                                                <div style="display: flex; align-items: center; gap: 10px;">
                                                    <span style="
                                                        background-color: orange; 
                                                        color: white; 
                                                        padding: 5px 10px; 
                                                        border-radius: 5px; 
                                                        font-weight: bold;
                                                        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);">
                                                        Pending
                                                    </span>
                                                    <p style="font-size: 18px; font-weight: bold;"><?php echo $pendingCount; ?></p>
                                                </div>
                                                <div style="display: flex; align-items: center; gap: 10px;">
                                                    <span style="
                                                        background-color: green; 
                                                        color: white; 
                                                        padding: 5px 10px; 
                                                        border-radius: 5px; 
                                                        font-weight: bold;
                                                        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);">
                                                        Active
                                                    </span>
                                                    <p style="font-size: 18px; font-weight: bold;"><?php echo $activeCount; ?></p>
                                                </div>
                                                <div style="display: flex; align-items: center; gap: 10px;">
                                                    <span style="
                                                        background-color: red; 
                                                        color: white; 
                                                        padding: 5px 10px; 
                                                        border-radius: 5px; 
                                                        font-weight: bold;
                                                        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);">
                                                        Disapproved
                                                    </span>
                                                    <p style="font-size: 18px; font-weight: bold;"><?php echo $disapprovedCount; ?></p>
                                                </div>
                                                <div style="display: flex; align-items: center; gap: 10px;">
                                                    <span style="
                                                        background-color: grey; 
                                                        color: white; 
                                                        padding: 5px 10px; 
                                                        border-radius: 5px; 
                                                        font-weight: bold;
                                                        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);">
                                                        Inactive
                                                    </span>
                                                    <p style="font-size: 18px; font-weight: bold;"><?php echo $inactiveCount; ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Account ID</th>
                                    <th>Username</th>
                                    <th>Full Name</th>
                                    <th>Mobile No.</th>
                                    <th>Date of Birth</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>ID Image</th>
                                    <th>Relationship</th>
                                    <th>Assisted ID Image</th>
                                    <th>Assisted Name</th>
                                    <th>Profile Image</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (!empty($accounts)) {
                                    foreach ($accounts as $account) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($account['account_id']) . "</td>"; // Account ID
                                        echo "<td>" . htmlspecialchars($account['username']) . "</td>"; // Username

                                        // Full Name (Concatenated)
                                        $fullName = htmlspecialchars($account['first_name'] . " " . ($account['middle_name'] ? $account['middle_name'] . " " : "") . $account['last_name'] . ($account['suffix'] ? ", " . $account['suffix'] : ""));
                                        echo "<td>" . $fullName . "</td>";
                                        echo "<td>" . htmlspecialchars($account['cellphone_number']) . "</td>"; // Mobile No.
                                        echo "<td>" . htmlspecialchars($account['date_of_birth']) . "</td>"; // Date of Birth
                                        echo "<td>" . (!empty($account['email']) ? htmlspecialchars($account['email']) : 'N/A') . "</td>"; // Email

                                        $statusColor = ($account['status'] === 'active') ? 'green' : (($account['status'] === 'pending') ? 'orange' : (($account['status'] === 'inactive') ? 'gray' : (($account['status'] === 'disapproved') ? 'red' : 'black')));

                                        echo "<td><span style='
                                            background-color: $statusColor; 
                                            color: white; 
                                            padding: 5px 10px; 
                                            border-radius: 5px; 
                                            font-weight: bold; 
                                            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3); 
                                            text-shadow: -1px -1px 0 black, 
                                                1px -1px 0 black, 
                                                -1px 1px 0 black, 
                                                1px 1px 0 black;'>
                                            " . htmlspecialchars($account['status']) . "</span></td>";

                                        // ID Image
                                        echo "<td>";
                                        if (!empty($account['id_image_name'])) {
                                            echo "<a href='../../img/id_images/" . htmlspecialchars($account['id_image_name'], ENT_QUOTES) . "' target='_blank'>";
                                            echo "<img src='../../img/id_images/" . htmlspecialchars($account['id_image_name'], ENT_QUOTES) . "' alt='ID Image' width='50' height='50' style='margin-right:5px;'>";
                                            echo "</a>";
                                        } else {
                                            echo "No image";
                                        }
                                        echo "</td>";

                                        echo "<td>" . htmlspecialchars($account['relationship']) . "</td>"; // Relationship

                                        // Assisted ID Image
                                        echo "<td>";
                                        if (!empty($account['guided_by_id_name'])) {
                                            echo "<a href='../../img/guided_by_id_images/" . htmlspecialchars($account['guided_by_id_name'], ENT_QUOTES) . "' target='_blank'>";
                                            echo "<img src='../../img/guided_by_id_images/" . htmlspecialchars($account['guided_by_id_name'], ENT_QUOTES) . "' alt='Guided By Image' width='50' height='50' style='margin-right:5px;'>";
                                            echo "</a>";
                                        } else {
                                            echo "No image";
                                        }
                                        echo "</td>";

                                        echo "<td>" . (!empty($account['assisted_by']) ? htmlspecialchars($account['assisted_by']) : 'N/A') . "</td>"; // Assisted Name

                                        // Profile Image
                                        echo "<td>";
                                        if (!empty($account['profile_image_name'])) {
                                            echo "<a href='../../img/profile_images/" . htmlspecialchars($account['profile_image_name'], ENT_QUOTES) . "' target='_blank'>";
                                            echo "<img src='../../img/profile_images/" . htmlspecialchars($account['profile_image_name'], ENT_QUOTES) . "' alt='Profile Image' width='50' height='50' style='margin-right:5px;'>";
                                            echo "</a>";
                                        } else {
                                            echo "No image";
                                        }
                                        echo "</td>";

                                        // Actions Button
                                        echo '<td>';
                                        echo '<div style="display: flex; gap: 5px;">';

                                        if ($account['status'] === 'pending') {
                                            echo '
                                                <button onclick="showApprovePopup(\'' . htmlspecialchars($account['account_id'], ENT_QUOTES) . '\')" class="buttons" style="background-color: #28a745; color: white; border: none; padding: 8px; border-radius: 5px; cursor: pointer; display: flex; align-items: center; justify-content: center; width: 32px; height: 32px;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-check-circle" viewBox="0 0 16 16">
                                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                                        <path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z"/>
                                                    </svg>
                                                </button>
                                                <button onclick="showDisapprovePopup(\'' . htmlspecialchars($account['account_id'], ENT_QUOTES) . '\')" class="buttons" style="background-color: #dc3545; color: white; border: none; padding: 8px; border-radius: 5px; cursor: pointer; display: flex; align-items: center; justify-content: center; width: 32px; height: 32px;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-x-circle" viewBox="0 0 16 16">
                                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                                    </svg>
                                                </button>';
                                        }
                                        echo '
                                            <button class="modal-open button" 
                                                aria-haspopup="true"
                                                data-account-id="' . htmlspecialchars($account['account_id'], ENT_QUOTES) . '"
                                                data-resident-id="' . htmlspecialchars($account['resident_id'], ENT_QUOTES) . '"
                                                data-username="' . htmlspecialchars($account['username'], ENT_QUOTES) . '"
                                                data-first-name="' . htmlspecialchars($account['first_name'], ENT_QUOTES) . '"
                                                data-middle-name="' . htmlspecialchars($account['middle_name'], ENT_QUOTES) . '"
                                                data-last-name="' . htmlspecialchars($account['account_id'], ENT_QUOTES) . '"
                                                data-email="' . htmlspecialchars($account['email'], ENT_QUOTES) . '"
                                                data-mobile-no="' . htmlspecialchars($account['cellphone_number'] ?? '', ENT_QUOTES) . '"
                                                data-privilege="' . htmlspecialchars($account['role_id'], ENT_QUOTES) . '"
                                                data-status="' . htmlspecialchars($account['status'], ENT_QUOTES) . '"
                                                data-profile-image="' . (!empty($account['profile_image']) ? 'uploads/' . htmlspecialchars($account['profile_image'], ENT_QUOTES) : 'default-profile.png') . '"
                                                data-id-image="' . (!empty($account['id_image']) ? 'uploads/' . htmlspecialchars($account['id_image'], ENT_QUOTES) : 'default-id.png') . '"
                                                onclick="populateModal(this)">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-pen" viewBox="0 0 16 16">
                                                    <path d="m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001m-.644.766a.5.5 0 0 0-.707 0L1.95 11.756l-.764 3.057 3.057-.764L14.44 3.854a.5.5 0 0 0 0-.708z"/>
                                                </svg>
                                            </button>
                                        ';
                                        echo '</div>';
                                        echo '</td>';
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='13'>No accounts found.</td></tr>";
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="13" id="paginationColspan">
                                        <?php if ($showPagination) { ?>
                                            <div class="pagination" style="display: flex; justify-content: center; align-items: center; margin-top: 20px;">
                                                <!-- Previous Button -->
                                                <?php if ($page > 1) { ?>
                                                    <a href="?page=<?php echo $page - 1; ?>" class="button" style="padding: 10px 15px; border: none; background-color: #007bff; color: white; cursor: pointer; margin-right: 10px; border-radius: 5px;">Previous</a>
                                                <?php } else { ?>
                                                    <span class="button disabled" style="padding: 10px 15px; border: none; background-color: grey; color: white; cursor: not-allowed; margin-right: 10px; border-radius: 5px;">Previous</span>
                                                <?php } ?>

                                                <!-- Page Numbers -->
                                                <span id="currentPage" style="font-size: 16px; font-weight: bold; margin: 0 10px;">
                                                    Page <?php echo $page; ?> of <?php echo $totalPages; ?>
                                                </span>

                                                <!-- Next Button -->
                                                <?php if ($page < $totalPages) { ?>
                                                    <a href="?page=<?php echo $page + 1; ?>" class="button" style="padding: 10px 15px; border: none; background-color: #007bff; color: white; cursor: pointer; margin-left: 10px; border-radius: 5px;">Next</a>
                                                <?php } else { ?>
                                                    <span class="button disabled" style="padding: 10px 15px; border: none; background-color: grey; color: white; cursor: not-allowed; margin-left: 10px; border-radius: 5px;">Next</span>
                                                <?php } ?>
                                            </div>
                                        <?php } ?>
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Disapprove Popup -->
                    <div id="disapprovePopup" class="popup-overlay">
                        <div class="popup-content">
                            <h2>Confirm Account Disapproval</h2>
                            <p>Are you sure you want to disapprove this account? Please provide a reason for disapproval. This action cannot be undone.</p>
                            <form id="disapproveForm" action="disapproveAccountController.php" method="POST">
                                <input type="hidden" name="account_id" id="popup-account-id">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES); ?>">
                                <textarea name="remarks" id="disapprove_reason" placeholder="Enter reason for disapproval" required style="width: 100%; height: 100px; resize: none;"></textarea>
                                <div class="popup-buttons">
                                    <button type="submit" class="confirm-btn">Confirm</button>
                                    <button type="button" class="cancel-btn" onclick="closeDisapprovePopup()">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Approve Popup -->
                    <div id="approvePopup" class="popup-overlay">
                        <div class="popup-content">
                            <h2>Confirm Account Approval</h2>
                            <p>Are you sure you want to approve this account? This action cannot be undone.</p>
                            <form id="approveForm" action="approveAccountController.php" method="POST">
                                <input type="hidden" name="account_id" id="approve-account-id">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES); ?>">
                                <div class="popup-buttons">
                                    <button type="submit" class="confirm-btn">Confirm</button>
                                    <button type="button" class="cancel-btn" onclick="closeApprovePopup()">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <style>
                        /* Popup Overlay */
                        .popup-overlay {
                            display: none;
                            position: fixed;
                            top: 0;
                            left: 0;
                            width: 100%;
                            height: 100%;
                            background: rgba(0, 0, 0, 0.5);
                            z-index: 1000;
                            justify-content: center;
                            align-items: center;
                            opacity: 0;
                            transition: opacity 0.3s ease;
                        }

                        .popup-overlay.active {
                            display: flex;
                            opacity: 1;
                        }

                        /* Popup Content */
                        .popup-content {
                            background: #ffffff;
                            border-radius: 12px;
                            padding: 24px;
                            width: 100%;
                            max-width: 400px;
                            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
                            transform: translateY(-20px);
                            transition: transform 0.3s ease;
                        }

                        .popup-overlay.active .popup-content {
                            transform: translateY(0);
                        }

                        /* Typography */
                        .popup-content h2 {
                            font-size: 1.5rem;
                            color: #1e3a8a;
                            margin: 0 0 12px;
                            font-weight: 600;
                        }

                        .popup-content p {
                            font-size: 1rem;
                            color: #4b5563;
                            margin: 0 0 20px;
                            line-height: 1.5;
                        }

                        /* Buttons */
                        .popup-buttons {
                            display: flex;
                            justify-content: flex-end;
                            gap: 12px;
                        }

                        .confirm-btn,
                        .cancel-btn {
                            padding: 10px 20px;
                            border: none;
                            border-radius: 8px;
                            font-size: 1rem;
                            cursor: pointer;
                            transition: background 0.2s ease, transform 0.1s ease;
                        }

                        .confirm-btn {
                            background: #2563eb;
                            color: #ffffff;
                        }

                        .confirm-btn:hover {
                            background: #1d4ed8;
                            transform: translateY(-1px);
                        }

                        .cancel-btn {
                            background: #e5e7eb;
                            color: #374151;
                        }

                        .cancel-btn:hover {
                            background: #d1d5db;
                            transform: translateY(-1px);
                        }

                        /* Responsive Design */
                        @media (max-width: 480px) {
                            .popup-content {
                                margin: 0 16px;
                                padding: 16px;
                            }

                            .popup-content h2 {
                                font-size: 1.25rem;
                            }

                            .popup-content p {
                                font-size: 0.875rem;
                            }

                            .confirm-btn,
                            .cancel-btn {
                                padding: 8px 16px;
                                font-size: 0.875rem;
                            }
                        }
                    </style>

                    <script>
                        // Disapprove Popup Functions
                        function showDisapprovePopup(accountId) {
                            document.getElementById('popup-account-id').value = accountId;
                            const popup = document.getElementById('disapprovePopup');
                            popup.classList.add('active');
                        }

                        function closeDisapprovePopup() {
                            const popup = document.getElementById('disapprovePopup');
                            popup.classList.remove('active');
                            document.getElementById('disapproveForm').reset();
                        }

                        document.getElementById('disapprovePopup').addEventListener('click', function(e) {
                            if (e.target === this) {
                                closeDisapprovePopup();
                            }
                        });

                        // Approve Popup Functions
                        function showApprovePopup(accountId) {
                            document.getElementById('approve-account-id').value = accountId;
                            const popup = document.getElementById('approvePopup');
                            popup.classList.add('active');
                        }

                        function closeApprovePopup() {
                            const popup = document.getElementById('approvePopup');
                            popup.classList.remove('active');
                            document.getElementById('approveForm').reset();
                        }

                        document.getElementById('approvePopup').addEventListener('click', function(e) {
                            if (e.target === this) {
                                closeApprovePopup();
                            }
                        });
                    </script>

                    <div class="modal-overlay-add">
                        <div class="modal-add" id="accessible-modal-add" role="dialog" aria-modal="true" aria-labelledby="modal-title-add" aria-describedby="modal-description-add">
                            <div class="modal-header-add">
                                <h2 id="modal-title-add">Add Resident Account</h2>
                                <button class="modal-close-add" aria-label="Close modal">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="modal-content-add">
                                <form id="addForm-add" action="adminResidentAccountsController.php" method="POST" enctype="multipart/form-data">
                                    <!-- Resident Selection -->
                                    <div class="form-group-add">
                                        <label for="add-residents-add">Resident</label>
                                        <input type="text" id="resident-search-main" placeholder="Search Residents" autocomplete="off" class="form-control-add">
                                        <select id="add-residents-add" name="resident_id" size="1" style="overflow-y: auto;" required>
                                            <option value="" disabled selected>Select a resident with no account.</option>
                                            <?php
                                            if (!empty($residents)) {
                                                foreach ($residents as $resident) {
                                                    echo "<option value='" . htmlspecialchars($resident['resident_id']) . "'>" 
                                                        . htmlspecialchars($resident['last_name']) . ", " 
                                                        . htmlspecialchars($resident['first_name']) . " " 
                                                        . htmlspecialchars($resident['middle_name']) 
                                                        . " (" . htmlspecialchars($resident['age']) . " years old)</option>";
                                                }
                                            } else {
                                                echo "<option>No residents found</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <!-- Username -->
                                    <div class="form-group-add">
                                        <label for="add-username-add">Username</label>
                                        <input type="text" id="add-username-add" name="username" placeholder="Use at least one special character, numbers, and letters" required>
                                    </div>

                                    <!-- Email -->
                                    <div class="form-group-add">
                                        <label for="add-email-add">Email</label>
                                        <input type="email" id="add-email-add" name="email" placeholder="ex. juandelacruz@gmail.com">
                                    </div>

                                    <?php
                                    // Display error messages 
                                    if (isset($_SESSION['errorMessages'])) {
                                        echo '<h5 style="color:red; font-size:10px;">';
                                        foreach ($_SESSION['errorMessages'] as $errorMessage) {
                                            echo $errorMessage . '<br>';
                                        }
                                        echo '<br></h5>';
                                        unset($_SESSION['errorMessages']);
                                    }
                                    ?>

                                    <!-- Password -->
                                    <div class="form-group-add">
                                        <label for="add-password-add">Password</label>
                                        <input type="password" id="add-password-add" name="password" placeholder="Use at least one special character, numbers, and letters" required>
                                    </div>

                                    <!-- Confirm Password -->
                                    <div class="form-group-add">
                                        <label for="confirm-password-add">Confirm Password</label>
                                        <input type="password" id="confirm-password-add" name="confirm_password" placeholder="Use at least one special character, numbers, and letters" required>
                                    </div>

                                    <!-- Privilege -->
                                    <input type="hidden" id="privilege" name="privilege" value="4">

                                    <!-- Account Status -->
                                    <div class="form-group-add">
                                        <label for="add-status-add">Status</label>
                                        <select id="add-status-add" name="status" required>
                                            <option value="" disabled selected>Choose a status</option>
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                    </div>
                                    <input type="submit" class="button-add" value="Add Resident Account">
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Structure -->
                    <div class="modal-overlay">
                        <div class="modal" id="accessible-modal" role="dialog" aria-modal="true" aria-labelledby="modal-title" aria-describedby="modal-description">
                            <div class="modal-header">
                                <h2 id="modal-title">Update Resident Account</h2>
                                <button class="modal-close" aria-label="Close modal">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="modal-content">
                                <form id="updateForm" action="adminResidentAccountUpdate.php" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" id="account-id" name="account_id">

                                    <!-- Resident ID -->
                                    <div class="form-group">
                                        <label for="edit-resident-id">Resident ID</label>
                                        <input type="text" id="edit-resident-id" name="resident_id" disabled>
                                    </div>

                                    <!-- Resident Selection (hidden by default) -->
                                    <div class="form-group" id="resident-selection-group" style="display: none;">
                                        <label for="edit-residents-add">Resident</label>
                                        <input type="text" id="edit-resident-search" placeholder="Search Residents" autocomplete="off" class="form-control-add">
                                        <select id="edit-residents-add" name="resident_id" size="1" style="overflow-y: auto;">
                                            <option value="" disabled selected>Select a resident with no account.</option>
                                            <?php
                                            if (!empty($residents)) {
                                                foreach ($residents as $resident) {
                                                    echo "<option value='" . htmlspecialchars($resident['resident_id']) . "'>"
                                                        . htmlspecialchars($resident['last_name']) . ", "
                                                        . htmlspecialchars($resident['first_name']) . " "
                                                        . htmlspecialchars($resident['middle_name'])
                                                        . " (" . htmlspecialchars($resident['age']) . " years old)</option>";
                                                }
                                            } else {
                                                echo "<option>No residents found</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <!-- Username -->
                                    <div class="form-group">
                                        <label for="edit-username">Username</label>
                                        <input type="text" id="edit-username" name="username" readonly>
                                    </div>

                                    <!-- Email -->
                                    <div class="form-group">
                                        <label for="edit-email">Email</label>
                                        <input type="email" id="edit-email" name="email" readonly>
                                    </div>

                                    <!-- Mobile Number -->
                                    <div class="form-group" id="phone-group" style="display: none;">
                                        <label for="edit-phone">Mobile No.</label>
                                        <input type="tel" id="edit-phone" name="cellphone_number" maxlength="13" readonly>
                                    </div>
                                    
                                    <input type="hidden" id="edit-privilege" name="privilege">

                                    <!-- Account Status -->
                                    <div class="form-group" id="status-group">
                                        <label for="edit-status">Status</label>
                                        <select id="edit-status" name="status">
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                    </div>

                                    <input type="submit" class="button" value="Update Resident Account">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </body>

    <script>
        // AJAX FOR SEARCH
        $(document).ready(function() {
            $('#searchInput').on('keyup', function() {
                let query = $(this).val();
                console.log("Search Query:", query);

                $.ajax({
                    url: 'adminResidentAccountSearch.php',
                    method: 'POST',
                    cache: false,
                    data: { query: query },
                    success: function(data) {
                        console.log("Response Data:", data);
                        $('tbody').html(data);

                        $('.modal-open').on('click', function() {
                            let residentId = $(this).data('resident-id');
                            console.log("Clicked Resident ID: " + residentId);
                            $('.modal-overlay').addClass('is-active');
                        });
                    },
                    error: function(request, error) {
                        console.log("Search Error: " + error);
                    }
                });
            });

            $('.modal-close').on('click', function() {
                $('.modal-overlay').removeClass('is-active');
            });
        });

        function populateModal(button) {
            const accountId = button.getAttribute("data-account-id") || "";
            const residentId = button.getAttribute("data-resident-id") || "Not a resident yet";
            const username = button.getAttribute("data-username") || "";
            const email = button.getAttribute("data-email") || "";
            const mobileNo = button.getAttribute("data-mobile-no") || "";
            const privilege = button.getAttribute("data-privilege") || "";
            const status = button.getAttribute("data-status") || "";
            const firstName = button.getAttribute("data-first-name") || "";
            const middleName = button.getAttribute("data-middle-name") || "";
            const lastName = button.getAttribute("data-last-name") || "";
            const profileImage = button.getAttribute("data-profile-image") || "";
            const idImage = button.getAttribute("data-id-image") || "";

            // Debugging: Log the status to ensure it's being passed correctly
            console.log("Populating modal with status:", status);

            // Set form field values
            document.getElementById("account-id").value = accountId;
            document.getElementById("edit-resident-id").value = residentId;
            document.getElementById("edit-username").value = username;
            document.getElementById("edit-email").value = email;
            document.getElementById("edit-privilege").value = privilege;

            // Set mobile number if available
            if (mobileNo.trim() !== "") {
                document.getElementById("edit-phone").value = mobileNo;
                document.getElementById("phone-group").style.display = "block";
            } else {
                document.getElementById("edit-phone").value = "";
                document.getElementById("phone-group").style.display = "none";
            }

            // Handle status field
            const statusGroup = document.getElementById("status-group");
            const statusSelect = document.getElementById("edit-status");

            if (status === "pending" || status === "disapproved") {
                statusGroup.style.display = "none";
            } else {
                statusGroup.style.display = "block";
                // Set the status, with fallback to 'active' if invalid
                if (status !== "active" && status !== "inactive") {
                    statusSelect.value = "active"; // Fallback to active
                    console.warn("Invalid status value, defaulting to active:", status);
                } else {
                    statusSelect.value = status;
                }
                // Verify that the status was set correctly
                console.log("Selected status in modal:", statusSelect.value);
            }

            // Handle resident selection visibility
            const isResidentConnected = (firstName.trim() !== "" || middleName.trim() !== "" || lastName.trim() !== "");
            document.getElementById("resident-selection-group").style.display = isResidentConnected ? "none" : "block";
            document.getElementById("edit-resident-search").value = (firstName + " " + middleName + " " + lastName).trim();

            // Store modal data in localStorage for persistence
            const modalData = {
                accountId: accountId,
                username: username,
                email: email,
                mobileNo: mobileNo,
                privilege: privilege,
                status: status,
                firstName: firstName,
                middleName: middleName,
                lastName: lastName,
                profileImage: profileImage,
                idImage: idImage
            };
            localStorage.setItem("modalData", JSON.stringify(modalData));
        }

        $(document).ready(function() {
            $('#edit-resident-search').on('keyup', function() {
                let searchQuery = $(this).val();
                $.ajax({
                    url: 'adminResidentNoAccountSearchModal.php',
                    type: 'POST',
                    data: { searchQuery: searchQuery },
                    success: function(response) {
                        $('#edit-residents-add').html(response);
                    },
                    error: function() {
                        console.log("Error fetching residents.");
                    }
                });
            });

            $('#resident-search-main').on('keyup', function() {
                let searchQuery = $(this).val();
                $.ajax({
                    url: 'adminResidentNoAccountSearchModal.php',
                    type: 'POST',
                    data: { searchQuery: searchQuery },
                    success: function(response) {
                        $('#add-residents-add').html(response);
                    },
                    error: function() {
                        console.log("Error fetching residents.");
                    }
                });
            });
        });

        function loadModalData() {
            const modalData = JSON.parse(localStorage.getItem("modalData"));
            if (modalData) {
                document.getElementById("account-id").value = modalData.accountId || "";
                document.getElementById("edit-resident-id").value = modalData.residentId || "Not a resident yet";
                document.getElementById("edit-username").value = modalData.username || "";
                document.getElementById("edit-email").value = modalData.email || "";
                document.getElementById("edit-privilege").value = modalData.privilege || "";

                // Handle mobile number
                if (modalData.mobileNo && modalData.mobileNo.trim() !== "") {
                    document.getElementById("edit-phone").value = modalData.mobileNo;
                    document.getElementById("phone-group").style.display = "block";
                } else {
                    document.getElementById("edit-phone").value = "";
                    document.getElementById("phone-group").style.display = "none";
                }

                // Handle status
                const statusGroup = document.getElementById("status-group");
                const statusSelect = document.getElementById("edit-status");
                if (modalData.status === "pending" || modalData.status === "disapproved") {
                    statusGroup.style.display = "none";
                } else {
                    statusGroup.style.display = "block";
                    if (modalData.status !== "active" && modalData.status !== "inactive") {
                        statusSelect.value = "active"; // Fallback to active
                        console.warn("Invalid status value in localStorage, defaulting to active:", modalData.status);
                    } else {
                        statusSelect.value = modalData.status;
                    }
                    console.log("Loaded status from localStorage:", statusSelect.value);
                }
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            loadModalData();
        });

        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function () {
                var output = document.getElementById('edit-profile-image-preview');
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>

</html>