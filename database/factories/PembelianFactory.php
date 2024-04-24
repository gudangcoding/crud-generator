<?php

namespace Database\Factories;

use App\Models\Pembelian;
use Illuminate\Database\Eloquent\Factories\Factory;

class PembelianFactory extends Factory
{
    protected $model = Pembelian::class;
    public function definition()
    {
        return [
            'nama_trx' => $this->faker->word(),
        ];
    }
};
