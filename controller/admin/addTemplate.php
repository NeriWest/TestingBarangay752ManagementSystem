<?php
require_once __DIR__ . '/../../model/dbconnection.php';
require_once __DIR__ . '/../../model/admin/uploadModel.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

$response = ["success" => false, "message" => "Invalid request"];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['templateType'], $_POST['templateName'])) {
            $templateType = trim($_POST['templateType']);
            $templateName = trim($_POST['templateName']);

            // Insert a blank template with empty fields for now
            $insertResult = insertNewTemplate($templateName, $templateType, '', '', 0.00, 0);

            if (!$insertResult['success']) {
                throw new Exception("Failed to insert new template");
            }

            $templateId = $insertResult['template_id'];

            // Fetch inserted template for confirmation
            $db = new dbcon();
            $conn = $db->getConnection();
            $stmt = $conn->prepare("SELECT * FROM templates WHERE id = ?");
            $stmt->bind_param("i", $templateId);
            $stmt->execute();
            $result = $stmt->get_result();
            $newTemplate = $result->fetch_assoc();

            $response = [
                "success" => true,
                "template" => $newTemplate
            ];
        } else {
            throw new Exception("Required fields missing");
        }
    }
} catch (Exception $e) {
    error_log("Add Template Error: " . $e->getMessage());
    $response["message"] = $e->getMessage();
}

echo json_encode($response);
?>
