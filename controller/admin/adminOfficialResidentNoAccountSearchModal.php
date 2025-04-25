<?php
require_once '../../model/admin/adminResidentModel.php'; // Include your model to fetch resident data

// Instantiate the model
$adminResidentModel = new adminResidentModel();

if (isset($_POST['searchQuery'])) {
    $searchQuery = $_POST['searchQuery'];
    
    // Call the search method from the model
    $residents = $adminResidentModel->searchOfficialResidentsWithNoAccount($conn, $searchQuery); 

    // Check if residents are found
    if (!empty($residents)) {
        echo "<option value='' disabled selected>Select a resident with no account</option>";
        foreach ($residents as $resident) {
            echo "<option value='" . htmlspecialchars($resident['resident_id']) . "'>" 
            . htmlspecialchars($resident['last_name']) . ", " 
            . htmlspecialchars($resident['first_name']) . " " 
            . htmlspecialchars($resident['middle_name']) 
            . " (" . htmlspecialchars($resident['age']) . " years old, " 
            . htmlspecialchars($resident['position'] ?? 'No position') . ")</option>";
        }
    } else {
        echo "<option>No residents found</option>";
    }
}
?>
