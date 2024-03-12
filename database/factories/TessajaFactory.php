<?php

namespace Database\Factories;

use App\Models\Tessaja; // Tambahkan namespace model Tessaja
use Illuminate\Database\Eloquent\Factories\Factory;


class TessajaFactory extends Factory
{
    protected $model = Tessaja::class;
    public function definition()
    {

        return [
            'nama' => $this->faker->word(),
            'email' => $this->faker->word(),
            'alamat' => $this->faker->word(),
        ];
    }
};
