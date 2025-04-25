<!DOCTYPE html>
<html lang="en">
<head>

<?php
$residentViewProfileModel = new ResidentViewProfileModel();
$personalAccount = $residentViewProfileModel->showPersonalAccount($conn, $_SESSION['account_id']);
$accountId = $_SESSION['account_id'] ?? null;?>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>E-Barangay Report</title>
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
  <link rel="stylesheet" href="../../css/admin/adminPortal.css">
  <link rel="stylesheet" href="../../css/resident/residentTable.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script src="../../js/main.js" defer></script>
  <script src="../../js/add-modal.js" defer></script>
  <script src="../../js/dashBoard.js" defer></script>
  <script src="../../js/update-modal.js" defer></script>
  <script src="https://kit.fontawesome.com/4907458c0c.js" crossorigin="anonymous"></script>
  <script src="../../js/residentBootstrap.js" defer></script>
  <script src="../../js/view-modal.js" defer></script>
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
      
      .modal {
        margin-top: 350px;
        max-width: 750px;
        width: 100%;
      }

      .modal-add {
        margin-top: 350px;
        max-width: 750px;
        width: 100%;
      }

      .modal-view {
        margin-top: 500px;
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
          <a href="residentDashboardController.php" class="nav-item" data-tooltip="Dashboard">
            <i class="fas fa-tachometer-alt nav-icon"></i>
            <span class="nav-label">Dashboard</span>
          </a>
          <a href="residentRequestController.php" class="nav-item" data-tooltip="Requests">
            <i class="fas fa-file-alt nav-icon"></i>
            <span class="nav-label">Requests</span>
          </a>
          <a href="residentReportController.php" class="nav-item active" data-tooltip="Reports">
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
              <?php echo '<h3>' . ($_SESSION['username']) . '</h3>' ?>
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
      <div class="content">
        <div class="table-container">
          <table class="table">
            <thead>
              <tr>
                <td colspan="13" class="search-column">
                  <div class="search-bar">
                    <p>Search</p>
                    <input type="text" placeholder="Search Reports" id="searchInput">
                    <button class="modal-open-add button" id="add-resident">Add Report +</button>
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
                <th>Remarks</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php
              if (!empty($reports)) {
                $groupedReports = [];
                foreach ($reports as $report) {
                  $case_id = $report['case_id'];
                  if (!isset($groupedReports[$case_id])) {
                    $groupedReports[$case_id] = [
                      'case_info' => $report,
                      'images' => []
                    ];
                  }
                  if (!empty($report['evidence_picture'])) {
                    $groupedReports[$case_id]['images'][] = $report['evidence_picture'];
                  }
                }
                foreach ($groupedReports as $groupedReport) {
                  $report = $groupedReport['case_info'];
                  echo "<tr>";
                  echo "<td>" . htmlspecialchars($report['case_id']) . "</td>";
                  echo "<td>" . htmlspecialchars($report['complainant']) . "</td>";
                  echo "<td>" . htmlspecialchars($report['subject']) . "</td>";
                  echo "<td>" . htmlspecialchars($report['date_of_incident']) . "</td>";
                  echo "<td>" . date("g:i A", strtotime(htmlspecialchars($report['time_of_incident']))) . "</td>";
                  echo "<td>" . htmlspecialchars($report['location']) . "</td>";
                  echo "<td>" . htmlspecialchars($report['person_involved']) . "</td>";
                  echo "<td>";
                  if (strlen($report['narration']) > 19) {
                    echo htmlspecialchars(substr($report['narration'], 0, 19)) . "...";
                  } else {
                    echo htmlspecialchars($report['narration']);
                  }
                  echo "</td>";
                  echo "<td>";
                  if (empty($report['evidence_description'])) {
                    echo "N/A";
                  } else {
                    echo htmlspecialchars($report['evidence_description']);
                  }
                  echo "</td>";
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
                  echo "<td>";
                  if (empty($report['remarks'])) {
                      echo "N/A";
                  } elseif (strlen($report['remarks']) > 16) {
                      echo htmlspecialchars(substr($report['remarks'], 0, 16)) . "...";
                  } else {
                      echo htmlspecialchars($report['remarks']);
                  }
                  echo "</td>";
                  $statusColor = ($report['status'] === 'resolved') ? 'green' : 
                                 (($report['status'] === 'pending') ? 'orange' : 
                                 (($report['status'] === 'closed') ? 'red' : 'gray'));
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
                      . htmlspecialchars($report['status']) . "</span></td>";
                  $evidenceJson = json_encode($groupedReport['images']);
                  echo "<td>";
                  // View Button for Closed or Resolved
                  if (strtolower($report['status']) === 'closed' || strtolower($report['status']) === 'resolved' || str_starts_with($report['case_id'], '125')) {
                    echo "<button class='modal-open-view button' aria-haspopup='true' style='background-color:rgb(92, 92, 92); margin-bottom: 10px;' 
                            data-case-id='" . htmlspecialchars($report['case_id']) . "' 
                            data-complainant='" . htmlspecialchars($report['complainant']) . "' 
                            data-subject='" . htmlspecialchars($report['subject']) . "' 
                            data-date-of-incident='" . htmlspecialchars($report['date_of_incident']) . "' 
                            data-time-of-incident='" . date("g:i A", strtotime(htmlspecialchars($report['time_of_incident']))) . "' 
                            data-location='" . htmlspecialchars($report['location']) . "' 
                            data-person-involved='" . htmlspecialchars($report['person_involved']) . "' 
                            data-narration='" . htmlspecialchars($report['narration']) . "' 
                            data-status='" . htmlspecialchars($report['status']) . "' 
                            data-evidence-description='" . htmlspecialchars($report['evidence_description']) . "' 
                            data-evidence='" . htmlspecialchars($evidenceJson) . "' 
                            data-remarks='" . htmlspecialchars($report['remarks'] ?? '') . "' 
                            onclick='populateViewModal(this)'>
                            <i class='fa-solid fa-eye'></i>
                          </button>";
                  }
                  // Edit Button for Pending
                  if (strtolower($report['status']) === 'pending' && !str_starts_with($report['case_id'], '125')) {
                    echo "<button class='modal-open button' aria-haspopup='true' 
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
                            data-remarks='" . htmlspecialchars($report['remarks'] ?? '') . "' 
                            onclick='populateBlotterModal(this)'>
                            <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-pen' viewBox='0 0 16 16'>
                              <path d='m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001m-.644.766a.5.5 0 0 0-.707 0L1.95 11.756l-.764 3.057 3.057-.764L14.44 3.854a.5.5 0 0 0 0-.708z'/>
                            </svg>
                          </button>";
                  }
                  echo "</td>";
                  echo "</tr>";
                }
              } else {
                echo "<tr><td colspan='13'>No report records found.</td></tr>";
              }
              ?>
            </tbody>
            <tfoot>
              <tr>
                <th colspan="13" id="paginationColspan">
                  <?php if ($showPagination) { ?>
                    <div class="pagination" style="display: flex; justify-content: center; align-items: center; margin-top: 20px;">
                      <?php if ($page > 1) { ?>
                        <a href="?page=<?php echo $page - 1; ?>" class="button" style="padding: 10px 15px; border: none; background-color: #007bff; color: white; cursor: pointer; margin-right: 10px; border-radius: 5px;">Previous</a>
                      <?php } else { ?>
                        <span class="button disabled" style="padding: 10px 15px; border: none; background-color: grey; color: white; cursor: not-allowed; margin-right: 10px; border-radius: 5px;">Previous</span>
                      <?php } ?>
                      <span id="currentPage" style="font-size: 16px; font-weight: bold; margin: 0 10px;">
                        Page <?php echo $page; ?> of <?php echo $totalPages; ?>
                      </span>
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
      <!-- Add Report Modal -->
      <div class="modal-overlay-add">
        <div class="modal-add" id="accessible-modal-add" role="dialog" aria-modal="true" aria-labelledby="modal-title-add" aria-describedby="modal-description-add">
          <div class="modal-header-add">
            <h2 id="modal-title-add">Add Report</h2>
            <button class="modal-close-add" aria-label="Close modal">
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-content-add">
            <form id="addBlotterForm-add" action="residentReportController.php" method="POST" enctype="multipart/form-data">
              <input type="hidden" id="account-id-add" name="account_id" value="<?= $_SESSION['account_id'] ?>">
                <input type="hidden" id="resident-id-add" name="complainant" value="<?= $_SESSION['first_name'] . ' ' . ($_SESSION['middle_name'] ?? '') . ' ' . $_SESSION['last_name'] . ($_SESSION['suffix'] ? ' ' . $_SESSION['suffix'] : '') ?>">
              <div class="form-group-add">
                <label for="type-of-report-add">Type of Report (Uri ng Ulat)</label>
                <select id="type-of-report-add" name="type_of_report" required>
                  <option value="" disabled selected>Select Report Type</option>
                  <option value="complaint">Complaint</option>
                  <option value="incident">Incident</option>
                </select>
              </div>
              <div class="form-group-add">
                <label for="subject-add">Subject (Paksa)</label>
                <input type="text" id="subject-add" name="subject" placeholder="Enter subject of Report" required maxlength="50">
              </div>
              <div class="form-group-add">
                <label for="incident-date-add">Date of Incident (Petsa ng Insident) </label>
                <input type="date" id="incident-date-add" name="date_reported" max="<?php echo date('Y-m-d'); ?>" required>
              </div>
              <div class="form-group-add">
                <label for="incident-time-add">Time of Incident (Oras ng Insident)</label>
                <input type="time" id="incident-time-add" name="time_reported" required>
              </div>
              <div class="form-group-add">
                <label for="location-add">Location (Lokasyon san naganap)</label>
                <input type="text" id="location-add" name="location" value="<?php  ?>" placeholder="Enter location of incident" required maxlength="50">
              </div>
              <div class="form-group-add">
                <label for="respondent-add">Persons Involved (Mga Taong Sangkot)</label>
                <input type="text" id="respondent-add" name="respondent" placeholder="Enter person's involved name" required maxlength="50">
              </div>
              <div class="form-group-add">
                <label for="narration-add">Narration (Salaysay)</label>
                <textarea id="narration-add" name="narration" rows="5" placeholder="Describe the incident" required maxlength="200" style="width: 99%; height: 100px; resize: none;"></textarea>
              </div>
              <div class="form-group-add">
                <label for="evidence-pictures-add">
                  Evidence Pictures <p style="color: gray; display: inline;">(You can upload up to 5 images with a maximum total size of 40 MB)</p> <p style="color: red; display: inline;">Note: Editing or deleting the evidence after submitting the form is not allowed. <br>Paalala: Hindi pinapayagan ang pag-edit o pagbura ng ebidensya pagkatapos isumite ang form."</p>
                  <p id="file-error-message" style="color:red; display:none;"></p>
                </label>
                <input type="file" id="evidence-pictures-add" name="evidence_picture[]" accept="image/*" multiple onchange="validateFileSize()" data-min-files="1" data-max-files="5">
                <div id="selected-files"></div>
              </div>
              <div class="form-group-add">
                <label for="evidence-description-add">Evidence Description (Deskripsyon ng Ebidensya)</label>
                <input type="text" id="evidence-description-add" name="evidence_description" placeholder="Enter evidence title" maxlength="200" required>
              </div>
              <input type="submit" class="button-add" value="Add Report">
            </form>
          </div>
        </div>
      </div>
      <!-- Update Report Modal -->
      <div class="modal-overlay">
        <div class="modal" id="accessible-modal" role="dialog" aria-modal="true" aria-labelledby="modal-title" aria-describedby="modal-description">
          <div class="modal-header">
            <h2 id="modal-title">Update Report</h2>
            <button class="modal-close" aria-label="Close modal">
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-content">
            <form id="edit-blotter-form" action="residentReportUpdate.php" method="POST" enctype="multipart/form-data">
              <input type="hidden" id="edit-case-id" name="case_id">
              <input type="hidden" id="account-id-add" name="account_id" value="<?= $_SESSION['account_id'] ?>">
              <div class="form-group">
                <label for="edit-complainant">Complainant (Pangalan ng nag Ulat)</label>
                <input type="text" id="edit-complainant" name="complainant" readonly>
              </div>
              <div class="form-group">
                <label for="edit-subject">Subject (Paksa)</label>
                <input type="text" id="edit-subject" name="subject" required>
              </div>
              <div class="form-group">
                <label for="edit-date-of-incident">Date of Incident (Petsa ng Insidente)</label>
                <input type="date" id="edit-date-of-incident" name="date_reported" required>
              </div>
              <div class="form-group">
                <label for="edit-time-of-incident">Time of Incident (Oras ng Insidente)</label>
                <input type="time" id="edit-time-of-incident" name="time_reported" required>
              </div>
              <div class="form-group">
                <label for="edit-location">Location (Lokasyon san naganap)</label>
                <input type="text" id="edit-location" name="location" required>
              </div>
              <div class="form-group">
                <label for="edit-person-involved">Persons Involved (Mga Taong Sangkot)</label>
                <input type="text" id="edit-person-involved" name="respondent" required>
              </div>
              <div class="form-group">
                <label for="edit-narration">Narration (Salaysay)</label>
                <textarea id="edit-narration" name="narration" style="width: 99%; height: 100px; resize: none;" required></textarea>
              </div>
              <div class="form-group">
                <label for="edit-status">Status (Katayuan)</label>
                <input type="text" id="edit-status" name="status" placeholder="Enter status" readonly>
              </div>
              <div class="form-group">
                <label for="edit-evidence-description">Evidence Description (Deskripsyon ng Ebidensya) <p style="color: gray; display: inline;">(Not editable)<p></label>
                <input type="text" id="edit-evidence-description" name="evidence_description" placeholder="N/A" maxlength="200" readonly>
              </div>
              <div class="form-group">
                <label for="edit-evidence">Existing Evidence Images (Ebidensya) <p style="color: gray; display: inline;">(Not editable)<p></label>
                <div id="existing-evidence"></div>
              </div>
              <input class="button" type="submit" value="Update Report">
            </form>
          </div>
        </div>
      </div>
      <!-- View Report Modal -->
      <div class="modal-overlay-view">
        <div class="modal-view" id="accessible-modal-view" role="dialog" aria-modal="true" aria-labelledby="modal-title-view" aria-describedby="modal-description-view">
          <div class="modal-header-view">
            <h2 id="modal-title-view">View Report Details</h2>
            <button class="modal-close-view" aria-label="Close modal">
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-content-view">
            <div class="form-group-view">
              <label for="view-case-id">Case Number</label>
              <input type="text" id="view-case-id" readonly>
            </div>
            <div class="form-group-view">
              <label for="view-complainant">Complainant (Pangalan ng nag Ulat)</label>
              <input type="text" id="view-complainant" readonly>
            </div>
            <div class="form-group-view">
              <label for="view-subject">Subject (Paksa)</label>
              <input type="text" id="view-subject" readonly>
            </div>
            <div class="form-group-view">
              <label for="view-date-of-incident">Date of Incident (Petsa ng Insidente)</label>
              <input type="text" id="view-date-of-incident" readonly>
            </div>
            <div class="form-group-view">
              <label for="view-time-of-incident">Time of Incident (Oras ng Insidente)</label>
              <input type="text" id="view-time-of-incident" readonly>
            </div>
            <div class="form-group-view">
              <label for="view-location">Location (Lokasyon san naganap)</label>
              <input type="text" id="view-location" readonly>
            </div>
            <div class="form-group-view">
              <label for="view-person-involved">Persons Involved (Mga Taong Sangkot)</label>
              <input type="text" id="view-person-involved" readonly>
            </div>
            <div class="form-group-view">
              <label for="view-narration">Narration (Salaysay)</label>
              <textarea id="view-narration" rows="5" readonly style="resize: none; width: 99%;"></textarea>
            </div>
            <div class="form-group-view">
              <label for="view-evidence-description">Evidence Description (Deskripsyon ng Ebidensya)</label>
              <input type="text" id="view-evidence-description" placeholder="N/A" readonly>
            </div>
            <div class="form-group-view">
              <label for="view-evidence">Evidence Images (Ebidensya)</label>
              <div id="view-evidence"></div>
            </div>
            <div class="form-group-view">
              <label for="view-status">Status</label>
              <input type="text" id="view-status" readonly>
            </div>
            <div class="form-group-view">
              <label for="view-remarks">Remarks</label>
              <textarea id="view-remarks" rows="4" readonly style="resize: none; width: 99%;"></textarea>
            </div>
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
            let query = $(this).val();
            console.log("Search Query:", query);

            $.ajax({
                url: 'residentReportSearch.php',
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
                        let caseId = $(this).data('case-id');
                        console.log("Clicked Edit Case ID: " + caseId);
                        $('.modal-overlay').addClass('is-active');
                        populateBlotterModal(this);
                    });

                    // Rebind view modal buttons
                    $('.modal-open-view').off('click').on('click', function() {
                        let caseId = $(this).data('case-id');
                        console.log("Clicked View Case ID: " + caseId);
                        $('.modal-overlay-view').addClass('is-active-view');
                        populateViewModal(this);
                    });
                },
                error: function(request, error) {
                    console.error("Search Error:", error);
                }
            });
        });

        // Initial binding for edit and view modal buttons
        $('.modal-open').on('click', function() {
            let caseId = $(this).data('case-id');
            console.log("Initial Clicked Edit Case ID: " + caseId);
            $('.modal-overlay').addClass('is-active');
            populateBlotterModal(this);
        });

        $('.modal-open-view').on('click', function() {
            let caseId = $(this).data('case-id');
            console.log("Initial Clicked View Case ID: " + caseId);
            $('.modal-overlay-view').addClass('is-active-view');
            populateViewModal(this);
        });
    });

    function populateBlotterModal(button) {
      console.log("Populating edit modal");
      const caseId = button.getAttribute('data-case-id');
      const complainant = button.getAttribute('data-complainant');
      const subject = button.getAttribute('data-subject');
      const dateOfIncident = button.getAttribute('data-date-of-incident');
      const timeOfIncident = button.getAttribute('data-time-of-incident');
      const location = button.getAttribute('data-location');
      const personInvolved = button.getAttribute('data-person-involved');
      const narration = button.getAttribute('data-narration');
      const status = button.getAttribute('data-status');
      const evidenceDescription = button.getAttribute('data-evidence-description');
      const evidence = JSON.parse(button.getAttribute('data-evidence'));
      const remarks = button.getAttribute('data-remarks') || 'No remarks provided';
      
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
      const existingEvidenceContainer = document.getElementById('existing-evidence');
      existingEvidenceContainer.innerHTML = '';
      if (evidence && evidence.length > 0) {
        evidence.forEach(imagePath => {
          const linkElement = document.createElement('a');
          linkElement.href = imagePath;
          linkElement.target = '_blank';
          const imgElement = document.createElement('img');
          imgElement.src = imagePath;
          imgElement.alt = 'Evidence Image';
          imgElement.style.width = '100px';
          imgElement.style.height = '100px';
          imgElement.style.marginRight = '10px';
          imgElement.style.marginBottom = '10px';
          linkElement.appendChild(imgElement);
          existingEvidenceContainer.appendChild(linkElement);
        });
      } else {
        existingEvidenceContainer.innerHTML = '<p>No evidence images available</p>';
      }
      console.log("Edit modal populated with case ID:", caseId);
    }

    function populateViewModal(button) {
      console.log("Populating view modal");
      const caseId = button.getAttribute('data-case-id');
      const complainant = button.getAttribute('data-complainant');
      const subject = button.getAttribute('data-subject');
      const dateOfIncident = button.getAttribute('data-date-of-incident');
      const timeOfIncident = button.getAttribute('data-time-of-incident');
      const location = button.getAttribute('data-location');
      const personInvolved = button.getAttribute('data-person-involved');
      const narration = button.getAttribute('data-narration');
      const status = button.getAttribute('data-status');
      const evidenceDescription = button.getAttribute('data-evidence-description');
      const evidence = JSON.parse(button.getAttribute('data-evidence'));
      const remarks = button.getAttribute('data-remarks') || 'No remarks provided';

      document.getElementById('view-case-id').value = caseId;
      document.getElementById('view-complainant').value = complainant;
      document.getElementById('view-subject').value = subject;
      document.getElementById('view-date-of-incident').value = dateOfIncident;
      document.getElementById('view-time-of-incident').value = timeOfIncident;
      document.getElementById('view-location').value = location;
      document.getElementById('view-person-involved').value = personInvolved;
      document.getElementById('view-narration').value = narration;
      document.getElementById('view-evidence-description').value = evidenceDescription;
      document.getElementById('view-status').value = status;
      document.getElementById('view-remarks').value = remarks;
      const viewEvidenceContainer = document.getElementById('view-evidence');
      viewEvidenceContainer.innerHTML = '';
      if (evidence && evidence.length > 0) {
        evidence.forEach(imagePath => {
          const linkElement = document.createElement('a');
          linkElement.href = imagePath;
          linkElement.target = '_blank';
          const imgElement = document.createElement('img');
          imgElement.src = imagePath;
          imgElement.alt = 'Evidence Image';
          imgElement.style.width = '100px';
          imgElement.style.height = '100px';
          imgElement.style.marginRight = '10px';
          imgElement.style.marginBottom = '10px';
          linkElement.appendChild(imgElement);
          viewEvidenceContainer.appendChild(linkElement);
        });
      } else {
        viewEvidenceContainer.innerHTML = '<p>No evidence images available</p>';
      }
      console.log("View modal populated with case ID:", caseId);
    }

    // Validate evidence pictures file size
    function validateFileSize() {
      const fileInput = document.getElementById('evidence-pictures-add');
      const files = fileInput.files;
      const maxTotalSize = 40 * 1024 * 1024; // 40 MB
      let totalSize = 0;

      for (let i = 0; i < files.length; i++) {
        totalSize += files[i].size;
      }

      const errorMessage = document.getElementById('file-error-message');
      if (totalSize > maxTotalSize) {
        errorMessage.style.display = 'block';
        errorMessage.textContent = 'Total file size exceeds 40 MB. Please select smaller files.';
        fileInput.value = '';
        document.getElementById('selected-files').innerHTML = '';
      } else if (files.length > 5) {
        errorMessage.style.display = 'block';
        errorMessage.textContent = 'You can only upload up to 5 images.';
        fileInput.value = '';
        document.getElementById('selected-files').innerHTML = '';
      } else {
        errorMessage.style.display = 'none';
        // Display selected file names
        const fileList = document.getElementById('selected-files');
        fileList.innerHTML = '';
        for (let i = 0; i < files.length; i++) {
          const fileName = document.createElement('p');
          fileName.textContent = files[i].name;
          fileList.appendChild(fileName);
        }
      }
    }

    // Clear form inputs when Add modal is closed
    document.querySelector('.modal-close-add').addEventListener('click', function() {
      document.getElementById('addBlotterForm-add').reset();
      document.getElementById('file-error-message').style.display = 'none';
      document.getElementById('selected-files').innerHTML = '';
      localStorage.removeItem('blotterFormData');
    });

    document.querySelector('.modal-overlay-add').addEventListener('click', function(event) {
      if (event.target === this) {
        document.getElementById('addBlotterForm-add').reset();
        document.getElementById('file-error-message').style.display = 'none';
        document.getElementById('selected-files').innerHTML = '';
        localStorage.removeItem('blotterFormData');
      }
    });


  </script>
</body>
</html>