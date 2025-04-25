<?php
require '../../model/admin/adminOfficialAccountsModel.php';
session_start();
$revokedBy = $_SESSION['account_id'];

// Call the resident model
$adminOfficialAccountsModel = new adminOfficialAccountsModel();

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and sanitize the inputs
    $accountId = intval($_POST['account_id']);
    $revokeReason = trim(htmlspecialchars($_POST['revoke_reason']));
    $revokedBy = $_SESSION['account_id'];

    try {
        // Revoke the official's account
        if ($adminOfficialAccountsModel->revokeOfficial($conn, $accountId, $revokedBy, $revokeReason)) {
            $_SESSION['message'] = "The official's account has been successfully revoked.";
            header('Location: adminOfficialAccountsController.php');
        } else {
            $_SESSION['message'] = "Failed to revoke the official's account.";
            header('Location: adminOfficialAccountsController.php');
            exit();
        }
    } catch (Exception $e) {
        // Handle any exceptions
        $_SESSION['message'] = "Error revoking account: " . $e->getMessage();
        header('Location: adminOfficialAccountsController.php');
        exit();
    }
} else {
    // Redirect if accessed without form submission
    header('Location: adminOfficialAccountsController.php');
    exit();
}
