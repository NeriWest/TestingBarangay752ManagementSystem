<?php

require '../../model/admin/adminFamilyModel.php';
require '../../model/admin/adminResidentModel.php';
require '../../model/admin/adminActivityLogModel.php';
require_once '../../config/sessionCheck.php';
require_once '../../config/permissionUtils.php';

// Initialize database connection



// Call the resident model
$adminResidentModel = new AdminResidentModel();
$adminFamilyModel = new AdminFamilyModel();

//For resident input
$residents = $adminResidentModel->showResidentsWithNoFamily($conn);

// Define the number of records per page (you can modify this value as needed)
$recordsPerPage = 13;

// Get the current page from the GET request (default to page 1 if not set)
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

// Calculate the offset for the SQL query
$offset = ($page - 1) * $recordsPerPage;

// Get the residents data (with pagination)
$families = $adminFamilyModel->showFamiliesWithPagination($conn, $offset, $recordsPerPage);

$members = !empty($families) ? $adminResidentModel->showResidentsByFamilyId($conn, $families[0]['family_id']) : [];

// Get the total number of residents (for pagination calculation)
$totalFamilies = $adminFamilyModel->getTotalFamilies($conn);

// Calculate total pages
$totalPages = ceil($totalFamilies / $recordsPerPage);

// Determine whether to show pagination
$showPagination = $totalPages > 1;
// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and sanitize form data
    $familyName = trim(htmlspecialchars($_POST['family_name']));
    $houseNumber = trim(htmlspecialchars($_POST['house_number']));
    $street = trim(htmlspecialchars($_POST['street']));
    $selectedResidents = isset($_POST['residents']) ? $_POST['residents'] : [];
    $accountId = $_POST['account_id'] ?? null; // Assuming you have the account ID in the session

    // Retrieve and sanitize form data
    $familyData = [
        'family_name' => trim(htmlspecialchars($_POST['family_name'])),
        'house_number' => trim(htmlspecialchars($_POST['house_number'])),
        'street' => trim(htmlspecialchars($_POST['street']))
    ];
    $selectedResidents = isset($_POST['residents']) ? $_POST['residents'] : [];

    // Call the createFamily method, passing the $conn object and $familyData array
    $familyId = $adminFamilyModel->createFamily($conn, $familyData);

    // Log the activity
    if ($familyId) {
        $adminActivityLogModel = new ActivityLogModel();
        $module = "Family Management";
        $activity = "Added a new family.";
        $description = "Family Name: " . $familyData['family_name'] . ", House Number: " . $familyData['house_number'] . ", Street: " . $familyData['street'];
        $adminActivityLogModel->recordActivityLog($conn, $accountId, $module, $activity, $description);
    }

    // Assign residents to the newly created family
    if ($familyId && !empty($selectedResidents)) {
        foreach ($selectedResidents as $residentId) {
            if (!$adminFamilyModel->assignResidentToFamily($conn, $familyId, intval($residentId))) {
                // Log or handle the error if the assignment fails
                error_log("Failed to assign resident ID $residentId to family ID $familyId.");
            }
        }
    }

    // Success message and redirect
    session_start();
    $_SESSION['message'] = "New family has been successfully added along with selected residents.";
    header('Location: adminFamilyController.php');
    exit();
}

require '../../public/views/admin/adminFamily.php';
