<?php

require '../../model/admin/adminOfficialModel.php';
require '../../model/admin/adminActivityLogModel.php';

$adminOfficialModel = new AdminOfficialModel();


    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Retrieve and sanitize form inputs
        $officialId = isset($_POST['official_id']) ? intval($_POST['official_id']) : 0;
        $position = trim(stripslashes(htmlspecialchars($_POST['position'])));
        $termStart = trim(stripslashes(htmlspecialchars($_POST['term_start'])));
        $termEnd = trim(stripslashes(htmlspecialchars($_POST['term_end'])));
        $status = trim(stripslashes(htmlspecialchars($_POST['status'])));
        $accountId = intval($_POST['account_id']); // Assuming account_id is an integer

        $officialData = [
            'position' => $position,
            'term_start' => $termStart,
            'term_end' => $termEnd,
            'status' => $status,
            'official_id' => $officialId
        ];


        // Insert the official into the database
        if ($adminOfficialModel->updateOfficial($conn, $officialData)) {
            $adminActivityLogModel = new ActivityLogModel();
            $module = "Official Management";
            $activity = "Updated official: " . htmlspecialchars($official['first_name']) . " " . htmlspecialchars($official['middle_name']) . " " . htmlspecialchars($official['last_name']) . " (ID: " . $officialData['official_id'] . ", Position: " . htmlspecialchars($officialData['position']) . ").";
            $description = "Updated Position: " . $officialData['position'] . ", Term: " . $officialData['term_start'] . " to " . $officialData['term_end'] . ", Status: " . $officialData['status'];
            $adminActivityLogModel->recordActivityLog($conn, $accountId, $module, $activity, $description);
            session_start();
            $_SESSION['message'] = "The official " . $officialData['official_id'] . " (" . $officialData['position'] . ") has been successfully updated.";
            header('Location: adminOfficialController.php');
            exit;
        } else {
            echo "Failed to create official";
        }

    }



?>