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
    $accountId = $_SESSION['account_id'];
?>
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Complaint</title>
    <script src="https://kit.fontawesome.com/4907458c0c.js" crossorigin="anonymous"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Metrophobic&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../css/main.css">
    <link rel="stylesheet" href="../../css/admin/Table.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="../../js/main.js" defer></script>
    <script src="../../js/dashBoard.js" defer></script>
    <script src="../../js/add-modal.js" defer></script>
    <script src="../../js/update-modal.js" defer></script>
    <style>
        /* FORM WIDTH DESIGN/TEMPORARY */
        /* Add Form */
        .modal-overlay-add {
            padding-top: 500px;
        }
        .modal-add {
            max-width: 750px;
            width: 100%;
        }
        /* Edit Form */
        .modal-overlay {
            padding-top: 675px;
        }
        .modal {
            max-width: 750px;
            width: 100%;
        }
        
    </style>
</head>

<body>
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
                    background-color: rgba(178, 176, 192, 0.9); /* Dark blue background */
                    color: white;
                    padding: 30px 40px; /* Increased padding for a larger box */
                    border-radius: 12px; /* Slightly more rounded corners */
                    background: linear-gradient(135deg, #007BFF, #0056b3); /* Blue gradient background */
                    color: #f8f9fa; /* Light gray-white text color */
                    box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15); /* Softer shadow */
                    display: flex;
                    align-items: center;
                    gap: 20px; /* Increased gap between elements */
                    z-index: 1001; /* Higher z-index to appear above overlay */
                    transition: opacity 0.5s ease, transform 0.3s ease; /* Added smooth transform transition */
                    font-family: 'Metrophobic', sans-serif;
                }

                .message-box .message-icon {
                    width: 36px; /* Slightly larger icon size */
                    height: 36px;
                }

                .message-box .message-text {
                    margin: 0;
                    font-size: 20px; /* Slightly larger font size */
                }

                .message-box .close-button {
                    background: none;
                    border: none;
                    color: #dee2e6; /* Light gray color for the close button */
                    font-size: 26px; /* Slightly larger font size for the close button */
                    cursor: pointer;
                    transition: color 0.3s ease;
                }

                .message-box .close-button:hover {
                    color: #f8f9fa; /* White color on hover */
                }

                .screen-overlay {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent gray */
                    z-index: 1000; /* Below the message box */
                    opacity: 1;
                    transition: opacity 0.5s ease; /* Smooth fade-out */
                }

                .screen-overlay.hidden {
                    opacity: 0;
                    pointer-events: none; /* Prevent interaction when hidden */
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
                                <i class="fa-solid fa-user"></i> &nbsp;&nbsp;&nbsp;
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
                            <a href="logoutController.php">Log Out</a>
                        </div>
                    </div>
                </nav>
            </div>
            <div class="content" id="table-content">
            <h1 id="table-title" style="font-size: 30px; margin-bottom: 20px">Complaint List</h1>
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <td colspan="15" class="search-column">
                                    <div class="search-bar">
                                        <p>Search</p>
                                        <input type="text" placeholder="Search Complaints" id="searchInput">
                                        <!-- <button class="modal-open-add button" onclick="newEntry()" id="add-resident">Add Complaint +</button> -->
                                        <?php
                                        $resolvedCount = 0;
                                        $closedCount = 0;
                                        $pendingCount = 0;

                                        if (!empty($complaints)) {
                                            foreach ($complaints as $complaint) {
                                                switch ($complaint['status']) {
                                                    case 'resolved':
                                                        $resolvedCount++;
                                                        break;
                                                    case 'closed':
                                                        $closedCount++;
                                                        break;
                                                    case 'pending':
                                                        $pendingCount++;
                                                        break;
                                                }
                                            }
                                        }
                                        ?>

                                        <div class="status-summary" style="display: flex; gap: 20px; align-items: center; margin-left: 20px;">
                                            <div style="display: flex; align-items: center; gap: 10px;">
                                                <span style="
                                                    background-color: green; 
                                                    color: white; 
                                                    padding: 5px 10px; 
                                                    border-radius: 5px; 
                                                    font-weight: bold;
                                                    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);">
                                                    Resolved
                                                </span>
                                                <p style="font-size: 18px; font-weight: bold;"><?php echo $resolvedCount; ?></p>
                                            </div>
                                            <div style="display: flex; align-items: center; gap: 10px;">
                                                <span style="
                                                    background-color: red; 
                                                    color: white; 
                                                    padding: 5px 10px; 
                                                    border-radius: 5px; 
                                                    font-weight: bold;
                                                    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);">
                                                    Closed
                                                </span>
                                                <p style="font-size: 18px; font-weight: bold;"><?php echo $closedCount; ?></p>
                                            </div>
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
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>Case Number</th>
                                <th>Complainant</th>
                                <th>Complainant Type</th>
                                <th>Subject</th>
                                <th>Official</th>
                                <th>Date of Incident</th>
                                <th>Time of Incident</th>
                                <th>Location</th>
                                <th>Respondent</th>
                                <th>Narration</th>
                                <th>Evidence Description</th>
                                <th>Evidence Image</th>
                                <th>Remarks</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            if (!empty($complaints)) {
                                $groupedComplaints = [];

                                // Group complaints by complaint_id
                                foreach ($complaints as $complaint) {
                                    $complaint_id = $complaint['complaint_id'];

                                    if (!isset($groupedComplaints[$complaint_id])) {
                                        // Initialize group for new complaint_id
                                        $groupedComplaints[$complaint_id] = [
                                            'complaint_info' => $complaint,
                                            'images' => []
                                        ];
                                    }

                                    // Add evidence image if available
                                    if (!empty($complaint['evidence_picture'])) {
                                        $groupedComplaints[$complaint_id]['images'][] = $complaint['evidence_picture'];
                                    }
                                }

                                // Print the table rows
                                foreach ($groupedComplaints as $groupedComplaint) {
                                    $complaint = $groupedComplaint['complaint_info'];
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($complaint['complaint_id']) . "</td>";                // Complaint Number
                                    echo "<td>" . htmlspecialchars($complaint['complainant']) . "</td>";                 // Complainant
                                    echo "<td>" . htmlspecialchars($complaint['complainant_type']) . "</td>";            // Complainant Type
                                    echo "<td>" . htmlspecialchars($complaint['subject']) . "</td>";                     // Subject
                                    echo "<td>" . (!empty($complaint['official_name']) ? htmlspecialchars($complaint['official_name']) : 'N/A') . "</td>"; // Official
                                    echo "<td>" . htmlspecialchars($complaint['date_of_incident']) . "</td>";            // Date of incident
                                    echo "<td>" . htmlspecialchars($complaint['time_of_incident']) . "</td>";            // Time of incident
                                    echo "<td>" . htmlspecialchars($complaint['location']) . "</td>";                    // Location
                                    echo "<td>" . htmlspecialchars($complaint['person_involved']) . "</td>";             // Person involved

                                    // Handle narration with length more than 19 characters
                                    echo "<td>";
                                    if (strlen($complaint['narration']) > 19) {
                                        echo htmlspecialchars(substr($complaint['narration'], 0, 19)) . "...";
                                    } else {
                                        echo htmlspecialchars($complaint['narration']);
                                    }
                                    echo "</td>";

                                    // Evidence description
                                    echo "<td>";
                                    if (empty($complaint['evidence_description'])) {
                                        echo "No description provided";
                                    } elseif (strlen($complaint['evidence_description']) > 16) {
                                        echo htmlspecialchars(substr($complaint['evidence_description'], 0, 16)) . "...";
                                    } else {
                                        echo htmlspecialchars($complaint['evidence_description']);
                                    }
                                    echo "</td>";

                                    // Display evidence images
                                    echo "<td>";
                                    if (!empty($groupedComplaint['images'])) {
                                        foreach ($groupedComplaint['images'] as $imagePath) {
                                            $imagePathEscaped = htmlspecialchars($imagePath);
                                            echo "<a href='" . $imagePathEscaped . "' target='_blank'>";
                                            echo "<img src='" . $imagePathEscaped . "' alt='Evidence Image' width='50' height='50' style='margin-right:5px;'>";
                                            echo "</a>";
                                        }
                                    } else {
                                        echo "No image evidence";
                                    }
                                    echo "</td>";

                                    echo "<td>";
                                    if (empty($complaint['remarks'])) {
                                        echo "N/A";
                                    } elseif (strlen($complaint['remarks']) > 16) {
                                        echo htmlspecialchars(substr($complaint['remarks'], 0, 16)) . "...";
                                    } else {
                                        echo htmlspecialchars($complaint['remarks']);
                                    }
                                    echo "</td>";

                                    $statusColor = ($complaint['status'] === 'resolved') ? 'green' : 
                                                   (($complaint['status'] === 'pending') ? 'orange' : 
                                                   (($complaint['status'] === 'closed') ? 'red' : 'gray'));
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
                                                     1px 1px 0 black; /* Creates an outline effect */
                                    '>" . htmlspecialchars($complaint['status']) . "</span></td>";


                                    echo '<td>';
                                    
                                    echo '<div style="display: flex; gap: 10px;">';
                                    if ($complaint['status'] === 'pending') {
                                        echo '
                                            <button class="resolve-button buttons" 
                                                style="background-color: #008000; color: white; border: none; padding: 8px 12px; border-radius: 5px; cursor: pointer; font-weight: bold; text-shadow: -1px -1px 0 black, 1px -1px 0 black, -1px 1px 0 black, 1px 1px 0 black;"
                                                onclick="showResolvePopup(\'' . htmlspecialchars($complaint['complaint_id'], ENT_QUOTES, 'UTF-8') . '\')">
                                                Resolve
                                            </button>
                                            <button class="close-button buttons" 
                                                style="background-color: #ff0707; color: white; border: none; padding: 8px 12px; border-radius: 5px; cursor: pointer; font-weight: bold; text-shadow: -1px -1px 0 black, 1px -1px 0 black, -1px 1px 0 black, 1px 1px 0 black;"
                                                onclick="showClosePopup(\'' . htmlspecialchars($complaint['complaint_id'], ENT_QUOTES, 'UTF-8') . '\')">
                                                Close
                                            </button>';
                                    } 
                                    // Button for opening modal, passing data including evidence as JSON
                                    $evidenceJson = json_encode($groupedComplaint['images']);
                                    echo "

                                    <button class='modal-open button' aria-haspopup='true' 
                                            data-complaint-id='" . htmlspecialchars($complaint['complaint_id']) . "' 
                                            data-complainant='" . htmlspecialchars($complaint['complainant']) . "' 
                                            data-complainant-type='" . htmlspecialchars($complaint['complainant_type']) . "' 
                                            data-subject='" . htmlspecialchars($complaint['subject']) . "' 
                                            data-official-id='" . htmlspecialchars($complaint['official_id']) . "' 
                                            data-date-of-incident='" . htmlspecialchars($complaint['date_of_incident']) . "' 
                                            data-time-of-incident='" . htmlspecialchars($complaint['time_of_incident']) . "' 
                                            data-location='" . htmlspecialchars($complaint['location']) . "' 
                                            data-person-involved='" . htmlspecialchars($complaint['person_involved']) . "' 
                                            data-narration='" . htmlspecialchars($complaint['narration']) . "' 
                                            data-status='" . htmlspecialchars($complaint['status']) . "' 
                                            data-evidence-description='" . htmlspecialchars($complaint['evidence_description']) . "' 
                                            data-evidence='" . htmlspecialchars($evidenceJson) . "' 
                                            onclick='populateComplaintModal(this)'>
                                            <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-pen' viewBox='0 0 16 16'>
                                                <path d='m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001m-.644.766a.5.5 0 0 0-.707 0L1.95 11.756l-.764 3.057 3.057-.764L14.44 3.854a.5.5 0 0 0 0-.708z'/>
                                            </svg>
                                        </button>
                                    
                                    </div>

                                </td>";

                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='15'>No complaint records found.</td></tr>";
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="15" id="paginationColspan">
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
            </div>
            
            <div id="resolvePopup" class="popup-overlay">
                <div class="popup-content">
                    <h2>Confirm Complaint Resolution</h2>
                    <p>Are you sure you want to resolve this complaint case?</p>
                    <form id="resolveForm" action="resolveReportController.php" method="POST">
                        <input type="hidden" name="complaint_id" id="resolve-complaint-id">
                        <input type="hidden" name="report_type" value="complaint"> 
                        <div class="popup-buttons">
                            <button type="submit" class="confirm-btn">Confirm</button>
                            <button type="button" class="cancel-btn" onclick="closeResolvePopup()">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>

            <div id="closePopup" class="popup-overlay">
                <div class="popup-content">
                    <h2>Confirm Complaint Closure</h2>
                    <p>Are you sure you want to close this complaint case? Please provide a reason for closure.</p>
                    <form id="closeForm" action="closeReportController.php" method="POST">
                        <input type="hidden" name="complaint_id" id="close-complaint-id" value="">
                        <input type="hidden" name="report_type" value="complaint"> 
                        <textarea name="remarks" id="close-reason" placeholder="Enter reason for closure" required style="width: 100%; height: 100px; resize: none;"></textarea>
                        <div class="popup-buttons">
                            <button type="submit" class="confirm-btn">Confirm</button>
                            <button type="button" class="cancel-btn" onclick="closeClosePopup()">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>

            <style>
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

            .popup-buttons {
                display: flex;
                justify-content: flex-end;
                gap: 12px;
            }

            .confirm-btn, .cancel-btn {
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

                .confirm-btn, .cancel-btn {
                    padding: 8px 16px;
                    font-size: 0.875rem;
                }
            }
            </style>

            <script>
            function showResolvePopup(caseId) {
                document.getElementById("resolve-complaint-id").value = caseId;
                const popup = document.getElementById("resolvePopup");
                popup.classList.add("active");
            }

            function closeResolvePopup() {
                const popup = document.getElementById("resolvePopup");
                popup.classList.remove("active");
                document.getElementById("resolveForm").reset();
            }

            function showClosePopup(caseId) {
                document.getElementById("close-complaint-id").value = caseId;
                const popup = document.getElementById("closePopup");
                popup.classList.add("active");
            }

            function closeClosePopup() {
                const popup = document.getElementById("closePopup");
                popup.classList.remove("active");
                document.getElementById("closeForm").reset();
            }

            document.getElementById("resolvePopup").addEventListener("click", function(e) {
                if (e.target === this) {
                    closeResolvePopup();
                }
            });

            document.getElementById("closePopup").addEventListener("click", function(e) {
                if (e.target === this) {
                    closeClosePopup();
                }
            });
            </script>

            <div class="modal-overlay-add">
                <div class="modal-add" id="accessible-modal-add" role="dialog" aria-modal="true" aria-labelledby="modal-title-add" aria-describedby="modal-description-add">
                    <div class="modal-header-add">
                        <h2 id="modal-title-add">Add Complaint</h2>
                        <button class="modal-close-add" aria-label="Close modal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-content-add">
                        <form id="addComplaintForm-add" action="adminComplaintsController.php" method="POST" enctype="multipart/form-data">
                            <!-- Hidden Account ID -->
                            <input type="hidden" id="account-id-add" name="account_id" value="<?= $_SESSION['account_id'] ?>">
                            

                            <!-- Resident Selection -->
                            <div class="form-group-add">
                                <label for="add-residents-add">Select Resident</label>
                                <input type="text" id="resident-search-main" placeholder="Search Residents" autocomplete="off" class="form-control-add">
                                <select id="add-residents-add" name="resident_id" size="1" style="overflow-y: auto;" required>
                                    <option value="" disabled selected>Select a resident</option>
                                    <option value="">Non-Resident</option>
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

                            <!-- Complainant -->
                            <div class="form-group-add">
                                <label for="complainant-add">Complainant Name</label>
                                <input type="text" id="complainant-add" name="complainant" placeholder="Enter complainant's name" required maxlength="50" readonly required>
                            </div>
                                
                            <!-- Hidden Complainant Type -->
                            <input type="hidden" id="complainant-type-add" name="complainant_type" value="resident">
                            <script>
                                document.getElementById('add-residents-add').addEventListener('change', function() {
                                    const selectedOption = this.options[this.selectedIndex];
                                    const complainantTypeInput = document.getElementById('complainant-type-add');

                                    if (selectedOption.value === "") {
                                        complainantTypeInput.value = 'non-resident'; // Set to non-resident if no resident is selected
                                    } else {
                                        complainantTypeInput.value = 'resident'; // Set to resident if a resident is selected
                                    }
                                });
                            </script>
                            <script>
                                document.getElementById('add-residents-add').addEventListener('change', function() {
                                    const selectedOption = this.options[this.selectedIndex];
                                    const complainantInput = document.getElementById('complainant-add');

                                    if (selectedOption.value) {
                                        // Remove age from the selected resident's name
                                        const residentName = selectedOption.text.replace(/\s\(\d+\syears\sold\)/, '');
                                        complainantInput.value = residentName; // Populate with selected resident's name
                                        complainantInput.setAttribute('readonly', true); // Make it readonly
                                    } else {
                                        complainantInput.value = ''; // Clear the input if no resident is selected
                                        complainantInput.removeAttribute('readonly'); // Make it editable
                                    }
                                });
                            </script>

                            <!-- Subject -->
                            <div class="form-group-add">
                                <label for="subject-add">Subject</label>
                                <input type="text" id="subject-add" name="subject" placeholder="Enter subject of complaint" required maxlength="50">
                            </div>

                            <!-- Date of Incident -->
                            <div class="form-group-add">
                                <label for="incident-date-add">Date of Incident</label>
                                <input type="date" id="incident-date-add" name="date_reported" required>
                            </div>

                            <!-- Time of Incident -->
                            <div class="form-group-add">
                                <label for="incident-time-add">Time of Incident</label>
                                <input type="time" id="incident-time-add" name="time_reported" required>
                            </div>

                            <!-- Location -->
                            <div class="form-group-add">
                                <label for="location-add">Location</label>
                                <input type="text" id="location-add" name="location" placeholder="Enter location of incident" required maxlength="50">
                            </div>

                            <!-- Official Selection -->
                            <div class="form-group-add">
                                <label for="add-officials-add">Official Handling the Case</label>
                                <select id="add-officials-add" name="official_id" size="1" style="overflow-y: auto;" required>
                                    <option value="" disabled selected>Select an official</option>
                                    <?php
                                    if (!empty($officials)) {
                                        foreach ($officials as $official) {
                                            echo "<option value='" . htmlspecialchars($official['official_id']) . "'>" 
                                                . htmlspecialchars($official['last_name']) . ", " 
                                                . htmlspecialchars($official['first_name']) . " " 
                                                . htmlspecialchars($official['middle_name']) 
                                                . " (" . htmlspecialchars($official['position']) . ")</option>";
                                        }
                                    } else {
                                        echo "<option>No officials found</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                           
                            <!-- Respondent -->
                            <div class="form-group-add">
                                <label for="respondent-add">Person Involved</label>
                                <input type="text" id="respondent-add" name="respondent" placeholder="Enter person involved" required maxlength="50">
                            </div>

                            <!-- Narration -->
                            <div class="form-group-add">
                                <label for="narration-add">Narration</label>
                                <textarea id="narration-add" name="narration" rows="5" placeholder="Describe the incident" required maxlength="200"
                                style="width: 99%; height: 150px; resize: none;"></textarea>
                            </div>

                            <!-- Evidence Description -->
                            <div class="form-group-add">
                                <label for="evidence-description-add">Evidence Name</label>
                                <input type="text" id="evidence-description-add" name="evidence_description" placeholder="Enter evidence title" maxlength="200" required>
                            </div>

                            <!-- Evidence Pictures -->
                            <div class="form-group-add">
                                <label for="evidence-pictures-add">
                                    Evidence Pictures <p style="color: gray; display: inline;">(Select up to 5 images, max total size 40 MB)</p>
                                    <p id="file-error-message" style="color:red; display:none;"></p>
                                </label>
                                <input type="file" id="evidence-pictures-add" name="evidence_picture[]" accept="image/*" value= "" multiple onchange="handleFileSelection()" data-min-files="1" data-max-files="5">
                                <div id="selected-files"></div> <!-- Container to show the selected files -->
                            </div>

                            
                            <input type="submit" class="button-add" value="Add Complaint">
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal Structure Update -->
            <div class="modal-overlay">
                <div class="modal" id="accessible-modal" role="dialog" aria-modal="true" aria-labelledby="modal-title" aria-describedby="modal-description">
                    <div class="modal-header">
                        <h2 id="modal-title">Update Complaint</h2>
                        <button class="modal-close" aria-label="Close modal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-content">
                    <form id="edit-blotter-form" action="adminComplaintsUpdate.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" id="edit-complaint-id" name="complaint_id">
                        <input type="hidden" id="account-id-add" name="account_id" value="<?= $_SESSION['account_id'] ?>">
                        <input type="hidden" id="edit-evidence-id" name="evidence_id">
                        <div class="form-group">
                            <label for="edit-complainant">Complainant</label>
                            <input type="text" id="edit-complainant" name="complainant" 
                                   readonly>
                        </div>
                        <div class="form-group">
                            <label for="edit-type-complainant">Complainant Type</label>
                            <input type="text" id="edit-type-complainant" readonly>
                        </div>
                        <div class="form-group">
                            <label for="edit-subject">Subject</label>
                            <input type="text" id="edit-subject" name="subject">
                        </div>
                        <div class="form-group">
                            <label for="edit-date-of-incident">Date of Incident</label>
                            <input type="date" id="edit-date-of-incident" name="date_reported">
                        </div>
                        <div class="form-group">
                            <label for="edit-time-of-incident">Time of Incident</label>
                            <input type="time" id="edit-time-of-incident" name="time_reported">
                        </div>
                        <div class="form-group">
                            <label for="edit-location">Location</label>
                            <input type="text" id="edit-location" name="location">
                        </div>
                          <!-- Official Selection -->
                        <div class="form-group">
                            <label for="edit-official">Official Handling the Case</label>
                            <select id="edit-official" name="official_id" size="1" style="overflow-y: auto;" required>
                                <option value="" disabled selected>Select an official</option>
                                <?php
                                if (!empty($officials)) {
                                    foreach ($officials as $official) {
                                        echo "<option value='" . htmlspecialchars($official['official_id']) . "'>" 
                                            . htmlspecialchars($official['last_name']) . ", " 
                                            . htmlspecialchars($official['first_name']) . " " 
                                            . htmlspecialchars($official['middle_name']) 
                                            . " (" . htmlspecialchars($official['position']) . ")</option>";
                                    }
                                } else {
                                    echo "<option>No officials found</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit-person-involved">Person Involved</label>
                            <input type="text" id="edit-person-involved" name="respondent">
                        </div>
                        <div class="form-group">
                        <label for="edit-narration">Narration</label>
                        <textarea id="edit-narration" name="narration"  style="width: 99%; height: 150px; resize: none;"></textarea>
                        </div>
                        <div class="form-group">
                        <label for="edit-status">Status</label>
                        <input type="text" id="edit-status" name="status" readonly>
                        </div>
                        <!-- Evidence Description -->
                        <div class="form-group-add">
                            <label for="edit-evidence-description">Evidence Name</label>
                            <input type="text" id="edit-evidence-description" name="evidence_description" placeholder="Enter evidence title" maxlength="200" required readonly>
                        </div>
                        <!-- Section for Existing Evidence Images -->
                        <div class="form-group">
                            <label for="edit-evidence">Existing Evidence Images</label>
                            <div id="existing-evidence"></div> <!-- Images will be appended here -->
                        </div>
                        <!-- Evidence Pictures -->
                        <!-- <div class="form-group">
                            <label for="edit-evidence-pictures">
                                Evidence Pictures <p style="color: gray; display: inline;">(Select up to 5 images, max total size 40 MB)</p>
                                <p id="edit-file-error-message" style="color:red; display:none;"></p>
                            </label>
                            <input type="file" id="edit-evidence-pictures" name="evidence_picture[]" accept="image/*" multiple onchange="handleEditFileSelection()" data-min-files="1" data-max-files="5">
                            <div id="edit-selected-files"></div>
                        </div> -->
                        <input class="button" type="submit" value="Update Complaint"></input>
                    </form>
                    </div>
                </div>
            </div>
        </section>
    </main>
</body>
<script>
    //AJAX FOR SEARCH
    $(document).ready(function() {
        // Search functionality with AJAX
        $('#searchInput').on('keyup', function() {
            let query = $(this).val(); // Get the input value
            console.log("Search Query:", query); // Check the input value

            // Perform the search query, or fetch all data if query is empty
            $.ajax({
                url: 'adminComplaintsSearch.php', // Ensure this is the correct path to your PHP file
                method: 'POST',
                cache: false,
                data: { query: query }, // Send the search query to the server, it can be empty
                success: function(data) {
                    console.log("Response Data:", data); // Log the response data to ensure it's correct
                    $('tbody').html(data); // Update the table body with the data (either all or filtered results)

                    // Attach click event for the dynamically loaded buttons
                    $('.modal-open').on('click', function() {
                        let caseId = $(this).data('case-id');
                        console.log("Clicked Case ID: " + caseId); // Debugging: check if the click event is captured

                        // Show the modal
                        $('.modal-overlay').addClass('is-active');
                    });
                },
                error: function(request, error) {
                    console.log("Search Error: " + error);
                }
            });
        });

        // Attach event listener to close the modal
        $('.modal-close').on('click', function() {
            $('.modal-overlay').removeClass('is-active');
        });
    });

    function handleEditFileSelection() {
        const fileInput = document.getElementById('edit-evidence-pictures');
        const files = Array.from(fileInput.files);
        const fileErrorMessage = document.getElementById('edit-file-error-message');
        const selectedFilesContainer = document.getElementById('edit-selected-files');
        const maxSizeInBytes = 41943040; // 40 MB
        const maxFiles = 5; // Maximum number of files
        let totalSize = 0;

        // Clear the error message and reset the selected files container
        fileErrorMessage.style.display = 'none';
        selectedFilesContainer.innerHTML = '';

        // Check if the number of files exceeds the allowed limit
        if (files.length > maxFiles) {
            fileErrorMessage.textContent = `You can only upload up to ${maxFiles} files.`;
            fileErrorMessage.style.display = 'block';
            fileInput.value = ''; // Clear the input
            return;
        }

        // Check if total file size exceeds the allowed limit
        for (const file of files) {
            totalSize += file.size;
            if (totalSize > maxSizeInBytes) {
                fileErrorMessage.textContent = `The total size of selected files exceeds the 40 MB limit. Please select smaller files.`;
                fileErrorMessage.style.display = 'block';
                fileInput.value = ''; // Clear the input
                return;
            }
        }

        // Display selected files
        files.forEach((file, index) => {
            const fileItem = document.createElement('div');
            fileItem.classList.add('file-item');

            const fileName = document.createElement('span');
            fileName.textContent = `File ${index + 1}: ${file.name}`;

            fileItem.appendChild(fileName);
            selectedFilesContainer.appendChild(fileItem);
        });
    }
</script>

<script>
    $(document).ready(function() {
        // Detect changes in the search box
        $('#resident-search-main').on('keyup', function() {
            let searchQuery = $(this).val().trim(); // Get the current value of the search input and trim whitespace
            
            // Perform AJAX request to fetch residents based on the search query
            $.ajax({
                url: 'adminResidentSearchModal.php',  // PHP file to handle the search
                type: 'POST',
                data: { searchQuery: searchQuery },
                success: function(response) {
                    // Populate the dropdown with the returned residents
                    $('#add-residents-add').html(response); // Ensure this matches the correct select element ID for the modal
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching residents:", error); // Log detailed error
                }
            });
        });
    });
</script>
</script>
<script>
function handleFileSelection() {
    const fileInput = document.getElementById('evidence-pictures-add');
    const files = Array.from(fileInput.files);
    const fileErrorMessage = document.getElementById('file-error-message');
    const selectedFilesContainer = document.getElementById('selected-files');
    const maxSizeInBytes = 41943040; // 40 MB
    const maxFiles = 5; // Maximum number of files
    let totalSize = 0;

    // Clear the error message and reset the selected files container
    fileErrorMessage.style.display = 'none';
    selectedFilesContainer.innerHTML = '';

    // Check if the number of files exceeds the allowed limit
    if (files.length > maxFiles) {
        fileErrorMessage.textContent = `You can only upload up to ${maxFiles} files.`;
        fileErrorMessage.style.display = 'block';
        fileInput.value = ''; // Clear the input
        return;
    }

    // Check if total file size exceeds the allowed limit
    for (const file of files) {
        totalSize += file.size;
        if (totalSize > maxSizeInBytes) {
            fileErrorMessage.textContent = `The total size of selected files exceeds the 40 MB limit. Please select smaller files.`;
            fileErrorMessage.style.display = 'block';
            fileInput.value = ''; // Clear the input
            return;
        }
    }

    // Display selected files
    files.forEach((file, index) => {
        const fileItem = document.createElement('div');
        fileItem.classList.add('file-item');

        const fileName = document.createElement('span');
        fileName.textContent = `File ${index + 1}: ${file.name}`;

        fileItem.appendChild(fileName);
        selectedFilesContainer.appendChild(fileItem);
    });
}

// Clear form data and reset file input on page load
document.addEventListener('DOMContentLoaded', () => {
    const selectedFilesContainer = document.getElementById('selected-files');
    const fileInput = document.getElementById('evidence-pictures-add');
    if (selectedFilesContainer) selectedFilesContainer.innerHTML = ''; // Clear selected files container
    if (fileInput) fileInput.value = ''; // Clear file input

    // Clear form fields
    const fieldsToClear = [
        'complainant-add',
        'subject-add',
        'incident-date-add',
        'incident-time-add',
        'location-add',
        'respondent-add',
        'narration-add',
        'evidence-description-add',
        'status-add'
    ];
    fieldsToClear.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) field.value = '';
    });
});

function populateComplaintModal(button) {
    // Get the complaint data from the button's data attributes
    var complaintId = button.getAttribute('data-complaint-id');
    var complainant = button.getAttribute('data-complainant');
    var complainantType = button.getAttribute('data-complainant-type');  // Corrected attribute
    var subject = button.getAttribute('data-subject');
    var dateOfIncident = button.getAttribute('data-date-of-incident');
    var timeOfIncident = button.getAttribute('data-time-of-incident');
    var location = button.getAttribute('data-location');
    var officialId = button.getAttribute('data-official-id');
    var personInvolved = button.getAttribute('data-person-involved');
    var narration = button.getAttribute('data-narration');
    var evidenceDescription = button.getAttribute('data-evidence-description');
    var status = button.getAttribute('data-status');

    // Populate the form inputs with the complaint data
    document.getElementById('edit-complaint-id').value = complaintId;
    document.getElementById('edit-complainant').value = complainant;
    document.getElementById('edit-type-complainant').value = complainantType;  // Populating complainant type

    // Dynamically set readonly based on complainant type
    const complainantInput = document.getElementById('edit-complainant');
    if (complainantType === 'non-resident') {
        complainantInput.removeAttribute('readonly'); // Remove readonly for non-resident
    } else {
        complainantInput.setAttribute('readonly', true); // Set readonly for resident
    }
    document.getElementById('edit-subject').value = subject;
    document.getElementById('edit-date-of-incident').value = dateOfIncident;
    document.getElementById('edit-time-of-incident').value = timeOfIncident;
    document.getElementById('edit-location').value = location;
    document.getElementById('edit-official').value = officialId;
    document.getElementById('edit-person-involved').value = personInvolved;
    document.getElementById('edit-narration').value = narration;
    document.getElementById('edit-evidence-description').value = evidenceDescription;
    document.getElementById('edit-status').value = status;

    // Fetch the existing evidence from the same page data
    var evidenceData = button.getAttribute('data-evidence');  // Store the evidence data in the button's data attribute
    var evidenceList = JSON.parse(evidenceData);  // Convert the JSON string back to an object

    // Clear the previous evidence images
    var evidenceContainer = document.getElementById('existing-evidence');
    evidenceContainer.innerHTML = '';

    // Define the base path to the upload folder
    var basePath = '../../img/image_evidences/complaint/';  // Ensure this matches your server structure

    // Loop through the evidence list and append images
    evidenceList.forEach(function(imagePath) {
        var fullImagePath = basePath + encodeURIComponent(imagePath.split('/').pop());  // Get the full image path

        // Create the anchor tag to make the image clickable
        var anchorElement = document.createElement('a');
        anchorElement.href = fullImagePath;  // Link to the full image
        anchorElement.target = '_blank';  // Open in a new tab

        // Create the image element
        var imgElement = document.createElement('img');
        imgElement.src = fullImagePath;  // Image path for the src attribute
        imgElement.alt = 'Evidence Image';
        imgElement.style.maxWidth = '100px';
        imgElement.style.margin = '5px';

        // Append the image to the anchor tag
        anchorElement.appendChild(imgElement);

        // Append the anchor tag (with the image inside) to the evidence container
        evidenceContainer.appendChild(anchorElement);
    });

    // Show the modal
    document.querySelector('.modal-overlay').classList.add('is-open');
}


</script>
</html>