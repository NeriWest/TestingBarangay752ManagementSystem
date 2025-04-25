<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'model/dbconnection.php';

$db = new dbcon();
$conn = $db->getConnection();

if (!$conn) {
    die(json_encode(['error' => 'Connection failed']));
}

header('Content-Type: application/json');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize variables
$isSenior = false;
$recipientGroups = ['Everyone']; // Base recipient groups for everyone

// Check if user is logged in
if (isset($_SESSION['account_id'])) {
    // Calculate user's age
    $ageQuery = "SELECT TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) AS age 
                 FROM residents 
                 WHERE account_id = ?";
    $stmt = $conn->prepare($ageQuery);
    $stmt->bind_param("i", $_SESSION['account_id']);
    $stmt->execute();
    $ageResult = $stmt->get_result();
    
    if ($ageResult && $ageRow = $ageResult->fetch_assoc()) {
        $isSenior = ($ageRow['age'] >= 60);
    }
    
    // Add role-specific recipient groups
    if (isset($_SESSION['role_id'])) {
        switch ($_SESSION['role_id']) {
            case 1: // Chairman
                array_push($recipientGroups, 'Chairman', 'Admin', 'Officials', 'Secretary', 'Seniors');
                break;
            case 2: // Secretary
                array_push($recipientGroups, 'Chairman', 'Admin', 'Officials', 'Secretary', 'Seniors');
                break;
            case 3: // Official
                array_push($recipientGroups, 'Chairman', 'Admin', 'Officials', 'Secretary', 'Seniors');
                break;
            case 5: // Admin
                array_push($recipientGroups, 'Chairman', 'Admin', 'Officials', 'Secretary', 'Seniors');
                break;
        }
    }
    
    // Add Seniors if qualified
    if ($isSenior) {
        $recipientGroups[] = 'Seniors';
    }
}

// Prepare the SQL query
$placeholders = implode(',', array_fill(0, count($recipientGroups), '?'));
$sql = "SELECT announcement_id, subject, message_body, schedule, 
               recipient_group, announcement_type
        FROM announcements
        WHERE schedule <= NOW()
        AND recipient_group IN ($placeholders)
        ORDER BY schedule DESC
        LIMIT 6";

// Prepare and execute the statement
$stmt = $conn->prepare($sql);
$types = str_repeat('s', count($recipientGroups));
$stmt->bind_param($types, ...$recipientGroups);
$stmt->execute();
$result = $stmt->get_result();

$announcements = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $announcements[] = [
            'id' => $row['announcement_id'],
            'title' => $row['subject'],
            'description' => $row['message_body'],
            'date' => date("F j, Y", strtotime($row['schedule'])),
            'type' => strtolower($row['announcement_type']),
            'image' => 'img/default.jpg',
            'for' => $row['recipient_group']
        ];
    }
}

echo json_encode($announcements);
exit;
?>