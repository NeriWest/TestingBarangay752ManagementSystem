<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
ob_start();
require_once __DIR__ . '/../../../../../vendor/autoload.php';
require __DIR__ . '/../../../../../model/admin/adminFamilyModel.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\{Border, Fill, Alignment, Font, Color};
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

try {
    // Initialize
    $adminFamilyModel = new AdminFamilyModel();

    // Set pagination parameters
    $limit = 20;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $limit;

    // Get paginated families data
    $families = $adminFamilyModel->showFamiliesWithPagination($conn, $offset, $limit);

    // Create spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Set headers with styling
    $headers = [
        'Family ID',
        'Family Name',
        'Address',
        'Family Members',
        'Total Income',
        'Date Created'
    ];

    // Write headers with styling
    $sheet->fromArray($headers, NULL, 'A1');
    $sheet->getStyle('A1:F1')->applyFromArray([
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

    // Populate data
    $rowNumber = 2;
    foreach ($families as $family) {
        $address = $family['house_number'] . " " . $family['street'] . " Street, Singalong, Malate, Manila";

        // Clean up and format family members list
        $familyMembers = $family['residents'] ?
            str_replace([", ", "<br>", "<br/>"], "\n", $family['residents']) :
            'No members';

        // Set values individually for better control
        $sheet->setCellValue('A' . $rowNumber, $family['family_id']);
        $sheet->setCellValue('B' . $rowNumber, $family['family_name']);
        $sheet->setCellValue('C' . $rowNumber, $address);

        // For Family Members - use explicit line breaks
        $sheet->setCellValue('D' . $rowNumber, $familyMembers);
        $sheet->getStyle('D' . $rowNumber)->getAlignment()->setWrapText(true);
        $sheet->setCellValue('E' . $rowNumber, $family['total_income'] ?? 0);

        // Format date column
        $sheet->setCellValue('F' . $rowNumber, date('m/j/Y g:i A', strtotime($family['created_at'])));        $sheet->getStyle('F' . $rowNumber)
            ->getNumberFormat()
            ->setFormatCode('yyyy-mm-dd');
        // Alternate row colors
        $fillColor = ($rowNumber % 2) ? 'FFFFFF' : 'F0F0F0';
        $sheet->getStyle('A' . $rowNumber . ':F' . $rowNumber)->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => $fillColor]
            ]
        ]);
        $rowNumber++;
    }

    // Apply data styling
    $sheet->getStyle('A2:F' . ($rowNumber - 1))->applyFromArray([
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

    // Set column widths
    $sheet->getColumnDimension('A')->setWidth(15);  // Family ID
    $sheet->getColumnDimension('B')->setWidth(25);  // Family Name
    $sheet->getColumnDimension('C')->setWidth(40);  // Address (wider)
    $sheet->getColumnDimension('D')->setWidth(60);  // Family Members (widest)
    $sheet->getColumnDimension('E')->setWidth(20);  // Date Created
    $sheet->getColumnDimension('F')->setWidth(20);  // Date Created

    // Enable text wrapping for Address and Family Members
    $sheet->getStyle('C2:C' . ($rowNumber - 1))->getAlignment()->setWrapText(true);
    $sheet->getStyle('D2:D' . ($rowNumber - 1))->getAlignment()->setWrapText(true);

    // Freeze header row
    $sheet->freezePane('A2');
    ob_end_clean();
    // Generate and output
    $writer = new Xlsx($spreadsheet);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Family_Excel_Export_('.date('m-d-Y').').xlsx"');
    header('Cache-Control: max-age=0');
    $writer->save('php://output');
} catch (Exception $e) {
    die('Error generating Excel file: ' . $e->getMessage());
}

exit();
