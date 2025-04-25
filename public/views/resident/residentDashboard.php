<!DOCTYPE html>
<html lang="en">

<head>
  <?php

  session_start();
  $residentViewProfileModel = new ResidentViewProfileModel();
  $personalAccount = $residentViewProfileModel->showPersonalAccount($conn, $_SESSION['account_id']);
  $accountId = $_SESSION['account_id'] ?? null;

  ?>

  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>E-Barangay Dashboard</title>
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
  <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Metrophobic&display=swap" rel="stylesheet">
  <link href="../../css/resident/residentBootstrap.css" rel="stylesheet">
  <link rel="icon" href="favicon-32x32.png" type="image/x-icon">
  <link rel="stylesheet" href="../../css/admin/adminPortal.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <link rel="stylesheet" href="../../css/admin/table.css">
  <link rel="stylesheet" href="../../css/main.css">
  <script src="https://kit.fontawesome.com/4907458c0c.js" crossorigin="anonymous"></script>
  <script src="../../js/main.js" defer></script>
  <script src="../../js/dashBoard.js" defer></script>
  <script src="../../js/residentBootstrap.js"></script>

  <style>
    * {
      font-family: Poppins;
    }

    .main-head {
      background-color: rgb(15, 117, 54);
    }

    .recipient-badge {
      color: black
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

    .datetime {
      top: 0px
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

    .content {
      padding: 18px;
    }
    .dashboard-body h2 {
    font-size: 30px;
    background-color: white;
    color: #004D40;
    border-top: #004D40 solid 1px;
    border-left: #004D40 solid 1px;
    border-right: #004D40 solid 1px;
    border-bottom: 2px solid #004D40;
    padding: 5px;
    border-radius: 5px;
    border-bottom-right-radius: 0;
    border-bottom-left-radius: 0;
    letter-spacing: 2px;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    text-align: center;
    }
    .dashboard-title {
    width: 100%;
    font-size: 25px;
    padding-bottom: 10px;
    color: #004D40;
    padding-top: 2px;
    position: sticky;
    display: flex;
    flex-direction: column;
    }.card {

    border-top: #013d7d solid 1px;
    border-left: #013d7d solid 1px;
    border-right: #013d7d solid 1px;
    border-bottom: 2px solid #013d7d;
}
  </style>
</head>

<body>

  <div class="app-container">
    <!-- Sidebar Navigation -->
    <div class="sidebar" id="sidebar" data-collapsed="false">
      <div class="sidebar-header">
        <div class="logo-container" id="sidebar-logo">
          <img src="../../img/Barangay Logo.png"
            alt="E-Barangay Logo"
            class="logo">
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
          <!-- <a href="/announcements" class="nav-item" data-tooltip="Announcements">
            <i class="fas fa-bell nav-icon"></i>
            <span class="nav-label">Announcements</span>
          </a>
          <a href="/settings" class="nav-item" data-tooltip="Settings">
            <i class="fas fa-cog nav-icon"></i>
            <span class="nav-label">Settings</span>
          </a> -->
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
        <div class="content-resident">


          <!-- DASHBOARD -->
          <div class="dashboard-title">
            <div class="title-wrapper">
              <h2 class="header">Resident Dashboard</h2>
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
            </div>


          </div>
        </div>
    </div>
  </div>

</body>

</html>