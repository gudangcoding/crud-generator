<?php

        namespace Database\Factories;

        use App\Models\CrudTransaksi;
        use Illuminate\Database\Eloquent\Factories\Factory;

        class CrudTransaksiFactory extends Factory
        {
            protected $model = CrudTransaksi::class;
            public function definition()
            {
                return ['nama' => $this->faker->word(),
'email' => $this->faker->word(),
'alamat' => $this->faker->word(),
 ];
            }
        };