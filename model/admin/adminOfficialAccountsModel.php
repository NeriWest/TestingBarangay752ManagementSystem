<?php
require_once 'dbconnection.php';

$db = new dbcon();
$conn = $db->getConnection();

class AdminOfficialAccountsModel {
    private $showAccountQuery;


    // Function to get the accounts data
    function showAccounts($conn) {
        // Properly define the query
        $this->showAccountQuery = "
        SELECT 
            Accounts.*,
            Accounts.profile_image_name,
            Accounts.profile_image_blob,
            Accounts.id_image_name,
            Accounts.id_image_blob,
            Residents.resident_id,
            Residents.first_name,
            Residents.middle_name,
            Residents.last_name,
            Residents.suffix,
            Residents.date_of_birth,
            Residents.sex,
            Residents.civil_status,
            Residents.house_number,
            Residents.street,
            -- Remove or retain the following line based on your actual schema
            -- Residents.citizenship,  
            Residents.occupation,
            Residents.disability,
            Residents.cellphone_number,
            Residents.voter_status,
            Residents.status AS resident_status,
            Residents.date_and_time_created_registration,
            Residents.date_and_time_updated_registration
        FROM Accounts
        LEFT JOIN Residents ON Accounts.account_id = Residents.account_id
        ORDER BY Accounts.date_and_time_created DESC;
        ";
    
        // Execute the query
        $result = $conn->query($this->showAccountQuery);

        // Check if the result is valid
        if ($result === FALSE) {
            return []; // Return an empty array if query fails
        }

        // Fetch all accounts data
        $accounts = [];
        while ($account = $result->fetch_assoc()) {
            $accounts[] = $account; // Store each account in the array
        }

        return $accounts; // Return the list of accounts
    }
    
    // Function to get paginated accounts data
    public function showAccountsWithPagination($conn, $offset, $limit) {
        // Define the paginated query for accounts
        $this->showAccountQuery = "
          SELECT 
            Accounts.*,
            Accounts.profile_image_name,
            Accounts.profile_image_blob,
            Accounts.id_image_name,
            Accounts.id_image_blob,
            Residents.resident_id,
            Residents.first_name,
            Residents.middle_name,
            Residents.last_name,
            Residents.suffix,
            Residents.date_of_birth,
            Residents.sex,
            Residents.civil_status,
            Residents.house_number,
            Residents.street,
            Residents.occupation,
            Residents.disability,
            Residents.cellphone_number,
            Residents.voter_status,
            Residents.status AS resident_status,
            Residents.date_and_time_created_registration,
            Residents.date_and_time_updated_registration,
            Roles.role_name
        FROM Accounts
        LEFT JOIN Residents ON Accounts.account_id = Residents.account_id
        LEFT JOIN Roles ON Accounts.role_id = Roles.role_id
        WHERE Accounts.role_id IN (1, 2, 3)
        ORDER BY Accounts.date_and_time_updated DESC
        LIMIT ?, ?;";

        // Prepare and execute the query
        $stmt = $conn->prepare($this->showAccountQuery);
        $stmt->bind_param("ii", $offset, $limit); // Bind offset and limit
        $stmt->execute();
        $result = $stmt->get_result();

        // Fetch all accounts data
        $accounts = [];
        while ($account = $result->fetch_assoc()) {
            $accounts[] = $account; // Store each account in the array
        }

        return $accounts; // Return the list of accounts
    }

    // Function to get the total number of accounts (for pagination calculation)
    public function getTotalAccounts($conn) {
        // Count the total number of accounts with role_id in (1, 2, 3) and active or inactive status
        $query = "SELECT COUNT(*) AS total FROM Accounts WHERE Accounts.role_id IN (1, 2, 3) AND Accounts.status IN ('active', 'inactive')";
        $result = $conn->query($query);
        $row = $result->fetch_assoc();

        return $row['total']; // Return the total number of accounts
    }

    public function updateAccount($conn, $accountData) {
        // Define the update query for the accounts table
        $query = "UPDATE accounts SET
                    username = ?,
                    email = ?,
                    status = ?
                WHERE account_id = ?";

        // Prepare the statement
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare update statement: " . $conn->error);
        }

        $stmt->bind_param(
            "sssi",
            $accountData['username'],
            $accountData['email'],
            $accountData['status'], // Use the variable here
            $accountData['account_id']
        );

        // Execute the update
        if (!$stmt->execute()) {
            throw new Exception("Failed to update account: " . $stmt->error);
        }

        return true;
    }

    // Function to search accounts based on role_name, username, status, email, resident names, cellphone number, or date of birth
    public function searchAccounts($conn, $query) {
        $this->showAccountQuery = "
        SELECT 
            Accounts.*,
            Accounts.profile_image_name,
            Accounts.profile_image_blob,
            Accounts.id_image_name,
            Accounts.id_image_blob,
            Residents.resident_id,
            Residents.first_name,
            Residents.middle_name,
            Residents.last_name,
            Residents.suffix,
            Residents.date_of_birth,
            Residents.sex,
            Residents.civil_status,
            Residents.house_number,
            Residents.street,
            Residents.occupation,
            Residents.disability,
            Residents.cellphone_number,
            Residents.voter_status,
            Residents.status AS resident_status,
            Residents.date_and_time_created_registration,
            Residents.date_and_time_updated_registration,
            Roles.role_name
        FROM Accounts
        LEFT JOIN Residents ON Accounts.account_id = Residents.account_id
        LEFT JOIN Roles ON Accounts.role_id = Roles.role_id
        WHERE Accounts.role_id IN (1, 2, 3) 
        AND Accounts.status IN ('Active', 'Inactive') 
        AND (
            Residents.first_name LIKE ?
            OR Residents.middle_name LIKE ?
            OR Residents.last_name LIKE ?
            OR Accounts.username LIKE ?
            OR Accounts.email LIKE ?
            OR Residents.cellphone_number LIKE ?
            OR Residents.date_of_birth LIKE ?
            OR Accounts.status LIKE ?
            OR Roles.role_name LIKE ?
        )
        ORDER BY Accounts.date_and_time_updated DESC";

        $stmt = $conn->prepare($this->showAccountQuery);
        $searchTerm = "%{$query}%";

        // Bind parameters for the search
        $stmt->bind_param("sssssssss", 
            $searchTerm, // first_name
            $searchTerm, // middle_name
            $searchTerm, // last_name
            $searchTerm, // username
            $searchTerm, // email
            $searchTerm, // cellphone_number
            $searchTerm, // date_of_birth
            $searchTerm, // status
            $searchTerm  // role_name
        );

        // Execute and fetch results
        if ($stmt->execute()) {
            $result = $stmt->get_result();
        } else {
            echo "Error executing query: " . $stmt->error;
            return [];
        }

        $accounts = [];
        while ($account = $result->fetch_assoc()) {
            $accounts[] = $account;
        }

        return $accounts;
    }


    // Function to get account by account ID
    public function getAccountById($conn, $accountId) {
        $query = "SELECT * FROM accounts WHERE account_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $accountId);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }
    public function createOfficial($conn, $officialData) {
        // Start a transaction
        $conn->begin_transaction();

        try {
            // Insert the new official into the Officials table
            $query = "INSERT INTO Officials (resident_id, position, term_start, term_end, status) 
                      VALUES (?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($query);
            $stmt->bind_param(
                "issss", 
                $officialData['resident_id'], 
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
                $officialData['position'], // role_id corresponds to position
                $officialData['resident_id']
            );

            if (!$updateStmt->execute()) {
                throw new Exception("Error updating account role_id: " . $updateStmt->error);
            }

            // Commit the transaction
            $conn->commit();
            return true;

        } catch (Exception $e) {
            // Rollback the transaction in case of an error
            $conn->rollback();
            echo $e->getMessage();
            return false;
        }
    }

    public function showRoles($conn) {
        $query = "SELECT * FROM roles WHERE role_name NOT IN ('Residents', 'Admin')";
        $result = $conn->query($query);

        if ($result === FALSE) {
            return []; // Return an empty array if query fails
        }

        $roles = [];
        while ($role = $result->fetch_assoc()) {
            // Check if the role is 'Chairman' or 'Secretary'
            if (in_array($role['role_name'], ['Chairman', 'Secretary'])) {
                // Verify if there is already an official with this role
                $checkQuery = "SELECT COUNT(*) AS count FROM Officials WHERE position = ?";
                $stmt = $conn->prepare($checkQuery);
                $stmt->bind_param("s", $role['role_name']);
                $stmt->execute();
                $checkResult = $stmt->get_result();
                $row = $checkResult->fetch_assoc();

                // If no official exists with this role, add it to the roles array
                if ($row['count'] == 0) {
                    $roles[] = $role;
                }
            } else {
                // Add other roles directly
                $roles[] = $role;
            }
        }

        return $roles; // Return the filtered list of roles
    }
    
    // Function to revoke an official's account and update the Officials table
    public function revokeOfficial($conn, $accountId, $revokedBy, $revokeReason) {
        // Start a transaction
        $conn->begin_transaction();

        try {
            // Update the Officials table to set revoked_by, revoked_at, revoke_reason, and status to 'Inactive'
            $query = "UPDATE Officials 
                      SET revoked_by = ?, 
                          revoked_at = NOW(), 
                          revoke_reason = ?, 
                          status = 'Inactive' 
                      WHERE account_id = ?";
            $stmt = $conn->prepare($query);

            if (!$stmt) {
                throw new Exception("Failed to prepare update statement: " . $conn->error);
            }

            // Bind the parameters
            $stmt->bind_param("isi", $revokedBy, $revokeReason, $accountId);

            // Execute the update
            if (!$stmt->execute()) {
                throw new Exception("Failed to update Officials table: " . $stmt->error);
            }

            // Optionally, update the associated account's role_id to 4 (revoked)
            $updateAccountQuery = "UPDATE accounts a
                                   JOIN Residents r ON a.account_id = r.account_id
                                   JOIN Officials o ON r.resident_id = o.resident_id
                                   SET a.role_id = 4
                                   WHERE a.account_id = ?";
            $updateStmt = $conn->prepare($updateAccountQuery);

            if (!$updateStmt) {
                throw new Exception("Failed to prepare account update statement: " . $conn->error);
            }

            // Bind the parameter
            $updateStmt->bind_param("i", $accountId);

            // Execute the account update
            if (!$updateStmt->execute()) {
                throw new Exception("Failed to update account role_id: " . $updateStmt->error);
            }

            // Commit the transaction
            $conn->commit();
            return true;

        } catch (Exception $e) {
            // Rollback the transaction in case of an error
            $conn->rollback();
            echo $e->getMessage();
            return false;
        }
    }
}
?>
