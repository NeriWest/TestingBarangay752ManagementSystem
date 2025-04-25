<?php

require '../../model/admin/adminResidentModel.php';
require_once '../../config/sessionCheck.php';
require_once '../../config/permissionUtils.php';

// Initialize database connection
$db = new dbcon();
$conn = $db->getConnection();

checkAccess(['Admin', 'Chairman', 'Secretary', 'Official'], $conn);

// Reload the permissions for the user's role
$roleId = $_SESSION['role_id'] ?? null;
if ($roleId) {
    reloadPermissions($conn, $roleId); // Reload permissions based on the user's role
}

$privilege = $_SESSION['permissions'] ?? []; 
// Call the senior model
$adminSeniorModel = new AdminSeniorModel();

// Define the number of records per page (you can modify this value as needed)
$recordsPerPage = 6;

// Get the current page from the GET request (default to page 1 if not set)
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

// Calculate the offset for the SQL query
$offset = ($page - 1) * $recordsPerPage;

// Get the seniors data (with pagination)
$seniors = $adminSeniorModel->showSeniorsWithPagination($conn, $offset, $recordsPerPage);

// Get the total number of seniors (for pagination calculation)
$totalSeniors = $adminSeniorModel->getTotalSeniors($conn);

// Calculate total pages
$totalPages = ceil($totalSeniors / $recordsPerPage);

// Only show pagination if total seniors exceed the records per page
$showPagination = $totalSeniors > $recordsPerPage;


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
