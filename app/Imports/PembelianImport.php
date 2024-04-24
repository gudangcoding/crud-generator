<?php

namespace App\Imports;

use App\Models\Pembelian;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;

class PembelianImport implements ToModel, WithChunkReading, WithHeadingRow
{
    use Importable;

    public function model(array $row)
    {
        try {
            $pembelian = new Pembelian([
                'nama_trx' =>  $row['nama_trx'],

            ]);


            $pembelian->save();


            Log::info('Baris berhasil diimpor: ' . json_encode($row));
        } catch (\Exception $e) {
            Log::error('Baris gagal diimpor: ' . json_encode($row) . '. Error: ' . $e->getMessage());
        }
    }

    public function chunkSize(): int
    {
        return 1000; // Menentukan ukuran chunk
    }
    //https://docs.laravel-excel.com/3.1/getting-started/installation.html


}
