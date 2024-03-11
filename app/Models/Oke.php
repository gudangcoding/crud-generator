<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Oke extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'dsdds',
        'ferer',
    ];
    protected $dates = ['deleted_at']; // Tentukan kolom yang merupakan soft delete
    public function User()

    {

        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function Route()

    {

        return $this->belongsTo(Route::class, 'route_id', 'id');
    }
}
