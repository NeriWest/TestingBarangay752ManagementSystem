<?php
// Include the model for admin certificate requests
require '../../model/admin/adminCertificateModel.php';

// Instantiate the CertificateRequestModel class
$adminCertificateRequestModel = new CertificateRequestModel();

// Check if the form was submitted via POST method
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and sanitize the request ID and document type from the POST request
    $requestId = intval($_POST['request_id']);
    $documentType = $_POST['document_type'];


    try {
        // Define the status to be updated (e.g., "approved" or "disapproved")
        $status = 'approved'; // Change this as needed
        
        // Call the model method to update the request status
        if ($adminCertificateRequestModel->updateRequestStatus($conn, $requestId, $status, null)) {
            // Start a session and set a success message
            session_start();
            $_SESSION['message'] = "The request has been successfully approved.";
            
            // Redirect to the appropriate controller based on document type
            if ($documentType === 'permit') {
                header('Location: adminPermitController.php');
            } elseif ($documentType === 'certificate') {
                header('Location: adminCertificateController.php');
            } elseif ($documentType === 'clearance') {
                header('Location: adminClearanceController.php');
            } else {
                header('Location: adminCertificateController.php'); // Default fallback
            }
        } else {
            // Start a session and set a failure message
            session_start();
            $_SESSION['message'] = "Failed to approve the request.";
            
            // Redirect to the appropriate controller based on document type
            if ($documentType === 'permit') {
                header('Location: adminPermitController.php');
            } elseif ($documentType === 'certificate') {
                header('Location: adminCertificateController.php');
            } elseif ($documentType === 'clearance') {
                header('Location: adminClearanceController.php');
            } else {
                header('Location: adminCertificateController.php'); // Default fallback
            }
            exit();
        }
    } catch (Exception $e) {
        // Handle any exceptions and display an error message
        echo "Error approving request: " . $e->getMessage();
        
        // Redirect to the appropriate controller based on document type
        if ($documentType === 'permit') {
            header('Location: adminPermitController.php');
        } elseif ($documentType === 'certificate') {
            header('Location: adminCertificateController.php');
        } elseif ($documentType === 'clearance') {
            header('Location: adminClearanceController.php');
        } else {
            header('Location: adminCertificateController.php'); // Default fallback
        }
        exit();
    }
} else {
    // Redirect to the certificate requests controller if accessed without form submission
    if ($documentType === 'permit') {
        header('Location: adminPermitController.php');
    } elseif ($documentType === 'certificate') {
        header('Location: adminCertificateController.php');
    } elseif ($documentType === 'clearance') {
        header('Location: adminClearanceController.php');
    } else {
        header('Location: adminCertificateController.php'); // Default fallback
    }
    exit();
}
