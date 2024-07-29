<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Tcpdf\Fpdi;
use App\Models\Client;
use App\Models\Project;
use App\Models\UploadFile;
use Intervention\Image\ImageManagerStatic as Image;
use TCPDF;
use Auth;

class WatermarkService extends Fpdi
{
    protected $extgstates = array();
    var $angle = 0;

    function Rotate($angle,$x=-1,$y=-1)
    {
        if($x==-1)
            $x=$this->x;
        if($y==-1)
            $y=$this->y;
        if($this->angle!=0)
            $this->_out('Q');
        $this->angle=$angle;
        if($angle!=0)
        {
            $angle*=M_PI/180;
            $c=cos($angle);
            $s=sin($angle);
            $cx=$x*$this->k;
            $cy=($this->h-$y)*$this->k;
            $this->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
        }
    }

    function _endpage()
    {
        if ($this->angle != 0) {
            $this->angle = 0;
            $this->_out('Q');
        }
        parent::_endpage();
    }

    function RotatedText($x,$y,$txt,$angle)
    {
        //Text rotated around its origin
        $this->Rotate($angle,$x,$y);
        $this->Text($x,$y,$txt);
        $this->Rotate(0);
    }


    function MultiLineRotatedText($x, $y, $txt, $angle, $lineHeight)
    {
        $lines = explode("\n", $txt);
        foreach ($lines as $i => $line) {
            $lineWidth = $this->GetStringWidth($line);
            $centeredX = $x - ($lineWidth / 2);
            $this->RotatedText($centeredX, $y + ($i * $lineHeight) - ($y / 4), $line, $angle);
        }
    }

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
        if (!empty($this->extgstates) && $this->PDFVersion < '1.4') $this->PDFVersion = '1.4';
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
            $this->_put('/BM ' . $parms['BM']);
            $this->_put('>>');
            $this->_put('endobj');
        }
    }

    protected function _putresourcedict()
    {
        parent::_putresourcedict();
        $this->_put('/ExtGState <<');
        foreach ($this->extgstates as $k => $extgstate)
            $this->_put('/GS' . $k . ' ' . $extgstate['n'] . ' 0 R');
        $this->_put('>>');
    }

    protected function _putresources()
    {
        $this->_putextgstates();
        parent::_putresources();
    }

    public function addPDFWatermark($fileID) 
    {
        $pdf = new WatermarkService();
        $filePath = Storage::path(UploadFile::where('basename', $fileID)->value('directory') . '/' . $fileID);
        $outputPath = Storage::path('temp/' . Auth::user()->user_id . '/temp');
        $pageCount = $pdf->setSourceFile($filePath);
        $watermarkPath = public_path(Auth::user()->user_id . ".png");

        
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $templateId = $pdf->importPage($pageNo);
            $size = $pdf->getTemplateSize($templateId);

            list($watermarkWidth, $watermarkHeight) = getimagesize($watermarkPath);

            $watermarkWidth = $watermarkWidth / 10;
            $watermarkHeight = $watermarkHeight / 10;


            $pageWidth = $size['width'];
            $pageHeight = $size['height'];
            $x = ($pageWidth - $watermarkWidth) /2;
            $y = ($pageHeight - $watermarkHeight) / 2;

            $pdf->AddPage($size['orientation'], [$pageWidth, $pageHeight]);
            $pdf->useTemplate($templateId);

            $pdf->Image($watermarkPath, $x, $y, $watermarkWidth , $watermarkHeight, 'PNG', '', 'T');
        }

        $outputDir = dirname($outputPath);
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0777, true);
        }

        $pdf->Output($outputPath, 'F');
        unlink($watermarkPath);
    }

    public function addIMGWatermark($fileID) 
    {
        try {
            $filePath = Storage::path(UploadFile::where('basename', $fileID)->value('directory') . '/' . $fileID);
            $outputPath = Storage::path('temp/' . Auth::user()->user_id . '/temp');
            $fileMimeType = UploadFile::where('basename', $fileID)->value('mime_type');
            $watermarkPath = public_path(Auth::user()->user_id . ".png");

            $image1 = Image::make($filePath);
            $image2 = Image::make($watermarkPath);

            $wmwidth = 0;
            $widthscale = 0;
            $wmheight = 0;
            $heightscale = 0;

            while ($wmwidth < ($image1->width() - ($image1->width() * 0.1))) {
                $wmwidth += $image2->width() * 0.001;
                $widthscale += 1;
            }

            while ($wmheight < ($image1->height() - ($image1->height() * 0.1))) {
                $wmheight += $image2->height() * 0.001;
                $heightscale += 1;
            }

            if ($widthscale < $heightscale) {
                $scale = $widthscale;
            } else {
                $scale = $heightscale;
            }

            $wmwidth = $image2->width() * (0.001 * $scale);
            $wmheight = $image2->height() * (0.001 * $scale);

            $image2->resize($wmwidth, $wmheight);

            $mergedImage = Image::canvas($image1->width(), $image1->height());
            $mergedImage->insert($image1, 'center');
            $mergedImage->insert($image2, 'center');
            $mergedImage->save($outputPath);

            unlink($watermarkPath);
        } catch (\Exception $e) {
            $outputPath = Storage::path('temp/' . Auth::user()->user_id . '/temp');
            $watermarkPath = public_path(Auth::user()->user_id . ".png");

            unlink($watermarkPath);
        }
    }
}