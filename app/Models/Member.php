<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'nama',
        'email',
        'alamat',
    ];
    protected $dates = ['deleted_at']; // Tentukan kolom yang merupakan soft delete

}
