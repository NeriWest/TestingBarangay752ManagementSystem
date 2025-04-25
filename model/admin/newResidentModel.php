<?php
require_once 'dbconnection.php';

$db = new dbcon();
$conn = $db->getConnection();

class newResidentModel {
    private $showResidentQuery;

    // FOR PDF PRINTING - Get all residents without pagination
    public function showResidents($conn) {
        $query = "SELECT *, TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) AS age
                 FROM Residents
                 ORDER BY date_and_time_created_registration DESC";

        $result = $conn->query($query);
        return $result === FALSE ? [] : $result->fetch_all(MYSQLI_ASSOC);
    }

    // Get all senior residents
    public function showSenior($conn) {
        $query = "SELECT *, TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) AS age
                 FROM Residents
                 WHERE TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) >= 60
                 ORDER BY date_and_time_created_registration DESC";

        $result = $conn->query($query);
        return $result === FALSE ? [] : $result->fetch_all(MYSQLI_ASSOC);
    }

    // Get paginated residents
    public function showResidentWithPagination($conn, $offset, $limit) {
        $query = "SELECT *, TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) AS age
                  FROM Residents
                  ORDER BY date_and_time_created_registration DESC
                  LIMIT ?, ?";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $offset, $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Count total residents
    public function getTotalResidents($conn) {
        $result = $conn->query("SELECT COUNT(*) AS total FROM Residents");
        return $result->fetch_assoc()['total'];
    }

    // Get paginated senior residents
    public function showSeniorsWithPagination($conn, $offset, $limit) {
        $query = "SELECT *, TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) AS age
                  FROM Residents
                  WHERE TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) >= 60
                  ORDER BY date_and_time_created_registration DESC
                  LIMIT ?, ?";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $offset, $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Count total senior residents
    public function getTotalSeniors($conn) {
        $result = $conn->query("SELECT COUNT(*) AS total FROM Residents 
                               WHERE TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) >= 60");
        return $result ? $result->fetch_assoc()['total'] : 0;
    }

    // Get residents without accounts
    public function showResidentWithNoAccount($conn) {
        $query = "SELECT *, TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) AS age
                  FROM Residents
                  WHERE account_id IS NULL
                  ORDER BY date_and_time_created_registration DESC";

        $result = $conn->query($query);
        return $result === FALSE ? [] : $result->fetch_all(MYSQLI_ASSOC);
    }

    // Get non-official residents
    public function showNonOfficialResidents($conn) {
        $query = "SELECT r.*, TIMESTAMPDIFF(YEAR, r.date_of_birth, CURDATE()) AS age
                  FROM Residents r
                  LEFT JOIN Officials o ON r.resident_id = o.resident_id
                  WHERE o.resident_id IS NULL
                  ORDER BY r.date_and_time_created_registration DESC";

        $result = $conn->query($query);
        return $result === FALSE ? [] : $result->fetch_all(MYSQLI_ASSOC);
    }

    // Search non-official residents
    public function searchNonOfficialResidents($conn, $query) {
        $searchQuery = "SELECT r.*, TIMESTAMPDIFF(YEAR, r.date_of_birth, CURDATE()) AS age
                       FROM Residents r
                       LEFT JOIN Officials o ON r.resident_id = o.resident_id
                       WHERE o.resident_id IS NULL
                       AND (r.first_name LIKE ? OR r.last_name LIKE ? OR r.middle_name LIKE ?)
                       ORDER BY r.date_and_time_created_registration DESC";

        $stmt = $conn->prepare($searchQuery);
        $searchTerm = "%{$query}%";
        $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Add new resident
    public function insertResident($conn, $residentData) {
        $query = "INSERT INTO Residents 
                 (first_name, middle_name, last_name, suffix, date_of_birth, sex, 
                  citizenship, civil_status, house_number, street, occupation, 
                  disability, cellphone_number, email, voter_status, osca_id, 
                  osca_date_issued, status) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($query);
        $stmt->bind_param(
            "ssssssssssssssssss", 
            $residentData['first_name'], 
            $residentData['middle_name'], 
            $residentData['last_name'],
            $residentData['suffix'], 
            $residentData['date_of_birth'],
            $residentData['sex'], 
            $residentData['citizenship'], 
            $residentData['civil_status'],
            $residentData['house_number'], 
            $residentData['street'],
            $residentData['occupation'], 
            $residentData['disability'], 
            $residentData['cellphone_number'],
            $residentData['email'], 
            $residentData['voter_status'],
            $residentData['osca_id'],
            $residentData['osca_date_issued'],
            $residentData['status']
        );

        return $stmt->execute();
    }

    // Search residents
    public function searchResidents($conn, $query) {
        $searchQuery = "SELECT *, TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) AS age 
                        FROM Residents 
                        WHERE (first_name LIKE ? 
                            OR middle_name LIKE ? 
                            OR last_name LIKE ? 
                            OR suffix LIKE ? 
                            OR cellphone_number LIKE ? 
                            OR sex = ? 
                            OR CONCAT(house_number, ' ', street) LIKE ? 
                            OR date_of_birth LIKE ? 
                            OR civil_status LIKE ? 
                            OR citizenship LIKE ? 
                            OR email LIKE ? 
                            OR occupation LIKE ? 
                            OR disability LIKE ? 
                            OR voter_status LIKE ? 
                            OR osca_id LIKE ? 
                            OR osca_date_issued LIKE ? 
                            OR status LIKE ?) 
                        ORDER BY date_and_time_created_registration DESC";
        
        $stmt = $conn->prepare($searchQuery);
        $searchTerm = "%{$query}%";
        $stmt->bind_param("sssssssssssssssss", 
            $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm,
            $query, $searchTerm, $searchTerm, $searchTerm, $searchTerm,
            $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm,
            $searchTerm, $searchTerm
        );
        
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Search residents without accounts
    public function searchResidentsWithNoAccount($conn, $query) {
        $searchQuery = "SELECT *, TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) AS age 
                        FROM Residents 
                        WHERE account_id IS NULL 
                        AND (first_name LIKE ? OR last_name LIKE ? OR middle_name LIKE ?) 
                        ORDER BY date_and_time_created_registration DESC";
    
        $stmt = $conn->prepare($searchQuery);
        $searchTerm = "%{$query}%";
        $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Update resident
    public function updateResident($conn, $residentData) {
        $query = "UPDATE Residents SET
                  first_name = ?,
                  middle_name = ?,
                  last_name = ?,
                  suffix = ?,
                  date_of_birth = ?,
                  cellphone_number = ?,
                  house_number = ?,
                  street = ?,
                  citizenship = ?,
                  occupation = ?,
                  sex = ?,
                  civil_status = ?,
                  disability = ?,
                  email = ?,
                  voter_status = ?,
                  status = ?  
                  WHERE resident_id = ?";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssssssssssssssi",
            $residentData['first_name'],
            $residentData['middle_name'],
            $residentData['last_name'],
            $residentData['suffix'],
            $residentData['date_of_birth'],
            $residentData['cellphone_number'],
            $residentData['house_number'],
            $residentData['street'],
            $residentData['citizenship'],
            $residentData['occupation'],
            $residentData['sex'],
            $residentData['civil_status'],
            $residentData['disability'],
            $residentData['email'], 
            $residentData['voter_status'], 
            $residentData['status'], 
            $residentData['resident_id']
        );

        return $stmt->execute();
    }

    // Get resident by ID
    public function getResidentById($conn, $residentId) {
        $stmt = $conn->prepare("SELECT * FROM Residents WHERE resident_id = ?");
        $stmt->bind_param("i", $residentId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Count new residents in last 7 days
    public function countNewResidents($conn) {
        $result = $conn->query("SELECT * AS newResidents 
                               FROM Residents 
                               WHERE DATE(date_and_time_created_registration) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
        return $result ? $result->fetch_assoc()['newResidents'] : 0;
    }
    
}



?>