<?php

                namespace App\Imports;

                use App\Models\Member;
                use Illuminate\Support\Facades\Log;
                use Maatwebsite\Excel\Concerns\ToModel;
                use Maatwebsite\Excel\Concerns\WithChunkReading;
                use Maatwebsite\Excel\Concerns\WithHeadingRow;
                use Maatwebsite\Excel\Concerns\Importable;

                class MemberImport implements ToModel, WithChunkReading, WithHeadingRow
                {
                    use Importable;

                    public function model(array $row)
                    {
                        try {
                            $Member = new Member(['nama' =>  $row['nama'],
'email' =>  $row['email'],
'alamat' =>  $row['alamat'],

]);


        $Member->save();


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