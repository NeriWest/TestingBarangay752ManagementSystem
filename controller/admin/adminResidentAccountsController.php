<?php
// require_once '../../config/sessionCheck.php';

// // Allow only specific roles (adjust as needed)
// checkAccess(['chairman', 'secretary', 'official','admin']); 

require '../../model/admin/adminResidentAccountsModel.php';
require '../../model/admin/adminResidentModel.php';
require_once '../../config/sessionCheck.php';
require_once '../../config/permissionUtils.php';

// Initialize database connection

$privilege = $_SESSION['permissions'] ?? [];  // Include your model to fetch resident data
// Instantiate the models
$adminResidentAccountsModel = new AdminResidentAccountsModel();
$adminResidentModel = new AdminResidentModel();

// Get the residents data (for displaying the resident list on the same page)
$residents = $adminResidentModel->showResidentWithNoAccount($conn);

// Define the number of records per page (you can modify this value as needed)
$recordsPerPage = 13;

// Get the current page from the GET request (default to page 1 if not set)
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

// Calculate the offset for the SQL query
$offset = ($page - 1) * $recordsPerPage;

// Get the accounts data (with pagination)
$accounts = $adminResidentAccountsModel->showAccountsWithPagination($conn, $offset, $recordsPerPage);

// Get the total number of accounts (for pagination calculation)
$totalAccounts = $adminResidentAccountsModel->getTotalAccounts($conn);

// Calculate total pages
$totalPages = ceil($totalAccounts / $recordsPerPage);

// Determine whether to show pagination
$showPagination = $totalPages > 1; // Show pagination only if there is more than one page


// Show the resident accounts page if no form submission
require '../../public/views/admin/adminResidentAccount.php';
?>
