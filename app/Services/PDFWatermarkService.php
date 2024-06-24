<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Tcpdf\Fpdi;
use App\Models\Client;
use App\Models\Project;
use App\Models\UploadFile;
use TCPDF;
use Auth;

class PDFWatermarkService extends Fpdi
{
    protected $extgstates = array();

    // Set alpha method for transparency


    protected function AddExtGState($parms)
    {
        $n = count($this->extgstates) + 1;
        $this->extgstates[$n]['parms'] = $parms;
        return $n;
    }

    protected function SetExtGState($gs)
    {
        $this->_out(sprintf('/GS%d gs', $gs));
    }

    protected function _enddoc()
    {
        if (!empty($this->extgstates) && $this->PDFVersion<'1.4')
            $this->PDFVersion='1.4';
        parent::_enddoc();
    }

    protected function _putextgstates()
    {
        for ($i = 1; $i <= count($this->extgstates); $i++) {
            $this->_newobj();
            $this->extgstates[$i]['n'] = $this->n;
            $this->_put('<</Type /ExtGState');
            $parms = $this->extgstates[$i]['parms'];
            $this->_put(sprintf('/ca %.3F', $parms['ca']));
            $this->_put(sprintf('/CA %.3F', $parms['CA']));
            $this->_put('/BM '.$parms['BM']);
            $this->_put('>>');
            $this->_put('endobj');
        }
    }

    protected function _putresourcedict()
    {
        parent::_putresourcedict();
        $this->_put('/ExtGState <<');
        foreach ($this->extgstates as $k => $extgstate)
            $this->_put('/GS'.$k.' '.$extgstate['n'].' 0 R');
        $this->_put('>>');
    }

    protected function _putresources()
    {
        $this->_putextgstates();
        parent::_putresources();
    }

    public function addWatermark($fileID)
    {
        $pdf = new FPDI();

        $filePath = Storage::path(UploadFile::where('basename', $fileID)->value('directory') . '/' . $fileID);
        $outputPath = Storage::path('temp/'. Auth::user()->user_id . '/temp');
        $projectName = Project::where('project_id', UploadFile::where('basename', $fileID)->value('project_id'))->value('project_name'); 
        $userName = Auth::user()->name;
        $companyName = Client::where('client_id', Auth::user()->client_id)->value('client_name');
        $timestamp = date('F j, Y H:i', strtotime('now'));

        $watermarkText = <<<EOT
                        Project $projectName
                        $userName
                        $companyName
                        $timestamp WIB
                        EOT;

        $pageCount = $pdf->setSourceFile($filePath);

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $templateId = $pdf->importPage($pageNo);
            $size = $pdf->getTemplateSize($templateId);
    
            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($templateId);
    
            $pdf->SetFont('Helvetica', 'B', 24);
            $pdf->SetAlpha(0.4);
            $pdf->SetTextColor(255,0,0); 
            $pdf->SetXY(10, $size['height']/2 - $size['height']/7);
    
            $pdf->MultiCell(0, 10, $watermarkText, 0, 'C', 0, 1, '', '', true);
    
            $pdf->SetXY(0, 0);
        }

        $outputDir = dirname($outputPath);
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0777, true);
        }

        $pdf->Output($outputPath, 'F');
    }
}
