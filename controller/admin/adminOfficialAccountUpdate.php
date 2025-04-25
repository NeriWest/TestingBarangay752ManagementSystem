<?php
require '../../model/admin/adminOfficialAccountsModel.php';

// Call the resident model
$adminOfficialAccountsModel = new adminOfficialAccountsModel();

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and sanitize form data
    $accountId = intval($_POST['account_id']);
    $username = trim(htmlspecialchars($_POST['username']));
    $email = trim(htmlspecialchars($_POST['email']));
    $password = trim($_POST['password']);
    $privilege = intval($_POST['privilege']);
    $status = trim(htmlspecialchars($_POST['status']));

    // Prepare account data array
    $accountData = [
        'account_id' => $accountId,
        'username' => $username,
        'status' => $status,
        'email' => $email,
    ];

    try {
        // Call the updateAccount method
        if($adminOfficialAccountsModel->updateAccount($conn, $accountData)) {
            session_start();
            $_SESSION['message'] = "The account with a username " . $accountData['username'] . " has been successfully updated.";
            header('Location: adminOfficialAccountsController.php');
        } else {
            $_SESSION['message'] = "Failed to update resident added.";
            header('Location: adminOfficialAccountsController.php');
            exit();
        }
       
    } catch (Exception $e) {
        // Handle any exceptions
        echo "Error updating account: " . $e->getMessage();
        header('Location: adminOfficialAccountsController.php');
        exit();
    }
}