    <?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    require_once __DIR__ . '/../dbconnection.php';

    function updateTemplateName($templateId, $newName) {
        $db = new dbcon();
        $conn = $db->getConnection();
        $sql = "UPDATE templates SET doc_name = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $newName, $templateId);
        $result = $stmt->execute();
        $stmt->close();
        $conn->close();
        return $result ? ["success" => true] : ["success" => false, "message" => "Failed to update template name"];
    }

    function updateTemplateData($templateId, $newName = null, $documentPath = null, $price = null, $priceEnabled = null) {
        $db = new dbcon();
        $conn = $db->getConnection();

        // Dynamically build SQL
        $fields = [];
        $params = [];
        $types = "";

        if ($newName !== null) {
            $fields[] = "doc_name = ?";
            $params[] = $newName;
            $types .= "s";
        }

        if ($documentPath !== null) {
            $fields[] = "template_document = ?";
            $params[] = $documentPath;
            $types .= "s";
        }

        if ($price !== null) {
            $fields[] = "price = ?";
            $params[] = $price;
            $types .= "d";
        }

        if ($priceEnabled !== null) {
            $fields[] = "price_enabled = ?";
            $params[] = $priceEnabled;
            $types .= "i";
        }

        if (empty($fields)) {
            return ["success" => false, "message" => "No data to update"];
        }

        $sql = "UPDATE templates SET " . implode(", ", $fields) . " WHERE id = ?";
        $params[] = $templateId;
        $types .= "i";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            return ["success" => false, "message" => "SQL prepare error: " . $conn->error];
        }

        $stmt->bind_param($types, ...$params);
        $success = $stmt->execute();

        $stmt->close();
        $conn->close();

        return $success ? ["success" => true] : ["success" => false, "message" => "Update failed: " . $stmt->error];
    }

    function insertNewTemplate($docName, $templateType, $templateImage, $templateDocument, $price, $priceEnabled) {
        $db = new dbcon();
        $conn = $db->getConnection();
        $sql = "INSERT INTO templates (doc_name, template_type, template_image, template_document, price, price_enabled, date_added)
                VALUES (?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            return ["success" => false, "message" => "Error preparing statement: " . $conn->error];
        }
        $stmt->bind_param("ssssdi", $docName, $templateType, $templateImage, $templateDocument, $price, $priceEnabled);
        if ($stmt->execute()) {
            $templateId = $conn->insert_id;
            $stmt->close();
            $conn->close();
            return ["success" => true, "template_id" => $templateId];
        } else {
            return ["success" => false, "message" => "Failed to insert template: " . $stmt->error];
        }
    }

    function getOfficialsByPosition() {
        $db = new dbcon();
        $conn = $db->getConnection();

        $sql = "SELECT o.position, r.first_name, r.middle_name, r.last_name, r.suffix
                FROM officials o
                JOIN residents r ON o.resident_id = r.resident_id
                WHERE o.status = 'active'
                ORDER BY o.position";

        $result = $conn->query($sql);
        $officials = [
            'Punong Barangay' => [],
            'Sangguniang Barangay Member' => [],
            'Secretary' => [],
            'Treasurer' => [],
            'SK Chairperson' => [],
            'SK Kagawad' => [],
            'SK Secretary' => [],
            'SK Treasurer' => []
        ];

        while ($row = $result->fetch_assoc()) {
            $firstName = strtoupper($row['first_name']);
            $middleName = strtoupper($row['middle_name'][0]) . '.';
            $lastName = strtoupper($row['last_name']);
            $suffix = strtoupper($row['suffix']);

            $fullName = trim($firstName . ' ' . $middleName . ' ' . $lastName);
            if ($suffix && $suffix !== 'N/A') {
                $fullName .= ' ' . $suffix;
            }

            $position = $row['position'];
            if (array_key_exists($position, $officials)) {
                $officials[$position][] = $fullName;
            }
        }

        $conn->close();
        return $officials;
    }

    function getTemplateById($templateId) {
        $db = new dbcon();
        $conn = $db->getConnection();

        $sql = "SELECT doc_name FROM templates WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $templateId);
        $stmt->execute();
        $result = $stmt->get_result();

        $template = $result->fetch_assoc();
        $stmt->close();
        $conn->close();

        return $template;
    }
    ?>