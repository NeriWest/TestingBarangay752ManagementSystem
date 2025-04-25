<?php
require_once '../../model/admin/adminActivityLogModel.php';

// Initialize the activity log model
$adminActivityLogModel = new ActivityLogModel();

// Check if a search query is set
if (isset($_POST['query'])) {
    $query = $_POST['query'];

    // Fetch activity logs based on the search query
    $activityLogs = $adminActivityLogModel->searchActivityLogs($conn, $query);

    if (!empty($activityLogs)) {
        foreach ($activityLogs as $log) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($log['log_id']) . "</td>";
            echo "<td>" . htmlspecialchars($log['module']) . "</td>";
            echo "<td>" . htmlspecialchars($log['account_name']) . "</td>";
            echo "<td>" . htmlspecialchars($log['activity']) . "</td>";
            echo "<td>" . date("m/d/Y", strtotime(htmlspecialchars($log['date']))) . "</td>";
            echo "<td>" . date("g:i A", strtotime(htmlspecialchars($log['time']))) . "</td>";
            echo "<td>" . htmlspecialchars($log['description']) . "</td>";
            echo "<td>
                <button class='modal-open button' style='background-color:rgb(92, 92, 92);' aria-haspopup='true' 
                    data-log-id='" . htmlspecialchars($log['log_id']) . "' 
                    data-module='" . htmlspecialchars($log['module']) . "' 
                    data-account-name='" . htmlspecialchars($log['account_name']) . "' 
                    data-activity='" . htmlspecialchars($log['activity']) . "' 
                    data-date='" . htmlspecialchars($log['date']) . "' 
                    data-time='" . htmlspecialchars($log['time']) . "' 
                    data-description='" . htmlspecialchars($log['description']) . "' 
                    onclick='populateBlotterModal(this)'>
                    <i class='fa-solid fa-eye'></i>
                </button>
            </td>";


            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='8'>No activity logs found.</td></tr>";
    }
}
?>
