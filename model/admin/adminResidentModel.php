<?php
require_once 'dbconnection.php';

$db = new dbcon();
$conn = $db->getConnection();

class AdminResidentModel
{
    private $showResidentQuery;

    //FOR PDF PRINTING
    // Function to get all residents data without pagination
    public function showResidents($conn)
    {
        // Define the query to fetch all residents
        $query = "SELECT r.*, TIMESTAMPDIFF(YEAR, r.date_of_birth, CURDATE()) AS age 
              FROM Residents r
              LEFT JOIN accounts a ON r.account_id = a.account_id
              WHERE a.status IN ('active', 'inactive') 
              ORDER BY r.date_and_time_created_registration DESC";

        // Execute the query
        $result = $conn->query($query);

        // Check if the result is valid
        if ($result === FALSE) {
            return []; // Return an empty array if query fails
        }

        // Fetch all residents data
        $residents = [];
        while ($resident = $result->fetch_assoc()) {
            $residents[] = $resident; // Store each resident in the array
        }

        return $residents; // Return the list of residents
    }

    public function showSenior($conn)
    {
        // Define the query to fetch all senior residents
        $query = "SELECT *, TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) AS age
            FROM Residents
            WHERE TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) >= 60
            ORDER BY date_and_time_created_registration DESC";

        // Execute the query
        $result = $conn->query($query);

        // Check if the result is valid
        if ($result === FALSE) {
            return []; // Return an empty array if query fails
        }

        // Fetch all residents data
        $residents = [];
        while ($resident = $result->fetch_assoc()) {
            $residents[] = $resident; // Store each resident in the array
        }

        return $residents; // Return the list of residents
    }

    // Function to get paginated residents data
    public function showResidentWithPagination($conn, $offset, $limit)
    {
        // Define the paginated query
        $query = "SELECT r.*, 
             TIMESTAMPDIFF(YEAR, r.date_of_birth, CURDATE()) AS age, 
             CASE WHEN a.email = '' THEN 'N/A' ELSE a.email END AS email,
             CASE WHEN r.middle_name = '' THEN 'N/A' ELSE r.middle_name END AS middle_name,
             CASE WHEN r.suffix = '' THEN 'N/A' ELSE r.suffix END AS suffix,
             CASE WHEN r.osca_id = '' THEN 'N/A' ELSE r.osca_id END AS osca_id,
             CASE WHEN r.osca_date_issued = '' THEN 'N/A' ELSE r.osca_date_issued END AS osca_date_issued
              FROM Residents r
              JOIN accounts a ON r.account_id = a.account_id
              WHERE a.status IN ('active', 'inactive')
              ORDER BY r.date_and_time_updated_registration DESC
              LIMIT ?, ?";

        // Prepare and execute the query
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $offset, $limit); // Bind offset and limit
        $stmt->execute();
        $result = $stmt->get_result();

        // Fetch all residents data
        $residents = [];
        while ($resident = $result->fetch_assoc()) {
            $residents[] = $resident; // Store each resident in the array
        }

        return $residents; // Return the list of residents
    }

    // Function to get the total number of residents (for pagination calculation)
    public function getTotalResidents($conn)
    {
        // Count the total number of residents
        $query = "SELECT COUNT(*) AS total 
              FROM Residents r
              JOIN accounts a ON r.account_id = a.account_id
              WHERE a.status IN ('active', 'inactive')";
        $result = $conn->query($query);
        $row = $result->fetch_assoc();

        return $row['total']; // Return the total number of residents
    }

    public function showSeniorsWithPagination($conn, $offset, $limit)
    {
        // Define the query to filter seniors (age 60 and above)
        $query = "SELECT r.*, 
             TIMESTAMPDIFF(YEAR, r.date_of_birth, CURDATE()) AS age, 
             CASE WHEN a.email = '' THEN 'N/A' ELSE a.email END AS email,
             CASE WHEN r.middle_name = '' THEN 'N/A' ELSE r.middle_name END AS middle_name,
             CASE WHEN r.suffix = '' THEN 'N/A' ELSE r.suffix END AS suffix,
             CASE WHEN r.osca_id = '' THEN 'N/A' ELSE r.osca_id END AS osca_id,
             CASE WHEN r.osca_date_issued = '' THEN 'N/A' ELSE r.osca_date_issued END AS osca_date_issued
              FROM Residents r
              JOIN accounts a ON r.account_id = a.account_id
              WHERE a.status IN ('active', 'inactive') AND TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) >= 60    
              ORDER BY r.date_and_time_updated_registration DESC
              LIMIT ?, ?";


        // Prepare and execute the query
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $offset, $limit); // Bind offset and limit
        $stmt->execute();
        $result = $stmt->get_result();

        // Fetch all senior residents data
        $seniors = [];
        while ($senior = $result->fetch_assoc()) {
            $seniors[] = $senior; // Store each senior in the array
        }

        return $seniors; // Return the list of seniors
    }


    public function getTotalSeniors($conn)
    {
        // Count the total number of senior residents
        $query = "SELECT COUNT(*) AS total 
              FROM Residents r
              JOIN accounts a ON r.account_id = a.account_id
              WHERE a.status IN ('active', 'inactive') 
              AND TIMESTAMPDIFF(YEAR, r.date_of_birth, CURDATE()) >= 60";
        $result = $conn->query($query);

        $row = $result->fetch_assoc();
        return $row['total']; // Return the total number of senior residents
    }

    // Function to get paginated new residents with pending status
    public function showNewResidentsWithPagination($conn, $offset, $limit)
    {
        // Define the paginated query for new residents with pending status
        $query = "SELECT r.*, 
             TIMESTAMPDIFF(YEAR, r.date_of_birth, CURDATE()) AS age, 
             CASE WHEN a.email = '' THEN 'N/A' ELSE a.email END AS email,
             CASE WHEN r.middle_name = '' THEN 'N/A' ELSE r.middle_name END AS middle_name,
             CASE WHEN r.suffix = '' THEN 'N/A' ELSE r.suffix END AS suffix,
             CASE WHEN r.osca_id = '' THEN 'N/A' ELSE r.osca_id END AS osca_id,
             CASE WHEN r.osca_date_issued = '' THEN 'N/A' ELSE r.osca_date_issued END AS osca_date_issued
              FROM Residents r
              JOIN accounts a ON r.account_id = a.account_id
              WHERE a.status = 'pending'
              ORDER BY r.date_and_time_updated_registration DESC
              LIMIT ?, ?";

        // Prepare and execute the query
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $offset, $limit); // Bind offset and limit
        $stmt->execute();
        $result = $stmt->get_result();

        // Fetch all residents data
        $residents = [];
        while ($resident = $result->fetch_assoc()) {
            $residents[] = $resident; // Store each resident in the array
        }

        return $residents; // Return the list of residents
    }

    // Function to get the total number of residents (for pagination calculation)
    public function getTotalNewResidents($conn)
    {
        // Count the total number of residents
        $query = "SELECT COUNT(*) AS total 
              FROM Residents r
              JOIN accounts a ON r.account_id = a.account_id
              WHERE a.status = 'pending'";
        $result = $conn->query($query);
        $row = $result->fetch_assoc();

        return $row['total']; // Return the total number of residents
    }


    function showResidentWithNoAccount($conn)
    {
        // Properly define the query
        $this->showResidentQuery = "SELECT *, TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) AS age
                            FROM
                                Residents
                            WHERE account_id IS NULL
                            ORDER BY
                                date_and_time_created_registration DESC;";
        // Execute the query
        $result = $conn->query($this->showResidentQuery);

        // Check if the result is valid
        if ($result === FALSE) {
            return []; // Return an empty array if query fails
        }

        // Fetch all residents data
        $residents = [];
        while ($resident = $result->fetch_assoc()) {
            $residents[] = $resident; // Store each resident in the array
        }

        return $residents; // Return the list of residents
    }

    function showNonOfficialResidents($conn)
    {
        // Define the query to fetch residents who are not officials
        $query = "SELECT r.*, TIMESTAMPDIFF(YEAR, r.date_of_birth, CURDATE()) AS age, 
             a.status AS account_status
              FROM Residents r
              LEFT JOIN accounts a ON r.account_id = a.account_id
              WHERE r.status = 'active' AND a.role_id = 4 AND a.status IN ('active', 'inactive')
              ORDER BY r.last_name ASC";

        // Execute the query
        $result = $conn->query($query);

        // Check if the result is valid
        if ($result === FALSE) {
            return []; // Return an empty array if query fails
        }

        // Fetch all non-official residents data
        $residents = [];
        while ($resident = $result->fetch_assoc()) {
            $residents[] = $resident; // Store each resident in the array
        }

        return $residents; // Return the list of non-official residents
    }


    function searchNonOfficialResidents($conn, $query)
    {
        $searchQuery = "SELECT r.*, TIMESTAMPDIFF(YEAR, r.date_of_birth, CURDATE()) AS age, 
                       a.status AS account_status
                FROM Residents r
                LEFT JOIN accounts a ON r.account_id = a.account_id
                WHERE r.status = 'active' AND a.role_id = 4 AND a.status IN ('active', 'inactive')
                AND (r.first_name LIKE ? 
                    OR r.last_name LIKE ? 
                    OR r.middle_name LIKE ?)
                ORDER BY r.last_name ASC";

        $stmt = $conn->prepare($searchQuery);
        $searchTerm = "%{$query}%";
        $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
        } else {
            echo "Error executing query: " . $stmt->error;
            return [];
        }

        $residents = [];
        while ($resident = $result->fetch_assoc()) {
            $residents[] = $resident;
        }

        return $residents;
    }


    function showResidentsWithNoFamily($conn)
    {
        $query = "SELECT r.*, TIMESTAMPDIFF(YEAR, r.date_of_birth, CURDATE()) AS age 
              FROM Residents r
              LEFT JOIN accounts a ON r.account_id = a.account_id
              WHERE r.family_id IS NULL AND a.status IN ('active', 'inactive') 
              ORDER BY r.last_name ASC";

        $result = $conn->query($query);

        if ($result === FALSE) {
            return []; // Return an empty array if query fails
        }

        $residents = [];
        while ($resident = $result->fetch_assoc()) {
            $residents[] = $resident;
        }

        return $residents;
    }

    function searchResidentsWithNoFamily($conn, $query)
    {
        $searchQuery = "SELECT *, TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) AS age 
            FROM Residents 
            WHERE family_id IS NULL 
            AND (first_name LIKE ? 
                OR last_name LIKE ? 
                OR middle_name LIKE ? 
                OR suffix LIKE ? 
                OR cellphone_number LIKE ? 
                OR sex LIKE ?) 
            ORDER BY last_name ASC";

        $stmt = $conn->prepare($searchQuery);
        $searchTerm = "%{$query}%";
        $stmt->bind_param("ssssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
        } else {
            echo "Error executing query: " . $stmt->error;
            return [];
        }

        $residents = [];
        while ($resident = $result->fetch_assoc()) {
            $residents[] = $resident;
        }

        return $residents;
    }

    function searchResidents($conn, $query)
    {
        $searchQuery = "SELECT r.*, 
                TIMESTAMPDIFF(YEAR, r.date_of_birth, CURDATE()) AS age, 
                CASE WHEN a.email = '' THEN 'N/A' ELSE a.email END AS email,
                CASE WHEN r.middle_name = '' THEN 'N/A' ELSE r.middle_name END AS middle_name,
                CASE WHEN r.suffix = '' THEN 'N/A' ELSE r.suffix END AS suffix,
                CASE WHEN r.osca_id = '' THEN 'N/A' ELSE r.osca_id END AS osca_id,
                CASE WHEN r.osca_date_issued = '' THEN 'N/A' ELSE r.osca_date_issued END AS osca_date_issued
                FROM Residents r
                JOIN accounts a ON r.account_id = a.account_id
                WHERE a.status IN ('active', 'inactive') 
                AND (r.first_name LIKE ? 
                    OR r.middle_name LIKE ? 
                    OR r.last_name LIKE ? 
                    OR r.suffix LIKE ? 
                    OR r.cellphone_number LIKE ? 
                    OR r.sex = ? 
                    OR TIMESTAMPDIFF(YEAR, r.date_of_birth, CURDATE()) LIKE ? 
                    OR CONCAT(r.house_number, ' ', r.street) LIKE ? 
                    OR r.date_of_birth LIKE ? 
                    OR r.civil_status LIKE ? 
                    OR r.citizenship LIKE ? 
                    OR r.occupation LIKE ? 
                    OR r.disability LIKE ? 
                    OR r.voter_status LIKE ? 
                    OR r.osca_id LIKE ? 
                    OR r.osca_date_issued LIKE ? 
                    OR r.status LIKE ? 
                    OR a.email LIKE ?)
                ORDER BY r.date_and_time_updated_registration DESC";

        $stmt = $conn->prepare($searchQuery);
        $searchTerm = "%{$query}%";

        // Bind parameters for all the fields, including email
        $stmt->bind_param(
            "ssssssssssssssssss",
            $searchTerm,   // first_name
            $searchTerm,   // middle_name
            $searchTerm,   // last_name
            $searchTerm,   // suffix
            $searchTerm,   // cellphone_number
            $query,        // sex (exact match)
            $searchTerm,   // age (calculated)
            $searchTerm,   // address (concatenated house number and street)
            $searchTerm,   // date_of_birth
            $searchTerm,   // civil_status
            $searchTerm,   // citizenship
            $searchTerm,   // occupation
            $searchTerm,   // disability
            $searchTerm,   // voter_status
            $searchTerm,   // osca_id
            $searchTerm,   // osca_date_issued
            $searchTerm,   // status
            $searchTerm    // email
        );

        // Execute and fetch results
        if ($stmt->execute()) {
            $result = $stmt->get_result();
        } else {
            echo "Error executing query: " . $stmt->error;
            return [];
        }

        $residents = [];
        while ($resident = $result->fetch_assoc()) {
            $residents[] = $resident;
        }

        return $residents;
    }

    function searchSeniorResidents($conn, $query)
    {
        $searchQuery = "SELECT r.*, 
                TIMESTAMPDIFF(YEAR, r.date_of_birth, CURDATE()) AS age, 
                CASE WHEN a.email = '' THEN 'N/A' ELSE a.email END AS email,
                CASE WHEN r.middle_name = '' THEN 'N/A' ELSE r.middle_name END AS middle_name,
                CASE WHEN r.suffix = '' THEN 'N/A' ELSE r.suffix END AS suffix,
                CASE WHEN r.osca_id = '' THEN 'N/A' ELSE r.osca_id END AS osca_id,
                CASE WHEN r.osca_date_issued = '' THEN 'N/A' ELSE r.osca_date_issued END AS osca_date_issued
                FROM Residents r
                JOIN accounts a ON r.account_id = a.account_id
                WHERE a.status IN ('active', 'inactive') 
                AND TIMESTAMPDIFF(YEAR, r.date_of_birth, CURDATE()) >= 60
                AND (r.first_name LIKE ? 
                    OR r.middle_name LIKE ? 
                    OR r.last_name LIKE ? 
                    OR r.suffix LIKE ? 
                    OR r.cellphone_number LIKE ? 
                    OR r.sex = ? 
                    OR TIMESTAMPDIFF(YEAR, r.date_of_birth, CURDATE()) LIKE ? 
                    OR CONCAT(r.house_number, ' ', r.street) LIKE ? 
                    OR r.date_of_birth LIKE ? 
                    OR r.civil_status LIKE ? 
                    OR r.citizenship LIKE ? 
                    OR r.occupation LIKE ? 
                    OR r.disability LIKE ? 
                    OR r.voter_status LIKE ? 
                    OR r.osca_id LIKE ? 
                    OR r.osca_date_issued LIKE ? 
                    OR r.status LIKE ? 
                    OR a.email LIKE ?)
                ORDER BY r.date_and_time_updated_registration DESC";

        $stmt = $conn->prepare($searchQuery);
        $searchTerm = "%{$query}%";

        // Bind parameters for all the fields, including email
        $stmt->bind_param(
            "ssssssssssssssssss",
            $searchTerm,   // first_name
            $searchTerm,   // middle_name
            $searchTerm,   // last_name
            $searchTerm,   // suffix
            $searchTerm,   // cellphone_number
            $query,        // sex (exact match)
            $searchTerm,   // age (calculated)
            $searchTerm,   // address (concatenated house number and street)
            $searchTerm,   // date_of_birth
            $searchTerm,   // civil_status
            $searchTerm,   // citizenship
            $searchTerm,   // occupation
            $searchTerm,   // disability
            $searchTerm,   // voter_status
            $searchTerm,   // osca_id
            $searchTerm,   // osca_date_issued
            $searchTerm,   // status
            $searchTerm    // email
        );

        // Execute and fetch results
        if ($stmt->execute()) {
            $result = $stmt->get_result();
        } else {
            echo "Error executing query: " . $stmt->error;
            return [];
        }

        $residents = [];
        while ($resident = $result->fetch_assoc()) {
            $residents[] = $resident;
        }

        return $residents;
    }

    function searchNewResidents($conn, $query)
    {
        $searchQuery = "SELECT r.*, 
                TIMESTAMPDIFF(YEAR, r.date_of_birth, CURDATE()) AS age, 
                CASE WHEN a.email = '' THEN 'N/A' ELSE a.email END AS email,
                CASE WHEN r.middle_name = '' THEN 'N/A' ELSE r.middle_name END AS middle_name,
                CASE WHEN r.suffix = '' THEN 'N/A' ELSE r.suffix END AS suffix,
                CASE WHEN r.osca_id = '' THEN 'N/A' ELSE r.osca_id END AS osca_id,
                CASE WHEN r.osca_date_issued = '' THEN 'N/A' ELSE r.osca_date_issued END AS osca_date_issued
                FROM Residents r
                JOIN accounts a ON r.account_id = a.account_id
                WHERE a.status = 'pending'
                AND (r.first_name LIKE ? 
                    OR r.middle_name LIKE ? 
                    OR r.last_name LIKE ? 
                    OR r.suffix LIKE ? 
                    OR r.cellphone_number LIKE ? 
                    OR r.sex = ? 
                    OR TIMESTAMPDIFF(YEAR, r.date_of_birth, CURDATE()) LIKE ? 
                    OR CONCAT(r.house_number, ' ', r.street) LIKE ? 
                    OR r.date_of_birth LIKE ? 
                    OR r.civil_status LIKE ? 
                    OR r.citizenship LIKE ? 
                    OR r.occupation LIKE ? 
                    OR r.disability LIKE ? 
                    OR r.voter_status LIKE ? 
                    OR r.osca_id LIKE ? 
                    OR r.osca_date_issued LIKE ? 
                    OR r.status LIKE ? 
                    OR a.email LIKE ?)
                ORDER BY r.date_and_time_updated_registration DESC";

        $stmt = $conn->prepare($searchQuery);
        $searchTerm = "%{$query}%";

        // Bind parameters for all the fields, including email
        $stmt->bind_param(
            "ssssssssssssssssss",
            $searchTerm,   // first_name
            $searchTerm,   // middle_name
            $searchTerm,   // last_name
            $searchTerm,   // suffix
            $searchTerm,   // cellphone_number
            $query,        // sex (exact match)
            $searchTerm,   // age (calculated)
            $searchTerm,   // address (concatenated house number and street)
            $searchTerm,   // date_of_birth
            $searchTerm,   // civil_status
            $searchTerm,   // citizenship
            $searchTerm,   // occupation
            $searchTerm,   // disability
            $searchTerm,   // voter_status
            $searchTerm,   // osca_id
            $searchTerm,   // osca_date_issued
            $searchTerm,   // status
            $searchTerm    // email
        );

        // Execute and fetch results
        if ($stmt->execute()) {
            $result = $stmt->get_result();
        } else {
            echo "Error executing query: " . $stmt->error;
            return [];
        }

        $residents = [];
        while ($resident = $result->fetch_assoc()) {
            $residents[] = $resident;
        }

        return $residents;
    }


    function searchResidentsWithNoAccount($conn, $query)
    {
        $searchQuery = "SELECT *, TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) AS age 
                        FROM Residents 
                        WHERE account_id IS NULL 
                        AND (first_name LIKE ? 
                            OR last_name LIKE ? 
                            OR middle_name LIKE ? 
                            OR suffix LIKE ? 
                            OR cellphone_number LIKE ? 
                            OR sex LIKE ?) 
                        ORDER BY date_and_time_created_registration DESC";

        $stmt = $conn->prepare($searchQuery);
        $searchTerm = "%{$query}%";
        $stmt->bind_param("ssssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
        } else {
            echo "Error executing query: " . $stmt->error;
            return [];
        }

        $residents = [];
        while ($resident = $result->fetch_assoc()) {
            $residents[] = $resident;
        }

        return $residents;
    }

    function searchOfficialResidentsWithNoAccount($conn, $query)
    {
        $searchQuery = "SELECT r.*, TIMESTAMPDIFF(YEAR, r.date_of_birth, CURDATE()) AS age, 
                o.position, o.term_start, o.term_end, o.status AS official_status
                FROM Residents r
                LEFT JOIN Officials o ON r.resident_id = o.resident_id
                WHERE r.account_id IS NULL AND o.resident_id IS NOT NULL
                AND (r.first_name LIKE ? 
                    OR r.last_name LIKE ? 
                    OR r.middle_name LIKE ? 
                    OR r.suffix LIKE ? 
                    OR r.cellphone_number LIKE ? 
                    OR r.sex LIKE ?)
                ORDER BY r.date_and_time_created_registration DESC";

        $stmt = $conn->prepare($searchQuery);
        $searchTerm = "%{$query}%";
        $stmt->bind_param("ssssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
        } else {
            echo "Error executing query: " . $stmt->error;
            return [];
        }

        $residents = [];
        while ($resident = $result->fetch_assoc()) {
            $residents[] = $resident;
        }

        return $residents;
    }


    public function updateResident($conn, $residentData)
    {
        $query = "UPDATE Residents r
                JOIN accounts a ON r.account_id = a.account_id
                SET
                r.first_name = ?,
                r.middle_name = ?,
                r.last_name = ?,
                r.suffix = ?,
                r.date_of_birth = ?,
                r.cellphone_number = ?,
                r.house_number = ?,
                r.street = ?,
                r.citizenship = ?,
                r.occupation = ?,
                r.sex = ?,
                r.civil_status = ?,
                r.disability = ?,
                r.osca_id = ?,
                r.osca_date_issued = ?,
                a.email = ?,
                r.voter_status = ?,
                r.status = ?  
              WHERE r.resident_id = ?";

        $stmt = $conn->prepare($query);
        $stmt->bind_param(
            "ssssssssssssssssssi",
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
            $residentData['osca_id'],
            $residentData['osca_date_issued'],
            $residentData['email'],
            $residentData['voter_status'],
            $residentData['status'],
            $residentData['resident_id']
        );

        return $stmt->execute();
    }

    public function showResidentsByFamilyId($conn, $familyId)
    {
        $query = "SELECT *, TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) AS age 
                  FROM Residents 
                  WHERE family_id = ? 
                  ORDER BY last_name ASC";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $familyId);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
        } else {
            echo "Error executing query: " . $stmt->error;
            return [];
        }

        $residentList = [];
        while ($row = $result->fetch_assoc()) {
            $residentList[] = [
                'resident_id' => $row['resident_id'],
                'first_name' => $row['first_name'],
                'last_name' => $row['last_name'],
                'age' => $row['age'],
                'house_number' => $row['house_number'],
                'street' => $row['street'],
                'family_id' => $row['family_id']
            ];
        }

        return $residentList;
    }


    public function getResidentById($conn, $residentId)
    {
        $query = "SELECT * FROM Residents WHERE resident_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $residentId);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }


    public function totalResidents($conn)
    {
        // Define the query to fetch all residents
        $query = "SELECT r.*, TIMESTAMPDIFF(YEAR, r.date_of_birth, CURDATE()) AS age, a.email, a.date_and_time_created AS date_registered FROM Residents r LEFT JOIN Accounts a ON r.account_id = a.account_id WHERE r.status = 'active' OR r.status ='bedridden'

ORDER BY date_and_time_created DESC
;
";

        // Execute the query
        $result = $conn->query($query);

        // Check if the result is valid
        if ($result === FALSE) {
            return []; // Return an empty array if query fails
        }

        // Fetch all residents data
        $residents = [];
        while ($resident = $result->fetch_assoc()) {
            $residents[] = $resident; // Store each resident in the array
        }

        return $residents; // Return the list of residents
    }

    // GET NEW RESIDENTS
    public function GETnewResident($conn)
    {
        $query = "SELECT r.*, TIMESTAMPDIFF(YEAR, r.date_of_birth, CURDATE()) AS age, a.email, a.date_and_time_created AS date_registered FROM Residents r LEFT JOIN Accounts a ON r.account_id = a.account_id WHERE DATE(date_and_time_created) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) AND r.status = 'active' OR r.status ='bedridden' ORDER BY r.date_and_time_created_registration DESC;";

        $result = $conn->query($query);
        if ($result === FALSE) {
            return []; // Return an empty array if query fails
        }

        $residents = [];
        while ($resident = $result->fetch_assoc()) {
            $residents[] = $resident; // Store each resident in the array
        }
        return $residents; // Return the list of residents

    }


    // GET SENIOR CITIZENS
    public function GETseniorCitizens($conn)
    {
        $query = "SELECT r.*, TIMESTAMPDIFF(YEAR, r.date_of_birth, CURDATE()) AS age, 
                  a.email, a.date_and_time_created AS date_registered 
                  FROM Residents r 
                  LEFT JOIN Accounts a ON r.account_id = a.account_id 
                  WHERE TIMESTAMPDIFF(YEAR, r.date_of_birth, CURDATE()) >= 60 AND r.status = 'active' OR r.status ='bedridden'
                  ORDER BY r.date_and_time_created_registration DESC";

        $result = $conn->query($query);

        if ($result === FALSE) {
            error_log("Database error: " . $conn->error);
            return []; // Always return array even if empty
        }

        $residents = [];
        while ($resident = $result->fetch_assoc()) {
            $residents[] = $resident;
        }

        return $residents;
    }

    // GET TOTAL ACCOUNTS
    public function GETtotalAccounts($conn)
    {
        $query = "SELECT 
                account_id,
                username,
                status,
                email,
                DATE_FORMAT(date_and_time_created, '%m/%d/%Y %h:%i%p') AS date_registered 
                FROM accounts 
                WHERE status = 'active' 
    OR status = 'bedridden'
                ORDER BY date_and_time_created DESC";

        $result = $conn->query($query);
        $accounts = [];
        while ($account = $result->fetch_assoc()) {
            $accounts[] = $account;
        }

        return [
            'data' => $accounts,
            'query' => $query,
            'error' => null
        ];
    }
    public function GETnewAccounts($conn)
    {
        $query = "SELECT 
                account_id,
                username,
                status,
                email,
                DATE_FORMAT(date_and_time_created, '%m/%d/%Y %h:%i%p') AS date_registered 
                FROM accounts
                WHERE DATE(date_and_time_created) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) 
                AND status = 'active' 
    OR status = 'bedridden'
                ORDER BY date_and_time_created DESC";

        $result = $conn->query($query);
        $accounts = [];
        while ($account = $result->fetch_assoc()) {
            $accounts[] = $account;
        }

        return [
            'data' => $accounts,
            'query' => $query,
            'error' => null
        ];
    }

    public function GETnewPendingAccounts($conn)
    {
        $query = "SELECT 
                account_id,
                username,
                status,
                email,
                DATE_FORMAT(date_and_time_created, '%m/%d/%Y %h:%i%p') AS date_registered 
                FROM accounts
                WHERE status = 'pending' AND DATE(date_and_time_created) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) 
                ORDER BY date_and_time_created DESC";

        $result = $conn->query($query);
        $accounts = [];
        while ($account = $result->fetch_assoc()) {
            $accounts[] = $account;
        }

        return [
            'data' => $accounts,
            'query' => $query,
            'error' => null
        ];
    }

    public function GETtotalPendingAccounts($conn)
    {
        $query = "SELECT 
                account_id,
                username,
                status,
                email,
                DATE_FORMAT(date_and_time_created, '%m/%d/%Y %h:%i%p') AS date_registered 
                FROM accounts
                WHERE status = 'pending' 
                ORDER BY date_and_time_created DESC";

        $result = $conn->query($query);
        $accounts = [];
        while ($account = $result->fetch_assoc()) {
            $accounts[] = $account;
        }

        return [
            'data' => $accounts,
            'query' => $query,
            'error' => null
        ];
    }



    public function GETfamilies($conn)
    {
        $query = "SELECT 
                    f.family_id,
                    f.family_name,
                    f.house_number,
                    f.street,
                    DATE_FORMAT(f.created_at, '%m/%d/%Y %h:%i%p') AS created_at,
                    GROUP_CONCAT(CONCAT(r.first_name, ' ', r.last_name) AS family_members
                  FROM families f
                  LEFT JOIN residents r ON f.family_id = r.family_id
                  GROUP BY f.family_id, f.family_name, f.house_number, f.street, f.created_at
                  ORDER BY f.created_at DESC";

        $result = $conn->query($query);
        if ($result === FALSE) {
            error_log("Database error in GETfamilies: " . $conn->error);
            return [];
        }

        $families = [];
        while ($row = $result->fetch_assoc()) {
            // Ensure family_members is not null
            $row['family_members'] = $row['family_members'] ?? 'No members';
            $families[] = $row;
        }
        return $families;
    }

    public function GETnewPermitsToday($conn, $offset, $limit)
    {
        $query = "SELECT 
    request_id, 
    CONCAT(first_name, ' ', last_name) AS full_name,
    CASE 
        WHEN cr.template_id = 2 THEN 'Business Permit'
WHEN cr.template_id = 8 THEN 'Building Permit'
WHEN cr.template_id = 9 THEN 'Business Permit'
WHEN cr.template_id = 10 THEN 'Occupancy Permit'
WHEN cr.template_id = 11 THEN 'Sanitary Permit'
WHEN cr.template_id = 12 THEN 'Fire Safety Permit'
WHEN cr.template_id = 13 THEN 'Electrical Permit'
WHEN cr.template_id = 14 THEN 'Zoning Permit'
        ELSE 'Unknown Template' 
    END AS template_name,
purpose,
DATE_FORMAT(date_requested, '%m/%d/%Y') AS date_submitted,
DATE_FORMAT(last_updated, '%m/%d/%Y') AS last_updated,
CASE
	WHEN cr.payment_type_id = 1 THEN 'GCash'
	WHEN cr.payment_type_id = 2 THEN 'Cash Payment'
    ELSE 'Testing'
    END AS payment_name,
    payment_amount,
    number_of_copies,
    cr.status
FROM 
    Certificate_Requests cr 
JOIN 
    Residents r ON cr.resident_id = r.resident_id 
LEFT JOIN 
    templates t ON cr.template_id = t.id 
WHERE cr.template_id IN (2, 8, 9, 10, 11, 12, 13, 14)
 AND DATE(date_requested) = CURDATE()";



        $result = $conn->query($query);

        // Check if the query was successful
        if (!$result) {
            // Output error message if query fails
            die("Query Failed: " . $conn->error);
        }

        $requests = [];
        while ($row = $result->fetch_assoc()) {
            $requests[] = $row; // Store each row in the $requests array
        }

        return $requests;
    }

    public function GETclearancesToday($conn, $offset, $limit)
    {
        $query = "SELECT 
    request_id, 
    CONCAT(first_name, ' ', last_name) AS full_name,
    CASE 
WHEN cr.template_id = 3 THEN 'Barangay Clearance'
WHEN cr.template_id = 4 THEN 'Barangay Business Clearance'
        ELSE 'Unknown Template' 
    END AS template_name,
purpose,
DATE_FORMAT(date_requested, '%m/%d/%Y') AS date_submitted,
DATE_FORMAT(last_updated, '%m/%d/%Y') AS last_updated,
CASE
	WHEN cr.payment_type_id = 1 THEN 'GCash'
	WHEN cr.payment_type_id = 2 THEN 'Cash Payment'
    ELSE 'Testing'
    END AS payment_name,
    payment_amount,
    number_of_copies,
    cr.status
FROM 
    Certificate_Requests cr 
JOIN 
    Residents r ON cr.resident_id = r.resident_id 
LEFT JOIN 
    templates t ON cr.template_id = t.id 
WHERE cr.template_id IN (3, 4)
AND DATE(date_requested) = CURDATE()
 ;



";

        $result = $conn->query($query);

        // Check if the query was successful
        if (!$result) {
            // Output error message if query fails
            die("Query Failed: " . $conn->error);
        }

        $requests = [];
        while ($row = $result->fetch_assoc()) {
            $requests[] = $row; // Store each row in the $requests array
        }

        return $requests;
    }


    public function GETcertificatesToday($conn, $offset, $limit)
    {
        $query = "SELECT 
    request_id, 
    CONCAT(first_name, ' ', last_name) AS full_name,
    CASE 
WHEN cr.template_id = 1 THEN 'Barangay Certificate'
WHEN cr.template_id = 5 THEN 'Barangay Indigency Certificate'
WHEN cr.template_id = 6 THEN 'Barangay Residency Certificate'
WHEN cr.template_id = 7 THEN 'Barangay Certificate of Good Standing'
        ELSE 'Unknown Template' 
    END AS template_name,
purpose,
DATE_FORMAT(date_requested, '%m/%d/%Y') AS date_submitted,
DATE_FORMAT(last_updated, '%m/%d/%Y') AS last_updated,
CASE
	WHEN cr.payment_type_id = 1 THEN 'GCash'
	WHEN cr.payment_type_id = 2 THEN 'Cash Payment'
    ELSE 'Testing'
    END AS payment_name,
    payment_amount,
    number_of_copies,
    cr.status
FROM 
    Certificate_Requests cr 
JOIN 
    Residents r ON cr.resident_id = r.resident_id 
LEFT JOIN 
    templates t ON cr.template_id = t.id 
WHERE cr.template_id IN (1, 5,6,7)
AND DATE(date_requested) = CURDATE()
 ;



";

        $result = $conn->query($query);

        // Check if the query was successful
        if (!$result) {
            // Output error message if query fails
            die("Query Failed: " . $conn->error);
        }

        $requests = [];
        while ($row = $result->fetch_assoc()) {
            $requests[] = $row; // Store each row in the $requests array
        }

        return $requests;
    }


    public function GETrequestsToday($conn, $offset, $limit)
    {
        $query = "SELECT 
    cr.request_id, 
    CONCAT(r.first_name, ' ', r.last_name) AS full_name,
    t.doc_name AS template_name,
    cr.purpose,
    DATE_FORMAT(cr.date_requested, '%m/%d/%Y') AS date_submitted,
    DATE_FORMAT(cr.last_updated, '%m/%d/%Y') AS last_updated,
    CASE
        WHEN cr.payment_type_id = 1 THEN 'GCash'
        WHEN cr.payment_type_id = 2 THEN 'Cash Payment'
        ELSE 'Unknown Payment Method'
    END AS payment_name,
    cr.payment_amount,
    cr.number_of_copies,
    cr.status
FROM 
    Certificate_Requests cr 
JOIN 
    Residents r ON cr.resident_id = r.resident_id 
JOIN 
    templates t ON cr.template_id = t.id 
WHERE 
    DATE(cr.date_requested) = CURDATE()
    AND cr.status = 'pending'
ORDER BY
    cr.date_requested;




";

        $result = $conn->query($query);

        // Check if the query was successful
        if (!$result) {
            // Output error message if query fails
            die("Query Failed: " . $conn->error);
        }

        $requests = [];
        while ($row = $result->fetch_assoc()) {
            $requests[] = $row; // Store each row in the $requests array
        }

        return $requests;
    }

    public function GETincidentsReportedToday($conn, $offset, $limit)
    {
        $query = "SELECT 
            I.incident_id AS Case_number,
            I.complainant,
            I.complainant_type,
            I.subject,
            CONCAT(o.first_name, ' ', o.last_name) AS official_name,
            DATE_FORMAT(I.date_of_incident,'%m/%d/%Y') AS date_of_incident,
            DATE_FORMAT(I.time_of_incident,  '%h:%i %p') AS time_of_incident,
            I.location,
            I.person_involved AS Respondent,
            I.narration,
            E.evidence_description,
            I.remarks,
            I.status,
            DATE_FORMAT(I.date_and_time_created, '%m/%d/%Y %h:%i%p') AS date_submitted
        FROM 
            (SELECT * FROM Incident WHERE DATE(date_and_time_created) = CURDATE()) AS I
        LEFT JOIN 
            Evidence E ON I.incident_id = E.incident_id
        LEFT JOIN 
            (SELECT 
                o.official_id,
                r.first_name,
                r.middle_name,
                r.last_name 
             FROM 
                Officials o
             LEFT JOIN 
                Residents r ON o.resident_id = r.resident_id
            ) AS o ON I.official_id = o.official_id
        ORDER BY 
            I.date_and_time_updated DESC";

        // Remove LIMIT if you want all records (since you're passing 0,0)
        if ($limit > 0) {
            $query .= " LIMIT ?, ?";
        }

        try {
            $stmt = $conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }

            if ($limit > 0) {
                $stmt->bind_param("ii", $offset, $limit);
            }

            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }

            $result = $stmt->get_result();
            $reports = [];

            while ($row = $result->fetch_assoc()) {
                $reports[] = $row;
            }

            $stmt->close();
            return $reports;
        } catch (Exception $e) {
            error_log("Database Error: " . $e->getMessage());
            return [];
        }
    }



    public function GETblottersReportedToday($conn, $offset, $limit)
    {
        $query = "SELECT 
            b.case_id AS Case_number,
            b.complainant,
            b.complainant_type,
            b.subject,
            CONCAT(o.first_name, ' ', o.last_name) AS official_name,
            DATE_FORMAT(b.date_of_incident, '%m/%d/%Y') AS date_of_incident,
            DATE_FORMAT(b.time_of_incident, '%h:%i %p') AS time_of_incident,
            b.location,
            b.person_involved AS Respondent,
            b.narration,
            E.evidence_description,
            b.remarks,
            b.status,
            DATE_FORMAT(b.date_and_time_created, '%m/%d/%Y %h:%i%p') AS date_submitted
        FROM 
            (SELECT * FROM blotter WHERE DATE(date_and_time_created) = CURDATE()) AS b
        LEFT JOIN 
            Evidence E ON b.case_id = E.incident_id
        LEFT JOIN 
            (SELECT 
                o.official_id,
                r.first_name,
                r.middle_name,
                r.last_name 
             FROM 
                Officials o
             LEFT JOIN 
                Residents r ON o.resident_id = r.resident_id
            ) AS o ON b.official_id = o.official_id
        ORDER BY 
            b.date_and_time_updated DESC";

        // Remove LIMIT if you want all records (since you're passing 0,0)
        if ($limit > 0) {
            $query .= " LIMIT ?, ?";
        }

        try {
            $stmt = $conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }

            if ($limit > 0) {
                $stmt->bind_param("ii", $offset, $limit);
            }

            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }

            $result = $stmt->get_result();
            $reports = [];

            while ($row = $result->fetch_assoc()) {
                $reports[] = $row;
            }

            $stmt->close();
            return $reports;
        } catch (Exception $e) {
            error_log("Database Error: " . $e->getMessage());
            return [];
        }
    }


    public function GETcomplaintsReportedToday($conn, $offset, $limit)
    {
        $query = "SELECT 
            c.complaint_id AS Case_number,
            c.complainant,
            c.complainant_type,
            c.subject,
            CONCAT(o.first_name, ' ', o.last_name) AS official_name,
            DATE_FORMAT(c.date_of_incident, '%m/%d/%Y') AS date_of_incident,
            DATE_FORMAT(c.time_of_incident, '%h:%i %p') AS time_of_incident,
            c.location,
            c.person_involved AS Respondent,
            c.narration,
            E.evidence_description,
            c.remarks,
            c.status,
            DATE_FORMAT(c.date_and_time_created, '%m/%d/%Y %h:%i%p') AS date_submitted
        FROM 
            (SELECT * FROM complaint WHERE DATE(date_and_time_created) = CURDATE()) AS c
        LEFT JOIN 
            Evidence E ON c.complaint_id = E.incident_id
        LEFT JOIN 
            (SELECT 
                o.official_id,
                r.first_name,
                r.middle_name,
                r.last_name 
             FROM 
                Officials o
             LEFT JOIN 
                Residents r ON o.resident_id = r.resident_id
            ) AS o ON c.official_id = o.official_id
        ORDER BY 
            c.date_and_time_updated DESC";

        // Remove LIMIT if you want all records (since you're passing 0,0)
        if ($limit > 0) {
            $query .= " LIMIT ?, ?";
        }

        try {
            $stmt = $conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }

            if ($limit > 0) {
                $stmt->bind_param("ii", $offset, $limit);
            }

            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }

            $result = $stmt->get_result();
            $reports = [];

            while ($row = $result->fetch_assoc()) {
                $reports[] = $row;
            }

            $stmt->close();
            return $reports;
        } catch (Exception $e) {
            error_log("Database Error: " . $e->getMessage());
            return [];
        }
    }






    public function GETtotalPendingReportsToday($conn, $offset, $limit)
    {
        $query = "SELECT 
    c.complaint_id AS Case_number,
    c.complainant,
    c.complainant_type,
    c.subject,
    CONCAT(o.first_name, ' ', o.last_name) AS official_name,
    DATE_FORMAT(c.date_of_incident, '%m/%d/%Y') AS date_of_incident,
    DATE_FORMAT(c.time_of_incident, '%h:%i %p') AS time_of_incident,
    c.location,
    c.person_involved AS Respondent,
    c.narration,
    E.evidence_description,
    c.remarks,
    c.status,
    DATE_FORMAT(c.date_and_time_created, '%m/%d/%Y %h:%i%p') AS date_submitted,
    'Complaint' AS record_type
FROM 
    (SELECT * FROM complaint WHERE DATE(date_and_time_created) = CURDATE() AND status = 'pending') AS c
LEFT JOIN 
    Evidence E ON c.complaint_id = E.incident_id
LEFT JOIN 
    (SELECT 
        o.official_id,
        r.first_name,
        r.middle_name,
        r.last_name 
     FROM 
        Officials o
     LEFT JOIN 
        Residents r ON o.resident_id = r.resident_id
    ) AS o ON c.official_id = o.official_id

UNION

SELECT 
    b.case_id AS Case_number,
    b.complainant,
    b.complainant_type,
    b.subject,
    CONCAT(o.first_name, ' ', o.last_name) AS official_name,
    DATE_FORMAT(b.date_of_incident, '%m/%d/%Y') AS date_of_incident,
    DATE_FORMAT(b.time_of_incident, '%h:%i %p') AS time_of_incident,
    b.location,
    b.person_involved AS Respondent,
    b.narration,
    E.evidence_description,
    b.remarks,
    b.status,
    DATE_FORMAT(b.date_and_time_created, '%m/%d/%Y %h:%i%p') AS date_submitted,
    'Blotter' AS record_type
FROM 
    (SELECT * FROM blotter WHERE DATE(date_and_time_created) = CURDATE() AND status = 'pending') AS b
LEFT JOIN 
    Evidence E ON b.case_id = E.incident_id
LEFT JOIN 
    (SELECT 
        o.official_id,
        r.first_name,
        r.middle_name,
        r.last_name 
     FROM 
        Officials o
     LEFT JOIN 
        Residents r ON o.resident_id = r.resident_id
    ) AS o ON b.official_id = o.official_id

UNION

SELECT 
    I.incident_id AS Case_number,
    I.complainant,
    I.complainant_type,
    I.subject,
    CONCAT(o.first_name, ' ', o.last_name) AS official_name,
    DATE_FORMAT(I.date_of_incident, '%m/%d/%Y') AS date_of_incident,
    DATE_FORMAT(I.time_of_incident, '%h:%i %p') AS time_of_incident,
    I.location,
    I.person_involved AS Respondent,
    I.narration,
    E.evidence_description,
    I.remarks,
    I.status,
    DATE_FORMAT(I.date_and_time_created, '%m/%d/%Y %h:%i%p') AS date_submitted,
    'Incident' AS record_type
FROM 
    (SELECT * FROM Incident WHERE DATE(date_and_time_created) = CURDATE() AND status = 'pending') AS I
LEFT JOIN 
    Evidence E ON I.incident_id = E.incident_id
LEFT JOIN 
    (SELECT 
        o.official_id,
        r.first_name,
        r.middle_name,
        r.last_name 
     FROM 
        Officials o
     LEFT JOIN 
        Residents r ON o.resident_id = r.resident_id
    ) AS o ON I.official_id = o.official_id

ORDER BY date_submitted DESC;";

        // Remove LIMIT if you want all records (since you're passing 0,0)
        if ($limit > 0) {
            $query .= " LIMIT ?, ?";
        }

        try {
            $stmt = $conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }

            if ($limit > 0) {
                $stmt->bind_param("ii", $offset, $limit);
            }

            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }

            $result = $stmt->get_result();
            $reports = [];

            while ($row = $result->fetch_assoc()) {
                $reports[] = $row;
            }

            $stmt->close();
            return $reports;
        } catch (Exception $e) {
            error_log("Database Error: " . $e->getMessage());
            return [];
        }
    }
}

class ResidentViewProfileModel {
    public function showPersonalAccount($conn, $accountId) {
        $query = "
            SELECT 
                Residents.*, 
                Accounts.username, 
                Accounts.password, 
                Accounts.email,
                Accounts.role_id,
                Accounts.status AS account_status, 
                Accounts.profile_image_name, 
                Accounts.profile_image_blob, 
                Accounts.id_image_name, 
                Accounts.id_image_blob 
            FROM Residents 
            LEFT JOIN Accounts ON Residents.account_id = Accounts.account_id 
            WHERE Accounts.account_id = ?
        ";
    
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $conn->error);
        }
    
        $stmt->bind_param("i", $accountId);
        $stmt->execute();
        $result = $stmt->get_result();
        $personalAccount = $result->fetch_assoc();
        $stmt->close();
    
        return $personalAccount;
    }
}
