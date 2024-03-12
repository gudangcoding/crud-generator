<?php

        namespace App\Pdfs;

        use Barryvdh\DomPDF\Facade as PDF;

        class MemberPdf
        {
            public function generatePdf($data)
            {
                $pdf = PDF::loadView('Member.pdf', compact('data'));
                return $pdf->download('Member.pdf');
            }
        }
        