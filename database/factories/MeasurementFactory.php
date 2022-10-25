<?php

namespace Database\Factories;

use App\Model;
use App\Models\Measurement;
use Illuminate\Database\Eloquent\Factories\Factory;

class MeasurementFactory extends Factory
{
    protected $model = Measurement::class;

    public function definition(): array
    {
    	return [
    	    'unit' => $this->faker->word,
            'netto' => 0,
            'brutto' => $this->faker->numberBetween(250, 10000),
            'product' => $this->faker->word,
            'customer' => $this->faker->lastName,
            'plate' => $this->faker->text(6),
            'driver' => $this->faker->firstName,
    	];
    }
}
