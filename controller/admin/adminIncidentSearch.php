<?php
require_once '../../model/admin/adminReportModel.php';

// Initialize the incident model
$adminReportModel = new ReportModel();

// Check if a search query is set
if (isset($_POST['query'])) {
    $query = $_POST['query'];

    // Fetch incidents based on the search query
    $incidents = $adminReportModel->searchIncidents($conn, $query);

    if (!empty($incidents)) {
        $groupedIncidents = [];

        // Group incidents by incident_id
        foreach ($incidents as $incident) {
            $incident_id = $incident['incident_id'];

            if (!isset($groupedIncidents[$incident_id])) {
                // Initialize group for new incident_id
                $groupedIncidents[$incident_id] = [
                    'incident_info' => $incident,
                    'images' => []
                ];
            }

            // Add evidence image if available
            if (!empty($incident['evidence_picture'])) {
                $groupedIncidents[$incident_id]['images'][] = $incident['evidence_picture'];
            }
        }

        // Print the table rows
        foreach ($groupedIncidents as $groupedIncident) {
            $incident = $groupedIncident['incident_info'];
            echo "<tr>";
            echo "<td>" . htmlspecialchars($incident['incident_id']) . "</td>";                // Incident Number
            echo "<td>" . htmlspecialchars($incident['complainant']) . "</td>";                 // Complainant
            echo "<td>" . htmlspecialchars($incident['complainant_type']) . "</td>";            // Complainant Type
            echo "<td>" . htmlspecialchars($incident['subject']) . "</td>";                     // Subject
            echo "<td>" . htmlspecialchars($incident['official_name'] ?? 'N/A') . "</td>"; // Official
            echo "<td>" . htmlspecialchars($incident['date_of_incident']) . "</td>";            // Date of incident
            echo "<td>" . htmlspecialchars($incident['time_of_incident']) . "</td>";            // Time of incident
            echo "<td>" . htmlspecialchars($incident['location']) . "</td>";                    // Location
            echo "<td>" . htmlspecialchars($incident['person_involved']) . "</td>";             // Person involved

            // Handle narration with length more than 19 characters
            echo "<td>";
            if (strlen($incident['narration']) > 19) {
                echo htmlspecialchars(substr($incident['narration'], 0, 19)) . "...";
            } else {
                echo htmlspecialchars($incident['narration']);
            }
            echo "</td>";

            // Evidence description
            echo "<td>";
            if (empty($incident['evidence_description'])) {
                echo "No description provided";
            } elseif (strlen($incident['evidence_description']) > 16) {
                echo htmlspecialchars(substr($incident['evidence_description'], 0, 16)) . "...";
            } else {
                echo htmlspecialchars($incident['evidence_description']);
            }
            echo "</td>";

            // Display evidence images
            echo "<td>";
            if (!empty($groupedIncident['images'])) {
                foreach ($groupedIncident['images'] as $imagePath) {
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
            if (empty($incident['remarks'])) {
                echo "N/A";
            } elseif (strlen($incident['remarks']) > 16) {
                echo htmlspecialchars(substr($incident['remarks'], 0, 16)) . "...";
            } else {
                echo htmlspecialchars($incident['remarks']);
            }
            echo "</td>";

            $statusColor = ($incident['status'] === 'resolved') ? 'green' : 
                            (($incident['status'] === 'pending') ? 'orange' : 
                            (($incident['status'] === 'closed') ? 'red' : 'gray'));
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
            '>" . htmlspecialchars($incident['status']) . "</span></td>";

            echo '<td>
                <div style="display: flex; gap: 10px;">';
            if ($incident['status'] === 'pending') {
                echo '
                    <button class="resolve-button buttons" 
                        style="background-color: #008000; color: white; border: none; padding: 8px 12px; border-radius: 5px; cursor: pointer; font-weight: bold; text-shadow: -1px -1px 0 black, 1px -1px 0 black, -1px 1px 0 black, 1px 1px 0 black;"
                        onclick="showResolvePopup(\'' . htmlspecialchars($incident['incident_id'], ENT_QUOTES, 'UTF-8') . '\')">
                        Resolve
                    </button>
                    <button class="close-button buttons" 
                        style="background-color: #ff0707; color: white; border: none; padding: 8px 12px; border-radius: 5px; cursor: pointer; font-weight: bold; text-shadow: -1px -1px 0 black, 1px -1px 0 black, -1px 1px 0 black, 1px 1px 0 black;"
                        onclick="showClosePopup(\'' . htmlspecialchars($incident['incident_id'], ENT_QUOTES, 'UTF-8') . '\')">
                        Close
                    </button>';
            }
            // Button for opening modal, passing data including evidence as JSON
            $evidenceJson = json_encode($groupedIncident['images']);
            echo "
                <button class='modal-open button' aria-haspopup='true' 
                        data-incident-id='" . htmlspecialchars($incident['incident_id']) . "' 
                        data-complainant='" . htmlspecialchars($incident['complainant']) . "' 
                        data-complainant-type='" . htmlspecialchars($incident['complainant_type']) . "' 
                        data-subject='" . htmlspecialchars($incident['subject']) . "' 
                        data-official-id='" . htmlspecialchars($incident['official_id']) . "' 
                        data-date-of-incident='" . htmlspecialchars($incident['date_of_incident']) . "' 
                        data-time-of-incident='" . htmlspecialchars($incident['time_of_incident']) . "' 
                        data-location='" . htmlspecialchars($incident['location']) . "' 
                        data-person-involved='" . htmlspecialchars($incident['person_involved']) . "' 
                        data-narration='" . htmlspecialchars($incident['narration']) . "' 
                        data-status='" . htmlspecialchars($incident['status']) . "' 
                        data-evidence-description='" . htmlspecialchars($incident['evidence_description']) . "' 
                        data-evidence='" . htmlspecialchars($evidenceJson) . "' 
                        onclick='populateIncidentModal(this)'>
                        <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-pen' viewBox='0 0 16 16'>
                            <path d='m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001m-.644.766a.5.5 0 0 0-.707 0L1.95 11.756l-.764 3.057 3.057-.764L14.44 3.854a.5.5 0 0 0 0-.708z'/>
                        </svg>
                    </button>
                </div>
            </td>";

            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='15'>No incident records found.</td></tr>";
    }
}
?>
