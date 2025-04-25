<?php
require_once 'dbconnection.php';

function updatePaymentQR($paymentId, $qr, $accountName) {
    $db = new dbcon();
    $conn = $db->getConnection();
    $sql = "UPDATE payment_types SET qr_photo = ?, account_name = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $qr, $accountName, $paymentId);

    if (!$stmt->execute()) {
        return ["success" => false, "message" => "Database error: " . $stmt->error];
    }

    $stmt->close();
    $conn->close();

    return ["success" => true];
}

function addPaymentType($name) {
    $db = new dbcon();
    $conn = $db->getConnection();
    $sql = "INSERT INTO payment_types (name) VALUES (?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $name);

    if (!$stmt->execute()) {
        return ["success" => false, "message" => "Database error: " . $stmt->error];
    }

    $stmt->close();
    $conn->close();

    return ["success" => true, "message" => "Payment type added successfully!"];
}

function deletePaymentType($paymentId) {
    $db = new dbcon();
    $conn = $db->getConnection();
    $sql = "DELETE FROM payment_types WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $paymentId);

    if (!$stmt->execute()) {
        return ["success" => false, "message" => "Database error: " . $stmt->error];
    }

    $stmt->close();
    $conn->close();

    return ["success" => true, "message" => "Payment type deleted successfully!"];
}
?>
