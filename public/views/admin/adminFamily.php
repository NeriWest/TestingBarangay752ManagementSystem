
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
    <title>Family</title>
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
                            <a href="../logoutController.php">Logout</a>
                        </div>
                    </div>
                </nav>
            </div>  
            <div class="content" id="table-content">
            <h1 id="table-title" style="font-size: 30px; margin-bottom: 20px">Family List</h1>
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <td colspan="19" class="search-column">
                                    <div class="search-bar">
                                        <p>Search</p>
                                        <input type="text" placeholder="Search Family" id="searchInput">
                                        <button class="modal-open-add button" onclick="" id="add-resident">Add Family +</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
        <th>Family ID</th>
        <th>Family Name</th>
                                        <!-- <th>Representative</th> -->
        <th>Address</th>
        <th>Family Members</th>
        <th>Total Income</th>
        <th>Created At</th>
        <th>Action</th>
    </tr>
                        </thead>
                        <tbody>
    <?php
    if (!empty($families)) {
        foreach ($families as $family) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($family['family_id']) . "</td>";
            echo "<td>" . htmlspecialchars($family['family_name']) . "</td>";
            echo "<td>" . htmlspecialchars(getAddress($family['house_number'], $family['street'])) . "</td>";

            // Display family members
            if (!empty($family['residents'])) {
                echo "<td>" . html_entity_decode($family['residents']) . "</td>";
            } else {
                echo "<td>No members</td>";
            }

            // Display total income (already calculated in the model)
            echo "<td>₱" . htmlspecialchars(number_format($family['total_income'], 2)) . "</td>";

            echo "<td>" . htmlspecialchars($family['created_at']) . "</td>";
            echo "<td>
                    <button class='modal-open button' 
                            data-family-id='" . htmlspecialchars($family['family_id']) . "' 
                            data-family-name='" . htmlspecialchars($family['family_name']) . "' 
                            data-house-number='" . htmlspecialchars($family['house_number']) . "' 
                            data-street='" . htmlspecialchars($family['street']) . "' 
                            data-residents='" . htmlspecialchars($family['residents']) . "' 
                            data-total-income='" . htmlspecialchars($family['total_income']) . "' 
                            onclick='populateModal(this)'>
                        <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-pen' viewBox='0 0 16 16'>
                            <path d='m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001m-.644.766a.5.5 0 0 0-.707 0L1.95 11.756l-.764 3.057 3.057-.764L14.44 3.854a.5.5 0 0 0 0-.708z'/>
                        </svg>
                    </button>
                </td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='7'>No families found.</td></tr>";
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

         <!-- Add Family Modal -->
         <div class="modal-overlay-add">
            <div class="modal-add">
                <div class="modal-header-add">
                    <h2>Add Family</h2>
                    <button class="modal-close-add">&times;</button>
                </div>
                <div class="modal-content-add">
                    <form action="adminFamilyController.php" method="POST">
                        <input type="hidden" name="account_id" value="<?php echo htmlspecialchars($_SESSION['account_id']); ?>">
                        <div class="form-group-add">
                            <label for="family-name-add">Family Name</label>
                            <input type="text" id="family-name-add" name="family_name" required>
                        </div>
                        <div class="form-group-add">
                            <label for="house-number-add">House Number</label>
                            <input type="text" id="house-number-add" name="house_number">
                        </div>
                        <div class="form-group-add">
                            <label for="street-add">Street</label>
                            <input type="text" id="street-add" name="street">
                        </div>
                        <div class="form-group-add">
                            <label for="resident-search">Select Residents</label>
                            <input type="text" id="resident-search" placeholder="Search Residents">
                            <select id="residents-add" multiple size="10">
                                <?php
                                    echo "<option value='' disabled selected>Select a resident with no family</option>";
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
                        <div class="form-group-add" id="selected-residents-container" style="display: none;">
                            <label for="selected-residents-container">Selected Residents</label>

                            <!-- Selected residents will be displayed here -->
                        </div>
                        <script>
                            const residentsDropdown = document.getElementById('residents-add');
                            const selectedContainer = document.getElementById('selected-residents-container');
                            const searchInput = document.getElementById('resident-search');

                            function addSelectedResident(option) {
                                if (!selectedContainer.querySelector(`[data-id="${option.value}"]`)) {
                                    const div = document.createElement('div');
                                    div.textContent = option.text;
                                    div.className = 'selected-resident';
                                    div.dataset.id = option.value;

                                    const input = document.createElement('input');
                                    input.type = 'hidden';
                                    input.name = 'residents[]';
                                    input.value = option.value;

                                    const removeButton = document.createElement('button');
                                    removeButton.textContent = 'Remove';
                                    removeButton.type = 'button';
                                    removeButton.className = 'remove-resident';
                                    removeButton.addEventListener('click', function() {
                                        div.remove();
                                        const newOption = document.createElement('option');
                                        newOption.value = option.value;
                                        newOption.textContent = option.textContent;
                                        residentsDropdown.appendChild(newOption);

                                        // Hide the label if no residents are selected
                                        if (!selectedContainer.querySelector('.selected-resident')) {
                                            selectedContainer.style.display = 'none';
                                        }
                                    });

                                    div.appendChild(input);
                                    div.appendChild(removeButton);
                                    selectedContainer.appendChild(div);

                                    // Remove the option from the dropdown
                                    option.remove();

                                    // Show the label when a resident is selected
                                    selectedContainer.style.display = 'block';
                                }
                            }

                            residentsDropdown.addEventListener('change', function() {
                                const selectedOptions = Array.from(residentsDropdown.selectedOptions);
                                selectedOptions.forEach(option => addSelectedResident(option));
                            });

                            searchInput.addEventListener('keyup', function() {
                                const query = searchInput.value.toLowerCase();
                                const options = Array.from(residentsDropdown.options);

                                options.forEach(option => {
                                    if (option.value && option.textContent.toLowerCase().includes(query)) {
                                        option.style.display = '';
                                    } else if (option.value === '') {
                                        option.style.display = ''; // Always show the default option
                                    } else {
                                        option.style.display = 'none';
                                    }
                                });
                            });
                        </script>
                        <input type="submit" class="button" value="Add Family">
                    </form>
                </div>
            </div>
        </div>

        <!-- Update Family Modal -->
        <div class="modal-overlay">
            <div class="modal">
            <div class="modal-header">
                <h2>Update Family</h2>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-content">
                <form action="adminFamilyUpdate.php" method="POST">
                <input type="hidden" name="account_id" value="<?php echo htmlspecialchars($_SESSION['account_id']); ?>">

                <!-- Hidden Input for Family ID -->
                <div class="form-group">
                    <label for="edit-family-id-display">Existing Family ID</label>
                    <input type="text" id="edit-family-id" name="family_id" readonly>
                </div>

                <div class="form-group">
                    <label for="edit-family-name">Family Name</label>
                    <input type="text" id="edit-family-name" name="family_name" required>
                </div>
                <div class="form-group">
                    <label for="edit-house-number">House Number</label>
                    <input type="text" id="edit-house-number" name="house_number">
                </div>
                <div class="form-group">
                    <label for="edit-street">Street</label>
                    <input type="text" id="edit-street" name="street">
                </div>

                <!-- Residents Selection -->
                <div class="form-group">
                    <label for="resident-search-update">Select Residents</label>
                    <input type="text" id="resident-search-update" placeholder="Search Residents">
                    <select id="residents-update" multiple size="10">
                    <?php
                    echo "<option value='' disabled selected>Select a resident with no family</option>";
                    if (!empty($residents)) {
                        foreach ($residents as $resident) {
                        $familyInfo = !empty($resident['family_id']) ? "(Family ID: " . htmlspecialchars($resident['family_id']) . ")" : "(No Family)";
                        echo "<option value='" . htmlspecialchars($resident['resident_id']) . "'>" 
                            . htmlspecialchars($resident['last_name']) . ", " 
                            . htmlspecialchars($resident['first_name']) . " " 
                            . htmlspecialchars($resident['middle_name']) 
                            . " (" . htmlspecialchars($resident['age']) . " years old) " . $familyInfo . "</option>";
                        }
                    } else {
                        echo "<option>No residents found</option>";
                    }
                    ?>
                    </select>
                </div>

                <!-- Existing Residents in the Same Family -->
                 <div class="form-group">
                    <label>Existing Residents in This Family</label>
                    <div id="existing-residents-list">
                    <!-- Existing residents will be populated dynamically -->
                    </div>
                </div>

                

                <div class="form-group-add" id="selected-residents-container-update" style="display: none;">
                    <label for="selected-residents-container-update">Selected Residents</label>
                </div>
                <div class="form-group-add" id="removed-residents-container-update" style="display: none;">
                    <label for="removed-residents-container-update">Removed Residents</label>
                    <!-- Removed residents will be displayed here -->
                </div>
                <script>
                    const residentsDropdownUpdate = document.getElementById('residents-update');
                    const selectedContainerUpdate = document.getElementById('selected-residents-container-update');
                    const removedContainerUpdate = document.getElementById('removed-residents-container-update');
                    const searchInputUpdate = document.getElementById('resident-search-update');
                    const existingResidentsList = document.getElementById('existing-residents-list');

                    function addSelectedResidentUpdate(option) {
                        if (!selectedContainerUpdate.querySelector(`[data-id="${option.value}"]`)) {
                            const div = document.createElement('div');
                            div.textContent = option.text;
                            div.className = 'selected-resident';
                            div.dataset.id = option.value;

                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'residents[]';
                            input.value = option.value;

                            const removeButton = document.createElement('button');
                            removeButton.textContent = 'Remove';
                            removeButton.type = 'button';
                            removeButton.className = 'remove-resident';
                            removeButton.addEventListener('click', function () {
                                div.remove();
                                const newOption = document.createElement('option');
                                newOption.value = option.value;
                                newOption.textContent = option.textContent;
                                residentsDropdownUpdate.appendChild(newOption);

                                // Hide the label if no residents are selected
                                if (!selectedContainerUpdate.querySelector('.selected-resident')) {
                                    selectedContainerUpdate.style.display = 'none';
                                }
                            });

                            div.appendChild(input);
                            div.appendChild(removeButton);
                            selectedContainerUpdate.appendChild(div);

                            // Remove the option from the dropdown
                            option.remove();

                            // Show the label when a resident is selected
                            selectedContainerUpdate.style.display = 'block';
                        }
                    }

                    residentsDropdownUpdate.addEventListener('change', function () {
                        const selectedOptions = Array.from(residentsDropdownUpdate.selectedOptions);
                        selectedOptions.forEach(option => addSelectedResidentUpdate(option));
                    });

                    searchInputUpdate.addEventListener('keyup', function () {
                        const query = searchInputUpdate.value.toLowerCase();
                        const options = Array.from(residentsDropdownUpdate.options);

                        options.forEach(option => {
                            if (option.value && option.textContent.toLowerCase().includes(query)) {
                                option.style.display = '';
                            } else if (option.value === '') {
                                option.style.display = ''; // Always show the default option
                            } else {
                                option.style.display = 'none';
                            }
                        });
                    });

                    // Populate existing residents when the modal is opened
                    function populateModal(button) {
                        document.getElementById('edit-family-id').value = button.getAttribute('data-family-id');
                        document.getElementById('edit-family-name').value = button.getAttribute('data-family-name');
                        document.getElementById('edit-house-number').value = button.getAttribute('data-house-number');
                        document.getElementById('edit-street').value = button.getAttribute('data-street');

                        // Populate the existing residents list
                        const existingResidents = button.getAttribute('data-residents');
                        existingResidentsList.innerHTML = existingResidents
                            ? existingResidents.split('<br>').map(resident => {
                                const residentId = resident.split(' - ')[0].trim();
                                const residentName = resident.split(' - ')[1].trim();
                                return `
                                    <div class="existing-resident" data-id="${residentId}">
                                        <span>${residentName}</span>
                                        <button type="button" class="remove-resident" onclick="removeResident(this, '${residentId}', '${residentName}')">Remove</button>
                                        <input type="hidden" name="existing_residents[]" value="${residentId}">
                                    </div>
                                `;
                            }).join('')
                            : '<div>No residents found</div>';

                        document.querySelector('.modal-overlay').classList.add('is-open');
                    }

                    function removeResident(button, residentId, residentName) {
                        const residentDiv = button.parentElement;
                        residentDiv.remove();

                        // Add the removed resident to the removed container
                        if (!removedContainerUpdate.querySelector(`[data-id="${residentId}"]`)) {
                            const removedDiv = document.createElement('div');
                            removedDiv.textContent = residentName;
                            removedDiv.className = 'removed-resident';
                            removedDiv.dataset.id = residentId;

                            const removedInput = document.createElement('input');
                            removedInput.type = 'hidden';
                            removedInput.name = 'removed_residents[]';
                            removedInput.value = residentId;

                            removedDiv.appendChild(removedInput);
                            removedContainerUpdate.appendChild(removedDiv);

                            // Show the removed residents container
                            removedContainerUpdate.style.display = 'block';
                        }

                        // If no residents are left, show a message
                        if (!existingResidentsList.querySelector('.existing-resident')) {
                            existingResidentsList.innerHTML = '<div>No residents found</div>';
                        }
                    }
                </script>
                
                </script>
                <input type="submit" class="button" value="Update Family">
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
                url: 'adminFamilySearch.php', // Ensure this is the correct path to your PHP file
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

</script>

</html>