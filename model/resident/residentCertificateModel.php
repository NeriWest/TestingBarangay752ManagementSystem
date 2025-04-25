<?php
require_once 'dbconnection.php';

$db = new dbcon();
$conn = $db->getConnection();

class CertificateRequestModel {
    private $requestQuery;

    // Function to get all certificate requests 
    public function showCertificateRequests($conn) {
        $this->requestQuery = "SELECT request_id, certificate_type, purpose, number_of_copies, 
                   date_requested, status, payment_amount, remarks, date_issued 
                   FROM Certificate_Requests
                   ORDER BY date_requested DESC";
        $result = $conn->query($this->requestQuery);

        if ($result === FALSE) {
            return []; // Return an empty array if the query fails
        }

        $requests = [];
        while ($request = $result->fetch_assoc()) {
            $requests[] = $request;
        }

        return $requests; // Return the list of requests
    }

    public function showCertificateRequestsWithPagination($conn, $offset, $limit, $residentId) {
        // Define the paginated query with a filter for a specific resident
        $this->requestQuery = "SELECT 
            cr.request_id, 
            t.doc_name AS document_name, 
            CONCAT(r.first_name, ' ', 
                   IFNULL(r.middle_name, ''), 
                   IF(r.middle_name IS NOT NULL AND r.middle_name != '', ' ', ''), 
                   r.last_name, 
                   IF(r.suffix IS NOT NULL AND r.suffix != '', CONCAT(' ', r.suffix), '')
            ) AS name_requested, 
            cr.purpose, 
            cr.date_requested AS date_submitted, 
            pt.name AS payment_type, 
            cr.payment_amount, 
            cr.number_of_copies AS no_of_copies, 
            cr.status, 
            cr.remarks,
            t.price AS template_price, 
            cr.template_id, 
            cr.payment_type_id,  
            cr.proof_of_payment, 
            cr.last_updated
        FROM Certificate_Requests cr
        JOIN Residents r ON cr.resident_id = r.resident_id
        LEFT JOIN templates t ON cr.template_id = t.id
        LEFT JOIN payment_types pt ON cr.payment_type_id = pt.id
        WHERE cr.template_id IS NOT NULL AND cr.resident_id = ?
        ORDER BY cr.date_requested DESC
        LIMIT ?, ?";
    
        // Prepare and execute the query
        $stmt = $conn->prepare($this->requestQuery);
        $stmt->bind_param("iii", $residentId, $offset, $limit); // Bind residentId, offset, and limit
        $stmt->execute();
        $result = $stmt->get_result();
    
        // Fetch all certificate requests data
        $requests = [];
        while ($request = $result->fetch_assoc()) {
            $requests[] = $request;
        }
    
        return $requests;
    }
    
    // Function to get the total number of certificate requests (for pagination calculation)
    public function getTotalCertificateRequests($conn, $residentId) {
        // Count the total number of certificate requests for a specific resident
        $query = "SELECT COUNT(*) AS total 
                FROM Certificate_Requests cr
                JOIN Residents r ON cr.resident_id = r.resident_id
                LEFT JOIN templates t ON cr.template_id = t.id
                WHERE cr.resident_id = ?";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $residentId); // Bind residentId as integer
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return $row['total']; // Return the total number of certificate requests
    }

    // Function to insert a new certificate request
    public function insertRequest($conn, $requestData) {
        $query = "INSERT INTO Certificate_Requests (
                    resident_id, 
                    account_id, 
                    official_id, 
                    template_id,
                    purpose, 
                    number_of_copies, 
                    payment_amount, 
                    proof_of_payment, 
                    payment_type_id, 
                    date_requested, 
                    status
                  ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)";
    
        $stmt = $conn->prepare($query);
        
        // Use bind_param with "b" type for the blob (proof_of_payment)
        $stmt->bind_param("iiiisidsss", 
            $requestData['resident_id'], 
            $requestData['account_id'], 
            $requestData['official_id'], 
            $requestData['template_id'], 
            $requestData['purpose'], 
            $requestData['number_of_copies'], 
            $requestData['payment_amount'], 
            $requestData['proof_of_payment'], // File path as a string
            $requestData['payment_type_id'], 
            $requestData['status']
        );
        return $stmt->execute();
    }
    
    // Function to update an existing certificate request
    public function updateRequest($conn, $requestId, $requestData) {
        $query = "UPDATE Certificate_Requests SET 
                official_id = ?, 
                template_id = ?, 
                purpose = ?, 
                number_of_copies = ?, 
                payment_amount = ?, 
                proof_of_payment = ?, 
                payment_type_id = ?, 
                status = ?
              WHERE request_id = ?";

        $stmt = $conn->prepare($query);
        $stmt->bind_param(
            "iisidsssi", 
            $requestData['official_id'], 
            $requestData['template_id'], 
            $requestData['purpose'], 
            $requestData['number_of_copies'], 
            $requestData['payment_amount'], 
            $requestData['proof_of_payment'], // File path as a string
            $requestData['payment_type_id'], 
            $requestData['status'], 
            $requestId
        );

        return $stmt->execute(); // Return true if the update is successful, false otherwise
    }

    // Function to get a certificate request by ID
    public function getRequestById($conn, $requestId) {
        $query = "SELECT * FROM CertificateRequests WHERE request_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $requestId);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }

    // Function to search certificate requests without pagination
    public function searchRequests($conn, $residentId, $query) {
        $searchQuery = "SELECT 
            cr.request_id, 
            t.doc_name AS document_name, 
            CONCAT(r.first_name, ' ', r.last_name) AS name_requested, 
            cr.purpose, 
            cr.date_requested AS date_submitted, 
            pt.name AS payment_type, 
            cr.payment_amount, 
            cr.number_of_copies AS no_of_copies, 
            cr.status,
            cr.remarks,  
            t.price AS template_price, 
            cr.template_id, 
            cr.payment_type_id, 
            cr.proof_of_payment, 
            cr.last_updated
        FROM Certificate_Requests cr
        JOIN Residents r ON cr.resident_id = r.resident_id
        LEFT JOIN templates t ON cr.template_id = t.id
        LEFT JOIN payment_types pt ON cr.payment_type_id = pt.id
        WHERE cr.template_id IS NOT NULL
          AND cr.resident_id = ?
          AND (
              cr.request_id LIKE ? OR
              t.doc_name LIKE ? OR
              CONCAT(r.first_name, ' ', r.last_name) LIKE ? OR
              cr.purpose LIKE ? OR
              cr.date_requested LIKE ? OR
              pt.name LIKE ? OR
              cr.payment_amount LIKE ? OR
              cr.number_of_copies LIKE ? OR
              cr.status LIKE ? OR
              cr.remarks LIKE ? OR
              cr.last_updated LIKE ?
          )
        ORDER BY cr.last_updated DESC";

        $stmt = $conn->prepare($searchQuery);
        $searchTerm = "%{$query}%";
        $stmt->bind_param("isssssssssss", $residentId, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();

        $requests = [];
        while ($request = $result->fetch_assoc()) {
            $requests[] = $request;
        }

        return $requests;
    }

     // Function to search certificate requests by various fields
     public function searchRequestsdsa($conn, $residentId, $query) {
        $searchQuery = "SELECT 
                            cr.request_id, 
                            t.doc_name AS document_name, 
                            CONCAT(r.first_name, ' ', r.last_name) AS name_requested, 
                            cr.purpose, 
                            cr.date_requested AS date_submitted, 
                            cr.last_updated, 
                            pt.name AS payment_type, 
                            cr.payment_amount, 
                            cr.proof_of_payment, 
                            cr.number_of_copies AS no_of_copies, 
                            cr.remarks, 
                            cr.status
                        FROM Certificate_Requests cr
                        JOIN Residents r ON cr.resident_id = r.resident_id
                        LEFT JOIN templates t ON cr.template_id = t.id
                        LEFT JOIN payment_types pt ON cr.payment_type_id = pt.id
                        WHERE cr.resident_id = ? AND (
                              t.doc_name LIKE ? 
                              OR CONCAT(r.first_name, ' ', r.last_name) LIKE ? 
                              OR cr.purpose LIKE ? 
                              OR cr.date_requested LIKE ? 
                              OR cr.last_updated LIKE ? 
                              OR pt.name LIKE ? 
                              OR cr.payment_amount LIKE ? 
                              OR cr.proof_of_payment LIKE ? 
                              OR cr.number_of_copies LIKE ? 
                              OR cr.remarks LIKE ? 
                              OR cr.status LIKE ?
                        )
                        ORDER BY cr.date_requested DESC";

        $stmt = $conn->prepare($searchQuery);
        $searchTerm = "%{$query}%";
        $stmt->bind_param(
            "issssssssss", 
            $residentId, 
            $searchTerm, 
            $searchTerm, 
            $searchTerm, 
            $searchTerm, 
            $searchTerm, 
            $searchTerm, 
            $searchTerm, 
            $searchTerm, 
            $searchTerm, 
            $searchTerm
        );
        $stmt->execute();
        $result = $stmt->get_result();

        $requests = [];
        while ($request = $result->fetch_assoc()) {
            $requests[] = $request;
        }

        return $requests;
    }

    public function getTypeOfPayment($conn) {
        $query = "SELECT * FROM payment_types";
        $result = $conn->query($query);

        $paymentTypes = [];
        while ($paymentType = $result->fetch_assoc()) {
            $paymentTypes[] = $paymentType;
        }

        return $paymentTypes;
    }
    
    // GET PERMIT TYPES
    public function getTypeOfDocument($conn) {
        $query = "SELECT * FROM templates";
        $result = $conn->query($query);

        $documentTypes = [];
        while ($documentType = $result->fetch_assoc()) {
            $documentTypes[] = $documentType;
        }

        return $documentTypes;
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
?>
