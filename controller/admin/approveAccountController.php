<?php
require '../../model/admin/adminResidentAccountsModel.php';

// Call the resident model
$adminResidentAccountsModel = new adminResidentAccountsModel();

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Start session early so it works even on error
    session_start();

    // Sanitize and validate input
    $accountId = isset($_POST['account_id']) ? intval($_POST['account_id']) : 0;
    $remarks = isset($_POST['remarks']) && !empty(trim($_POST['remarks'])) ? trim($_POST['remarks']) : null;

    if ($accountId <= 0) {
        $_SESSION['message'] = "Invalid account ID.";
        header('Location: adminResidentAccountsController.php');
        exit();
    }

    $accountInfo = $adminResidentAccountsModel->getAccountById($conn, $accountId);
    
    foreach ($accountInfo as $information) {
        $recipientNumber = $information['cellphone_number'];
        $recipientName = $information['full_name'];
        $username = $information['username'];
        $password = $information['date_of_birth'];

    }

    // Convert the recipient number to start with +63 if it starts with 0
    if (strpos($recipientNumber, '0') === 0) {
        $recipientNumber = '+63' . substr($recipientNumber, 1);
    }

    try {
        $status = 'active';

        // Update account status in the DB
        if ($adminResidentAccountsModel->updateAccountStatus($conn, $accountId, $status, $remarks)) {
            // Get account info
            $accountInfo = $adminResidentAccountsModel->getAccountById($conn, $accountId);

            $apiSecret = '14369c92ac196b3bbcd45820947b2c5b8eccdf49'; // Replace with actual SMSChef API secret
            $message = "Hello " . $recipientName . "your resident account has been approved. You may now access our services using your username: " . $username . " and password: " . $password . ".";

            // Prepare cURL request to SMSChef
            $ch = curl_init();
            $postFields = [
                "secret" => $apiSecret,
                "mode" => "devices",
                "device" => '00000000-0000-0000-5566-565b5fb54c86',
                "phone" => $recipientNumber,
                "message" => $message,
                "sim" => 1
            ];

            curl_setopt($ch, CURLOPT_URL, 'https://www.cloud.smschef.com/api/send/sms');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            // Handle the response
            if ($http_code === 200) {
                $_SESSION['message'] = "The account has been successfully approved and SMS sent.";
            } else {
                $_SESSION['message'] = "The account has been approved, but there was an error sending the SMS: HTTP Code " . $http_code . ", Response: " . $response;
            }

            // Show any cURL error
            if (curl_errno($ch)) {
                $_SESSION['message'] = "The account has been approved, but there was an error sending the SMS: " . curl_error($ch);
            }

            curl_close($ch);

            header('Location: adminResidentAccountsController.php');
            exit();
        } else {
            $_SESSION['message'] = "Failed to approve the account.";
            header('Location: adminResidentAccountsController.php');
            exit();
        }
    } catch (Exception $e) {
        $_SESSION['message'] = "Error approving account: " . $e->getMessage();
        header('Location: adminResidentAccountsController.php');
        exit();
    }
} else {
    // Redirect if accessed without form submission
    header('Location: adminResidentAccountsController.php');
    exit();
}
?>
