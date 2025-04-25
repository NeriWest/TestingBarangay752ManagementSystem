<?php

require_once '../../model/admin/adminOfficialAccountsModel.php'; // Include your model to fetch resident data

// SEARCH QUERY FOR RESIDENTS
$adminOfficialAccountsModel = new adminOfficialAccountsModel();

if (isset($_POST['query'])) {
    $query = $_POST['query'];
    $accounts = $adminOfficialAccountsModel->searchAccounts($conn, $query);

    if (!empty($accounts)) {
        foreach ($accounts as $account) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($account['account_id']) . "</td>"; // Account ID
            echo "<td>" . htmlspecialchars($account['username']) . "</td>"; // Username

            // Full Name (Concatenated)
            $fullName = htmlspecialchars($account['first_name'] . " " . ($account['middle_name'] ? $account['middle_name'] . " " : "") . $account['last_name']);
            echo "<td>" . $fullName . "</td>";
            echo "<td>" . htmlspecialchars($account['cellphone_number']) . "</td>"; // Cellphone Number
            echo "<td>" . htmlspecialchars($account['date_of_birth']) . "</td>"; // Date of Birth
            echo "<td>" . (!empty($account['email']) ? htmlspecialchars($account['email']) : 'N/A') . "</td>"; // Email
            echo "<td>" . htmlspecialchars($account['role_id'] ?? 'N/A') . " - " . htmlspecialchars($account['role_name'] ?? 'N/A') . "</td>"; // Role ID and Role Name

            $statusColor = ($account['status'] === 'active') ? 'green' : (($account['status'] === 'pending') ? 'orange' : (($account['status'] === 'inactive') ? 'gray' : (($account['status'] === 'disapproved') ? 'red' : 'black')));


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
            '>" . htmlspecialchars($account['status']) . "</span></td>";

            echo "<td>";
            if (!empty($account['id_image_name'])) {
                // Create a link for the ID image, display the image when clicked
                echo "<a href='../../img/id_images/" . htmlspecialchars($account['id_image_name'], ENT_QUOTES) . "' target='_blank'>";
                // Display the ID image thumbnail
                echo "<img src='../../img/id_images/" . htmlspecialchars($account['id_image_name'], ENT_QUOTES) . "' alt='ID Image' width='50' height='50' style='margin-right:5px;'>";
                echo "</a>";
            } else {
                // If no ID image is present, display this message
                echo "No image";
            }
            echo "</td>";


            // Profile Image (Click to open the full image in a new tab)
            echo "<td>";
            if (!empty($account['profile_image_name'])) {
                // Create a link for the profile image, display the image when clicked
                echo "<a href='../../img/profile_images/" . htmlspecialchars($account['profile_image_name'], ENT_QUOTES) . "' target='_blank'>";
                // Display the profile image thumbnail
                echo "<img src='../../img/profile_images/" . htmlspecialchars($account['profile_image_name'], ENT_QUOTES) . "' alt='Profile Image' width='50' height='50' style='margin-right:5px;'>";
                echo "</a>";
            } else {
                // If no profile image is present, display "No image"
                echo "No image";
            }
            echo "</td>";
            // Actions Button
            echo "<td>";

            echo '<div style="display: flex; gap: 5px;">';

            if ($account['status'] === 'active') {
                echo '
                <button class="revoke-btn buttons" 
                        data-account-id="' . htmlspecialchars($account['account_id'], ENT_QUOTES) . '"
                        style="background-color:rgb(255, 7, 7); color: white; border: none; padding: 8px; border-radius: 5px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-weight: bold; text-shadow: -1px -1px 0 black, 1px -1px 0 black, -1px 1px 0 black, 1px 1px 0 black;"
                        onclick="showRevokePopup(\'' . htmlspecialchars($account['account_id'], ENT_QUOTES) . '\')">
                    Revoke
                </button>';
            }

            echo "
            <button class='modal-open button' aria-haspopup='true' 
            data-account-id='" . htmlspecialchars($account['account_id'], ENT_QUOTES) . "' 
            data-resident-id='" . htmlspecialchars($account['resident_id'], ENT_QUOTES) . "' 
            data-username='" . htmlspecialchars($account['username'], ENT_QUOTES) . "' 
            data-first-name='" . htmlspecialchars($account['first_name'], ENT_QUOTES) . "' 
            data-middle-name='" . htmlspecialchars($account['middle_name'], ENT_QUOTES) . "' 
            data-last-name='" . htmlspecialchars($account['last_name'], ENT_QUOTES) . "' 
            data-email='" . htmlspecialchars($account['email'], ENT_QUOTES) . "' 
            data-mobile-no='" . htmlspecialchars($account['cellphone_number'] ?? '', ENT_QUOTES) . "' 
            data-privilege='" . htmlspecialchars($account['role_id'], ENT_QUOTES) . "' 
            data-status='" . htmlspecialchars($account['status'], ENT_QUOTES) . "' 
            data-profile-image='" . (!empty($account['profile_image']) ? "uploads/" . htmlspecialchars($account['profile_image'], ENT_QUOTES) : "default-profile.png") . "' 
            data-id-image='" . (!empty($account['id_image']) ? "uploads/" . htmlspecialchars($account['id_image'], ENT_QUOTES) : "default-id.png") . "' 
            onclick='populateModal(this)'>
            
            <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-pen' viewBox='0 0 16 16'>
                <path d='m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001m-.644.766a.5.5 0 0 0-.707 0L1.95 11.756l-.764 3.057 3.057-.764L14.44 3.854a.5.5 0 0 0 0-.708z'/>
            </svg>
            </button>
            </td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='11'>No accounts found.</td></tr>";
    }
}
?>
