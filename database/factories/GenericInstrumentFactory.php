<?php

namespace Database\Factories;

use App\Models\GenericInstrument;
use Illuminate\Database\Eloquent\Factories\Factory;

class GenericInstrumentFactory extends Factory
{
    protected $model = GenericInstrument::class;

    public function definition()
    {
        return [
            'name' => $this->faker->unique()->word(),
        ];
    }
}
