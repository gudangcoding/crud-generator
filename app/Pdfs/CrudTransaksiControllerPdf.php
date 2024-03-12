<?php

        namespace App\Pdfs;

        use Barryvdh\DomPDF\Facade as PDF;

        class CrudTransaksiPdf
        {
            public function generatePdf($data)
            {
                $pdf = PDF::loadView('CrudTransaksi.pdf', compact('data'));
                return $pdf->download('CrudTransaksi.pdf');
            }
        }
        