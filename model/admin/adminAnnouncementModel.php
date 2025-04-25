<?php
require_once 'dbconnection.php';

$db = new dbcon();
$conn = $db->getConnection();

class AdminAnnouncementModel {
    private $showAnnouncementQuery;

    function showAnnouncement($conn) {
        // Fetch the announcements without updating their status
        $this->showAnnouncementQuery = "SELECT * FROM Announcements ORDER BY date_created DESC;";
        $result = $conn->query($this->showAnnouncementQuery);
    
        // Check if the result is valid
        if ($result === FALSE) {
            return []; // Return an empty array if query fails
        }
    
        // Fetch all announcements data
        $announcements = [];
        while ($announcement = $result->fetch_assoc()) {
            $announcements[] = $announcement; // Store each announcement in the array
        }
    
        return $announcements; // Return the list of announcements
    }

   // Function to get paginated announcements data
    public function showAnnouncementWithPagination($conn, $offset, $limit) {
        // Define the paginated query for announcements with dynamic status alias as 'status'
        $query = "SELECT 
            announcement_id, 
            subject, 
            recipient_group, 
            message_body, 
            schedule, 
            date_created,
            announcement_type,
            (CASE 
            WHEN schedule <= NOW() THEN 'sent' 
            ELSE 'scheduled' 
            END) AS status
          FROM Announcements 
          ORDER BY last_updated DESC 
          LIMIT ?, ?";


        // Prepare and execute the query
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            error_log("Failed to prepare statement: " . $conn->error); // Log error if preparation fails
            return []; // Return an empty array in case of failure
        }
        $stmt->bind_param("ii", $offset, $limit); // Bind offset and limit
        $stmt->execute();
        $result = $stmt->get_result();

        // Fetch all announcements data
        $announcements = [];
        while ($announcement = $result->fetch_assoc()) {
            $announcements[] = $announcement; // Store each announcement in the array
        }

        return $announcements; // Return the list of announcements
    }



    // Function to get the total number of announcements (for pagination calculation)
    public function getTotalAnnouncements($conn) {
        // Count the total number of announcements
        $query = "SELECT COUNT(*) AS total FROM Announcements";
        $result = $conn->query($query);
        $row = $result->fetch_assoc();

        return $row['total']; // Return the total number of announcements
    }

    function insertAnnouncement($conn, $announcementData) {
        // Prepare the query
        $query = "INSERT INTO Announcements 
            (account_id, recipient_group, subject, message_body, schedule, date_created, last_updated, announcement_type) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        if ($stmt = $conn->prepare($query)) {
            // Bind the parameters
            // "i" for integer (account_id), "s" for strings
            $stmt->bind_param(
                "isssssss", // Data types: "i" for account_id (int), rest are strings
                $announcementData['account_id'], 
                $announcementData['recipient_group'], 
                $announcementData['subject'], 
                $announcementData['message_body'], 
                $announcementData['schedule'], 
                $announcementData['date_created'], 
                $announcementData['last_updated'],
                $announcementData['announcement_type']
            );
    
            // Execute the query and handle the result
            if ($stmt->execute()) {
                return true;
            } else {
                error_log("Failed to create announcement: " . $stmt->error); // Log error
                return false;
            }
        } else {
            error_log("Prepare failed: " . $conn->error); // Log error if prepare fails
            return false;
        }
    }
    
    // Function to search announcements by subject, message_body, recipient_group, or status
    function searchAnnouncements($conn, $query) {
        $searchQuery = "SELECT 
                    announcement_id, 
                    subject, 
                    recipient_group, 
                    message_body, 
                    schedule, 
                    date_created,
                    announcement_type,
                    (CASE 
                    WHEN schedule <= NOW() THEN 'sent' 
                    ELSE 'scheduled' 
                    END) AS status
                FROM Announcements 
                WHERE (schedule LIKE ? OR subject LIKE ? OR message_body LIKE ? OR recipient_group LIKE ? OR announcement_type LIKE ? OR 
                      (CASE 
                      WHEN schedule <= NOW() THEN 'sent' 
                      ELSE 'scheduled' 
                      END) LIKE ?)
                ORDER BY last_updated DESC";

        // Prepare the query statement
        $stmt = $conn->prepare($searchQuery);
        
        // Create the search term with wildcards
        $searchTerm = "%{$query}%";
        
        // Bind parameters to the prepared statement (for subject, message_body, recipient_group, announcement_type, and status)
        $stmt->bind_param("ssssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);

        // Execute the query and check if successful
        if ($stmt->execute()) {
            $result = $stmt->get_result();  // Get the result set
        } else {
            // Handle error in case of a failed query execution
            echo "Error executing query: " . $stmt->error;
            return [];  // Return an empty array in case of error
        }

        // Fetch all announcements into an array
        $announcements = [];
        while ($announcement = $result->fetch_assoc()) {
            $announcements[] = $announcement;  // Append each row to the announcements array
        }

        // Return the array of announcements
        return $announcements;
    }


    // Function to update an existing announcement
    public function updateAnnouncement($conn, $announcementData) {
        $query = "UPDATE Announcements SET
                account_id = ?,
                recipient_group = ?,
                subject = ?,
                message_body = ?,
                schedule = ?,
                last_updated = ?,
                announcement_type = ?
              WHERE announcement_id = ?";

        $stmt = $conn->prepare($query);
        $stmt->bind_param(
            "issssssi",
            $announcementData['account_id'], 
            $announcementData['recipient_group'], 
            $announcementData['subject'], 
            $announcementData['message_body'], 
            $announcementData['schedule'], 
            $announcementData['last_updated'], 
            $announcementData['announcement_type'], 
            $announcementData['announcement_id']
        );

        return $stmt->execute();
    }

    // Function to get a specific announcement by ID
    public function getAnnouncementById($conn, $announcementId) {
        $query = "SELECT * FROM Announcements WHERE announcement_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $announcementId);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }

    // Function to delete an announcement by ID
    public function deleteAnnouncement($conn, $announcementId) {
        $query = "DELETE FROM Announcements WHERE announcement_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $announcementId);

        return $stmt->execute();
    }
}
?>
