<!DOCTYPE html>
<html lang="en">
<?php
// Check if the user is logged in by verifying the 'username' session variable
// if (!isset($_SESSION['username'])) {
//     // Generate a CSRF token if not already set
//     if (empty($_SESSION['csrf_token'])) {
//         $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
//     }

//     // Redirect to the login page
//     header('Location: ../../public/views/login.php');
//     exit(); // Ensure the script stops executing after redirection
// }

// If the user is logged in, proceed with the rest of your code


?>

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Resident Dashboard</title>
    <link rel="icon" href="favicon-32x32.png" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Metrophobic&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../css/admin/adminPortal.css">
    <link rel="stylesheet" href="../../css/admin/table.css">
    <link rel="stylesheet" href="../../css/main.css">
    <script src="https://kit.fontawesome.com/4907458c0c.js" crossorigin="anonymous"></script>
    <script src="../../js/main.js" defer></script>
    <script src="../../js/add-modal.js" defer></script>
    <script src="../../js/dashBoard.js" defer></script>
    <script src="../../js/update-modal.js" defer></script>
    <style>
        .main-head {
            background-color:rgb(15, 117, 54);
        }
        /* a:hover {
            background-color:rgb(145, 253, 187);        
        } */
        .hamburger {
            background-color: rgb(5, 88, 37);
        }
        .hamburger:hover {
            background-color: rgb(3, 71, 29);
        }
        .nav-list-item:hover {
            background-color: rgb(3, 71, 29);
        }
        .footer-nav-bar {
            color: rgb(3, 71, 29);
        }
        .header-nav {
            background-color:rgb(15, 117, 54);
        }
        .floating-menu a {
            background-color: rgb(15, 117, 54);
        }
        .floating-menu a:hover {
            background-color: rgb(3, 71, 29);
        }
        .head button:hover {
            color: rgb(3, 71, 29);
        }
        
        table {
        border-collapse: collapse;
        font-size: 15px;
        width: auto;
        table-layout: auto;
        max-width: 100%;
        }
        /* FORM WIDTH DESIGN/TEMPORARY */
        /* Add Form */
        .modal-overlay-add {
            padding-top: 150px;
        }
        .modal-add {
            max-width: 750px;
            width: 100%;
        }
        /* Edit Form */
        .modal-overlay {
            padding-top: 150px;
        }
        .modal {
            max-width: 750px;
            width: 100%;
        }
        

    </style>
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
                            <a href="residentDashboardController.php" class="nav-link">
                                <li class="nav-list-item">
                                    <i class="fa-solid fa-gauge"></i>
                                    <span class="link-text">DASHBOARD</span>
                                </li>
                            </a>

                            <!-- REQUESTS DROPDOWN -->
                            <a href="residentRequestController.php" class="nav-link">
                                <li class="nav-list-item" id="request-link">
                                    <i class="fa-solid fa-print"></i>
                                    <span class="link-text">REQUEST</span>
                                </li>
                            </a>

                            <!-- REPORTS DROPDOWN -->
                            <a href="residentReportController.php" class="nav-link">
                                <li class="nav-list-item" id="report-link">
                                    <i class="fa-solid fa-flag"></i>
                                    <span class="link-text">REPORT</span>
                                </li>
                            </a>


                            <!-- ACTIVITY LOG -->
                            <a href="residentActivityLogController.php" class="nav-link">
                                <li class="nav-list-item">
                                    <i class="fa-regular fa-clock"></i>
                                    <span class="link-text">ACTIVITY LOG</span>
                                </li>
                            </a>
                        </ul>

                        <!-- SETTINGS -->
                        <div class="footer-nav-bar"></div>
                        <ul class="nav-list">
                            <li class="nav-list-item">
                                <a href="/Barangay752ManagementSystem/public/views/admin/adminSettings.php" class="nav-link">
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
            <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <td colspan="12" class="search-column">
                                    <div class="search-bar">
                                        <p>Search</p>
                                        <input type="text" placeholder="Search Reports" id="searchInput">
                                        <button class="modal-open-add button" onclick="newEntry()" id="add-resident">Add Report +</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>Case Number</th>
                                <th>Complainant</th>
                                <th>Subject</th>
                                <th>Date of Incident</th>
                                <th>Time of Incident</th>
                                <th>Location</th>
                                <th>Respondent</th>
                                <th>Narration</th>
                                <th>Evidence Description</th>
                                <th>Evidence Image</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        if (!empty($reports)) {
                            $groupedReports = [];
                            
                            // Group reports by case_id
                            foreach ($reports as $report) {
                                $case_id = $report['case_id'];
                                
                                if (!isset($groupedReports[$case_id])) {
                                    // Initialize group for new case_id
                                    $groupedReports[$case_id] = [
                                        'case_info' => $report,
                                        'images' => []
                                    ];
                                }
                                
                                // Add evidence image if available
                                if (!empty($report['evidence_picture'])) {
                                    $groupedReports[$case_id]['images'][] = $report['evidence_picture'];
                                }
                            }

                            // Print the table rows
                            foreach ($groupedReports as $groupedReport) {
                                $report = $groupedReport['case_info'];
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($report['case_id']) . "</td>";                   // Case Number
                                echo "<td>" . htmlspecialchars($report['complainant']) . "</td>";                // Complainant
                                echo "<td>" . htmlspecialchars($report['subject']) . "</td>";                    // Subject
                                echo "<td>" . htmlspecialchars($report['date_of_incident']) . "</td>";           // Date of incident
                                echo "<td>" . htmlspecialchars($report['time_of_incident']) . "</td>";           // Time of incident
                                echo "<td>" . htmlspecialchars($report['location']) . "</td>";                   // Location
                                echo "<td>" . htmlspecialchars($report['person_involved']) . "</td>";            // Person involved

                                // Handle narration with length more than 19 characters
                                echo "<td>";
                                if (strlen($report['narration']) > 19) {
                                    echo htmlspecialchars(substr($report['narration'], 0, 19)) . "...";
                                } else {
                                    echo htmlspecialchars($report['narration']);
                                }
                                echo "</td>";

                                // Evidence description
                                echo "<td>" . htmlspecialchars($report['evidence_description']) . "</td>";

                               // Display evidence images
                                echo "<td>";
                                if (!empty($groupedReport['images'])) {
                                    foreach ($groupedReport['images'] as $imagePath) {
                                        $imagePathEscaped = htmlspecialchars($imagePath);
                                        echo "<a href='" . $imagePathEscaped . "' target='_blank'>";
                                        echo "<img src='" . $imagePathEscaped . "' alt='Evidence Image' width='50' height='50' style='margin-right:5px;'>";
                                        echo "</a>";
                                    }
                                } else {
                                    echo "No image evidence";
                                }
                                echo "</td>";


                                echo "<td>" . htmlspecialchars($report['status']) . "</td>";  // Status

                                // Button for opening modal, passing data including evidence as JSON
                                $evidenceJson = json_encode($groupedReport['images']);
                                echo "<td>
                                <button class='modal-open button' style='margin-bottom: 10px;; background-color:rgb(92, 92, 92);' aria-haspopup='true' 
                                    data-case-id='" . htmlspecialchars($report['case_id']) . "' 
                                    data-complainant='" . htmlspecialchars($report['complainant']) . "' 
                                    data-subject='" . htmlspecialchars($report['subject']) . "' 
                                    data-date-of-incident='" . htmlspecialchars($report['date_of_incident']) . "' 
                                    data-time-of-incident='" . htmlspecialchars($report['time_of_incident']) . "' 
                                    data-location='" . htmlspecialchars($report['location']) . "' 
                                    data-person-involved='" . htmlspecialchars($report['person_involved']) . "' 
                                    data-narration='" . htmlspecialchars($report['narration']) . "' 
                                    data-status='" . htmlspecialchars($report['status']) . "' 
                                    data-evidence-description='" . htmlspecialchars($report['evidence_description']) . "' 
                                    data-evidence='" . htmlspecialchars($evidenceJson) . "' 
                                    onclick='populateBlotterModal(this)'>
                                    <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='white' class='bi bi-eye' viewBox='0 0 16 16'>
                                        <path d='M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z'/>
                                        <path d='M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0'/>
                                    </svg>
                                </button>

                                <button class='modal-open button' aria-haspopup='true' 
                                    data-case-id='" . htmlspecialchars($report['case_id']) . "' 
                                    data-complainant='" . htmlspecialchars($report['complainant']) . "' 
                                    data-subject='" . htmlspecialchars($report['subject']) . "' 
                                    data-date-of-incident='" . htmlspecialchars($report['date_of_incident']) . "' 
                                    data-time-of-incident='" . htmlspecialchars($report['time_of_incident']) . "' 
                                    data-location='" . htmlspecialchars($report['location']) . "' 
                                    data-person-involved='" . htmlspecialchars($report['person_involved']) . "' 
                                    data-narration='" . htmlspecialchars($report['narration']) . "' 
                                    data-status='" . htmlspecialchars($report['status']) . "' 
                                    data-evidence-description='" . htmlspecialchars($report['evidence_description']) . "' 
                                    data-evidence='" . htmlspecialchars($evidenceJson) . "' 
                                    onclick='populateBlotterModal(this)'>
                                    <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-pen' viewBox='0 0 16 16'>
                                        <path d='m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001m-.644.766a.5.5 0 0 0-.707 0L1.95 11.756l-.764 3.057 3.057-.764L14.44 3.854a.5.5 0 0 0 0-.708z'/>
                                    </svg>
                                </button>

                                
                            </td>";

                            }
                        } else {
                            echo "<tr><td colspan='11'>No report records found.</td></tr>";
                        }
                        ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="12" id="paginationColspan">
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
            <div class="modal-overlay-add">
                <div class="modal-add" id="accessible-modal-add" role="dialog" aria-modal="true" aria-labelledby="modal-title-add" aria-describedby="modal-description-add">
                    <div class="modal-header-add">
                        <h2 id="modal-title-add">Add Report</h2>
                        <button class="modal-close-add" aria-label="Close modal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-content-add">
                        <form id="addBlotterForm-add" action="residentReportController.php" method="POST" enctype="multipart/form-data">
                            <!-- Hidden Account ID -->
                            <input type="hidden" id="account-id-add" name="account_id" value="<?= $_SESSION['account_id'] ?>">
                            <input type="hidden" id="resident-id-add" name="complainant" value="<?= $_SESSION['first_name'] . " " . $_SESSION['middle_name'] . " " . $_SESSION['last_name']  ?>">

                            <!-- Subject -->
                            <div class="form-group-add">
                                <label for="subject-add">Subject</label>
                                <input type="text" id="subject-add" name="subject" placeholder="Enter subject of blotter" required maxlength="50">
                            </div>

                            <!-- Type of Report -->
                            <div class="form-group-add">
                                <label for="type-of-report-add">Type of Report</label>
                                <select id="type-of-report-add" name="type_of_report" required>
                                    <option value="" disabled selected>Select Report Type</option>
                                    <option value="complaint">Complaint</option>
                                    <option value="incident">Incident</option>
                                </select>
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

                            <!-- Respondent -->
                            <div class="form-group-add">
                                <label for="respondent-add">Respondent</label>
                                <input type="text" id="respondent-add" name="respondent" placeholder="Enter respondent's name" required maxlength="50">
                            </div>

                            <!-- Narration -->
                            <div class="form-group-add">
                                <label for="narration-add">Narration</label>
                                <textarea id="narration-add" name="narration" rows="5" placeholder="Describe the incident" required maxlength="200" rows="20" cols="93" 
                                style="resize: none;"></textarea>
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

                            <input type="submit" class="button-add" value="Add Report">
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal Structure Update -->
            <div class="modal-overlay">
                <div class="modal" id="accessible-modal" role="dialog" aria-modal="true" aria-labelledby="modal-title" aria-describedby="modal-description">
                    <div class="modal-header">
                        <h2 id="modal-title">Update Blotter</h2>
                        <button class="modal-close" aria-label="Close modal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-content">
                    <form id="edit-blotter-form" action="adminBlotterUpdate.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" id="edit-case-id" name="case_id">
                        <input type="hidden" id="account-id-add" name="account_id" value="<?= $_SESSION['account_id'] ?>">
                        <div class="form-group">
                            <label for="edit-complainant">Complainant</label>
                            <input type="text" id="edit-complainant" name="complainant">
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
                        <div class="form-group">
                            <label for="edit-person-involved">Person Involved</label>
                            <input type="text" id="edit-person-involved" name="respondent">
                        </div>
                        <div class="form-group">
                        <label for="edit-narration">Narration</label>
                        <textarea id="edit-narration" name="narration" rows="10" cols="93" style="resize: none;"></textarea>
                        </div>
                        <div class="form-group">
                        <label for="edit-status">Status</label>
                        <select id="edit-status" name="status">
                            <option value="pending">Pending</option>
                            <option value="resolved">Resolved</option>
                            <option value="closed">Closed</option>
                        </select>
                        </div>
                        <!-- Evidence Description -->
                        <div class="form-group-add">
                            <label for="edit-evidence-description">Evidence Name</label>
                            <input type="text" id="edit-evidence-description" name="evidence_description" placeholder="Enter evidence title" maxlength="200" required>
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
                        <input class="button" type="submit" value="Update Blotter"></input>
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
                url: 'adminBlotterSearch.php', // Ensure this is the correct path to your PHP file
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

function populateBlotterModal(button) {
    // Get the blotter data from the button's data attributes
    var caseId = button.getAttribute('data-case-id');
    var complainant = button.getAttribute('data-complainant');
    var subject = button.getAttribute('data-subject');
    var dateOfIncident = button.getAttribute('data-date-of-incident');
    var timeOfIncident = button.getAttribute('data-time-of-incident');
    var location = button.getAttribute('data-location');
    var personInvolved = button.getAttribute('data-person-involved');
    var narration = button.getAttribute('data-narration');
    var evidenceDescription = button.getAttribute('data-evidence-description');
    var status = button.getAttribute('data-status');

    // Populate the form inputs with the blotter data
    document.getElementById('edit-case-id').value = caseId;
    document.getElementById('edit-complainant').value = complainant;
    document.getElementById('edit-subject').value = subject;
    document.getElementById('edit-date-of-incident').value = dateOfIncident;
    document.getElementById('edit-time-of-incident').value = timeOfIncident;
    document.getElementById('edit-location').value = location;
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
    var basePath = '../../img/image_evidences/';
    if (caseId.startsWith('COM')) {
        basePath += 'complaint/';
    } else if (caseId.startsWith('INC')) {
        basePath += 'incident/';
    } else if (caseId.startsWith('125')) {
        basePath += 'blotter/';
    }

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