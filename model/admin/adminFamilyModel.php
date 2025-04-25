
<?php
require_once 'dbconnection.php';

$db = new dbcon();
$conn = $db->getConnection();

class AdminFamilyModel {
    // Function to show all families
    public function showFamilies($conn) {
        $query = "SELECT * FROM Families ORDER BY updated_at DESC";
        $result = $conn->query($query);

        if ($result === FALSE) {
            return []; // Return an empty array if query fails
        }

        $families = [];
        while ($family = $result->fetch_assoc()) {
            $families[] = $family;
        }

        return $families;
    }

    public function showFamiliesWithPagination($conn, $offset, $limit) {
        $query = "SELECT 
                Families.family_id, 
                Families.family_name, 
                Families.house_number, 
                Families.street, 
                Families.created_at,
                COALESCE(SUM(Residents.salary), 0) AS total_income,
                GROUP_CONCAT(
                CONCAT(
                    Residents.first_name, 
                    ' ', 
                    COALESCE(Residents.middle_name, ''), 
                    ' ', 
                    Residents.last_name
                ) SEPARATOR '<br>'
                ) AS residents,
                GROUP_CONCAT(CONCAT(Residents.resident_id, ' - ', Residents.first_name, ' ', Residents.middle_name, ' ', Residents.last_name) SEPARATOR '<br>') AS detailed_residents,
                GROUP_CONCAT(CONCAT(Residents.resident_id, ' - ', Residents.first_name, ' ', COALESCE(Residents.middle_name, ''), ' ', Residents.last_name) SEPARATOR '<br>') AS residents
             FROM Families 
             LEFT JOIN Residents ON Families.family_id = Residents.family_id 
             GROUP BY Families.family_id 
             ORDER BY Families.updated_at DESC 
             LIMIT ?, ?";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $offset, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $families = [];
        while ($row = $result->fetch_assoc()) {
            // Remove number_format here - keep as float
            $row['total_income'] = (float)$row['total_income'];
            $families[] = $row;
        }
        
        return $families;
        }

    // Function to get the total number of families
    public function getTotalFamilies($conn) {
        $query = "SELECT COUNT(*) AS total FROM Families";
        $result = $conn->query($query);
        $row = $result->fetch_assoc();

        return $row['total'];
    }

    // Function to add a new family
    public function createFamily($conn, $familyData) {
        $query = "INSERT INTO Families (family_name, house_number, street) 
                VALUES (?, ?, ?)";

        $stmt = $conn->prepare($query);
        $stmt->bind_param(
            "sss",
            $familyData['family_name'],
            $familyData['house_number'],
            $familyData['street']
        );

        if ($stmt->execute()) {
            // Return the ID of the newly inserted family
            return $conn->insert_id;
        } else {
            echo "Error inserting family: " . $stmt->error;
            return false;
        }
    }


    // Function to assign a resident to a family
    public function assignResidentToFamily($conn, $familyId, $residentId) {
        $query = "UPDATE Residents SET family_id = ? WHERE resident_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $familyId, $residentId);

        if ($stmt->execute()) {
            return true;
        } else {
            echo "Error updating resident's family_id: " . $stmt->error;
            return false;
        }
    }
    // Function to update an existing family and correct family_id in residents if needed
    public function updateFamily($conn, $familyData) {
        // Update the family details
        $query = "UPDATE Families SET 
                  family_name = ?, 
                  house_number = ?, 
                  street = ? 
                  WHERE family_id = ?";

        $stmt = $conn->prepare($query);
        $stmt->bind_param(
            "sssi",
            $familyData['family_name'],
            $familyData['house_number'],
            $familyData['street'],
            $familyData['family_id']
        );

        if ($stmt->execute()) {
            // Check if the family_id exists in Residents table
            $checkQuery = "SELECT COUNT(*) AS count FROM Residents WHERE family_id = ?";
            $checkStmt = $conn->prepare($checkQuery);
            $checkStmt->bind_param("i", $familyData['family_id']);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
            $row = $checkResult->fetch_assoc();

            // If no residents are assigned to the family, reset family_id in Residents table
            if ($row['count'] == 0) {
                $resetQuery = "UPDATE Residents SET family_id = NULL WHERE family_id = ?";
                $resetStmt = $conn->prepare($resetQuery);
                $resetStmt->bind_param("i", $familyData['family_id']);
                $resetStmt->execute();
            }

            return true;
        } else {
            echo "Error updating family: " . $stmt->error;
            return false;
        }
    }

    public function removeResidentFromFamily($conn, $residentId) {
        $query = "UPDATE Residents SET family_id = NULL WHERE resident_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $residentId);

        if ($stmt->execute()) {
            return true;
        } else {
            echo "Error removing resident from family: " . $stmt->error;
            return false;
        }
    }

    

    // Function to delete a family
    public function deleteFamily($conn, $family_id) {
        $query = "DELETE FROM Families WHERE family_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $family_id);

        if ($stmt->execute()) {
            return true;
        } else {
            echo "Error deleting family: " . $stmt->error;
            return false;
        }
    }

    
    // Function to search families without pagination and make it full name searchable, including created_at and residents
    public function searchFamilies($conn, $query) {
        $searchQuery = "SELECT 
                        Families.family_id, 
                        Families.family_name, 
                        Families.house_number, 
                        Families.street, 
                        Families.created_at,
                        COALESCE(SUM(Residents.salary), 0) AS total_income,
                        GROUP_CONCAT(
                            CONCAT(
                                Residents.first_name, 
                                ' ', 
                                COALESCE(Residents.middle_name, ''), 
                                ' ', 
                                Residents.last_name
                            ) SEPARATOR '<br>'
                        ) AS residents,
                        GROUP_CONCAT(Residents.resident_id SEPARATOR ',') AS resident_ids,
                        GROUP_CONCAT(CONCAT(Residents.resident_id, ' - ', Residents.first_name, ' ', Residents.middle_name, ' ', Residents.last_name) SEPARATOR '<br>') AS detailed_residents,
                        GROUP_CONCAT(CONCAT(Residents.resident_id, ' - ', Residents.first_name, ' ', COALESCE(Residents.middle_name, ''), ' ', Residents.last_name) SEPARATOR '<br>') AS residents
                        FROM Families 
                        LEFT JOIN Residents ON Families.family_id = Residents.family_id 
                        WHERE Families.family_name LIKE ? 
                        OR Families.house_number LIKE ? 
                        OR Families.street LIKE ? 
                        OR Families.created_at LIKE ?
                        OR CONCAT(Residents.first_name, ' ', COALESCE(Residents.middle_name, ''), ' ', Residents.last_name) LIKE ? 
                        GROUP BY Families.family_id 
                        ORDER BY Families.updated_at DESC";

        $stmt = $conn->prepare($searchQuery);
        $searchTerm = "%{$query}%";
        $stmt->bind_param("sssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();

        $families = [];
        while ($row = $result->fetch_assoc()) {
            $row['total_income'] = (float)$row['total_income'];
            $families[] = $row;
        }

        return $families;
    }

    public function showAllFamilies($conn) {
        $query = "SELECT 
                    Families.family_id, 
                    Families.family_name, 
                    Families.house_number, 
                    Families.street, 
                    Families.created_at,
                    COALESCE(SUM(Residents.salary), 0) AS total_income,
                    GROUP_CONCAT(
                        CONCAT(
                            Residents.first_name, 
                            ' ', 
                            COALESCE(Residents.middle_name, ''), 
                            ' ', 
                            Residents.last_name
                        ) SEPARATOR '<br>'
                    ) AS residents,
                    GROUP_CONCAT(Residents.resident_id SEPARATOR ',') AS resident_ids
                 FROM Families 
                 LEFT JOIN Residents ON Families.family_id = Residents.family_id 
                 GROUP BY Families.family_id 
                 ORDER BY Families.updated_at DESC";
    
        $result = $conn->query($query);
    
        if (!$result) {
            throw new Exception("Database error: " . $conn->error);
        }
    
        $families = [];
        while ($row = $result->fetch_assoc()) {
            $row['total_income'] = (float)$row['total_income'];
            $families[] = $row;
        }
    
        return $families;
    }

  


}
?>
