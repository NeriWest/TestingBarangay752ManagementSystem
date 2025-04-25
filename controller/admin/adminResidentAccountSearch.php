<?php

require_once '../../model/admin/adminResidentAccountsModel.php'; // Include your model to fetch resident data

// SEARCH QUERY FOR RESIDENTS
$adminResidentAccountsModel = new adminResidentAccountsModel();

if (isset($_POST['query'])) {
    $query = $_POST['query'];
    $accounts = $adminResidentAccountsModel->searchAccounts($conn, $query);

    if (!empty($accounts)) {
        foreach ($accounts as $account) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($account['account_id']) . "</td>"; // Account ID
            echo "<td>" . htmlspecialchars($account['username']) . "</td>"; // Username

            // Full Name (Concatenated)
            $fullName = htmlspecialchars($account['first_name'] . " " . ($account['middle_name'] ? $account['middle_name'] . " " : "") . $account['last_name'] . ($account['suffix'] ? ", " . $account['suffix'] : ""));
            echo "<td>" . $fullName . "</td>";
            echo "<td>" . htmlspecialchars($account['cellphone_number']) . "</td>"; // Mobile No.
            echo "<td>" . htmlspecialchars($account['date_of_birth']) . "</td>"; // Date of Birth
            echo "<td>" . (!empty($account['email']) ? htmlspecialchars($account['email']) : 'N/A') . "</td>"; // Email

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

            // ID Image (Click to open the full image in a new tab)
            echo "<td>";
            if (!empty($account['id_image_name'])) {
                echo "<a href='../../img/id_images/" . htmlspecialchars($account['id_image_name'], ENT_QUOTES) . "' target='_blank'>";
                echo "<img src='../../img/id_images/" . htmlspecialchars($account['id_image_name'], ENT_QUOTES) . "' alt='ID Image' width='50' height='50' style='margin-right:5px;'>";
                echo "</a>";
            } else {
                echo "No image";
            }
            echo "</td>";

            echo "<td>" . htmlspecialchars($account['relationship']) . "</td>"; // Relationship

            // Assisted ID Image (Click to open the full image in a new tab)
            echo "<td>";
            if (!empty($account['guided_by_id_name'])) {
                echo "<a href='../../img/guided_by_id_images/" . htmlspecialchars($account['guided_by_id_name'], ENT_QUOTES) . "' target='_blank'>";
                echo "<img src='../../img/guided_by_id_images/" . htmlspecialchars($account['guided_by_id_name'], ENT_QUOTES) . "' alt='Guided By Image' width='50' height='50' style='margin-right:5px;'>";
                echo "</a>";
            } else {
                echo "No image";
            }
            echo "</td>";

            echo "<td>" . (!empty($account['assisted_by']) ? htmlspecialchars($account['assisted_by']) : 'N/A') . "</td>"; // Assisted Name

            // Profile Image (Click to open the full image in a new tab)
            echo "<td>";
            if (!empty($account['profile_image_name'])) {
                echo "<a href='../../img/profile_images/" . htmlspecialchars($account['profile_image_name'], ENT_QUOTES) . "' target='_blank'>";
                echo "<img src='../../img/profile_images/" . htmlspecialchars($account['profile_image_name'], ENT_QUOTES) . "' alt='Profile Image' width='50' height='50' style='margin-right:5px;'>";
                echo "</a>";
            } else {
                echo "No image";
            }
            echo "</td>";

            // Actions Button
            echo '<td>';
            echo '<div style="display: flex; gap: 5px;">';

            if ($account['status'] === 'pending') {
                echo '
                    <button onclick="showApprovePopup(\'' . htmlspecialchars($account['account_id'], ENT_QUOTES) . '\')" class="buttons" style="background-color: #28a745; color: white; border: none; padding: 8px; border-radius: 5px; cursor: pointer; display: flex; align-items: center; justify-content: center; width: 32px; height: 32px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-check-circle" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                            <path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z"/>
                        </svg>
                    </button>
                    <button onclick="showDisapprovePopup(\'' . htmlspecialchars($account['account_id'], ENT_QUOTES) . '\')" class="buttons" style="background-color: #dc3545; color: white; border: none; padding: 8px; border-radius: 5px; cursor: pointer; display: flex; align-items: center; justify-content: center; width: 32px; height: 32px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-x-circle" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                        </svg>
                    </button>';
            }
            echo '
                <button class="modal-open button" 
                    aria-haspopup="true"
                    data-account-id="' . htmlspecialchars($account['account_id'], ENT_QUOTES) . '"
                    data-resident-id="' . htmlspecialchars($account['resident_id'], ENT_QUOTES) . '"
                    data-username="' . htmlspecialchars($account['username'], ENT_QUOTES) . '"
                    data-first-name="' . htmlspecialchars($account['first_name'], ENT_QUOTES) . '"
                    data-middle-name="' . htmlspecialchars($account['middle_name'], ENT_QUOTES) . '"
                    data-last-name="' . htmlspecialchars($account['last_name'], ENT_QUOTES) . '"
                    data-email="' . htmlspecialchars($account['email'], ENT_QUOTES) . '"
                    data-mobile-no="' . htmlspecialchars($account['cellphone_number'] ?? '', ENT_QUOTES) . '"
                    data-privilege="' . htmlspecialchars($account['role_id'], ENT_QUOTES) . '"
                    data-status="' . htmlspecialchars($account['status'], ENT_QUOTES) . '"
                    data-profile-image="' . (!empty($account['profile_image']) ? 'uploads/' . htmlspecialchars($account['profile_image'], ENT_QUOTES) : 'default-profile.png') . '"
                    data-id-image="' . (!empty($account['id_image']) ? 'uploads/' . htmlspecialchars($account['id_image'], ENT_QUOTES) : 'default-id.png') . '"
                    onclick="populateModal(this)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-pen" viewBox="0 0 16 16">
                        <path d="m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001m-.644.766a.5.5 0 0 0-.707 0L1.95 11.756l-.764 3.057 3.057-.764L14.44 3.854a.5.5 0 0 0 0-.708z"/>
                    </svg>
                </button>
            ';
            echo '</div>';
            echo '</td>';
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='13'>No accounts found.</td></tr>";
    }
}
?>

