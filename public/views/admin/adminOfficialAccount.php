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
    <title>Official Accounts</title>
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
                                    <span class="link-text">ACCOUNTS</span>
                                    <span class="link-text"><i class="fa-solid fa-caret-down" id="accounts-dropdown-logo"></i></span>
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
                                    <span class="link-text">REQUEST</span>
                                    <span class="link-text"><i class="fa-solid fa-caret-down" id="request-dropdown-logo"></i></span>
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
                                    <span class="link-text">REPORT</span>
                                    <span class="link-text"><i class="fa-solid fa-caret-down" id="report-dropdown-logo"></i></span>
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
                            <?php echo '<h3>' . htmlspecialchars($_SESSION['username']) . '</h3>'; ?>
                            <a href="adminViewProfileController.php">View Profile</a>
                            <a href="../logoutController.php">Log Out</a>
                        </div>
                    </div>
                </nav>
            </div>
            <div class="content" id="table-content">
                <h1 id="table-title" style="font-size: 30px; margin-bottom: 20px">Official Accounts List</h1>
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <td colspan="11" class="search-column">
                                    <div class="search-bar">
                                        <p>Search</p>
                                        <input type="text" placeholder="Search Official" id="searchInput">
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
                                <th>Privilege</th>
                                <th>Status</th>
                                <th>ID Image</th>
                                <th>Profile Image</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($accounts)) {
                                foreach ($accounts as $account) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($account['account_id']) . "</td>";
                                    echo "<td>" . htmlspecialchars($account['username']) . "</td>";

                                    // Full Name (Concatenated)
                                    $fullName = htmlspecialchars($account['first_name'] . " " . ($account['middle_name'] ? $account['middle_name'] . " " : "") . $account['last_name']);
                                    echo "<td>" . $fullName . "</td>";
                                    echo "<td>" . htmlspecialchars($account['cellphone_number'] ?? 'N/A') . "</td>";
                                    echo "<td>" . htmlspecialchars($account['date_of_birth'] ?? 'N/A') . "</td>";
                                    echo "<td>" . (!empty($account['email']) ? htmlspecialchars($account['email']) : 'N/A') . "</td>";
                                    echo "<td>" . htmlspecialchars($account['role_id'] ?? 'N/A') . " - " . htmlspecialchars($account['role_name'] ?? 'N/A') . "</td>";

                                    $statusColor = ($account['status'] === 'active') ? 'green' : (($account['status'] === 'pending') ? 'orange' : (($account['status'] === 'inactive') ? 'gray' : (($account['status'] === 'disapproved') ? 'red' : 'black')));
                                    echo "<td><span style='background-color: $statusColor; color: white; padding: 5px 10px; border-radius: 5px; font-weight: bold; box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3); text-shadow: -1px -1px 0 black, 1px -1px 0 black, -1px 1px 0 black, 1px 1px 0 black;'>" . htmlspecialchars($account['status']) . "</span></td>";

                                    // ID Image
                                    echo "<td>";
                                    if (!empty($account['id_image_name'])) {
                                        // Create a link for the ID image, display the image when clicked
                                        echo "<a href='../../img/id_images/" . htmlspecialchars($account['id_image_name'], ENT_QUOTES) . "' target='_blank'>";
                                        // Display the ID image thumbnail
                                        echo "<img src='../../img/id_images/" . htmlspecialchars($account['id_image_name'], ENT_QUOTES) . "' alt='ID Image' width='50' height='50' style='margin-right:5px;'>";
                                        echo "</a>";
                                    } else {
                                        // If no ID image is present, display this message
                                        echo "No image";
                                    }
                                    echo "</td>";
                        
                        
                                    // Profile Image (Click to open the full image in a new tab)
                                    echo "<td>";
                                    if (!empty($account['profile_image_name'])) {
                                        // Create a link for the profile image, display the image when clicked
                                        echo "<a href='../../img/profile_images/" . htmlspecialchars($account['profile_image_name'], ENT_QUOTES) . "' target='_blank'>";
                                        // Display the profile image thumbnail
                                        echo "<img src='../../img/profile_images/" . htmlspecialchars($account['profile_image_name'], ENT_QUOTES) . "' alt='Profile Image' width='50' height='50' style='margin-right:5px;'>";
                                        echo "</a>";
                                    } else {
                                        // If no profile image is present, display "No image"
                                        echo "No image";
                                    }
                                    echo "</td>";

                                    // Actions
                                    echo "<td>";
                                    echo '<div style="display: flex; gap: 5px;">';
                                    if ($account['status'] === 'active') {
                                        echo '
                                        <button class="revoke-btn buttons" 
                                                data-official-account-id="' . htmlspecialchars($account['account_id'], ENT_QUOTES) . '"
                                                style="background-color:rgb(255, 7, 7); color: white; border: none; padding: 8px; border-radius: 5px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-weight: bold; text-shadow: -1px -1px 0 black, 1px -1px 0 black, -1px 1px 0 black, 1px 1px 0 black;"
                                                onclick="showRevokePopup(\'' . htmlspecialchars($account['account_id'], ENT_QUOTES) . '\')">
                                            Revoke
                                        </button>';
                                    }
                                    echo '
                                    <button class="modal-open button" 
                                            aria-haspopup="true"
                                            data-official-account-id="' . htmlspecialchars($account['account_id'], ENT_QUOTES) . '"
                                            data-official-resident-id="' . htmlspecialchars($account['resident_id'], ENT_QUOTES) . '"
                                            data-official-username="' . htmlspecialchars($account['username'], ENT_QUOTES) . '"
                                            data-official-email="' . htmlspecialchars($account['email'], ENT_QUOTES) . '"
                                            data-official-mobile-no="' . htmlspecialchars($account['cellphone_number'] ?? '', ENT_QUOTES) . '"
                                            data-official-status="' . htmlspecialchars($account['status'], ENT_QUOTES) . '"
                                            data-official-profile-image="' . (!empty($account['profile_image_name']) ? '../../img/profile_images/' . htmlspecialchars($account['profile_image_name'], ENT_QUOTES) : 'default-profile.png') . '"
                                            data-official-id-image="' . (!empty($account['id_image_name']) ? '../../img/id_images/' . htmlspecialchars($account['id_image_name'], ENT_QUOTES) : 'default-id.png') . '"
                                            onclick="populateOfficialModal(this)">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-pen" viewBox="0 0 16 16">
                                            <path d="m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001m-.644.766a.5.5 0 0 0-.707 0L1.95 11.756l-.764 3.057 3.057-.764L14.44 3.854a.5.5 0 0 0 0-.708z"/>
                                        </svg>
                                    </button>';
                                    echo '</div>';
                                    echo '</td>';
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='11'>No accounts found.</td></tr>";
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="11" id="paginationColspan">
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

            <div id="revokePopup" class="popup-overlay">
                <div class="popup-content">
                    <h2>Confirm Account Revocation</h2>
                    <p>Are you sure you want to revoke this account? This action cannot be undone.</p>
                    <form id="revokeForm" action="revokeAccountController.php" method="POST">
                        <input type="hidden" name="account_id" id="popup-account-id">
                        <textarea name="revoke_reason" id="revoke_reason" placeholder="Enter reason for revocation" required style="width: 100%; height: 100px; resize: none;"></textarea>
                        <div class="popup-buttons">
                            <button type="submit" class="confirm-btn">Confirm</button>
                            <button type="button" class="cancel-btn" onclick="closeRevokePopup()">Cancel</button>
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

                    .confirm-btn, .cancel-btn {
                        padding: 8px 16px;
                        font-size: 0.875rem;
                    }
                }
            </style>

            <script>
                function showRevokePopup(accountId) {
                    document.getElementById('popup-account-id').value = accountId;
                    const popup = document.getElementById('revokePopup');
                    popup.classList.add('active');
                }

                function closeRevokePopup() {
                    const popup = document.getElementById('revokePopup');
                    popup.classList.remove('active');
                    document.getElementById('revokeForm').reset();
                }

                document.getElementById('revokePopup').addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeRevokePopup();
                    }
                });
            </script>

            <!-- Modal Structure -->
            <div class="modal-overlay">
                <div class="modal" id="accessible-modal" role="dialog" aria-modal="true" aria-labelledby="modal-title" aria-describedby="modal-description">
                    <div class="modal-header">
                        <h2 id="modal-title">Update Official Account</h2>
                        <button class="modal-close" aria-label="Close modal">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-content">
                        <form id="updateForm" action="adminOfficialAccountUpdate.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" id="official-account-id" name="account_id">

                            <!-- Resident ID -->
                            <div class="form-group">
                                <label for="edit-official-resident-id">Resident ID</label>
                                <input type="text" id="edit-official-resident-id" name="resident_id" readonly>
                            </div>

                            <!-- Username -->
                            <div class="form-group">
                                <label for="edit-official-username">Username</label>
                                <input type="text" id="edit-official-username" name="username" readonly>
                            </div>

                            <!-- Email -->
                            <div class="form-group">
                                <label for="edit-official-email">Email</label>
                                <input type="email" id="edit-official-email" name="email" readonly>
                            </div>

                            <!-- Mobile Number -->
                            <div class="form-group" id="official-phone-group">
                                <label for="edit-official-phone">Mobile No.</label>
                                <input type="tel" id="edit-official-phone" name="cellphone_number" maxlength="11" readonly>
                            </div>

                      
                            <!-- Account Status -->
                            <div class="form-group">
                                <label for="edit-official-status">Status</label>
                                <select id="edit-official-status" name="status">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>

                            <input type="submit" class="button" value="Update Official Account">
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script>
        // AJAX FOR SEARCH
        $(document).ready(function() {
            // Search functionality with AJAX
            $('#searchInput').on('keyup', function() {
                let query = $(this).val(); // Get the input value
                console.log("Search Query:", query); // Check the input value

                // Perform the search query, or fetch all data if query is empty
                $.ajax({
                    url: 'adminOfficialAccountSearch.php', // Ensure this is the correct path to your PHP file
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
                            let residentId = $(this).data('resident-id');
                            console.log("Clicked Resident ID: " + residentId); // Debugging: check if the click event is captured

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

        // Populate Modal for Official Accounts
        function populateOfficialModal(button) {
            const accountId = button.getAttribute("data-official-account-id") || "";
            const residentId = button.getAttribute("data-official-resident-id") || "";
            const username = button.getAttribute("data-official-username") || "";
            const email = button.getAttribute("data-official-email") || "";
            const mobileNo = button.getAttribute("data-official-mobile-no") || "";
            const status = button.getAttribute("data-official-status") || "";
            const profileImage = button.getAttribute("data-official-profile-image") || "default-profile.png";
            const idImage = button.getAttribute("data-official-id-image") || "default-id.png";

            console.log("Status from button:", status); // Debug status value

            // Populate form fields
            document.getElementById("official-account-id").value = accountId;
            document.getElementById("edit-official-resident-id").value = residentId || "N/A";
            document.getElementById("edit-official-username").value = username;
            document.getElementById("edit-official-email").value = email;
            document.getElementById("edit-official-phone").value = mobileNo;
            document.getElementById("edit-official-status").value = status;

            console.log("Selected status in select:", document.getElementById("edit-official-status").value); // Debug final value

            // Handle phone group visibility
            const phoneGroup = document.getElementById("official-phone-group");
            if (mobileNo.trim() !== "") {
                phoneGroup.style.display = "block";
            } else {
                phoneGroup.style.display = "none";
            }

            // Save to localStorage
            const officialModalData = {
                accountId,
                residentId,
                username,
                email,
                mobileNo,
                status,
                profileImage,
                idImage
            };
            localStorage.setItem("officialModalData", JSON.stringify(officialModalData));

            // Show modal
            document.querySelector(".modal-overlay").classList.add("is-active");
        }

        // Load Modal Data from localStorage
        function loadOfficialModalData() {
            const savedData = JSON.parse(localStorage.getItem("officialModalData"));
            if (savedData) {
                document.getElementById("official-account-id").value = savedData.accountId || "";
                document.getElementById("edit-official-resident-id").value = savedData.residentId || "N/A";
                document.getElementById("edit-official-username").value = savedData.username || "";
                document.getElementById("edit-official-email").value = savedData.email || "";
                document.getElementById("edit-official-phone").value = savedData.mobileNo || "";
                document.getElementById("edit-official-status").value = savedData.status || "";
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            loadOfficialModalData();
        });

        // Resident search for add modal (if applicable)
        $(document).ready(function() {
            $('#resident-search-main').on('keyup', function() {
                let searchQuery = $(this).val();
                $.ajax({
                    url: 'adminOfficialResidentNoAccountSearchModal.php',
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
    </script>
</body>
</html>