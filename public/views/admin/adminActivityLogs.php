<!DOCTYPE html>
<html lang="en">
<?php
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
    <title>Activity Log</title>
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
</head>
<style>
    table {
        border-collapse: collapse;
        font-size: 15px;
        width: auto;
        table-layout: auto;
        max-width: 100%;
    }

    /* View Form */
    .modal-overlay {
        padding-top: 50px;
    }

    .modal {
        max-width: 750px;
        width: 100%;
    }
</style>

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
                            <h1>BRGY. 752</h1>
                        </div>
                        <ul class="nav-list">
                            <a href="../../index.php" class="nav-link">
                                <li class="nav-list-item">
                                    <i class="fa-solid fa-house"></i>
                                    <span class="link-text">HOME</span>
                                </li>
                            </a>
                            <a href="adminDashboardController.php" class="nav-link">
                                <li class="nav-list-item">
                                    <i class="fa-solid fa-gauge"></i>
                                    <span class="link-text">DASHBOARD</span>
                                </li>
                            </a>
                            <li class="nav-list-item" id="resident-link">
                                <a href="#" class="nav-link">
                                    <i class="fa-solid fa-user"></i>
                                    <span class="link-text">PEOPLE</span>
                                    <span class="link-text">
                                        <i class="fa-solid fa-caret-down" id="resident-dropdown-logo"></i>
                                    </span>
                                    <ul class="resident-dropdown">
                                        <li class="dropdown-item"><a href="adminResidentController.php">Resident</a></li>
                                        <li class="dropdown-item"><a href="adminOfficialController.php">Officials</a></li>
                                        <li class="dropdown-item"><a href="adminFamilyController.php">Family</a></li>
                                    </ul>
                                </a>
                            </li>
                            <a href="#" class="nav-link">
                                <li class="nav-list-item" id="accounts-link">
                                    <i class="fa-solid fa-user-pen"></i>
                                    <span class="link-text">ACCOUNTS</span><span class="link-text"><i
                                            class="fa-solid fa-caret-down" id="accounts-dropdown-logo"></i></span>
                                    <ul class="accounts-dropdown">
                                        <li class="dropdown-item"><a href="adminResidentAccountsController.php">Resident Accounts</a></li>
                                        <li class="dropdown-item"><a href="adminOfficialAccountsController.php">Official Accounts</a></li>
                                    </ul>
                                </li>
                            </a>
                            <a href="#" class="nav-link">
                                <li class="nav-list-item" id="request-link">
                                    <i class="fa-solid fa-print"></i>
                                    <span class="link-text">REQUEST</span><span class="link-text"><i
                                            class="fa-solid fa-caret-down" id="request-dropdown-logo"></i></span>
                                    <ul class="request-dropdown">
                                        <li class="dropdown-item"><a href="adminPermitController.php">Permits</a></li>
                                        <li class="dropdown-item"><a href="adminClearanceController.php">Clearance</a></li>
                                        <li class="dropdown-item"><a href="adminCertificateController.php">Certificate</a></li>
                                    </ul>
                                </li>
                            </a>
                            <a href="#" class="nav-link">
                                <li class="nav-list-item" id="report-link">
                                    <i class="fa-solid fa-flag"></i>
                                    <span class="link-text">REPORT</span><span class="link-text"><i
                                            class="fa-solid fa-caret-down" id="report-dropdown-logo"></i></span>
                                    <ul class="report-dropdown">
                                        <li class="dropdown-item"><a href="adminBlotterController.php">Blotters</a></li>
                                        <li class="dropdown-item"><a href="adminComplaintsController.php">Complaints</a></li>
                                        <li class="dropdown-item"><a href="adminIncidentController.php">Incident</a></li>
                                    </ul>
                                </li>
                            </a>
                            <a href="adminAnnouncementController.php" class="nav-link">
                                <li class="nav-list-item">
                                    <i class="fa-solid fa-bullhorn"></i>
                                    <span class="link-text">ANNOUNCEMENT</span>
                                </li>
                            </a>
                            <a href="adminActivityLogController.php" class="nav-link">
                                <li class="nav-list-item">
                                    <i class="fa-regular fa-clock"></i>
                                    <span class="link-text">ACTIVITY LOG</span>
                                </li>
                            </a>
                        </ul>
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
                <h1 id="table-title" style="font-size: 30px; margin-bottom: 20px">Activity Logs</h1>
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <td colspan="8" class="search-column">
                                    <div class="search-bar">
                                        <p>Search</p>
                                        <input type="text" placeholder="Search Logs" id="searchInput">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>Log Id</th>
                                <th>Module</th>
                                <th>Account Name</th>
                                <th>Activity</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($activityLogs)) {
                                foreach ($activityLogs as $log) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($log['log_id']) . "</td>";
                                    echo "<td>" . htmlspecialchars($log['module']) . "</td>";
                                    echo "<td>" . htmlspecialchars($log['account_name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($log['activity']) . "</td>";
                                    echo "<td>" . date("m/d/Y", strtotime(htmlspecialchars($log['date']))) . "</td>";
                                    echo "<td>" . date("g:i A", strtotime(htmlspecialchars($log['time']))) . "</td>";
                                    echo "<td>" . htmlspecialchars($log['description']) . "</td>";
                                    echo "<td>
                                        <button class='modal-open button' style='background-color:rgb(92, 92, 92);' aria-haspopup='true' 
                                            data-log-id='" . htmlspecialchars($log['log_id']) . "' 
                                            data-module='" . htmlspecialchars($log['module']) . "' 
                                            data-account-name='" . htmlspecialchars($log['account_name']) . "' 
                                            data-activity='" . htmlspecialchars($log['activity']) . "' 
                                            data-date='" . htmlspecialchars($log['date']) . "' 
                                            data-time='" . htmlspecialchars($log['time']) . "' 
                                            data-description='" . htmlspecialchars($log['description']) . "' 
                                            onclick='populateBlotterModal(this)'>
                                            <i class='fa-solid fa-eye'></i>
                                        </button>
                                    </td>";


                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='7'>No activity logs found.</td></tr>";
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
            
            <div class="modal-overlay">
                <div class="modal" id="accessible-modal" role="dialog" aria-modal="true" aria-labelledby="modal-title" aria-describedby="modal-description">
                    <div class="modal-header">
                        <h2 id="modal-title">View Activity Log</h2>
                        <button class="modal-close" aria-label="Close modal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-content">
                        <form id="view-activity-log-form">
                            <input type="hidden" id="view-log-id" name="log_id" readonly>
                            <input type="hidden" name="account_id" value="<?= $_SESSION['account_id'] ?>" readonly>
                            <div class="form-group">
                                <label for="view-module">Module</label>
                                <input type="text" id="view-module" name="module" readonly>
                            </div>
                            <div class="form-group">
                                <label for="view-accountName">Account Name</label>
                                <input type="text" id="view-accountName" name="account_name" readonly>
                            </div>
                            <div class="form-group">
                                <label for="view-date">Date</label>
                                <input type="date" id="view-date" name="date" readonly>
                            </div>
                            <div class="form-group">
                                <label for="view-time">Time</label>
                                <input type="time" id="view-time" name="time" readonly>
                            </div>
                            <div class="form-group">
                                <label for="view-activity">Activity</label>
                                <input type="text" id="view-activity" name="activity" readonly>
                            </div>
                            <div class="form-group">
                                <label for="view-description">Description</label>
                                <textarea id="view-description" name="description" readonly style="width: 99%; height: 150px; resize: none;"></textarea>
                            </div>
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
                url: 'adminActivityLogSearch.php', // Ensure this is the correct path to your PHP file
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
                        let logId = $(this).data('log-id');
                        console.log("Clicked Log ID: " + logId); // Debugging: check if the click event is captured

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


    function populateBlotterModal(button) {
        // Get the blotter data from the button's data attributes
        var logId = button.getAttribute('data-log-id');
        var module = button.getAttribute('data-module');
        var accountName = button.getAttribute('data-account-name');
        var activity = button.getAttribute('data-activity');
        var date = button.getAttribute('data-date');
        var time = button.getAttribute('data-time');
        var description = button.getAttribute('data-description');

        // Populate the form inputs with the blotter data
        document.getElementById('view-log-id').value = logId;
        document.getElementById('view-module').value = module;
        document.getElementById('view-accountName').value = accountName;
        document.getElementById('view-date').value = date;
        document.getElementById('view-time').value = time;
        document.getElementById('view-activity').value = activity;
        document.getElementById('view-description').value = description;
    }
</script>

</html>


