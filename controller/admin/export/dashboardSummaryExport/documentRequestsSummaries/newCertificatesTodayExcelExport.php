<?php
require_once __DIR__ . '../../../../../../vendor/autoload.php';
require '../../../../../model/admin/adminResidentModel.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\{Border, Fill, Alignment, Font, Color};
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Cell\DataType;


session_start();

// Fetch data (same as PDF)
$adminResidentModel = new AdminResidentModel();
$requests = $adminResidentModel->GETcertificatesToday($conn, 0, 0);
$totalRequestsToday = is_array($requests) ? count($requests) : 0;
$conn->close();

// Create spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set document properties (similar to PDF header)
$spreadsheet->getProperties()
    ->setCreator($_SESSION['first_name'].' '.$_SESSION['last_name'])
    ->setTitle('Requests Today List - '.date('F j, Y'))
    ->setDescription('Export of all requests made today');

// Setup headers (matching PDF columns)
$headers = [
    'Request ID',
    'Type of Document',
    'Requestor',
    'Purpose',
    'Date Submitted',
    'Last Updated',
    'Type of Payment',
    'Payment Amount',
    'No. Of Copies',
    'Status'
];

// Apply header styles (similar to PDF)
$sheet->fromArray($headers, null, 'A1');
$sheet->getStyle('A1:J1')->applyFromArray([
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

// Set column widths (matching PDF proportions)
$sheet->getColumnDimension('A')->setWidth(15); // Request ID
$sheet->getColumnDimension('B')->setWidth(35); // Type of Document
$sheet->getColumnDimension('C')->setWidth(25); // Requestor
$sheet->getColumnDimension('D')->setWidth(40); // Purpose
$sheet->getColumnDimension('E')->setWidth(20); // Date Submitted
$sheet->getColumnDimension('F')->setWidth(20); // Last Updated
$sheet->getColumnDimension('G')->setWidth(20); // Type of Payment
$sheet->getColumnDimension('H')->setWidth(15); // Payment Amount
$sheet->getColumnDimension('I')->setWidth(15); // No. Of Copies
$sheet->getColumnDimension('J')->setWidth(15); // Status

// Add data rows
$rowNumber = 2;
foreach ($requests as $request) {
    $sheet->fromArray([
        $request['request_id'] ?? 'N/A',
        $request['template_name'] ?? 'N/A',
        $request['full_name'] ?? 'N/A',
        $request['purpose'] ?? 'N/A',
        $request['date_submitted'] ?? 'N/A',
        $request['last_updated'] ?? 'N/A',
        $request['payment_name'] ?? 'N/A',
        $request['payment_amount'] ?? 'N/A',
        $request['number_of_copies'] ?? 'N/A',
        $request['status'] ?? 'N/A'
    ], null, 'A'.$rowNumber);
    
    // Alternate row colors like PDF
    $fillColor = ($rowNumber % 2) ? 'FFFFFF' : 'F0F0F0';
    $sheet->getStyle('A'.$rowNumber.':J'.$rowNumber)->applyFromArray([
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'color' => ['rgb' => $fillColor]
        ]
    ]);
    
    $rowNumber++;
}

// Apply borders to all data cells
$sheet->getStyle('A2:J'.($rowNumber-1))->applyFromArray([
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

// Format dates
$sheet->getStyle('E2:E'.$rowNumber)
    ->getNumberFormat()
    ->setFormatCode('mm/dd/yyyy');
$sheet->getStyle('F2:F'.$rowNumber)
    ->getNumberFormat()
    ->setFormatCode('mm/dd/yyyy');

// Format currency
$sheet->getStyle('H2:H'.$rowNumber)
    ->getNumberFormat()
    ->setFormatCode('#,##0.00');

// Set page layout (landscape like PDF)
$sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
$sheet->getPageSetup()->setFitToWidth(1);

// Add header/footer like PDF
$sheet->getHeaderFooter()
    ->setOddHeader('&C&16&"Arial,Bold"REQUESTS TODAY LIST')
    ->setOddFooter('&RPage &P of &N');

// Output the file
$writer = new Xlsx($spreadsheet);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="New_Clearances_Today_Excel_Export_('.date('m-d-Y').').xlsx"');
header('Cache-Control: max-age=0');
$writer->save('php://output');
exit;