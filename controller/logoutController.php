<?php
session_start();

// Unset all of the session variables
$_SESSION = array();

// If you want to kill the session, also delete the session cookie.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Unset specific session variables
unset($_SESSION['username']);
unset($_SESSION['firstname']);
unset($_SESSION['password']);
unset($_SESSION['csrf_token']); // Unset the CSRF token
unset($_SESSION['user_id']);
unset($_SESSION['account_id']);
unset($_SESSION['lastname']); 

// Finally, destroy the session.
session_destroy();

// Redirect to login page
header("Location: ../public/views/login.php");
exit();
?>
