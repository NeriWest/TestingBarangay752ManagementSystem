<?php
require '../../model/admin/adminResidentModel.php';
require '../../model/admin/adminActivityLogModel.php';

// Call the resident model
$adminResidentModel = new AdminResidentModel();

// Define the number of records per page (you can modify this value as needed)
$recordsPerPage = 9;

// Get the current page from the GET request (default to page 1 if not set)
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

// Calculate the offset for the SQL query
$offset = ($page - 1) * $recordsPerPage;

// For general residents with pagination
$residents = $adminResidentModel->showResidentWithPagination($conn, $offset, $recordsPerPage);
$totalResidents = $adminResidentModel->getTotalResidents($conn);

$seniors = $adminResidentModel->showSeniorsWithPagination($conn, $offset, $recordsPerPage);
$totalSeniors = $adminResidentModel->getTotalSeniors($conn);

$newResidents = $adminResidentModel->showNewResidentsWithPagination($conn, $offset, $recordsPerPage);
$totalNewResidents = $adminResidentModel->getTotalNewResidents($conn);

$totalPages = ceil($totalResidents / $recordsPerPage);
$totalSeniorPages = ceil($totalSeniors / $recordsPerPage);
$totalNewResidentPages = ceil($totalNewResidents / $recordsPerPage);



// Only show pagination if total residents exceed the records per page
$showPagination = $totalResidents > $recordsPerPage;
// Check if it's a POST request (for AJAX form submission)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Log all incoming data for debugging (optional)
    error_log(print_r($_POST, true));

   // Check if form fields are received properly
if (isset($_POST['first-name'])) {
    // Retrieve and sanitize form inputs
    $residentId = isset($_POST['resident_id']) ? intval($_POST['resident_id']) : 0;
    $lastName = trim(stripslashes(htmlspecialchars($_POST['last-name'])));
    $firstName = trim(stripslashes(htmlspecialchars($_POST['first-name'])));
    $middleName = trim(stripslashes(htmlspecialchars($_POST['middle-name'])));
    $suffix = trim(stripslashes(htmlspecialchars($_POST['suffix'])));
    $phoneNumber = trim(stripslashes(htmlspecialchars($_POST['phonenumber'])));
    $sex = trim(stripslashes(htmlspecialchars($_POST['sex'])));
    $dateOfBirth = trim(stripslashes(htmlspecialchars($_POST['date-of-birth'])));
    $civilStatus = trim(stripslashes(htmlspecialchars($_POST['civil-status'])));
    $houseNumber = trim(stripslashes(htmlspecialchars($_POST['house-number'])));
    $street = trim(stripslashes(htmlspecialchars($_POST['street'])));
    $occupation = trim(stripslashes(htmlspecialchars($_POST['occupation'])));
    $disability = trim(stripslashes(htmlspecialchars($_POST['disability'])));
    $citizenship = trim(stripslashes(htmlspecialchars($_POST['citizenship'])));
    $email = trim(stripslashes(htmlspecialchars($_POST['email'])));
    $voterStatus = trim(stripslashes(htmlspecialchars($_POST['voter_status'])));
    $oscaId = trim(stripslashes(htmlspecialchars($_POST['osca-id'])));
    $oscaIdIssued = trim(stripslashes(htmlspecialchars($_POST['osca-id-issued'])));
    $status = trim(stripslashes(htmlspecialchars($_POST['status'])));
    
    $accountId = intval($_POST['account_id']); // Assuming account_id is an integer

    $residentData = [
        'first_name' => $firstName,
        'middle_name' => $middleName,
        'last_name' => $lastName,
        'suffix' => $suffix,
        'date_of_birth' => $dateOfBirth,
        'cellphone_number' => $phoneNumber,
        'house_number' => $houseNumber,
        'street' => $street,
        'email' => $email,
        'occupation' => $occupation,
        'sex' => $sex,
        'civil_status' => $civilStatus,
        'disability' => $disability,
        'resident_id' => $residentId,
        'citizenship' => $citizenship,
        'voter_status' => $voterStatus,
        'osca_id' => $oscaId,
        'osca_date_issued' => $oscaIdIssued,
        'status' => $status
    ];


        // Insert the resident into the database
        if ($adminResidentModel->insertResident($conn, $residentData)) {
            $adminActivityLogModel = new ActivityLogModel();
            $module = "Resident Management";
            $activity = "Added a new resident with the name " . $residentData['first_name'] . " " . $residentData['last_name'] . ".";
            $description = "Name: " . $residentData['first_name'] . " " . $residentData['last_name'] . ", Address: " . $residentData['house_number'] . " " . $residentData['street'] . ", Contact: " . $residentData['cellphone_number'];
            $dateCreated = date('Y-m-d');
            $adminActivityLogModel->recordActivityLog($conn, $accountId, $module, $activity, $dateCreated, date('H:i:s'), $description);
            session_start();
            $_SESSION['message'] = "New resident has been successfully added.";
            header('Location: adminResidentController.php');
        } else {
            echo "Failed to create resident";
        }
    } else {
        echo "Required form data missing";
    }
    exit(); // Prevent further execution
}

// If it's a GET request, load the page view
require '../../public/views/admin/adminResident.php';

?>
