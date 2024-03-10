<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $fillable = ['nama', 'harga', 'deskripsi']; // Attribut yang dapat diisi secara massal

    // Definisikan relasi atau metode lain sesuai kebutuhan Anda di sini
}
