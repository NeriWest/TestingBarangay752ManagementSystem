<?php
require_once __DIR__ . "/../model/dbconnection.php";
require_once __DIR__ . "/permissionUtils.php"; // add this to reload session

$db = new dbcon();
$conn = $db->getConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['role'] ?? null;
    $permission = $_POST['permission'] ?? null;
    $is_enabled = $_POST['is_enabled'] ?? null;

    if ($role && $permission !== null && $is_enabled !== null) {
        // Get role_id
        $stmt = $conn->prepare("SELECT role_id FROM Roles WHERE role_name = ?");
        $stmt->bind_param("s", $role);
        $stmt->execute();
        $stmt->bind_result($role_id);   
        $stmt->fetch();
        $stmt->close();

        if (!$role_id) {
            echo json_encode(["success" => false, "error" => "Role not found"]);
            exit;
        }

        // Get permission_id
        $stmt = $conn->prepare("SELECT permission_id FROM Permissions WHERE permission_name = ?");
        $stmt->bind_param("s", $permission);
        $stmt->execute();
        $stmt->bind_result($permission_id);
        $stmt->fetch();
        $stmt->close();

        if (!$permission_id) {
            echo json_encode(["success" => false, "error" => "Permission not found"]);
            exit;
        }

        // Update RolePermissions
        $stmt = $conn->prepare("UPDATE RolePermissions SET is_enabled = ? WHERE role_id = ? AND permission_id = ?");
        $stmt->bind_param("iii", $is_enabled, $role_id, $permission_id);

        if ($stmt->execute()) {
            // Reload session permissions only if current user's role was updated
            if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == $role_id) {
                reloadPermissions($conn, $_SESSION['role_id']);
            }

            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => $stmt->error]);
        }

        $stmt->close();
        exit;
    }
}
?>
