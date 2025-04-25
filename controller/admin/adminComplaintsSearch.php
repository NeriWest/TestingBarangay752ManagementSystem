<?php
require_once '../../model/admin/adminReportModel.php';

// Initialize the complaint model
$adminReportModel = new ReportModel();

// Check if a search query is set
if (isset($_POST['query'])) {
    $query = $_POST['query'];

    // Fetch complaints based on the search query
    $complaints = $adminReportModel->searchComplaints($conn, $query); // Update method to search for complaints

    if (!empty($complaints)) {
        $groupedComplaints = [];

        // Group complaints by complaint_id
        foreach ($complaints as $complaint) {
            $complaint_id = $complaint['complaint_id'];

            if (!isset($groupedComplaints[$complaint_id])) {
                // Initialize group for new complaint_id
                $groupedComplaints[$complaint_id] = [
                    'complaint_info' => $complaint,
                    'images' => []
                ];
            }

            // Add evidence image if available
            if (!empty($complaint['evidence_picture'])) {
                $groupedComplaints[$complaint_id]['images'][] = $complaint['evidence_picture'];
            }
        }

        // Print the table rows
        foreach ($groupedComplaints as $groupedComplaint) {
            $complaint = $groupedComplaint['complaint_info'];
            echo "<tr>";
            echo "<td>" . htmlspecialchars($complaint['complaint_id']) . "</td>";                // Complaint Number
            echo "<td>" . htmlspecialchars($complaint['complainant']) . "</td>";                 // Complainant
            echo "<td>" . htmlspecialchars($complaint['complainant_type']) . "</td>";            // Complainant Type
            echo "<td>" . htmlspecialchars($complaint['subject']) . "</td>";                     // Subject
            echo "<td>" . (!empty($complaint['official_name']) ? htmlspecialchars($complaint['official_name']) : 'N/A') . "</td>"; // Official
            echo "<td>" . htmlspecialchars($complaint['date_of_incident']) . "</td>";            // Date of incident
            echo "<td>" . htmlspecialchars($complaint['time_of_incident']) . "</td>";            // Time of incident
            echo "<td>" . htmlspecialchars($complaint['location']) . "</td>";                    // Location
            echo "<td>" . htmlspecialchars($complaint['person_involved']) . "</td>";             // Person involved

            // Handle narration with length more than 19 characters
            echo "<td>";
            if (strlen($complaint['narration']) > 19) {
                echo htmlspecialchars(substr($complaint['narration'], 0, 19)) . "...";
            } else {
                echo htmlspecialchars($complaint['narration']);
            }
            echo "</td>";

            // Evidence description
            echo "<td>";
            if (empty($complaint['evidence_description'])) {
                echo "No description provided";
            } elseif (strlen($complaint['evidence_description']) > 16) {
                echo htmlspecialchars(substr($complaint['evidence_description'], 0, 16)) . "...";
            } else {
                echo htmlspecialchars($complaint['evidence_description']);
            }
            echo "</td>";

            // Display evidence images
            echo "<td>";
            if (!empty($groupedComplaint['images'])) {
                foreach ($groupedComplaint['images'] as $imagePath) {
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
            if (empty($complaint['remarks'])) {
                echo "N/A";
            } elseif (strlen($complaint['remarks']) > 16) {
                echo htmlspecialchars(substr($complaint['remarks'], 0, 16)) . "...";
            } else {
                echo htmlspecialchars($complaint['remarks']);
            }
            echo "</td>";

            $statusColor = ($complaint['status'] === 'resolved') ? 'green' : 
                           (($complaint['status'] === 'pending') ? 'orange' : 
                           (($complaint['status'] === 'closed') ? 'red' : 'gray'));
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
                             1px 1px 0 black; /* Creates an outline effect */
            '>" . htmlspecialchars($complaint['status']) . "</span></td>";


            echo '<td>';
            
            echo '<div style="display: flex; gap: 10px;">';
            if ($complaint['status'] === 'pending') {
                echo '
                    <button class="resolve-button buttons" 
                        style="background-color: #008000; color: white; border: none; padding: 8px 12px; border-radius: 5px; cursor: pointer; font-weight: bold; text-shadow: -1px -1px 0 black, 1px -1px 0 black, -1px 1px 0 black, 1px 1px 0 black;"
                        onclick="showResolvePopup(\'' . htmlspecialchars($complaint['complaint_id'], ENT_QUOTES, 'UTF-8') . '\')">
                        Resolve
                    </button>
                    <button class="close-button buttons" 
                        style="background-color: #ff0707; color: white; border: none; padding: 8px 12px; border-radius: 5px; cursor: pointer; font-weight: bold; text-shadow: -1px -1px 0 black, 1px -1px 0 black, -1px 1px 0 black, 1px 1px 0 black;"
                        onclick="showClosePopup(\'' . htmlspecialchars($complaint['complaint_id'], ENT_QUOTES, 'UTF-8') . '\')">
                        Close
                    </button>';
            } 
            // Button for opening modal, passing data including evidence as JSON
            $evidenceJson = json_encode($groupedComplaint['images']);
            echo "

            <button class='modal-open button' aria-haspopup='true' 
                    data-complaint-id='" . htmlspecialchars($complaint['complaint_id']) . "' 
                    data-complainant='" . htmlspecialchars($complaint['complainant']) . "' 
                    data-complainant-type='" . htmlspecialchars($complaint['complainant_type']) . "' 
                    data-subject='" . htmlspecialchars($complaint['subject']) . "' 
                    data-official-id='" . htmlspecialchars($complaint['official_id']) . "' 
                    data-date-of-incident='" . htmlspecialchars($complaint['date_of_incident']) . "' 
                    data-time-of-incident='" . htmlspecialchars($complaint['time_of_incident']) . "' 
                    data-location='" . htmlspecialchars($complaint['location']) . "' 
                    data-person-involved='" . htmlspecialchars($complaint['person_involved']) . "' 
                    data-narration='" . htmlspecialchars($complaint['narration']) . "' 
                    data-status='" . htmlspecialchars($complaint['status']) . "' 
                    data-evidence-description='" . htmlspecialchars($complaint['evidence_description']) . "' 
                    data-evidence='" . htmlspecialchars($evidenceJson) . "' 
                    onclick='populateComplaintModal(this)'>
                    <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-pen' viewBox='0 0 16 16'>
                        <path d='m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001m-.644.766a.5.5 0 0 0-.707 0L1.95 11.756l-.764 3.057 3.057-.764L14.44 3.854a.5.5 0 0 0 0-.708z'/>
                    </svg>
                </button>
            
            </div>

        </td>";

            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='15'>No complaint records found.</td></tr>";
    }
}
?>
