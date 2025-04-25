<?php
require '../../model/admin/adminAnnouncementModel.php';
require_once '../../model/admin/adminActivityLogModel.php';

session_start(); // Ensure session is started

$adminAnnouncementModel = new AdminAnnouncementModel();
$adminActivityLogModel = new ActivityLogModel();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['announcement_id'])) {
    $announcementId = intval($_POST['announcement_id']);
    $accountId = $_SESSION['account_id']; // Assuming the logged-in user's account ID is stored in the session
    $subject = isset($_POST['subject']) ? htmlspecialchars($_POST['subject']) : 'Unknown';
    $recipients = isset($_POST['recipients']) ? htmlspecialchars($_POST['recipients']) : 'Unknown';
    $schedule = isset($_POST['schedule']) ? htmlspecialchars($_POST['schedule']) : 'Unknown';

    // Call the deleteAnnouncement method
    $result = $adminAnnouncementModel->deleteAnnouncement($conn, $announcementId);

    if ($result) {
        // Log the activity
        $module = "Announcement";
        $activity = "Deleted an announcement with ID $announcementId.";
        $description = "Announcement ID: $announcementId, Subject: $subject, Recipients: $recipients, Schedule: $schedule was deleted.";
        $adminActivityLogModel->recordActivityLog($conn, $accountId, $module, $activity, $description);

        $_SESSION['message'] = "Announcement deleted successfully.";
        header("Location: adminAnnouncementController.php");
        exit;
    } else {
        $_SESSION['message'] = "Failed to delete the announcement.";
        header("Location: adminAnnouncementController.php");
        exit;
    }
} else {
    echo "Invalid request.";
}