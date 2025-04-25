<?php
require '../../model/resident/residentReportModel.php';
require '../../model/resident/residentActivityLogModel.php';

$reportModel = new ReportModel();

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and sanitize form data
    $accountId = $_POST['account_id'];
    $residentId = $_SESSION['resident_id'];
    $complainant = trim(htmlspecialchars($_POST['complainant']));
    $subject = trim(htmlspecialchars($_POST['subject']));
    $dateReported = trim(htmlspecialchars($_POST['date_reported']));
    $timeReported = trim(htmlspecialchars($_POST['time_reported']));
    $location = trim(htmlspecialchars($_POST['location']));
    $respondent = trim(htmlspecialchars($_POST['respondent']));
    $narration = trim(htmlspecialchars($_POST['narration']));
    $status = trim(htmlspecialchars($_POST['status']));
    $caseId = $_POST['case_id'];
    $evidenceDescription = trim(htmlspecialchars($_POST['evidence_description']));

    // Determine report type based on case_id prefix
    $reportType = '';
    if (strpos(strtoupper($caseId), 'INC') === 0) {
        $reportType = 'incident';
    } elseif (strpos(strtoupper($caseId), 'COM') === 0) {
        $reportType = 'complaint';
    } else {
        $_SESSION['error'] = "Invalid case ID. Only complaints (COM) and incidents (INC) are editable.";
        header('Location: residentReportController.php');
        exit;
    }

    // Handle evidence data (if any)
    $evidenceData = [];
    if (!empty($_FILES['evidence']['name'][0])) {
        $uploadDir = '../../Uploads/evidence/';
        foreach ($_FILES['evidence']['name'] as $key => $name) {
            $tmpName = $_FILES['evidence']['tmp_name'][$key];
            $filePath = $uploadDir . basename($name);
            if (move_uploaded_file($tmpName, $filePath)) {
                $evidenceData[] = [
                    'evidence_description' => $evidenceDescription,
                    'evidence_picture' => $filePath
                ];
            }
        }
    }

    // Report data
    $reportData = [
        'account_id' => $accountId,
        'resident_id' => $residentId,
        'complainant' => $complainant,
        'subject' => $subject,
        'location' => $location,
        'narration' => $narration,
        'person_involved' => $respondent,
        'status' => $status,
        'date_of_incident' => $dateReported,
        'time_of_incident' => $timeReported,
        strtolower($reportType) . '_id' => $caseId
    ];

    // Update the report and insert evidence if provided
    if ($reportModel->updateReport($conn, $reportType, $reportData, $evidenceData)) {
        $adminActivityLogModel = new ActivityLogModel();
        $module = ucfirst($reportType) . " Management";
        $activity = "Updated a {$reportType} case with ID: " . $caseId . ".";
        $description = "Complainant: " . $complainant . ", Subject: " . $subject . ", Location: " . $location . ", Status: " . $status;
        $adminActivityLogModel->recordActivityLog($conn, $accountId, $module, $activity, $description);

        $_SESSION['message'] = "The {$reportType} with case ID " . $caseId . " has been successfully updated.";
        header('Location: residentReportController.php');
        exit;
    } else {
        $_SESSION['error'] = "Failed to update {$reportType} or upload evidence.";
        header('Location: residentReportController.php');
        exit;
    }
}
?>