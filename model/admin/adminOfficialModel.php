<?php
require_once 'dbconnection.php';

$db = new dbcon();
$conn = $db->getConnection();

class AdminOfficialModel {
    
    public function showRoles($conn) {
        $query = "SELECT * FROM roles WHERE role_name NOT IN ('Residents', 'Admin')";
        $result = $conn->query($query);

        if ($result === FALSE) {
            return []; // Return an empty array if query fails
        }

        $roles = [];
        while ($role = $result->fetch_assoc()) {
            // Check if the role is 'Chairman' or 'Secretary'
            if (in_array($role['role_name'], ['Chairman', 'Secretary'])) {
                // Verify if there is already an account with this role
                $checkQuery = "SELECT COUNT(*) AS count 
                               FROM accounts a
                               JOIN Residents r ON a.account_id = r.account_id
                               JOIN Officials o ON r.resident_id = o.resident_id
                               WHERE a.role_id = ?";
                $stmt = $conn->prepare($checkQuery);
                $stmt->bind_param("i", $role['role_id']);
                $stmt->execute();
                $checkResult = $stmt->get_result();
                $row = $checkResult->fetch_assoc();

                // If no account exists with this role, add it to the roles array
                if ($row['count'] == 0) {
                    $roles[] = $role;
                }
            } else {
                // Add other roles directly
                $roles[] = $role;
            }
        }

        return $roles; // Return the filtered list of roles
    }

    // Function to show all officials
    public function showOfficials($conn) {
        $query = "SELECT *, 
                 COALESCE(revoked_by, 'N/A') AS revoked_by, 
                 COALESCE(revoked_at, 'N/A') AS revoked_at, 
                 COALESCE(revoke_reason, 'N/A') AS revoke_reason 
              FROM Officials 
              ORDER BY last_updated DESC";
        $result = $conn->query($query);

        if ($result === FALSE) {
            return []; // Return an empty array if query fails
        }

        $officials = [];
        while ($official = $result->fetch_assoc()) {
            $officials[] = $official;
        }

        return $officials;
    }

    public function showActiveOfficials($conn) {
        $query = "SELECT o.*, 
             r.first_name, r.middle_name, r.last_name 
              FROM Officials o
              LEFT JOIN Residents r ON o.resident_id = r.resident_id
              WHERE o.status = 'active'
              ORDER BY o.last_updated DESC";
        $result = $conn->query($query);

        if ($result === FALSE) {
            return []; // Return an empty array if query fails
        }

        $officials = [];
        while ($official = $result->fetch_assoc()) {
            $officials[] = $official;
        }

        return $officials;
    }

    // Function to show officials with pagination and joined resident details
    public function showOfficialsWithPagination($conn, $offset, $limit) {
        // Define the paginated query with joined resident details
        $query = "SELECT o.*, 
              COALESCE(CONCAT(r2.first_name, ' ', r2.middle_name, ' ', r2.last_name, ' (', roles.role_name, ')'), 'N/A') AS revoked_by, 
              COALESCE(o.revoked_at, 'N/A') AS revoked_at, 
              COALESCE(o.revoke_reason, 'N/A') AS revoke_reason, 
              r.first_name, r.middle_name, r.last_name, 
              a.status AS account_status 
              FROM Officials o
              LEFT JOIN Residents r ON o.resident_id = r.resident_id
              LEFT JOIN accounts a ON r.account_id = a.account_id
              LEFT JOIN accounts a2 ON o.revoked_by = a2.account_id
              LEFT JOIN Residents r2 ON a2.account_id = r2.account_id
              LEFT JOIN roles ON a2.role_id = roles.role_id
              WHERE a.account_id IS NOT NULL AND a.status IN ('active', 'inactive')
              ORDER BY o.last_updated DESC 
              LIMIT ?, ?";

        // Prepare and execute the query
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $offset, $limit); // Bind offset and limit
        $stmt->execute();
        $result = $stmt->get_result();

        // Fetch all officials data
        $officials = [];
        while ($official = $result->fetch_assoc()) {
            $officials[] = $official; // Store each official in the array
        }

        return $officials; // Return the list of officials
    }
    // Function to get the total number of officials (for pagination calculation)
    public function getTotalOfficials($conn) {
        // Count the total number of officials
        $query = "SELECT COUNT(*) AS total FROM Officials";
        $result = $conn->query($query);
        $row = $result->fetch_assoc();

        return $row['total']; // Return the total number of officials
    }


    // Function to add a new official and update the account role_id using resident_id
    public function createOfficial($conn, $officialData) {
        // Start a transaction
        $conn->begin_transaction();

        try {
            // Insert the new official into the Officials table, including account_id
            $query = "INSERT INTO Officials (resident_id, account_id, position, term_start, term_end, status) 
                      VALUES (?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($query);
            $stmt->bind_param(
                "iissss", 
                $officialData['resident_id'], 
                $officialData['account_id'], 
                $officialData['specify_position'], 
                $officialData['term_start'], 
                $officialData['term_end'], 
                $officialData['status']
            );

            if (!$stmt->execute()) {
                throw new Exception("Error inserting official: " . $stmt->error);
            }

            // Update the role_id in the accounts table using the resident_id
            $updateQuery = "UPDATE accounts a
                            JOIN Residents r ON a.account_id = r.account_id
                            SET a.role_id = ?
                            WHERE r.resident_id = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param(
                "ii", 
                $officialData['position'], // role_id corresponds to position
                $officialData['resident_id']
            );

            if (!$updateStmt->execute()) {
                throw new Exception("Error updating account role_id: " . $updateStmt->error);
            }

            // Commit the transaction
            $conn->commit();
            return true;

        } catch (Exception $e) {
            // Rollback the transaction in case of an error
            $conn->rollback();
            echo $e->getMessage();
            return false;
        }
    }

    // Function to update an existing official
    public function updateOfficial($conn, $officialData) {
        $query = "UPDATE Officials SET 
                  position = ?, 
                  term_start = ?, 
                  term_end = ?, 
                  status = ? 
                  WHERE official_id = ?";

        $stmt = $conn->prepare($query);
        $stmt->bind_param(
            "ssssi", 
            $officialData['position'], 
            $officialData['term_start'], 
            $officialData['term_end'], 
            $officialData['status'], 
            $officialData['official_id']
        );

        if ($stmt->execute()) {
            return true;
        } else {
            echo "Error updating official: " . $stmt->error;
            return false;
        }
    }

    // Function to delete an official
    public function deleteOfficial($conn, $official_id) {
        $query = "DELETE FROM Officials WHERE official_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $official_id);

        if ($stmt->execute()) {
            return true;
        } else {
            echo "Error deleting official: " . $stmt->error;
            return false;
        }
    }

    // Function to search officials without pagination
    public function searchOfficials($conn, $query) {
        $searchQuery = "SELECT o.*, 
                r.first_name, r.middle_name, r.last_name, 
                COALESCE(o.revoked_by, 'N/A') AS revoked_by, 
                COALESCE(o.revoked_at, 'N/A') AS revoked_at, 
                COALESCE(o.revoke_reason, 'N/A') AS revoke_reason, 
                a.status AS account_status
                FROM Officials o
                LEFT JOIN Residents r ON o.resident_id = r.resident_id
                LEFT JOIN accounts a ON r.account_id = a.account_id
                WHERE (o.official_id = ?)
                OR (o.status = ?)
                OR (o.position LIKE ? 
                OR r.first_name LIKE ? 
                OR r.middle_name LIKE ? 
                OR r.last_name LIKE ? 
                OR o.term_start LIKE ? 
                OR o.term_end LIKE ? 
                OR o.revoked_by LIKE ? 
                OR o.revoked_at LIKE ? 
                OR o.revoke_reason LIKE ?)
                ORDER BY o.last_updated DESC";

        $stmt = $conn->prepare($searchQuery);
        $searchTerm = "%{$query}%";
        $stmt->bind_param(
            "issssssssss", 
            $query, 
            $query, $searchTerm, $searchTerm, $searchTerm, $searchTerm, 
            $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm
        );

        if ($stmt->execute()) {
            $result = $stmt->get_result();
        } else {
            echo "Error executing query: " . $stmt->error;
            return [];
        }

        $officials = [];
        while ($official = $result->fetch_assoc()) {
            $officials[] = $official;
        }

        return $officials;
    }
}
?>
