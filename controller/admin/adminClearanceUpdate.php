<?php
require '../../model/admin/adminCertificateModel.php';
require '../../model/admin/adminResidentModel.php';
require '../../model/admin/adminActivityLogModel.php';

// Call the resident model
$adminResidentModel = new AdminResidentModel();
$adminCertificateRequestModel = new CertificateRequestModel();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize POST data
    $requestId = isset($_POST['request_id']) ? intval($_POST['request_id']) : null;
    // $residentId = isset($_POST['resident_id']) ? intval($_POST['resident_id']) : null;
    $accountId = isset($_POST['account_id']) ? intval($_POST['account_id']) : null;
    $officialId = isset($_POST['official_id']) ? intval($_POST['official_id']) : null;
    $certificateType = htmlspecialchars(trim($_POST['type_of_document']));
    $purpose = htmlspecialchars(trim($_POST['purpose']));
    $numberOfCopies = intval($_POST['number_of_copies']);
    $paymentAmount = floatval($_POST['payment_amount']);
    $paymentTypeId = isset($_POST['payment_type']) ? intval($_POST['payment_type']) : null;
    $status = htmlspecialchars(trim($_POST['status']));

    // Handle file upload for proof of payment (store as file in folder)
    $proofOfPaymentPath = null;
    if (isset($_FILES['proof_of_payment']) && $_FILES['proof_of_payment']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../img/proof_of_payments';

        // Create the folder if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Generate a unique filename to avoid overwriting
        $fileName = uniqid() . '_' . basename($_FILES['proof_of_payment']['name']);
        $filePath = $uploadDir . '/' . $fileName;

        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES['proof_of_payment']['tmp_name'], $filePath)) {
            $proofOfPaymentPath = $filePath;
        } else {
            session_start();
            $_SESSION['error'] = "Failed to upload the proof of payment.";
            header('Location: adminClearanceController.php');
            exit;
        }
    } else {
        // If no new file is uploaded, retain the existing file path from the POST data
        $proofOfPaymentPath = isset($_POST['existing_proof_of_payment']) ? htmlspecialchars(trim($_POST['existing_proof_of_payment'])) : null;
    }

    // Prepare request data array
    $requestData = [
        'official_id' => $officialId,
        'template_id' => $certificateType,
        'purpose' => $purpose,
        'number_of_copies' => $numberOfCopies,
        'payment_amount' => $paymentAmount,
        'proof_of_payment' => $proofOfPaymentPath,
        'payment_type_id' => $paymentTypeId,
        'status' => $status
    ];

    // Update the request
    if ($adminCertificateRequestModel->updateRequest($conn, $requestId, $requestData)) {
        $adminActivityLogModel = new ActivityLogModel();
        $module = "Certificate Request Management";
        $activity = "Updated a certificate request with ID " . $requestId . ".";
        $description = "Certificate Type: " . $certificateType . ", Purpose: " . $purpose . ", Number of Copies: " . $numberOfCopies . ", Payment Amount: " . $paymentAmount . ", Status: " . $status;
        $adminActivityLogModel->recordActivityLog($conn, $accountId, $module, $activity, $description);

        $_SESSION['message'] = "Request has been successfully updated.";
        header('Location: adminClearanceController.php');
        exit();
    } else {
        $_SESSION['error'] = "Failed to update the request. Please try again.";
        header('Location: adminClearanceController.php');
        exit();
    }
}

require '../../public/views/admin/adminClearance.php';
?>
