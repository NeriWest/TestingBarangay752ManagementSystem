<?php
session_start();
$accountId = $_SESSION['account_id'];
$residentId = $_SESSION['resident_id'];
require '../../model/resident/residentReportModel.php';
require '../../model/resident/residentActivityLogModel.php';

$reportModel = new ReportModel();
// FOR PDF
// $blotters = $blotterModel->showBlotter($conn);

// Define the number of records per page (you can modify this value as needed)
$recordsPerPage = 10;

// Get the current page from the GET request (default to page 1 if not set)
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

// Calculate the offset for the SQL query
$offset = ($page - 1) * $recordsPerPage;

// Get the reports data (with pagination)
$reports = $reportModel->showAllReportsWithPagination($conn, $offset, $recordsPerPage, $residentId);

// Get the total number of reports (for pagination calculation)
$totalReports = $reportModel->getTotalReports($conn, $residentId);

// Calculate total pages
$totalPages = ceil($totalReports / $recordsPerPage);

// Determine whether to show pagination
$showPagination = $totalPages > 1; // Show pagination only if there is more than one page


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and sanitize form data
    $accountId = $_POST['account_id'];
    $complainant = trim(htmlspecialchars($_POST['complainant']));
    $typeOfReport = trim(htmlspecialchars($_POST['type_of_report']));
    $subject = trim(htmlspecialchars($_POST['subject']));
    $dateReported = trim(htmlspecialchars($_POST['date_reported']));
    $timeReported = trim(htmlspecialchars($_POST['time_reported']));
    $location = trim(htmlspecialchars($_POST['location']));
    $respondent = trim(htmlspecialchars($_POST['respondent']));
    $narration = trim(htmlspecialchars($_POST['narration']));
    $status = "pending";

    // Evidence details
    $evidenceDescription = trim(htmlspecialchars($_POST['evidence_description']));

    // Initialize evidence data array to store file paths
    $evidenceData = [];

    // Handle multiple file uploads (evidence_picture as an array)
    if (isset($_FILES['evidence_picture']) && !empty($_FILES['evidence_picture']['name'][0])) {
        $files = $_FILES['evidence_picture'];

        // Loop through each uploaded file
        foreach ($files['tmp_name'] as $key => $tmp_name) {
            if ($files['error'][$key] == UPLOAD_ERR_OK) {
                $fileTmpPath = $tmp_name;
                $fileName = basename($files['name'][$key]);

                // Create a unique file name to avoid overwriting
                $uniqueFileName = uniqid() . '_' . $fileName;

                // Determine the upload directory based on the type of report
                $uploadDirectory = "../../img/image_evidences/{$typeOfReport}/";

                // Check if the directory exists, if not, create it
                if (!is_dir($uploadDirectory)) {
                    mkdir($uploadDirectory, 0777, true); // Create the folder with full permissions
                }

                // Move the uploaded file to the target directory
                $targetFilePath = $uploadDirectory . $uniqueFileName;
                if (move_uploaded_file($fileTmpPath, $targetFilePath)) {
                    // Store the file path and description in the evidenceData array
                    $evidenceData[] = [
                        'evidence_description' => $evidenceDescription,
                        'evidence_picture' => $targetFilePath // Store the path instead of binary data
                    ];
                } else {
                    echo "Failed to upload evidence picture.";
                    exit;
                }
            } else {
                echo "Failed to upload evidence picture.";
                exit;
            }
        }
    }
  
    // Prepare report data
    $reportData = [
        'account_id' => $accountId,
        'official_id' => null, // Adjust as needed
        'resident_id' => $residentId,
        'date_of_incident' => $dateReported,
        'time_of_incident' => $timeReported,
        'subject' => $subject,
        'complainant' => $complainant,
        'complainant_type' => 'resident', // Adjust as needed
        'person_involved' => $respondent,
        'location' => $location,
        'narration' => $narration,
        'status' => $status
    ];
    

    // Create the report
    $new_case_id = $reportModel->createReport($conn, $typeOfReport, $reportData, $evidenceData);
    if ($new_case_id) {
        $adminActivityLogModel = new ActivityLogModel();
        $module = ucfirst($typeOfReport) . " Management";
        $activity = "Added a new {$typeOfReport} case with ID: " . $new_case_id . ".";
        $description = "Complainant: " . $reportData['complainant'] . ", Subject: " . $reportData['subject'] . ", Location: " . $reportData['location'] . ", Status: " . $reportData['status'];
        $dateCreated = date('Y-m-d');
        $adminActivityLogModel->recordActivityLog($conn, $accountId, $module, $activity, $description);
        $_SESSION['message'] = "New {$typeOfReport} has been successfully added.";
        header("Location: residentReportController.php");
        exit;
    } else {
        echo "Failed to create {$typeOfReport} or upload evidence.";
    }
    
}



// View of blotter cases
require '../../public/views/resident/residentReport.php';
?>
