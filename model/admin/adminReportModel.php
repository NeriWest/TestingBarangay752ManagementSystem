<?php
require_once 'dbconnection.php';

$db = new dbcon();
$conn = $db->getConnection();

class ReportModel {
    private $showBlotterQuery;

    // Function to show all blotter cases
    function showBlotter($conn)
    {
        // Query to fetch all blotter cases
        $this->showBlotterQuery = "SELECT B.*, E.evidence_description, E.evidence_picture
                                    FROM Blotter B
                                    LEFT JOIN Evidence E ON B.case_id = E.case_id
                                    ORDER BY B.date_and_time_created DESC;";
        $result = $conn->query($this->showBlotterQuery);

        // Check if the result is valid
        if ($result === FALSE) {
            return []; // Return an empty array if query fails
        }

        // Fetch all blotter cases data
        $blotters = [];
        while ($blotter = $result->fetch_assoc()) {
            $blotters[] = $blotter; // Store each blotter case in the array
        }

        return $blotters; // Return the list of blotter cases
    }

    // Function to show blotter cases with pagination
    public function showBlotterWithPagination($conn, $offset, $limit)
    {
        // Define the paginated query
        $query = "SELECT B.*, E.evidence_description, E.evidence_picture, 
                CONCAT(o.first_name, ' ', o.middle_name, ' ', o.last_name) AS official_name
        FROM (
            SELECT * FROM Blotter
            ORDER BY date_and_time_updated DESC
            LIMIT ?, ?
        ) AS B
        LEFT JOIN Evidence E ON B.case_id = E.case_id
        LEFT JOIN (
            SELECT o.*, 
               r.first_name, r.middle_name, r.last_name 
            FROM Officials o
            LEFT JOIN Residents r ON o.resident_id = r.resident_id
        ) AS o ON B.official_id = o.official_id
        ORDER BY B.date_and_time_updated DESC;";

        // Prepare and execute the query
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $offset, $limit); // Bind offset and limit
        $stmt->execute();
        $result = $stmt->get_result();

        // Fetch all blotter cases data
        $blotters = [];
        while ($blotter = $result->fetch_assoc()) {
            // Fetch all evidence for the current case_id
            $evidenceQuery = "SELECT evidence_description, evidence_picture 
                              FROM Evidence 
                              WHERE case_id = ?";
            $evidenceStmt = $conn->prepare($evidenceQuery);
            $evidenceStmt->bind_param("s", $blotter['case_id']);
            $evidenceStmt->execute();
            $evidenceResult = $evidenceStmt->get_result();

            // Attach evidence data to each blotter case
            $blotter['evidence'] = [];
            while ($evidence = $evidenceResult->fetch_assoc()) {
                $blotter['evidence'][] = $evidence;
            }

            $blotters[] = $blotter; // Store each blotter case in the array
        }

        return $blotters; // Return the list of blotter cases
    }

    // Function to get the total number of blotter cases (for pagination calculation)
    public function getTotalBlotters($conn)
    {
        // Count the total number of blotter cases
        $query = "SELECT COUNT(*) AS total FROM Blotter ORDER BY date_and_time_updated DESC";
        $result = $conn->query($query);
        $row = $result->fetch_assoc();

        return $row['total']; // Return the total number of blotter cases
    }


    // Function to search accounts based on role_name, username, status, email, resident names, cellphone number, or date of birth
    public function searchBlotters($conn, $query)
    {
        $searchQuery = "
        SELECT 
            B.*, 
            E.evidence_description, 
            E.evidence_picture, 
            CONCAT(o.first_name, ' ', o.middle_name, ' ', o.last_name) AS official_name
        FROM (
            SELECT * FROM Blotter
            ORDER BY date_and_time_updated DESC
        ) AS B
        LEFT JOIN Evidence E ON B.case_id = E.case_id
        LEFT JOIN (
            SELECT o.*, 
                   r.first_name, r.middle_name, r.last_name 
            FROM Officials o
            LEFT JOIN Residents r ON o.resident_id = r.resident_id
        ) AS o ON B.official_id = o.official_id
        WHERE 
            (B.case_id LIKE ? 
            OR B.complainant LIKE ? 
            OR B.subject LIKE ? 
            OR B.location LIKE ? 
            OR B.narration LIKE ? 
            OR B.person_involved LIKE ? 
            OR B.date_of_incident LIKE ? 
            OR B.time_of_incident LIKE ? 
            OR B.status LIKE ? 
            OR B.complainant_type LIKE ? 
            OR B.remarks LIKE ? 
            OR CONCAT(o.first_name, ' ', o.middle_name, ' ', o.last_name) LIKE ?)
        ORDER BY B.date_and_time_updated DESC";

        $stmt = $conn->prepare($searchQuery);
        $searchTerm = "%{$query}%";

        // Bind parameters for the search
        $stmt->bind_param(
            "ssssssssssss",
            $searchTerm, // case_id
            $searchTerm, // complainant
            $searchTerm, // subject
            $searchTerm, // location
            $searchTerm, // narration
            $searchTerm, // person_involved
            $searchTerm, // date_of_incident
            $searchTerm, // time_of_incident
            $searchTerm, // status
            $searchTerm, // complainant_type
            $searchTerm, // remarks
            $searchTerm  // official_name
        );

        // Execute and fetch results
        if ($stmt->execute()) {
            $result = $stmt->get_result();
        } else {
            echo "Error executing query: " . $stmt->error;
            return [];
        }

        $blotters = [];
        while ($blotter = $result->fetch_assoc()) {
            $blotters[] = $blotter;
        }

        return $blotters;
    }


    public function createBlotter($conn, $blotterData, $evidenceData)
    {
        // Folder path for storing evidence images
        $uploadFolder = '../../img/image_evidences/blotter';

        // Check if the folder exists
        if (!is_dir($uploadFolder)) {
            // Try to create the folder if it doesn't exist
            if (!mkdir($uploadFolder, 0777, true)) {
                echo "Error: Upload folder does not exist and could not be created.";
                return false;
            }
        }

        // Check if the folder is writable
        if (!is_writable($uploadFolder)) {
            echo "Error: Upload folder is not writable.";
            return false;
        }

        // Fetch the last inserted case_id from the Blotter table
        $query = "SELECT case_id FROM Blotter ORDER BY case_id DESC LIMIT 1";
        $result = $conn->query($query);
        $last_case_id = $result->fetch_assoc()['case_id'];

        // Generate the new case_id
        if ($last_case_id) {
            $last_number = intval(substr($last_case_id, 4)); // Get the numeric part
        } else {
            $last_number = 0; // No records exist, start from 0
        }
        $next_number = $last_number + 1;
        $new_case_id = '125-' . str_pad($next_number, 3, '0', STR_PAD_LEFT);

        // Insert the new blotter record
        $query = "INSERT INTO Blotter 
            (case_id, account_id, complainant, subject, location, narration, person_involved, date_of_incident, time_of_incident, official_id, status, complainant_type, resident_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param(
            "sisssssssssss", 
            $new_case_id, 
            $blotterData['account_id'], 
            $blotterData['complainant'], 
            $blotterData['subject'], 
            $blotterData['location'], 
            $blotterData['narration'], 
            $blotterData['person_involved'], 
            $blotterData['date_of_incident'], 
            $blotterData['time_of_incident'], 
            $blotterData['official_id'], // Corrected position of official_id
            $blotterData['status'],
            $blotterData['complainant_type'], // Assuming type_of_complainant is part of the blotter data
            $blotterData['resident_id'] // Assuming resident_id is part of the blotter data
        );

        // Execute the blotter insertion
        if ($stmt->execute()) {
            echo "Blotter inserted successfully with case_id: $new_case_id";

            // Insert multiple pieces of evidence into the Evidence table
            $query_evidence = "INSERT INTO Evidence (case_id, evidence_description, evidence_picture) 
                            VALUES (?, ?, ?)";
            $stmt_evidence = $conn->prepare($query_evidence);

            foreach ($evidenceData as $evidence) {
                $fileDestination = $evidence['evidence_picture']; // Already the file path

                // Insert the file path into the database
                $stmt_evidence->bind_param(
                    "sss",
                    $new_case_id,
                    $evidence['evidence_description'],
                    $fileDestination // Store file path
                );

                if (!$stmt_evidence->execute()) {
                    echo "Error inserting evidence: " . $stmt_evidence->error;
                    return false;
                }
            }

            $stmt_evidence->close();
            return $new_case_id;  // Return the new case_id on success
        } else {
            echo "Blotter insertion error: " . $stmt->error;
        }

        // Close statements
        $stmt->close();
        return false;
    }
    
    // Function to update an existing blotter case
    public function updateBlotter($conn, $blotterData, $evidenceData = null) {
        // Start a transaction to ensure both blotter and evidence updates succeed or fail together
        $conn->begin_transaction();

        try {
            // Update the blotter case
            $query = "UPDATE Blotter SET
                account_id = ?,
                complainant = ?,
                subject = ?,
                location = ?,
                narration = ?,
                person_involved = ?,
                date_of_incident = ?,
                time_of_incident = ?,
                status = ?,
                official_id = ?  -- Added official_id field
                WHERE case_id = ?";

            $stmt = $conn->prepare($query);
            $stmt->bind_param(
                "issssssssss",  // Adjust format string for parameters
                $blotterData['account_id'], 
                $blotterData['complainant'], 
                $blotterData['subject'], 
                $blotterData['location'], 
                $blotterData['narration'], 
                $blotterData['person_involved'], 
                $blotterData['date_of_incident'], 
                $blotterData['time_of_incident'], 
                $blotterData['status'], 
                $blotterData['official_id'],  // Added official_id parameter
                $blotterData['case_id']  // Use case_id here
            );

            if (!$stmt->execute()) {
                throw new Exception("Blotter update error: " . $stmt->error);
            }

            // Check if there is new evidence data to insert
            if (!empty($evidenceData)) {
                $query_insert_evidence = "INSERT INTO Evidence (case_id, evidence_description, evidence_picture) 
                                        VALUES (?, ?, ?)";
                $stmt_insert_evidence = $conn->prepare($query_insert_evidence);

                foreach ($evidenceData as $evidence) {
                    $fileDestination = $evidence['evidence_picture']; // Already the file path

                    // Insert the file path into the database
                    $stmt_insert_evidence->bind_param(
                        "iss",  // Use correct format
                        $blotterData['case_id'],  // Use case_id
                        $evidence['evidence_description'], 
                        $fileDestination
                    );

                    if (!$stmt_insert_evidence->execute()) {
                        throw new Exception("Error inserting evidence: " . $stmt_insert_evidence->error);
                    }
                }
            }

            // Commit the transaction if both blotter and evidence updates succeed
            $conn->commit();
            return true;
        } catch (Exception $e) {
            // Roll back the transaction in case of any errors
            $conn->rollback();
            echo $e->getMessage();
            return false;
        } finally {
            if (isset($stmt)) {
                $stmt->close();
            }
            if (isset($stmt_insert_evidence)) {
                $stmt_insert_evidence->close();
            }
        }
    }

    // Function to show all complaints
    public function showComplaints($conn)
    {
        $query = "SELECT C.*, E.evidence_description, E.evidence_picture
                FROM Complaint C
                LEFT JOIN Evidence E ON C.complaint_id = E.complaint_id
                ORDER BY C.date_and_time_created DESC";

        $result = $conn->query($query);

        if ($result === FALSE) {
            return [];
        }

        $complaints = [];
        while ($complaint = $result->fetch_assoc()) {
            $complaints[] = $complaint;
        }

        return $complaints;
    }

    // Function to show complaints with pagination
    public function showComplaintsWithPagination($conn, $offset, $limit)
    {
        // Define the paginated query
        $query = "SELECT C.*, E.evidence_description, E.evidence_picture, 
                CONCAT(o.first_name, ' ', o.middle_name, ' ', o.last_name) AS official_name
        FROM (
            SELECT * FROM Complaint
            ORDER BY date_and_time_updated DESC
            LIMIT ?, ?
        ) AS C
        LEFT JOIN Evidence E ON C.complaint_id = E.complaint_id
        LEFT JOIN (
            SELECT o.*, 
               r.first_name, r.middle_name, r.last_name 
            FROM Officials o
            LEFT JOIN Residents r ON o.resident_id = r.resident_id
        ) AS o ON C.official_id = o.official_id
        ORDER BY C.date_and_time_updated DESC;";

        // Prepare and execute the query
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $offset, $limit); // Bind offset and limit
        $stmt->execute();
        $result = $stmt->get_result();

        // Fetch all complaints data
        $complaints = [];
        while ($complaint = $result->fetch_assoc()) {
            // Fetch all evidence for the current complaint_id
            $evidenceQuery = "SELECT evidence_description, evidence_picture 
                              FROM Evidence 
                              WHERE complaint_id = ?";
            $evidenceStmt = $conn->prepare($evidenceQuery);
            $evidenceStmt->bind_param("s", $complaint['complaint_id']);
            $evidenceStmt->execute();
            $evidenceResult = $evidenceStmt->get_result();

            // Attach evidence data to each complaint
            $complaint['evidence'] = [];
            while ($evidence = $evidenceResult->fetch_assoc()) {
                $complaint['evidence'][] = $evidence;
            }

            $complaints[] = $complaint; // Store each complaint in the array
        }

        return $complaints; // Return the list of complaints
    }

    // Count total complaints for pagination
    public function getTotalComplaints($conn)
    {
        $query = "SELECT COUNT(*) AS total FROM Complaint";
        $result = $conn->query($query);
        $row = $result->fetch_assoc();

        return $row['total'];
    }

    // Search complaints
    public function searchComplaints($conn, $query)
    {
        $searchQuery = "
        SELECT 
            C.*, 
            E.evidence_description, 
            E.evidence_picture, 
            CONCAT(o.first_name, ' ', o.middle_name, ' ', o.last_name) AS official_name
        FROM (
            SELECT * FROM Complaint
            ORDER BY date_and_time_updated DESC
        ) AS C
        LEFT JOIN Evidence E ON C.complaint_id = E.complaint_id
        LEFT JOIN (
            SELECT o.*, 
                   r.first_name, r.middle_name, r.last_name 
            FROM Officials o
            LEFT JOIN Residents r ON o.resident_id = r.resident_id
        ) AS o ON C.official_id = o.official_id
        WHERE 
            (C.complaint_id LIKE ? 
            OR C.complainant LIKE ? 
            OR C.subject LIKE ? 
            OR C.location LIKE ? 
            OR C.narration LIKE ? 
            OR C.person_involved LIKE ? 
            OR C.date_of_incident LIKE ? 
            OR C.time_of_incident LIKE ? 
            OR C.status LIKE ? 
            OR C.complainant_type LIKE ? 
            OR C.remarks LIKE ? 
            OR CONCAT(o.first_name, ' ', o.middle_name, ' ', o.last_name) LIKE ?)
        ORDER BY C.date_and_time_updated DESC";

        $stmt = $conn->prepare($searchQuery);
        $searchTerm = "%{$query}%";

        // Bind parameters for the search
        $stmt->bind_param(
            "ssssssssssss",
            $searchTerm, // complaint_id
            $searchTerm, // complainant
            $searchTerm, // subject
            $searchTerm, // location
            $searchTerm, // narration
            $searchTerm, // person_involved
            $searchTerm, // date_of_incident
            $searchTerm, // time_of_incident
            $searchTerm, // status
            $searchTerm, // complainant_type
            $searchTerm, // remarks
            $searchTerm  // official_name
        );

        // Execute and fetch results
        if ($stmt->execute()) {
            $result = $stmt->get_result();
        } else {
            echo "Error executing query: " . $stmt->error;
            return [];
        }

        $complaints = [];
        while ($complaint = $result->fetch_assoc()) {
            $complaints[] = $complaint;
        }

        return $complaints;
    }

    // Create complaint
    public function createComplaint($conn, $complaintData, $evidenceData)
    {
        $uploadFolder = '../../img/image_evidences/complaint';
        if (!is_dir($uploadFolder)) mkdir($uploadFolder, 0777, true);
        if (!is_writable($uploadFolder)) return false;

        $query = "SELECT complaint_id FROM Complaint ORDER BY complaint_id DESC LIMIT 1";
        $result = $conn->query($query);
        $last_id = $result->fetch_assoc()['complaint_id'];

        $next_number = $last_id ? intval(substr($last_id, 4)) + 1 : 1;
        $new_complaint_id = 'CMP-' . str_pad($next_number, 3, '0', STR_PAD_LEFT);

        $query = "INSERT INTO Complaint 
                (complaint_id, account_id, official_id, resident_id, date_of_incident, time_of_incident,
                subject, complainant, complainant_type, person_involved, location, narration, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($query);
        $stmt->bind_param(
            "siiisssssssss",
            $new_complaint_id,
            $complaintData['account_id'],
            $complaintData['official_id'],
            $complaintData['resident_id'],
            $complaintData['date_of_incident'],
            $complaintData['time_of_incident'],
            $complaintData['subject'],
            $complaintData['complainant'],
            $complaintData['complainant_type'],
            $complaintData['person_involved'],
            $complaintData['location'],
            $complaintData['narration'],
            $complaintData['status']
        );

        if ($stmt->execute()) {
            $query_evidence = "INSERT INTO Evidence (complaint_id, evidence_description, evidence_picture) VALUES (?, ?, ?)";
            $stmt_evidence = $conn->prepare($query_evidence);

            foreach ($evidenceData as $evidence) {
                $stmt_evidence->bind_param("sss", $new_complaint_id, $evidence['evidence_description'], $evidence['evidence_picture']);
                if (!$stmt_evidence->execute()) {
                    echo "Error inserting evidence: " . $stmt_evidence->error;
                    return false;
                }
            }

            return $new_complaint_id;
        } else {
            echo "Error inserting complaint: " . $stmt->error;
            return false;
        }
    }

    // Update complaint
    public function updateComplaint($conn, $complaintData, $evidenceData = null)
    {
        $conn->begin_transaction();

        try {
            $query = "UPDATE Complaint SET 
                        account_id = ?, official_id = ?, resident_id = ?, 
                        date_of_incident = ?, time_of_incident = ?, subject = ?, complainant = ?,  person_involved = ?, location = ?, narration = ?, status = ?
                    WHERE complaint_id = ?";

            $stmt = $conn->prepare($query);
            $stmt->bind_param(
                "iiisssssssss",
                $complaintData['account_id'],
                $complaintData['official_id'],
                $complaintData['resident_id'],
                $complaintData['date_of_incident'],
                $complaintData['time_of_incident'],
                $complaintData['subject'],
                $complaintData['complainant'],
                $complaintData['person_involved'],
                $complaintData['location'],
                $complaintData['narration'],
                $complaintData['status'],
                $complaintData['complaint_id']
            );

            if (!$stmt->execute()) throw new Exception("Complaint update error: " . $stmt->error);

            if (!empty($evidenceData)) {
                $query_evidence = "INSERT INTO Evidence (complaint_id, evidence_description, evidence_picture) VALUES (?, ?, ?)";
                $stmt_evidence = $conn->prepare($query_evidence);

                foreach ($evidenceData as $evidence) {
                    $stmt_evidence->bind_param("sss", $complaintData['complaint_id'], $evidence['evidence_description'], $evidence['evidence_picture']);
                    if (!$stmt_evidence->execute()) throw new Exception("Evidence error: " . $stmt_evidence->error);
                }
            }

            $conn->commit();
            return true;
        } catch (Exception $e) {
            $conn->rollback();
            echo $e->getMessage();
            return false;
        }
    }

    // Function to show all incidents
    public function showIncidents($conn)
    {
        $query = "SELECT I.*, E.evidence_description, E.evidence_picture
                FROM Incident I
                LEFT JOIN Evidence E ON I.incident_id = E.incident_id
                ORDER BY I.date_and_time_created DESC";

        $result = $conn->query($query);

        if ($result === FALSE) {
            return [];
        }

        $incidents = [];
        while ($incident = $result->fetch_assoc()) {
            $incidents[] = $incident;
        }

        return $incidents;
    }

    // Function to show incidents with pagination
    public function showIncidentsWithPagination($conn, $offset, $limit)
    {
        $query = "SELECT I.*, E.evidence_description, E.evidence_picture, 
                        CONCAT(o.first_name, ' ', o.middle_name, ' ', o.last_name) AS official_name
                FROM (
                    SELECT * FROM Incident
                    ORDER BY date_and_time_updated DESC
                    LIMIT ?, ? 
                ) AS I
                LEFT JOIN Evidence E ON I.incident_id = E.incident_id
                LEFT JOIN (
                    SELECT o.*, r.first_name, r.middle_name, r.last_name 
                    FROM Officials o
                    LEFT JOIN Residents r ON o.resident_id = r.resident_id
                ) AS o ON I.official_id = o.official_id
                ORDER BY I.date_and_time_updated DESC";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $offset, $limit);
        $stmt->execute();
        $result = $stmt->get_result();

        $incidents = [];
        while ($incident = $result->fetch_assoc()) {
            // Get evidence
            $evidenceQuery = "SELECT evidence_description, evidence_picture FROM Evidence WHERE incident_id = ?";
            $evidenceStmt = $conn->prepare($evidenceQuery);
            $evidenceStmt->bind_param("s", $incident['incident_id']);
            $evidenceStmt->execute();
            $evidenceResult = $evidenceStmt->get_result();

            $incident['evidence'] = [];
            while ($evidence = $evidenceResult->fetch_assoc()) {
                $incident['evidence'][] = $evidence;
            }

            $incidents[] = $incident;
        }

        return $incidents;
    }

    // Count total incidents for pagination
    public function getTotalIncidents($conn)
    {
        $query = "SELECT COUNT(*) AS total FROM Incident";
        $result = $conn->query($query);
        $row = $result->fetch_assoc();

        return $row['total'];
    }

    // Search incidents
    public function searchIncidents($conn, $query)
    {
        $searchQuery = "
        SELECT 
            I.*, 
            E.evidence_description, 
            E.evidence_picture, 
            CONCAT(o.first_name, ' ', o.middle_name, ' ', o.last_name) AS official_name
        FROM (
            SELECT * FROM Incident
            ORDER BY date_and_time_updated DESC
        ) AS I
        LEFT JOIN Evidence E ON I.incident_id = E.incident_id
        LEFT JOIN (
            SELECT o.*, 
                   r.first_name, r.middle_name, r.last_name 
            FROM Officials o
            LEFT JOIN Residents r ON o.resident_id = r.resident_id
        ) AS o ON I.official_id = o.official_id
        WHERE 
            (I.incident_id LIKE ? 
            OR I.complainant LIKE ? 
            OR I.subject LIKE ? 
            OR I.location LIKE ? 
            OR I.narration LIKE ? 
            OR I.person_involved LIKE ? 
            OR I.date_of_incident LIKE ? 
            OR I.time_of_incident LIKE ? 
            OR I.status LIKE ? 
            OR I.complainant_type LIKE ? 
            OR I.remarks LIKE ? 
            OR CONCAT(o.first_name, ' ', o.middle_name, ' ', o.last_name) LIKE ?)
        ORDER BY I.date_and_time_updated DESC";

        $stmt = $conn->prepare($searchQuery);
        $searchTerm = "%{$query}%";

        // Bind parameters for the search
        $stmt->bind_param(
            "ssssssssssss",
            $searchTerm, // incident_id
            $searchTerm, // complainant
            $searchTerm, // subject
            $searchTerm, // location
            $searchTerm, // narration
            $searchTerm, // person_involved
            $searchTerm, // date_of_incident
            $searchTerm, // time_of_incident
            $searchTerm, // status
            $searchTerm, // complainant_type
            $searchTerm, // remarks
            $searchTerm  // official_name
        );

        // Execute and fetch results
        if ($stmt->execute()) {
            $result = $stmt->get_result();
        } else {
            echo "Error executing query: " . $stmt->error;
            return [];
        }

        $incidents = [];
        while ($incident = $result->fetch_assoc()) {
            $incidents[] = $incident;
        }

        return $incidents;
    }

    // Create incident
    public function createIncident($conn, $incidentData, $evidenceData)
    {
        $uploadFolder = '../../img/image_evidences/incident';
        if (!is_dir($uploadFolder)) mkdir($uploadFolder, 0777, true);
        if (!is_writable($uploadFolder)) return false;

        // Get the last incident ID and generate a new one
        $query = "SELECT incident_id FROM Incident ORDER BY incident_id DESC LIMIT 1";
        $result = $conn->query($query);
        $last_id = $result->fetch_assoc()['incident_id'];

        $next_number = $last_id ? intval(substr($last_id, 4)) + 1 : 1;
        $new_incident_id = 'INC-' . str_pad($next_number, 3, '0', STR_PAD_LEFT);

        $query = "INSERT INTO Incident 
                (incident_id, account_id, official_id, resident_id, date_of_incident, time_of_incident,
                subject, complainant, complainant_type, person_involved, location, narration, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($query);
        $stmt->bind_param(
            "siiisssssssss",
            $new_incident_id,
            $incidentData['account_id'],
            $incidentData['official_id'],
            $incidentData['resident_id'],
            $incidentData['date_of_incident'],
            $incidentData['time_of_incident'],
            $incidentData['subject'],
            $incidentData['complainant'],
            $incidentData['complainant_type'],
            $incidentData['person_involved'],
            $incidentData['location'],
            $incidentData['narration'],
            $incidentData['status']
        );

        if ($stmt->execute()) {
            $query_evidence = "INSERT INTO Evidence (incident_id, evidence_description, evidence_picture) VALUES (?, ?, ?)";
            $stmt_evidence = $conn->prepare($query_evidence);

            foreach ($evidenceData as $evidence) {
                $stmt_evidence->bind_param("sss", $new_incident_id, $evidence['evidence_description'], $evidence['evidence_picture']);
                if (!$stmt_evidence->execute()) {
                    echo "Error inserting evidence: " . $stmt_evidence->error;
                    return false;
                }
            }

            return $new_incident_id;
        } else {
            echo "Error inserting incident: " . $stmt->error;
            return false;
        }
    }

    // Update incident
    public function updateIncident($conn, $incidentData, $evidenceData = null)
    {
        $conn->begin_transaction();

        try {
            $query = "UPDATE Incident SET 
                        account_id = ?, official_id = ?, resident_id = ?, 
                        date_of_incident = ?, time_of_incident = ?, subject = ?, complainant = ?,  person_involved = ?, location = ?, narration = ?, status = ? 
                    WHERE incident_id = ?";

            $stmt = $conn->prepare($query);
            $stmt->bind_param(
                "iiisssssssss",
                $incidentData['account_id'],
                $incidentData['official_id'],
                $incidentData['resident_id'],
                $incidentData['date_of_incident'],
                $incidentData['time_of_incident'],
                $incidentData['subject'],
                $incidentData['complainant'],
                $incidentData['person_involved'],
                $incidentData['location'],
                $incidentData['narration'],
                $incidentData['status'],
                $incidentData['incident_id']
            );

            if (!$stmt->execute()) throw new Exception("Incident update error: " . $stmt->error);

            if (!empty($evidenceData)) {
                $query_evidence = "INSERT INTO Evidence (incident_id, evidence_description, evidence_picture) VALUES (?, ?, ?)";
                $stmt_evidence = $conn->prepare($query_evidence);

                foreach ($evidenceData as $evidence) {
                    $stmt_evidence->bind_param("sss", $incidentData['incident_id'], $evidence['evidence_description'], $evidence['evidence_picture']);
                    if (!$stmt_evidence->execute()) throw new Exception("Evidence error: " . $stmt_evidence->error);
                }
            }

            $conn->commit();
            return true;
        } catch (Exception $e) {
            $conn->rollback();
            echo $e->getMessage();
            return false;
        }
    }
   
    public function updateReportStatus($conn, $tableName, $caseId, $status, $remarks) {
        // Ensure the table name is safe to use in the query
        $allowedTables = ['Blotter', 'Complaint', 'Incident'];
        if (!in_array($tableName, $allowedTables)) {
            echo "Error: Invalid table name.";
            return false;
        }

        // Determine the appropriate ID column based on the table name
        $idColumn = $tableName === 'Complaint' ? 'complaint_id' : ($tableName === 'Incident' ? 'incident_id' : 'case_id');

        // Prepare the SQL query to update the report status
        $query = "UPDATE $tableName SET 
                    status = ?, 
                    date_and_time_updated = NOW(),
                    remarks = ?
                  WHERE $idColumn = ?";
        $stmt = $conn->prepare($query);

        // Check if the statement was prepared successfully
        if (!$stmt) {
            echo "Error preparing statement: " . $conn->error;
            return false;
        }

        // Bind the parameters to the query
        $stmt->bind_param("sss", $status, $remarks, $caseId);

        // Execute the query and return the result
        if ($stmt->execute()) {
            return true;
        } else {
            echo "Error executing query: " . $stmt->error;
            return false;
        }
    }

    

    

}