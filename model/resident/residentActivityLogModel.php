<?php
require_once 'dbconnection.php';

$db = new dbcon();
$conn = $db->getConnection();

class ActivityLogModel {
    private $showActivityLogQuery;

    // Function to show all activity logs
    function showActivityLog($conn) {
        // Query to fetch all activity logs
        $this->showActivityLogQuery = "SELECT * FROM ActivityLog ORDER BY date DESC, time DESC;";
        $result = $conn->query($this->showActivityLogQuery);

        // Check if the result is valid
        if ($result === FALSE) {
            return []; // Return an empty array if query fails
        }

        // Fetch all activity log data
        $activityLogs = [];
        while ($activityLog = $result->fetch_assoc()) {
            $activityLogs[] = $activityLog; // Store each activity log in the array
        }

        return $activityLogs; // Return the list of activity logs
    }

    // Function to show activity logs with pagination including account id, username, and role
    public function showActivityLogWithPagination($conn, $offset, $limit) {
        // Define the paginated query with account id, username, and role
        $query = "
        SELECT ActivityLog.*, 
               CONCAT(Account.username, ' - ', Roles.role_name) AS account_name, 
               Roles.role_name AS role_name
        FROM ActivityLog 
        LEFT JOIN accounts AS Account ON ActivityLog.account_id = Account.account_id 
        LEFT JOIN roles AS Roles ON Account.role_id = Roles.role_id
        ORDER BY date DESC, time DESC 
        LIMIT ?, ?";

        // Prepare and execute the query
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $offset, $limit); // Bind offset and limit
        $stmt->execute();
        $result = $stmt->get_result();

        // Fetch all activity log data
        $activityLogs = [];
        while ($activityLog = $result->fetch_assoc()) {
            $activityLogs[] = $activityLog; // Store each activity log in the array
        }

        return $activityLogs; // Return the list of activity logs
    }

    // Function to get the total number of activity logs (for pagination calculation)
    public function getTotalActivityLog($conn) {
        // Count the total number of activity logs
        $query = "SELECT COUNT(*) AS total FROM ActivityLog";
        $result = $conn->query($query);
        $row = $result->fetch_assoc();

        return $row['total']; // Return the total number of activity logs
    }

    // Function to search activity logs based on various fields
    public function searchActivityLogs($conn, $query) {
        $searchQuery = "
        SELECT ActivityLog.*, 
               CONCAT(Account.username, ' - ', Roles.role_name) AS account_name, 
               Roles.role_name AS role_name
        FROM ActivityLog 
        LEFT JOIN accounts AS Account ON ActivityLog.account_id = Account.account_id 
        LEFT JOIN roles AS Roles ON Account.role_id = Roles.role_id
        WHERE 
            (CAST(ActivityLog.log_id AS CHAR) LIKE ? 
            OR ActivityLog.account_id LIKE ? 
            OR ActivityLog.module LIKE ? 
            OR ActivityLog.activity LIKE ? 
            OR ActivityLog.date LIKE ? 
            OR ActivityLog.time LIKE ? 
            OR ActivityLog.description LIKE ?
            OR Account.username LIKE ? 
            OR Roles.role_name LIKE ?)
        ORDER BY ActivityLog.log_id DESC, ActivityLog.date DESC, ActivityLog.time DESC";

        $stmt = $conn->prepare($searchQuery);
        $searchTerm = "%{$query}%";

        // Bind parameters for the search
        $stmt->bind_param("sssssssss", 
            $searchTerm, // log_id (casted to CHAR for partial matching)
            $searchTerm, // account_id
            $searchTerm, // module
            $searchTerm, // activity
            $searchTerm, // date
            $searchTerm, // time
            $searchTerm, // description
            $searchTerm, // username (account_name)
            $searchTerm  // role_name
        );

        // Execute and fetch results
        if ($stmt->execute()) {
            $result = $stmt->get_result();
        } else {
            echo "Error executing query: " . $stmt->error;
            return [];
        }

        $activityLogs = [];
        while ($activityLog = $result->fetch_assoc()) {
            $activityLogs[] = $activityLog;
        }

        return $activityLogs;
    }

    // Function to insert a new activity log
    public function recordActivityLog($conn, $account_id, $module, $activity, $description) {
        // Query to insert a new activity log
        $query = "INSERT INTO ActivityLog (account_id, module, activity, description) 
                  VALUES (?, ?, ?, ?)";

        // Prepare and execute the query
        $stmt = $conn->prepare($query);
        $stmt->bind_param("isss", $account_id, $module, $activity, $description);

        // Execute the statement and check for success
        if ($stmt->execute()) {
            return true; // Return true if insertion is successful
        } else {
            echo "Error inserting activity log: " . $stmt->error;
            return false; // Return false if insertion fails
        }
    }
}
?>
