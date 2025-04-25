<?php
require_once '../../model/admin/adminResidentModel.php';

function getAddress($houseNumber, $street) {
    return $houseNumber . " " . $street . " Singalong, Malate, Manila";
}

// Initialize the resident model
$adminResidentModel = new AdminResidentModel();

// Check if a search query is set
if (isset($_POST['query'])) {
    $query = $_POST['query'];

    // Fetch residents based on the search query
    $residents = $adminResidentModel->searchResidents($conn, $query);

    if (!empty($residents)) {
        $current_date = date('Y-m-d');

        foreach ($residents as $resident) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($resident['last_name']) . "</td>";
            echo "<td>" . htmlspecialchars($resident['first_name']) . "</td>";
            echo "<td>" . htmlspecialchars($resident['middle_name']) . "</td>";
            echo "<td>" . htmlspecialchars($resident['suffix']) . "</td>";
            echo "<td>" . htmlspecialchars($resident['cellphone_number']) . "</td>";
            echo "<td>" . htmlspecialchars($resident['sex']) . "</td>";
            echo "<td>" . htmlspecialchars($resident['age']) . "</td>";
            echo "<td>" . getAddress($resident['house_number'], $resident['street']) . "</td>";
            echo "<td>" . htmlspecialchars($resident['date_of_birth']) . "</td>";
            echo "<td>" . htmlspecialchars($resident['civil_status']) . "</td>";
            echo "<td>" . htmlspecialchars($resident['house_number']) . "</td>";
            echo "<td>" . htmlspecialchars($resident['street']) . "</td>";
            echo "<td>" . htmlspecialchars($resident['citizenship']) . "</td>";
            echo "<td>" . htmlspecialchars($resident['email']) . "</td>";
            echo "<td>" . htmlspecialchars($resident['occupation']) . "</td>";
            echo "<td>" . htmlspecialchars($resident['disability']) . "</td>";
            echo "<td>" . htmlspecialchars($resident['voter_status']) . "</td>";

            echo "<td>" . htmlspecialchars($resident['status']) . "</td>";
            echo "<td>";
            echo '<button class="modal-open button" aria-haspopup="true"  style="background-color:rgb(92, 92, 92);"
                data-resident-id="' . htmlspecialchars($resident['resident_id']) . '"
                data-last-name="' . htmlspecialchars($resident['last_name']) . '"
                data-first-name="' . htmlspecialchars($resident['first_name']) . '"
                data-middle-name="' . htmlspecialchars($resident['middle_name']) . '"
                data-suffix="' . htmlspecialchars($resident['suffix']) . '"
                data-phone-number="' . htmlspecialchars($resident['cellphone_number']) . '"
                data-sex="' . htmlspecialchars($resident['sex']) . '"
                data-date-of-birth="' . htmlspecialchars($resident['date_of_birth']) . '"
                data-civil-status="' . htmlspecialchars($resident['civil_status']) . '"
                data-house-number="' . htmlspecialchars($resident['house_number']) . '"
                data-street="' . htmlspecialchars($resident['street']) . '"
                data-citizenship="' . htmlspecialchars($resident['citizenship']) . '"
                data-occupation="' . htmlspecialchars($resident['occupation']) . '"
                data-disability="' . htmlspecialchars($resident['disability']) . '"
                data-email="' . htmlspecialchars($resident['email']) . '"
                data-voter-status="' . htmlspecialchars($resident['voter_status']) . '"
                data-status="' . htmlspecialchars($resident['status']) . '"
                onclick="populateModal(this)">
                <i class="fa-solid fa-eye"></i>
                </button>';
            echo "</td>";

            
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='18'>No residents found.</td></tr>";
    }
}
?>
