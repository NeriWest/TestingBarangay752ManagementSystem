<?php
function checkAccess(array $allowedRoles, $conn)
{
    if (!isset($_SESSION['role_id'])) {
        header("Location: ../../public/views/login.php");
        exit();
    }

    $role_id = $_SESSION['role_id'];

    // If role_name is not in session, fetch it and cache it
    if (!isset($_SESSION['role_name'])) {
        $stmt = $conn->prepare("SELECT role_name FROM Roles WHERE role_id = ?");
        $stmt->bind_param("i", $role_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $_SESSION['role_name'] = $row['role_name'] ?? 'Unknown';
        $stmt->close();
    }

    if (!in_array($_SESSION['role_name'], $allowedRoles)) {
        header("Location: ../../public/views/login.php");
        exit();
    }
}
?>
