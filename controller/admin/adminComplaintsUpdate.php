<?php
require '../../model/admin/adminReportModel.php';
require '../../model/admin/adminActivityLogModel.php';

$reportModel = new ReportModel();
$complaints = $reportModel->showComplaints($conn); // Assumes you have this method

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and sanitize form data
    $accountId = $_POST['account_id'];
    $officialId = $_POST['official_id'];
    $complainant = trim(htmlspecialchars($_POST['complainant']));
    $subject = trim(htmlspecialchars($_POST['subject']));
    $dateReported = trim(htmlspecialchars($_POST['date_reported']));
    $timeReported = trim(htmlspecialchars($_POST['time_reported']));
    $location = trim(htmlspecialchars($_POST['location']));
    $respondent = trim(htmlspecialchars($_POST['respondent']));
    $narration = trim(htmlspecialchars($_POST['narration']));
    $status = trim(htmlspecialchars($_POST['status']));
    $complaintId = $_POST['complaint_id'];
    
    // Evidence details
    $evidenceDescription = isset($_POST['evidence_description']);

    // Initialize evidence data array to store file paths
    $evidenceData = [];

    // Handle multiple file uploads
    if (isset($_FILES['evidence_picture']) && $_FILES['evidence_picture']['error'][0] != UPLOAD_ERR_NO_FILE) {
        $files = $_FILES['evidence_picture'];

        // Directory to store uploaded evidence images
        $uploadDirectory = '../../img/image_evidences/complaint/';

        if (!is_dir($uploadDirectory)) {
            mkdir($uploadDirectory, 0777, true);
        }

        foreach ($files['tmp_name'] as $key => $tmp_name) {
            if ($files['error'][$key] == UPLOAD_ERR_OK) {
                $fileTmpPath = $tmp_name;
                $fileName = basename($files['name'][$key]);
                $uniqueFileName = uniqid() . '_' . $fileName;
                $targetFilePath = $uploadDirectory . $uniqueFileName;

                if (move_uploaded_file($fileTmpPath, $targetFilePath)) {
                    $evidenceData[] = [
                        'evidence_description' => $evidenceDescription,
                        'evidence_picture' => $targetFilePath
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

    // Complaint data
    $complaintData = [
        'account_id' => $accountId,
        'official_id' => $officialId,
        'complainant' => $complainant,
        'subject' => $subject,
        'location' => $location,
        'narration' => $narration,
        'person_involved' => $respondent,
        'status' => $status,
        'date_of_incident' => $dateReported,
        'time_of_incident' => $timeReported,
        'complaint_id' => $complaintId,
    ];

    // Create or update complaint record
    if ($reportModel->updateComplaint($conn, $complaintData, $evidenceData)) {
        $adminActivityLogModel = new ActivityLogModel();
        $module = "Complaint Management";
        $activity = "Updated a complaint case with ID: " . $complaintData['complaint_id'] . ".";
        $description = "Complainant: " . $complaintData['complainant'] . ", Subject: " . $complaintData['subject'] . ", Location: " . $complaintData['location'] . ", Status: " . $complaintData['status'];
        $adminActivityLogModel->recordActivityLog($conn, $accountId, $module, $activity, $description);

        session_start();
        $_SESSION['message'] = "The complaint with ID " . $complaintData['complaint_id'] . " has been successfully updated.";
        header('Location: adminComplaintsController.php');
        exit;
    } else {
        echo "Failed to update complaint or upload evidence.";
    }
}

// View of complaints
require '../../public/views/admin/adminComplaint.php';
?>
