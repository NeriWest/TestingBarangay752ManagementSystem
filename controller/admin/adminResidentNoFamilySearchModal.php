<?php
require_once '../../model/admin/adminResidentModel.php'; // Include your model to fetch resident data

// Instantiate the model
$adminResidentModel = new adminResidentModel();

if (isset($_POST['searchQuery'])) {
    $searchQuery = $_POST['searchQuery'];
    
    // Call the search method from the model
    $residents = $adminResidentModel->searchResidentsWithNoFamily($conn, $searchQuery); 

    // Check if residents are found
    if (!empty($residents)) {
        echo "<option value='' disabled selected>Select a resident with no account</option>";
        foreach ($residents as $resident) {
            echo "<option value='" . $resident['id'] . "'>" 
                . htmlspecialchars($resident['last_name']) . ", " 
                . htmlspecialchars($resident['first_name']) . " " 
                . htmlspecialchars($resident['middle_name']) 
                . " (" . htmlspecialchars($resident['age']) . " years old)</option>";
        }
    } else {
        echo "<option disabled>No residents found</option>";
    }
}
?>