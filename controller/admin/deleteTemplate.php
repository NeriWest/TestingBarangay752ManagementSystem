<?php

require_once __DIR__ . '/../../model/dbconnection.php';
require_once __DIR__ . '/../../model/admin/uploadModel.php';

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["templateId"])) {
    echo json_encode(["success" => false, "message" => "Template ID missing"]);
    exit;
}

$templateId = intval($data["templateId"]);

$db = new dbcon();
$conn = $db->getConnection();

$sql = "DELETE FROM templates WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $templateId);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to delete template."]);
}

$stmt->close();
$conn->close();
?>