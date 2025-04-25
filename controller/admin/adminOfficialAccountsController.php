<?php
require '../../model/admin/adminOfficialAccountsModel.php';
require '../../model/admin/adminResidentModel.php'; 
require_once '../../config/sessionCheck.php';
require_once '../../config/permissionUtils.php';

// Initialize database connection

$privilege = $_SESSION['permissions'] ?? []; // Include your model to fetch resident data
// Instantiate the models
$adminOfficialAccountsModel = new AdminOfficialAccountsModel();
$adminResidentModel = new AdminResidentModel();

// Get the residents data (for displaying the resident list on the same page)
$residents = $adminResidentModel->showNonOfficialResidents($conn);

// Define the number of records per page (you can modify this value as needed)
$recordsPerPage = 13;

// Get the current page from the GET request (default to page 1 if not set)
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

// Calculate the offset for the SQL query
$offset = ($page - 1) * $recordsPerPage;

// Get the accounts data (with pagination)
$accounts = $adminOfficialAccountsModel->showAccountsWithPagination($conn, $offset, $recordsPerPage);

// Get the total number of accounts (for pagination calculation)
$totalAccounts = $adminOfficialAccountsModel->getTotalAccounts($conn);

// Calculate total pages
$totalPages = ceil($totalAccounts / $recordsPerPage);

// Determine whether to show pagination
$showPagination = $totalPages > 1; // Show pagination only if there is more than one page

$roles = $adminOfficialAccountsModel->showRoles($conn); // Fetch roles from the database


// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and sanitize form data
    $residentId = $_POST['resident_id'];  // Selected resident ID from dropdown
    $username = trim(htmlspecialchars($_POST['username']));
    $email = trim(htmlspecialchars($_POST['email']));
    $password = trim($_POST['password']);
    $accountId = intval($_POST['account_id']); // Assuming account_id is an integer
    $confirmPassword = trim($_POST['confirm_password']);
    $privilege = trim(htmlspecialchars($_POST['privilege']));
    $status = trim(htmlspecialchars($_POST['status']));
    $officialData = [
        'resident_id' => $residentId,
        'specify_position' => trim(htmlspecialchars($_POST['specify_position'])),
        'term_start' => trim(htmlspecialchars($_POST['term_start'])),
        'term_end' => trim(htmlspecialchars($_POST['term_end'])),
        'status' => $status
    ];

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Insert the new official into the Officials table
        $query = "INSERT INTO Officials (resident_id, account_id, position, term_start, term_end, status) 
                  VALUES (?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($query);
        $stmt->bind_param(
            "iissss", 
            $officialData['resident_id'], 
            $officialData['account_id'], 
            $officialData['specify_position'], 
            $officialData['term_start'], 
            $officialData['term_end'], 
            $officialData['status']
        );

        if (!$stmt->execute()) {
            throw new Exception("Error inserting official: " . $stmt->error);
        }

        // Update the role_id in the accounts table using the resident_id
        $updateQuery = "UPDATE accounts a
                        JOIN Residents r ON a.account_id = r.account_id
                        SET a.role_id = ?
                        WHERE r.resident_id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param(
            "ii", 
            $officialData['specify_position'], // role_id corresponds to position
            $officialData['resident_id']
        );

        if (!$updateStmt->execute()) {
            throw new Exception("Error updating account role_id: " . $updateStmt->error);
        }

        // Commit the transaction
        $conn->commit();

        // Success message and redirect
        session_start();
        $_SESSION['message'] = "New official account has been successfully added.";
        header('Location: adminResidentAccountsController.php');
        exit();

    } catch (Exception $e) {
        // Rollback the transaction in case of an error
        $conn->rollback();
        echo $e->getMessage();
    }
} else {
    // Show the resident accounts page if no form submission
    require '../../public/views/admin/adminOfficialAccount.php';
}
?>
