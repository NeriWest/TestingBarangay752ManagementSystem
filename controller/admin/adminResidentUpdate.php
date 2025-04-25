<?php

require '../../model/admin/adminResidentModel.php';
require '../../model/admin/adminActivityLogModel.php';


// Call the resident model
$adminResidentModel = new adminResidentModel();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and sanitize form inputs
    $residentId = isset($_POST['resident_id']) ? intval($_POST['resident_id']) : 0;
    $lastName = trim(stripslashes(htmlspecialchars($_POST['last-name'])));
    $firstName = trim(stripslashes(htmlspecialchars($_POST['first-name'])));
    $middleName = trim(stripslashes(htmlspecialchars($_POST['middle-name'])));
    $suffix = trim(stripslashes(htmlspecialchars($_POST['suffix'])));
    $phoneNumber = trim(stripslashes(htmlspecialchars($_POST['phonenumber'])));
    $sex = trim(stripslashes(htmlspecialchars($_POST['sex'])));
    $dateOfBirth = trim(stripslashes(htmlspecialchars($_POST['date-of-birth'])));
    $civilStatus = trim(stripslashes(htmlspecialchars($_POST['civil-status'])));
    $houseNumber = trim(stripslashes(htmlspecialchars($_POST['house-number'])));
    $street = trim(stripslashes(htmlspecialchars($_POST['street'])));
    $occupation = trim(stripslashes(htmlspecialchars($_POST['occupation'])));
    $disability = trim(stripslashes(htmlspecialchars($_POST['disability'])));
    $citizenship = trim(stripslashes(htmlspecialchars($_POST['citizenship'])));
    $email = trim(stripslashes(htmlspecialchars($_POST['email'])));
    $voterStatus = trim(stripslashes(htmlspecialchars($_POST['voter_status'])));
    $oscaId = trim(stripslashes(htmlspecialchars($_POST['osca-id'])));
    $oscaIdIssued = trim(stripslashes(htmlspecialchars($_POST['osca-id-issued'])));
    $status = trim(stripslashes(htmlspecialchars($_POST['status'])));
    $accountId = intval($_POST['account_id']); // Assuming account_id is an integer


    $residentData = [
        'first_name' => $firstName,
        'middle_name' => $middleName,
        'last_name' => $lastName,
        'suffix' => $suffix,
        'date_of_birth' => $dateOfBirth,
        'cellphone_number' => $phoneNumber,
        'house_number' => $houseNumber,
        'street' => $street,
        'email' => $email,
        'occupation' => $occupation,
        'sex' => $sex,
        'civil_status' => $civilStatus,
        'disability' => $disability,
        'resident_id' => $residentId,
        'citizenship' => $citizenship,
        'voter_status' => $voterStatus,
        'osca_id' => $oscaId,
        'osca_date_issued' => $oscaIdIssued,
        'status' => $status
    ];

    if ($adminResidentModel->updateResident($conn, $residentData)) {
        $adminActivityLogModel = new ActivityLogModel();
        $module = "Resident Management";
        $activity = "Updated a resident with the name " . $residentData['first_name'] . " " . $residentData['last_name'] . ".";
        $description = "Name: " . $residentData['first_name'] . " " . $residentData['last_name'] . ", Address: " . $residentData['house_number'] . " " . $residentData['street'] . ", Contact: " . $residentData['cellphone_number'];
        $adminActivityLogModel->recordActivityLog($conn, $accountId, $module, $activity, $description);
        session_start();
        $_SESSION['message'] = "The resident named " . $residentData['first_name'] . " has been successfully updated.";
        header('Location: adminResidentController.php');
    } else {
        echo "Failed to update resident information: " . $conn->error;
    }
}
?>
