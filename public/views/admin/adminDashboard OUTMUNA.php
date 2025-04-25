<!DOCTYPE html>
<html lang="en">

<head>
  <?php
$residentViewProfileModel = new ResidentViewProfileModel();
$personalAccount = $residentViewProfileModel->showPersonalAccount($conn, $_SESSION['account_id']);
$accountId = $_SESSION['account_id'] ?? null;
  ?>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>E-Barangay Report</title>
  <meta name="sidebar-collapsed" content="false" id="sidebar-meta">
  <script>
    document.documentElement.classList.add('no-transitions');
    window.addEventListener('load', function () {
      setTimeout(function () {
        document.documentElement.classList.remove('no-transitions');
      }, 100);
    });
  </script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../css/admin/officialAdminBootstrap.css">
  <link rel="stylesheet" href="../../css/admin/adminPortal.css">
  <link rel="stylesheet" href="../../css/main.css">
</head>

<body>
  <div class="app-container">
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
          <a href="adminNavigationDashboard.html" class="nav-item" data-tooltip="Dashboard">
            <i class="fas fa-tachometer-alt nav-icon"></i>
            <span class="nav-label">Dashboard</span>
          </a>

          <div class="nav-item accordion" data-tooltip="People">
            <div class="accordion-header">
              <i class="fas fa-users nav-icon"></i>
              <span class="nav-label">People</span>
              <i class="fas fa-chevron-down accordion-icon"></i>
            </div>
            <div class="accordion-content">
              <a href="adminResidentController.php" class="accordion-item">Resident</a>
              <a href="adminNavigationbarOfficial.html" class="accordion-item">Official</a>
              <a href="adminNavigationbarFamily.html" class="accordion-item">Family</a>
            </div>
          </div>

          <div class="nav-item accordion" data-tooltip="Accounts">
            <div class="accordion-header">
              <i class="fas fa-user-circle nav-icon"></i>
              <span class="nav-label">Accounts</span>
              <i class="fas fa-chevron-down accordion-icon"></i>
            </div>
            <div class="accordion-content">
              <a href="adminNavigationbarResidentAccount.html" class="accordion-item">Resident Account</a>
              <a href="adminNavigationbarOfficialAccount.html" class="accordion-item">Official Account</a>
            </div>
          </div>

          <div class="nav-item accordion" data-tooltip="Reports">
            <div class="accordion-header">
              <i class="fas fa-file-alt nav-icon"></i>
              <span class="nav-label">Reports</span>
              <i class="fas fa-chevron-down accordion-icon"></i>
            </div>
            <div class="accordion-content">
              <a href="adminNavigationbarBlotter.html" class="accordion-item">Blotter</a>
              <a href="adminNavigationbarComplaint.html" class="accordion-item">Complaint</a>
              <a href="adminNavigationbarIncident.html" class="accordion-item">Incident</a>
            </div>
          </div>

          <a href="/announcements" class="nav-item" data-tooltip="Announcements">
            <i class="fas fa-bell nav-icon"></i>
            <span class="nav-label">Announcements</span>
          </a>
          <a href="/settings" class="nav-item" data-tooltip="Settings">
            <i class="fas fa-cog nav-icon"></i>
            <span class="nav-label">Settings</span>
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
            <p class="user-name">Juan Dela Cruz</p>
            <p class="user-role">Resident</p>
          </div>
        </div>
      </div>
    </div>

    <div class="main-content" id="main-content">
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
                <a href="residentViewProfileController.php">View Profile</a>
                <a href="../logoutController.php">Log Out</a>
              </div>
            </div>
          </nav>
        </section>

      <button class="mobile-toggle-btn" id="mobile-toggle">
        <i class="fas fa-bars"></i> <span class="menu-text">Menu</span>
      </button>


            <!-- DASHBOARD -->
            <div class="content">
                <div class="dashboard-title">
                    <div class="title-wrapper">
                        <h2 class="header">Barangay Official Dashboard</h2>
                    </div>
                    <hr class="style-six">
                </div>
                <div class="dashboard-header">
                    <div class="overlay-text">
                        <h1>Hello There,
                            <?php
                            if (isset($_SESSION['first_name']) && isset($_SESSION['last_name'])) {
                                echo htmlspecialchars($_SESSION['first_name']) . " " . htmlspecialchars($_SESSION['last_name']);
                            } else {
                                echo "Guest!";
                            } ?>
                            !
                        </h1>
                    </div>
                    <div class="weather-info">
                        <div class="temp-city">
                            <h2 class="temp">22°c</h2>
                            <p class="city"></p>
                        </div>
                        <div class="temp-city">
                            <i class="weather-icon fa-solid fa-cloud-rain"></i>
                        </div>
                    </div>
                </div>

                <div class="dashboard-body">
                    <div class="section-container">
                        <div class="card-container">

                            <h2>RESIDENTS DATA</h2>
                            <div class="card-wrapper" id="resident-table">
                                <!-- Total Residents -->
                                <div class="card card-residents" onclick="this.classList.toggle('flipped')">
                                    <div class="card-front">
                                        <div class="card-text">
                                            <h3><?php echo "$totalResidents"; ?></h3>
                                            <p>Total Residents <br> &nbsp;</p>
                                        </div>
                                        <div class="card-logo">
                                            <i class="fa-solid fa-users faIconSize"></i>
                                        </div>
                                    </div>
                                    <div class="card-back">
                                        <div class="card-text">
                                            <h4>Total Residents</h4>

                                            <!-- PRINT BUTTONS -->
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <td colspan="19" class="search-column" style="background-color: #1a4b8c; border:#1a4b8c">
                                                            <div class="search-bar">
                                                                <button class="button" style="background-color: red;" onclick="window.open('export/dashboardSummaryExport/residentsDataSummaries/totalResidentPDFExport.php', '_blank')">Print Total Residents PDF</button>
                                                                <button class="button" style="background-color: green;" onclick="window.open('export/dashboardSummaryExport/residentsDataSummaries/totalResidentExcelExport.php', '_blank')">Print Total Residents Excel</button>
                                                            </div>
                                                        </td>
                                            </table>

                                        </div>
                                    </div>
                                </div>

                                <!-- New Residents -->
                                <div class="card" onclick="this.classList.toggle('flipped')">
                                    <div class="card-front">
                                        <div class="card-text">
                                            <h3><?php echo $newResidents ?></h3>
                                            <p>New Residents</p>
                                        </div>
                                        <div class="card-logo">
                                            <i class="fa-solid fa-user-plus faIconSize"></i>
                                        </div>
                                    </div>
                                    <div class="card-back">
                                        <div class="card-text">
                                            <h4>New Residents</h4>

                                            <!-- PRINT BUTTONS -->
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <td colspan="19" class="search-column" style="background-color: #1a4b8c; border:#1a4b8c">
                                                            <div class="search-bar">
                                                                <button class="button" style="background-color: red;" onclick="window.open('export/dashboardSummaryExport/residentsDataSummaries/newResidentPDFExport.php', '_blank')">Print New Residents PDF</button>
                                                                <button class="button" style="background-color: green;" onclick="window.open('export/dashboardSummaryExport/residentsDataSummaries/newResidentsExcelExport.php', '_blank')">Print New Residents Excel</button>
                                                            </div>
                                                        </td>
                                            </table>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-wrapper" id="resident-table">
                                <!-- Senior Citizens -->
                                <div class="card card-reports" onclick="this.classList.toggle('flipped')">
                                    <div class="card-front">
                                        <div class="card-text">
                                            <h3><?php echo $seniorCitizens; ?></h3>
                                            <p>Senior Citizens</p>
                                        </div>
                                        <div class="card-logo">
                                            <i class="fa-solid fa-person-cane faIconSize"></i>
                                        </div>
                                    </div>
                                    <div class="card-back">
                                        <div class="card-text">
                                            <h4>Senior Citizens</h4>

                                            <!-- PRINT BUTTONS -->
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <td colspan="19" class="search-column" style="background-color: #1a4b8c; border:#1a4b8c">
                                                            <div class="search-bar">
                                                                <button class="button" style="background-color: red;" onclick="window.open('export/dashboardSummaryExport/residentsDataSummaries/seniorCitizensPDFExport.php', '_blank')">Print Senior Citizens PDF</button>
                                                                <button class="button" style="background-color: green;" onclick="window.open('export/dashboardSummaryExport/residentsDataSummaries/seniorCitizensExcelExport.php', '_blank')">Print Senior Citizens Excel</button>
                                                            </div>
                                                        </td>
                                            </table>

                                        </div>
                                    </div>
                                </div>

                                <!-- Total Families -->
                                <div class="card" onclick="this.classList.toggle('flipped')">
                                    <div class="card-front">
                                        <div class="card-text">
                                            <h3><?php echo $totalFamilies; ?></h3>
                                            <p>Total Families <br> &nbsp;</p>
                                        </div>
                                        <div class="card-logo">
                                            <i class="fa-solid fa-people-roof faIconSize"></i>
                                        </div>
                                    </div>
                                    <div class="card-back">
                                        <div class="card-text">
                                            <h4>Total Families</h4>

                                            <!-- PRINT BUTTONS -->
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <td colspan="19" class="search-column" style="background-color: #1a4b8c; border:#1a4b8c">
                                                            <div class="search-bar">
                                                                <button class="button" style="background-color: red;" onclick="window.open('export/dashboardSummaryExport/residentsDataSummaries/familyPDFExport.php', '_blank')">Print Total Families PDF</button>
                                                                <button class="button" style="background-color: green;" onclick="window.open('export/dashboardSummaryExport/residentsDataSummaries/familyExcelExport.php', '_blank')">Print Total Families Excel</button>
                                                            </div>
                                                        </td>
                                            </table>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-container">
                            <h2>REPORTS</h2>
                            <div class="card-wrapper" id="resident-table">

                                <!-- Incidents Today -->
                                <div class="card" onclick="this.classList.toggle('flipped')">
                                    <div class="card-front">
                                        <div class="card-text">
                                            <h3><?php echo $incidentsToday; ?></h3>
                                            <p>Incidents Reported Today <br> &nbsp;</p>
                                        </div>
                                        <div class="card-logo">
                                            <i class="fa-solid fa-clipboard-list faIconSize"></i>
                                        </div>
                                    </div>
                                    <div class="card-back">
                                        <div class="card-text">
                                            <h4>Incidents Today</h4>

                                            <!-- PRINT BUTTONS -->
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <td colspan="19" class="search-column" style="background-color: #1a4b8c; border:#1a4b8c">
                                                            <div class="search-bar">
                                                                <button class="button" style="background-color: red;" onclick="window.open('export/dashboardSummaryExport/reportsDataSummaries/incidentsReportedTodayPDFExport.php', '_blank')">Print Incidents Today PDF</button>
                                                                <button class="button" style="background-color: green;" onclick="window.open('export/dashboardSummaryExport/reportsDataSummaries/incidentsReportedTodayExcelExport.php', '_blank')">Print Incidents Today Excel</button>
                                                            </div>
                                                        </td>
                                            </table>

                                        </div>
                                    </div>
                                </div>

                                <!-- Blotters Today -->
                                <div class="card" onclick="this.classList.toggle('flipped')">
                                    <div class="card-front">
                                        <div class="card-text">
                                            <h3><?php echo $blottersToday; ?></h3>
                                            <p>Blotters Reported Today</p>
                                        </div>
                                        <div class="card-logo">
                                            <i class="fa-solid fa-file-contract faIconSize"></i>
                                        </div>
                                    </div>
                                    <div class="card-back">
                                        <div class="card-text">
                                            <h4>Blotters Today</h4>

                                            <!-- PRINT BUTTONS -->
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <td colspan="19" class="search-column" style="background-color: #1a4b8c; border:#1a4b8c">
                                                            <div class="search-bar">
                                                                <button class="button" style="background-color: red;" onclick="window.open('export/dashboardSummaryExport/reportsDataSummaries/blottersReportedTodayPDFExport.php', '_blank')">Print Blotters Today PDF</button>
                                                                <button class="button" style="background-color: green;" onclick="window.open('export/dashboardSummaryExport/reportsDataSummaries/blottersReportedTodayExcelExport.php', '_blank')">Print Blotters Today Excel</button>
                                                            </div>
                                                        </td>
                                            </table>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-wrapper" id="resident-table">

                                <!-- Complaints Reported Today  -->
                                <div class="card" onclick="this.classList.toggle('flipped')">
                                    <div class="card-front">
                                        <div class="card-text">
                                            <h3><?php echo $complaintsToday ?></h3>
                                            <p>Complaints Reported Today </p>
                                        </div>
                                        <div class="card-logo">
                                            <i class="fa-solid fa-triangle-exclamation faIconSize"></i>
                                        </div>
                                    </div>
                                    <div class="card-back">
                                        <div class="card-text">
                                            <h4>Complaints Reported Today </h4>

                                            <!-- PRINT BUTTONS -->
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <td colspan="19" class="search-column" style="background-color: #1a4b8c; border:#1a4b8c">
                                                            <div class="search-bar">
                                                                <button class="button" style="background-color: red;" onclick="window.open('export/dashboardSummaryExport/reportsDataSummaries/complaintsReportedTodayPDFExport.php', '_blank')">Print Complaints Reported Today PDF</button>
                                                                <button class="button" style="background-color: green;" onclick="window.open('export/dashboardSummaryExport/reportsDataSummaries/complaintsReportedTodayExcelExport.php', '_blank')">Print Complaints Reported Today Excel</button>
                                                            </div>
                                                        </td>
                                            </table>

                                        </div>
                                    </div>
                                </div>

                                <!-- Total Pending Reports Today -->
                                <div class="card" onclick="this.classList.toggle('flipped')">
                                    <div class="card-front">
                                        <div class="card-text">
                                            <h3><?php echo $totalPendingReportsToday; ?></h3>
                                            <p>Total Pending Reports Today <br> &nbsp;</p>
                                        </div>
                                        <div class="card-logo">
                                            <i class="fa-solid fa-file-invoice faIconSize"></i>
                                        </div>
                                    </div>
                                    <div class="card-back">
                                        <div class="card-text">
                                            <h4>Total Pending Reports Today</h4>

                                            <!-- PRINT BUTTONS -->
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <td colspan="19" class="search-column" style="background-color: #1a4b8c; border:#1a4b8c">
                                                            <div class="search-bar">
                                                                <button class="button" style="background-color: red;" onclick="window.open('export/dashboardSummaryExport/reportsDataSummaries/totalPendingReportsTodayPDFExport.php', '_blank')">Print Total Pending Reports Today PDF</button>
                                                                <button class="button" style="background-color: green;" onclick="window.open('export/dashboardSummaryExport/reportsDataSummaries/totalPendingReportsTodayExcelExport.php', '_blank')">Print Total Pending Reports Today Excel</button>
                                                            </div>
                                                        </td>
                                            </table>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="section-container">

                        <!-- DOCUMENT REUESTS -->
                        <div class="card-container">
                            <h2>DOCUMENT REQUESTS</h2>
                            <div class="card-wrapper" id="resident-table">

                                <!-- Requests Today -->
                                <div class="card card-requests" onclick="this.classList.toggle('flipped')">
                                    <div class="card-front">
                                        <div class="card-text">
                                            <h3><?php echo $permitsToday; ?></h3>
                                            <p>Permits Requested Today <br> &nbsp;</p>
                                        </div>
                                        <div class="card-logo">
                                            <i class="fa-solid fa-file-signature faIconSize-1-25"></i>
                                        </div>  
                                    </div>
                                    <div class="card-back">
                                        <div class="card-text">
                                            <h4>Permits Requested Today</h4>

                                            <!-- PRINT BUTTONS -->
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <td colspan="19" class="search-column" style="background-color: #1a4b8c; border:#1a4b8c">
                                                            <div class="search-bar">
                                                                <button class="button" style="background-color: red;" onclick="window.open('export/dashboardSummaryExport/documentRequestsSummaries/newPermitsTodayPDFExport.php', '_blank')">Print Permits Requested Today PDF</button>
                                                                <button class="button" style="background-color: green;" onclick="window.open('export/dashboardSummaryExport/documentRequestsSummaries/newPermitsTodayExcelExport.php', '_blank')">Print Permits Requested Today Excel</button>
                                                            </div>
                                                        </td>
                                            </table>

                                        </div>
                                    </div>
                                </div>

                                <!-- Pending Requests -->
                                <div class="card card-requests" onclick="this.classList.toggle('flipped')">
                                    <div class="card-front">
                                        <div class="card-text">
                                            <h3><?php echo $clearanceToday ?></h3>
                                            <p>Clearance Requested Today</p>
                                        </div>
                                        <div class="card-logo">
                                            <i class="fa-solid fa-id-card faIconSize-1-25"></i>
                                        </div>
                                    </div>
                                    <div class="card-back">
                                        <div class="card-text">
                                            <h4>Clearance Requested Today</h4>

                                            <!-- PRINT BUTTONS -->
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <td colspan="19" class="search-column" style="background-color: #1a4b8c; border:#1a4b8c">
                                                            <div class="search-bar">
                                                                <button class="button" style="background-color: red;" onclick="window.open('export/dashboardSummaryExport/documentRequestsSummaries/newClearancesTodayPDFExport.php', '_blank')">Print Clearance Requested Today PDF</button>
                                                                <button class="button" style="background-color: green;" onclick="window.open('export/dashboardSummaryExport/documentRequestsSummaries/newClearancesTodayExcelExport.php', '_blank')">Print Clearance Requested Today Excel</button>
                                                            </div>
                                                        </td>
                                            </table>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-wrapper" id="resident-table">

                                <!-- Certificate Requested  -->
                                <div class="card card-requests" onclick="this.classList.toggle('flipped')">
                                    <div class="card-front">
                                        <div class="card-text">
                                            <h3><?php echo $certificateToday ?></h3>
                                            <p>Certificate Requested Today </p>
                                        </div>
                                        <div class="card-logo">
                                            <i class="fa-solid fa-file-contract faIconSize"></i>
                                        </div>
                                    </div>
                                    <div class="card-back">
                                        <div class="card-text">
                                            <h4>Certificate Requested </h4>

                                            <!-- PRINT BUTTONS -->
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <td colspan="19" class="search-column" style="background-color: #1a4b8c; border:#1a4b8c">
                                                            <div class="search-bar">
                                                                <button class="button" style="background-color: red;" onclick="window.open('export/dashboardSummaryExport/documentRequestsSummaries/newCertificatesTodayPDFExport.php', '_blank')">Print Certificate Requested PDF</button>
                                                                <button class="button" style="background-color: green;" onclick="window.open('export/dashboardSummaryExport/documentRequestsSummaries/newCertificatesTodayExcelExport.php', '_blank')">Print Certificate Requested Excel</button>
                                                            </div>
                                                        </td>
                                            </table>

                                        </div>
                                    </div>
                                </div>

                                <!-- Total Pending Requests Today -->
                                <div class="card card-requests" onclick="this.classList.toggle('flipped')">
                                    <div class="card-front">
                                        <div class="card-text">
                                            <h3><?php echo $requestsMadeToday ?></h3>
                                            <p>Total Pending Requests Today </p>
                                        </div>
                                        <div class="card-logo">
                                            <i class="fa-solid fa-circle-exclamation faIconSize"></i>
                                        </div>
                                    </div>
                                    <div class="card-back">
                                        <div class="card-text">
                                            <h4>Total Pending Requests Today </h4>

                                            <!-- PRINT BUTTONS -->
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <td colspan="19" class="search-column" style="background-color: #1a4b8c; border:#1a4b8c;">
                                                            <div class="search-bar">
                                                                <button class="button" style="background-color: red;" onclick="window.open('export/dashboardSummaryExport/documentRequestsSummaries/requestsTodayPDFExport.php', '_blank')">Print Total Pending Requests Today PDF</button>
                                                                <button class="button" style="background-color: green;" onclick="window.open('export/dashboardSummaryExport/documentRequestsSummaries/requestsTodayExcelExport.php', '_blank')">Print Total Pending Requests Today Excel</button>
                                                            </div>
                                                        </td>
                                            </table>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="card-container">
                            <h2>ANNOUNCEMENTS</h2>
                            <div class="announcements-wrapper">
                                <?php if (!empty($announcements)): ?>
                                    <div class="announcements-list">
                                        <?php foreach ($announcements as $announcement): ?>
                                            <div class="announcement-card">
                                                <div class="announcement-top-row">
                                                    <span class="announcement-type" style="background-color: <?= $typeColors[strtolower($announcement['announcement_type'])] ?? '#6b7280' ?>">
                                                        <?= htmlspecialchars(ucfirst(strtolower($announcement['announcement_type'] ?? 'general'))) ?>
                                                    </span>
                                                    <span class="announcement-date">
                                                        <?= date('M j, Y', strtotime($announcement['date_created'])) ?>
                                                    </span>
                                                </div>

                                                <!-- Subject -->
                                                <h3 class="announcement-subject">
                                                    <?= htmlspecialchars($announcement['subject']) ?>
                                                </h3>

                                                <!-- Author with Role -->
                                                <div class="announcement-author">
                                                    <i class="fas fa-user"></i>
                                                    <span class="author-name">
                                                        <?= htmlspecialchars($announcement['author_full_name']) ?>
                                                    </span>
                                                    <span class="author-role">
                                                        (<?= htmlspecialchars($announcement['author_role']) ?>)
                                                    </span>
                                                </div>

                                                <!-- Footer with Recipient Group -->
                                                <div class="announcement-footer">
                                                    <span class="recipient-badge">
                                                        For: <?= htmlspecialchars($announcement['recipient_group']) ?>
                                                    </span>
                                                </div>
<br>

                                                <!-- Message Body -->
                                                <div class="announcement-content">
                                                    <p><?= nl2br(htmlspecialchars($announcement['message_body'])) ?></p>
                                                </div>


                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="no-announcements">
                                        <i class="fas fa-bullhorn"></i>
                                        <p>No recent announcements found</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>


                        <!-- ACCOUNTS -->
                        <div class="card-container">
                            <h2>ACCOUNTS</h2>
                            <div class="card-wrapper" id="resident-table">

                                <!-- Total Accounts -->
                                <div class="card card-requests" onclick="this.classList.toggle('flipped')">
                                    <div class="card-front">
                                        <div class="card-text">
                                            <h3><?php echo $totalAccounts; ?></h3>
                                            <p>Total Accounts <br> &nbsp;</p>
                                        </div>
                                        <div class="card-logo">
                                            <i class="fa-solid fa-users faIconSize-1-25"></i>
                                        </div>
                                    </div>
                                    <div class="card-back">
                                        <div class="card-text">
                                            <h4>Total Accounts</h4>

                                            <!-- PRINT BUTTONS -->
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <td colspan="19" class="search-column" style="background-color: #1a4b8c; border:#1a4b8c">
                                                            <div class="search-bar">
                                                                <button class="button" style="background-color: red;" onclick="window.open('export/dashboardSummaryExport/accountsDataSummaries/totalAccountsPDFExport.php', '_blank')">Print Total Accounts PDF</button>
                                                                <button class="button" style="background-color: green;" onclick="window.open('export/dashboardSummaryExport/accountsDataSummaries/totalAccountsExcelExport.php', '_blank')">Print Total Accounts Excel</button>
                                                            </div>
                                                        </td>
                                            </table>

                                        </div>
                                    </div>
                                </div>

                                <!-- New Accounts -->
                                <div class="card card-requests" onclick="this.classList.toggle('flipped')">
                                    <div class="card-front">
                                        <div class="card-text">
                                            <h3><?php echo $newAccounts ?></h3>
                                            <p>New Accounts</p>
                                        </div>
                                        <div class="card-logo">
                                            <i class="fa-solid fa-user faIconSize-1-25"></i>
                                        </div>
                                    </div>
                                    <div class="card-back">
                                        <div class="card-text">
                                            <h4>New Accounts</h4>

                                            <!-- PRINT BUTTONS -->
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <td colspan="19" class="search-column" style="background-color: #1a4b8c; border:#1a4b8c">
                                                            <div class="search-bar">
                                                                <button class="button" style="background-color: red;" onclick="window.open('export/dashboardSummaryExport/accountsDataSummaries/newAccountsPDFExport.php', '_blank')">Print New Accounts PDF</button>
                                                                <button class="button" style="background-color: green;" onclick="window.open('export/dashboardSummaryExport/accountsDataSummaries/newAccountsExcelExport.php', '_blank')">Print New Accounts Excel</button>
                                                            </div>
                                                        </td>
                                            </table>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-wrapper" id="resident-table">

                                <!-- New Pending Accounts  -->
                                <div class="card card-requests" onclick="this.classList.toggle('flipped')">
                                    <div class="card-front">
                                        <div class="card-text">
                                            <h3><?php echo $newPendingAccounts ?></h3>
                                            <p>New Pending Accounts </p>
                                        </div>
                                        <div class="card-logo">
                                            <i class="fa-solid fa-circle-exclamation faIconSize"></i>
                                        </div>
                                    </div>
                                    <div class="card-back">
                                        <div class="card-text">
                                            <h4>New Pending Accounts </h4>

                                            <!-- PRINT BUTTONS -->
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <td colspan="19" class="search-column" style="background-color: #1a4b8c; border:#1a4b8c">
                                                            <div class="search-bar">
                                                                <button class="button" style="background-color: red;" onclick="window.open('export/dashboardSummaryExport/accountsDataSummaries/newPendingAccountsPDFExport.php', '_blank')">Print New Pending Accounts PDF</button>
                                                                <button class="button" style="background-color: green;" onclick="window.open('export/dashboardSummaryExport/accountsDataSummaries/newPendingAccountsExcelExport.php', '_blank')">Print New Pending Accounts Excel</button>
                                                            </div>
                                                        </td>
                                            </table>

                                        </div>
                                    </div>
                                </div>

                                <!-- Total Pending Accounts -->
                                <div class="card card-requests" onclick="this.classList.toggle('flipped')">
                                    <div class="card-front">
                                        <div class="card-text">
                                            <h3><?php echo $totalPendingAccounts; ?></h3>
                                            <p>Total Pending Accounts <br> &nbsp;</p>
                                        </div>
                                        <div class="card-logo">
                                            <i class="fa-solid fa-people-roof faIconSize-1-25"></i>
                                        </div>
                                    </div>
                                    <div class="card-back">
                                        <div class="card-text">
                                            <h4>Total Pending Accounts</h4>

                                            <!-- PRINT BUTTONS -->
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <td colspan="19" class="search-column" style="background-color: #1a4b8c; border:#1a4b8c">
                                                            <div class="search-bar">
                                                                <button class="button" style="background-color: red;" onclick="window.open('export/dashboardSummaryExport/accountsDataSummaries/totalPendingAccountsPDFEXport.php', '_blank')">Print Total Pending Accounts PDF</button>
                                                                <button class="button" style="background-color: green;" onclick="window.open('export/dashboardSummaryExport/accountsDataSummaries/totalPendingAccountsExcelExport.php', '_blank')">Print Total Pending Accounts Excel</button>
                                                            </div>
                                                        </td>
                                            </table>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="./../../js/officialAdminBootstrap.js"></script>
</body>

</html> 