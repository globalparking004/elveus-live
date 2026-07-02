<?php

namespace App\Libraries;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfGenerator
{
    protected $dompdf;

    public function __construct()
    {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);

        $this->dompdf = new Dompdf($options);
    }

    public function generatePdf($htmlContent, $outputFilePath)
    {
        $this->dompdf->loadHtml($htmlContent);
        $this->dompdf->render();
        $output = $this->dompdf->output();

        file_put_contents($outputFilePath, $output);
    }
}
