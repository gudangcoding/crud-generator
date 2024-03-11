<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class OkeFactory extends Factory
{

    public function definition()
    {
        return [
            'dsdds' => $this->faker->word(),
            'ferer' => $this->faker->word(),
        ];
    }
};
