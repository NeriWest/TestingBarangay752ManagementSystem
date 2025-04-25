<?php
// require_once '../../config/sessionCheck.php';

// // Allow only specific roles (adjust as needed)
// checkAccess(['chairman', 'secretary', 'official','admin']); 

require '../../model/admin/adminCertificateModel.php';
require '../../model/admin/adminResidentModel.php';
require '../../model/admin/adminActivityLogModel.php';
require_once '../../config/sessionCheck.php';
require_once '../../config/permissionUtils.php';

// Initialize database connection
// Call the resident model
$adminResidentModel = new AdminResidentModel();
$adminCertificateRequestModel = new CertificateRequestModel(); // Assuming this is the correct model for certificate requests

// Define the number of records per page (you can modify this value as needed)
$recordsPerPage = 13;

// Get the current page from the GET request (default to page 1 if not set)
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

// Calculate the offset for the SQL query
$offset = ($page - 1) * $recordsPerPage;

// Get the certificate requests data (with pagination)
$requests = $adminCertificateRequestModel->showPermitRequestsWithPagination($conn, $offset, $recordsPerPage);

// Get the total number of certificate requests (for pagination calculation)
$totalCertificateRequests = $adminCertificateRequestModel->getTotalPermitRequests($conn);

// Calculate total pages
$totalPages = ceil($totalCertificateRequests / $recordsPerPage);

// Determine whether to show pagination
$showPagination = $totalPages > 1;

$typeOfPayment = $adminCertificateRequestModel->getTypeOfPayment($conn);
$typeOfDocument = $adminCertificateRequestModel->getTypeOfDocumentPermit($conn);
$residents = $adminResidentModel->showResidents($conn);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize POST data
    $residentId = isset($_POST['resident_id']) && !empty($_POST['resident_id']) ? $_POST['resident_id'] : null;
    $accountId = isset($_POST['account_id']) && !empty($_POST['account_id']) ? $_POST['account_id'] : null;
    $officialId = isset($_POST['official_id']) && !empty($_POST['official_id']) ? $_POST['official_id'] : null;
    $certificateType = htmlspecialchars(trim($_POST['type_of_document']));
    $purpose = htmlspecialchars(trim($_POST['purpose']));
    $numberOfCopies = htmlspecialchars(trim($_POST['number_of_copies']));
    $paymentAmount = htmlspecialchars(trim($_POST['payment_amount']));
    $paymentTypeId = isset($_POST['payment_type']) ? htmlspecialchars(trim($_POST['payment_type'])) : null;

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
            header('Location: adminPermitController.php');
            exit;
        }
    }

    // Prepare request data array
    $requestData = [
        'resident_id' => $residentId,
        'account_id' => $accountId,
        'official_id' => $officialId,
        'template_id' => $certificateType,
        'purpose' => $purpose,
        'number_of_copies' => $numberOfCopies,
        'payment_amount' => $paymentAmount,
        'payment_type_id' => $paymentTypeId,
        'proof_of_payment' => $proofOfPaymentPath, // Save the file path instead of binary content
        'status' => 'Pending' // Default status
    ];

    // Insert the request
    if ($adminCertificateRequestModel->insertRequest($conn, $requestData)) {
        $adminActivityLogModel = new ActivityLogModel();
        $module = "Certificate Request Management";
        $activity = "Added a new certificate request.";
        $description = "Certificate Type: " . $certificateType . ", Purpose: " . $purpose . ", Number of Copies: " . $numberOfCopies . ", Payment Amount: " . $paymentAmount . ", Payment Type ID: " . $paymentTypeId . ", Status: Pending";
        $adminActivityLogModel->recordActivityLog($conn, $accountId, $module, $activity, $description);
        session_start();
        $_SESSION['message'] = "New request has been successfully added.";
        header('Location: adminPermitController.php');
        exit;
    } else {
        // Handle insert error
        session_start();
        $_SESSION['message'] = "Failed to add the request.";
        header('Location: adminPermitController.php');
        exit;
    }
}

require '../../public/views/admin/adminPermit.php';


?>