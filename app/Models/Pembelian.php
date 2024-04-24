<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Pembelian extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'nama_trx',
    ];
    protected $dates = ['deleted_at']; // Tentukan kolom yang merupakan soft delete
    public function Member()

    {

        return $this->belongsTo(Member::class, 'member_id', 'id');
    }
}
