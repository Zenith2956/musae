<?php

namespace Database\Factories;

use App\Models\Sheet;
use App\Models\User;
use App\Models\GenericInstrument;
use Illuminate\Database\Eloquent\Factories\Factory;

class SheetFactory extends Factory
{
    protected $model = Sheet::class;

    public function definition()
    {
        return [
            'name' => $this->faker->sentence(3),
            'link' => $this->faker->url(),
            'composer' => $this->faker->name(),
            'instrument_id' => GenericInstrument::factory(),
            'user_id' => User::factory(),
        ];
    }
}
