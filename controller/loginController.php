<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usernameOrPassword = trim(stripslashes(htmlspecialchars($_POST['username'])));
    $password = trim(stripslashes(htmlspecialchars($_POST['password'])));
    $loginType = trim(stripslashes(htmlspecialchars($_POST['login_type'])));

    if (strlen($usernameOrPassword) === 0 && strlen($password) === 0) {
        $_SESSION['errorMessage'] = "Both username and password are required";
        header('Location: ../public/views/login.php');
        exit();
    } elseif (strlen($password) === 0) {
        $_SESSION['errorMessage'] = "Password is required";
        header('Location: ../public/views/login.php');
        exit();
    } elseif (strlen($usernameOrPassword) === 0) {
        $_SESSION['errorMessage'] = "Username is required";
        header('Location: ../public/views/login.php');
        exit();
    } else {
        require '../model/loginModel.php';
        $loginModel = new LoginModel($usernameOrPassword, $password);
        $user = $loginModel->loginVerification($conn);
        
        if ($user) {
            $_SESSION['user_id'] = $user['account_id'];
            $_SESSION['resident_id'] = $user['resident_id'];
            $_SESSION['role_id'] = $user['role_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['account_id'] = $user['account_id'];
            $_SESSION['last_name'] = $user['last_name']; 
            $_SESSION['suffix'] = $user['suffix']; 
            $_SESSION['email'] = $user['email'];
            $_SESSION['status'] = $user['account_status'];
            $_SESSION['middle_name'] = $user['middle_name'];
            $_SESSION['login_type'] = $loginType;

            // Official roles: Admin(1), Chairman(2), Secretary(3)
            $officialRoles = [1, 2, 3, 5];
            
            if ($loginType === 'official') {
                if (in_array($_SESSION['role_id'], $officialRoles)) {
                    $fullName = $_SESSION['first_name'] . ' ' . $_SESSION['middle_name'] . ' ' . $_SESSION['last_name'];
                    if (!empty($_SESSION['suffix'])) {
                        $fullName .= ' ' . $_SESSION['suffix'];
                    }
                    $_SESSION['message'] = "Welcome to Barangay 752, " . $fullName . "!";
                    header('Location: admin/adminDashboardController.php');
                } elseif (!in_array($_SESSION['role_id'], $officialRoles)) {
                    $_SESSION['errorMessage'] = "You don't have official privileges";
                    header('Location: /Barangay752managementsystem/public/views/login.php');
                    exit();
                }
            } else {
                $fullName = $_SESSION['first_name'] . ' ' . $_SESSION['middle_name'] . ' ' . $_SESSION['last_name'];
                if (!empty($_SESSION['suffix'])) {
                    $fullName .= ' ' . $_SESSION['suffix'];
                }
                if (in_array($_SESSION['role_id'], $officialRoles)) {
                    $_SESSION['message'] = "Welcome to Barangay 752, " . $fullName . "!";
                    header('Location: resident/residentDashboardController.php');
                } else {
                    $_SESSION['message'] = "Welcome to Barangay 752, " . $fullName . "!";
                    header('Location: resident/residentDashboardController.php');
                }
            }
            exit();
        } else {
            $_SESSION['errorMessage'] = "Invalid username or password.";
            header('Location: /Barangay752managementsystem/public/views/login.phpp');
            exit();
        }
    }
} else {
    header("Location: /Barangay752managementsystem/public/views/login.php");
    exit();
}
?>