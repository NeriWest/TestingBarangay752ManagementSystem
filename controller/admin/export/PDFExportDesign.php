<?php

class PDF extends FPDF {
    // Page footer
    function Footer() {
        $this->SetY(-15); // Position at 15mm from bottom
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(0);
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}
?>