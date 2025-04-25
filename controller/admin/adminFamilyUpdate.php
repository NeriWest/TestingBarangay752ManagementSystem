<?php

require '../../model/admin/adminFamilyModel.php';
require '../../model/admin/adminResidentModel.php';
require '../../model/admin/adminActivityLogModel.php';

// Call the resident model
$adminResidentModel = new AdminResidentModel();
$adminFamilyModel = new AdminFamilyModel();

// For resident input
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
    $familyId = intval($_POST['family_id']);
    $familyName = trim(htmlspecialchars($_POST['family_name']));
    $houseNumber = trim(htmlspecialchars($_POST['house_number']));
    $street = trim(htmlspecialchars($_POST['street']));
    $selectedResidents = isset($_POST['residents']) ? $_POST['residents'] : [];
    $removedResidents = isset($_POST['removed_residents']) ? array_map('intval', $_POST['removed_residents']) : [];
    $accountId = intval($_POST['account_id']); // Assuming account_id is an integer

    // Update family details
    $familyData = [
        'family_id' => $familyId,
        'family_name' => $familyName,
        'house_number' => $houseNumber,
        'street' => $street
    ];
    $updateSuccess = $adminFamilyModel->updateFamily($conn, $familyData);

    // Get current residents assigned to the family
    $currentResidents = $adminResidentModel->showResidentsByFamilyId($conn, $familyId);
    $currentResidentIds = array_column($currentResidents, 'resident_id');

    // Assign new residents to the family (only if not already assigned)
    if (!empty($selectedResidents)) {
        foreach ($selectedResidents as $residentId) {
            if (!in_array($residentId, $currentResidentIds)) {
                if (!$adminFamilyModel->assignResidentToFamily($conn, $familyId, intval($residentId))) {
                    // Log or handle the error if the assignment fails
                    error_log("Failed to assign resident ID $residentId to family ID $familyId.");
                }
            }
        }
    }

    // Remove residents from the family (only if currently assigned)
    if (!empty($removedResidents)) {
        foreach ($removedResidents as $residentId) {
            if (in_array($residentId, $currentResidentIds)) {
                if (!$adminFamilyModel->removeResidentFromFamily($conn, $residentId)) {
                    // Log or handle the error if the removal fails
                    error_log("Failed to remove resident ID $residentId from family ID $familyId.");
                }
            }
        }
    }

    // Log the activity
    $adminActivityLogModel = new ActivityLogModel();
    $module = "Family Management";
    $activity = "Updated family details.";
    $description = "Family Name: " . $familyData['family_name'] . ", House Number: " . $familyData['house_number'] . ", Street: " . $familyData['street'];
    $adminActivityLogModel->recordActivityLog($conn, $accountId, $module, $activity, $description);

    // Success message and redirect
    session_start();
    if ($updateSuccess) {
        $_SESSION['message'] = "Family details have been successfully updated.";
    } else {
        $_SESSION['message'] = "Failed to update family details.";
    }
    header('Location: adminFamilyController.php');
    exit();
}

require '../../public/views/admin/adminFamily.php';
