<?php

        namespace Database\Factories;

        use Illuminate\Database\Eloquent\Factories\Factory;

        class BarangFactory extends Factory
        {

            public function definition()
            {
                return ['tes' => $this->faker->word(),
'bnbn' => $this->faker->word(),
 ];
            }
        };