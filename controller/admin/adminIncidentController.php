<?php
require '../../model/admin/adminReportModel.php';
require '../../model/admin/adminResidentModel.php';
require '../../model/admin/adminOfficialModel.php';
require '../../model/admin/adminActivityLogModel.php';
require_once '../../config/sessionCheck.php';
require_once '../../config/permissionUtils.php';

// Initialize database connection

$reportModel = new ReportModel();
$adminResidentModel = new AdminResidentModel();
$adminOfficialModel = new AdminOfficialModel();

$residents = $adminResidentModel->showResidents($conn);
$officials = $adminOfficialModel->showActiveOfficials($conn);

// Define the number of records per page (you can modify this value as needed)
$recordsPerPage = 10;

// Get the current page from the GET request (default to page 1 if not set)
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

// Calculate the offset for the SQL query
$offset = ($page - 1) * $recordsPerPage;

// Get the incident data (with pagination)
$incidents = $reportModel->showIncidentsWithPagination($conn, $offset, $recordsPerPage);

// Get the total number of incidents (for pagination calculation)
$totalIncidents = $reportModel->getTotalIncidents($conn);

// Calculate total pages
$totalPages = ceil($totalIncidents / $recordsPerPage);

// Determine whether to show pagination
$showPagination = $totalPages > 1; // Show pagination only if there is more than one page

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and sanitize form data
    $accountId = intval($_POST['account_id']);
    $officialId = $_POST['official_id']; // Added official_id
    $residentId = empty($_POST['resident_id']) ? null : intval($_POST['resident_id']);
    $complainant = trim(htmlspecialchars($_POST['complainant']));
    $subject = trim(htmlspecialchars($_POST['subject']));
    $dateReported = trim(htmlspecialchars($_POST['date_reported']));
    $timeReported = trim(htmlspecialchars($_POST['time_reported']));
    $location = trim(htmlspecialchars($_POST['location']));
    $respondent = trim(htmlspecialchars($_POST['respondent']));
    $narration = trim(htmlspecialchars($_POST['narration']));
    $status = trim(htmlspecialchars($_POST['status']));
    $typeOfComplainant = (empty($residentId) || strlen($residentId) === 0) ? 'non-resident' : 'resident';
    
    // Evidence details
    $evidenceDescription = trim(htmlspecialchars($_POST['evidence_description']));

    // Initialize evidence data array to store file paths
    $evidenceData = [];

    // Handle multiple file uploads (evidence_picture as an array)
    if (isset($_FILES['evidence_picture']) && !empty($_FILES['evidence_picture']['name'][0])) {
        $files = $_FILES['evidence_picture'];

        // Directory to store uploaded evidence images
        $uploadDirectory = '../../img/image_evidences/incident/';  // Changed directory name

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

    // Incident data
    $incidentData = [
        'account_id' => $accountId,
        'official_id' => $officialId, 
        'complainant' => $complainant,
        'subject' => $subject,
        'location' => $location,
        'narration' => $narration,
        'person_involved' => $respondent,
        'status' => $status,
        'resident_id' => $residentId,
        'complainant_type' => $typeOfComplainant,
        'date_of_incident' => $dateReported,
        'time_of_incident' => $timeReported
    ];

    // Create the incident case and insert evidence
    $new_case_id = $reportModel->createIncident($conn, $incidentData, $evidenceData);  // Updated method for incidents
    if ($new_case_id) {
        $adminActivityLogModel = new ActivityLogModel();
        $module = "Incident Management"; // Changed to Incident Management
        $activity = "Added a new incident case with ID: " . $new_case_id . ".";
        $description = "Complainant: " . $incidentData['complainant'] . ", Subject: " . $incidentData['subject'] . ", Location: " . $incidentData['location'] . ", Status: " . $incidentData['status'];
        $adminActivityLogModel->recordActivityLog($conn, $accountId, $module, $activity, $description);
        session_start();
        $_SESSION['message'] = "New incident has been successfully added.";
        header('Location: adminIncidentController.php'); // Updated redirect to incident page
        exit;
    } else {
        alert("Failed to create incident or upload evidence.");
    }
}

// View of incident cases
require '../../public/views/admin/adminIncident.php'; // Updated view to incident page
?>
