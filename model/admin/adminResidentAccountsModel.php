<?php
require_once 'dbconnection.php';

$db = new dbcon();
$conn = $db->getConnection();

class AdminResidentAccountsModel {
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
            Accounts.guided_by_id_name,
            Accounts.guided_by_id_blob, 
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
            Residents.relationship,
            Residents.disability,
            Residents.cellphone_number,
            Residents.assisted_by,
            Residents.voter_status,
            Residents.status AS resident_status,
            Residents.date_and_time_created_registration,
            Residents.date_and_time_updated_registration
        FROM Accounts
        LEFT JOIN Residents ON Accounts.account_id = Residents.account_id
        WHERE Accounts.role_id = 4
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
        // Count the total number of accounts
        $query = "SELECT COUNT(*) AS total FROM Accounts WHERE Accounts.role_id = 4";
        $result = $conn->query($query);
        $row = $result->fetch_assoc();

        return $row['total']; // Return the total number of accounts
    }

      // Function to update account, including password reset
    public function updateAccount($conn, $accountData) {
        // Fetch the existing password if no new password is provided
        if (empty($accountData['password'])) {
            $query = "SELECT password FROM accounts WHERE account_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $accountData['account_id']);
            $stmt->execute();
            $stmt->bind_result($existingPassword);
            $stmt->fetch();
            $stmt->close();
            $accountData['password'] = $existingPassword;
        } else {
            // Hash the new password if provided
            $accountData['password'] = password_hash($accountData['password'], PASSWORD_DEFAULT);
        }

        // Define the update query for the accounts table
        $query = "UPDATE accounts SET
                    username = ?,
                    email = ?,
                    password = ?,
                    role_id = ?,
                    status = ?,
                    profile_image_name = ?,
                    id_image_name = ?
                WHERE account_id = ?";

        // Prepare the statement
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare update statement: " . $conn->error);
        }

        // Bind parameters for the account update
        $stmt->bind_param("sssisssi",
            $accountData['username'],
            $accountData['email'],
            $accountData['password'], // Use the newly hashed or existing password
            $accountData['role_id'],
            $accountData['status'],
            $accountData['profile_image_name'],
            $accountData['id_image_name'],
            $accountData['account_id']
        );

        // Execute the update
        if (!$stmt->execute()) {
            throw new Exception("Failed to update account: " . $stmt->error);
        }

        // Update the Residents table to reflect the new account_id if provided
        if (isset($accountData['resident_id'])) {
            $updateResidentQuery = "UPDATE Residents SET account_id = ? WHERE resident_id = ?";
            $stmt = $conn->prepare($updateResidentQuery);
            if (!$stmt) {
                throw new Exception("Failed to prepare resident update statement: " . $conn->error);
            }

            // Bind parameters for the resident update
            $stmt->bind_param("ii", $accountData['account_id'], $accountData['resident_id']);

            // Execute the resident update
            if (!$stmt->execute()) {
                throw new Exception("Failed to update resident: " . $stmt->error);
            }
        }

        // Close the statement
        $stmt->close();

        return true;
    }


    // Function to update account, including password reset
    public function updatePersonalAccount($conn, $accountData) {
        // Fetch the existing password if no new password is provided
        if (empty($accountData['password'])) {
            $query = "SELECT password FROM accounts WHERE account_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $accountData['account_id']);
            $stmt->execute();
            $stmt->bind_result($existingPassword);
            $stmt->fetch();
            $stmt->close();
            $accountData['password'] = $existingPassword;
        } else {
            // Hash the new password if provided
            $accountData['password'] = password_hash($accountData['password'], PASSWORD_DEFAULT);
        }

        // Define the update query, excluding fields like cellphone_number if not required
        $query = "UPDATE accounts SET
                    username = ?,
                    email = ?,
                    password = ?,
                    role_id = ?,
                    status = ?,
                    profile_image_name = ?,
                    id_image_name = ?
                WHERE account_id = ?";

        // Prepare the statement
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare update statement: " . $conn->error);
        }

        // Bind parameters for the account update
        $stmt->bind_param("sssisssi",
            $accountData['username'],
            $accountData['email'],
            $accountData['password'], // Use the newly hashed or existing password
            $accountData['role_id'],
            $accountData['status'],
            $accountData['profile_image_name'],
            $accountData['id_image_name'],
            $accountData['account_id']
        );

        // Execute the update
        if (!$stmt->execute()) {
            throw new Exception("Failed to update account: " . $stmt->error);
        }

        // Close the statement
        $stmt->close();

        return true;
    }
    // Function to search accounts based on role_name, username, status, email, resident names, cellphone number, date of birth, Assisted_ID_Image, or Assisted_Name
    public function searchAccounts($conn, $query) {
        $this->showAccountQuery = "
        SELECT 
            Accounts.*,
            Accounts.profile_image_name,
            Accounts.profile_image_blob,
            Accounts.id_image_name,
            Accounts.id_image_blob,
            Accounts.guided_by_id_name,
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
            Residents.relationship,
            Residents.disability,
            Residents.cellphone_number,
            Residents.voter_status,
            Residents.status AS resident_status,
            Residents.date_and_time_created_registration,
            Residents.date_and_time_updated_registration,
            Residents.assisted_by
        FROM Accounts
        LEFT JOIN Residents ON Accounts.account_id = Residents.account_id
        WHERE Accounts.role_id = 4 AND (
            Residents.first_name LIKE ?
            OR Residents.middle_name LIKE ?
            OR Residents.last_name LIKE ?
            OR Accounts.username LIKE ?
            OR Accounts.email LIKE ?
            OR Residents.cellphone_number LIKE ?
            OR Residents.date_of_birth LIKE ?
            OR Accounts.status LIKE ?
            OR Residents.relationship LIKE ?
            OR Accounts.guided_by_id_name LIKE ?
            OR Residents.assisted_by LIKE ?
        )
        ORDER BY Accounts.date_and_time_updated DESC";

        $stmt = $conn->prepare($this->showAccountQuery);
        $searchTerm = "%{$query}%";

        // Bind parameters for the search
        $stmt->bind_param("sssssssssss", 
            $searchTerm, // first_name
            $searchTerm, // middle_name
            $searchTerm, // last_name
            $searchTerm, // username
            $searchTerm, // email
            $searchTerm, // cellphone_number
            $searchTerm, // date_of_birth
            $searchTerm, // status
            $searchTerm, // relationship
            $searchTerm, // Assisted_ID_Image
            $searchTerm  // Assisted_Name
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

    // Function to get account by account ID, including contact number and full name
    public function getAccountById($conn, $accountId) {
        $query = "
        SELECT 
            Accounts.*,
            CONCAT(Residents.first_name, ' ', IFNULL(Residents.middle_name, ''), ' ', Residents.last_name, ' ', IFNULL(Residents.suffix, '')) AS full_name,
            Residents.cellphone_number AS cellphone_number,
            DATE_FORMAT(Residents.date_of_birth, '%m/%d/%Y') AS date_of_birth
        FROM accounts
        LEFT JOIN Residents ON Accounts.account_id = Residents.account_id
        WHERE Accounts.account_id = ?";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $accountId);
        $stmt->execute();
        $result = $stmt->get_result();

        $accounts = [];
        while ($account = $result->fetch_assoc()) {
            $accounts[] = $account;
        }

        return $accounts;
    }

    public function createAccount($conn, $residentId, $username, $email, $password, $privilege, $status) {
        try {
            // Start a transaction to ensure atomicity
            $conn->begin_transaction();
    
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    
            // Fetch role ID directly based on privilege (assuming privilege is a numeric role_id)
            $roleQuery = "SELECT role_id FROM roles WHERE role_id = ?";
            $stmt = $conn->prepare($roleQuery);
            if (!$stmt) {
                throw new Exception("Failed to prepare role query statement: " . $conn->error);
            }
    
            // Bind privilege to the role query (assuming privilege is already the role_id)
            $stmt->bind_param("i", $privilege);
            $stmt->execute();
            $stmt->bind_result($roleId);
            $stmt->fetch();
            $stmt->close();
    
            // If no matching role is found, throw an error
            if (!$roleId) {
                throw new Exception("No matching role found for the given privilege: " . $privilege);
            }
    
            // Insert into the accounts table
            $insertAccountQuery = "INSERT INTO accounts (username, password, status, email, role_id) 
                                   VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insertAccountQuery);
            if (!$stmt) {
                throw new Exception("Failed to prepare account insert statement: " . $conn->error);
            }
    
            // Bind parameters for the accounts table
            $stmt->bind_param("ssssi", $username, $hashedPassword, $status, $email, $roleId);
    
            // Execute the account insertion
            if (!$stmt->execute()) {
                throw new Exception("Failed to insert account: " . $stmt->error);
            }
    
            // Get the last inserted account_id
            $accountId = $conn->insert_id;
    
            // Update the Residents table to set the account_id and role for the selected resident
            $updateResidentQuery = "UPDATE Residents SET account_id = ? WHERE resident_id = ?";
            $stmt = $conn->prepare($updateResidentQuery);
            if (!$stmt) {
                throw new Exception("Failed to prepare resident update statement: " . $conn->error);
            }
    
            // Bind parameters for the residents table (set the resident's role based on the selected privilege)
            $stmt->bind_param("ii", $accountId, $residentId);
    
            // Execute the resident update
            if (!$stmt->execute()) {
                throw new Exception("Failed to update resident: " . $stmt->error);
            }
    
            // Commit the transaction if everything is successful
            $conn->commit();
    
            // Close the statement
            $stmt->close();
    
        } catch (Exception $e) {
            // Rollback the transaction in case of failure
            $conn->rollback();
            throw new Exception("Error creating account: " . $e->getMessage());
        }
    }
    
    // Function to update the status of an account with remarks
    public function updateAccountStatus($conn, $accountId, $status, $remarks) {
        // Define the update query for the account status and remarks
        $query = "UPDATE accounts SET status = ?, remarks = ? WHERE account_id = ?";

        // Prepare the statement
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare update statement: " . $conn->error);
        }

        // Bind parameters for the account status and remarks update
        $stmt->bind_param("ssi", $status, $remarks, $accountId);

        // Execute the update
        if (!$stmt->execute()) {
            throw new Exception("Failed to update account status with remarks: " . $stmt->error);
        }

        // Close the statement
        $stmt->close();

        return true;
    }
}
?>
