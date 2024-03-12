<?php

        namespace App\Pdfs;

        use Barryvdh\DomPDF\Facade as PDF;

        class TessajaPdf
        {
            public function generatePdf($data)
            {
                $pdf = PDF::loadView('Tessaja.pdf', compact('data'));
                return $pdf->download('Tessaja.pdf');
            }
        }
        