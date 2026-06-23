<?php

namespace Database\Factories;

use App\Models\Training;
use App\Models\User;
use App\Models\Sheet;
use App\Models\GenericInstrument;
use App\Models\TrainingMedia;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrainingFactory extends Factory
{
    protected $model = Training::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word(),
            'date_training' => now()->addDay(),
            'end_training' => now()->addDays(2),
            'duration' => 60,
            'training_media_id' => TrainingMedia::factory(),
            'user_id' => User::factory(),
            'sheet_id' => Sheet::factory(),
            'instrument_id' => GenericInstrument::factory(),
            'link' => null,
        ];
    }
}
