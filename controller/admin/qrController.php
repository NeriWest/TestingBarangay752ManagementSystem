<?php
require_once __DIR__ . '/../../model/admin/qrModel.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($data['action'])) {
        if ($data['action'] === 'add' && isset($data['name'])) {
            $name = trim($data['name']);
            $result = addPaymentType($name);
            echo json_encode($result);
            exit;
        } elseif ($data['action'] === 'delete' && isset($data['paymentId'])) {
            $paymentId = intval($data['paymentId']);
            $result = deletePaymentType($paymentId);
            echo json_encode($result);
            exit;
        }
    }

    if (isset($_POST['paymentId'])) {
        $paymentId = intval($_POST['paymentId']);
        $accountName = trim($_POST['account_name'] ?? '');

        if (empty($accountName)) {
            echo json_encode(["success" => false, "message" => "Account name cannot be empty."]);
            exit;
        }

        $uploadDir = __DIR__ . "/../../img/uploads/";
        $relativePath = null;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (!empty($_FILES['payment_image']['name']) && $_FILES['payment_image']['error'] === UPLOAD_ERR_OK) {
            $qr = "payment_{$paymentId}_" . time() . "_" . basename($_FILES['payment_image']['name']);
            $qr = preg_replace('/\s+/', '_', $qr);
            $targetFile = $uploadDir . $qr;

            if (move_uploaded_file($_FILES['payment_image']['tmp_name'], $targetFile)) {
                $relativePath = "img/uploads/" . $qr;
            } else {
                echo json_encode(["success" => false, "message" => "File upload failed."]);
                exit;
            }
        }

        $updateResult = updatePaymentQR($paymentId, $relativePath, $accountName);

        if ($updateResult["success"]) {
            echo json_encode([
                "success" => true,
                "message" => "Payment details updated successfully!",
                "new_qr_photo" => "/Barangay752ManagementSystem/" . $relativePath
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => $updateResult["message"]
            ]);
        }
    }
}
exit;
?>
