<?php
require '../../../libraries/fpdf186/fpdf.php'; // Include FPDF
require '../../../model/admin/adminResidentModel.php';

function getAddress($houseNumber, $street)
{
    $address = $houseNumber . " " .  $street . " Singalong, Malate, Manila";
    return $address;
}

// Call the resident model
$adminResidentModel = new AdminResidentModel();

// Get the residents data (for displaying the resident list on the same page)
$residents = $adminResidentModel->showResidents($conn);

// Create a new FPDF object in landscape mode
$pdf = new FPDF('L', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 7); // Set smaller font size for better fit

// Table headers
$pdf->Cell(15, 7, 'Last Name', 1);
$pdf->Cell(15, 7, 'First Name', 1);
$pdf->Cell(17, 7, 'Middle Name', 1);
$pdf->Cell(10, 7, 'Suffix', 1);
$pdf->Cell(20, 7, 'Mobile No.', 1);
$pdf->Cell(10, 7, 'Sex', 1);
$pdf->Cell(10, 7, 'Age', 1);
$pdf->Cell(30, 7, 'Address', 1);
$pdf->Cell(15, 7, 'Date of Birth', 1);
$pdf->Cell(15, 7, 'Civil Status', 1);
$pdf->Cell(14, 7, 'House No.', 1);
$pdf->Cell(15, 7, 'Street', 1);
$pdf->Cell(15, 7, 'Citizenship', 1);
$pdf->Cell(15, 7, 'Email', 1);
$pdf->Cell(20, 7, 'Occupation', 1);
$pdf->Cell(15, 7, 'Disability', 1);
$pdf->Cell(15, 7, 'Voter Status', 1);
$pdf->Cell(15, 7, 'Status', 1);
$pdf->Ln(); // New line

// Table body
$pdf->SetFont('Arial', '', 5); // Smaller font for content
foreach ($residents as $resident) {
    // Save the initial Y position
    $startY = $pdf->GetY();
    $maxHeight = 6; // Default height for the row
    
    // Adjust row height for the rest of the cells
    $pdf->Cell(15, $maxHeight, $resident['last_name'], 1);
    $pdf->Cell(15, $maxHeight, $resident['first_name'], 1);
    $pdf->Cell(17, $maxHeight, $resident['middle_name'], 1);
    $pdf->Cell(10, $maxHeight, $resident['suffix'], 1);
    $pdf->Cell(20, $maxHeight, $resident['cellphone_number'], 1);
    $pdf->Cell(10, $maxHeight, $resident['sex'], 1);
    $pdf->Cell(10, $maxHeight, $resident['age'], 1);

    // Address Cell (Wraps content using MultiCell)
    $x = $pdf->GetX(); // Save X position
    $pdf->MultiCell(30, 3, getAddress($resident['house_number'], $resident['street']), 1);
    $maxHeight = max($maxHeight, $pdf->GetY() - $startY); // Track the maximum height
    $pdf->SetXY($x + 30, $startY); // Move cursor to the right position

    $pdf->Cell(15, $maxHeight, $resident['date_of_birth'], 1);
    $pdf->Cell(15, $maxHeight, $resident['civil_status'], 1);
    $pdf->Cell(14, $maxHeight, $resident['house_number'], 1);
    $pdf->Cell(15, $maxHeight, $resident['street'], 1);
    $pdf->Cell(15, $maxHeight, $resident['citizenship'], 1);

    // Email Cell (Wraps content using MultiCell)
    $x = $pdf->GetX();
    $pdf->MultiCell(15, 3, $resident['email'], 1);
    $maxHeight = max($maxHeight, $pdf->GetY() - $startY); // Track the maximum height
    $pdf->SetXY($x + 15, $startY); // Move cursor to the right position

    $pdf->Cell(20, $maxHeight, $resident['occupation'], 1);
    $pdf->Cell(15, $maxHeight, $resident['disability'], 1);
    $pdf->Cell(15, $maxHeight, $resident['voter_status'], 1);
    $pdf->Cell(15, $maxHeight, $resident['status'], 1);

    // Move to the next line
    $pdf->Ln($maxHeight);
}

$pdf->Output();
?>
