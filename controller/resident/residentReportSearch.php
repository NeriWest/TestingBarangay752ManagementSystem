<?php
require_once '../../model/resident/residentReportModel.php';

// Initialize the report model
$residentReportModel = new ReportModel(); // Assuming this is the correct model for reports

// Check if a search query is set
if (isset($_POST['query'])) {
    $query = $_POST['query'];
    $residentID = isset($_POST['resident_id']) ? $_POST['resident_id'] : (isset($_SESSION['resident_id']) ? $_SESSION['resident_id'] : null); // Use resident_id from POST, fallback to session, or null if not set

    // Fetch reports based on the search query
    $reports = $residentReportModel->searchReports($conn, $residentID, $query);

    if (!empty($reports)) {
        $groupedReports = [];
        foreach ($reports as $report) {
            $case_id = $report['case_id'];
            if (!isset($groupedReports[$case_id])) {
                $groupedReports[$case_id] = [
                    'case_info' => $report,
                    'images' => []
                ];
            }
            if (!empty($report['evidence_picture'])) {
                $groupedReports[$case_id]['images'][] = $report['evidence_picture'];
            }
        }

        foreach ($groupedReports as $groupedReport) {
            $report = $groupedReport['case_info'];
            echo "<tr>";
            echo "<td>" . htmlspecialchars($report['case_id']) . "</td>";
            echo "<td>" . htmlspecialchars($report['complainant']) . "</td>";
            echo "<td>" . htmlspecialchars($report['subject']) . "</td>";
            echo "<td>" . htmlspecialchars($report['date_of_incident']) . "</td>";
            echo "<td>" . date("g:i A", strtotime(htmlspecialchars($report['time_of_incident']))) . "</td>";
            echo "<td>" . htmlspecialchars($report['location']) . "</td>";
            echo "<td>" . htmlspecialchars($report['person_involved']) . "</td>";
            echo "<td>";
            if (strlen($report['narration']) > 19) {
                echo htmlspecialchars(substr($report['narration'], 0, 19)) . "...";
            } else {
                echo htmlspecialchars($report['narration']);
            }
            echo "</td>";
            echo "</td>";
            echo "<td>";
            if (empty($report['evidence_description'])) {
              echo "N/A";
            } else {
              echo htmlspecialchars($report['evidence_description']);
            }
            echo "</td>";
            echo "<td>";
            if (!empty($groupedReport['images'])) {
                foreach ($groupedReport['images'] as $imagePath) {
                    $imagePathEscaped = htmlspecialchars($imagePath);
                    echo "<a href='" . $imagePathEscaped . "' target='_blank'>";
                    echo "<img src='" . $imagePathEscaped . "' alt='Evidence Image' width='50' height='50' style='margin-right:5px;'>";
                    echo "</a>";
                }
            } else {
                echo "No image evidence";
            }
            echo "</td>";
            echo "<td>";
            if (empty($report['remarks'])) {
                echo "N/A";
            } elseif (strlen($report['remarks']) > 16) {
                echo htmlspecialchars(substr($report['remarks'], 0, 16)) . "...";
            } else {
                echo htmlspecialchars($report['remarks']);
            }
            echo "</td>";
            $statusColor = ($report['status'] === 'resolved') ? 'green' : 
                           (($report['status'] === 'pending') ? 'orange' : 
                           (($report['status'] === 'closed') ? 'red' : 'gray'));
            echo "<td><span style='
                background-color: $statusColor; 
                color: white; 
                padding: 5px 10px; 
                border-radius: 5px; 
                font-weight: bold; 
                box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3); 
                text-shadow: -1px -1px 0 black, 
                             1px -1px 0 black, 
                             -1px 1px 0 black, 
                             1px 1px 0 black;'>"
                . htmlspecialchars($report['status']) . "</span></td>";
            $evidenceJson = json_encode($groupedReport['images']);
            echo "<td>";
            // View Button for Closed or Resolved
            if (strtolower($report['status']) === 'closed' || strtolower($report['status']) === 'resolved') {
                echo "<button class='modal-open-view button' aria-haspopup='true' style='background-color:rgb(92, 92, 92); margin-bottom: 10px;' 
                        data-case-id='" . htmlspecialchars($report['case_id']) . "' 
                        data-complainant='" . htmlspecialchars($report['complainant']) . "' 
                        data-subject='" . htmlspecialchars($report['subject']) . "' 
                        data-date-of-incident='" . htmlspecialchars($report['date_of_incident']) . "' 
                        data-time-of-incident='" . date("g:i A", strtotime(htmlspecialchars($report['time_of_incident']))) . "' 
                        data-location='" . htmlspecialchars($report['location']) . "' 
                        data-person-involved='" . htmlspecialchars($report['person_involved']) . "' 
                        data-narration='" . htmlspecialchars($report['narration']) . "' 
                        data-status='" . htmlspecialchars($report['status']) . "' 
                        data-evidence-description='" . htmlspecialchars($report['evidence_description']) . "' 
                        data-evidence='" . htmlspecialchars($evidenceJson) . "' 
                        data-remarks='" . htmlspecialchars($report['remarks'] ?? '') . "' 
                        onclick='populateViewModal(this)'>
                        <i class='fa-solid fa-eye'></i>
                      </button>";
            }
            // Edit Button for Pending
            if (strtolower($report['status']) === 'pending') {
                echo "<button class='modal-open button' aria-haspopup='true' 
                        data-case-id='" . htmlspecialchars($report['case_id']) . "' 
                        data-complainant='" . htmlspecialchars($report['complainant']) . "' 
                        data-subject='" . htmlspecialchars($report['subject']) . "' 
                        data-date-of-incident='" . htmlspecialchars($report['date_of_incident']) . "' 
                        data-time-of-incident='" . htmlspecialchars($report['time_of_incident']) . "' 
                        data-location='" . htmlspecialchars($report['location']) . "' 
                        data-person-involved='" . htmlspecialchars($report['person_involved']) . "' 
                        data-narration='" . htmlspecialchars($report['narration']) . "' 
                        data-status='" . htmlspecialchars($report['status']) . "' 
                        data-evidence-description='" . htmlspecialchars($report['evidence_description']) . "' 
                        data-evidence='" . htmlspecialchars($evidenceJson) . "' 
                        data-remarks='" . htmlspecialchars($report['remarks'] ?? '') . "' 
                        onclick='populateBlotterModal(this)'>
                        <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-pen' viewBox='0 0 16 16'>
                          <path d='m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001m-.644.766a.5.5 0 0 0-.707 0L1.95 11.756l-.764 3.057 3.057-.764L14.44 3.854a.5.5 0 0 0 0-.708z'/>
                        </svg>
                      </button>";
            }
            echo "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='13'>No report records found.</td></tr>";
    }
}
?>