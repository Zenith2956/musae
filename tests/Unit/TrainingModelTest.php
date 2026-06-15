<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Training;
use App\Models\GenericInstrument;
use App\Models\Sheet;

class TrainingModelTest extends TestCase
{
    public function test_training_belongs_to_instrument()
    {
        $training = Training::factory()->create();
        $this->assertInstanceOf(GenericInstrument::class, $training->instrument);
    }

    public function test_training_belongs_to_sheet()
    {
        $training = Training::factory()->create();
        $this->assertInstanceOf(Sheet::class, $training->sheet);
    }
}
