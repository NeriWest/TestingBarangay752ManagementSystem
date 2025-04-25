<?php
require_once '../model/dbconnection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['exists' => false, 'message' => 'Invalid request method']);
    exit;
}

$field = $_POST['field'] ?? '';
$value = $_POST['value'] ?? '';

if (empty($field) || empty($value)) {
    echo json_encode(['exists' => false, 'message' => 'Invalid input']);
    exit;
}

try {
    $db = new dbcon();
    $conn = $db->getConnection();

    $exists = false;
    $message = '';

    if ($field === 'email') {
        $email_table = 'accounts';
        $email_column = 'email';
        $email_stmt = $conn->prepare("SELECT COUNT(*) as count FROM $email_table WHERE $email_column = ?");
        if (!$email_stmt) {
            throw new Exception('Email prepare failed: ' . $conn->error);
        }
        $email_stmt->bind_param('s', $value);
        if (!$email_stmt->execute()) {
            throw new Exception('Email execute failed: ' . $email_stmt->error);
        }
        $email_result = $email_stmt->get_result();
        $email_row = $email_result->fetch_assoc();
        $email_count = $email_row['count'];
        error_log("Email check: value=$value, count=$email_count");
        $exists = $email_count > 0;
        $message = $exists ? 'Email already exists, try another one.' : '';
        $email_stmt->close();
    } elseif ($field === 'cellphone_number') {
        $phone_table = 'residents';
        $phone_column = 'cellphone_number';
        $phone_stmt = $conn->prepare("SELECT COUNT(*) as count FROM $phone_table WHERE $phone_column = ?");
        if (!$phone_stmt) {
            throw new Exception('Phone prepare failed: ' . $conn->error);
        }
        $phone_stmt->bind_param('s', $value);
        if (!$phone_stmt->execute()) {
            throw new Exception('Phone execute failed: ' . $phone_stmt->error);
        }
        $phone_result = $phone_stmt->get_result();
        $phone_row = $phone_result->fetch_assoc();
        $phone_count = $phone_row['count'];
        error_log("Phone check: value=$value, count=$phone_count");
        $exists = $phone_count > 0;
        $message = $exists ? 'Cellphone number already exists, try another one.' : '';
        $phone_stmt->close();
    } else {
        echo json_encode(['exists' => false, 'message' => 'Invalid field']);
        $conn->close();
        exit;
    }

    $response = [
        'exists' => $exists,
        'message' => $message
    ];
    echo json_encode($response);

    $conn->close();
} catch (Exception $e) {
    error_log('Check duplicate error: ' . $e->getMessage());
    echo json_encode(['exists' => false, 'message' => 'Database error']);
    exit;
}
?>