<?php
require_once __DIR__ . '/../../../../../vendor/autoload.php';
require __DIR__ . '/../../../../../model/admin/adminResidentModel.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\{Border, Fill, Alignment, Font, Color};
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Cell\DataType;


session_start();

// Initialize model and fetch data
$adminResidentModel = new AdminResidentModel();
$residents = $adminResidentModel->totalResidents($conn);

// Create spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set document properties
$spreadsheet->getProperties()
    ->setCreator($_SESSION['first_name'].' '.$_SESSION['last_name'])
    ->setTitle('Residents Export - '.date('F j, Y'))
    ->setDescription('Complete list of residents');

// Define headers with proper column widths
$headers = [
    'Last Name' => 20,
    'First Name' => 20,
    'Middle Name' => 20,
    'Suffix' => 10,
    'Mobile No.' => 15,
    'Sex' => 8,
    'Age' => 8,
    'Address' => 40,
    'Date of Birth' => 15,
    'Civil Status' => 15,
    'House No.' => 12,
    'Street' => 25,
    'Citizenship' => 20,
    'Email' => 30,
    'Occupation' => 25,
    'Disability' => 20,
    'Status' => 15,
    'Date Registered' => 20
];

// Write headers with styling
$column = 'A';
foreach ($headers as $header => $width) {
    $sheet->setCellValue($column.'1', $header)
          ->getColumnDimension($column)->setWidth($width);
    $column++;
}

// Apply header styling
$sheet->getStyle('A1:R1')->applyFromArray([
    'font' => [
        'bold' => true,
        'color' => ['rgb' => 'FFFFFF']
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'color' => ['rgb' => '404040']
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['rgb' => '000000']
        ]
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER
    ]
]);

// Populate data with alternate row coloring
$rowNumber = 2;
foreach ($residents as $resident) {
    $address = trim(($resident['house_number'] ?? '') . " " . ($resident['street'] ?? ''));
    $address = $address ?: 'N/A';

    $sheet->setCellValueExplicit('A'.$rowNumber, $resident['last_name'] ?? 'N/A', DataType::TYPE_STRING)
          ->setCellValueExplicit('B'.$rowNumber, $resident['first_name'] ?? 'N/A', DataType::TYPE_STRING)
          ->setCellValueExplicit('C'.$rowNumber, $resident['middle_name'] ?? 'N/A', DataType::TYPE_STRING)
          ->setCellValueExplicit('D'.$rowNumber, !empty($resident['suffix']) ? $resident['suffix'] : 'N/A', DataType::TYPE_STRING) // Handle Suffix
          ->setCellValueExplicit('E'.$rowNumber, $resident['cellphone_number'] ?? 'N/A', DataType::TYPE_STRING)
          ->setCellValueExplicit('F'.$rowNumber, $resident['sex'] ?? 'N/A', DataType::TYPE_STRING)
          ->setCellValueExplicit('G'.$rowNumber, $resident['age'] ?? 'N/A', DataType::TYPE_STRING)
          ->setCellValueExplicit('H'.$rowNumber, $address, DataType::TYPE_STRING)
          ->setCellValueExplicit('I'.$rowNumber, date('m/j/Y', strtotime($resident['date_of_birth'])) ?? 'N/A', DataType::TYPE_STRING)
          ->setCellValueExplicit('J'.$rowNumber, $resident['civil_status'] ?? 'N/A', DataType::TYPE_STRING)
          ->setCellValueExplicit('K'.$rowNumber, $resident['house_number'] ?? 'N/A', DataType::TYPE_STRING)
          ->setCellValueExplicit('L'.$rowNumber, $resident['street'] ?? 'N/A', DataType::TYPE_STRING)
          ->setCellValueExplicit('M'.$rowNumber, $resident['citizenship'] ?? 'N/A', DataType::TYPE_STRING)
          ->setCellValueExplicit('N'.$rowNumber, !empty($resident['email']) ? $resident['email'] : 'N/A', DataType::TYPE_STRING) // Handle Email
          ->setCellValueExplicit('O'.$rowNumber, $resident['occupation'] ?? 'N/A', DataType::TYPE_STRING)
          ->setCellValueExplicit('P'.$rowNumber, $resident['disability'] ?? 'N/A', DataType::TYPE_STRING)
          ->setCellValueExplicit('Q'.$rowNumber, $resident['status'] ?? 'N/A', DataType::TYPE_STRING)
            ->setCellValueExplicit('R'.$rowNumber, date('m/j/Y g:i A', strtotime($resident['date_registered'])) ?? 'N/A', DataType::TYPE_STRING); // Handle Date Registered

    // Alternate row colors
    $fillColor = ($rowNumber % 2) ? 'FFFFFF' : 'F0F0F0';
    $sheet->getStyle('A'.$rowNumber.':R'.$rowNumber)->applyFromArray([
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'color' => ['rgb' => $fillColor]
        ]
    ]);

    $rowNumber++;
}

// Apply data styling
$sheet->getStyle('A2:R'.($rowNumber-1))->applyFromArray([
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['rgb' => '000000']
        ]
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_LEFT, // Ensure left alignment
        'vertical' => Alignment::VERTICAL_CENTER
    ]
]);

// Format specific columns
$sheet->getStyle('I2:I'.($rowNumber-1)) // Date of Birth
    ->getNumberFormat()
    ->setFormatCode('yyyy-mm-dd');

// Set page layout
$sheet->getPageSetup()
    ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
    ->setFitToWidth(1)
    ->setFitToHeight(0);

// Add header/footer
$sheet->getHeaderFooter()
    ->setOddHeader('&C&16&"Arial,Bold"RESIDENTS MASTER LIST')
    ->setOddFooter('&LExported on '.date('Y-m-d H:i').'&RPage &P of &N');

// Freeze header row
$sheet->freezePane('A2');

// Generate and output
$writer = new Xlsx($spreadsheet);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="New_Residents_Excel_Export_('.date('m-d-Y').').xlsx"');
header('Cache-Control: max-age=0');
$writer->save('php://output');
exit;