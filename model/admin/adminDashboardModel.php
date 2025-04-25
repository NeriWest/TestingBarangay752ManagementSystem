<?php
require_once '../../model/dbconnection.php';

$db = new dbcon();
$conn = $db->getConnection();

class ResidentViewProfileModel {
    public function showPersonalAccount($conn, $accountId) {
        $query = "
            SELECT 
                Residents.*, 
                Accounts.username, 
                Accounts.password, 
                Accounts.email,
                Accounts.role_id,
                Accounts.status AS account_status, 
                Accounts.profile_image_name, 
                Accounts.profile_image_blob, 
                Accounts.id_image_name, 
                Accounts.id_image_blob 
            FROM Residents 
            LEFT JOIN Accounts ON Residents.account_id = Accounts.account_id 
            WHERE Accounts.account_id = ?
        ";
    
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $conn->error);
        }
    
        $stmt->bind_param("i", $accountId);
        $stmt->execute();
        $result = $stmt->get_result();
        $personalAccount = $result->fetch_assoc();
        $stmt->close();
    
        return $personalAccount;
    }
    
    public function updatePersonalResidentInformation($conn, $residentData) {
        $query = "UPDATE Residents SET 
                    first_name = ?, middle_name = ?, last_name = ?, suffix = ?,
                    civil_status = ?, citizenship = ?, date_of_birth = ?, sex = ?,
                    cellphone_number = ?, occupation = ?, house_number = ?, street = ?,
                    disability = ?, voter_status = ?, status = ?
                  WHERE account_id = ?";
    
        $stmt = $conn->prepare($query);
        if (!$stmt) throw new Exception("Resident update prep failed: " . $conn->error);
    
        $stmt->bind_param("sssssssssssssssi",
            $residentData['first_name'],
            $residentData['middle_name'],
            $residentData['last_name'],
            $residentData['suffix'],
            $residentData['civil_status'],
            $residentData['citizenship'],
            $residentData['date_of_birth'],
            $residentData['sex'],
            $residentData['cellphone_number'],
            $residentData['occupation'],
            $residentData['house_number'],
            $residentData['street'],
            $residentData['disability'],
            $residentData['voter_status'],
            $residentData['status'],
            $residentData['account_id']
        );
    
        if (!$stmt->execute()) {
            throw new Exception("Resident update failed: " . $stmt->error);
        }
    
        $stmt->close();
        return true;
    }
    
    public function updatePersonalAccountInformation($conn, $accountData) {
        if (empty($accountData['new_password'])) {
            $query = "SELECT password FROM Accounts WHERE account_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $accountData['account_id']);
            $stmt->execute();
            $stmt->bind_result($existingPassword);
            $stmt->fetch();
            $stmt->close();
            $accountData['password'] = $existingPassword;
        } else {
            $accountData['password'] = password_hash($accountData['new_password'], PASSWORD_DEFAULT);
        }
    
        $query = "UPDATE Accounts SET username = ?, email = ?, password = ? WHERE account_id = ?";
        $stmt = $conn->prepare($query);
        if (!$stmt) throw new Exception("Account update prep failed: " . $conn->error);
    
        $stmt->bind_param("sssi", 
            $accountData['username'], 
            $accountData['email'], 
            $accountData['password'], 
            $accountData['account_id']
        );
    
        if (!$stmt->execute()) {
            throw new Exception("Account update failed: " . $stmt->error);
        }
    
        $stmt->close();
        return true;
    }
    
    public function getAccountById($conn, $accountId) {
        $query = "SELECT * FROM Accounts WHERE account_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $accountId);
        $stmt->execute();
        $result = $stmt->get_result();
        $account = $result->fetch_assoc();
        $stmt->close();
        return $account;
    }
}
?>