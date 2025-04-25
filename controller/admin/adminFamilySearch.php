<?php
require_once '../../model/admin/adminFamilyModel.php';

// Initialize the family model
$adminFamilyModel = new AdminFamilyModel();

function getAddress($houseNumber, $street)
{
    $address = $houseNumber . " " .  $street . " Singalong, Malate, Manila";
    return $address;
}


// Check if a search query is set
if (isset($_POST['query'])) {
    $query = $_POST['query'];

    // Fetch families based on the search query
    $families = $adminFamilyModel->searchFamilies($conn, $query);

    if (!empty($families)) {
        foreach ($families as $family) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($family['family_id']) . "</td>";
            echo "<td>" . htmlspecialchars($family['family_name']) . "</td>";
            echo "<td>" . htmlspecialchars(getAddress($family['house_number'], $family['street'])) . "</td>";

            // Display family members
            if (!empty($family['residents'])) {
                echo "<td>" . html_entity_decode($family['residents']) . "</td>";
            } else {
                echo "<td>No members</td>";
            }

            // Display total income (already calculated in the model)
            echo "<td>₱" . htmlspecialchars(number_format($family['total_income'], 2)) . "</td>";

            echo "<td>" . htmlspecialchars($family['created_at']) . "</td>";
            echo "<td>
                    <button class='modal-open button' 
                            data-family-id='" . htmlspecialchars($family['family_id']) . "' 
                            data-family-name='" . htmlspecialchars($family['family_name']) . "' 
                            data-house-number='" . htmlspecialchars($family['house_number']) . "' 
                            data-street='" . htmlspecialchars($family['street']) . "' 
                            data-residents='" . htmlspecialchars($family['residents']) . "' 
                            data-total-income='" . htmlspecialchars($family['total_income']) . "' 
                            onclick='populateModal(this)'>
                        <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-pen' viewBox='0 0 16 16'>
                            <path d='m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001m-.644.766a.5.5 0 0 0-.707 0L1.95 11.756l-.764 3.057 3.057-.764L14.44 3.854a.5.5 0 0 0 0-.708z'/>
                        </svg>
                    </button>
                </td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='7'>No families found.</td></tr>";
    }
}
?>