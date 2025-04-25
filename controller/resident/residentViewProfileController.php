<?php
require '../../model/resident/residentViewProfileModel.php';

session_start();
$accountId = $_SESSION['account_id'] ?? null;

if (!$accountId) {
    $_SESSION['errorMessage'] = "Session expired. Please log in again.";
    header("Location: ../../public/views/login.php");
    exit();
}

$residentViewProfileModel = new ResidentViewProfileModel();

try {
    $personalAccount = $residentViewProfileModel->showPersonalAccount($conn, $accountId);
    if (!$personalAccount) {
        throw new Exception("Account not found.");
    }

    $_SESSION['username'] = $personalAccount['username'] ?? '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = isset($_POST['username']) ? htmlspecialchars(trim($_POST['username'])) : null;
        $email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : null;
        $currentPassword = isset($_POST['current-password']) ? trim($_POST['current-password']) : '';
        $newPassword = isset($_POST['new-password']) ? trim($_POST['new-password']) : '';

        $firstName = isset($_POST['firstName']) ? htmlspecialchars(trim($_POST['firstName'])) : null;
        $middleName = isset($_POST['middleName']) ? htmlspecialchars(trim($_POST['middleName'])) : null;
        $lastName = isset($_POST['lastName']) ? htmlspecialchars(trim($_POST['lastName'])) : null;
        $suffix = isset($_POST['suffix']) ? htmlspecialchars(trim($_POST['suffix'])) : null;
        $civilStatus = isset($_POST['civil-status']) ? htmlspecialchars(trim($_POST['civil-status'])) : null;
        $citizenship = isset($_POST['citizenship']) ? htmlspecialchars(trim($_POST['citizenship'])) : null;
        $birthdate = isset($_POST['birthdate']) ? htmlspecialchars(trim($_POST['birthdate'])) : null;
        $sex = isset($_POST['sex']) ? htmlspecialchars(trim($_POST['sex'])) : null;
        $phone = isset($_POST['phone']) ? htmlspecialchars(trim($_POST['phone'])) : null;
        $occupation = isset($_POST['occupation']) ? htmlspecialchars(trim($_POST['occupation'])) : null;
        $houseNumber = isset($_POST['houseNumber']) ? htmlspecialchars(trim($_POST['houseNumber'])) : null;
        $street = isset($_POST['street']) ? htmlspecialchars(trim($_POST['street'])) : null;
        $disability = isset($_POST['disability']) ? htmlspecialchars(trim($_POST['disability'])) : null;
        $voterStatus = isset($_POST['voter-status']) ? htmlspecialchars(trim($_POST['voter-status'])) : null;
        $status = isset($_POST['status']) ? htmlspecialchars(trim($_POST['status'])) : null;

        $accountData = [
            'account_id' => $accountId,
            'username' => $username,
            'email' => $email,
            'new_password' => $newPassword
        ];

        $residentData = [
            'account_id' => $accountId,
            'first_name' => $firstName,
            'middle_name' => $middleName,
            'last_name' => $lastName,
            'suffix' => $suffix,
            'civil_status' => $civilStatus,
            'citizenship' => $citizenship,
            'date_of_birth' => $birthdate,
            'sex' => $sex,
            'cellphone_number' => $phone,
            'occupation' => $occupation,
            'house_number' => $houseNumber,
            'street' => $street,
            'disability' => $disability,
            'voter_status' => $voterStatus,
            'status' => $status
        ];

        if (!empty($currentPassword) && !empty($newPassword)) {
            if (!password_verify($currentPassword, $personalAccount['password'])) {
                $_SESSION['errorMessage'] = "Incorrect current password.";
                header("Location: residentViewProfileController.php");
                exit();
            }
            if (password_verify($newPassword, $personalAccount['password'])) {
                $_SESSION['errorMessage'] = "New password cannot be the same as the current password.";
                header("Location: residentViewProfileController.php");
                exit();
            }
        } elseif (!empty($currentPassword) xor !empty($newPassword)) {
            $_SESSION['errorMessage'] = "Both current and new passwords must be provided to change password.";
            header("Location: residentViewProfileController.php");
            exit();
        }

        $accountUpdated = false;
        $residentUpdated = false;

        if (!empty($username) || !empty($email) || (!empty($currentPassword) && !empty($newPassword))) {
            $accountUpdated = $residentViewProfileModel->updatePersonalAccountInformation($conn, $accountData);
        }

        if (!empty($firstName) || !empty($lastName) || !empty($civilStatus) || !empty($citizenship) || !empty($birthdate) || !empty($sex) || !empty($status)) {
            $residentUpdated = $residentViewProfileModel->updatePersonalResidentInformation($conn, $residentData);
        }

        if (!empty($_FILES['profileImageUpload']['name'])) {
            $file = $_FILES['profileImageUpload'];
            $allowedTypes = ['image/jpeg', 'image/png'];
            $uploadDir = '../../img/profile_images/';
            $imageName = uniqid() . '_' . basename($file['name']);
            $uploadPath = $uploadDir . $imageName;
            $relativePath = 'img/profile_images/' . $imageName;

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            if (in_array($file['type'], $allowedTypes)) {
                if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                    $query = "UPDATE Accounts SET profile_image_blob = ?, profile_image_name = ? WHERE account_id = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("ssi", $relativePath, $imageName, $accountId);
                    if ($stmt->execute()) {
                        $accountUpdated = true;
                    } else {
                        unlink($uploadPath);
                        throw new Exception("Failed to update profile image in database.");
                    }
                    $stmt->close();
                } else {
                    throw new Exception("Failed to upload profile image.");
                }
            } else {
                $_SESSION['errorMessage'] = "Invalid image type. Only JPEG and PNG are allowed.";
                header("Location: residentViewProfileController.php");
                exit();
            }
        }

        if ($accountUpdated && $residentUpdated) {
            $_SESSION['message'] = "Account and resident information updated successfully!";
        } elseif ($accountUpdated) {
            $_SESSION['message'] = "Account information updated successfully!";
        } elseif ($residentUpdated) {
            $_SESSION['message'] = "Resident information updated successfully!";
        } else {
            $_SESSION['errorMessage'] = "No changes were made.";
        }

        header("Location: residentViewProfileController.php");
        exit();
    }
} catch (Exception $e) {
    $_SESSION['errorMessage'] = "Error: " . $e->getMessage();
    header("Location: residentViewProfileController.php");
    exit();
}

require '../../public/views/resident/residentViewProfile.php';
?>