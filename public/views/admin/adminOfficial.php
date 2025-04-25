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

?>


<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Officials</title>
    <script src="https://kit.fontawesome.com/4907458c0c.js" crossorigin="anonymous"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Metrophobic&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../../css/admin/adminPortal.css">
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
            padding-top: 50px;
        }

        .modal-add {
            max-width: 750px;
            width: 100%;
        }

        /* Edit Form */
        .modal-overlay {
            padding-top: 50px;
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
                                <a href="/Barangay752ManagementSystem/controller/admin/adminSettingsController.php/controller/admin/adminSettingsController.php" class="nav-link">
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
                            <a href="../logoutController.php">Logout</a>
                        </div>
                    </div>
                </nav>
            </div>
            <div class="content" id="table-content">
            <h1 id="table-title" style="font-size: 30px; margin-bottom: 20px">Officials List</h1>
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <td colspan="19" class="search-column">
                                    <div class="search-bar">
                                        <p>Search</p>
                                        <input type="text" placeholder="Search Official" id="searchInput">
                                        <button class="modal-open-add button" onclick="" id="add-resident">Add Official +</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>Official ID</th>
                                <th>Last Name</th>
                                <th>First Name</th>
                                <th>Middle Name</th>
                                <th>Position</th>
                                <th>Term Start</th>
                                <th>Term End</th>
                                <th>Revoked at</th>
                                <th>Revoked by</th>
                                <th>Revoked reason</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($officials)) {
                                foreach ($officials as $official) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($official['official_id']) . "</td>";
                                    echo "<td>" . htmlspecialchars($official['last_name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($official['first_name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($official['middle_name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($official['position']) . "</td>";
                                    echo "<td>" . htmlspecialchars($official['term_start']) . "</td>";
                                    echo "<td>" . htmlspecialchars($official['term_end']) . "</td>";
                                    echo "<td>" . htmlspecialchars($official['revoked_at']) . "</td>";
                                    echo "<td>" . htmlspecialchars($official['revoked_by']) . "</td>";
                                    echo "<td>" . htmlspecialchars($official['revoke_reason']) . "</td>";
                                    echo "<td>" . htmlspecialchars($official['status']) . "</td>";
                                    echo "<td>" . '<button class="modal-open button" aria-haspopup="true" 
                                    data-official-id="' . $official['official_id'] . '" 
                                    data-position="' . $official['position'] . '"
                                    data-term-start="' . $official['term_start'] . '"
                                    data-term-end="' . $official['term_end'] . '"
                                    data-status="' . $official['status'] . '"
                                    onclick="populateModal(this)"> 
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pen" viewBox="0 0 16 16">
                                     <path d="m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001m-.644.766a.5.5 0 0 0-.707 0L1.95 11.756l-.764 3.057 3.057-.764L14.44 3.854a.5.5 0 0 0 0-.708z"/></path></svg></button>' . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='9'>No officials found.</td></tr>";
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
        </section>

        <!-- Modal Structure -->
        <div class="modal-overlay-add">
            <div class="modal-add" id="accessible-modal-add" role="dialog" aria-modal="true" aria-labelledby="modal-title-add" aria-describedby="modal-description-add">
            <div class="modal-header-add">
                <h2 id="modal-title-add">Add Official</h2>
                <button class="modal-close-add" aria-label="Close modal">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-content-add">
                <form id="addForm-add" action="adminOfficialController.php" method="POST" enctype="multipart/form-data">

                <div class="form-group-add">
                    <label for="add-residents-add">Resident</label>
                    <input type="text" id="resident-search" placeholder="Search Residents" autocomplete="off" class="form-control-add">
                    <select id="add-residents-add" name="resident_id" size="1" style="overflow-y: auto;" required>
                        <option value="" disabled selected>Select a resident</option>
                        <?php
                        if (!empty($residents)) {
                            foreach ($residents as $resident) {
                                echo "<option value='" . htmlspecialchars($resident['resident_id']) . "'>" 
                                . htmlspecialchars($resident['last_name']) . ", " 
                                . htmlspecialchars($resident['first_name']) . " " 
                                . htmlspecialchars($resident['middle_name']) 
                                . " (" . htmlspecialchars($resident['age']) . " years old, Account ID: " 
                                . htmlspecialchars($resident['account_id']) . ")</option>";
                            }
                        } else {
                            echo "<option>No residents found</option>";
                        }
                        ?>
                    </select>
                    <input type="hidden" name="account_id" id="account-id-hidden" value="">
                    <script>
                        // Update hidden input when a resident is selected
                        document.getElementById('add-residents-add').addEventListener('change', function() {
                            const selectedOption = this.options[this.selectedIndex];
                            const accountId = selectedOption.textContent.match(/Account ID: (\d+)/)?.[1] || '';
                            document.getElementById('account-id-hidden').value = accountId;
                        });
                    </script>

            </div>
                <!-- Privilege -->
                <div class="form-group">
                    <label for="edit-position">Position</label>
                    <select id="edit-position" name="position" onchange="toggleSpecifyInput(this)" required>
                        <option value="" selected disabled>Select a position</option>
                        <?php
                        foreach ($roles as $role) {
                            echo "<option value='" . htmlspecialchars($role['role_id']) . "' data-role-name='" . htmlspecialchars($role['role_name']) . "'>" . htmlspecialchars($role['role_name']) . "</option>";
                        }
                        ?>
                    </select>
                    <input type="hidden" id="role-name" name="role_name" value="">
                </div>
                <script>
                    document.getElementById('edit-position').addEventListener('change', function() {
                        const selectedOption = this.options[this.selectedIndex];
                        const roleName = selectedOption.getAttribute('data-role-name');
                        document.getElementById('role-name').value = roleName;
                    });
                </script>
                <div class="form-group" id="specify-position-group" style="display: none;">
                    <label for="specify-position">Specify Position</label>
                    <select id="specify-position" name="specify_position">
                        <option value="" disabled selected>Select Specific Position</option>
                        <option value="Punong Barangay">Punong Barangay</option>
                        <option value="Sangguniang Barangay Members">Sangguniang Barangay Members</option>
                        <option value="Secretary">Secretary</option>
                        <option value="Treasurer">Treasurer</option>
                        <option value="SK Chairperson">SK Chairperson</option>
                        <option value="SK Kagawads">SK Kagawad</option>
                        <option value="SK Secretary">SK Secretary</option>
                        <option value="SK Treasurer">SK Treasurer</option>
                    </select>
                </div>
                <script>
                    function toggleSpecifyInput(selectElement) {
                        const specifyGroup = document.getElementById('specify-position-group');
                        const specifyInput = document.getElementById('specify-position');
                        if (selectElement.value === "3") { // Show input if "Official" is selected
                            specifyGroup.style.display = "block";
                            specifyInput.setAttribute('required', 'required'); // Make input required
                        } else {
                            specifyGroup.style.display = "none";
                            specifyInput.removeAttribute('required'); // Remove required attribute
                        }
                    }
                </script>
                <div class="form-group-add">
                    <label for="term-start-add">Term Start</label>
                    <input type="date" id="term-start-add" name="term_start" required max="<?= date('Y-m-d'); ?>">
                </div>
                <div class="form-group-add">
                    <label for="term-end-add">Term End</label>
                    <input type="date" id="term-end-add" name="term_end" required min="<?= date('Y-m-d', strtotime('+6 months')); ?>">
                </div>
                <input type="hidden" name="status" value="active">
                <input type="submit" class="button" value="Add Official">
                </form>
            </div>
            </div>
        </div>

        <!-- Modal Structure Update -->
        <div class="modal-overlay">
            <div class="modal" id="accessible-modal-add" role="dialog" aria-modal="true" aria-labelledby="modal-title-add" aria-describedby="modal-description-add">
            <div class="modal-header">
            <h2 id="modal-title-add">Update Official</h2>
            <button class="modal-close" aria-label="Close modal">
            <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-content">
            <form id="addForm" action="adminOfficialUpdate.php" method="POST" enctype="multipart/form-data">

            <div class="form-group">
                <input type="hidden" name="account_id" value="<?php echo htmlspecialchars($_SESSION['account_id']); ?>">
                <input type="hidden" id="edit-official-id" name="official_id" value="">
                <label for="position">Position</label>
                <input type="text" id="edit-positions" name="position" placeholder="Position" required>
            </div>
            <div class="form-group">
                <label for="term-start">Term Start</label>
                <input type="date" id="edit-term-start" name="term_start" required max="<?= date('Y-m-d'); ?>">
            </div>
            <div class="form-group">
                <label for="term-end">Term End</label>
                <input type="date" id="edit-term-end" name="term_end" required min="<?= date('Y-m-d'); ?>">
            </div>
            <div class="form-group">
                <label for="status">Status</label>
                <input type="text" id="edit-status" name="status" readonly>
            </div>
            <input type="submit" class="button" value="Update Official">
            </form>
            </div>
            </div>
        </div>
    </main>
</body>
<script>




    $(document).ready(function() {
        // Search functionality with AJAX for officials
        $('#searchInput').on('keyup', function() {
            let query = $(this).val(); // Get the input value
            console.log("Search Query:", query); // Check the input value

            // Perform the search query, or fetch all data if query is empty
            $.ajax({
                url: 'adminOfficialSearch.php', // Ensure this is the correct path to your PHP file
                method: 'POST',
                cache: false,
                data: {
                    query: query
                }, // Send the search query to the server, it can be empty
                success: function(data) {
                    console.log("Response Data:", data); // Log the response data to ensure it's correct
                    $('tbody').html(data); // Update the table body with the data (either all or filtered results)

                    // Attach click event for the dynamically loaded buttons
                    $('.modal-open').on('click', function() {
                        let officialId = $(this).data('official-id');
                        console.log("Clicked Official ID: " + officialId); // Debugging: check if the click event is captured

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

        
$(document).ready(function() {
    // Detect changes in the search box
    $('#resident-search').on('keyup', function() {
        let searchQuery = $(this).val(); // Get the current value of the search input
        
        // Perform AJAX request to fetch residents based on the search query
        $.ajax({
            url: 'adminResidentNonOfficialSearchModal.php',  // PHP file to handle the search
            type: 'POST',
            data: { searchQuery: searchQuery },
            success: function(response) {
                // Populate the dropdown with the returned residents
                $('#add-residents-add').html(response); // Make sure this matches the correct select element ID
            },
            error: function() {
                console.log("Error fetching residents.");
            }
        });
    });
});

function populateModal(button) {
    // Get the official data from the button's data attributes
    var officialId = button.getAttribute('data-official-id');
    var position = button.getAttribute('data-position');
    var termStart = button.getAttribute('data-term-start');
    var termEnd = button.getAttribute('data-term-end');
    var status = button.getAttribute('data-status');

    // Populate the form inputs with the official data
    document.getElementById('edit-official-id').value = officialId;
    document.getElementById('edit-positions').value = position;
    document.getElementById('edit-term-start').value = termStart;
    document.getElementById('edit-term-end').value = termEnd;
    document.getElementById('edit-status').value = status;

    // Show the modal
    document.querySelector('.modal-overlay').classList.add('is-open');
}
</script>

</html>