<?php

require_once __DIR__ . '/../../model/dbconnection.php';

$db = new dbcon();
$conn = $db->getConnection();
$templatesByType = [];

$sql = "SELECT doc_name, id, template_image, template_document, template_type, price, price_enabled FROM templates ORDER BY template_type, id";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $imagePath = !empty($row['template_image']) ? "/Barangay752managementsystem/" . $row['template_image'] : "/Barangay752managementsystem/img/uploads/image.png";
    $documentPath = !empty($row['template_document']) ? "/Barangay752managementsystem/" . $row['template_document'] : null;
    
    $templatesByType[$row['template_type']][$row['id']] = [
        'doc_name' => $row['doc_name'],
        'image' => $imagePath,
        'document' => $documentPath,
        'price' => $row['price'],
        'price_enabled' => $row['price_enabled']
    ];
}



$sql = "SELECT id, name, qr_photo, status, account_name FROM payment_types ORDER BY id";
$result = $conn->query($sql);
$paymentData = [];

while ($row = $result->fetch_assoc()) {
    $paymentData[$row['id']] = [
        'name' => $row['name'],
        'qr_photo' => !empty($row['qr_photo']) ? "/Barangay752ManagementSystem/" . $row['qr_photo'] . "?t=" . time() : "/Barangay752ManagementSystem/img/uploads/image.png",
        'status' => $row['status'],
        'account_name' => $row['account_name'] ?? ''
    ];
}

$roles = [];
$permissions = [];
$permissions_data = [];

// Get roles from DB
$sql = "SELECT role_name FROM Roles";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $roles[] = $row['role_name'];
}

// Get permissions from DB (id and label)
$sql = "SELECT permission_name FROM Permissions";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    // You can prettify label if needed
    $label = ucwords(str_replace('_', ' ', $row['permission_name']));
    $permissions[$row['permission_name']] = $label;
}

// Now get is_enabled matrix per role
$sql = "SELECT r.role_name, p.permission_name, rp.is_enabled
        FROM Roles r
        JOIN RolePermissions rp ON r.role_id = rp.role_id
        JOIN Permissions p ON rp.permission_id = p.permission_id";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $permissions_data[$row['role_name']][$row['permission_name']] = (int)$row['is_enabled'];
}

?>
