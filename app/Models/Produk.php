<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class produk extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'tes',
        'bnbn',
    ];
    protected $dates = ['deleted_at']; // Tentukan kolom yang merupakan soft delete

}
