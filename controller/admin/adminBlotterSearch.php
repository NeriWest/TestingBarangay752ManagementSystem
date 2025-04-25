<?php
require_once '../../model/admin/adminReportModel.php';

// Initialize the blotter model
$adminReportModel = new ReportModel();

// Check if a search query is set
if (isset($_POST['query'])) {
    $query = $_POST['query'];

    // Fetch blotters based on the search query
    $blotters = $adminReportModel->searchBlotters($conn, $query);

    if (!empty($blotters)) {
        $groupedBlotters = [];
        
        // Group blotters by case_id
        foreach ($blotters as $blotter) {
            $case_id = $blotter['case_id'];
            
            if (!isset($groupedBlotters[$case_id])) {
                // Initialize group for new case_id
                $groupedBlotters[$case_id] = [
                    'case_info' => $blotter,
                    'images' => []
                ];
            }
            
            // Add evidence image if available
            if (!empty($blotter['evidence_picture'])) {
                $groupedBlotters[$case_id]['images'][] = $blotter['evidence_picture'];
            }
        }

        // Print the table rows
        foreach ($groupedBlotters as $groupedBlotter) {
            $blotter = $groupedBlotter['case_info'];
            echo "<tr>";
            echo "<td>" . htmlspecialchars($blotter['case_id']) . "</td>";                   // Case Number
            echo "<td>" . htmlspecialchars($blotter['complainant']) . "</td>";                // Complainant
            echo "<td>" . htmlspecialchars($blotter['complainant_type']) . "</td>";                // Complainant
            echo "<td>" . htmlspecialchars($blotter['subject']) . "</td>";                    // Subject
            echo "<td>" . (empty($blotter['official_name']) ? "N/A" : htmlspecialchars($blotter['official_name'])) . "</td>"; // Subject
            echo "<td>" . htmlspecialchars($blotter['date_of_incident']) . "</td>";           // Date of incident
            echo "<td>" . htmlspecialchars($blotter['time_of_incident']) . "</td>";           // Time of incident
            echo "<td>" . htmlspecialchars($blotter['location']) . "</td>";                   // Location
            echo "<td>" . htmlspecialchars($blotter['person_involved']) . "</td>";            // Person involved

            // Handle narration with length more than 19 characters
            echo "<td>";
            if (strlen($blotter['narration']) > 19) {
                echo htmlspecialchars(substr($blotter['narration'], 0, 19)) . "...";
            } else {
                echo htmlspecialchars($blotter['narration']);
            }
            echo "</td>";

            // Evidence description
            echo "<td>";
            if (empty($blotter['evidence_description'])) {
                echo "No description provided";
            } elseif (strlen($blotter['evidence_description']) > 16) {
                echo htmlspecialchars(substr($blotter['evidence_description'], 0, 16)) . "...";
            } else {
                echo htmlspecialchars($blotter['evidence_description']);
            }
            echo "</td>";

            // Display evidence images
            echo "<td>";
            if (!empty($groupedBlotter['images'])) {
                foreach ($groupedBlotter['images'] as $imagePath) {
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
            if (empty($blotter['remarks'])) {
                echo "N/A";
            } elseif (strlen($blotter['remarks']) > 16) {
                echo htmlspecialchars(substr($blotter['remarks'], 0, 16)) . "...";
            } else {
                echo htmlspecialchars($blotter['remarks']);
            }
            echo "</td>";


            $statusColor = ($blotter['status'] === 'resolved') ? 'green' : 
                            (($blotter['status'] === 'pending') ? 'orange' : 
                            (($blotter['status'] === 'closed') ? 'red' : 'gray'));
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
            '>" . htmlspecialchars($blotter['status']) . "</span></td>";

            echo '<div style="display: flex; gap: 10px;">';
            echo '<td>';
            echo '<div style="display: flex; gap: 10px;">';
            if ($blotter['status'] === 'pending') {
                echo '
                    <button class="resolve-button buttons" 
                        style="background-color: #008000; color: white; border: none; padding: 8px 12px; border-radius: 5px; cursor: pointer; font-weight: bold; text-shadow: -1px -1px 0 black, 1px -1px 0 black, -1px 1px 0 black, 1px 1px 0 black;"
                        onclick="showResolvePopup(\'' . htmlspecialchars($blotter['case_id'], ENT_QUOTES, 'UTF-8') . '\')">
                        Resolve
                    </button>
                    <button class="close-button buttons" 
                        style="background-color: #ff0707; color: white; border: none; padding: 8px 12px; border-radius: 5px; cursor: pointer; font-weight: bold; text-shadow: -1px -1px 0 black, 1px -1px 0 black, -1px 1px 0 black, 1px 1px 0 black;"
                        onclick="showClosePopup(\'' . htmlspecialchars($blotter['case_id'], ENT_QUOTES, 'UTF-8') . '\')">
                        Close
                    </button>';
            } 
            // Button for opening modal, passing data including evidence as JSON
            $evidenceJson = json_encode($groupedBlotter['images']);
            echo "

            <button class='modal-open button' aria-haspopup='true' 
                    data-case-id='" . htmlspecialchars($blotter['case_id']) . "' 
                    data-complainant='" . htmlspecialchars($blotter['complainant']) . "' 
                    data-complainant-type='" . htmlspecialchars($blotter['complainant_type']) . "' 
                    data-subject='" . htmlspecialchars($blotter['subject']) . "' 
                    data-official-id='" . htmlspecialchars($blotter['official_id']) . "' 
                    data-date-of-incident='" . htmlspecialchars($blotter['date_of_incident']) . "' 
                    data-time-of-incident='" . htmlspecialchars($blotter['time_of_incident']) . "' 
                    data-location='" . htmlspecialchars($blotter['location']) . "' 
                    data-person-involved='" . htmlspecialchars($blotter['person_involved']) . "' 
                    data-narration='" . htmlspecialchars($blotter['narration']) . "' 
                    data-status='" . htmlspecialchars($blotter['status']) . "' 
                    data-evidence-description='" . htmlspecialchars($blotter['evidence_description']) . "' 
                    data-evidence='" . htmlspecialchars($evidenceJson) . "' 
                onclick='populateBlotterModal(this)'>
                <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-pen' viewBox='0 0 16 16'>
                    <path d='m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001m-.644.766a.5.5 0 0 0-.707 0L1.95 11.756l-.764 3.057 3.057-.764L14.44 3.854a.5.5 0 0 0 0-.708z'/>
                </svg>
            </button>
            
            </div>

        </td>";
        echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='15'>No blotter records found.</td></tr>";
    }
}
?>

