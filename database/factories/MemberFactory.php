<?php

namespace Database\Factories;

use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

class MemberFactory extends Factory
{
    protected $model = Member::class;
    /**
     * A description of the entire PHP function.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'nama' => $this->faker->word(),
            'email' => $this->faker->word(),
            'alamat' => $this->faker->word(),
        ];
    }
};
