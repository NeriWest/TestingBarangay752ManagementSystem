<!DOCTYPE html>
<html lang="en">
<?php
session_start();
require_once '../../config/sessionCheck.php';

$allowedRoles = ['Admin', 'Chairman', 'Secretary', 'Official'];
$roleName = $_SESSION['role_name'] ?? null;
if (!in_array($roleName, $allowedRoles)) {
    header('Location: ../login.php');
    exit();
}

if (!isset($_SESSION['username'])) {
    // Generate a CSRF token if not already set
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    // Redirect to the login page
    header('Location: ../login.php');
    exit(); // Ensure the script stops executing after redirection
}
function getAge($birthdate)
{
    // YYYY-MM-DD
    //$birthdate = '2004-08-24'; // No need to specify the time

    // Step 2: Create DateTime objects for the birthdate and the current date/time
    $birthdate_obj = new DateTime($birthdate); // Birthdate defaults to 00:00:00
    $current_date_obj = new DateTime(); // Current date and time

    // Step 3: Calculate the difference between the current date/time and the birthdate
    $age_interval = $current_date_obj->diff($birthdate_obj);

    // Step 4: Get the exact age in years
    $years = $age_interval->y;

    return $years;
}

function getAddress($houseNumber, $street)
{
    $address = $houseNumber . " " .  $street . " Singalong, Malate, Manila";
    return $address;
}

$accountId = ($_SESSION['account_id']);
?>


<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Resident</title>
    <script src="https://kit.fontawesome.com/4907458c0c.js" crossorigin="anonymous"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Metrophobic&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../css/main.css">
    <link rel="stylesheet" href="../../css/admin/table.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="../../js/add-modal.js" defer></script>
    <script src="../../js/dashBoard.js" defer></script>
    <script src="../../js/main.js" defer></script>
    <script src="../../js/update-modal.js" defer></script>
    <style>
        table {
            border-collapse: collapse;
            font-size: 15px;
            width: auto;
            table-layout: auto;
            width: 100%;
        }

        /* FORM WIDTH DESIGN/TEMPORARY */
        /* Add Form */
        .modal-overlay-add {
            padding-top: 600px;
        }

        .modal-add {
            max-width: 750px;
            width: 100%;
        }

        /* Edit Form */
        .modal-overlay {
            padding-top: 600px;
        }

        .modal {
            max-width: 750px;
            width: 100%;
        }

        .table-toggle {
            margin-bottom: 1rem;
        }

        .table-toggle .button {
            margin-right: 10px;
            padding: 10px 20px;
            cursor: pointer;
            font-weight: bold;
            border: 1px solid #ddd;
            background-color: rgb(110, 110, 110);
            transition: background-color 0.3s;
        }

        .table-toggle .button.active {
            background-color: #007bff;
            color: #fff;
        }
        td {
            word-wrap: break-word;
            word-break: break-word;
            color: #333;
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
                            <a href="../logoutController.php">Log Out</a>
                        </div>
                    </div>
                </nav>
            </div>
            <section class="showcase">
                <div class="content" id="table-content">


                    <!-- END MESSAGE -->
                    <!-- Table Toggle Button -->
                    <!-- BUTTON FOR SELECTING SENIORS OR RESIDENT -->

                    <h1 id="table-title" style="font-size: 30px;">Resident List</h1>
                    <br>
                    <div class="table-toggle">
                        <button id="residentBtn" class="button active" onclick="showResidentTable()">Resident</button>
                        <button id="seniorBtn" class="button" onclick="showSeniorTable()">Senior</button>
                        <button id="residentBtn" class="button" onclick="showNewResidentTable()">New Resident</button>
                    </div>
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <td colspan="19" class="search-column">
                                        <div class="search-bar">
                                            <p>Search</p>
                                            <input type="text" placeholder="Search Resident" id="searchInput">
                                            <!-- <button class="modal-open-add button" id="add-resident">Add Resident +</button> -->
                                            <!-- <button class="add-resident button">Upload CSV +</button> -->
                                            <!-- <button class="button" style="background-color: red;" onclick="window.open('export/residentPdfExport.php', '_blank')">Print Resident PDF</button>
                                            <button class="button" style="background-color: red;" onclick="window.open('export/seniortPdfExport.php', '_blank')">Print Senior PDF</button>
                                            <button class="button" style="background-color: green;" onclick="window.open('export/residentExcelExport.php', '_blank')">Print Excel</button> -->
                                        </div>
                                    </td>
                                </tr>
                                <tr id="table-header">
                                    <th>Last Name</th>
                                    <th>First Name</th>
                                    <th>Middle Name</th>
                                    <th>Suffix</th>
                                    <th>Mobile No.</th>
                                    <th>Sex</th>
                                    <th>Age</th>
                                    <th>Address</th>
                                    <th>Date of Birth</th>
                                    <th>Civil Status</th>
                                    <th>House No.</th>
                                    <th>Street</th>
                                    <th>Citizenship</th>
                                    <th>Email</th>
                                    <th>Occupation</th>
                                    <th>Disability</th>
                                    <th>Voter</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="table-body">
                            <tfoot>
                                <tr>
                                    <th colspan="19" id="paginationColspan">
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
                                                    Page <?php echo $page; ?> of 
                                                    <?php 
                                                        if (isset($_GET['table']) && $_GET['table'] === 'senior') {
                                                            echo $totalSeniorPages;
                                                        } elseif (isset($_GET['table']) && $_GET['table'] === 'newResident') {
                                                            echo $totalNewResidentPages;
                                                        } else {
                                                            echo $totalPages;
                                                        }
                                                    ?>
                                                </span>

                                                <!-- Next Button -->
                                                <?php 
                                                    $currentTotalPages = isset($_GET['table']) && $_GET['table'] === 'senior' ? $totalSeniorPages : 
                                                                         (isset($_GET['table']) && $_GET['table'] === 'newResident' ? $totalNewResidentPages : $totalPages);
                                                ?>
                                                <?php if ($page < $currentTotalPages) { ?>
                                                    <a href="?page=<?php echo $page + 1; ?>&table=<?php echo isset($_GET['table']) ? $_GET['table'] : 'resident'; ?>" class="button" style="padding: 10px 15px; border: none; background-color: #007bff; color: white; cursor: pointer; margin-left: 10px; border-radius: 5px;">Next</a>
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
            </section>

            <!-- Modal Structure Update -->
            <div class="modal-overlay">
                <div class="modal" id="accessible-modal" role="dialog" aria-modal="true" aria-labelledby="modal-title" aria-describedby="modal-description">
                    <div class="modal-header">
                        <h2 id="modal-title">View Resident</h2>
                        <button class="modal-close" aria-label="Close modal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-content">
                        <form id="updateForm" action="adminResidentUpdate.php" method="POST">
                            <input type="text" id="account-id" name="account_id" value="<?= $accountId ?>" hidden>
                            <input type="hidden" id="resident-id" name="resident_id">
                            <div class="form-group">
                                <label for="edit-last-name">Last Name </label>
                                <input type="text" id="edit-last-name" name="last-name" value="" readonly>
                            </div>
                            <div class="form-group">
                                <label for="edit-first-name">First Name </label>
                                <input type="text" id="edit-first-name" name="first-name" readonly>
                            </div>
                            <div class="form-group">
                                <label for="edit-middle-name">Middle Name </label>
                                <input type="text" id="edit-middle-name" name="middle-name" readonly>
                            </div>
                            <div class="form-group">
                                <label for="edit-suffix">Suffix</label>
                                <input type="text" id="edit-suffix" name="suffix" readonly>
                            </div>
                            <div class="form-group">
                                <label for="edit-phone">Mobile No.</label>
                                <input type="text" id="edit-phone" name="phonenumber" readonly>
                            </div>
                            <div class="form-group">
                                <label for="edit-sex">Sex</label>
                                <input type="text" id="edit-sex" name="sex" readonly>
                            </div>
                            <div class="form-group">
                                <label for="edit-dob">Date of Birth</label>
                                <input type="text" id="edit-dob" name="date-of-birth" readonly>
                            </div>
                            <div class="form-group">
                                <label for="edit-civil-status">Civil Status</label>
                                <input type="text" id="edit-civil-status" name="civil-status" readonly>
                            </div>
                            <div class="form-group">
                                <label for="edit-house-number">House No.</label>
                                <input type="text" id="edit-house-number" name="house-number" readonly>
                            </div>
                            <div class="form-group">
                                <label for="edit-street">Street</label>
                                <input type="text" id="edit-street" name="street" readonly>
                            </div>
                            <div class="form-group">
                                <label for="edit-citizenship">Citizenship</label>
                                <input type="text" id="edit-citizenship" name="citizenship" readonly>
                            </div>
                            <div class="form-group">
                                <label for="edit-email">Email</label>
                                <input type="text" id="edit-email" name="email" readonly>
                            </div>
                            <div class="form-group">
                                <label for="edit-occupation">Occupation</label>
                                <input type="text" id="edit-occupation" name="occupation" readonly>
                            </div>
                            <div class="form-group">
                                <label for="edit-disability">Disability</label>
                                <input type="text" id="edit-disability" name="disability" readonly>
                            </div>
                            <div class="form-group">
                                <label for="edit-voter">Voter Status</label>
                                <input type="text" id="edit-voter-status" name="voter_status" readonly>
                            </div>
                            <div class="form-group">
                                <label for="edit-status">Status</label>
                                <input type="text" id="edit-status" name="status" readonly>
                            </div>
                            <div class="form-group">
                                <label for="edit-osca-id">OSCA ID</label>
                                <input type="text" id="edit-osca-id" name="osca-id" readonly>
                            </div>
                            <div class="form-group">
                                <label for="edit-osca-id-date">OSCA ID Date Issued</label>
                                <input type="text" id="edit-osca-id-date" name="osca-id-issued" readonly>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
                                                </section>
            

            
    </main>
</body>
<script>
    function showSeniorTable() {
        document.getElementById('table-title').textContent = 'Senior List';
        document.getElementById('table-header').innerHTML = `
        <th>Last Name</th>
        <th>First Name</th>
        <th>Middle Name</th>
        <th>Suffix</th>
        <th>Mobile No.</th>
        <th>Sex</th>
        <th>Age</th>
        <th>Address</th>
        <th>Date of Birth</th>
        <th>Civil Status</th>
        <th>House No.</th>
        <th>Street</th>
        <th>Citizenship</th>
        <th>Email</th>
        <th>Occupation</th>
        <th>Disability</th>
        <th>Voter</th>
        <th>OSCA ID</th>
        <th>OSCA Date Issued</th>
        <th>Status</th>
        <th>Action</th>
    `;

        const tableBody = document.getElementById('table-body');
        tableBody.innerHTML = ''; // Clear the table before adding new rows

        if (seniors && seniors.length > 0) {
            seniors.forEach(senior => {
                const row = document.createElement('tr');
                row.innerHTML = `
                <td>${senior.last_name}</td>
                <td>${senior.first_name}</td>
                <td>${senior.middle_name}</td>
                <td>${senior.suffix}</td>
                <td>${senior.cellphone_number}</td>
                <td>${senior.sex}</td>
                <td>${senior.age}</td>
                <td>${senior.house_number} ${senior.street} Singalong, Malate, Manila</td>
                <td>${senior.date_of_birth}</td>
                <td>${senior.civil_status}</td>
                <td>${senior.house_number}</td>
                <td>${senior.street}</td>
                <td>${senior.citizenship}</td>
                <td>${senior.email}</td>
                <td>${senior.occupation}</td>
                <td>${senior.disability}</td>
                <td>${senior.voter_status}</td>
                <td class='senior-only'>${senior.osca_id}</td>
                <td class='senior-only'>${senior.osca_date_issued}</td>
                <td>${senior.status}</td>
                <td>
                    <button class="modal-open button" aria-haspopup="true"  style='background-color:rgb(92, 92, 92);' 
                        data-resident-id="${senior.resident_id}" 
                        data-last-name="${senior.last_name}" 
                        data-first-name="${senior.first_name}" 
                        data-middle-name="${senior.middle_name}" 
                        data-suffix="${senior.suffix}" 
                        data-phone-number="${senior.cellphone_number}" 
                        data-sex="${senior.sex}" 
                        data-date-of-birth="${senior.date_of_birth}" 
                        data-civil-status="${senior.civil_status}" 
                        data-house-number="${senior.house_number}" 
                        data-street="${senior.street}" 
                        data-citizenship="${senior.citizenship}" 
                        data-occupation="${senior.occupation}" 
                        data-disability="${senior.disability}" 
                        data-email="${senior.email}" 
                        data-voter-status="${senior.voter_status}" 
                        data-status="${senior.status}" 
                        data-osca-id="${senior.osca_id}" 
                        data-osca-date-issued="${senior.osca_date_issued}" 
                        onclick="populateModal(this)">
                        <i class='fa-solid fa-eye'></i>
                    </button>
                </td>
            `;
                tableBody.appendChild(row);
            });
        } else {
            const row = document.createElement('tr');
            row.innerHTML = `<td colspan="21">No seniors found.</td>`;
            tableBody.appendChild(row);
        }

        // Toggle button active state
        document.querySelectorAll('.table-toggle .button').forEach(btn => btn.classList.remove('active'));
        document.getElementById('seniorBtn').classList.add('active');

        // Show senior-specific columns
        document.querySelectorAll('.senior-only').forEach(el => {
            el.style.display = 'table-cell';
        });

        // Set colspan for search and pagination
        document.querySelector('.search-column').setAttribute('colspan', 21);
        document.querySelector('#paginationColspan').setAttribute('colspan', 21);

        // Save state
        localStorage.setItem('tableState', 'senior');

        // Update pagination visibility
        updatePaginationVisibility();
    }

    function showResidentTable() {
        document.getElementById('table-title').textContent = 'Resident List';
        document.getElementById('table-header').innerHTML = `
        <th>Last Name</th>
        <th>First Name</th>
        <th>Middle Name</th>
        <th>Suffix</th>
        <th>Mobile No.</th>
        <th>Sex</th>
        <th>Age</th>
        <th>Address</th>
        <th>Date of Birth</th>
        <th>Civil Status</th>
        <th>House No.</th>
        <th>Street</th>
        <th>Citizenship</th>
        <th>Email</th>
        <th>Occupation</th>
        <th>Disability</th>
        <th>Voter</th>
        <th>Status</th>
        <th>Action</th>
    `;

        const tableBody = document.getElementById('table-body');
        tableBody.innerHTML = ''; // Clear the table before adding new rows

        if (residents && residents.length > 0) {
            residents.forEach(resident => {
                const row = document.createElement('tr');
                row.innerHTML = `
                <td>${resident.last_name}</td>
                <td>${resident.first_name}</td>
                <td>${resident.middle_name}</td>
                <td>${resident.suffix}</td>
                <td>${resident.cellphone_number}</td>
                <td>${resident.sex}</td>
                <td>${resident.age}</td>
                <td>${resident.house_number} ${resident.street} Singalong, Malate, Manila</td>
                <td>${resident.date_of_birth}</td>
                <td>${resident.civil_status}</td>
                <td>${resident.house_number}</td>
                <td>${resident.street}</td>
                <td>${resident.citizenship}</td>
                <td>${resident.email}</td>
                <td>${resident.occupation}</td>
                <td>${resident.disability}</td>
                <td>${resident.voter_status}</td>
                <td>${resident.status}</td>
                <td>
                    <button class="modal-open button" aria-haspopup="true" style='background-color:rgb(92, 92, 92);' 
                        data-resident-id="${resident.resident_id}" 
                        data-last-name="${resident.last_name}" 
                        data-first-name="${resident.first_name}" 
                        data-middle-name="${resident.middle_name}" 
                        data-suffix="${resident.suffix}" 
                        data-phone-number="${resident.cellphone_number}" 
                        data-sex="${resident.sex}" 
                        data-date-of-birth="${resident.date_of_birth}" 
                        data-civil-status="${resident.civil_status}" 
                        data-house-number="${resident.house_number}" 
                        data-street="${resident.street}" 
                        data-citizenship="${resident.citizenship}" 
                        data-occupation="${resident.occupation}" 
                        data-disability="${resident.disability}" 
                        data-email="${resident.email}" 
                        data-voter-status="${resident.voter_status}" 
                        data-status="${resident.status}" 
                        data-osca-id="${resident.osca_id || ''}" 
                        data-osca-date-issued="${resident.osca_date_issued || ''}" 
                        onclick="populateModal(this)">
                        <i class='fa-solid fa-eye'></i>
                    </button>
                </td>
            `;
                tableBody.appendChild(row);
            });
        } else {
            const row = document.createElement('tr');
            row.innerHTML = `<td colspan="19">No residents found.</td>`;
            tableBody.appendChild(row);
        }

        // Toggle button active state
        document.querySelectorAll('.table-toggle .button').forEach(btn => btn.classList.remove('active'));
        document.querySelector('#residentBtn').classList.add('active');

        // Hide senior-only columns
        document.querySelectorAll('.senior-only').forEach(el => {
            el.classList.add('hidden');
            el.style.display = 'none';
        });

        // Set colspan for search and pagination
        document.querySelector('.search-column').setAttribute('colspan', 19);
        document.querySelector('#paginationColspan').setAttribute('colspan', 19);

        // Save state
        localStorage.setItem('tableState', 'resident');

        // Update pagination visibility
        updatePaginationVisibility();
    }

    function showNewResidentTable() {
        document.getElementById('table-title').textContent = 'New Resident List';
        document.getElementById('table-header').innerHTML = `
        <th>Last Name</th>
        <th>First Name</th>
        <th>Middle Name</th>
        <th>Suffix</th>
        <th>Mobile No.</th>
        <th>Sex</th>
        <th>Age</th>
        <th>Address</th>
        <th>Date of Birth</th>
        <th>Civil Status</th>
        <th>House No.</th>
        <th>Street</th>
        <th>Citizenship</th>
        <th>Email</th>
        <th>Occupation</th>
        <th>Disability</th>
        <th>Voter</th>
        <th>Status</th>
        <th>Action</th>
    `;

        const tableBody = document.getElementById('table-body');
        tableBody.innerHTML = ''; // Clear the table before adding new rows

        if (newResidents && newResidents.length > 0) {
            newResidents.forEach(resident => {
                const row = document.createElement('tr');
                row.innerHTML = `
                <td>${resident.last_name}</td>
                <td>${resident.first_name}</td>
                <td>${resident.middle_name}</td>
                <td>${resident.suffix}</td>
                <td>${resident.cellphone_number}</td>
                <td>${resident.sex}</td>
                <td>${resident.age}</td>
                <td>${resident.house_number} ${resident.street} Singalong, Malate, Manila</td>
                <td>${resident.date_of_birth}</td>
                <td>${resident.civil_status}</td>
                <td>${resident.house_number}</td>
                <td>${resident.street}</td>
                <td>${resident.citizenship}</td>
                <td>${resident.email}</td>
                <td>${resident.occupation}</td>
                <td>${resident.disability}</td>
                <td>${resident.voter_status}</td>
                <td>${resident.status}</td>
                <td>
                    <button class="modal-open button" aria-haspopup="true"  style='background-color:rgb(92, 92, 92);' 
                        data-resident-id="${resident.resident_id}" 
                        data-last-name="${resident.last_name}" 
                        data-first-name="${resident.first_name}" 
                        data-middle-name="${resident.middle_name}" 
                        data-suffix="${resident.suffix}" 
                        data-phone-number="${resident.cellphone_number}" 
                        data-sex="${resident.sex}" 
                        data-date-of-birth="${resident.date_of_birth}" 
                        data-civil-status="${resident.civil_status}" 
                        data-house-number="${resident.house_number}" 
                        data-street="${resident.street}" 
                        data-citizenship="${resident.citizenship}" 
                        data-occupation="${resident.occupation}" 
                        data-disability="${resident.disability}" 
                        data-email="${resident.email}" 
                        data-voter-status="${resident.voter_status}" 
                        data-status="${resident.status}" 
                        data-osca-id="${resident.osca_id || ''}" 
                        data-osca-date-issued="${resident.osca_date_issued || ''}" 
                        onclick="populateModal(this)">
                        <i class='fa-solid fa-eye'></i>
                    </button>
                </td>
            `;
                tableBody.appendChild(row);
            });
        } else {
            const row = document.createElement('tr');
            row.innerHTML = `<td colspan="19">No new residents found.</td>`;
            tableBody.appendChild(row);
        }

        // Toggle button active state
        document.querySelectorAll('.table-toggle .button').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('#residentBtn')[1].classList.add('active'); // Select the New Resident button

        // Hide senior-only columns
        document.querySelectorAll('.senior-only').forEach(el => {
            el.classList.add('hidden');
            el.style.display = 'none';
        });

        // Set colspan for search and pagination
        document.querySelector('.search-column').setAttribute('colspan', 19);
        document.querySelector('#paginationColspan').setAttribute('colspan', 19);

        // Save state
        localStorage.setItem('tableState', 'newResident');

        // Update pagination visibility
        updatePaginationVisibility();
    }

    // Helper function to update pagination visibility
    function updatePaginationVisibility() {
        const tableState = localStorage.getItem('tableState');
        const pagination = document.querySelector('.pagination');
        if (pagination) {
            let totalRecords = tableState === 'senior' ? totalSeniors :
                              tableState === 'newResident' ? totalNewResidents :
                              totalResidents;
            pagination.style.display = totalRecords > recordsPerPage ? 'flex' : 'none';
        }
    }

    // Auto-load the correct table view based on previous state
    window.addEventListener('load', function() {
        const savedTableState = localStorage.getItem('tableState');
        if (savedTableState === 'senior') {
            showSeniorTable();
        } else if (savedTableState === 'newResident') {
            showNewResidentTable();
        } else {
            showResidentTable();
        }
    });

    // Search function for Residents
    function searchResidents(query) {
        $.ajax({
            url: 'adminResidentSearch.php',
            method: 'POST',
            cache: false,
            data: {
                query: query,
                page: 1 // Reset to first page on search
            },
            success: function(data) {
                $('tbody').html(data);
                bindPaginationLinks();
                bindUpdateButton();
                updatePaginationVisibility();
            },
            error: function(request, error) {
                console.log("Resident Search Error: " + error);
            }
        });
    }

    // Search function for New Residents
    function searchNewResidents(query) {
        $.ajax({
            url: 'adminNewResidentSearch.php',
            method: 'POST',
            cache: false,
            data: {
                query: query,
                page: 1 // Reset to first page on search
            },
            success: function(data) {
                $('tbody').html(data);
                bindPaginationLinks();
                bindUpdateButton();
                updatePaginationVisibility();
            },
            error: function(request, error) {
                console.log("New Resident Search Error: " + error);
            }
        });
    }

    // Search function for Seniors
    function searchSeniors(query) {
        $.ajax({
            url: 'adminSeniorSearch.php',
            method: 'POST',
            cache: false,
            data: {
                query: query,
                page: 1 // Reset to first page on search
            },
            success: function(data) {
                $('tbody').html(data);
                bindPaginationLinks();
                bindUpdateButton();
                updatePaginationVisibility();
            },
            error: function(request, error) {
                console.log("Senior Search Error: " + error);
            }
        });
    }

    // Pagination click event handler
    function bindPaginationLinks() {
        $(document).on('click', '.pagination-link', function(e) {
            e.preventDefault();
            var page = $(this).attr('href').split('page=')[1];
            var query = $('#searchInput').val(); // Get the search query, if any

            // Check which table is active
            const savedTableState = localStorage.getItem('tableState');
            var url = savedTableState === 'senior' ? 'adminSeniorController.php' : 
                      savedTableState === 'newResident' ? 'adminNewResidentController.php' : 
                      'adminResidentController.php';

            $.ajax({
                url: url,
                type: 'GET',
                data: {
                    page: page,
                    query: query
                },
                success: function(response) {
                    $('#table-body').html($(response).find('#table-body').html());
                    $('#paginationColspan').html($(response).find('#paginationColspan').html());
                    bindPaginationLinks();
                    var totalPages = savedTableState === 'senior' ? totalSeniorPages :
                                     savedTableState === 'newResident' ? totalNewResidentPages :
                                     totalPages;
                    var currentPage = page;
                    updatePaginationControls(currentPage, totalPages);
                },
                error: function() {
                    alert('Failed to load data. Please try again.');
                }
            });
        });
    }

    // Function to update pagination controls dynamically
    function updatePaginationControls(currentPage, totalPages) {
        const paginationColspan = document.querySelector('#paginationColspan');
        const savedTableState = localStorage.getItem('tableState');
        let colspan = savedTableState === 'senior' ? 21 : 19;

        if (totalPages > 1) {
            let paginationHtml = `
                <div class="pagination" style="display: flex; justify-content: center; align-items: center; margin-top: 20px;">
                    <!-- Previous Button -->
                    ${currentPage > 1 ? 
                        `<a href="?page=${parseInt(currentPage) - 1}" class="button pagination-link" style="padding: 10px 15px; border: none; background-color: #007bff; color: white; cursor: pointer; margin-right: 10px; border-radius: 5px;">Previous</a>` :
                        `<span class="button disabled" style="padding: 10px 15px; border: none; background-color: grey; color: white; cursor: not-allowed; margin-right: 10px; border-radius: 5px;">Previous</span>`
                    }
                    <!-- Page Numbers -->
                    <span id="currentPage" style="font-size: 16px; font-weight: bold; margin: 0 10px;">
                        Page ${currentPage} of ${totalPages}
                    </span>
                    <!-- Next Button -->
                    ${currentPage < totalPages ? 
                        `<a href="?page=${parseInt(currentPage) + 1}" class="button pagination-link" style="padding: 10px 15px; border: none; background-color: #007bff; color: white; cursor: pointer; margin-left: 10px; border-radius: 5px;">Next</a>` :
                        `<span class="button disabled" style="padding: 10px 15px; border: none; background-color: grey; color: white; cursor: not-allowed; margin-left: 10px; border-radius: 5px;">Next</span>`
                    }
                </div>
            `;
            paginationColspan.innerHTML = paginationHtml;
            paginationColspan.setAttribute('colspan', colspan);
        } else {
            paginationColspan.innerHTML = '';
            paginationColspan.setAttribute('colspan', colspan);
        }
    }

    // Event listener for previous/next page buttons
    $('#prevPage, #nextPage').on('click', function(e) {
        e.preventDefault();
        var currentPage = parseInt($('#currentPage').text().split(' ')[1]) || 1;
        var newPage = $(this).attr('id') === 'prevPage' ? currentPage - 1 : currentPage + 1;
        var query = $('#searchInput').val();

        if (newPage > 0) {
            const savedTableState = localStorage.getItem('tableState');
            var url = savedTableState === 'senior' ? 'adminSeniorController.php' : 
                      savedTableState === 'newResident' ? 'adminNewResidentController.php' : 
                      'adminResidentController.php';

            $.ajax({
                url: url,
                type: 'GET',
                data: {
                    page: newPage,
                    query: query
                },
                success: function(response) {
                    $('#table-body').html($(response).find('#table-body').html());
                    var totalPages = savedTableState === 'senior' ? totalSeniorPages :
                                     savedTableState === 'newResident' ? totalNewResidentPages :
                                     totalPages;
                    updatePaginationControls(newPage, totalPages);
                    bindPaginationLinks();
                },
                error: function() {
                    alert('Failed to navigate pages. Please try again.');
                }
            });
        }
    });

    // Bind update button for modal
    function bindUpdateButton() {
        $('.modal-open').off('click').on('click', function() {
            let residentId = $(this).data('resident-id');
            populateModal(this);
            $('.modal-overlay').addClass('is-active');
        });
    }

    // Search handler based on table state
    $(document).ready(function() {
        $('#searchInput').on('keyup', function() {
            let query = $(this).val();
            const savedTableState = localStorage.getItem('tableState');

            if (savedTableState === 'senior') {
                searchSeniors(query);
            } else if (savedTableState === 'newResident') {
                searchNewResidents(query);
            } else {
                searchResidents(query);
            }
        });

        $('.modal-close').on('click', function() {
            $('.modal-overlay').removeClass('is-active');
            $('.modal-overlay').removeClass('is-open');
        });

        bindPaginationLinks();
    });

    function populateModal(button) {
        var residentId = button.getAttribute('data-resident-id');
        var lastName = button.getAttribute('data-last-name');
        var firstName = button.getAttribute('data-first-name');
        var middleName = button.getAttribute('data-middle-name');
        var suffix = button.getAttribute('data-suffix');
        var phoneNumber = button.getAttribute('data-phone-number');
        var sex = button.getAttribute('data-sex');
        var dateOfBirth = button.getAttribute('data-date-of-birth');
        var civilStatus = button.getAttribute('data-civil-status');
        var houseNumber = button.getAttribute('data-house-number');
        var street = button.getAttribute('data-street');
        var citizenship = button.getAttribute('data-citizenship');
        var occupation = button.getAttribute('data-occupation');
        var disability = button.getAttribute('data-disability');
        var email = button.getAttribute('data-email');
        var voterStatus = button.getAttribute('data-voter-status');
        var status = button.getAttribute('data-status');
        var oscaId = button.getAttribute('data-osca-id');
        var oscaIdIssued = button.getAttribute('data-osca-date-issued');

        document.getElementById('resident-id').value = residentId;
        document.getElementById('edit-last-name').value = lastName;
        document.getElementById('edit-first-name').value = firstName;
        document.getElementById('edit-middle-name').value = middleName;
        document.getElementById('edit-suffix').value = suffix;
        document.getElementById('edit-phone').value = phoneNumber;
        document.getElementById('edit-sex').value = sex;
        document.getElementById('edit-dob').value = dateOfBirth;
        document.getElementById('edit-civil-status').value = civilStatus;
        document.getElementById('edit-house-number').value = houseNumber;
        document.getElementById('edit-street').value = street;
        document.getElementById('edit-citizenship').value = citizenship;
        document.getElementById('edit-occupation').value = occupation;
        document.getElementById('edit-disability').value = disability;
        document.getElementById('edit-email').value = email;
        document.getElementById('edit-voter-status').value = voterStatus;
        document.getElementById('edit-status').value = status;
        document.getElementById('edit-osca-id').value = oscaId;
        document.getElementById('edit-osca-id-date').value = oscaIdIssued;

        document.querySelector('.modal-overlay').classList.add('is-active');
        document.querySelector('.modal-overlay').classList.add('is-open');
    }
</script>

<script type="text/javascript">
    var residents = <?php echo json_encode($residents); ?>;
    var seniors = <?php echo json_encode($seniors); ?>;
    var newResidents = <?php echo json_encode($newResidents); ?>;
    var totalResidents = <?php echo json_encode($totalResidents); ?>;
    var totalSeniors = <?php echo json_encode($totalSeniors); ?>;
    var totalNewResidents = <?php echo json_encode($totalNewResidents); ?>;
    var recordsPerPage = <?php echo json_encode($recordsPerPage); ?>;
    var totalPages = <?php echo json_encode($totalPages); ?>;
    var totalSeniorPages = <?php echo json_encode($totalSeniorPages); ?>;
    var totalNewResidentPages = <?php echo json_encode($totalNewResidentPages); ?>;
    var page = <?php echo json_encode($page); ?>;
</script>
</html>