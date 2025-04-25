<!DOCTYPE html>
<html lang="en">

<head>
    <?php
$residentViewProfileModel = new ResidentViewProfileModel();
$personalAccount = $residentViewProfileModel->showPersonalAccount($conn, $_SESSION['account_id']);
$accountId = $_SESSION['account_id'] ?? null;?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Barangay Request</title>
    <!-- State persistence meta tag -->
    <meta name="sidebar-collapsed" content="false" id="sidebar-meta">
    <script>
        // Prevent transitions during page load
        document.documentElement.classList.add('no-transitions');
        window.addEventListener('load', function() {
            setTimeout(function() {
                document.documentElement.classList.remove('no-transitions');
            }, 100);
        });
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Metrophobic&display=swap" rel="stylesheet">
    <link href="../../css/resident/residentBootstrap.css" rel="stylesheet">
    <link rel="icon" href="favicon-32x32.png" type="image/x-icon">
    <link rel="stylesheet" href="../../css/admin/adminPortal.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="../../css/resident/residentTable.css">
    <script src="https://kit.fontawesome.com/4907458c0c.js" crossorigin="anonymous"></script>
    <script src="../../js/main.js" defer></script>
    <script src="../../js/dashBoard.js" defer></script>
    <script src="../../js/add-modal.js" defer></script>
    <script src="../../js/view-modal.js" defer></script>
    <script src="../../js/update-modal.js" defer></script>
    <style>
        * {
            font-family: Poppins;
        }
        .main-head {
            background-color: rgb(15, 117, 54);
        }
        .hamburger {
            background-color: rgb(5, 88, 37);
        }
        .hamburger:hover {
            background-color: rgb(3, 71, 29);
        }
        .nav-list-item:hover {
            background-color: rgb(3, 71, 29);
        }
        .datetime {
            top: 0px;
        }
        .footer-nav-bar {
            color: rgb(3, 71, 29);
        }
        .header-nav {
            background-color: #004D40;
        }
        .floating-menu a {
            background-color: #004D40;
        }
        .floating-menu a:hover {
            background-color: rgb(3, 71, 29);
        }
        .head button:hover {
            color: rgb(3, 71, 29);
        }
        .fa-user:before{
            margin-right: 5px;
        }

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

        .modal-view {
            margin-top: 350px;
            max-width: 750px;
            width: 100%;
        }
        

    </style>
</head>

<body>
    <div class="app-container">
        <!-- Sidebar Navigation -->
        <div class="sidebar" id="sidebar" data-collapsed="false">
            <div class="sidebar-header">
                <div class="logo-container" id="sidebar-logo">
                    <img src="../../img/Barangay Logo.png" alt="E-Barangay Logo" class="logo">
                    <span class="logo-text">BARANGAY 752</span>
                </div>
                <button class="toggle-btn" id="toggle-sidebar">
                    <i class="fas fa-bars nav-icon"></i>
                </button>
            </div>
            <nav class="sidebar-nav">
                <div class="nav-items">
                    <a href="../../index.php" class="nav-item" data-tooltip="Home">
                        <i class="fas fa-home nav-icon"></i>
                        <span class="nav-label">Home</span>
                    </a>
                    <a href="residentDashboardController.php" class="nav-item active" data-tooltip="Dashboard">
                        <i class="fas fa-tachometer-alt nav-icon"></i>
                        <span class="nav-label">Dashboard</span>
                    </a>
                    <a href="residentRequestController.php" class="nav-item" data-tooltip="Requests">
                        <i class="fas fa-file-alt nav-icon"></i>
                        <span class="nav-label">Requests</span>
                    </a>
                    <a href="residentReportController.php" class="nav-item" data-tooltip="Reports">
                        <i class="fas fa-exclamation-triangle nav-icon"></i>
                        <span class="nav-label">Reports</span>
                    </a>
                </div>
            </nav>
            
            <div class="sidebar-footer">
                <div class="user-info">
                <?php
                                if (!empty($personalAccount['profile_image_name'])) {
                                    echo "<a href='../../img/profile_images/" . htmlspecialchars($personalAccount['profile_image_name'], ENT_QUOTES) . "' target='_blank'>";
                                    echo "<img src='../../img/profile_images/" . htmlspecialchars($personalAccount['profile_image_name'], ENT_QUOTES) . "' alt='Profile Image' class='sidebar-profile-image' id='profileImage'>";
                                    echo "</a>";
                                } else {
                                    echo "<a href='../../img/id_images/default.jpg' target='_blank'>";
                                    echo "<img src='../../img/id_images/default.jpg' alt='Default Profile Image' class='sidebar-profile-image' id='profileImage'>";
                                    echo "</a>";
                                }
                                ?>
                <div class="user-details">
                    <p class="user-name">
                    <?php
                    if (isset($_SESSION['first_name']) && isset($_SESSION['last_name'])) {
                        echo htmlspecialchars($_SESSION['first_name']) . " " . htmlspecialchars($_SESSION['last_name']);
                    } else {
                        echo "Guest";
                    } ?>
                    </p>
                    <p class="user-role">Resident</p>
                </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="main-content" id="main-content">
            <!-- Top Navigation Bar -->
            <section class="top-navbar">
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
                            <?php echo '<h3>' . ($_SESSION['username']) . '</h3>'; ?>
                            <a href="residentViewProfileController.php">View Profile</a>
                            <a href="../logoutController.php">Log Out</a>
                        </div>
                    </div>
                </nav>
            </section>
            <button class="mobile-toggle-btn" id="mobile-toggle">
                <i class="fas fa-bars"></i> <span class="menu-text">Menu</span>
            </button>
             <!-- Message Box -->
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
          background: linear-gradient(135deg, #004d40, #002d26);
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
          }, 5000);
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
          }, 500);
        }
      </script>

            <!-- DASHBOARD -->
            <div class="content-resident">
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <td colspan="13" class="search-column">
                                    <div class="search-bar">
                                        <p>Search</p>
                                        <input type="text" placeholder="Search Requests" id="searchInput">
                                        <button class="modal-open-add button" id="add-permit">Request Document +</button>
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
                                    echo "<td>" . htmlspecialchars($request['no_of_copies']) . "</td>";
                                    echo "<td>" . (!empty($request['remarks']) ? (strlen($request['remarks']) > 16 ? htmlspecialchars(substr($request['remarks'], 0, 16)) . "..." : htmlspecialchars($request['remarks'])) : "N/A") . "</td>";
                                    $statusText = ($request['status'] === 'approved') ? 'available' : htmlspecialchars($request['status']);
                                    $statusColor = ($request['status'] === 'approved') ? 'green' : (($request['status'] === 'pending') ? 'orange' : (($request['status'] === 'rejected' || $request['status'] === 'disapproved') ? 'red' : (($request['status'] === 'issued') ? 'gray' : 'gray')));
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
                                                     1px 1px 0 black;'>"
                                        . htmlspecialchars($statusText) . "</span></td>";
                                    echo "<td>";
                                    // View Button
                                    if ($request['status'] === 'rejected' || $request['status'] === 'issued') {
                                        echo "<button class='modal-open-view button' aria-haspopup='true' style='background-color:rgb(92, 92, 92);'
                                            data-request-id='" . $request['request_id'] . "'
                                            data-document-type='" . htmlspecialchars($request['document_name']) . "'
                                            data-purpose='" . htmlspecialchars($request['purpose']) . "'
                                            data-date-submitted='" . htmlspecialchars($request['date_submitted']) . "'
                                            data-payment-type-id='" . htmlspecialchars($request['payment_type_id']) . "'
                                            data-proof-of-payment='" . htmlspecialchars($request['proof_of_payment']) . "'
                                            data-payment-amount='" . htmlspecialchars($request['payment_amount']) . "'
                                            data-no-of-copies='" . htmlspecialchars($request['no_of_copies']) . "'
                                            data-status='" . htmlspecialchars($request['status']) . "'
                                            data-remarks='" . htmlspecialchars($request['remarks']) . "'>
                                            <i class='fa-solid fa-eye'></i>
                                        </button>";
                                    }
                                    // Edit Button
                                    if ($request['status'] === 'approved' || $request['status'] === 'pending') {
                                        echo "<button class='modal-open button' aria-haspopup='true'
                                            data-request-id='" . $request['request_id'] . "'
                                            data-document-type='" . htmlspecialchars($request['document_name']) . "'
                                            data-purpose='" . htmlspecialchars($request['purpose']) . "'
                                            data-date-submitted='" . htmlspecialchars($request['date_submitted']) . "'
                                            data-payment-type-id='" . htmlspecialchars($request['payment_type_id']) . "'
                                            data-proof-of-payment='" . htmlspecialchars($request['proof_of_payment']) . "'
                                            data-payment-amount='" . htmlspecialchars($request['payment_amount']) . "'
                                            data-no-of-copies='" . htmlspecialchars($request['no_of_copies']) . "'
                                            data-status='" . htmlspecialchars($request['status']) . "'
                                            data-remarks='" . htmlspecialchars($request['remarks']) . "'
                                            onclick='populateModal(this)'>
                                            <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-pen' viewBox='0 0 16 16'>
                                                <path d='m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001m-.644.766a.5.5 0 0 0-.707 0L1.95 11.756l-.764 3.057 3.057-.764L14.44 3.854a.5.5 0 0 0 0-.708z'/>
                                            </svg>
                                        </button>";
                                    }
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='13'>No requests found.</td></tr>";
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

            <!-- Add Modal -->
            <div class="modal-overlay-add">
                <div class="modal-add" id="accessible-modal-add" role="dialog" aria-modal="true" aria-labelledby="modal-title-add" aria-describedby="modal-description-add">
                    <div class="modal-header-add">
                        <h2 id="modal-title-add">Request Document</h2>
                        <button class="modal-close-add" aria-label="Close modal">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-content-add">
                        <form id="addForm-add" action="residentRequestController.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" id="account-id-add" name="account_id" value="<?= $_SESSION['account_id'] ?>">
                            <input type="hidden" id="resident-id-add" name="resident_id" value="<?= $_SESSION['resident_id'] ?>">
                            <div class="form-group-add">
                                <label for="add-document-add">Type of Document (Uri ng Dokumento)</label>
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
                                <label for="add-purpose-add">Purpose (Dahilan)</label>
                                <textarea id="add-purpose-add" name="purpose" placeholder="Enter the purpose of the document" required style="width: 99%; height: 100px; resize: none;"></textarea>
                            </div>
                            <div class="form-group-add" id="price-container" style="display: none;">
                                <label for="price">Price (Presyo)</label>
                                <input type="text" id="price" name="price" readonly>
                            </div>
                            <?php if (!empty($typeOfPayment)) : ?>
                                <div class="form-group-add" id="payment-type-container" style="display: none;">
                                    <label for="add-payment-type-add">Type of Payment (Uri ng Bayad)</label>
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
                                    Proof of Payment Upload (Pag-upload ng Patunay ng Pagbabayad) <p style="color: gray; display: inline;">(Select one image, max size 8 MB)</p>
                                    <p id="file-error-message" style="color:red; display:none;"></p>
                                </label>
                                <input type="file" id="evidence-picture-add" name="proof_of_payment" accept="image/*" onchange="validateFileSize()">
                                <div id="selected-file"></div>
                            </div>
                            <div class="form-group-add" id="payment-amount-container" style="display: none;">
                                <label for="payment-amount">Payment Amount (Halaga ng Bayad)</label>
                                <input type="number" id="payment-amount" name="payment_amount" step="0.01" min="0">
                                <p id="payment-error" style="color: red; display: none;">Payment amount cannot exceed the price.</p>
                            </div>
                            <div class="form-group-add">
                                <label for="number-of-copies">No. of Copies (Bilang ng Kopya)</label>
                                <input type="number" id="number-of-copies" name="number_of_copies" value="1" min="1" max="5">
                            </div>
                            <input type="submit" class="button-add" value="Request Document">
                        </form>
                    </div>
                </div>
            </div>

            <!-- Update Modal -->
            <div class="modal-overlay">
                <div class="modal" id="accessible-modal" role="dialog" aria-modal="true">
                    <div class="modal-header">
                        <h2>Update Requested Document</h2>
                        <button class="modal-close" aria-label="Close modal">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-content">
                        <form id="updateForm" action="residentRequestUpdate.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" id="account-id" name="account_id" value="<?= $_SESSION['account_id'] ?>">
                            <input type="hidden" id="update-request-id" name="request_id" value="">
                            <div class="form-group">
                                <label for="edit-document">Type of Document (Uri ng Dokumento)</label>
                                <select id="edit-document" name="type_of_document">
                                    <option value="" disabled>Select a document type</option>
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
                            <script>
                                document.getElementById('edit-document').addEventListener('mousedown', function(e) {
                                    e.preventDefault();
                                });
                            </script>
                            <div class="form-group">
                                <label for="edit-purpose">Purpose (Dahilan)</label>
                                <textarea id="edit-purpose" name="purpose" placeholder="Enter the purpose of the document" required style="width: 99%; height: 100px; resize: none;"></textarea>
                            </div>
                            <div class="form-group" id="edit-price-container">
                                <label for="edit-price">Price (Presyo)</label>
                                <input type="text" id="edit-price" name="price" readonly>
                            </div>
                            <?php if (!empty($typeOfPayment)) : ?>
                                <div class="form-group" id="edit-payment-type-container">
                                    <label for="edit-payment-type">Type of Payment (Uri ng Pagbabayad)</label>
                                    <select id="edit-payment-type" name="payment_type">
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
                            <div class="form-group" id="payment-details-container" style="display: none;">
                                <label>Payment Details</label>
                                <div id="payment-image-container"></div>
                                <p id="account-name"></p>
                            </div>
                            <div class="form-group" id="edit-payment-amount-container">
                                <label for="edit-payment-amount">Payment Amount (Halaga ng Bayad)</label>
                                <input type="number" id="edit-payment-amount" name="payment_amount" step="0.01" min="0" required>
                                <p id="edit-payment-error" style="color: red; display: none;">Payment amount cannot exceed the price.</p>
                            </div>
                            <div class="form-group">
                                <label for="edit-number-of-copies">No. of Copies (Bilang ng Kopya)</label>
                                <input type="number" id="edit-number-of-copies" name="number_of_copies" min="1" required>
                            </div>
                            <div class="form-group">
                                <label for="edit-status">Status (Katayuan)</label>
                                <input type="text" id="edit-status" name="status" readonly>
                            </div>
                            <div class="form-group">
                                <label for="edit-evidence">Proof of Payment (Patunay ng Pagbabayad)</label>
                                <div id="existing-evidence"></div>
                            </div>
                            <div class="form-group">
                                <label for="">
                                    Proof of Payment Upload (Pag-upload ng Patunay ng Pagbabayad) <p style="color: gray; display: inline;">(Select one image, max size 8 MB)</p>
                                    <p id="file-error-message" style="color:red; display:none;"></p>
                                </label>
                                <input type="file" id="proof-of-payment" name="proof_of_payment" accept="image/*" onchange="validateFileSize()">
                                <div id="selected-file"></div>
                            </div>
                            <input type="submit" class="button" value="Update Request">
                        </form>
                    </div>
                </div>
            </div>

            <!-- View Modal -->
            <div class="modal-overlay-view">
                <div class="modal-view" id="accessible-modal-view" role="dialog" aria-modal="true" aria-labelledby="modal-title-view" aria-describedby="modal-description-view">
                    <div class="modal-header-view">
                        <h2 id="modal-title-view">View Permit Details</h2>
                        <button class="modal-close-view" aria-label="Close modal">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-content-view">
                        <div class="form-group-view">
                            <label for="view-request-id">Request ID</label>
                            <input type="text" id="view-request-id" readonly>
                        </div>
                        <div class="form-group-view">
                            <label for="view-document">Type of Document (Uri ng Dokumento)</label>
                            <input type="text" id="view-document" readonly>
                        </div>
                        <div class="form-group-view">
                            <label for="view-purpose">Purpose (Dahilan)</label>
                            <textarea id="view-purpose" readonly style="width: 99%; height: 100px; resize: none;"></textarea>
                        </div>
                        <div class="form-group-view">
                            <label for="view-price">Price (Presyo)</label>
                            <input type="text" id="view-price" readonly>
                        </div>
                        <div class="form-group-view">
                            <label for="view-payment-type">Type of Payment (Uri ng Bayad)</label>
                            <input type="text" id="view-payment-type" readonly>
                        </div>
                        <div class="form-group-view">
                            <label for="view-payment-amount">Payment Amount (Halaga ng Bayad)</label>
                            <input type="text" id="view-payment-amount" readonly>
                        </div>
                        <div class="form-group-view">
                            <label for="view-number-of-copies">No. of Copies (Bilang ng Kopya)</label>
                            <input type="text" id="view-number-of-copies" readonly>
                        </div>
                        <div class="form-group-view">
                            <label for="view-status">Status (Katayuan)</label>
                            <input type="text" id="view-status" readonly>
                        </div>
                        <div class="form-group-view">
                            <label for="view-evidence">Proof of Payment (Patunay ng Pagbabayad)</label>
                            <div id="view-evidence"></div>
                        </div>
                        <div class="form-group-view">
                            <label for="view-remarks">Remarks (Dagdag na Pahayag)</label>
                            <textarea id="view-remarks" rows="4" readonly style="resize: none;"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../../js/residentBootstrap.js"></script>

    <script>
        // AJAX FOR SEARCH
        $(document).ready(function() {
            // Search functionality with AJAX
            $('#searchInput').on('keyup', function() {
                let query = $(this).val();
                console.log("Search Query:", query);

                $.ajax({
                    url: 'residentRequestSearch.php',
                    method: 'POST',
                    cache: false,
                    data: {
                        query: query,
                        resident_id: <?= json_encode($_SESSION['resident_id']); ?>
                    },
                    success: function(data) {
                        console.log("Search Response Data:", data);
                        $('tbody').html(data);

                        // Rebind edit modal buttons
                        $('.modal-open').off('click').on('click', function() {
                            let requestId = $(this).data('request-id');
                            console.log("Clicked Edit Request ID: " + requestId);
                            $('.modal-overlay').addClass('is-active');
                            populateModal(this);
                        });

                        // Rebind view modal buttons
                        $('.modal-open-view').off('click').on('click', function() {
                            let requestId = $(this).data('request-id');
                            console.log("Clicked View Request ID: " + requestId);
                            populateViewModal(this);
                        });
                    },
                    error: function(request, error) {
                        console.error("Search Error:", error);
                    }
                });
            });

            // Initial binding for view modal buttons
            $('.modal-open-view').on('click', function() {
                let requestId = $(this).data('request-id');
                console.log("Initial Clicked View Request ID: " + requestId);
                populateViewModal(this);
            });

            // Close modals
            $('.modal-close').on('click', function() {
                $('.modal-overlay').removeClass('is-active');
                console.log("Update modal closed via close button");
            });

            $('.modal-close-view').on('click', function() {
                $('.modal-overlay-view').removeClass('is-active');
                console.log("View modal closed via close button");
            });

            $('.button-close-view').on('click', function() {
                $('.modal-overlay-view').removeClass('is-active');
                console.log("View modal closed via secondary close button");
            });
        });

        // Populate the edit modal with request data
        function populateModal(button) {
            console.log("Populating edit modal");
            var requestId = button.getAttribute('data-request-id');
            var documentType = button.getAttribute('data-document-type');
            var purpose = button.getAttribute('data-purpose');
            var paymentTypeId = button.getAttribute('data-payment-type-id');
            var paymentAmount = button.getAttribute('data-payment-amount');
            var proofOfPayment = button.getAttribute('data-proof-of-payment');
            var noOfCopies = button.getAttribute('data-no-of-copies');
            var status = button.getAttribute('data-status');
            var remarks = button.getAttribute('data-remarks');

            document.getElementById('edit-status').value = status;
            document.getElementById('update-request-id').value = requestId;

            const editDocumentSelect = document.getElementById('edit-document');
            const documentOption = Array.from(editDocumentSelect.options).find(option => option.text === documentType);
            if (documentOption) {
            editDocumentSelect.value = documentOption.value;
            var price = parseFloat(documentOption.getAttribute('data-price'));
            document.getElementById('edit-price').value = price;

            // Hide payment type and payment amount if price is 0
            if (price === 0) {
                document.getElementById('edit-payment-type-container').style.display = 'none';
                document.getElementById('edit-payment-amount-container').style.display = 'none';
            } else {
                document.getElementById('edit-payment-type-container').style.display = 'block';
                document.getElementById('edit-payment-amount-container').style.display = 'block';
            }
            } else {
            console.warn("Document type not found in edit select:", documentType);
            }

            document.getElementById('edit-purpose').value = purpose;

            const editPaymentTypeSelect = document.getElementById('edit-payment-type');
            const paymentOption = Array.from(editPaymentTypeSelect.options).find(option => option.value === paymentTypeId);
            if (paymentOption) {
            editPaymentTypeSelect.value = paymentOption.value;
            } else {
            console.warn("Payment type ID not found in edit select:", paymentTypeId);
            }

            document.getElementById('edit-payment-amount').value = paymentAmount;
            document.getElementById('edit-number-of-copies').value = noOfCopies;

            const evidenceContainer = document.getElementById('existing-evidence');
            if (proofOfPayment) {
            const proofOfPaymentPath = `../../img/proof_of_payment/${proofOfPayment}`;
            evidenceContainer.innerHTML = `
                <a href="${proofOfPaymentPath}" target="_blank">
                <img src="${proofOfPaymentPath}" alt="Proof of Payment" width="50" height="50" style="margin-right:5px;">
                </a>`;
            } else {
            evidenceContainer.innerHTML = "No proof of payment";
            }
            console.log("Edit modal populated with request ID:", requestId);
        }

        // Populate the view modal with request data
        function populateViewModal(button) {
            console.log("Populating view modal");
            var requestId = button.getAttribute('data-request-id');
            var documentType = button.getAttribute('data-document-type');
            var purpose = button.getAttribute('data-purpose');
            var paymentTypeId = button.getAttribute('data-payment-type-id');
            var paymentAmount = button.getAttribute('data-payment-amount');
            var proofOfPayment = button.getAttribute('data-proof-of-payment');
            var noOfCopies = button.getAttribute('data-no-of-copies');
            var status = button.getAttribute('data-status');
            var remarks = button.getAttribute('data-remarks');

            // Open the modal (handled by view-modal.js, but ensure it's active)
            $('.modal-overlay-view').addClass('is-active');

            document.getElementById('view-request-id').value = requestId || '';
            document.getElementById('view-document').value = documentType || '';
            document.getElementById('view-purpose').value = purpose || '';
            document.getElementById('view-number-of-copies').value = noOfCopies || '';
            document.getElementById('view-status').value = status || '';
            document.getElementById('view-payment-amount').value = paymentAmount || '';
            document.getElementById('view-remarks').value = remarks || 'No remarks provided';

            const documentSelect = document.getElementById('edit-document');
            const documentOption = documentSelect && Array.from(documentSelect.options).find(option => option.text === documentType);
            if (documentOption) {
                const price = documentOption.getAttribute('data-price');
                document.getElementById('view-price').value = price || 'N/A';
            } else {
                document.getElementById('view-price').value = 'N/A';
                console.warn("Document type not found in edit select for view modal:", documentType);
            }

            const paymentSelect = document.getElementById('edit-payment-type');
            const paymentOption = paymentSelect && Array.from(paymentSelect.options).find(option => option.value === paymentTypeId);
            if (paymentOption) {
                document.getElementById('view-payment-type').value = paymentOption.text || 'N/A';
            } else {
                document.getElementById('view-payment-type').value = 'N/A';
                console.warn("Payment type ID not found in edit select for view modal:", paymentTypeId);
            }

            const evidenceContainer = document.getElementById('view-evidence');
            if (proofOfPayment) {
                const proofOfPaymentPath = `../../img/proof_of_payment/${proofOfPayment}`;
                evidenceContainer.innerHTML = `
                    <a href="${proofOfPaymentPath}" target="_blank">
                        <img src="${proofOfPaymentPath}" alt="Proof of Payment" width="50" height="50" style="margin-right:5px;">
                    </a>`;
            } else {
                evidenceContainer.innerHTML = "No proof of payment";
            }

            // Trigger input events to save form data to localStorage
            const inputs = document.querySelectorAll('.modal-view input, .modal-view textarea');
            inputs.forEach(input => {
                input.dispatchEvent(new Event('input'));
            });
            console.log("View modal populated with request ID:", requestId);
        }

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

        // Add Modal Handling
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
                fileInput.value = '';
            } else {
                document.getElementById('file-error-message').style.display = 'none';
            }
        }

        // Add Document Handling
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

            localStorage.setItem('paymentAmount', this.value);
        });

        

        
    </script>
</body>
</html>