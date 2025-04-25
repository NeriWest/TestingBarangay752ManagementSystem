<?php
require_once 'dbconnection.php';

// Create a database connection
$db = new dbcon();
$conn = $db->getConnection(); 

class ForgotPasswordModel {
    public string $forgotPasswordQuery;
    public string $usernameOrPhone; // This will store either the username or cellphone number

    public function __construct($usernameOrPhone) {
        // Store the username or cellphone number for later use
        $this->usernameOrPhone = $usernameOrPhone;
    
        // Query to check both username and cellphone_number in Accounts
        $this->forgotPasswordQuery = "
        SELECT Residents.*, Accounts.username, Accounts.status AS account_status 
        FROM Residents 
        LEFT JOIN Accounts ON Residents.account_id = Accounts.account_id 
        WHERE (Accounts.username = ? OR Residents.cellphone_number = ?)";
    }
    
    public function verifyIdentity($conn) {
        // Prepare the SQL statement to avoid SQL injection
        $stmt = $conn->prepare($this->forgotPasswordQuery);
        if ($stmt === false) {
            die('Prepare failed: ' . htmlspecialchars($conn->error));
        }
    
        // Bind the input parameters (usernameOrPhone will be used for both username and cellphone_number)
        $stmt->bind_param("ss", $this->usernameOrPhone, $this->usernameOrPhone);
    
        // Execute the prepared statement
        $stmt->execute();
    
        // Get the result
        $result = $stmt->get_result();
    
        // Check if there is a matching record
        if ($result && $result->num_rows > 0) {
            // Use the fetch_assoc method on the result object
            $row = $result->fetch_assoc();
    
            // Check the account status (active, pending, inactive, disapproved, etc.)
            if ($row['status'] === 'active') {
                // If the account is active, proceed to send a password reset link or OTP
                $_SESSION['message'] = "A password reset link or OTP has been sent to your registered contact.";
                return $row;
            } elseif ($row['status'] === 'pending') {
                // Account is pending
                $_SESSION['errorMessage'] = "Your account is pending. Please wait for activation.";
                return false;
            } elseif ($row['status'] === 'disapproved') {
                // Account is disapproved
                $_SESSION['errorMessage'] = "Your account has been disapproved. Please contact support.";
                return false;
            } else {
                // Account is inactive or other status
                $_SESSION['errorMessage'] = "Your account is inactive. Please contact support.";
                return false;
            }
        } else {
            // No matching username or cellphone number found
            $_SESSION['errorMessage'] = "No account found with the provided details.";
            return false;
        }
    }

    public function resetPassword($conn, $newPassword) {
        // Hash the new password for security
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

        // Query to update the password in the Accounts table
        $resetPasswordQuery = "
        UPDATE Accounts 
        SET password = ? 
        WHERE username = ? OR account_id = (
            SELECT account_id 
            FROM Residents 
            WHERE cellphone_number = ?
        )";

        // Prepare the SQL statement to avoid SQL injection
        $stmt = $conn->prepare($resetPasswordQuery);
        if ($stmt === false) {
            die('Prepare failed: ' . htmlspecialchars($conn->error));
        }

        // Bind the input parameters
        $stmt->bind_param("sss", $hashedPassword, $this->usernameOrPhone, $this->usernameOrPhone);

        // Execute the prepared statement
        if ($stmt->execute()) {
            // Check if any rows were affected
            if ($stmt->affected_rows > 0) {
                $_SESSION['message'] = "Password has been successfully reset.";
                return true;
            } else {
                $_SESSION['errorMessage'] = "Failed to reset password. Please check the provided details.";
                return false;
            }
        } else {
            $_SESSION['errorMessage'] = "Error executing query: " . htmlspecialchars($stmt->error);
            return false;
        }
    }
}
?>
