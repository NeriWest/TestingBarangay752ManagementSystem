<?php
require_once 'dbconnection.php';

$db = new dbcon();
$conn = $db->getConnection();

class CertificateRequestModel {
    private $requestQuery;

    // Function to get all certificate requests 
    public function showCertificateRequests($conn) {
        $this->requestQuery = "SELECT request_id, certificate_type, purpose, number_of_copies, 
                   date_requested, status, payment_amount, date_issued 
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

    // Function to get all certificate requests with pagination
    public function showCertificateRequestsWithPagination($conn, $offset, $limit) {
        // Define the paginated query
        $this->requestQuery = "SELECT 
            cr.request_id, 
            t.doc_name AS document_name, 
            CONCAT(r.first_name, ' ', r.last_name) AS name_requested, 
            cr.purpose, 
            cr.date_requested AS date_submitted, 
            pt.name AS payment_type, 
            cr.payment_amount, 
            cr.number_of_copies AS no_of_copies, 
            cr.status, 
            t.price AS template_price, 
            cr.template_id, 
            cr.payment_type_id,  -- Including the payment_type_id
            cr.proof_of_payment, -- Including the proof of payment
            cr.last_updated,     -- Including the last updated timestamp
            cr.remarks,          -- Including remarks
            t.id AS template_id, -- Show template_id
            t.price AS template_price -- Get the price based on the id in templates
        FROM Certificate_Requests cr
        JOIN Residents r ON cr.resident_id = r.resident_id
        LEFT JOIN templates t ON cr.template_id = t.id
        LEFT JOIN payment_types pt ON cr.payment_type_id = pt.id
        WHERE t.template_type = 'Certificates' AND cr.template_id IS NOT NULL
        ORDER BY cr.last_updated DESC
        LIMIT ?, ?";

        // Prepare and execute the query
        $stmt = $conn->prepare($this->requestQuery);
        $stmt->bind_param("ii", $offset, $limit); // Bind offset and limit
        $stmt->execute();
        $result = $stmt->get_result();

        // Fetch all certificate requests data
        $requests = [];
        while ($request = $result->fetch_assoc()) {
            $requests[] = $request; // Store each request in the array
        }

        return $requests; // Return the list of requests
    }

    // Function to get the total number of certificate requests (for pagination calculation)
    public function getTotalCertificateRequests($conn) {
        // Count the total number of certificate requests
        $query = "SELECT COUNT(*) AS total 
              FROM Certificate_Requests cr
              JOIN Residents r ON cr.resident_id = r.resident_id
              LEFT JOIN templates t ON cr.template_id = t.id
              WHERE t.template_type = 'Certificates' AND cr.template_id IS NOT NULL";
        $result = $conn->query($query);
        $row = $result->fetch_assoc();

        return $row['total']; // Return the total number of certificate requests
    }

     // Function to get all certificate requests with pagination
     public function showPermitRequestsWithPagination($conn, $offset, $limit) {
                // Define the paginated query
                $this->requestQuery = "SELECT 
            cr.request_id, 
            t.doc_name AS document_name, 
            CONCAT(r.first_name, ' ', r.last_name) AS name_requested, 
            cr.purpose, 
            cr.date_requested AS date_submitted, 
            pt.name AS payment_type, 
            cr.payment_amount, 
            cr.number_of_copies AS no_of_copies, 
            cr.status, 
            cr.remarks,          -- Including remarks
            t.price AS template_price, 
            cr.template_id, 
            cr.payment_type_id,  -- Including the payment_type_id
            cr.proof_of_payment, -- Including the proof of payment
            cr.last_updated      -- Including the last updated timestamp
        FROM Certificate_Requests cr
        JOIN Residents r ON cr.resident_id = r.resident_id
        LEFT JOIN templates t ON cr.template_id = t.id
        LEFT JOIN payment_types pt ON cr.payment_type_id = pt.id
        WHERE t.template_type = 'Permits' AND cr.template_id IS NOT NULL
        ORDER BY cr.last_updated DESC
        LIMIT ?, ?";

        // Prepare and execute the query
        $stmt = $conn->prepare($this->requestQuery);
        $stmt->bind_param("ii", $offset, $limit); // Bind offset and limit
        $stmt->execute();
        $result = $stmt->get_result();

        // Fetch all certificate requests data
        $requests = [];
        while ($request = $result->fetch_assoc()) {
            $requests[] = $request; // Store each request in the array
        }

        return $requests; // Return the list of requests
    }


    // Function to get the total number of certificate requests (for pagination calculation)
    public function getTotalPermitRequests($conn) {
        // Count the total number of certificate requests
        $query = "SELECT COUNT(*) AS total 
              FROM Certificate_Requests cr
              JOIN Residents r ON cr.resident_id = r.resident_id
              LEFT JOIN templates t ON cr.template_id = t.id
              WHERE t.template_type = 'Permits' AND cr.template_id IS NOT NULL";
        $result = $conn->query($query);
        $row = $result->fetch_assoc();

        return $row['total']; // Return the total number of certificate requests
    }

    // Function to get all certificate requests with pagination
    public function showClearanceRequestsWithPagination($conn, $offset, $limit) {
            // Define the paginated query
            $this->requestQuery = "SELECT 
        cr.request_id, 
        t.doc_name AS document_name, 
        CONCAT(r.first_name, ' ', r.last_name) AS name_requested, 
        cr.purpose, 
        cr.date_requested AS date_submitted, 
        pt.name AS payment_type, 
        cr.payment_amount, 
        cr.number_of_copies AS no_of_copies, 
        cr.status, 
        cr.remarks,          -- Including remarks
        t.price AS template_price, 
        cr.template_id, 
        cr.payment_type_id,  -- Including the payment_type_id
        cr.proof_of_payment, -- Including the proof of payment
        cr.last_updated      -- Including the last updated timestamp
    FROM Certificate_Requests cr
    JOIN Residents r ON cr.resident_id = r.resident_id
    LEFT JOIN templates t ON cr.template_id = t.id
    LEFT JOIN payment_types pt ON cr.payment_type_id = pt.id
    WHERE t.template_type = 'Clearances' AND cr.template_id IS NOT NULL
    ORDER BY cr.last_updated DESC
    LIMIT ?, ?";

    // Prepare and execute the query
    $stmt = $conn->prepare($this->requestQuery);
    $stmt->bind_param("ii", $offset, $limit); // Bind offset and limit
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch all certificate requests data
    $requests = [];
    while ($request = $result->fetch_assoc()) {
        $requests[] = $request; // Store each request in the array
    }

    return $requests; // Return the list of requests
    }


    // Function to get the total number of certificate requests (for pagination calculation)
    public function getTotalClearanceRequests($conn) {
    // Count the total number of certificate requests
    $query = "SELECT COUNT(*) AS total 
        FROM Certificate_Requests cr
        JOIN Residents r ON cr.resident_id = r.resident_id
        LEFT JOIN templates t ON cr.template_id = t.id
        WHERE t.template_type = 'Clearances' AND cr.template_id IS NOT NULL";
    $result = $conn->query($query);
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
    
        // Bind all parameters including proof_of_payment as a string
        $stmt->bind_param(
            "iisidsssi", 
            $requestData['official_id'], 
            $requestData['template_id'], 
            $requestData['purpose'], 
            $requestData['number_of_copies'], 
            $requestData['payment_amount'], 
            $requestData['proof_of_payment'], // Now treated as a string
            $requestData['payment_type_id'], 
            $requestData['status'], 
            $requestId
        );
    
        return $stmt->execute();
    }

    // Function to update the status of a request for approval or disapproval with remarks
    public function updateRequestStatus($conn, $requestId, $status, $remarks) {
        $query = "UPDATE Certificate_Requests SET 
                    status = ?, 
                    remarks = ?, 
                    last_updated = NOW() 
                  WHERE request_id = ?";
    
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssi", $status, $remarks, $requestId); // Bind status, remarks, and request ID
    
        return $stmt->execute();
    }

    
    // Function to search permit requests without pagination
    public function searchPermitRequests($conn, $query) {
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
        WHERE t.template_type = 'Permits' 
          AND cr.template_id IS NOT NULL
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
        $stmt->bind_param("sssssssssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();

        $requests = [];
        while ($request = $result->fetch_assoc()) {
            $requests[] = $request;
        }

        return $requests;
    }
   
    // Function to search permit requests without pagination
    public function searchClearanceRequests($conn, $query) {
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
        WHERE t.template_type = 'Clearances' 
          AND cr.template_id IS NOT NULL
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
        $stmt->bind_param("sssssssssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();

        $requests = [];
        while ($request = $result->fetch_assoc()) {
            $requests[] = $request;
        }

        return $requests;
    }

    // Function to search permit requests without pagination
    public function searchCertificateRequests($conn, $query) {
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
        WHERE t.template_type = 'Certificates' 
          AND cr.template_id IS NOT NULL
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
        $stmt->bind_param("sssssssssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
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
    public function getTypeOfDocumentPermit($conn) {
        $query = "SELECT * FROM templates WHERE template_type = 'permits'";
        $result = $conn->query($query);

        $documentTypes = [];
        while ($documentType = $result->fetch_assoc()) {
            $documentTypes[] = $documentType;
        }

        return $documentTypes;
    }

     // GET PERMIT TYPES
     public function getTypeOfDocumentCertificates($conn) {
        $query = "SELECT * FROM templates WHERE template_type = 'certificates'";
        $result = $conn->query($query);

        $documentTypes = [];
        while ($documentType = $result->fetch_assoc()) {
            $documentTypes[] = $documentType;
        }

        return $documentTypes;
    }

     // GET PERMIT TYPES
     public function getTypeOfDocumentClearances($conn) {
        $query = "SELECT * FROM templates WHERE template_type = 'clearances'";
        $result = $conn->query($query);

        $documentTypes = [];
        while ($documentType = $result->fetch_assoc()) {
            $documentTypes[] = $documentType;
        }

        return $documentTypes;
    }
 
}

?>
