<?php   
session_start();

require_once '../../config/sessionCheck.php';
require_once '../../config/rolePermissions.php';
require_once '../../model/dbconnection.php';
require_once '../../config/permissionUtils.php';
require '../../model/admin/adminDashboardModel.php';


// Initialize database connection
$db = new dbcon();
$conn = $db->getConnection();

// Check if the user has a valid role
checkAccess(['Admin', 'Chairman', 'Secretary', 'Official'], $conn);
    $roleId = $_SESSION['role_id'] ?? null;
if ($roleId) {
    reloadPermissions($conn, $roleId); // Reload permissions based on the user's role
}

$privilege = $_SESSION['permissions'] ?? [];  // Access the permissions stored in the session


$totalResidents = 10;
$totalNewResidents = 10;
$totalPWD = 5;
// $totalSeniorCitizen = 5;

$totalPresentIncidents = 10;
$totalPresentNewResidents = 10;
$totalPresentPWD = 5;
$totalReportsSeniorCitizen = 5;

$totalPresentRequests = 10;
$totalApprovedRequests = 10;
$totalPendingRequests = 5;
$totalProcessedRequests = 5;


// =====----- RESIDENTS DATA -----=====
//  GET Number of Total Residents from Accounts Table
$sqlTotalResidents = "SELECT COUNT(*) AS totalResidents FROM residents WHERE status = 'active' 
    OR status = 'bedridden'";
$resultTotalResidents = $conn->query($sqlTotalResidents); // Assign the query result to $result

if ($resultTotalResidents && $row = $resultTotalResidents->fetch_assoc()) {
    $totalResidents = $row['totalResidents']; // Fetch the correct value
} else {
    $totalResidents = 0; // Default value if query fails
}



//  GET Number of New Residents from Accounts Table
$sqlNewResidents = "SELECT COUNT(*) AS newResidents FROM residents WHERE DATE(date_and_time_created_registration) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) AND status = 'active' 
    OR status = 'bedridden'";
$resultNewResidents = $conn->query($sqlNewResidents); // Assign the query result to $result

if ($resultNewResidents && $row = $resultNewResidents->fetch_assoc()) {
    $newResidents = $row['newResidents']; // Fetch the correct value
} else {
    $newResidents = 0; // Default value if query fails
}
     

// GET Number of Senior Citizens from Acount Table
$sqlseniorCitizens = "SELECT COUNT(*) AS seniorCitizens FROM residents WHERE date_of_birth <= DATE_SUB(CURDATE(), INTERVAL 60 YEAR) AND status = 'active' 
    OR status = 'bedridden'";

$resultseniorCitizens = $conn->query($sqlseniorCitizens);

if ($resultseniorCitizens && $row = $resultseniorCitizens->fetch_assoc()) {
    $seniorCitizens = $row['seniorCitizens'];
} else {
    $seniorCitizens = 0;
}

// GET Number of Total Families from Families table
$sqlTotalFamilies = "SELECT COUNT(*) AS totalFamilies FROM families";
$resultTotalFamilies = $conn->query($sqlTotalFamilies); // Assign the query result to $result

if ($resultTotalFamilies && $row = $resultTotalFamilies->fetch_assoc()) {
    $totalFamilies = $row['totalFamilies']; // Fetch the correct value
} else {
    $totalFamilies = 0; // Default value if query fails
}



// =====----- REPORTS DATA -----=====

// GET Numbers of Blotters made TODAY
$blottersTodayQuery = "SELECT COUNT(*) AS blottersToday FROM blotter WHERE DATE(date_and_time_created) = CURDATE()";

$resultBlottersToday = $conn->query($blottersTodayQuery);

if ($resultBlottersToday) {
    $row = $resultBlottersToday->fetch_assoc();
    $blottersToday = $row ? $row['blottersToday'] : 0;
} else {
    $blottersToday = 0; // Default value if the query fails
    // You might want to log the error for debugging:
    error_log("Blotters today query failed: " . $conn->error);
}


// GET Numbers of Blotters made TODAY
$incidentsTodayQuery = "SELECT COUNT(*) AS incidentsToday FROM incident WHERE DATE(date_and_time_created) = CURDATE()";

$resultIncidentsToday = $conn->query($incidentsTodayQuery);

if ($resultIncidentsToday) {
    $row = $resultIncidentsToday->fetch_assoc();
    $incidentsToday = $row ? $row['incidentsToday'] : 0;
} else {
    $incidentsToday = 0; // Default value if the query fails
    // You might want to log the error for debugging:
    error_log("Blotters today query failed: " . $conn->error);
}


// GET Numbers of COMPLAINTS made TODAY
$complaintsTodayQuery = "SELECT COUNT(*) AS complaintsToday FROM complaint WHERE DATE(date_and_time_created) = CURDATE()";

$resultComplaintsToday = $conn->query($complaintsTodayQuery);

if ($resultComplaintsToday) {
    $row = $resultComplaintsToday->fetch_assoc();
    $complaintsToday = $row ? $row['complaintsToday'] : 0;
} else {
    $complaintsToday = 0; // Default value if the query fails
    // You might want to log the error for debugging:
    error_log("Blotters today query failed: " . $conn->error);
}


// Execute each query and get counts
$pendingComplaintsToday = 0;
$pendingBlottersToday = 0;
$pendingIncidentsToday = 0;

// Get pending complaints count
$result = $conn->query("SELECT COUNT(*) AS count FROM complaint WHERE DATE(date_and_time_created) = CURDATE() AND status = 'pending'");
if ($result) {
    $row = $result->fetch_assoc();
    $pendingComplaintsToday = $row ? $row['count'] : 0;
} else {
    error_log("Complaints today query failed: " . $conn->error);
}

// Get pending blotters count
$result = $conn->query("SELECT COUNT(*) AS count FROM blotter WHERE DATE(date_and_time_created) = CURDATE() AND status = 'pending'");
if ($result) {
    $row = $result->fetch_assoc();
    $pendingBlottersToday = $row ? $row['count'] : 0;
} else {
    error_log("Blotters today query failed: " . $conn->error);
}

// Get pending incidents count
$result = $conn->query("SELECT COUNT(*) AS count FROM incident WHERE DATE(date_and_time_created) = CURDATE() AND status = 'pending'");
if ($result) {
    $row = $result->fetch_assoc();
    $pendingIncidentsToday = $row ? $row['count'] : 0;
} else {
    error_log("Incidents today query failed: " . $conn->error);
}

// Calculate total
$totalPendingReportsToday = $pendingComplaintsToday + $pendingBlottersToday + $pendingIncidentsToday;






// ----- DOCUMENT REQUESTS -----
// GET Number of Permit made TODAY
$permitsTodayQuery = "SELECT COUNT(*) AS permitsToday 
    FROM certificate_requests 
    WHERE DATE(date_requested) = CURDATE()
    AND template_id IN (2, 8, 9, 10, 11, 12, 13, 14)
";

$resultPermitsToday = $conn->query($permitsTodayQuery);

if ($resultPermitsToday) {
    $row = $resultPermitsToday->fetch_assoc();
    $permitsToday = $row ? (int)$row['permitsToday'] : 0;
} else {
    $permitsToday = 0; // Default value if the query fails

    // Error logging (uncomment when needed)
    // error_log("Permits today query failed: " . $conn->error);
}


// GET Number of Clearance made TODAY
$clearanceTodayQuery = "SELECT COUNT(*) AS clearanceToday 
    FROM certificate_requests 
    WHERE DATE(date_requested) = CURDATE()
    AND template_id IN (3,4)
";

$resultClearanceToday = $conn->query($clearanceTodayQuery);

if ($resultClearanceToday) {
    $row = $resultClearanceToday->fetch_assoc();
    $clearanceToday = $row ? (int)$row['clearanceToday'] : 0;
} else {
    $clearanceToday = 0; // Default value if the query fails

    // Error logging (uncomment when needed)
    // error_log("Permits today query failed: " . $conn->error);
}


// GET Number of Certificate made TODAY
$certificateTodayQuery = "SELECT COUNT(*) AS certificateToday 
    FROM certificate_requests 
    WHERE DATE(date_requested) = CURDATE()
    AND template_id IN (1,5,6,7)
";

$resultCertificateToday = $conn->query($certificateTodayQuery);

if ($resultCertificateToday) {
    $row = $resultCertificateToday->fetch_assoc();
    $certificateToday = $row ? (int)$row['certificateToday'] : 0;
} else {
    $certificateToday = 0; // Default value if the query fails

    // Error logging (uncomment when needed)
    // error_log("Permits today query failed: " . $conn->error);
}

$pendingPermitToday = 0;
$pendingClearanceToday = 0;
$pendingCertificateToday = 0;

// Get pending complaints count
$result = $conn->query("SELECT COUNT(*) AS count FROM certificate_requests WHERE DATE(date_requested) = CURDATE() AND template_id IN (2, 8, 9, 10, 11, 12, 13, 14) AND status = 'pending'");
if ($result) {
    $row = $result->fetch_assoc();
    $pendingPermitToday = $row ? $row['count'] : 0;
} else {
    error_log("Requests today query failed: " . $conn->error);
}

// Get pending blotters count
$result = $conn->query("SELECT COUNT(*) AS count FROM certificate_requests WHERE DATE(date_requested) = CURDATE() AND template_id IN (3, 4) AND status = 'pending'");
if ($result) {
    $row = $result->fetch_assoc();
    $pendingClearanceToday = $row ? $row['count'] : 0;
} else {
    error_log("Clearance today query failed: " . $conn->error);
}

// Get pending incidents count
$result = $conn->query("SELECT COUNT(*) AS count FROM certificate_requests WHERE DATE(date_requested) = CURDATE() AND template_id IN (1, 5,6,7) AND status = 'pending'");
if ($result) {
    $row = $result->fetch_assoc();
    $pendingCertificateToday = $row ? $row['count'] : 0;
} else {
    error_log("Certificate today query failed: " . $conn->error);
}



// GET Number of Requests made TODAY
$requestsMadeToday = $pendingPermitToday + $pendingClearanceToday + $pendingCertificateToday;



// =====----- ACCOUNTS DATA -----===== 
$sqlTotalAccounts = "SELECT COUNT(*) AS totalAccounts FROM accounts WHERE status = 'active' 
    OR status = 'bedridden'";
$resultTotalAccounts = $conn->query($sqlTotalAccounts); // Assign the query result to $result

if ($resultTotalAccounts && $row = $resultTotalAccounts->fetch_assoc()) {
    $totalAccounts = $row['totalAccounts']; // Fetch the correct value
} else {
    $totalAccounts = 0; // Default value if query fails
}
    









// =====----- ACCOUNTS DATA -----===== 


// ----- NEW ACCOUNTS IN THE LAST 7 DAYS -----
$sqlNewAccounts = "SELECT COUNT(*) AS newAccounts FROM accounts WHERE DATE(date_and_time_created) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) AND status = 'active' 
    OR status = 'bedridden'";
$resultNewAccounts = $conn->query($sqlNewAccounts);

if ($resultNewAccounts && $row = $resultNewAccounts->fetch_assoc()) {
    $newAccounts = $row['newAccounts'];
} else {
    $newAccounts = 0;
}



// ----- PENDING ACCOUNTS IN THE LAST 7 DAYS -----
$sqlNewPendingAccounts = "SELECT COUNT(*) AS newPendingAccounts FROM accounts WHERE status = 'pending' AND DATE(date_and_time_created) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";

$resultNewPendingAccounts = $conn->query($sqlNewPendingAccounts);

if ($resultNewPendingAccounts && $row = $resultNewPendingAccounts->fetch_assoc()) {
    $newPendingAccounts = $row['newPendingAccounts'];
} else {
    $newPendingAccounts = 0;
}


// ----- TOTAL PENDING ACCOUNTS -----
$sqlTotalPendingAccounts = "SELECT COUNT(*) AS totalPendingAccounts FROM accounts WHERE status = 'pending'";

$resultTotalPendingAccounts = $conn->query($sqlTotalPendingAccounts);

if ($resultTotalPendingAccounts && $row = $resultTotalPendingAccounts->fetch_assoc()) {
    $totalPendingAccounts = $row['totalPendingAccounts'];
} else {
    $totalPendingAccounts = 0;
}

function getRecentAnnouncements(mysqli $conn): array {
    // Set explicit timezone
    date_default_timezone_set('Asia/Manila');
    
    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

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

include '../../public/views/admin/adminDashboard.php';

?>
