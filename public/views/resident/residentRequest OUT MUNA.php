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
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Metrophobic&display=swap" rel="stylesheet">
  <link href="../../css/resident/residentBootstrap.css" rel="stylesheet">
  <link rel="icon" href="favicon-32x32.png" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Metrophobic&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../css/admin/adminPortal.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="../../css/admin/table.css">
    <link rel="stylesheet" href="../../css/main.css">
    <script src="https://kit.fontawesome.com/4907458c0c.js" crossorigin="anonymous"></script>
    <script src="../../js/main.js" defer></script>
    <script src="../../js/dashBoard.js" defer></script>
    <script src="../../js/add-modal.js" defer></script>
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
                        <td colspan="13" class="search-column">
                            <div class="search-bar">
                            <p>Search</p>
                            <input type="text" placeholder="Search Requests" id="searchInput">
                            <button class="modal-open-add button" onclick="" id="add-permit">Request Document +</button>
                            </div>
                        </td>
                        </tr>
                        <tr>
                        <th>Request ID</th>
                        <th>Type of Document</th>
                        <th>Name of Requested</th>
                        <th>Purpose</th>
                        <th>Date Submitted</th>
                        <th>Last Updated</th>
                        <th>Type of Payment</th>
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
                            echo "</td>";
                            echo "<td>" . htmlspecialchars($request['no_of_copies']) . "</td>";
                            echo "<td>" . (strlen($request['remarks']) > 16 ? htmlspecialchars(substr($request['remarks'], 0, 16)) . "..." : htmlspecialchars($request['remarks'])) . "</td>";
                            $statusText = ($request['status'] === 'approved') ? 'available' : htmlspecialchars($request['status']);
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
                            '>" . htmlspecialchars($statusText) . "</span></td>";
                                echo "<td>" . '<button class="modal-open button" aria-haspopup="true" 
                                data-request-id="' . $request['request_id'] . '" 
                                data-document-type="' . htmlspecialchars($request['document_name']) . '"
                                data-name-requested="' . $request['name_requested'] . '"
                                data-purpose="' . $request['purpose'] . '"
                                data-date-submitted="' . $request['date_submitted'] . '"
                                data-payment-type-id="' . htmlspecialchars($request['payment_type_id']) . '" 
                                data-proof-of-payment="' . htmlspecialchars($request['proof_of_payment']) . '" 
                                data-payment-amount="' . $request['payment_amount'] . '"
                                data-no-of-copies="' . $request['no_of_copies'] . '"
                                data-status="' . $request['status'] . '"
                                onclick="populateModal(this)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pen" viewBox="0 0 16 16">
                                <path d="m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001m-.644.766a.5.5 0 0 0-.707 0L1.95 11.756l-.764 3.057 3.057-.764L14.44 3.854a.5.5 0 0 0 0-.708z"/></path></svg></button>' . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='12'>No requests found.</td></tr>";
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
            </div>
            <div class="modal-overlay-add">
                <div class="modal-add" id="accessible-modal-add" role="dialog" aria-modal="true" aria-labelledby="modal-title-add" aria-describedby="modal-description-add">
                    <div class="modal-header-add">
                        <h2 id="modal-title-add">Issue Permit</h2>
                        <button class="modal-close-add" aria-label="Close modal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-content-add">
                        <form id="addForm-add" action="residentRequestController.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" id="account-id-add" name="account_id" value="<?= $_SESSION['account_id'] ?>">
                            <input type="hidden" id="resident-id-add" name="resident_id" value="<?= $_SESSION['resident_id'] ?>">
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
                                <textarea id="add-purpose-add" name="purpose" rows="10" cols="59" placeholder="Enter the purpose of the document" required style="resize: none;"></textarea>
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
                                <input type="number" id="number-of-copies" name="number_of_copies" min="1" max="5">
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
                        <h2 id="modal-title">Update Permit</h2>
                        <button class="modal-close" aria-label="Close modal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-content">
                        <form id="updateForm" action="adminPermitUpdate.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" id="account-id" name="account_id" value="<?= $_SESSION['account_id'] ?>">
                        <input type="hidden" id="update-request-id" name="request_id" value="">
                            <div class="form-group">
                                <label for="edit-document">Type of Document</label>
                                <select id="edit-document" name="type_of_document" required>
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
                            <div class="form-group">
                                <label for="edit-purpose">Purpose</label>
                                <textarea id="edit-purpose" name="purpose" rows="10" cols="59" placeholder="Enter the purpose of the document" required style="resize: none;"></textarea>
                            </div>
                            <div class="form-group" id="edit-price-container">
                                <label for="edit-price">Price</label>
                                <input type="text" id="edit-price" name="price" readonly>
                            </div>
                            <?php if (!empty($typeOfPayment)) : ?>
                            <div class="form-group" id="edit-payment-type-container">
                                <label for="edit-payment-type">Type of Payment</label>
                                <select id="edit-payment-type" name="payment_type" required>
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
                         
                            <div class="form-group" id="edit-payment-amount-container">
                                <label for="edit-payment-amount">Payment Amount</label>
                                <input type="number" id="edit-payment-amount" name="payment_amount" step="0.01" min="0" required>
                                <p id="edit-payment-error" style="color: red; display: none;">Payment amount cannot exceed the price.</p>
                            </div>
                            <div class="form-group">
                                <label for="edit-number-of-copies">No. of Copies</label>
                                <input type="number" id="edit-number-of-copies" name="number_of_copies" min="1" required>
                            </div>
                            <div class="form-group">
                                <label for="edit-status">Status</label>
                                <input type="text" id="edit-status" name="status" readonly>
                            </div>
                            <div class="form-group">
                                <label for="edit-evidence">Proof of Payment</label>
                                <div id="existing-evidence">
                                    <?php
                                    if (!empty($request['proof_of_payment'])) {
                                        $proofOfPaymentPath = '../../img/proof_of_payment/' . htmlspecialchars($request['proof_of_payment']);
                                        echo "<a href='" . $proofOfPaymentPath . "' target='_blank'>";
                                        echo "<img src='" . $proofOfPaymentPath . "' alt='Proof of Payment' width='50' height='50' style='margin-right:5px;'>";
                                        echo "</a>";
                                    } else {
                                        echo "No proof of payment";
                                    }
                                    ?>
                                </div>
                            </div>
                             <div class="form-group">
                                <label for="">
                                    Proof of Payment <p style="color: gray; display: inline;">(Select one image, max size 8 MB)</p>
                                    <p id="file-error-message" style="color:red; display:none;"></p>
                                </label>
                                <input type="file" id="proof-of-payment" name="proof_of_payment" accept="image/*" onchange="validateFileSize()">
                                <div id="selected-file"></div> <!-- Container to show the selected file -->
                            </div>
                            <input type="submit" class="button" value="Update Permit">
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>
</body>

<script>

        // AJAX FOR SEARCH
        $(document).ready(function() {
            // Search functionality with AJAX
            $('#searchInput').on('keyup', function() {
            let query = $(this).val(); // Get the input value
            console.log("Search Query:", query); // Check the input value

            // Perform the search query, or fetch all data if query is empty
            $.ajax({
                url: 'residentRequestSearch.php', // Ensure this is the correct path to your PHP file
                method: 'POST',
                cache: false,
                data: { 
                query: query,
                resident_id: <?= json_encode($_SESSION['resident_id']); ?> // Pass the resident ID
                }, // Send the search query and resident ID to the server
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
                // Populate the modal with request data
                function populateModal(button) {
                    var requestId = button.getAttribute('data-request-id');
                    var documentType = button.getAttribute('data-document-type');
                    var purpose = button.getAttribute('data-purpose');
                    var paymentTypeId = button.getAttribute('data-payment-type-id');
                    var paymentAmount = button.getAttribute('data-payment-amount');
                    var proofOfPayment = button.getAttribute('data-proof-of-payment');
                    var noOfCopies = button.getAttribute('data-no-of-copies');
                    var status = button.getAttribute('data-status');

                    document.getElementById('edit-status').value = status;
                    document.getElementById('update-request-id').value = requestId;

                    const editDocumentSelect = document.getElementById('edit-document');
                    const documentOption = Array.from(editDocumentSelect.options).find(option => option.text === documentType);
                    if (documentOption) {
                        editDocumentSelect.value = documentOption.value;

                        // Populate the payment type based on the selected payment type ID
                        const editPaymentTypeSelect = document.getElementById('edit-payment-type');
                        if (paymentTypeId) {
                            const paymentOption = Array.from(editPaymentTypeSelect.options).find(option => option.value === paymentTypeId);
                            if (paymentOption) {
                                editPaymentTypeSelect.value = paymentOption.value;
                            }
                        }

                        // Populate the price based on the selected document ID
                        var price = documentOption.getAttribute('data-price');
                        document.getElementById('edit-price').value = price;
                    }

                    document.getElementById('edit-purpose').value = purpose;

                    const editPaymentTypeSelect = document.getElementById('edit-payment-type');
                    const paymentOption = Array.from(editPaymentTypeSelect.options).find(option => option.value === paymentTypeId);
                    if (paymentOption) {
                        editPaymentTypeSelect.value = paymentOption.value;
                    }

                    document.getElementById('edit-payment-amount').value = paymentAmount;
                    document.getElementById('edit-number-of-copies').value = noOfCopies;

                    document.querySelector('.modal-overlay').classList.add('is-open');
                }

                document.querySelector('.modal-close').addEventListener('click', function() {
                    document.querySelector('.modal-overlay').classList.remove('is-open');
                });

                document.querySelector('tbody').addEventListener('click', function(event) {
                    if (event.target.classList.contains('populate-modal-btn')) {
                        event.preventDefault();
                        populateModal(event.target);
                    }
                });

                // Ensure payment amount does not exceed price
                document.getElementById('edit-payment-amount').addEventListener('input', function() {
                    const price = parseFloat(document.getElementById('edit-price').value);
                    let paymentAmount = parseFloat(this.value);

                    if (paymentAmount > price) {
                        paymentAmount = price;
                        this.value = paymentAmount.toFixed(2);
                        document.getElementById('edit-payment-error').style.display = 'block';
                    } else {
                        document.getElementById('edit-payment-error').style.display = 'none';
                    }

                    if (paymentAmount < 0) {
                        this.value = '0.00';
                    }
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