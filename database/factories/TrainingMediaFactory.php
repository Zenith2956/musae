<?php

namespace Database\Factories;

use App\Models\TrainingMedia;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrainingMediaFactory extends Factory
{
    protected $model = TrainingMedia::class;

    public function definition()
    {
        return [
            'link' => $this->faker->url(),
        ];
    }
}
