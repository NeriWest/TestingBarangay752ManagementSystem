<?php
require '../../model/admin/adminOfficialModel.php';
require '../../model/admin/adminResidentModel.php';
require '../../model/admin/adminActivityLogModel.php';

$adminResidentModel = new AdminResidentModel();
$adminOfficialModel = new AdminOfficialModel();

$residents = $adminResidentModel->showNonOfficialResidents($conn);

$recordsPerPage = 13;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $recordsPerPage;

$officials = $adminOfficialModel->showOfficialsWithPagination($conn, $offset, $recordsPerPage);
$totalOfficials = $adminOfficialModel->getTotalOfficials($conn);
$totalPages = ceil($totalOfficials / $recordsPerPage);
$showPagination = $totalPages > 1;

$roles = $adminOfficialModel->showRoles($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $residentId = isset($_POST['resident_id']) ? intval($_POST['resident_id']) : 0;
    $accountId = isset($_POST['account_id']) ? intval($_POST['account_id']) : 0;
    $position = trim(stripslashes(htmlspecialchars($_POST['position'])));
    $roleName = trim(stripslashes(htmlspecialchars($_POST['role_name'])));
    $specifyPosition = trim(stripslashes(htmlspecialchars($_POST['specify_position'])));

    if ($position == 1) {
        $specifyPosition = "Chairman";
    } elseif ($position == 2) {
        $specifyPosition = "Secretary";
    } elseif ($position == 3) {
        $specifyPosition = trim(stripslashes(htmlspecialchars($_POST['specify_position'])));
    } else {
        $specifyPosition = $roleName;
    }

    $termStart = trim(stripslashes(htmlspecialchars($_POST['term_start'])));
    $termEnd = trim(stripslashes(htmlspecialchars($_POST['term_end'])));
    $status = trim(stripslashes(htmlspecialchars($_POST['status'])));

    $officialData = [
        'resident_id' => $residentId,
        'account_id' => $accountId, // Include account_id here
        'position' => $position,
        'specify_position' => $specifyPosition,
        'term_start' => $termStart,
        'term_end' => $termEnd,
        'status' => $status
    ];

    if ($adminOfficialModel->createOfficial($conn, $officialData)) {
        $adminActivityLogModel = new ActivityLogModel();
        $module = "Official Management";
        $activity = "Added a new official with an ID: " . $officialData['resident_id'] . ".";
        $description = "Position: " . $officialData['position'] . ", Term: " . $officialData['term_start'] . " to " . $officialData['term_end'] . ", Status: " . $officialData['status'];
        $adminActivityLogModel->recordActivityLog($conn, $accountId, $module, $activity, $description);
        session_start();
        $_SESSION['message'] = "New official has been successfully added.";
        header('Location: adminOfficialController.php');
        exit;
    } else {
        echo "Failed to create official";
    }
}

require '../../public/views/admin/adminOfficial.php';

?>
