<?php
session_start();
require_once '../../model/admin/adminActivityLogModel.php';
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
// Initialize the activity log model
$activityLogModel = new ActivityLogModel();

// Define the number of records per page (you can modify this value as needed)
$recordsPerPage = 13;

// Get the current page from the GET request (default to page 1 if not set)
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

// Calculate the offset for the SQL query
$offset = ($page - 1) * $recordsPerPage;

// Get the activity logs data (with pagination)
$activityLogs = $activityLogModel->showActivityLogWithPagination($conn, $offset, $recordsPerPage);

// Get the total number of activity logs (for pagination calculation)
$totalActivityLogs = $activityLogModel->getTotalActivityLog($conn);

// Calculate total pages
$totalPages = ceil($totalActivityLogs / $recordsPerPage);

// Determine whether to show pagination
$showPagination = $totalPages > 1; // Show pagination only if there is more than one page


// Include the view file
require '../../public/views/admin/adminActivityLogs.php';
?>
