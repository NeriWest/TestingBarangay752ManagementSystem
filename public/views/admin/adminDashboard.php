    <!DOCTYPE html>
    <html lang="en">
    <?php
    require_once '../../model/dbconnection.php';
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
    ?>

    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Dashboard</title>
        <link rel="icon" href="favicon-32x32.png" type="image/x-icon">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Metrophobic&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="../../css/admin/adminPortal.css">
        <link rel="stylesheet" href="../../css/admin/table.css">
        <link rel="stylesheet" href="../../css/main.css">
        <script src="https://kit.fontawesome.com/4907458c0c.js" crossorigin="anonymous"></script>
        <script src="../../js/dashBoard.js" defer></script>
        <script src="../../js/main.js" defer></script>
    </head>

    <body>
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

        <!-- TOP NAV -->
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

                                <i class="fa-solid fa-user"></i> &nbsp;&nbsp;&nbsp;
                                <?php
                                if (isset($_SESSION['first_name']) && isset($_SESSION['last_name'])) {
                                    echo htmlspecialchars($_SESSION['first_name']) . " " . htmlspecialchars($_SESSION['last_name']);
                                    if (isset($_SESSION['suffix']) && $_SESSION['suffix'] !== 'N/A') {
                                        echo " " . htmlspecialchars($_SESSION['suffix']);
                                    }
                                } else {
                                    echo "Guest!";
                                } ?>    </p>
                        </button>
                        <div class="floating-menu">
                            <p>You're logged in as</p>
                            <?php echo '<h3>' . ($_SESSION['username']) . '</h3>' ?>
                            <a href="adminViewProfileController.php">View Profile</a>
                            <a href="../logoutController.php">Log Out</a>
                        </div>
                    </div>
                </nav>
            </div>

            <!-- Residents -->
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
                    /* Dark blue background */
                    color: white;
                    padding: 30px 40px;
                    /* Increased padding for a larger box */
                    border-radius: 12px;
                    /* Slightly more rounded corners */
                    background: linear-gradient(135deg, #007BFF, #0056b3);
                    /* Blue gradient background */
                    color: #f8f9fa;
                    /* Light gray-white text color */
                    box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
                    /* Softer shadow */
                    display: flex;
                    align-items: center;
                    gap: 20px;
                    /* Increased gap between elements */
                    z-index: 1001;
                    /* Higher z-index to appear above overlay */
                    transition: opacity 0.5s ease, transform 0.3s ease;
                    /* Added smooth transform transition */
                    font-family: 'Metrophobic', sans-serif;
                }

                .message-box .message-icon {
                    width: 36px;
                    /* Slightly larger icon size */
                    height: 36px;
                }

                .message-box .message-text {
                    margin: 0;
                    font-size: 20px;
                    /* Slightly larger font size */
                }

                .message-box .close-button {
                    background: none;
                    border: none;
                    color: #dee2e6;
                    /* Light gray color for the close button */
                    font-size: 26px;
                    /* Slightly larger font size for the close button */
                    cursor: pointer;
                    transition: color 0.3s ease;
                }

                .message-box .close-button:hover {
                    color: #f8f9fa;
                    /* White color on hover */
                }

                .screen-overlay {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background-color: rgba(0, 0, 0, 0.5);
                    /* Semi-transparent gray */
                    z-index: 1000;
                    /* Below the message box */
                    opacity: 1;
                    transition: opacity 0.5s ease;
                    /* Smooth fade-out */
                }

                .screen-overlay.hidden {
                    opacity: 0;
                    pointer-events: none;
                    /* Prevent interaction when hidden */
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

            <!-- DASHBOARD -->
            <div class="content">
                <div class="dashboard-title">
                    <div class="title-wrapper">
                        <h2 class="header">Barangay Official Dashboard</h2>
                    </div>
                    <hr class="style-six">
                </div>
                <div class="dashboard-header">
                    <div class="overlay-text">
                        <h1>Hello There,
                            <?php
                            if (isset($_SESSION['first_name']) && isset($_SESSION['last_name'])) {
                                echo htmlspecialchars($_SESSION['first_name']) . " " . htmlspecialchars($_SESSION['last_name']);
                            } else {
                                echo "Guest!";
                            } ?>
                            !
                        </h1>
                    </div>
                    <div class="weather-info">
                        <div class="temp-city">
                            <h2 class="temp">22°c</h2>
                            <p class="city"></p>
                        </div>
                        <div class="temp-city">
                            <i class="weather-icon fa-solid fa-cloud-rain"></i>
                        </div>
                    </div>
                </div>

                <div class="dashboard-body">
                    <div class="section-container">
                        <div class="card-container">

                            <h2>RESIDENTS DATA</h2>
                            <div class="card-wrapper" id="resident-table">
                                <!-- Total Residents -->
                                <div class="card card-residents" onclick="this.classList.toggle('flipped')">
                                    <div class="card-front">
                                        <div class="card-text">
                                            <h3><?php echo "$totalResidents"; ?></h3>
                                            <p>Total Residents <br> &nbsp;</p>
                                        </div>
                                        <div class="card-logo">
                                            <i class="fa-solid fa-users faIconSize"></i>
                                        </div>
                                    </div>
                                    <div class="card-back">
                                        <div class="card-text">
                                            <h4>Total Residents</h4>

                                            <!-- PRINT BUTTONS -->
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <td colspan="19" class="search-column" style="background-color: #1a4b8c; border:#1a4b8c">
                                                            <div class="search-bar">
                                                                <button class="button" style="background-color: red;" onclick="window.open('export/dashboardSummaryExport/residentsDataSummaries/totalResidentPDFExport.php', '_blank')">Print Total Residents PDF</button>
                                                                <button class="button" style="background-color: green;" onclick="window.open('export/dashboardSummaryExport/residentsDataSummaries/totalResidentExcelExport.php', '_blank')">Print Total Residents Excel</button>
                                                            </div>
                                                        </td>
                                            </table>

                                        </div>
                                    </div>
                                </div>

                                <!-- New Residents -->
                                <div class="card" onclick="this.classList.toggle('flipped')">
                                    <div class="card-front">
                                        <div class="card-text">
                                            <h3><?php echo $newResidents ?></h3>
                                            <p>New Residents</p>
                                        </div>
                                        <div class="card-logo">
                                            <i class="fa-solid fa-user-plus faIconSize"></i>
                                        </div>
                                    </div>
                                    <div class="card-back">
                                        <div class="card-text">
                                            <h4>New Residents</h4>

                                            <!-- PRINT BUTTONS -->
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <td colspan="19" class="search-column" style="background-color: #1a4b8c; border:#1a4b8c">
                                                            <div class="search-bar">
                                                                <button class="button" style="background-color: red;" onclick="window.open('export/dashboardSummaryExport/residentsDataSummaries/newResidentPDFExport.php', '_blank')">Print New Residents PDF</button>
                                                                <button class="button" style="background-color: green;" onclick="window.open('export/dashboardSummaryExport/residentsDataSummaries/newResidentsExcelExport.php', '_blank')">Print New Residents Excel</button>
                                                            </div>
                                                        </td>
                                            </table>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-wrapper" id="resident-table">
                                <!-- Senior Citizens -->
                                <div class="card card-reports" onclick="this.classList.toggle('flipped')">
                                    <div class="card-front">
                                        <div class="card-text">
                                            <h3><?php echo $seniorCitizens; ?></h3>
                                            <p>Senior Citizens</p>
                                        </div>
                                        <div class="card-logo">
                                            <i class="fa-solid fa-person-cane faIconSize"></i>
                                        </div>
                                    </div>
                                    <div class="card-back">
                                        <div class="card-text">
                                            <h4>Senior Citizens</h4>

                                            <!-- PRINT BUTTONS -->
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <td colspan="19" class="search-column" style="background-color: #1a4b8c; border:#1a4b8c">
                                                            <div class="search-bar">
                                                                <button class="button" style="background-color: red;" onclick="window.open('export/dashboardSummaryExport/residentsDataSummaries/seniorCitizensPDFExport.php', '_blank')">Print Senior Citizens PDF</button>
                                                                <button class="button" style="background-color: green;" onclick="window.open('export/dashboardSummaryExport/residentsDataSummaries/seniorCitizensExcelExport.php', '_blank')">Print Senior Citizens Excel</button>
                                                            </div>
                                                        </td>
                                            </table>

                                        </div>
                                    </div>
                                </div>

                                <!-- Total Families -->
                                <div class="card" onclick="this.classList.toggle('flipped')">
                                    <div class="card-front">
                                        <div class="card-text">
                                            <h3><?php echo $totalFamilies; ?></h3>
                                            <p>Total Families <br> &nbsp;</p>
                                        </div>
                                        <div class="card-logo">
                                            <i class="fa-solid fa-people-roof faIconSize"></i>
                                        </div>
                                    </div>
                                    <div class="card-back">
                                        <div class="card-text">
                                            <h4>Total Families</h4>

                                            <!-- PRINT BUTTONS -->
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <td colspan="19" class="search-column" style="background-color: #1a4b8c; border:#1a4b8c">
                                                            <div class="search-bar">
                                                                <button class="button" style="background-color: red;" onclick="window.open('export/dashboardSummaryExport/residentsDataSummaries/familyPDFExport.php', '_blank')">Print Total Families PDF</button>
                                                                <button class="button" style="background-color: green;" onclick="window.open('export/dashboardSummaryExport/residentsDataSummaries/familyExcelExport.php', '_blank')">Print Total Families Excel</button>
                                                            </div>
                                                        </td>
                                            </table>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-container">
                            <h2>REPORTS</h2>
                            <div class="card-wrapper" id="resident-table">

                                <!-- Incidents Today -->
                                <div class="card" onclick="this.classList.toggle('flipped')">
                                    <div class="card-front">
                                        <div class="card-text">
                                            <h3><?php echo $incidentsToday; ?></h3>
                                            <p>Incidents Reported Today <br> &nbsp;</p>
                                        </div>
                                        <div class="card-logo">
                                            <i class="fa-solid fa-clipboard-list faIconSize"></i>
                                        </div>
                                    </div>
                                    <div class="card-back">
                                        <div class="card-text">
                                            <h4>Incidents Today</h4>

                                            <!-- PRINT BUTTONS -->
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <td colspan="19" class="search-column" style="background-color: #1a4b8c; border:#1a4b8c">
                                                            <div class="search-bar">
                                                                <button class="button" style="background-color: red;" onclick="window.open('export/dashboardSummaryExport/reportsDataSummaries/incidentsReportedTodayPDFExport.php', '_blank')">Print Incidents Today PDF</button>
                                                                <button class="button" style="background-color: green;" onclick="window.open('export/dashboardSummaryExport/reportsDataSummaries/incidentsReportedTodayExcelExport.php', '_blank')">Print Incidents Today Excel</button>
                                                            </div>
                                                        </td>
                                            </table>

                                        </div>
                                    </div>
                                </div>

                                <!-- Blotters Today -->
                                <div class="card" onclick="this.classList.toggle('flipped')">
                                    <div class="card-front">
                                        <div class="card-text">
                                            <h3><?php echo $blottersToday; ?></h3>
                                            <p>Blotters Reported Today</p>
                                        </div>
                                        <div class="card-logo">
                                            <i class="fa-solid fa-file-contract faIconSize"></i>
                                        </div>
                                    </div>
                                    <div class="card-back">
                                        <div class="card-text">
                                            <h4>Blotters Today</h4>

                                            <!-- PRINT BUTTONS -->
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <td colspan="19" class="search-column" style="background-color: #1a4b8c; border:#1a4b8c">
                                                            <div class="search-bar">
                                                                <button class="button" style="background-color: red;" onclick="window.open('export/dashboardSummaryExport/reportsDataSummaries/blottersReportedTodayPDFExport.php', '_blank')">Print Blotters Today PDF</button>
                                                                <button class="button" style="background-color: green;" onclick="window.open('export/dashboardSummaryExport/reportsDataSummaries/blottersReportedTodayExcelExport.php', '_blank')">Print Blotters Today Excel</button>
                                                            </div>
                                                        </td>
                                            </table>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-wrapper" id="resident-table">

                                <!-- Complaints Reported Today  -->
                                <div class="card" onclick="this.classList.toggle('flipped')">
                                    <div class="card-front">
                                        <div class="card-text">
                                            <h3><?php echo $complaintsToday ?></h3>
                                            <p>Complaints Reported Today </p>
                                        </div>
                                        <div class="card-logo">
                                            <i class="fa-solid fa-triangle-exclamation faIconSize"></i>
                                        </div>
                                    </div>
                                    <div class="card-back">
                                        <div class="card-text">
                                            <h4>Complaints Reported Today </h4>

                                            <!-- PRINT BUTTONS -->
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <td colspan="19" class="search-column" style="background-color: #1a4b8c; border:#1a4b8c">
                                                            <div class="search-bar">
                                                                <button class="button" style="background-color: red;" onclick="window.open('export/dashboardSummaryExport/reportsDataSummaries/complaintsReportedTodayPDFExport.php', '_blank')">Print Complaints Reported Today PDF</button>
                                                                <button class="button" style="background-color: green;" onclick="window.open('export/dashboardSummaryExport/reportsDataSummaries/complaintsReportedTodayExcelExport.php', '_blank')">Print Complaints Reported Today Excel</button>
                                                            </div>
                                                        </td>
                                            </table>

                                        </div>
                                    </div>
                                </div>

                                <!-- Total Pending Reports Today -->
                                <div class="card" onclick="this.classList.toggle('flipped')">
                                    <div class="card-front">
                                        <div class="card-text">
                                            <h3><?php echo $totalPendingReportsToday; ?></h3>
                                            <p>Total Pending Reports Today <br> &nbsp;</p>
                                        </div>
                                        <div class="card-logo">
                                            <i class="fa-solid fa-file-invoice faIconSize"></i>
                                        </div>
                                    </div>
                                    <div class="card-back">
                                        <div class="card-text">
                                            <h4>Total Pending Reports Today</h4>

                                            <!-- PRINT BUTTONS -->
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <td colspan="19" class="search-column" style="background-color: #1a4b8c; border:#1a4b8c">
                                                            <div class="search-bar">
                                                                <button class="button" style="background-color: red;" onclick="window.open('export/dashboardSummaryExport/reportsDataSummaries/totalPendingReportsTodayPDFExport.php', '_blank')">Print Total Pending Reports Today PDF</button>
                                                                <button class="button" style="background-color: green;" onclick="window.open('export/dashboardSummaryExport/reportsDataSummaries/totalPendingReportsTodayExcelExport.php', '_blank')">Print Total Pending Reports Today Excel</button>
                                                            </div>
                                                        </td>
                                            </table>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="section-container">

                        <!-- DOCUMENT REUESTS -->
                        <div class="card-container">
                            <h2>DOCUMENT REQUESTS</h2>
                            <div class="card-wrapper" id="resident-table">

                                <!-- Requests Today -->
                                <div class="card card-requests" onclick="this.classList.toggle('flipped')">
                                    <div class="card-front">
                                        <div class="card-text">
                                            <h3><?php echo $permitsToday; ?></h3>
                                            <p>Permits Requested Today <br> &nbsp;</p>
                                        </div>
                                        <div class="card-logo">
                                            <i class="fa-solid fa-file-signature faIconSize-1-25"></i>
                                        </div>  
                                    </div>
                                    <div class="card-back">
                                        <div class="card-text">
                                            <h4>Permits Requested Today</h4>

                                            <!-- PRINT BUTTONS -->
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <td colspan="19" class="search-column" style="background-color: #1a4b8c; border:#1a4b8c">
                                                            <div class="search-bar">
                                                                <button class="button" style="background-color: red;" onclick="window.open('export/dashboardSummaryExport/documentRequestsSummaries/newPermitsTodayPDFExport.php', '_blank')">Print Permits Requested Today PDF</button>
                                                                <button class="button" style="background-color: green;" onclick="window.open('export/dashboardSummaryExport/documentRequestsSummaries/newPermitsTodayExcelExport.php', '_blank')">Print Permits Requested Today Excel</button>
                                                            </div>
                                                        </td>
                                            </table>

                                        </div>
                                    </div>
                                </div>

                                <!-- Pending Requests -->
                                <div class="card card-requests" onclick="this.classList.toggle('flipped')">
                                    <div class="card-front">
                                        <div class="card-text">
                                            <h3><?php echo $clearanceToday ?></h3>
                                            <p>Clearance Requested Today</p>
                                        </div>
                                        <div class="card-logo">
                                            <i class="fa-solid fa-id-card faIconSize-1-25"></i>
                                        </div>
                                    </div>
                                    <div class="card-back">
                                        <div class="card-text">
                                            <h4>Clearance Requested Today</h4>

                                            <!-- PRINT BUTTONS -->
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <td colspan="19" class="search-column" style="background-color: #1a4b8c; border:#1a4b8c">
                                                            <div class="search-bar">
                                                                <button class="button" style="background-color: red;" onclick="window.open('export/dashboardSummaryExport/documentRequestsSummaries/newClearancesTodayPDFExport.php', '_blank')">Print Clearance Requested Today PDF</button>
                                                                <button class="button" style="background-color: green;" onclick="window.open('export/dashboardSummaryExport/documentRequestsSummaries/newClearancesTodayExcelExport.php', '_blank')">Print Clearance Requested Today Excel</button>
                                                            </div>
                                                        </td>
                                            </table>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-wrapper" id="resident-table">

                                <!-- Certificate Requested  -->
                                <div class="card card-requests" onclick="this.classList.toggle('flipped')">
                                    <div class="card-front">
                                        <div class="card-text">
                                            <h3><?php echo $certificateToday ?></h3>
                                            <p>Certificate Requested Today </p>
                                        </div>
                                        <div class="card-logo">
                                            <i class="fa-solid fa-file-contract faIconSize"></i>
                                        </div>
                                    </div>
                                    <div class="card-back">
                                        <div class="card-text">
                                            <h4>Certificate Requested </h4>

                                            <!-- PRINT BUTTONS -->
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <td colspan="19" class="search-column" style="background-color: #1a4b8c; border:#1a4b8c">
                                                            <div class="search-bar">
                                                                <button class="button" style="background-color: red;" onclick="window.open('export/dashboardSummaryExport/documentRequestsSummaries/newCertificatesTodayPDFExport.php', '_blank')">Print Certificate Requested PDF</button>
                                                                <button class="button" style="background-color: green;" onclick="window.open('export/dashboardSummaryExport/documentRequestsSummaries/newCertificatesTodayExcelExport.php', '_blank')">Print Certificate Requested Excel</button>
                                                            </div>
                                                        </td>
                                            </table>

                                        </div>
                                    </div>
                                </div>

                                <!-- Total Pending Requests Today -->
                                <div class="card card-requests" onclick="this.classList.toggle('flipped')">
                                    <div class="card-front">
                                        <div class="card-text">
                                            <h3><?php echo $requestsMadeToday ?></h3>
                                            <p>Total Pending Requests Today </p>
                                        </div>
                                        <div class="card-logo">
                                            <i class="fa-solid fa-circle-exclamation faIconSize"></i>
                                        </div>
                                    </div>
                                    <div class="card-back">
                                        <div class="card-text">
                                            <h4>Total Pending Requests Today </h4>

                                            <!-- PRINT BUTTONS -->
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <td colspan="19" class="search-column" style="background-color: #1a4b8c; border:#1a4b8c;">
                                                            <div class="search-bar">
                                                                <button class="button" style="background-color: red;" onclick="window.open('export/dashboardSummaryExport/documentRequestsSummaries/requestsTodayPDFExport.php', '_blank')">Print Total Pending Requests Today PDF</button>
                                                                <button class="button" style="background-color: green;" onclick="window.open('export/dashboardSummaryExport/documentRequestsSummaries/requestsTodayExcelExport.php', '_blank')">Print Total Pending Requests Today Excel</button>
                                                            </div>
                                                        </td>
                                            </table>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="card-container">
                            <h2>ANNOUNCEMENTS</h2>
                            <div class="announcements-wrapper">
    <?php if (!empty($announcements)): ?>
        <div class="announcements-list">
            <?php foreach ($announcements as $announcement): ?>
                <div class="announcement-card">
                    <div class="announcement-top-row">
                        <span class="announcement-type" style="background-color: <?= $typeColors[strtolower($announcement['announcement_type'])] ?? '#6b7280' ?>">
                            <?= htmlspecialchars(ucfirst(strtolower($announcement['announcement_type'] ?? 'general'))) ?>
                        </span>
                        <span class="announcement-date">
                            Scheduled: <?= date('M j, Y g:i A', strtotime($announcement['schedule'])) ?>
                        </span>
                    </div>

                    <!-- Rest of your announcement card content -->
                    <h3 class="announcement-subject">
                        <?= htmlspecialchars($announcement['subject']) ?>
                    </h3>

                    <div class="announcement-author">
                        <i class="fas fa-user"></i>
                        <span class="author-name">
                            <?= htmlspecialchars($announcement['author_full_name']) ?>
                        </span>
                        <span class="author-role">
                            (<?= htmlspecialchars($announcement['author_role']) ?>)
                        </span>
                    </div>

                    <div class="announcement-footer">
                        <span class="recipient-badge">
                            For: <?= htmlspecialchars($announcement['recipient_group']) ?>
                        </span>
                    </div>
                    <br>

                    <div class="announcement-content">
                        <p><?= nl2br(htmlspecialchars($announcement['message_body'])) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="no-announcements">
            <i class="fas fa-bullhorn"></i>
            <p>No announcements at this time</p>
        </div>
    <?php endif; ?>
</div>
                        </div>


                        <!-- ACCOUNTS -->
                        <div class="card-container">
                            <h2>ACCOUNTS</h2>
                            <div class="card-wrapper" id="resident-table">

                                <!-- Total Accounts -->
                                <div class="card card-requests" onclick="this.classList.toggle('flipped')">
                                    <div class="card-front">
                                        <div class="card-text">
                                            <h3><?php echo $totalAccounts; ?></h3>
                                            <p>Total Accounts <br> &nbsp;</p>
                                        </div>
                                        <div class="card-logo">
                                            <i class="fa-solid fa-users faIconSize-1-25"></i>
                                        </div>
                                    </div>
                                    <div class="card-back">
                                        <div class="card-text">
                                            <h4>Total Accounts</h4>

                                            <!-- PRINT BUTTONS -->
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <td colspan="19" class="search-column" style="background-color: #1a4b8c; border:#1a4b8c">
                                                            <div class="search-bar">
                                                                <button class="button" style="background-color: red;" onclick="window.open('export/dashboardSummaryExport/accountsDataSummaries/totalAccountsPDFExport.php', '_blank')">Print Total Accounts PDF</button>
                                                                <button class="button" style="background-color: green;" onclick="window.open('export/dashboardSummaryExport/accountsDataSummaries/totalAccountsExcelExport.php', '_blank')">Print Total Accounts Excel</button>
                                                            </div>
                                                        </td>
                                            </table>

                                        </div>
                                    </div>
                                </div>

                                <!-- New Accounts -->
                                <div class="card card-requests" onclick="this.classList.toggle('flipped')">
                                    <div class="card-front">
                                        <div class="card-text">
                                            <h3><?php echo $newAccounts ?></h3>
                                            <p>New Accounts</p>
                                        </div>
                                        <div class="card-logo">
                                            <i class="fa-solid fa-user faIconSize-1-25"></i>
                                        </div>
                                    </div>
                                    <div class="card-back">
                                        <div class="card-text">
                                            <h4>New Accounts</h4>

                                            <!-- PRINT BUTTONS -->
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <td colspan="19" class="search-column" style="background-color: #1a4b8c; border:#1a4b8c">
                                                            <div class="search-bar">
                                                                <button class="button" style="background-color: red;" onclick="window.open('export/dashboardSummaryExport/accountsDataSummaries/newAccountsPDFExport.php', '_blank')">Print New Accounts PDF</button>
                                                                <button class="button" style="background-color: green;" onclick="window.open('export/dashboardSummaryExport/accountsDataSummaries/newAccountsExcelExport.php', '_blank')">Print New Accounts Excel</button>
                                                            </div>
                                                        </td>
                                            </table>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-wrapper" id="resident-table">

                                <!-- New Pending Accounts  -->
                                <div class="card card-requests" onclick="this.classList.toggle('flipped')">
                                    <div class="card-front">
                                        <div class="card-text">
                                            <h3><?php echo $newPendingAccounts ?></h3>
                                            <p>New Pending Accounts </p>
                                        </div>
                                        <div class="card-logo">
                                            <i class="fa-solid fa-circle-exclamation faIconSize"></i>
                                        </div>
                                    </div>
                                    <div class="card-back">
                                        <div class="card-text">
                                            <h4>New Pending Accounts </h4>

                                            <!-- PRINT BUTTONS -->
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <td colspan="19" class="search-column" style="background-color: #1a4b8c; border:#1a4b8c">
                                                            <div class="search-bar">
                                                                <button class="button" style="background-color: red;" onclick="window.open('export/dashboardSummaryExport/accountsDataSummaries/newPendingAccountsPDFExport.php', '_blank')">Print New Pending Accounts PDF</button>
                                                                <button class="button" style="background-color: green;" onclick="window.open('export/dashboardSummaryExport/accountsDataSummaries/newPendingAccountsExcelExport.php', '_blank')">Print New Pending Accounts Excel</button>
                                                            </div>
                                                        </td>
                                            </table>

                                        </div>
                                    </div>
                                </div>

                                <!-- Total Pending Accounts -->
                                <div class="card card-requests" onclick="this.classList.toggle('flipped')">
                                    <div class="card-front">
                                        <div class="card-text">
                                            <h3><?php echo $totalPendingAccounts; ?></h3>
                                            <p>Total Pending Accounts <br> &nbsp;</p>
                                        </div>
                                        <div class="card-logo">
                                            <i class="fa-solid fa-people-roof faIconSize-1-25"></i>
                                        </div>
                                    </div>
                                    <div class="card-back">
                                        <div class="card-text">
                                            <h4>Total Pending Accounts</h4>

                                            <!-- PRINT BUTTONS -->
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <td colspan="19" class="search-column" style="background-color: #1a4b8c; border:#1a4b8c">
                                                            <div class="search-bar">
                                                                <button class="button" style="background-color: red;" onclick="window.open('export/dashboardSummaryExport/accountsDataSummaries/totalPendingAccountsPDFEXport.php', '_blank')">Print Total Pending Accounts PDF</button>
                                                                <button class="button" style="background-color: green;" onclick="window.open('export/dashboardSummaryExport/accountsDataSummaries/totalPendingAccountsExcelExport.php', '_blank')">Print Total Pending Accounts Excel</button>
                                                            </div>
                                                        </td>
                                            </table>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </section>
    </main>
</body>

</html>