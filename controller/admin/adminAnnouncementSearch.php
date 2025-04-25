<?php
require_once '../../model/admin/adminAnnouncementModel.php';

// Initialize the announcement model
$adminAnnouncementModel = new AdminAnnouncementModel();

// Check if a search query is set
if (isset($_POST['query'])) {
    $query = $_POST['query'];

    // Fetch announcements based on the search query
    $announcements = $adminAnnouncementModel->searchAnnouncements($conn, $query);

    if (!empty($announcements)) {
        foreach ($announcements as $announcement) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($announcement['announcement_id']) . "</td>";
            echo "<td>" . htmlspecialchars($announcement['date_created']) . "</td>";
            echo "<td>" . (strlen($announcement['subject']) > 30 ? htmlspecialchars(substr($announcement['subject'], 0, 30)) . '...' : htmlspecialchars($announcement['subject'])) . "</td>";
            echo "<td>" . htmlspecialchars($announcement['recipient_group']) . "</td>";
            echo "<td>" . htmlspecialchars($announcement['announcement_type']) . "</td>";
            echo "<td>" . (strlen($announcement['message_body']) > 30 ? htmlspecialchars(substr($announcement['message_body'], 0, 30)) . '...' : htmlspecialchars($announcement['message_body'])) . "</td>";
            echo "<td>" . htmlspecialchars($announcement['schedule']) . "</td>";
            echo "<td>" . htmlspecialchars($announcement['status']) . "</td>";
            echo "<td>";
            echo '<div style="display: flex; gap: 10px;">';
            echo '<button class="modal-open button" aria-haspopup="true" 
                data-announcement-id="' . $announcement['announcement_id'] . '"
                data-subject="' . htmlspecialchars($announcement['subject']) . '"
                data-recipient="' . htmlspecialchars($announcement['recipient_group']) . '"
                data-announcement-type="' . htmlspecialchars($announcement['announcement_type']) . '"
                data-message-body="' . htmlspecialchars($announcement['message_body']) . '"
                data-schedule="' . htmlspecialchars($announcement['schedule']) . '"
                onclick="populateModal(this)"> 
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pen" viewBox="0 0 16 16">
                    <path d="m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001m-.644.766a.5.5 0 0 0-.707 0L1.95 11.756l-.764 3.057 3.057-.764L14.44 3.854a.5.5 0 0 0 0-.708z"/>
                </svg>
            </button>';
            echo '<button class="delete-button" 
                data-announcement-id="' . $announcement['announcement_id'] . '" 
                data-subject="' . htmlspecialchars($announcement['subject']) . '"
                data-recipient="' . htmlspecialchars($announcement['recipient_group']) . '"
                data-schedule="' . htmlspecialchars($announcement['schedule']) . '"
                onclick="showDeletePopup(' . $announcement['announcement_id'] . ', \'' . htmlspecialchars($announcement['subject']) . '\', \'' . htmlspecialchars($announcement['recipient_group']) . '\', \'' . htmlspecialchars($announcement['schedule']) . '\')">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                        <path d="M5.5 5.5A.5.5 0 0 1 6 5h4a.5.5 0 0 1 0 1H6a.5.5 0 0 1-.5-.5zm2.5 3a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0v-4a.5.5 0 0 1 .5-.5zm-2 0a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0v-4a.5.5 0 0 1 .5-.5z"/>
                    <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3V2h11v1h-11z"/>
                </svg>
            </button>';
            echo '</div>';
            echo "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='9'>No announcements found.</td></tr>";
    }

}
?>
