<?php
function reloadPermissions($conn, $roleId) {
    // Query to get the permission names associated with the user's role from RolePermissions
    $stmt = $conn->prepare("
        SELECT p.permission_name
        FROM Permissions p
        INNER JOIN RolePermissions rp ON p.permission_id = rp.permission_id
        WHERE rp.role_id = ? AND rp.is_enabled = 1
    ");
    $stmt->bind_param("i", $roleId);
    $stmt->execute();
    $result = $stmt->get_result();

    // If permissions exist, store them in the session
    if ($result->num_rows > 0) {
        $permissions = [];
        while ($row = $result->fetch_assoc()) {
            $permissions[$row['permission_name']] = true;
        }

        // Store the permissions in the session
        $_SESSION['permissions'] = $permissions;
    } else {
        // If no permissions are found, initialize an empty permissions array
        $_SESSION['permissions'] = [];
    }

    $stmt->close();
}

?>
