<?php
require_once '../../config/sessionCheck.php';
require_once '../../config/rolePermissions.php';
require_once '../../model/dbconnection.php';
require_once '../../config/permissionUtils.php';
require_once '../../model/resident/residentViewProfileModel.php'; // Add this line

// Get profile data
function getRecentAnnouncements(mysqli $conn): array {
    // Set explicit timezone
    date_default_timezone_set('Asia/Manila');
    


    // Base query
    $sql = "SELECT 
            a.announcement_id,
            a.announcement_type, 
            a.recipient_group, 
            a.subject, 
            a.date_created, 
            a.schedule,
            ac.role_id,
            CONCAT(r.first_name, ' ', r.last_name) AS author_full_name, 
            a.message_body
            FROM announcements a 
            JOIN residents r ON a.account_id = r.account_id
            JOIN accounts ac ON a.account_id = ac.account_id
            WHERE a.schedule <= NOW()";

    // Apply recipient group filtering based on role
    if (isset($_SESSION['role_id'])) {
        $role_id = $_SESSION['role_id'];
        
        switch ($role_id) {
            case 1: // Chairman
                $sql .= " AND (a.recipient_group = 'Everyone' OR a.recipient_group = 'Chairman')";
                break;
            case 2: // Secretary
                $sql .= " AND (a.recipient_group = 'Everyone' OR a.recipient_group = 'Secretary')";
                break;
            case 3: // Official
                $sql .= " AND (a.recipient_group = 'Everyone' OR a.recipient_group = 'Officials')";
                break;
            case 5: // Admin
                $sql .= " AND (a.recipient_group = 'Everyone' OR a.recipient_group = 'Admin' OR a.recipient_group = 'Officials' OR a.recipient_group = 'Secretary')";
                break;
            default: // Other roles
                $sql .= " AND a.recipient_group = 'Everyone'";
        }
    } else {
        // Not logged in - show only Everyone announcements
        $sql .= " AND a.recipient_group = 'Everyone'";
    }

    $sql .= " ORDER BY a.schedule DESC LIMIT 5";

    try {
        $result = $conn->query($sql);
        
        if (!$result) {
            throw new Exception("Query failed: " . $conn->error);
        }
        
        $announcements = $result->fetch_all(MYSQLI_ASSOC);
        
        // Debug output
        error_log("Found " . count($announcements) . " announcements");
        foreach ($announcements as $a) {
            error_log("ID: " . $a['announcement_id'] . 
                     " | Subject: " . $a['subject'] . 
                     " | Schedule: " . $a['schedule'] .
                     " | Recipient: " . $a['recipient_group']);
        }
        
        // Map role_id to role names
        $roleNames = [
            1 => 'Chairman',
            2 => 'Secretary',
            3 => 'Official',
            4 => 'Resident',
            5 => 'Admin'
        ];
        
        foreach ($announcements as &$announcement) {
            $announcement['author_role'] = $roleNames[$announcement['role_id']] ?? 'Unknown';
        }
        
        return $announcements;
        
    } catch (Exception $e) {
        error_log("Announcements query failed: " . $e->getMessage());
        return [];
    }
}

$typeColors = [
    'event' => '#3b82f6',  // Blue
    'urgent' => '#ef4444', // Red
    'notice' => '#10b981'  // Green
];

$announcements = getRecentAnnouncements($conn);

include '../../public/views/resident/residentDashboard.php';
?>