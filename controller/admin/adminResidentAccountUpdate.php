<?php
require '../../model/admin/adminResidentAccountsModel.php';

// Call the resident model
$adminResidentAccountsModel = new adminResidentAccountsModel();

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and sanitize form data
    $accountId = intval($_POST['account_id']);
    $residentId = intval($_POST['resident_id']);
    $username = trim(htmlspecialchars($_POST['username']));
    $email = trim(htmlspecialchars($_POST['email']));
    $password = trim($_POST['password']);
    $privilege = intval($_POST['privilege']);
    $status = trim(htmlspecialchars($_POST['status']));

    // Prepare account data array
    $accountData = [
        'account_id' => $accountId,
        'resident_id' => $residentId,
        'username' => $username,
        'email' => $email,
        'password' => $password,
        'role_id' => $privilege, // Map privilege to role_id
        'status' => $status,
        'existing_password' => ''     // To be fetched from DB if no new password
    ];

    try {
        // Call the updateAccount method
        if($adminResidentAccountsModel->updateAccount($conn, $accountData)) {
            session_start();
            $_SESSION['message'] = "The account with a username " . $accountData['username'] . " has been successfully updated.";
            header('Location: adminResidentAccountsController.php');
        } else {
            $_SESSION['message'] = "Failed to update resident added.";
            header('Location: adminResidentAccountsController.php');
            exit();
        }
       
    } catch (Exception $e) {
        // Handle any exceptions
        echo "Error updating account: " . $e->getMessage();
        header('Location: adminResidentAccountsController.php');
        exit();
    }
} 