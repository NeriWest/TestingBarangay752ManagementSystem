<?php
class tempFetch {
    public function fetchTemp() {
        require_once '../../../model/dbconnection.php';

        $db = new dbcon();
        $conn = $db->getConnection();

        if (!$conn) {
            die("Database connection failed!");
        }

        $tables = ['certificates', 'permits', 'clearances'];
        $data = []; 

        foreach ($tables as $table) {
            $sql = "SELECT doc_name, template_image FROM $table";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $imagePath = !empty($row['template_image'])
                    ? "/Barangay752ManagementSystem/img/uploads/" . basename($row['template_image'])
                    : "/Barangay752ManagementSystem/img/uploads/image.png";

                $data[$table][$row['doc_name']] = $imagePath;
            }
        }
    }
}

$conn->close();
return $data;
?>