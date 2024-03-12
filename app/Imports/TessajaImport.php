<?php

                namespace App\Imports;

                use App\Models\Tessaja;
                use Illuminate\Support\Facades\Log;
                use Maatwebsite\Excel\Concerns\ToModel;
                use Maatwebsite\Excel\Concerns\WithChunkReading;
                use Maatwebsite\Excel\Concerns\WithHeadingRow;
                use Maatwebsite\Excel\Concerns\Importable;

                class TessajaImport implements ToModel, WithChunkReading, WithHeadingRow
                {
                    use Importable;

                    public function model(array $row)
                    {
                        try {
                            $tessaja = new Tessaja(['nama' =>  $row['nama'],
'email' =>  $row['email'],
'alamat' =>  $row['alamat'],

]);


        $tessaja->save();


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