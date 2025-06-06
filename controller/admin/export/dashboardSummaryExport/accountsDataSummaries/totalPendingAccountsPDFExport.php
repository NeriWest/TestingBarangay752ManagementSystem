<?php
require '../../../../../libraries/fpdf186/fpdf.php';
require '../../../../../model/admin/adminResidentModel.php';
require '../../PDFExportDesign.php';
session_start();

// Extend FPDF class to add headers and footers
class PDFWithHeaderFooter extends FPDF {
    private $tableHeaders;
    private $startX;

    function Header() {
        // Logos
        $this->Image('../../../../../img/Barangay Logo.png', 68, 5, 20);
        $this->Image('../../../../../img/lunsgodNgMayNilaLogo.png', 210, 5, 20);

        // Title
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, 'TOTAL PENDING ACCOUNTS LIST REPORT', 0, 1, 'C');

        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 6, 'Generated on: ' . date('F j, Y'), 0, 1, 'C');
        $this->Cell(0, 6, 'Generated by: ' . htmlspecialchars($_SESSION['last_name']) . ', ' . htmlspecialchars($_SESSION['first_name']), 0, 1, 'C');
        $this->Cell(0, 6, 'Total Pending Accounts: ' . $GLOBALS['totalAccounts'], 0, 1, 'C');
        $this->Ln(8);

        // Table Header
        $this->drawTableHeader();
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
        $this->Line(10, $this->GetY()-2, $this->GetPageWidth()-10, $this->GetY()-2);
    }

    function setTableHeaders($headers, $startX) {
        $this->tableHeaders = $headers;
        $this->startX = $startX;
    }

    function drawTableHeader() {
        $this->SetFont('Arial', 'B', 8);
        $this->SetFillColor(64, 64, 64);
        $this->SetTextColor(255);
        $this->SetX($this->startX);
        $maxHeight = 10;
        $paddingTop = 2;

        foreach ($this->tableHeaders as $header) {
            $x = $this->GetX();
            $y = $this->GetY();
            $this->Rect($x, $y, $header['width'], $maxHeight, 'DF');
            $this->SetXY($x, $y + $paddingTop);
            $this->MultiCell($header['width'], 4, $header['title'], 0, 'C', false);
            $this->SetXY($x + $header['width'], $y);
        }

        $this->Ln($maxHeight);
    }
}

// Fetch account data
$adminResidentModel = new AdminResidentModel();
$result = $adminResidentModel->GETtotalPendingAccounts($conn);
$accounts = $result['data'];
$GLOBALS['totalAccounts'] = count($accounts);
$conn->close();

// Setup headers
$headers = [
    ['width' => 20, 'title' => 'Account ID'],
    ['width' => 40, 'title' => 'Username'],
    ['width' => 25, 'title' => 'Status'],
    ['width' => 80, 'title' => 'Email'],
    ['width' => 40, 'title' => 'Date Registered']
];

$totalWidth = array_sum(array_column($headers, 'width'));
$pdf = new PDFWithHeaderFooter('L', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->SetAutoPageBreak(true, 25);
$pageWidth = $pdf->GetPageWidth();
$startX = ($pageWidth - $totalWidth) / 2;
$pdf->setTableHeaders($headers, $startX);
$pdf->AddPage();

// Render table
$pdf->SetFont('Arial', '', 8);
$pdf->SetTextColor(0);
$fill = false;

foreach ($accounts as $account) {
    if ($pdf->GetY() > 170) {
        $pdf->AddPage();
        $fill = false;
    }

    $pdf->SetX($startX);
    $cells = [
        $account['account_id'],
        $account['username'],
        $account['status'],
        $account['email'],
        $account['date_registered']
    ];

    $maxHeight = 6;
    foreach ($cells as $i => $content) {
        $numLines = $pdf->GetStringWidth($content) > 0 ? ceil($pdf->GetStringWidth($content) / $headers[$i]['width']) : 1;
        $maxHeight = max($maxHeight, $numLines * 4);
    }

    foreach ($headers as $i => $header) {
        $x = $pdf->GetX();
        $y = $pdf->GetY();

        $pdf->SetFillColor($fill ? 240 : 255);
        $pdf->Rect($x, $y, $header['width'], $maxHeight, 'F');

        $pdf->SetXY($x, $y + 2);
        $pdf->MultiCell($header['width'], 4, $cells[$i], 0, 'C', false);
        $pdf->SetXY($x + $header['width'], $y);
        $pdf->Rect($x, $y, $header['width'], $maxHeight);
    }

    $pdf->Ln($maxHeight);
    $fill = !$fill;
}

// Output
$pdf->Output('Accounts_List_' . date('Y-m-d') . '.pdf', 'I');
