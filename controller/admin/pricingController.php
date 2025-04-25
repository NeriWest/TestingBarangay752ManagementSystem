<?php
session_start();
include '../../model/dbconnection.php';

header('Content-Type: application/json');

$db = new dbcon();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $template_name = trim($_POST['template_name']);
    $template_type = trim($_POST['template_type']);
    $price_enabled = isset($_POST['price_enabled']) ? intval($_POST['price_enabled']) : 0;
    $price = isset($_POST['price']) ? floatval($_POST['price']) : 0.00;

    // Validate price input (avoid negative values)
    if ($price < 0) {
        echo json_encode(["success" => false, "error" => "Invalid price value."]);
        exit;
    }

    $stmt_check = $conn->prepare("SELECT price FROM templates WHERE id = ?");
    $stmt_check->bind_param("i", $template_id);
    $stmt_check->execute();
    $stmt_check->bind_result($current_price);
    $stmt_check->fetch();
    $stmt_check->close();

    $price_changed = ($current_price != $price);

    // Find the template ID
    $sql_check = "SELECT id FROM templates WHERE doc_name = ? AND template_type = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("ss", $template_name, $template_type);
    $stmt_check->execute();
    $result = $stmt_check->get_result();
    $row = $result->fetch_assoc();

    if (!$row) {
        echo json_encode(["success" => false, "error" => "Template not found"]);
        exit;
    }

    $template_id = $row['id'];

    // Update the template price and status
    $sql = "UPDATE templates SET price = ?, price_enabled = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("dii", $price, $price_enabled, $template_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(["success" => true, "message" => "Price updated successfully.", "price_changed" => $price_changed]);
    } else {
        echo json_encode(["success" => false, "error" => "No changes made."]);
    }



    $stmt->close();
} else {
    echo json_encode(["success" => false, "error" => "Invalid request method"]);
}
?>
