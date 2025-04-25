<?php
require_once 'dbconnection.php';

class RegisterModel {
    private array $data;

    public function __construct(array $data) {
        $this->data = $data;
    }
    
    public function createAccount($conn) {
        try {
            if (!$conn || $conn->connect_error) {
                throw new Exception("Database connection failed: " . ($conn ? $conn->connect_error : "No connection"));
            }
    
            $conn->begin_transaction();
    
            // Insert into accounts table
            $accountQuery = "INSERT INTO accounts (
                username, password, role_id, status, email, id_image_name, id_image_blob,
                guided_by_id_name, guided_by_id_blob
            ) VALUES (?, ?, ?, 'pending', ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($accountQuery);
            if (!$stmt) {
                throw new Exception("Failed to prepare account insert: " . $conn->error);
            }
    
            $stmt->bind_param(
                "ssisssss",
                $this->data['username'],
                $this->data['hashedPassword'],
                $this->data['roleId'],
                $this->data['email'],
                $this->data['idImageName'],
                $this->data['idImageBlob'],
                $this->data['guidedByIdName'],
                $this->data['guidedByIdBlob']
            );
    
            if (!$stmt->execute()) {
                throw new Exception("Failed to insert account: " . $stmt->error);
            }
    
            $accountId = $conn->insert_id;
            $stmt->close();
    
            // Log status for debugging
            error_log("Inserting resident with status: " . $this->data['status']);
    
            // Insert into residents table
            $residentQuery = "INSERT INTO residents (
                account_id, first_name, middle_name, last_name, suffix, date_of_birth, sex,
                cellphone_number, civil_status, house_number, street, citizenship, occupation,
                disability, voter_status, salary, status, osca_id, osca_date_issued, relationship, assisted_by
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($residentQuery);
            if (!$stmt) {
                error_log("Failed to prepare resident insert: " . $conn->error);
                throw new Exception("Failed to prepare resident insert: " . $conn->error);
            }
    
            $bindResult = $stmt->bind_param(
                "issssssssssssssssssss",
                $accountId,
                $this->data['firstName'],
                $this->data['middleName'],
                $this->data['lastName'],
                $this->data['suffix'],
                $this->data['dateOfBirth'],
                $this->data['sex'],
                $this->data['phoneNumber'],
                $this->data['civilStatus'],
                $this->data['houseNumber'],
                $this->data['street'],
                $this->data['citizenship'],
                $this->data['occupation'],
                $this->data['disability'],
                $this->data['voterStatus'],
                $this->data['salary'],
                $this->data['status'],
                $this->data['oscaId'],
                $this->data['oscaDateIssued'],
                $this->data['relationship'],
                $this->data['assistedBy']
            );
            if (!$bindResult) {
                error_log("Failed to bind parameters for resident insert: " . $stmt->error);
                throw new Exception("Failed to bind parameters for resident insert: " . $stmt->error);
            }
    
            if (!$stmt->execute()) {
                $error = $stmt->error;
                error_log("Resident insert failed: " . $error);
                throw new Exception("Failed to insert resident: " . $error);
            }
            $stmt->close();
    
            $conn->commit();
            $_SESSION['successMessage'] = "Account successfully created. Please wait for approval. Your username is " . $this->data['username'] . " but you can also use your phone number " . $this->data['phoneNumber'] . " and password is " . $_SESSION['generatedPassword'];
            header('Location: ../public/views/login.php');
            exit();
    
        } catch (Exception $e) {
            if (isset($conn) && $conn instanceof mysqli) {
                try {
                    $conn->rollback();
                } catch (Exception $rollbackEx) {
                    error_log("Rollback failed: " . $rollbackEx->getMessage());
                }
            }
    
            $errorMessage = strpos($e->getMessage(), 'Duplicate entry') !== false 
                ? "That Phone Number is already taken." 
                : "Registration failed: " . $e->getMessage();
            error_log("Registration error: " . $errorMessage);
            $_SESSION['errorMessages'] = [$errorMessage];
            header('Location: ../public/views/register.php');
            exit();
        }
    }
}
?>