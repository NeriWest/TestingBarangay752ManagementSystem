<?php
require_once '../model/registerModel.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $db = new dbcon();
    $conn = $db->getConnection();
    if (!$conn || $conn->connect_error) {
        $_SESSION['errorMessages'] = ["Database connection failed: " . ($conn ? $conn->connect_error : "No connection")];
        header('Location: ../public/views/register.php');
        exit();
    }

    // Sanitize and retrieve form inputs into an array
    $data = [
        'firstName' => trim(stripslashes(htmlspecialchars($_POST['first-name'] ?? ''))),
        'middleName' => trim(stripslashes(htmlspecialchars($_POST['middle-name'] ?? ''))),
        'lastName' => trim(stripslashes(htmlspecialchars($_POST['last-name'] ?? ''))),
        'email' => trim(stripslashes(htmlspecialchars($_POST['email'] ?? ''))),
        'phoneNumber' => trim(stripslashes(htmlspecialchars($_POST['cellphone_number'] ?? ''))),
        'dateOfBirth' => trim(stripslashes(htmlspecialchars($_POST['birth-date'] ?? ''))),
        'sex' => trim(stripslashes(htmlspecialchars($_POST['sex'] ?? ''))),
        'suffix' => trim(stripslashes(htmlspecialchars($_POST['suffix'] ?? $_POST['other_suffix'] ?? ''))),
        'civilStatus' => trim(stripslashes(htmlspecialchars($_POST['civil_status'] ?? ''))),
        'houseNumber' => trim(stripslashes(htmlspecialchars($_POST['house_number'] ?? ''))),
        'street' => trim(stripslashes(htmlspecialchars($_POST['street'] ?? ''))),
        'citizenship' => trim(stripslashes(htmlspecialchars($_POST['citizenship'] ?? ''))),
        'occupation' => trim(stripslashes(htmlspecialchars($_POST['occupation'] ?? ''))),
        'disability' => trim(stripslashes(htmlspecialchars($_POST['disability'] ?? ''))),
        'voterStatus' => trim(stripslashes(htmlspecialchars($_POST['voter_status'] ?? ''))),
        'salary' => isset($_POST['salary']) ? floatval(trim(stripslashes(htmlspecialchars($_POST['salary'])))) : 0,
        'status' => isset($_POST['bedridden']) && trim($_POST['bedridden']) === 'Yes' ? 'bedridden' : 'active',
        'oscaId' => isset($_POST['osca_id']) ? trim(stripslashes(htmlspecialchars($_POST['osca_id']))) : null,
        'oscaDateIssued' => isset($_POST['osca_date_issued']) ? trim(stripslashes(htmlspecialchars($_POST['osca_date_issued']))) : null,
        'relationship' => trim(stripslashes(htmlspecialchars($_POST['relationship'] ?? 'none'))),
        'assistedBy' => trim(stripslashes(htmlspecialchars($_POST['assisted_by'] ?? ''))),
        'idImageName' => '',
        'idImageBlob' => '../img/id_images/',
        'guidedByIdName' => '',
        'guidedByIdBlob' => '../img/guided_by_id_images/',
    ];

    // Log critical inputs for debugging
    error_log("POST birth-date: " . var_export($_POST['birth-date'] ?? 'not set', true));
    error_log("Assigned dateOfBirth: " . $data['dateOfBirth']);
    error_log("POST bedridden: " . var_export($_POST['bedridden'] ?? 'not set', true));
    error_log("Assigned status: " . $data['status']);

    // Generate username and password
    $data['username'] = strtolower(str_replace(' ', '', $data['lastName'] . '.' . $data['firstName'])) . rand(100, 999) . '.752';
    $password = date('m/d/Y', strtotime(str_replace('-', '/', $data['dateOfBirth'])));
    $data['hashedPassword'] = password_hash($password, PASSWORD_DEFAULT);
    $_SESSION['generatedPassword'] = $password;

    $errors = [];

    // Validate required fields
    if (empty($data['firstName'])) $errors[] = 'First Name is required.';
    if (empty($data['lastName'])) $errors[] = 'Last Name is required.';
    if (empty($data['dateOfBirth'])) {
        $errors[] = 'Birthdate is required.';
        error_log("Birthdate validation failed: dateOfBirth is empty");
    }
    if (empty($data['sex'])) $errors[] = 'Sex is required.';
    if (empty($data['civilStatus'])) $errors[] = 'Civil Status is required.';
    if (empty($data['phoneNumber'])) $errors[] = 'Contact Number is required.';
    if (empty($data['houseNumber'])) $errors[] = 'House Number is required.';
    if (empty($data['street'])) $errors[] = 'Street is required.';
    if (empty($data['citizenship'])) $errors[] = 'Citizenship is required.';
    if (empty($data['occupation'])) $errors[] = 'Occupation is required.';
    if (empty($data['disability'])) $errors[] = 'Disability Status is required.';
    if (empty($data['voterStatus'])) $errors[] = 'Voter Status is required.';
    if (!isset($_POST['bedridden']) || !in_array($_POST['bedridden'], ['Yes', 'No'])) {
        $errors[] = 'Bedridden status must be either Yes or No.';
        error_log("Invalid bedridden input: " . var_export($_POST['bedridden'] ?? 'not set', true));
    }

    // Validate disability
    $validDisabilities = ['With Disability', 'Without Disability'];
    if (!in_array($data['disability'], $validDisabilities)) {
        $errors[] = 'Invalid disability status selected.';
    }

    // Validate voter status
    $validVoterStatuses = ['voter', 'non-voter'];
    if (!in_array($data['voterStatus'], $validVoterStatuses)) {
        $errors[] = 'Invalid voter status selected.';
    }

    // Calculate age for OSCA and relationship validation
    if (!empty($data['dateOfBirth'])) {
        try {
            $birthDate = new DateTime($data['dateOfBirth']);
            $today = new DateTime();
            $age = $today->diff($birthDate)->y;



            // Validate relationship for bedridden, minors (1-12), or those with disability
            $validRelationships = ['parent', 'guardian', 'representative', 'none'];
            if (($age >= 1 && $age <= 12) || $data['status'] === 'bedridden' || $data['disability'] === 'With Disability') {
                if (!in_array($data['relationship'], $validRelationships)) {
                    $errors[] = 'Relationship is required for minors, bedridden individuals, or those with disabilities.';
                }
                if (in_array($data['relationship'], ['parent', 'guardian', 'representative']) && empty($data['assistedBy'])) {
                    $errors[] = 'Assisted By Full Name is required when a relationship is selected.';
                }
            } else {
                $data['relationship'] = 'none';
                $data['assistedBy'] = '';
            }
        } catch (Exception $e) {
            $errors[] = 'Invalid birthdate format: ' . $e->getMessage();
            error_log("Birthdate parsing error: " . $e->getMessage());
        }
    }

    // Retrieve role ID
    $roleStmt = $conn->prepare("SELECT role_id FROM roles WHERE role_name = 'Residents' LIMIT 1");
    if (!$roleStmt || !$roleStmt->execute()) {
        $errors[] = "Failed to retrieve role: " . ($roleStmt ? $roleStmt->error : $conn->error);
    } else {
        $roleResult = $roleStmt->get_result();
        $role = $roleResult->fetch_assoc();
        $data['roleId'] = $role ? $role['role_id'] : null;
        if (!$data['roleId']) $errors[] = 'Role not found.';
        $roleStmt->close();
    }

    // Handle id_image file upload
    if (isset($_FILES['id_image']) && $_FILES['id_image']['error'] == UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['id_image']['tmp_name'];
        $fileName = basename($_FILES['id_image']['name']);
        $fileType = mime_content_type($fileTmpPath);

        // Validate file type and size
        if (!in_array($fileType, ['image/jpeg', 'image/png'])) {
            $errors[] = 'Invalid ID image type. Only JPG and PNG are allowed.';
        } elseif ($_FILES['id_image']['size'] > 5242880) { // 5MB limit
            $errors[] = 'ID image size exceeds 5MB limit.';
        } else {
            $uploadDirectory = '../img/id_images/';
            if (!is_dir($uploadDirectory)) {
                mkdir($uploadDirectory, 0755, true);
            }

            // Create a unique file name
            $uniqueFileName = uniqid() . '_' . $fileName;
            $targetFilePath = $uploadDirectory . $uniqueFileName;

            if (move_uploaded_file($fileTmpPath, $targetFilePath)) {
                $data['idImageName'] = $uniqueFileName;
                $data['idImageBlob'] .= $uniqueFileName;
            } else {
                $errors[] = 'Failed to upload ID image.';
            }
        }
    } else {
        $errors[] = 'No ID image uploaded or upload error.';
    }

    // Handle guided_by_id file upload
    if (in_array($data['relationship'], ['parent', 'guardian', 'representative'])) {
        if (isset($_FILES['guided_by_id']) && $_FILES['guided_by_id']['error'] == UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['guided_by_id']['tmp_name'];
            $fileName = basename($_FILES['guided_by_id']['name']);
            $fileType = mime_content_type($fileTmpPath);

            // Validate file type and size
            if (!in_array($fileType, ['image/jpeg', 'image/png'])) {
                $errors[] = 'Invalid guided by ID image type. Only JPG and PNG are allowed.';
            } elseif ($_FILES['guided_by_id']['size'] > 5242880) { // 5MB limit
                $errors[] = 'Guided by ID image size exceeds 5MB limit.';
            } else {
                $uploadDirectory = '../img/guided_by_id_images/';
                if (!is_dir($uploadDirectory)) {
                    mkdir($uploadDirectory, 0755, true);
                }

                // Create a unique file name
                $uniqueFileName = uniqid() . '_' . $fileName;
                $targetFilePath = $uploadDirectory . $uniqueFileName;

                if (move_uploaded_file($fileTmpPath, $targetFilePath)) {
                    $data['guidedByIdName'] = $uniqueFileName;
                    $data['guidedByIdBlob'] .= $uniqueFileName;
                } else {
                    $errors[] = 'Failed to upload guided by ID image.';
                }
            }
        } else {
            $errors[] = 'Guided by ID image is required for selected relationship.';
        }
    }

    // If there are errors, store form data and redirect
    if (!empty($errors)) {
        $_SESSION['errorMessages'] = $errors;
        $_SESSION['form_data'] = array_merge($_POST, [
            'id_image' => isset($_FILES['id_image']) && $_FILES['id_image']['error'] == UPLOAD_ERR_OK ? $_FILES['id_image']['name'] : ($_SESSION['form_data']['id_image'] ?? ''),
            'guided_by_id' => isset($_FILES['guided_by_id']) && $_FILES['guided_by_id']['error'] == UPLOAD_ERR_OK ? $_FILES['guided_by_id']['name'] : ($_SESSION['form_data']['guided_by_id'] ?? '')
        ]);
        header('Location: ../public/views/register.php');
        exit();
    }

    // Create account using RegisterModel
    try {
        $registerModel = new RegisterModel($data);
        $registerModel->createAccount($conn);
        $_SESSION['successMessage'] = 'Registration successful! Your username is ' . $data['username'] . ' and password is ' . $password;
        header('Location: ../public/views/login.php');
        exit();
    } catch (Exception $e) {
        error_log("Registration error: " . $e->getMessage());
        $_SESSION['errorMessages'] = ['Registration failed: ' . $e->getMessage()];
        $_SESSION['form_data'] = array_merge($_POST, [
            'id_image' => isset($_FILES['id_image']) && $_FILES['id_image']['error'] == UPLOAD_ERR_OK ? $_FILES['id_image']['name'] : ($_SESSION['form_data']['id_image'] ?? ''),
            'guided_by_id' => isset($_FILES['guided_by_id']) && $_FILES['guided_by_id']['error'] == UPLOAD_ERR_OK ? $_FILES['guided_by_id']['name'] : ($_SESSION['form_data']['guided_by_id'] ?? '')
        ]);
        header('Location: ../public/views/register.php');
        exit();
    } finally {
        $conn->close();
    }
} else {
    $_SESSION['errorMessages'] = ['Invalid request method.'];
    header('Location: ../public/views/register.php');
    exit();
}
?>