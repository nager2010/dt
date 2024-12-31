<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\License;

class LicenseSeeder extends Seeder
{
    public function run()
    {
        License::create([
            'name' => 'رخصة تجريبية',
            'licenseDate' => now()->subMonths(6),
            'endDate' => now()->subDays(30),
            'status' => 'expired',
        ]);
    }
}
