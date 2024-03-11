<?php

        namespace Database\Factories;

        use Illuminate\Database\Eloquent\Factories\Factory;

        class TesFactory extends Factory
        {

            public function definition()
            {
                return ['tes' => $this->faker->word(),
'bnbn' => $this->faker->word(),
 ];
            }
        };