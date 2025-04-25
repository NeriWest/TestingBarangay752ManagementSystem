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

$accountId = ($_SESSION['account_id']);
?>
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Announcements</title>
    <script src="https://kit.fontawesome.com/4907458c0c.js" crossorigin="anonymous"></script>
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
        /* FORM WIDTH DESIGN/TEMPORARY */
        .modal-add {
            max-width: 750px;
            width: 100%;
        }
        .modal {
            max-width: 750px;
            width: 100%;
        }

        /* Delete Button Styling */
        .delete-button {
            background-color: #dc3545; /* Red background for delete */
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 5px;
            transition: background 0.2s ease;
        }

        .delete-button:hover {
            background-color: #c82333; /* Darker red on hover */
        }

        .delete-button svg {
            vertical-align: middle;
        }

        /* Popup Styling */
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
                                    <span class="link-text">ACCOUNTS</span><span class="link-text"><i class="fa-solid fa-caret-down" id="accounts-dropdown-logo"></i></span>
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
                                    <span class="link-text">REPORT</span><span class="link-text"><i class="fa-solid fa-caret-down" id="report-dropdown-logo"></i></span>
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
                            <?php echo '<h3>' . htmlspecialchars($_SESSION['username']) . '</h3>' ?>
                            <a href="adminViewProfileController.php">View Profile</a>
                            <a href="../logoutController.php">Logout</a>
                        </div>
                    </div>
                </nav>
            </div>
            <div class="content" id="table-content">
                <h1 id="table-title" style="font-size: 30px; margin-bottom: 20px">Announcement List</h1>
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <td colspan="9" class="search-column">
                                    <div class="search-bar">
                                        <p>Search</p>
                                        <input type="text" placeholder="Search Announcements" id="searchInput">
                                        <button class="modal-open-add button" id="add-announcement">Add Announcement +</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>Announcement ID</th>
                                <th>Date Created</th>
                                <th>Subject</th>
                                <th>Recipient</th>
                                <th>Announcement Type</th>
                                <th>Message</th>
                                <th>Schedule</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="table-body">
                        <?php
                        // Assuming $announcements is populated elsewhere in your controller
                        if (!empty($announcements)) {
                            foreach ($announcements as $announcement) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($announcement['announcement_id']) . "</td>";
                                echo "<td>" . htmlspecialchars($announcement['date_created']) . "</td>";
                                echo "<td>" . (strlen($announcement['subject']) > 30 ? htmlspecialchars(substr($announcement['subject'], 0, 30)) . '...' : htmlspecialchars($announcement['subject'])) . "</td>";
                                echo "<td>" . htmlspecialchars($announcement['recipient_group']) . "</td>";
                                echo "<td>" . htmlspecialchars($announcement['announcement_type']) . "</td>";
                                echo "<td>" . (strlen($announcement['message_body']) > 30 ? htmlspecialchars(substr($announcement['message_body'], 0, 30)) . '...' : htmlspecialchars($announcement['message_body'])) . "</td>";
                                echo "<td>" . htmlspecialchars($announcement['schedule']) . "</td>";
                                echo "<td>" . htmlspecialchars($announcement['status']) . "</td>";
                                echo "<td>";
                                echo '<div style="display: flex; gap: 10px;">';
                                echo '<button class="modal-open button" aria-haspopup="true" 
                                    data-announcement-id="' . $announcement['announcement_id'] . '"
                                    data-subject="' . htmlspecialchars($announcement['subject']) . '"
                                    data-recipient="' . htmlspecialchars($announcement['recipient_group']) . '"
                                    data-announcement-type="' . htmlspecialchars($announcement['announcement_type']) . '"
                                    data-message-body="' . htmlspecialchars($announcement['message_body']) . '"
                                    data-schedule="' . htmlspecialchars($announcement['schedule']) . '"
                                    data-status="' . htmlspecialchars($announcement['status']) . '"
                                    onclick="populateModal(this)"> 
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pen" viewBox="0 0 16 16">
                                        <path d="m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001m-.644.766a.5.5 0 0 0-.707 0L1.95 11.756l-.764 3.057 3.057-.764L14.44 3.854a.5.5 0 0 0 0-.708z"/>
                                    </svg>
                                </button>';
                                echo '<button class="delete-button" 
                                    data-announcement-id="' . $announcement['announcement_id'] . '" 
                                    data-subject="' . htmlspecialchars($announcement['subject']) . '"
                                    data-recipient="' . htmlspecialchars($announcement['recipient_group']) . '"
                                    data-schedule="' . htmlspecialchars($announcement['schedule']) . '"
                                    onclick="showDeletePopup(' . $announcement['announcement_id'] . ', \'' . htmlspecialchars($announcement['subject']) . '\', \'' . htmlspecialchars($announcement['recipient_group']) . '\', \'' . htmlspecialchars($announcement['schedule']) . '\')">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                            <path d="M5.5 5.5A.5.5 0 0 1 6 5h4a.5.5 0 0 1 0 1H6a.5.5 0 0 1-.5-.5zm2.5 3a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0v-4a.5.5 0 0 1 .5-.5zm-2 0a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0v-4a.5.5 0 0 1 .5-.5z"/>
                                        <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3V2h11v1h-11z"/>
                                    </svg>
                                </button>';
                                echo '</div>';
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='9'>No announcements found.</td></tr>";
                        }
                        ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="9" id="paginationColspan">
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
            <!-- Modal Structure Add -->
            <div class="modal-overlay-add">
                <div class="modal-add" id="accessible-modal-add" role="dialog" aria-modal="true" aria-labelledby="modal-title-add" aria-describedby="modal-description-add">
                    <div class="modal-header-add">
                        <h2 id="modal-title-add">Add Announcement</h2>
                        <button class="modal-close-add" aria-label="Close modal">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-content-add">
                        <form id="addForm-add" action="adminAnnouncementController.php" method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <input type="text" id="account-id" name="account_id" value="<?= $accountId ?>" hidden>
                                <label for="recipients">Recipients</label>
                                <select id="recipients" name="recipients" size="0" style="overflow-y: auto;" required>
                                    <option value="" disabled selected>Select Recipients</option>
                                    <option value="Everyone">Everyone</option>
                                    <option value="Residents">Residents</option>
                                    <option value="Seniors">Seniors</option>
                                    <option value="Officials">Officials</option>
                                </select>
                            </div>
                            <div class="form-group-add">
                                <label for="subject-add">Subject</label>
                                <input type="text" id="subject-add" name="subject" placeholder="Subject" required>
                            </div>
                            <div class="form-group-add">
                                <label for="announcement-type-add">Announcement Type</label>
                                <select id="announcement-type-add" name="announcement_type" required>
                                    <option value="" disabled selected>Select Type</option>
                                    <option value="urgent">Urgent</option>
                                    <option value="event">Event</option>
                                    <option value="notice">Notice</option>
                                </select>
                            </div>
                            <div class="form-group-add">
                                <label for="message-body-add">Message Body</label>
                                <textarea id="message-body-add" name="message_body" placeholder="Message Body" required style="width: 100%; height: 100px; resize: none;"></textarea>
                            </div>
                            <div class="form-group-add">
                                <label for="schedule-add">Schedule</label>
                                <input type="datetime-local" id="schedule-add" name="schedule" required min="<?= date('Y-m-d\TH:i'); ?>">
                            </div>
                            <input type="submit" class="button" value="Add Announcement">
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal Structure Edit -->
            <div class="modal-overlay">
                <div class="modal" id="accessible-modal" role="dialog" aria-modal="true" aria-labelledby="modal-title" aria-describedby="modal-description">
                    <div class="modal-header">
                        <h2 id="modal-title">Edit Announcement</h2>
                        <button class="modal-close" aria-label="Close modal">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-content">
                        <form id="updateForm" action="adminAnnouncementUpdate.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" id="announcement-id" name="announcement_id">
                            <input type="hidden" id="account-id-edit" name="account_id" value="<?= $accountId ?>">
                            <div class="form-group">
                                <label for="edit-recipient">Recipients</label>
                                <select id="edit-recipient" name="recipients" size="0" style="overflow-y: auto;">
                                    <option value="" disabled selected>Select Recipients</option>
                                    <option value="Everyone">Everyone</option>
                                    <option value="Residents">Residents</option>
                                    <option value="Seniors">Seniors</option>
                                    <option value="Officials">Officials</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="edit-subject">Subject</label>
                                <input type="text" id="edit-subject" name="subject" placeholder="Subject" required>
                            </div>
                            <div class="form-group">
                                <label for="edit-announcement-type">Announcement Type</label>
                                <select id="edit-announcement-type" name="announcement_type" required>
                                    <option value="" disabled selected>Select Type</option>
                                    <option value="urgent">Urgent</option>
                                    <option value="event">Event</option>
                                    <option value="notice">Notice</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="edit-message-body">Message Body</label>
                                <textarea id="edit-message-body" name="message_body" placeholder="Message Body" required style="width: 99%; height: 100px; resize: none;"></textarea>
                            </div>
                            <input type="text" id="edit-status" name="status" hidden>
                            <div class="form-group" id="edit-schedule-group" style="display: none;">
                                <label for="edit-schedule">Schedule</label>
                                <input type="datetime-local" id="edit-schedule" name="schedule">
                            </div>
                            <input type="submit" class="button" value="Update Announcement">
                        </form>
                    </div>
                </div>
            </div>

            <!-- Delete Confirmation Popup -->
            <div id="deletePopup" class="popup-overlay">
                <div class="popup-content">
                    <h2>Confirm Announcement Deletion</h2>
                    <p>Are you sure you want to delete this announcement? This action cannot be undone.</p>
                    <form id="deleteForm" action="deleteAnnouncement.php" method="POST">
                        <input type="hidden" name="announcement_id" id="delete-announcement-id">
                        <input type="hidden" name="subject" id="delete-subject">
                        <input type="hidden" name="recipients" id="delete-recipients">
                        <input type="hidden" name="schedule" id="delete-schedule">
                        <div class="popup-buttons">
                            <button type="submit" class="confirm-btn">Confirm</button>
                            <button type="button" class="cancel-btn" onclick="closeDeletePopup()">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </main>
</body>

<script>
$(document).ready(function() {
    // Search functionality
    $('#searchInput').on('keyup', function() {
        let query = $(this).val(); // Get the input value
        $.ajax({
            url: 'adminAnnouncementSearch.php',
            method: 'POST',
            cache: false,
            data: { query: query },
            success: function(data) {
                $('tbody').html(data);
                bindPaginationLinks();
            },
            error: function(request, error) {
                console.log("Search Error: " + error);
            }
        });
    });

    // Pagination click event handler
    function bindPaginationLinks() {
        $(document).off('click', '.pagination-link').on('click', '.pagination-link', function(e) {
            e.preventDefault();
            var page = $(this).attr('href').split('page=')[1];
            var query = $('#searchInput').val();
            $.ajax({
                url: 'adminAnnouncementController.php',
                type: 'GET',
                data: { page: page, query: query },
                success: function(response) {
                    $('#table-body').html($(response).find('#table-body').html());
                    $('#paginationColspan').html($(response).find('#paginationColspan').html());
                    bindPaginationLinks();
                },
                error: function() {
                    alert('Failed to load announcements. Please try again.');
                }
            });
        });
    }

    // Bind pagination links initially
    bindPaginationLinks();

    // Event delegation for edit button
    $('tbody').on('click', '.modal-open', function() {
        populateModal(this);
        $('.modal-overlay').addClass('is-active');
    });

    // Event delegation for delete button
    $('tbody').on('click', '.delete-button', function() {
        var announcementId = $(this).data('announcement-id');
        var subject = $(this).data('subject');
        var recipient = $(this).data('recipient');
        var schedule = $(this).data('schedule');
        showDeletePopup(announcementId, subject, recipient, schedule);
    });

    // Close edit modal
    $('.modal-close').on('click', function() {
        $('.modal-overlay').removeClass('is-active');
    });
});

// Populate the edit modal with announcement data
function populateModal(button) {
    var announcementId = button.getAttribute('data-announcement-id');
    var subject = button.getAttribute('data-subject');
    var recipient = button.getAttribute('data-recipient');
    var announcementType = button.getAttribute('data-announcement-type');
    var messageBody = button.getAttribute('data-message-body');
    var schedule = button.getAttribute('data-schedule');
    var status = button.getAttribute('data-status');

    // Populate modal fields
    document.getElementById('announcement-id').value = announcementId;
    document.getElementById('edit-subject').value = subject;
    document.getElementById('edit-recipient').value = recipient;
    document.getElementById('edit-announcement-type').value = announcementType;
    document.getElementById('edit-message-body').value = messageBody;
    document.getElementById('edit-status').value = status;
    document.getElementById('edit-schedule').value = schedule; // Always populate schedule

    // Show or hide the schedule field based on status
    var scheduleGroup = document.getElementById('edit-schedule-group');
    if (status.toLowerCase() === 'sent') {
        scheduleGroup.style.display = 'none';
    } else {
        scheduleGroup.style.display = 'block';
    }

    document.querySelector('.modal-overlay').classList.add('is-open');
}

// Delete popup functions
function showDeletePopup(announcementId, subject, recipients, schedule) {
    document.getElementById('delete-announcement-id').value = announcementId;
    document.getElementById('delete-subject').value = subject;
    document.getElementById('delete-recipients').value = recipients;
    document.getElementById('delete-schedule').value = schedule;
    const popup = document.getElementById('deletePopup');
    popup.classList.add('active');
}

function closeDeletePopup() {
    const popup = document.getElementById('deletePopup');
    popup.classList.remove('active');
    document.getElementById('deleteForm').reset();
}

// Close delete popup when clicking outside
document.getElementById('deletePopup').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeletePopup();
    }
});
</script>
</html>