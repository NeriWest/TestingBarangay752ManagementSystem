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

$accountId = $_SESSION['account_id'];

?>
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Permit</title>
    <script src="https://kit.fontawesome.com/4907458c0c.js" crossorigin="anonymous"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Metrophobic&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../css/main.css">
    <link rel="stylesheet" href="../../css/admin/table.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="../../js/main.js" defer></script>
    <script src="../../js/dashBoard.js" defer></script>
    <script src="../../js/add-modal.js" defer></script>
    <script src="../../js/update-modal.js" defer></script>
    <style>
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
            padding-top: 300px;
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
            <div class="content" id="table-content">
            <h1 id="table-title" style="font-size: 30px; margin-bottom: 20px">Permit List</h1>

            <div class="table-container">
                <table class="table">
                <thead>
                    <tr>
                    <td colspan="14" class="search-column">
                        <div class="search-bar">
                        <p>Search</p>
                        <input type="text" placeholder="Search Requests" id="searchInput">
                        <button class="modal-open-add button" onclick="" id="add-permit">Issue Permit +</button>

                        <?php
                        $approvedCount = 0;
                        $disapprovedCount = 0;
                        $pendingCount = 0;

                        if (!empty($requests)) {
                            foreach ($requests as $request) {
                                switch ($request['status']) {
                                    case 'approved':
                                        $approvedCount++;
                                        break;
                                    case 'disapproved':
                                    case 'rejected':
                                        $disapprovedCount++;
                                        break;
                                    case 'pending':
                                        $pendingCount++;
                                        break;
                                }
                            }
                        }
                        ?>

                        <div class="status-summary" style="display: flex; gap: 20px; align-items: center; margin-left: 50px;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <span style="
                                    background-color: green; 
                                    color: white; 
                                    padding: 5px 10px; 
                                    border-radius: 5px; 
                                    font-weight: bold;
                                    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);">
                                    Approved
                                </span>
                                <p style="font-size: 18px; font-weight: bold;"><?php echo $approvedCount; ?></p>
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
                    <th>Request ID</th>
                    <th>Type of Document</th>
                    <th>Name of Requestor</th>
                    <th>Purpose</th>
                    <th>Date Submitted</th>
                    <th>Last Updated</th>
                    <th>Type of Payment</th>
                    <th>Price</th>
                    <th>Payment Amount</th>
                    <th>Proof of Payment</th>
                    <th>No. Of Copies</th>
                    <th>Remarks</th>
                    <th>Status</th>
                    <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php

                if (!empty($requests)) {
                    foreach ($requests as $request) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($request['request_id']) . "</td>";
                        echo "<td>" . htmlspecialchars($request['document_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($request['name_requested']) . "</td>";
                        echo "<td>" . htmlspecialchars($request['purpose']) . "</td>";
                        echo "<td>" . htmlspecialchars($request['date_submitted']) . "</td>";
                        echo "<td>" . htmlspecialchars($request['last_updated']) . "</td>";
                        echo "<td>" . (isset($request['payment_type']) && !empty($request['payment_type']) ? htmlspecialchars($request['payment_type']) : 'N/A') . "</td>";
                        echo "<td>" . htmlspecialchars($request['template_price']) . "</td>";
                        echo "<td>" . htmlspecialchars($request['payment_amount']) . "</td>";
                        echo "<td>";
                        if (!empty($request['proof_of_payment'])) {
                            $proofOfPaymentPath = '../../img/proof_of_payment/' . htmlspecialchars($request['proof_of_payment']);
                            echo "<a href='" . $proofOfPaymentPath . "' target='_blank'>";
                            echo "<img src='" . $proofOfPaymentPath . "' alt='Proof of Payment' width='50' height='50' style='margin-right:5px;'>";
                            echo "</a>";
                        } else {
                            echo "No proof of payment";
                        }
                        echo "</td>";
                        echo "<td>" . htmlspecialchars($request['no_of_copies']) . "</td>";
                        echo "<td>" . (!empty($request['remarks']) ? (strlen($request['remarks']) > 16 ? htmlspecialchars(substr($request['remarks'], 0, 16)) . "..." : htmlspecialchars($request['remarks'])) : "N/A") . "</td>";
                        $statusColor = ($request['status'] === 'approved') ? 'green' : 
                        (($request['status'] === 'pending') ? 'orange' : 
                        (($request['status'] === 'rejected' || $request['status'] === 'disapproved') ? 'red' : 
                        (($request['status'] === 'issued') ? 'gray' : 'gray')));
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
                        '>" . htmlspecialchars($request['status']) . "</span></td>";

                        echo "<td>"; 
                        echo '<div style="display: flex; gap: 10px;">';
                        if ($request['status'] === 'pending') {
                            echo '
                                <button class="approve-button buttons" 
                                    style="background-color: #008000; color: white; border: none; padding: 8px 12px; border-radius: 5px; cursor: pointer; font-weight: bold; text-shadow: -1px -1px 0 black, 1px -1px 0 black, -1px 1px 0 black, 1px 1px 0 black;"
                                    onclick="showApprovePopup(\'' . htmlspecialchars($request['request_id'], ENT_QUOTES, 'UTF-8') . '\')">
                                    Approve
                                </button>
                                <button class="disapprove-button buttons" 
                                    style="background-color: #ff0707; color: white; border: none; padding: 8px 12px; border-radius: 5px; cursor: pointer; font-weight: bold; text-shadow: -1px -1px 0 black, 1px -1px 0 black, -1px 1px 0 black, 1px 1px 0 black;"
                                    onclick="showDisapprovePopup(\'' . htmlspecialchars($request['request_id'], ENT_QUOTES, 'UTF-8') . '\')">
                                    Disapprove
                                </button>';
                        } elseif ($request['status'] === 'approved') {
                            echo '
                                <button class="issue-button buttons" 
                                    style="background-color: #008000; color: white; border: none; padding: 8px 12px; border-radius: 5px; cursor: pointer; font-weight: bold; text-shadow: -1px -1px 0 black, 1px -1px 0 black, -1px 1px 0 black, 1px 1px 0 black;"
                                    onclick="showIssuePopup(\'' . htmlspecialchars($request['request_id'], ENT_QUOTES, 'UTF-8') . '\')">
                                    Issue
                                </button>';
                        }
                        
                        echo '<button class="modal-open button" style="background-color:rgb(92, 92, 92)"; aria-haspopup="true" 
                        data-request-id="' . $request['request_id'] . '" 
                        data-document-type="' . htmlspecialchars($request['document_name']) . '"
                        data-name-requested="' . htmlspecialchars($request['name_requested']) . '"
                        data-purpose="' . htmlspecialchars($request['purpose']) . '"
                        data-date-submitted="' . htmlspecialchars($request['date_submitted']) . '"
                        data-payment-type-id="' . (!empty($request['payment_type']) ? htmlspecialchars($request['payment_type']) : 'N/A') . '" 
                        data-proof-of-payment="' . htmlspecialchars($request['proof_of_payment']) . '" 
                        data-payment-amount="' . htmlspecialchars($request['payment_amount']) . '"
                        data-no-of-copies="' . htmlspecialchars($request['no_of_copies']) . '"
                        data-status="' . htmlspecialchars($request['status']) . '"
                        data-remarks="' . htmlspecialchars($request['remarks']) . '"
                        data-price="' . htmlspecialchars($request['template_price']) . '"
                        onclick="populateModal(this)">
                        <i class="fa-solid fa-eye"></i>
                    </button>';
                        echo "</div>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='14'>No requests found.</td></tr>";
                }

                ?>
                </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="14" id="paginationColspan">
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

<div id="approvePopup" class="popup-overlay">
    <div class="popup-content">
        <h2>Confirm Document Approval</h2>
        <p>Are you sure you want to approve this document request?</p>
        <form id="approveForm" action="approveDocumentRequestController.php" method="POST">
            <input type="hidden" name="request_id" id="approve-request-id">
            <input type="hidden" name="document_type" value="permit">
            <div class="popup-buttons">
                <button type="submit" class="confirm-btn">Confirm</button>
                <button type="button" class="cancel-btn" onclick="closeApprovePopup()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<div id="disapprovePopup" class="popup-overlay">
    <div class="popup-content">
        <h2>Confirm Document Disapproval</h2>
        <p>Are you sure you want to disapprove this document request? Please provide a reason for disapproval.</p>
        <form id="disapproveForm" action="disapproveDocumentRequestController.php" method="POST">
            <input type="hidden" name="request_id" id="popup-request-id">
            <input type="hidden" name="document_type" value="permit">
            <textarea name="remarks" id="disapprove_reason" placeholder="Enter reason for disapproval" required style="width: 100%; height: 100px; resize: none;"></textarea>
            <div class="popup-buttons">
                <button type="submit" class="confirm-btn">Confirm</button>
                <button type="button" class="cancel-btn" onclick="closeDisapprovePopup()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<div id="issuePopup" class="popup-overlay">
    <div class="popup-content">
        <h2>Confirm Document Issuance</h2>
        <p>Are you sure you want to issue this document?</p>
        <form id="issueForm" action="issuedDocumentRequestController.php" method="POST">
            <input type="hidden" name="request_id" id="issue-request-id">
            <input type="hidden" name="document_type" value="permit">
            <div class="popup-buttons">
                <button type="submit" class="confirm-btn">Confirm</button>
                <button type="button" class="cancel-btn" onclick="closeIssuePopup()">Cancel</button>
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
function showApprovePopup(requestId) {
    document.getElementById("approve-request-id").value = requestId;
    const popup = document.getElementById("approvePopup");
    popup.classList.add("active");
}

function closeApprovePopup() {
    const popup = document.getElementById("approvePopup");
    popup.classList.remove("active");
    document.getElementById("approveForm").reset();
}

function showDisapprovePopup(requestId) {
    document.getElementById("popup-request-id").value = requestId;
    const popup = document.getElementById("disapprovePopup");
    popup.classList.add("active");
}

function closeDisapprovePopup() {
    const popup = document.getElementById("disapprovePopup");
    popup.classList.remove("active");
    document.getElementById("disapproveForm").reset();
}

function showIssuePopup(requestId) {
    document.getElementById("issue-request-id").value = requestId;
    const popup = document.getElementById("issuePopup");
    popup.classList.add("active");
}

function closeIssuePopup() {
    const popup = document.getElementById("issuePopup");
    popup.classList.remove("active");
    document.getElementById("issueForm").reset();
}

document.getElementById("approvePopup").addEventListener("click", function(e) {
    if (e.target === this) {
        closeApprovePopup();
    }
});

document.getElementById("disapprovePopup").addEventListener("click", function(e) {
    if (e.target === this) {
        closeDisapprovePopup();
    }
});

document.getElementById("issuePopup").addEventListener("click", function(e) {
    if (e.target === this) {
        closeIssuePopup();
    }
});
</script>


            <div class="modal-overlay-add">
                <div class="modal-add" id="accessible-modal-add" role="dialog" aria-modal="true" aria-labelledby="modal-title-add" aria-describedby="modal-description-add">
                    <div class="modal-header-add">
                        <h2 id="modal-title-add">Issue Permit</h2>
                        <button class="modal-close-add" aria-label="Close modal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-content-add">
                        <form id="addForm-add" action="adminPermitController.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm();">
                            <div class="form-group-add">
                                <input type="hidden" id="account-id-add" name="account_id" value="<?= $_SESSION['account_id'] ?>">
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
                                            . " (" . htmlspecialchars($resident['age']) . " years old)</option>";
                                        }
                                    } else {
                                        echo "<option>No residents found</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group-add">
                                <label for="add-document-add">Type of Document</label>
                                <select id="add-document-add" name="type_of_document" required>
                                    <option value="" disabled selected>Select a document type</option>
                                    <?php
                                    if (!empty($typeOfDocument)) {
                                        foreach ($typeOfDocument as $document) {
                                            echo "<option value='" . htmlspecialchars($document['id']) . "' data-price='" . htmlspecialchars($document['price']) . "' data-price-enabled='" . htmlspecialchars($document['price_enabled']) . "'>" 
                                            . htmlspecialchars($document['doc_name']) . "</option>";
                                        }
                                    } else {
                                        echo "<option>No document types available</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group-add">
                                <label for="add-purpose-add">Purpose</label>
                                <textarea id="add-purpose-add" name="purpose" placeholder="Enter the purpose of the document" style="width: 99%; height: 150px; resize: none;"></textarea>
                            </div>

                            <div class="form-group-add" id="price-container" style="display: none;">
                                <label for="price">Price</label>
                                <input type="text" id="price" name="price" readonly>
                            </div>

                            <?php if (!empty($typeOfPayment)) : ?>
                            <div class="form-group-add" id="payment-type-container" style="display: none;">
                                <label for="add-payment-type-add">Type of Payment</label>
                               
                                <select id="add-payment-type-add" name="payment_type">
                                    <option value="" disabled selected>Select a payment type</option>
                                    <?php
                                    foreach ($typeOfPayment as $payment) {
                                        if ($payment['status'] === 'available') {
                                            echo "<option value='" . htmlspecialchars($payment['id']) . "' data-qr-photo='" . htmlspecialchars($payment['qr_photo']) . "' data-account-name='" . htmlspecialchars($payment['account_name']) . "'>" 
                                            . htmlspecialchars($payment['name']) . "</option>";
                                        }
                                    }

                                    ?>
                                </select>
                                
                            </div>
                            <?php endif; ?>

                            
                            <div class="form-group-add" id="payment-details-container" style="display: none;">
                                <label>Payment Details</label>
                                <div id="payment-image-container"></div>
                                <p id="account-name"></p>
                            </div>
                            
                            <div class="form-group-add" id="proof-of-payment-container" style="display: none;">
                                <label for="">
                                    Proof of Payment <p style="color: gray; display: inline;">(Select one image, max size 8 MB)</p>
                                    <p id="file-error-message" style="color:red; display:none;"></p>
                                </label>
                                <input type="file" id="evidence-picture-add" name="proof_of_payment" accept="image/*" onchange="validateFileSize()">
                                <div id="selected-file"></div> <!-- Container to show the selected file -->
                            </div>

                            <div class="form-group-add" id="payment-amount-container" style="display: none;">
                                <label for="payment-amount">Payment Amount</label>
                                <input type="number" id="payment-amount" name="payment_amount" step="0.01" min="0">
                                <p id="payment-error" style="color: red; display: none;">Payment amount cannot exceed the price.</p>
                            </div>

                            <div class="form-group-add">
                                <label for="number-of-copies">No. of Copies</label>
                                <input type="number" id="number-of-copies" name="number_of_copies" min="1" max="5" value="1" required>
                            </div>

                            <input type="submit" class="button-add" value="Add Permit">
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal Structure Update -->
            <div class="modal-overlay">
                <div class="modal" id="accessible-modal" role="dialog" aria-modal="true" aria-labelledby="modal-title" aria-describedby="modal-description">
                    <div class="modal-header">
                        <h2 id="modal-title">Show Permit</h2>
                        <button class="modal-close" aria-label="Close modal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-content">
                        <div class="form-group">
                            <label for="edit-requestor">Name of Requestor</label>
                            <input type="text" id="edit-requestor" class="form-text" readonly>
                        </div>
                        <div class="form-group">
                            <label for="edit-document">Type of Document</label>
                            <input type="text" id="edit-document" class="form-text" readonly>
                        </div>
                        <div class="form-group">
                            <label for="edit-purpose">Purpose</label>
                            <textarea id="edit-purpose" class="form-text" style="width: 99%; height: 150px; resize: none;" readonly></textarea>
                        </div>
                        <div class="form-group">
                            <label for="edit-price">Price</label>
                            <input type="text" id="edit-price" class="form-text" readonly>
                        </div>
                        <div class="form-group">
                            <label for="edit-payment-type">Type of Payment</label>
                            <input type="text" id="edit-payment-type" class="form-text" readonly>
                        </div>
                        <div class="form-group">
                            <label for="edit-payment-amount">Payment Amount</label>
                            <input type="text" id="edit-payment-amount" class="form-text" readonly>
                        </div>
                        <div class="form-group">
                            <label for="edit-number-of-copies">No. of Copies</label>
                            <input type="text" id="edit-number-of-copies" class="form-text" readonly>
                        </div>
                        <div class="form-group">
                            <label for="edit-status">Status</label>
                            <input type="text" id="edit-status" class="form-text" readonly>
                        </div>
                        <div class="form-group">
                            <label for="edit-status">Remarks</label>
                            <textarea id="edit-remarks" class="form-text" style="width: 99%; height: 150px; resize: none;" readonly></textarea>
                        </div>
                        <div class="form-group">
                            <label for="edit-evidence">Proof of Payment</label>
                            <div id="existing-evidence">
                                <p>No proof of payment</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script>

                // AJAX FOR SEARCH
                $(document).ready(function() {
                    // Search functionality with AJAX
                    $('#searchInput').on('keyup', function() {
                        let query = $(this).val(); // Get the input value
                        console.log("Search Query:", query); // Check the input value

                        // Perform the search query, or fetch all data if query is empty
                        $.ajax({
                            url: 'adminPermitSearch.php', // Ensure this is the correct path to your PHP file
                            method: 'POST',
                            cache: false,
                            data: { query: query }, // Send the search query to the server, it can be empty
                            success: function(data) {
                                console.log("Response Data:", data); // Log the response data to ensure it's correct
                                $('tbody').html(data); // Update the table body with the data (either all or filtered results)

                                // Attach click event for the dynamically loaded buttons
                                $('.modal-open').on('click', function() {
                                    let requestId = $(this).data('request-id');
                                    console.log("Clicked Request ID: " + requestId); // Debugging: check if the click event is captured

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

                function populateModal(button) {
                    const requestId = button.getAttribute('data-request-id');
                    const documentType = button.getAttribute('data-document-type');
                    const requestor = button.getAttribute('data-name-requested');
                    const purpose = button.getAttribute('data-purpose');
                    const paymentType = button.getAttribute('data-payment-type-id');
                    const paymentAmount = button.getAttribute('data-payment-amount');
                    const proofOfPayment = button.getAttribute('data-proof-of-payment');
                    const noOfCopies = button.getAttribute('data-no-of-copies');
                    const status = button.getAttribute('data-status');
                    const price = button.getAttribute('data-price'); // Added to fetch the template_price
                    const remarks = button.getAttribute('data-remarks'); // Added to fetch the template_price

                    document.getElementById('edit-requestor').value = requestor;
                    document.getElementById('edit-document').value = documentType;
                    document.getElementById('edit-purpose').value = purpose;
                    document.getElementById('edit-price').value = price; // Corrected to set the template_price value
                    document.getElementById('edit-payment-type').value = paymentType;
                    document.getElementById('edit-payment-amount').value = paymentAmount;
                    document.getElementById('edit-number-of-copies').value = noOfCopies;
                    document.getElementById('edit-status').value = status;
                    document.getElementById('edit-remarks').value = remarks;

                    if (proofOfPayment) {
                        const proofOfPaymentPath = '../../img/proof_of_payment/' + proofOfPayment;
                        document.getElementById('existing-evidence').innerHTML = `
                            <a href="${proofOfPaymentPath}" target="_blank">
                                <img src="${proofOfPaymentPath}" alt="Proof of Payment" width="100" height="150" style="margin-left:auto; margin-right:auto; display:block;">
                            </a>
                        `;
                    } else {
                        document.getElementById('existing-evidence').innerHTML = "<p>No proof of payment</p>";
                    }

                    document.querySelector('.modal-overlay').classList.add('is-open');
                }

                document.querySelector('.modal-close').addEventListener('click', function() {
                    document.querySelector('.modal-overlay').classList.remove('is-open');
                });
            </script>
        </section>
    </main>
</body>

<script>

    
$(document).ready(function() {
    // Detect changes in the search box
    $('#resident-search').on('keyup', function() {
        let searchQuery = $(this).val(); // Get the current value of the search input
        
        // Perform AJAX request to fetch residents based on the search query
        $.ajax({
            url: 'adminResidentSearchModal.php',  // PHP file to handle the search
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

<script>
                document.getElementById('add-payment-type-add').addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const qrPhoto = selectedOption.getAttribute('data-qr-photo');
                    const accountName = selectedOption.getAttribute('data-account-name');


                    if (qrPhoto && accountName) {
                        document.getElementById('payment-details-container').style.display = 'block';
                        document.getElementById('proof-of-payment-container').style.display = 'block';
                        document.getElementById('payment-image-container').innerHTML = `<img src="../../${qrPhoto}" alt="QR Code" style="max-width: 100px;">`;
                        document.getElementById('account-name').textContent = `Account Name: ${accountName}`;
                    } else {
                        document.getElementById('payment-details-container').style.display = 'none';
                        document.getElementById('proof-of-payment-container').style.display = 'none';
                    }
                });

                // Validate proof of payment file size
                function validateFileSize() {
                    const fileInput = document.querySelector('input[name="proof_of_payment"]');
                    const file = fileInput?.files[0];
                    const maxSize = 8 * 1024 * 1024; // 8 MB

                    if (file && file.size > maxSize) {
                        document.getElementById('file-error-message').style.display = 'block';
                        document.getElementById('file-error-message').textContent = 'File size exceeds 8 MB. Please select a smaller file.';
                        fileInput.value = ''; // Clear the input
                    } else {
                        document.getElementById('file-error-message').style.display = 'none';
                    }
                }
</script>
<script>
    document.getElementById('add-document-add').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const priceEnabled = selectedOption.getAttribute('data-price-enabled') === '1';
        const price = selectedOption.getAttribute('data-price');

        if (priceEnabled) {
            document.getElementById('price-container').style.display = 'block';
            document.getElementById('price').value = price;
            document.getElementById('payment-type-container').style.display = 'block';
            document.getElementById('payment-amount-container').style.display = 'block';
            document.getElementById('payment-amount').value = price;
        } else {
            document.getElementById('price-container').style.display = 'none';
            document.getElementById('payment-type-container').style.display = 'none';
            document.getElementById('payment-amount-container').style.display = 'none';
            document.getElementById('payment-amount').value = '';
        }

        // Save the selected document type to localStorage
        localStorage.setItem('selectedDocument', this.value);
    });

    document.getElementById('add-payment-type-add').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const qrPhoto = selectedOption.getAttribute('data-qr-photo');
        const accountName = selectedOption.getAttribute('data-account-name');

        if (qrPhoto && accountName) {
            document.getElementById('payment-details-container').style.display = 'block';
            document.getElementById('payment-image-container').innerHTML = `<a href="../../${qrPhoto}" target="_blank"><img src="../../${qrPhoto}" alt="QR Code" style="max-width: 150px;"></a>`;
            document.getElementById('account-name').textContent = `Account Name: ${accountName}`;
        } else {
            document.getElementById('payment-details-container').style.display = 'none';
        }

        // Save the selected payment type to localStorage
        localStorage.setItem('selectedPaymentType', this.value);
    });

    document.getElementById('payment-amount').addEventListener('input', function() {
        const price = parseFloat(document.getElementById('price').value);
        let paymentAmount = parseFloat(this.value);

        if (paymentAmount > price) {
            paymentAmount = price;
            this.value = paymentAmount.toFixed(2);
        }

        if (paymentAmount < 0) {
            paymentAmount = 0;
            this.value = paymentAmount.toFixed(2);
        }

        if (paymentAmount > price) {
            document.getElementById('payment-error').style.display = 'block';
        } else {
            document.getElementById('payment-error').style.display = 'none';
        }

        // Save the payment amount to localStorage
        localStorage.setItem('paymentAmount', this.value);
    });

    // Validate proof of payment file size
    function validateFileSize() {
        const fileInput = document.getElementById('proof-of-payment');
        const file = fileInput.files[0];
        const maxSize = 8 * 1024 * 1024; // 8 MB

        if (file && file.size > maxSize) {
            document.getElementById('file-error-message').style.display = 'block';
            document.getElementById('file-error-message').textContent = 'File size exceeds 8 MB. Please select a smaller file.';
            fileInput.value = ''; // Clear the input
        } else {
            document.getElementById('file-error-message').style.display = 'none';
        }
    }

    // Restore form inputs from localStorage on page load
    window.addEventListener('load', function() {
        const savedDocument = localStorage.getItem('selectedDocument');
        const savedPaymentType = localStorage.getItem('selectedPaymentType');
        const savedPaymentAmount = localStorage.getItem('paymentAmount');

        if (savedDocument) {
            document.getElementById('add-document-add').value = savedDocument;
            document.getElementById('add-document-add').dispatchEvent(new Event('change'));
        }

        if (savedPaymentType) {
            document.getElementById('add-payment-type-add').value = savedPaymentType;
            document.getElementById('add-payment-type-add').dispatchEvent(new Event('change'));
        }

        if (savedPaymentAmount) {
            document.getElementById('payment-amount').value = savedPaymentAmount;
        }
    });

    // Clear form inputs when modal is closed
    function validateForm() {
        const residentSelect = document.getElementById('add-residents-add');
        const documentSelect = document.getElementById('add-document-add');
        const purposeInput = document.getElementById('add-purpose-add');
    
        if (!residentSelect.value || !documentSelect.value || !purposeInput.value.trim()) {
            alert('Please fill in all required fields.');
            return false;
        }
        return true;
    }
    
    document.querySelector('.modal-close-add').addEventListener('click', function() {
        document.getElementById('addForm-add').reset();
        document.getElementById('price-container').style.display = 'none';
        document.getElementById('payment-type-container').style.display = 'none';
        document.getElementById('payment-details-container').style.display = 'none';
        document.getElementById('proof-of-payment-container').style.display = 'none';
        document.getElementById('payment-amount-container').style.display = 'none';

        // Clear localStorage
        localStorage.removeItem('selectedDocument');
        localStorage.removeItem('selectedPaymentType');
        localStorage.removeItem('paymentAmount');
    });

    // Clear form inputs when modal overlay is clicked
    document.querySelector('.modal-overlay-add').addEventListener('click', function(event) {
        if (event.target === this) {
            document.getElementById('addForm-add').reset();
            document.getElementById('price-container').style.display = 'none';
            document.getElementById('payment-type-container').style.display = 'none';
            document.getElementById('payment-details-container').style.display = 'none';
            document.getElementById('proof-of-payment-container').style.display = 'none';
            document.getElementById('payment-amount-container').style.display = 'none';

            // Clear localStorage
            localStorage.removeItem('selectedDocument');
            localStorage.removeItem('selectedPaymentType');
            localStorage.removeItem('paymentAmount');
        }
    });
</script>
</html>