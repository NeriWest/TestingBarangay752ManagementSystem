<?php
// For PHP 7.2-7.4 (XAMPP default)
require_once __DIR__ . '/../../../vendor/autoload.php';

// Call the resident model
require '../../../model/admin/adminResidentModel.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Initialize
$adminResidentModel = new AdminResidentModel();
$residents = $adminResidentModel->showResidents($conn);

// Create spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set headers with styling
$headers = [
    'Last Name', 'First Name', 'Middle Name', 'Suffix', 'Mobile No.', 
    'Sex', 'Age', 'Address', 'Date of Birth', 'Civil Status',
    'House No.', 'Street', 'Citizenship', 'Email', 
    'Occupation', 'Disability', 'Status'
];

// Add header styling
$sheet->getStyle('A1:Q1')->applyFromArray([
    'font' => ['bold' => true],
    'fill' => [
        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        'color' => ['rgb' => 'D9D9D9']
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
        ]
    ]
]);

// Write headers
$sheet->fromArray($headers, NULL, 'A1');

// Populate data
$rowNumber = 2;
foreach ($residents as $resident) {
    $address = $resident['house_number'] . " " . $resident['street'] . " Singalong, Malate, Manila";
    
    $sheet->setCellValue('A' . $rowNumber, $resident['last_name'])
          ->setCellValue('B' . $rowNumber, $resident['first_name'])
          ->setCellValue('C' . $rowNumber, $resident['middle_name'])
          ->setCellValue('D' . $rowNumber, $resident['suffix'])
          ->setCellValue('E' . $rowNumber, $resident['cellphone_number'])
          ->setCellValue('F' . $rowNumber, $resident['sex'])
          ->setCellValue('G' . $rowNumber, $resident['age'])
          ->setCellValue('H' . $rowNumber, $address)
          ->setCellValue('I' . $rowNumber, $resident['date_of_birth'])
          ->setCellValue('J' . $rowNumber, $resident['civil_status'])
          ->setCellValue('K' . $rowNumber, $resident['house_number'])
          ->setCellValue('L' . $rowNumber, $resident['street'])
          ->setCellValue('M' . $rowNumber, $resident['citizenship'])
          ->setCellValue('N' . $rowNumber, $resident['email'])
          ->setCellValue('O' . $rowNumber, $resident['occupation'])
          ->setCellValue('P' . $rowNumber, $resident['disability'])
          ->setCellValue('Q' . $rowNumber, $resident['status']);
    
    $rowNumber++;
}

// Auto-size columns
foreach (range('A', 'Q') as $column) {
    $sheet->getColumnDimension($column)->setAutoSize(true);
}

// Add data borders
$sheet->getStyle('A2:Q' . ($rowNumber-1))->applyFromArray([
    'borders' => [
        'allBorders' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
        ]
    ]
]);

// Generate and output
$writer = new Xlsx($spreadsheet);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Residents_Export_' . date('Y-m-d') . '.xlsx"');
header('Cache-Control: max-age=0');
$writer->save('php://output');
exit();