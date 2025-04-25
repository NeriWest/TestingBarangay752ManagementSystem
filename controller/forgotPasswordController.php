<?php

session_start();
require_once '../model/forgotPasswordModel.php';

$usernameOrPhone = $_POST['username-or-phone'] ?? null;

if (empty($usernameOrPhone)) {
    $_SESSION['errorMessage'] = "Username or phone number is required.";
    header('Location: ../public/views/forgotPassword.php');
    exit();
}

// Initialize the ForgotPasswordModel
$forgotPasswordModel = new ForgotPasswordModel($usernameOrPhone);

// Verify the identity
$user = $forgotPasswordModel->verifyIdentity($conn);

if ($user) {
    // Redirect to a success page or handle the next steps
    header('Location: ../public/views/resetPassword.php');
    exit();
} else {
    // Redirect back to the forgot password page with an error message
    header('Location: ../public/views/forgotPassword.php');
    exit();
}
?>
