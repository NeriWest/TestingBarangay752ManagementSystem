<?php
require '../../model/admin/adminAnnouncementModel.php';
require_once '../../model/admin/adminAnnouncementModel.php';
require_once '../../model/admin/adminActivityLogModel.php';


// Initialize the announcement model
$adminAnnouncementModel = new AdminAnnouncementModel();

// Get the updated announcements data (for displaying in the view)
$announcements = $adminAnnouncementModel->showAnnouncement($conn);

// Check if it's a POST request (for form submission)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Log all incoming data for debugging (optional)
    error_log(print_r($_POST, true));

    // Check if the required form fields are received
    if (isset($_POST['subject']) && isset($_POST['message_body']) && isset($_POST['schedule'])) {
        // Retrieve and sanitize form inputs
        $recipients = trim(stripslashes(htmlspecialchars($_POST['recipients'])));
        $subject = trim(stripslashes(htmlspecialchars($_POST['subject'])));
        $messageBody = trim(stripslashes(htmlspecialchars($_POST['message_body'])));
        $schedule = $_POST['schedule'];
        $accountId = trim(stripslashes(htmlspecialchars($_POST['account_id'])));
        $announcementType = trim(stripslashes(htmlspecialchars($_POST['announcement_type']))); // Assuming this is also a form field
        $announcementId = trim(stripslashes(htmlspecialchars($_POST['announcement_id'])));


        // Convert the schedule to DateTime and format it for the database
        try {
            $scheduledDateTime = new DateTime($schedule);
            $formattedSchedule = $scheduledDateTime->format('Y-m-d H:i:s'); // Format for MySQL DATETIME field
        } catch (Exception $e) {
            echo "Invalid date format.";
            exit(); // Exit if the schedule is invalid
        }

        // Get the current date and time for creation and update
        $dateCreated = date('Y-m-d H:i:s');
        $lastUpdated = $dateCreated;

        // Define the announcement data
        $announcementData = [
            'account_id' => $accountId, // Replace with the logged-in account's ID
            'recipient_group' => $recipients,
            'subject' => $subject,
            'message_body' => $messageBody,
            'schedule' => $schedule,
            'announcement_type' => $announcementType,
            'announcement_id' => $announcementId, // Replace with the logged-in account's ID
        ];

        // Insert the announcement into the database
        if ($adminAnnouncementModel->updateAnnouncement($conn, $announcementData)) {
            $adminActivityLogModel = new ActivityLogModel();
            $module = "Announcement";
            $activity = "Updated a new announcement with a subject " . $announcementData['subject'] . ".";
            $description = "Subject: $subject, Recipients: $recipients, Schedule: $formattedSchedule";
            $adminActivityLogModel->recordActivityLog($conn, $accountId, $module, $activity, $description);
            session_start();
            if (strlen($announcementData['subject']) > 12) {
                $_SESSION['message'] = "The announcement with a subject " . substr($announcementData['subject'], 0, 12) . "... has been successfully updated.";
            } else {
                $_SESSION['message'] = "The announcement with a subject " . $announcementData['subject'] . " has been successfully updated.";
            }
            header('Location: adminAnnouncementController.php');
        } else {
            echo "Failed to create announcement";
        }
    } else {
        echo "Required form data missing";
    }
    exit(); // Prevent further execution
}
?>
