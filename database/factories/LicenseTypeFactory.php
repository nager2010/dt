<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\LicenseType;

class LicenseTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LicenseType::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'category' => $this->faker->regexify('[A-Za-z0-9]{50}'),
            'type' => $this->faker->regexify('[A-Za-z0-9]{100}'),
        ];
    }
}
