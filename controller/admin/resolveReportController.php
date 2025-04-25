<?php
// Include the database connection and the model for admin report handling
require '../../model/admin/adminReportModel.php';
session_start(); // Start the session

// Instantiate the AdminReportModel class
$adminReportModel = new ReportModel();

// Check if the form was submitted via POST method
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and sanitize the report type and remarks from the POST request
    $reportType = $_POST['report_type'];
    $remarks = isset($_POST['remarks']) ? trim($_POST['remarks']) : null;

    // Dynamically determine the case ID column based on the report type
    if ($reportType === 'incident') {
        $caseId = $_POST['incident_id'];
    } elseif ($reportType === 'complaint') {
        $caseId = $_POST['complaint_id'];
    } elseif ($reportType === 'blotter') {
        $caseId = $_POST['case_id'];
    } else {
        throw new Exception("Invalid report type.");
    }

    try {
        // Define the status to be updated (e.g., "resolved")
        $status = 'resolved';

        // Map report type to table name
        $tableName = '';
        if ($reportType === 'incident') {
            $tableName = 'Incident';
        } elseif ($reportType === 'complaint') {
            $tableName = 'Complaint';
        } elseif ($reportType === 'blotter') {
            $tableName = 'Blotter';
        } else {
            throw new Exception("Invalid report type.");
        }

        // Call the model method to update the report status
        if ($adminReportModel->updateReportStatus($conn, $tableName, $caseId, $status, $remarks)) {
            // Set a success message
            $_SESSION['message'] = "The report has been successfully resolved.";

            // Redirect to the appropriate controller based on report type
            if ($reportType === 'incident') {
                header('Location: adminIncidentController.php');
            } elseif ($reportType === 'complaint') {
                header('Location: adminComplaintsController.php');
            } else {
                header('Location: adminBlotterController.php'); // Default fallback
            }
        } else {
            // Set a failure message
            $_SESSION['message'] = "Failed to resolve the report.";

            // Redirect to the appropriate controller based on report type
            if ($reportType === 'incident') {
                header('Location: adminIncidentController.php');
            } elseif ($reportType === 'complaint') {
                header('Location: adminComplaintsController.php');
            } else {
                header('Location: adminBlotterController.php'); // Default fallback
            }
        }
        exit();
    } catch (Exception $e) {
        // Handle any exceptions and display an error message
        echo "Error resolving report: " . $e->getMessage();

        // Redirect to the appropriate controller based on report type
        if ($reportType === 'incident') {
            header('Location: adminIncidentController.php');
        } elseif ($reportType === 'complaint') {
            header('Location: adminComplaintsController.php');
        } else {
            header('Location: adminBlotterController.php'); // Default fallback
        }
        exit();
    }
} else {
    // Redirect to the report controller if accessed without form submission
    header('Location: adminBlotterController.php');
    exit();
}
