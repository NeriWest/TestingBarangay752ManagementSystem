<?php
require_once __DIR__ . '../../../../../../vendor/autoload.php';
require '../../../../../model/admin/adminResidentModel.php';


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\{Border, Fill, Alignment, Font, Color};
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

$adminResidentModel = new AdminResidentModel();
$result = $adminResidentModel->GETtotalAccounts($conn);
$accounts = $result['data']; // Get the actual accounts data

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Match headers with PDF version (5 columns)
$headers = [
    'Account ID',       // A
    'Username',         // B
    'Status',           // C
    'Email',            // D
    'Date Registered'   // E
];

// Style only the columns we're using (A-E)
$sheet->getStyle('A1:E1')->applyFromArray([
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

$sheet->fromArray($headers, NULL, 'A1');

$rowNumber = 2;
foreach ($accounts as $account) {
    $sheet->setCellValue('A' . $rowNumber, $account['account_id'])
          ->setCellValue('B' . $rowNumber, $account['username'])
          ->setCellValue('C' . $rowNumber, $account['status'])
          ->setCellValue('D' . $rowNumber, $account['email'])
          ->setCellValue('E' . $rowNumber, $account['date_registered']);
    $rowNumber++;
}

// Auto-size only used columns
foreach (range('A', 'E') as $column) {
    $sheet->getColumnDimension($column)->setAutoSize(true);
}

// Apply borders to actual data range
$sheet->getStyle('A2:E' . ($rowNumber-1))->applyFromArray([
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

$writer = new Xlsx($spreadsheet);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Total_Accounts_Export_('.date('m-d-Y').').xlsx"');
header('Cache-Control: max-age=0');
$writer->save('php://output');
exit();