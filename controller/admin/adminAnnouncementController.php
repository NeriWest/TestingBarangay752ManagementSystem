<?php
require_once '../../config/sessionCheck.php';
require '../../model/admin/adminAnnouncementModel.php';
require '../../model/admin/adminActivityLogModel.php';
require_once '../../config/sessionCheck.php';
require_once '../../config/permissionUtils.php';

// Initialize database connection


// Initialize the announcement model
$adminAnnouncementModel = new AdminAnnouncementModel();

// Define the number of records per page (you can modify this value as needed)
$recordsPerPage = 13;

// Get the current page from the GET request (default to page 1 if not set)
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

// Calculate the offset for the SQL query
$offset = ($page - 1) * $recordsPerPage;

// Get the announcements data (with pagination)
$announcements = $adminAnnouncementModel->showAnnouncementWithPagination($conn, $offset, $recordsPerPage);

// Get the total number of announcements (for pagination calculation)
$totalAnnouncements = $adminAnnouncementModel->getTotalAnnouncements($conn);

// Calculate total pages
$totalPages = ceil($totalAnnouncements / $recordsPerPage);

// Determine whether to show pagination
$showPagination = $totalPages > 1; // Show pagination only if there is more than one page

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
        $schedule = trim(stripslashes(htmlspecialchars($_POST['schedule']))); // Make sure to sanitize this as well
        $announcementType = trim(stripslashes(htmlspecialchars($_POST['announcement_type']))); // Assuming this is also a form field
        $accountId = intval($_POST['account_id']); // Assuming account_id is an integer

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

        // Determine the status based on the schedule
        $currentDateTime = new DateTime(); // Current datetime
        if ($scheduledDateTime > $currentDateTime) {
            $status = 'scheduled'; // Set as 'scheduled' if the time is in the future
        } else {
            $status = 'sent'; // Set as 'sent' if the time is now or in the past
        }

        // Define the announcement data
        $announcementData = [
            'account_id' => $accountId, // Replace with the logged-in account's ID
            'recipient_group' => $recipients,
            'subject' => $subject,
            'message_body' => $messageBody,
            'schedule' => $formattedSchedule, // Formatted schedule
            'status' => $status,
            'announcement_type' => $announcementType,
            'date_created' => $dateCreated,
            'last_updated' => $lastUpdated
        ];

        // Insert the announcement into the database
        if ($adminAnnouncementModel->insertAnnouncement($conn, $announcementData)) {
            $adminActivityLogModel = new ActivityLogModel();
            $module = "Announcement";
            $activity = "Created a new announcement";
            $description = "Subject: $subject, Recipients: $recipients, Schedule: $formattedSchedule";
            $adminActivityLogModel->recordActivityLog($conn, $accountId, $module, $activity, $description);
            session_start();
            $_SESSION['message'] = "New announcement has been successfully added.";
            header('Location: adminAnnouncementController.php');
        } else {
            echo "Failed to create announcement.";
        }
    } else {
        echo "Required form data missing.";
    }
    exit(); // Prevent further execution
}


//CAN BE USED TO GET PHONE NUMBER IN SMS
// // Call the resident model for my search resident
// $adminResidentModel = new AdminResidentModel();

// // Get the residents data (for displaying the resident list on the same page)
// $residents = $adminResidentModel->showResident($conn);


require '../../public/views/admin/adminAnnouncements.php';
?>
