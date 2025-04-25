<?php
// Include the model for admin resident accounts
require '../../model/admin/adminResidentAccountsModel.php';

// Instantiate the adminResidentAccountsModel class
$adminResidentAccountsModel = new adminResidentAccountsModel();

// Check if the form was submitted via POST method
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    session_start();
    // Retrieve and sanitize the account ID and remarks from the POST request
    $accountId = intval($_POST['account_id']);
    $remarks = trim($_POST['remarks']); // Sanitize the remarks input

    $accountInfo = $adminResidentAccountsModel->getAccountById($conn, $accountId);
    
    foreach ($accountInfo as $information) {
        $recipientNumber = $information['cellphone_number'];
        $recipientName = $information['full_name'];

    }

    // Convert the recipient number to start with +63 if it starts with 0
    if (strpos($recipientNumber, '0') === 0) {
        $recipientNumber = '+63' . substr($recipientNumber, 1);
    }

    try {
        // Define the status to be updated as "disapproved"
        $status = 'disapproved';
        
        // Call the model method to update the account status with remarks
        if ($adminResidentAccountsModel->updateAccountStatus($conn, $accountId, $status, $remarks)) {
              // Get account info
              $accountInfo = $adminResidentAccountsModel->getAccountById($conn, $accountId);

              $apiSecret = '14369c92ac196b3bbcd45820947b2c5b8eccdf49'; // Replace with actual SMSChef API secret
              $message = "Hello " . $recipientName . "your resident account has been diapproved. For the reason that, " . $remarks . "." . " Go to the barangay office for further assistance.";

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
                  $_SESSION['message'] = "The account has been successfully disapproved and SMS sent.";
              } else {
                  $_SESSION['message'] = "The account has been disapproved, but there was an error sending the SMS: HTTP Code " . $http_code . ", Response: " . $response;
              }
  
              // Show any cURL error
              if (curl_errno($ch)) {
                  $_SESSION['message'] = "The account has been disapproved, but there was an error sending the SMS: " . curl_error($ch);
              }
  
              curl_close($ch);
  
              header('Location: adminResidentAccountsController.php');
              exit();
            
            // Redirect to the admin resident accounts controller
            header('Location: adminResidentAccountsController.php');
        } else {
            // Start a session and set a failure message
            session_start();
            $_SESSION['message'] = "Failed to disapprove the account.";
            
            // Redirect to the admin resident accounts controller
            header('Location: adminResidentAccountsController.php');
            exit();
        }
    } catch (Exception $e) {
        // Handle any exceptions and display an error message
        echo "Error disapproving account: " . $e->getMessage();
        
        // Redirect to the admin resident accounts controller
        header('Location: adminResidentAccountsController.php');
        exit();
    }
} else {
    // Redirect to the admin resident accounts controller if accessed without form submission
    header('Location: adminResidentAccountsController.php');
    exit();
}
?>