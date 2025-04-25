<?php

require_once '../../model/resident/residentActivityLogModel.php';

// Initialize database connection
$db = new dbcon();
$conn = $db->getConnection();

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
require '../../public/views/resident/residentActivityLogs.php';
?>
