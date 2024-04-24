<?php

namespace App\Pdfs;

use Barryvdh\DomPDF\Facade as PDF;

class PembelianPdf
{
    public function generatePdf($data)
    {
        $pdf = PDF::loadView('Pembelian.pdf', compact('data'));
        return $pdf->download('Pembelian.pdf');
    }
}
