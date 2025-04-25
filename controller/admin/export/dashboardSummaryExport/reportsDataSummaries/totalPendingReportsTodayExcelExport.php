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
$requests = $adminResidentModel->GETtotalPendingReportsToday($conn, 0, 0);
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
    'Case Number',
    'Report Type',
    'Complainant',
    'Complainant Type',
    'Subject',
    'Official',
    'Date of Incident',
    'Time of Incident',
    'Location',
    'Respondent',
    'Narration',
    'Evidence Description',
    'Remarks',
    'Status',
    'Date Submitted',
];

// Apply header styles (similar to PDF)
$sheet->fromArray($headers, null, 'A1');
$sheet->getStyle('A1:O1')->applyFromArray([
    'font' => [
        'bold' => true,
        'color' => ['rgb' => 'FFFFFF'] // Change font color to white
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'color' => ['rgb' => '404040'] // Dark gray like PDF
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN
        ]
    ],
    'alignment' => [
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
    ]
]);

// Set column widths (matching PDF proportions)
$sheet->getColumnDimension('A')->setWidth(15); // Case Number
$sheet->getColumnDimension('B')->setWidth(15); // Complainant
$sheet->getColumnDimension('C')->setWidth(30); // Complainant Type
$sheet->getColumnDimension('D')->setWidth(20); // Subject
$sheet->getColumnDimension('E')->setWidth(30); // Official
$sheet->getColumnDimension('F')->setWidth(25); // Date of Incident
$sheet->getColumnDimension('G')->setWidth(15); // Time of Incident
$sheet->getColumnDimension('H')->setWidth(15); // Payment Amount
$sheet->getColumnDimension('I')->setWidth(15); // No. Of Copies
$sheet->getColumnDimension('J')->setWidth(15); // Status
$sheet->getColumnDimension('K')->setWidth(50); // Status
$sheet->getColumnDimension('L')->setWidth(20); // Status
$sheet->getColumnDimension('M')->setWidth(15); // Status
$sheet->getColumnDimension('N')->setWidth(15); // Status
$sheet->getColumnDimension('O')->setWidth(20); // Status

// Add data rows
$rowNumber = 2;
foreach ($requests as $request) {
    $sheet->fromArray([
        $request['Case_number'] ?? 'N/A',
        $request['record_type'] ?? 'N/A',
        $request['complainant'] ?? 'N/A',
        $request['complainant_type'] ?? 'N/A',
        $request['subject'] ?? 'N/A',
        $request['official_name'] ?? 'N/A',
        $request['date_of_incident'] ?? 'N/A',
        $request['time_of_incident'] ?? 'N/A',
        $request['location'] ?? 'N/A',
        $request['Respondent'] ?? 'N/A',
        $request['narration'] ?? 'N/A',
        $request['evidence_description'] ?? 'N/A',
        $request['remarks'] ?? 'N/A',
        $request['status'] ?? 'N/A',
        $request['date_submitted'] ?? 'N/A'
    ], null, 'A'.$rowNumber);
    
    // Alternate row colors like PDF
    $fillColor = ($rowNumber % 2) ? 'FFFFFF' : 'F0F0F0';
    $sheet->getStyle('A'.$rowNumber.':O'.$rowNumber)->applyFromArray([
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'color' => ['rgb' => $fillColor]
        ]
    ]);
    
    $rowNumber++;
}

// Apply borders to all data cells
$sheet->getStyle('A2:O'.($rowNumber-1))->applyFromArray([
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
header('Content-Disposition: attachment;filename="Total_Pending_Reports_Today_Excel_Export_('.date('m-d-Y').').xlsx"');
header('Cache-Control: max-age=0');
$writer->save('php://output');
exit;