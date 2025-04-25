<?php
require_once 'dbconnection.php';

// Create a database connection
$db = new dbcon();
$conn = $db->getConnection(); 

class LoginModel {
    public string $loginQuery;
    public string $usernameOrPhone; // This will store either the username or cellphone number
    public string $password; // Define a property to hold the password

    public function __construct($usernameOrPhone, $password) {
        // Store the username or cellphone number and password for later use
        $this->usernameOrPhone = $usernameOrPhone;
        $this->password = $password;
    
        // Query to check both username and cellphone_number in Accounts
        $this->loginQuery = "SELECT Residents.*, Accounts.username, Accounts.password, Accounts.status AS account_status, Accounts.role_id 
        FROM Accounts 
        LEFT JOIN Residents ON Accounts.account_id = Residents.account_id 
        WHERE (Accounts.username = ? OR Residents.cellphone_number = ?)";

    }
    
    public function loginVerification($conn) {
        // Prepare the SQL statement to avoid SQL injection
        $stmt = $conn->prepare($this->loginQuery);
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
            if ($row['account_status'] === 'active') {
                // If the account is active, proceed to verify the password
                if (($row['username'] === $this->usernameOrPhone) || ($row['cellphone_number'] === $this->usernameOrPhone)) {
                    // Verify the provided password using password_verify
                    $dbPassword = $row['password']; // The hashed password from the database
                    if (password_verify($this->password, $dbPassword)) {
                        // Successful login, return user details
                        return $row;
                    } else {
                        // Incorrect password
                        $_SESSION['errorMessage'] = "Invalid username or password";
                        header('Location: ../public/views/login.php');
                        exit();
                        }
                } else {
                    // No matching username or cellphone number
                    $_SESSION['errorMessage'] = "Invalid username or password";
                    header('Location: ../public/views/login.php');
                    exit();
                }
            } elseif ($row['account_status'] === 'pending') {
                // Account is pending
                $_SESSION['errorMessage'] = "Your account is pending. Please wait for activation.";
                header('Location: ../public/views/login.php');
                exit();
            } elseif ($row['account_status'] === 'disapproved') {
                // Account is disapproved
                $_SESSION['errorMessage'] = "Your account has been disapproved. Kindly come to the barangay for assistance.";
                header('Location: ../public/views/login.php');
                exit();
            } else {
                // Account is inactive or other status
                $_SESSION['errorMessage'] = "Your account is inactive. Kindly come to the barangay for assistance.";
                header('Location: ../public/views/login.php');
                exit();
            }
        } else {
            // No matching username or cellphone number found
            $_SESSION['errorMessage'] = "Invalid username or password";
            header('Location: ../public/views/login.php');
            exit();
        }
    }
}
?>
