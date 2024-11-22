<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\IssuingLicense;

class IssuingLicenseSeeder extends Seeder
{
    public function run(): void
    {
        IssuingLicense::factory()->count(300)->create();
    }
}
