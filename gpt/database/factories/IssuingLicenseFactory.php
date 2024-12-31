<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\IssuingLicense;

class IssuingLicenseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = IssuingLicense::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'fullName' => $this->faker->regexify('[A-Za-z0-9]{10}'),
            'nationalID' => $this->faker->regexify('(119|219)[0-9]{9}'),
            'passportOrID' => $this->faker->regexify('[A-Za-z0-9]{10}'),
            'phoneNumber' => $this->faker->regexify('[0-9]{15}'),
            'projectName' => $this->faker->regexify('[A-Za-z0-9]{20}'),
            'positionInProject' => $this->faker->regexify('[A-Za-z0-9]{20}'),
            'projectAddress' => $this->faker->regexify('[A-Za-z0-9]{20}'),
            'municipality_id' => $this->faker->word(),
            'region_id' => $this->faker->word(),
            // تعيين قيمة ثابتة أو عشوائية من القيم المقبولة
            'license_type_id' => $this->faker->randomElement(['نشاط فردي', 'شركات']),
            'nearestLandmark' => $this->faker->regexify('[A-Za-z0-9]{20}'),
            'licenseDate' => $this->faker->date(),
            'licenseNumber' => $this->faker->regexify('[A-Za-z0-9]{15}'),
            'licenseDuration' => $this->faker->numberBetween(-10000, 10000),
            'licenseFee' => $this->faker->numberBetween(-10000, 10000),
            'discount' => $this->faker->numberBetween(-10000, 10000),
            'chamberOfCommerceNumber' => $this->faker->regexify('[A-Za-z0-9]{20}'),
            'taxNumber' => $this->faker->regexify('[A-Za-z0-9]{20}'),
            'economicNumber' => $this->faker->regexify('[A-Za-z0-9]{20}'),
            'created_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
        ];
    }

}
