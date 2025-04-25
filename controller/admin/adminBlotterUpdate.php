<?php
require '../../model/admin/adminReportModel.php';
require '../../model/admin/adminActivityLogModel.php';

$reportModel = new ReportModel();
$blotters = $reportModel->showBlotter($conn);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and sanitize form data
    $accountId = $_POST['account_id'];
    $officialId = $_POST['official_id']; // Added official_id
    $complainant = trim(htmlspecialchars($_POST['complainant']));
    $subject = trim(htmlspecialchars($_POST['subject']));
    $dateReported = trim(htmlspecialchars($_POST['date_reported']));
    $timeReported = trim(htmlspecialchars($_POST['time_reported']));
    $location = trim(htmlspecialchars($_POST['location']));
    $respondent = trim(htmlspecialchars($_POST['respondent']));
    $narration = trim(htmlspecialchars($_POST['narration']));
    $status = trim(htmlspecialchars($_POST['status']));
    $caseId = $_POST['case_id'];
    
    // Evidence details
    $evidenceDescription = isset($_POST['evidence_description']);

    // Initialize evidence data array to store file paths
    $evidenceData = [];

    // Handle multiple file uploads (evidence_picture as an array), but make it optional
    if (isset($_FILES['evidence_picture']) && $_FILES['evidence_picture']['error'][0] != UPLOAD_ERR_NO_FILE) {
        $files = $_FILES['evidence_picture'];

        // Directory to store uploaded evidence images
        $uploadDirectory = '../../img/image_evidences/blotter/';  // Add a trailing slash

        // Check if the directory exists, if not, create it
        if (!is_dir($uploadDirectory)) {
            mkdir($uploadDirectory, 0777, true); // Create the folder with full permissions
        }

        // Loop through each uploaded file
        foreach ($files['tmp_name'] as $key => $tmp_name) {
            if ($files['error'][$key] == UPLOAD_ERR_OK) {
                $fileTmpPath = $tmp_name;
                $fileName = basename($files['name'][$key]);
                
                // Create a unique file name to avoid overwriting
                $uniqueFileName = uniqid() . '_' . $fileName;
                
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

    // Blotter data
    $blotterData = [
        'account_id' => $accountId,
        'official_id' => $officialId, // Added official_id to blotterData
        'complainant' => $complainant,
        'subject' => $subject,
        'location' => $location,
        'narration' => $narration,
        'person_involved' => $respondent,
        'status' => $status,
        'date_of_incident' => $dateReported,
        'time_of_incident' => $timeReported,
        'case_id' => $caseId,
    ];

    // Create the blotter case and insert evidence if provided
    if ($reportModel->updateBlotter($conn, $blotterData, $evidenceData)) {
        $adminActivityLogModel = new ActivityLogModel();
        $module = "Blotter Management";
        $activity = "Updated a blotter case with ID: " . $blotterData['case_id'] . ".";
        $description = "Complainant: " . $blotterData['complainant'] . ", Subject: " . $blotterData['subject'] . ", Location: " . $blotterData['location'] . ", Status: " . $blotterData['status'];
        $adminActivityLogModel->recordActivityLog($conn, $accountId, $module, $activity, $description);
        session_start();
        $_SESSION['message'] = "The blotter with a case-id " . $blotterData['case_id'] . " has been successfully updated.";
        header('Location: adminBlotterController.php');
        exit;
    } else {
        echo "Failed to update blotter or upload evidence.";
    }
}

// View of blotter cases
require '../../public/views/admin/adminBlotter.php';
?>
