<?php 
session_start();
include '../../public/views/admin/adminSettings.php';
require_once '../../config/sessionCheck.php';
require_once '../../config/rolePermissions.php';
require_once '../../model/dbconnection.php';
require_once '../../config/permissionUtils.php';

$db = new dbcon();
$conn = $db->getConnection();

// Check if the user has a valid role
checkAccess(['Admin', 'Chairman', 'Secretary', 'Official'], $conn);

// Reload the permissions for the user's role
$roleId = $_SESSION['role_id'] ?? null;
if ($roleId) {
    reloadPermissions($conn, $roleId);
}

$privilege = $_SESSION['permissions'] ?? [];

$accountId = $_SESSION['account_id'] ?? null;

if (!$accountId) {
    $_SESSION['errorMessage'] = "Session expired. Please log in again.";
    header("Location: ../../login.php");
    exit();
}
