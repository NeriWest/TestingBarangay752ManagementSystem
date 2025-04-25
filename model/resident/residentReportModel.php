<?php
require_once 'dbconnection.php';

$db = new dbcon();
$conn = $db->getConnection();

class ReportModel {
    private $showBlotterQuery;

    // Function to show all blotter cases
    function showBlotter($conn) {
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
    
    public function showAllReportsWithPagination($conn, $offset, $limit, $residentId)
    {
        $query = "SELECT R.*, 
                        CONCAT(o.first_name, ' ', o.middle_name, ' ', o.last_name) AS official_name,
                        E.evidence_description, E.evidence_picture
                FROM (
                    SELECT * FROM (
                        SELECT 'Blotter' AS type, 
                                B.case_id, 
                                B.account_id, 
                                B.official_id, 
                                B.resident_id, 
                                B.date_of_incident, 
                                B.time_of_incident, 
                                B.subject, 
                                B.date_issued, 
                                B.complainant, 
                                B.complainant_type, 
                                B.person_involved, 
                                B.location, 
                                B.narration, 
                                B.status, 
                                B.date_and_time_created, 
                                B.remarks, 
                                B.date_and_time_updated
                        FROM Blotter B
                        WHERE B.resident_id = ?
                        UNION ALL
                        SELECT 'Incident' AS type, 
                                I.incident_id AS case_id, 
                                I.account_id, 
                                I.official_id, 
                                I.resident_id, 
                                I.date_of_incident, 
                                I.time_of_incident, 
                                I.subject, 
                                I.date_issued, 
                                I.complainant, 
                                I.complainant_type, 
                                I.person_involved, 
                                I.location, 
                                I.narration, 
                                I.status, 
                                I.date_and_time_created, 
                                I.remarks, 
                                I.date_and_time_updated
                        FROM Incident I
                        WHERE I.resident_id = ?
                        UNION ALL
                        SELECT 'Complaint' AS type, 
                                C.complaint_id AS case_id, 
                                C.account_id, 
                                C.official_id, 
                                C.resident_id, 
                                C.date_of_incident, 
                                C.time_of_incident, 
                                C.subject, 
                                C.date_issued, 
                                C.complainant, 
                                C.complainant_type, 
                                C.person_involved, 
                                C.location, 
                                C.narration, 
                                C.status, 
                                C.date_and_time_created, 
                                C.remarks, 
                                C.date_and_time_updated
                        FROM Complaint C
                        WHERE C.resident_id = ?
                    ) AS AllReports
                    ORDER BY date_and_time_updated DESC
                    LIMIT ?, ?
                ) AS R
                LEFT JOIN (
                    SELECT o.*, r.first_name, r.middle_name, r.last_name 
                    FROM Officials o
                    LEFT JOIN Residents r ON o.resident_id = r.resident_id
                ) AS o ON R.official_id = o.official_id
                LEFT JOIN Evidence E ON 
                    (R.type = 'Blotter' AND R.case_id = E.case_id) OR
                    (R.type = 'Incident' AND R.case_id = E.incident_id) OR
                    (R.type = 'Complaint' AND R.case_id = E.complaint_id)
                ORDER BY R.date_and_time_updated DESC";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("iiiii", $residentId, $residentId, $residentId, $offset, $limit);
        $stmt->execute();
        $result = $stmt->get_result();

        $reports = [];

        while ($report = $result->fetch_assoc()) {
            $reports[] = $report; // Store each report in the array
        }

        return $reports;
    }


    // Function to show reports with pagination
    public function showAlldasdReportsWithPagination($conn, $offset, $limit)
    {
        $query = "SELECT R.*, 
                        CONCAT(o.first_name, ' ', o.middle_name, ' ', o.last_name) AS official_name
                FROM (
                    SELECT * FROM (
                        SELECT 'Blotter' AS type, B.case_id, B.account_id, B.official_id, B.resident_id, 
                               B.date_of_incident, B.time_of_incident, B.subject, B.date_issued, B.complainant, 
                               B.complainant_type, B.person_involved, B.location, B.narration, B.status, 
                               B.date_and_time_created, B.remarks, B.date_and_time_updated
                        FROM Blotter B
                        UNION ALL
                        SELECT 'Incident' AS type, I.incident_id AS case_id, I.account_id, I.official_id, I.resident_id, 
                               I.date_of_incident, I.time_of_incident, I.subject, I.date_issued, I.complainant, 
                               I.complainant_type, I.person_involved, I.location, I.narration, I.status, 
                               I.date_and_time_created, I.remarks, I.date_and_time_updated
                        FROM Incident I
                        UNION ALL
                        SELECT 'Complaint' AS type, C.complaint_id AS case_id, C.account_id, C.official_id, C.resident_id, 
                               C.date_of_incident, C.time_of_incident, C.subject, C.date_issued, C.complainant, 
                               C.complainant_type, C.person_involved, C.location, C.narration, C.status, 
                               C.date_and_time_created, C.remarks, C.date_and_time_updated
                        FROM Complaint C
                    ) AS AllReports
                    ORDER BY date_and_time_updated DESC
                    LIMIT ?, ?
                ) AS R
                LEFT JOIN (
                    SELECT o.*, r.first_name, r.middle_name, r.last_name 
                    FROM Officials o
                    LEFT JOIN Residents r ON o.resident_id = r.resident_id
                ) AS o ON R.official_id = o.official_id
                ORDER BY R.date_and_time_updated DESC";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $offset, $limit);
        $stmt->execute();
        $result = $stmt->get_result();

        $reports = [];
        while ($report = $result->fetch_assoc()) {
            // Get evidence based on the type of report
            $evidenceQuery = "";
            if ($report['type'] === 'Blotter') {
                $evidenceQuery = "SELECT evidence_description, evidence_picture FROM Evidence WHERE case_id = ?";
            } elseif ($report['type'] === 'Incident') {
                $evidenceQuery = "SELECT evidence_description, evidence_picture FROM Evidence WHERE incident_id = ?";
            } elseif ($report['type'] === 'Complaint') {
                $evidenceQuery = "SELECT evidence_description, evidence_picture FROM Evidence WHERE complaint_id = ?";
            }

            if ($evidenceQuery) {
                $evidenceStmt = $conn->prepare($evidenceQuery);
                $evidenceStmt->bind_param("s", $report['case_id']);
                $evidenceStmt->execute();
                $evidenceResult = $evidenceStmt->get_result();

                $report['evidence'] = [];
                while ($evidence = $evidenceResult->fetch_assoc()) {
                    $report['evidence'][] = $evidence;
                }
            }

            $reports[] = $report;
        }

        return $reports;
    }

    // Function to get the total number of reports (Blotter, Incident, Complaint) for pagination calculation
    public function getTotalReports($conn, $residentId) {
        // Count the total number of reports for the specific resident ID
        $query = "SELECT COUNT(*) AS total FROM (
                    SELECT case_id FROM Blotter WHERE resident_id = ?
                    UNION ALL
                    SELECT incident_id FROM Incident WHERE resident_id = ?
                    UNION ALL
                    SELECT complaint_id FROM Complaint WHERE resident_id = ?
                  ) AS AllReports";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iii", $residentId, $residentId, $residentId); // Bind resident ID for all report types
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return $row['total']; // Return the total number of reports
    }

    public function searchReports($conn, $residentId, $query) {
        $searchQuery = "
        SELECT 
            'Blotter' AS type, 
            B.case_id, 
            B.account_id, 
            B.official_id, 
            B.resident_id, 
            B.date_of_incident, 
            B.time_of_incident, 
            B.subject, 
            B.date_issued, 
            B.complainant, 
            B.complainant_type, 
            B.person_involved, 
            B.location, 
            B.narration, 
            B.status, 
            B.date_and_time_created, 
            B.date_and_time_updated, 
            B.remarks, 
            E.evidence_description, 
            E.evidence_picture
        FROM Blotter B
        LEFT JOIN Evidence E ON B.case_id = E.case_id
        WHERE B.resident_id = ? AND (
            B.case_id LIKE ? 
            OR B.complainant LIKE ? 
            OR B.subject LIKE ? 
            OR B.location LIKE ? 
            OR B.narration LIKE ? 
            OR B.person_involved LIKE ? 
            OR B.date_of_incident LIKE ? 
            OR TIME_FORMAT(B.time_of_incident, '%h:%i %p') LIKE ? 
            OR B.status LIKE ?
            OR B.remarks LIKE ?
        )
        UNION ALL
        SELECT 
            'Incident' AS type, 
            I.incident_id, 
            I.account_id, 
            I.official_id, 
            I.resident_id, 
            I.date_of_incident, 
            I.time_of_incident, 
            I.subject, 
            I.date_issued, 
            I.complainant, 
            I.complainant_type, 
            I.person_involved, 
            I.location, 
            I.narration, 
            I.status, 
            I.date_and_time_created, 
            I.date_and_time_updated, 
            I.remarks, 
            E.evidence_description, 
            E.evidence_picture
        FROM Incident I
        LEFT JOIN Evidence E ON I.incident_id = E.incident_id
        WHERE I.resident_id = ? AND (
            I.incident_id LIKE ? 
            OR I.complainant LIKE ? 
            OR I.subject LIKE ? 
            OR I.location LIKE ? 
            OR I.narration LIKE ? 
            OR I.person_involved LIKE ? 
            OR I.date_of_incident LIKE ? 
            OR TIME_FORMAT(I.time_of_incident, '%h:%i %p') LIKE ? 
            OR I.status LIKE ?
            OR I.remarks LIKE ?
        )
        UNION ALL
        SELECT 
            'Complaint' AS type, 
            C.complaint_id, 
            C.account_id, 
            C.official_id, 
            C.resident_id, 
            C.date_of_incident, 
            C.time_of_incident, 
            C.subject, 
            C.date_issued, 
            C.complainant, 
            C.complainant_type, 
            C.person_involved, 
            C.location, 
            C.narration, 
            C.status, 
            C.date_and_time_created, 
            C.date_and_time_updated, 
            C.remarks, 
            E.evidence_description, 
            E.evidence_picture
        FROM Complaint C
        LEFT JOIN Evidence E ON C.complaint_id = E.complaint_id
        WHERE C.resident_id = ? AND (
            C.complaint_id LIKE ? 
            OR C.complainant LIKE ? 
            OR C.subject LIKE ? 
            OR C.location LIKE ? 
            OR C.narration LIKE ? 
            OR C.person_involved LIKE ? 
            OR C.date_of_incident LIKE ? 
            OR TIME_FORMAT(C.time_of_incident, '%h:%i %p') LIKE ? 
            OR C.status LIKE ?
            OR C.remarks LIKE ?
        )
        ORDER BY date_and_time_updated DESC";
    
        $stmt = $conn->prepare($searchQuery);
        $searchTerm = "%{$query}%";
    
        // Bind parameters for the search (33 placeholders: 3 sections with 1 integer + 10 strings each)
        $stmt->bind_param(
            "issssssssssissssssssssissssssssss", 
            $residentId, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, 
            $residentId, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, 
            $residentId, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm
        );
    
        // Execute and fetch results
        if ($stmt->execute()) {
            $result = $stmt->get_result();
        } else {
            echo "Error executing query: " . $stmt->error;
            return [];
        }
    
        $reports = [];
        while ($report = $result->fetch_assoc()) {
            $reports[] = $report;
        }
    
        return $reports;
    }

    // Function to create a new report (Blotter, Incident, or Complaint)
    public function createReport($conn, $reportType, $reportData, $evidenceData)
    {
        $uploadFolder = "../../img/image_evidences/{$reportType}";
        if (!is_dir($uploadFolder)) mkdir($uploadFolder, 0777, true);
        if (!is_writable($uploadFolder)) return false;

        // Determine table and ID prefix based on report type
        $table = ucfirst($reportType);
        $idColumn = strtolower($reportType) . '_id';
        $idPrefix = strtoupper(substr($reportType, 0, 3)) . '-';

        // Get the last ID and generate a new one
        $query = "SELECT {$idColumn} FROM {$table} ORDER BY {$idColumn} DESC LIMIT 1";
        $result = $conn->query($query);
        $last_id = $result->fetch_assoc()[$idColumn];

        $next_number = $last_id ? intval(substr($last_id, 4)) + 1 : 1;
        $new_id = $idPrefix . str_pad($next_number, 3, '0', STR_PAD_LEFT);

        // Insert the report into the appropriate table
        $query = "INSERT INTO {$table} 
                ({$idColumn}, account_id, official_id, resident_id, date_of_incident, time_of_incident,
                subject, complainant, complainant_type, person_involved, location, narration, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'resident', ?, ?, ?, ?)";

        $stmt = $conn->prepare($query);
        $stmt->bind_param(
            "siiissssssss",
            $new_id,
            $reportData['account_id'],
            $reportData['official_id'],
            $reportData['resident_id'],
            $reportData['date_of_incident'],
            $reportData['time_of_incident'],
            $reportData['subject'],
            $reportData['complainant'],
            $reportData['person_involved'],
            $reportData['location'],
            $reportData['narration'],
            $reportData['status']
        );

        if ($stmt->execute()) {
            // Insert evidence data if provided
            $query_evidence = "INSERT INTO Evidence ({$idColumn}, evidence_description, evidence_picture) VALUES (?, ?, ?)";
            $stmt_evidence = $conn->prepare($query_evidence);

            foreach ($evidenceData as $evidence) {
                $stmt_evidence->bind_param("sss", $new_id, $evidence['evidence_description'], $evidence['evidence_picture']);
                if (!$stmt_evidence->execute()) {
                    echo "Error inserting evidence: " . $stmt_evidence->error;
                    return false;
                }
            }

            return $new_id;
        } else {
            echo "Error inserting {$reportType}: " . $stmt->error;
            return false;
        }
    }
    
    // Function to update an existing report (Complaint or Incident only)
    public function updateReport($conn, $reportType, $reportData, $evidenceData = null) {
        // Start a transaction to ensure both report and evidence updates succeed or fail together
        $conn->begin_transaction();

        try {
            // Determine table and ID column based on report type
            $table = ucfirst($reportType);
            $idColumn = strtolower($reportType) . '_id';

            // Update the report
            $query = "UPDATE {$table} SET
                    account_id = ?,
                    official_id = ?,
                    resident_id = ?,
                    date_of_incident = ?,
                    time_of_incident = ?,
                    subject = ?,
                    complainant = ?,
                    complainant_type = ?,
                    person_involved = ?,
                    location = ?,
                    narration = ?,
                    status = ?
                WHERE {$idColumn} = ?";

            $stmt = $conn->prepare($query);
            $stmt->bind_param(
                "iiissssssssss",
                $reportData['account_id'],
                $reportData['official_id'],
                $reportData['resident_id'],
                $reportData['date_of_incident'],
                $reportData['time_of_incident'],
                $reportData['subject'],
                $reportData['complainant'],
                $reportData['complainant_type'],
                $reportData['person_involved'],
                $reportData['location'],
                $reportData['narration'],
                $reportData['status'],
                $reportData[$idColumn]
            );

            if (!$stmt->execute()) {
                throw new Exception("Report update error: " . $stmt->error);
            }

            // Check if there is new evidence data to insert
            if (!empty($evidenceData)) {
                $query_insert_evidence = "INSERT INTO Evidence ({$idColumn}, evidence_description, evidence_picture) 
                                        VALUES (?, ?, ?)";
                $stmt_insert_evidence = $conn->prepare($query_insert_evidence);

                foreach ($evidenceData as $evidence) {
                    $fileDestination = $evidence['evidence_picture']; // Already the file path

                    // Insert the file path into the database
                    $stmt_insert_evidence->bind_param(
                        "sss",
                        $reportData[$idColumn],
                        $evidence['evidence_description'],
                        $fileDestination
                    );

                    if (!$stmt_insert_evidence->execute()) {
                        throw new Exception("Error inserting evidence: " . $stmt_insert_evidence->error);
                    }
                }
            }

            // Commit the transaction if both report and evidence updates succeed
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
