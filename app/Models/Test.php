<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class test extends Model
{
    use SoftDeletes;
    protected $fillable = ['dsddsferer,'];
    protected $dates = ['deleted_at']; // Tentukan kolom yang merupakan soft delete
    public function User()

    {

        return $this->belongsTo(User::class);
    }
    public function Route()

    {

        return $this->belongsTo(Route::class);
    }
}
