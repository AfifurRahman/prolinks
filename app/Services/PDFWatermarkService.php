<?php

namespace App\Services;

use setasign\Fpdi\Fpdi;
use TCPDF;

class PDFWatermarkService
{
    public function addWatermark($filePath, $outputPath, $watermarkText)
    {
        // Initialize FPDI
        $pdf = new FPDI();

        // Set the source file
        $pageCount = $pdf->setSourceFile($filePath);

        // Add watermark to each page
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $templateId = $pdf->importPage($pageNo);
            $size = $pdf->getTemplateSize($templateId);

            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($templateId);

            // Set font and color
            $pdf->SetFont('Helvetica', 'B', 50);
            $pdf->SetTextColor(255, 0, 0);

            // Calculate the position of the watermark
            $width = $pdf->GetPageWidth();
            $height = $pdf->GetPageHeight();
            $x = ($width - $pdf->GetStringWidth($watermarkText)) / 2;
            $y = $height / 2;

            // Add the watermark text
            $pdf->Text($x, $y, $watermarkText);

        }

        $outputDir = dirname($outputPath);
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0777, true);
        }

        $pdf->Output($outputPath, 'F');
    }
}
