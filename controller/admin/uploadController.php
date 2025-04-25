<?php
require_once __DIR__ . '/../../model/dbconnection.php';
require_once __DIR__ . '/../../model/admin/uploadModel.php';
require_once __DIR__ . '/../../vendor/autoload.php'; // FPDI library

use setasign\Fpdi\Fpdi;
use setasign\Fpdf\Fpdf;

header('Content-Type: application/json');

// Enable error reporting for development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Debugging Logs
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../../logs/error_log.txt');

$response = ["success" => false, "message" => "Invalid request"];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Invalid request method. Only POST is allowed.");
    }

    // Log received inputs
    error_log("Received POST: " . print_r($_POST, true));
    error_log("Received FILES: " . print_r($_FILES, true));

    // Validate inputs
    if (!isset($_FILES['templateFile']) || !isset($_POST['templateId'])) {
        throw new Exception("Missing required fields: templateFile or templateId");
    }

    if ($_FILES['templateFile']['error'] !== UPLOAD_ERR_OK) {
        $uploadErrors = [
            UPLOAD_ERR_INI_SIZE => "File exceeds upload_max_filesize",
            UPLOAD_ERR_FORM_SIZE => "File exceeds form max size",
            UPLOAD_ERR_PARTIAL => "File was only partially uploaded",
            UPLOAD_ERR_NO_FILE => "No file was uploaded",
            UPLOAD_ERR_NO_TMP_DIR => "Missing temporary folder",
            UPLOAD_ERR_CANT_WRITE => "Failed to write file to disk",
            UPLOAD_ERR_EXTENSION => "A PHP extension stopped the upload"
        ];
        $errorMsg = $uploadErrors[$_FILES['templateFile']['error']] ?? "Unknown upload error";
        throw new Exception("File upload error: $errorMsg (Code: {$_FILES['templateFile']['error']})");
    }

    $templateId = filter_var($_POST['templateId'], FILTER_VALIDATE_INT);
    if ($templateId === false || $templateId <= 0) {
        throw new Exception("Invalid template ID");
    }

    // Validate file type
    $fileType = mime_content_type($_FILES['templateFile']['tmp_name']);
    if ($fileType !== 'application/pdf') {
        throw new Exception("Invalid file type: $fileType. Only PDFs are allowed.");
    }

    // Set up upload directory
    $uploadDir = __DIR__ . "/../../documents/processed/";
    error_log("Upload dir: $uploadDir");

    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0777, true)) {
            throw new Exception("Failed to create upload directory: $uploadDir");
        }
    }

    if (!is_writable($uploadDir)) {
        throw new Exception("Upload directory is not writable: $uploadDir");
    }

    // Generate unique file name
    $fileName = "document$templateId-" . time() . "-" . basename($_FILES['templateFile']['name']);
    $fileName = preg_replace('/\s+/', '_', $fileName);
    $targetFile = $uploadDir . $fileName;
    error_log("Target file: $targetFile");

    // Move uploaded file
    if (!move_uploaded_file($_FILES['templateFile']['tmp_name'], $targetFile)) {
        throw new Exception("Failed to move uploaded file to $targetFile");
    }

    // Process PDF
    $modifiedFilePath = processPdfWithFpdi($targetFile, $templateId);
    if (!$modifiedFilePath) {
        throw new Exception("Failed to modify PDF");
    }

    // Update database
    $relativePath = "documents/processed/" . basename($modifiedFilePath);
    error_log("Updating template ID: $templateId with path: $relativePath");
    $updateResult = updateTemplateDocument($templateId, $relativePath);

    if (!$updateResult["success"]) {
        throw new Exception("Database update failed");
    }

    // Success response
    $response = [
        "success" => true,
        "documentUrl" => "/Barangay752managementsystem/" . $relativePath
    ];
    error_log("Returning document URL: " . $response['documentUrl']);

} catch (Exception $e) {
    error_log("Upload Error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
    $response["message"] = $e->getMessage();
}

echo json_encode($response);

        function processPdfWithFpdi($filePath, $templateId) {
            require_once __DIR__ . '/../../model/admin/uploadModel.php';
            $outputDir = __DIR__ . "/../../documents/processed/";
            error_log("Output dir: $outputDir");

            if (!is_dir($outputDir)) {
                if (!mkdir($outputDir, 0777, true)) {
                    error_log("Failed to create output directory: $outputDir");
                    return false;
                }
            }

            if (!is_writable($outputDir)) {
                error_log("Output directory is not writable: $outputDir");
                return false;
            }

            $outputFilePath = $outputDir . "modified_$templateId-" . time() . ".pdf";
            error_log("Output file path: $outputFilePath");

            $template = getTemplateById($templateId);
            $docTitle = isset($template['doc_name']) ? strtoupper($template['doc_name']) : 'DOCUMENT TITLE';
            error_log("Document title: $docTitle");

            try {
                $pdf = new FPDI();
                $pageCount = $pdf->setSourceFile($filePath);
                error_log("PDF page count: $pageCount");

                $officials = getOfficialsByPosition();
                error_log("Officials: " . print_r($officials, true));

                for ($i = 1; $i <= $pageCount; $i++) {
                    $tplIdx = $pdf->importPage($i);
                    $size = $pdf->getTemplateSize($tplIdx);
                    error_log("Page $i size: " . print_r($size, true));

                    $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                    $pdf->useTemplate($tplIdx, 0, 0, $size['width'], $size['height']);

                    $centerX = 34.5; // X position in mm

                    // Punong Barangay
                    $pdf->SetFont('Helvetica', 'B', 11.5); // Use Helvetica instead of Times
                    $pdf->SetTextColor(0, 0, 0);
                    $punongBarangay = !empty($officials['Punong Barangay']) ? 'Hon. ' . $officials['Punong Barangay'][0] : 'Hon. [NAME]';
                    $text = $punongBarangay;
                    $textWidth = $pdf->GetStringWidth($text);
                    $pdf->SetXY($centerX - ($textWidth / 2), 63);
                    $pdf->Cell($textWidth, 10, $text, 0, 1, 'L');

                    $pdf->SetFont('Helvetica', '', 11.5);
                    $text = "Punong Barangay";
                    $textWidth = $pdf->GetStringWidth($text);
                    $pdf->SetXY($centerX - ($textWidth / 2), 68.5);
                    $pdf->Cell($textWidth, 10, $text, 0, 1, 'L');

                    $text = "Zone 81, Zone Chairman";
                    $textWidth = $pdf->GetStringWidth($text);
                    $pdf->SetXY($centerX - ($textWidth / 2), 73);
                    $pdf->Cell($textWidth, 10, $text, 0, 1, 'L');

                    $text = "Sangguniang Barangay Members";
                    $textWidth = $pdf->GetStringWidth($text);
                    $pdf->SetXY($centerX - ($textWidth / 2), 84);
                    $pdf->Cell($textWidth, 10, $text, 0, 1, 'L');

                    // Sangguniang Barangay Members
                    $pdf->SetFont('Helvetica', 'B', 11.5);
                    $sbmStartY = 95;
                    $lineHeight = 15;
                    if (!empty($officials['Sangguniang Barangay Member'])) {
                        foreach ($officials['Sangguniang Barangay Member'] as $name) {
                            $fullText = 'SBM ' . $name;
                            $textWidth = $pdf->GetStringWidth($fullText);
                            $pdf->SetXY($centerX - ($textWidth / 2), $sbmStartY);
                            $pdf->Cell($textWidth, 10, $fullText, 0, 1, 'L');
                            $sbmStartY += $lineHeight;
                        }
                    }

                    // SK Chairperson
                    $skchair = !empty($officials['SK Chairperson']) ? $officials['SK Chairperson'][0] : '[NAME]';
                    $text = $skchair;
                    $textWidth = $pdf->GetStringWidth($text);
                    $pdf->SetXY($centerX - ($textWidth / 2), 200);
                    $pdf->Cell($textWidth, 10, $text, 0, 1, 'L');

                    $pdf->SetFont('Helvetica', '', 11.5);
                    $text = "SK Chairperson";
                    $textWidth = $pdf->GetStringWidth($text);
                    $pdf->SetXY($centerX - ($textWidth / 2), 205.5);
                    $pdf->Cell($textWidth, 10, $text, 0, 1, 'L');

                    // Secretary
                    $secretary = !empty($officials['Secretary']) ? $officials['Secretary'][0] : '[NAME]';
                    $text = $secretary;
                    $textWidth = $pdf->GetStringWidth($text);
                    $pdf->SetXY($centerX - ($textWidth / 2), 217);
                    $pdf->Cell($textWidth, 10, $text, 0, 1, 'L');

                    $pdf->SetFont('Helvetica', '', 11.5);
                    $text = 'Barangay Secretary';
                    $textWidth = $pdf->GetStringWidth($text);
                    $pdf->SetXY($centerX - ($textWidth / 2), 222);
                    $pdf->Cell($textWidth, 10, $text, 0, 1, 'L');

                    $text = 'Zone Secretary, Zone 81';
                    $textWidth = $pdf->GetStringWidth($text);
                    $pdf->SetXY($centerX - ($textWidth / 2), 227);
                    $pdf->Cell($textWidth, 10, $text, 0, 1, 'L');

                    // Treasurer
                    $treasurer = !empty($officials['Treasurer']) ? $officials['Treasurer'][0] : '[NAME]';
                    $text = $treasurer;
                    $textWidth = $pdf->GetStringWidth($text);
                    $pdf->SetXY($centerX - ($textWidth / 2), 238);
                    $pdf->Cell($textWidth, 10, $text, 0, 1, 'L');

                    $pdf->SetFont('Helvetica', '', 11.5);
                    $text = 'Barangay Treasurer';
                    $textWidth = $pdf->GetStringWidth($text);
                    $pdf->SetXY($centerX - ($textWidth / 2), 243.5);
                    $pdf->Cell($textWidth, 10, $text, 0, 1, 'L');

                    // Paragraph writing function
                    function writeParagraphWithBold($pdf, $x, $y, $rawText, $boldPhrases, $maxWidth, $lineHeight) {
                        $pdf->SetXY($x, $y);
                        $pdf->SetFont('Helvetica', '', 12);

                        $wordList = [];
                        $offset = 0;
                        while ($offset < strlen($rawText)) {
                            $matched = false;
                            foreach ($boldPhrases as $phrase) {
                                if (stripos($rawText, $phrase, $offset) === $offset) {
                                    $wordList[] = [$phrase, true];
                                    $offset += strlen($phrase);
                                    $matched = true;
                                    break;
                                }
                            }
                            if (!$matched) {
                                $chunk = '';
                                while ($offset < strlen($rawText)) {
                                    $char = $rawText[$offset];
                                    $chunk .= $char;
                                    $offset++;
                                    $next = substr($rawText, $offset);
                                    $found = false;
                                    foreach ($boldPhrases as $phrase) {
                                        if (stripos($next, $phrase) === 0) {
                                            $found = true;
                                            break;
                                        }
                                    }
                                    if ($char === ' ' || $found) break;
                                }
                                $wordList[] = [$chunk, false];
                            }
                        }

                        $currentLine = [];
                        $currentWidth = 0;
                        $isFirstLine = true;

                        foreach ($wordList as [$word, $isBold]) {
                            $pdf->SetFont('Helvetica', $isBold ? 'B' : '', 12);
                            $wordWidth = $pdf->GetStringWidth($word);

                            if ($isFirstLine && empty($currentLine)) {
                                $indentWidth = $pdf->GetStringWidth('        ');
                                $wordWidth += $indentWidth;
                            }

                            if ($currentWidth + $wordWidth > $maxWidth) {
                                $pdf->SetX($x);
                                if ($isFirstLine) $pdf->Write($lineHeight, '        ');
                                foreach ($currentLine as [$txt, $bold]) {
                                    $pdf->SetFont('Helvetica', $bold ? 'B' : '', 12);
                                    $pdf->Write($lineHeight, $txt);
                                }
                                $pdf->Ln($lineHeight);
                                $currentLine = [];
                                $currentWidth = 0;
                                $isFirstLine = false;
                            }

                            $currentLine[] = [$word, $isBold];
                            $currentWidth += $wordWidth;
                        }

                        if (!empty($currentLine)) {
                            $pdf->SetX($x);
                            if ($isFirstLine) $pdf->Write($lineHeight, '        ');
                            foreach ($currentLine as [$txt, $bold]) {
                                $pdf->SetFont('Helvetica', $bold ? 'B' : '', 12);
                                $pdf->Write($lineHeight, $txt);
                            }
                        }
                    }

                    // Write paragraphs
                    writeParagraphWithBold($pdf, 75, 113,
                        "This is to certify that MA. TERESA O. PAPA is a bonafide resident and belongs to one of indigent families of Brgy. 752, Zone 81, District V, Manila with residence address at 2445 Leyte St. Singalong Malate Manila.",
                        [
                            "MA. TERESA O. PAPA",
                            "bonafide resident",
                            "Brgy. 752, Zone 81, District V, Manila",
                            "2445 Leyte St. Singalong Malate Manila."
                        ], 130, 5
                    );

                    writeParagraphWithBold($pdf, 75, 145,
                        "This certification is issued upon the request of Ms. Papa for any legal intents and purposes that it may serve.",
                        ["Ms. Papa"], 130, 4.5
                    );

                    // Date
                    $day = 25;
                    $month = 'March';
                    $year = 2025;
                    $pdf->SetXY(80, 167);
                    $pdf->SetFont('Helvetica', '', 12);
                    $pdf->Write(5, "Issued this ");
                    $pdf->SetFont('Helvetica', 'BU', 12);
                    $pdf->Write(5, "$day");
                    $xSup = $pdf->GetX();
                    $ySup = $pdf->GetY();
                    $pdf->SetFont('Helvetica', 'BU', 8);
                    $pdf->SetXY($xSup, $ySup - 1.5);
                    $pdf->Write(5, "th");
                    $pdf->SetXY($xSup + $pdf->GetStringWidth("th"), $ySup);
                    $pdf->SetFont('Helvetica', '', 12);
                    $pdf->Write(5, " day of ");
                    $pdf->SetFont('Helvetica', 'BU', 12);
                    $pdf->Write(5, "$month");
                    $pdf->SetFont('Helvetica', '', 12);
                    $pdf->Write(5, ", ");
                    $pdf->SetFont('Helvetica', 'BU', 12);
                    $pdf->Write(5, "$year");
                    $pdf->SetFont('Helvetica', '', 12);
                    $pdf->Write(5, ".");

                    // Purpose
                    $purpose = 'for legal purposes'; // Replaced inappropriate text
                    $pdf->SetFont('Helvetica', 'BU', 12);
                    $text = "PURPOSE: " . $purpose . ".";
                    $textWidth = $pdf->GetStringWidth($text);
                    $pdf->SetXY(75, 183);
                    $pdf->Cell($textWidth, 10, $text, 0, 1, 'L');

                    // Image (with error handling)
                    $imagePath = __DIR__ . '/../../img/AboutUsCarousel1.jpg';
                    if (file_exists($imagePath)) {
                        $pdf->Image($imagePath, 164.5, 162, 42, 38);
                        error_log("Image added: $imagePath");
                    } else {
                        error_log("Image not found: $imagePath");
                    }

                    // Document Title
                    $centerX = 140;
                    $pdf->SetFont('Helvetica', 'BUI', 25);
                    $pdf->SetTextColor(255, 0, 0);
                    $textWidth = $pdf->GetStringWidth($docTitle);
                    $pdf->SetXY($centerX - ($textWidth / 2), 70);
                    $pdf->Cell($textWidth, 10, $docTitle, 0, 1, 'L');

                    // Attested by
                    $centerX = 110;
                    $pdf->SetFont('Helvetica', '', 10);
                    $pdf->SetTextColor(0, 0, 0);
                    $text = 'Attested by:';
                    $textWidth = $pdf->GetStringWidth($text);
                    $pdf->SetXY($centerX - ($textWidth / 2), 197);
                    $pdf->Cell($textWidth, 10, $text, 0, 1, 'L');

                    // Approved by
                    $centerX = 180;
                    $pdf->SetFont('Helvetica', '', 10);
                    $text = 'Approved by:';
                    $textWidth = $pdf->GetStringWidth($text);
                    $pdf->SetXY($centerX - ($textWidth / 2), 197);
                    $pdf->Cell($textWidth, 10, $text, 0, 1, 'L');

                    // To Whom It May Concern
                    $pdf->SetFont('Helvetica', 'B', 12);
                    $text = "TO WHOM IT MAY CONCERN:";
                    $textWidth = $pdf->GetStringWidth($text);
                    $pdf->SetXY(75, 90);
                    $pdf->Cell($textWidth, 10, $text, 0, 1, 'L');

                    // Secretary (Attested)
                    $centerX = 110;
                    $pdf->SetFont('Helvetica', 'B', 12);
                    $text = $secretary;
                    $textWidth = $pdf->GetStringWidth($text);
                    $pdf->SetXY($centerX - ($textWidth / 2), 212);
                    $pdf->Cell($textWidth, 10, $text, 0, 1, 'L');

                    $pdf->SetFont('Helvetica', '', 11.5);
                    $text = 'Barangay Secretary';
                    $textWidth = $pdf->GetStringWidth($text);
                    $pdf->SetXY($centerX - ($textWidth / 2), 218);
                    $pdf->Cell($textWidth, 10, $text, 0, 1, 'L');

                    $text = 'Zone Secretary, Zone 81';
                    $textWidth = $pdf->GetStringWidth($text);
                    $pdf->SetXY($centerX - ($textWidth / 2), 224);
                    $pdf->Cell($textWidth, 10, $text, 0, 1, 'L');

                    // Punong Barangay (Approved)
                    $centerX = 180;
                    $pdf->SetFont('Helvetica', 'B', 12);
                    $punongBarangay = !empty($officials['Punong Barangay']) ? $officials['Punong Barangay'][0] : '[NAME]';
                    $text = $punongBarangay;
                    $textWidth = $pdf->GetStringWidth($text);
                    $pdf->SetXY($centerX - ($textWidth / 2), 212);
                    $pdf->Cell($textWidth, 10, $text, 0, 1, 'L');

                    $pdf->SetFont('Helvetica', 'I', 11.5);
                    $text = "Punong Barangay";
                    $textWidth = $pdf->GetStringWidth($text);
                    $pdf->SetXY($centerX - ($textWidth / 2), 218);
                    $pdf->Cell($textWidth, 10, $text, 0, 1, 'L');

                    $text = "Zone 81, Zone Chairman";
                    $textWidth = $pdf->GetStringWidth($text);
                    $pdf->SetXY($centerX - ($textWidth / 2), 224);
                    $pdf->Cell($textWidth, 10, $text, 0, 1, 'L');

                    // Dry Seal Notice
                    $centerX = 140;
                    $pdf->SetFont('Helvetica', 'I', 10);
                    $text = "(NOT VALID WITHOUT BARANGAY DRY SEAL)";
                    $textWidth = $pdf->GetStringWidth($text);
                    $pdf->SetXY($centerX - ($textWidth / 2), 240);
                    $pdf->Cell($textWidth, 10, $text, 0, 1, 'L');

                    // Drug Cleared Notice
                    $centerX = 140;
                    $pdf->SetFont('Helvetica', 'B', 15);
                    $text = '"DRUG CLEARED BARANGAY SINCE 2019"';
                    $textWidth = $pdf->GetStringWidth($text);
                    $pdf->SetXY($centerX - ($textWidth / 2), 249);
                    $pdf->Cell($textWidth, 10, $text, 0, 1, 'L');
                }

                $pdf->Output($outputFilePath, 'F');
                error_log("PDF output successful: $outputFilePath");
                return $outputFilePath;

            } catch (Exception $e) {
                error_log("FPDI Error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
                return false;
            }
        }
?>