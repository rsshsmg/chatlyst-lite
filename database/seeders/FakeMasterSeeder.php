<?php

namespace Database\Seeders;

use App\Models\District;
use App\Models\Education;
use App\Models\JobTitle;
use App\Models\Province;
use App\Models\Regency;
use App\Models\SubDistrict;
use Illuminate\Database\Seeder;

class FakeMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Education::factory()
            ->count(10)
            ->create();

        JobTitle::factory()
            ->count(10)
            ->create();

        $provinces = Province::factory()
            ->count(10)
            ->create([
                'country_id' => 103, // Assuming 103 is the ID for a specific country
            ]);

        foreach ($provinces as $province) {
            $regencies = Regency::factory()
                ->count(10)
                ->create([
                    'province_id' => $province->id, // Create a province and use its ID
                ]);

            foreach ($regencies as $regency) {
                $districts = District::factory()
                    ->count(10)
                    ->create([
                        'regency_id' => $regency->id, // Create a regency and use its ID
                    ]);

                foreach ($districts as $district) {
                    SubDistrict::factory()
                        ->count(10)
                        ->create([
                            'district_id' => $district->id, // Create a district and use its ID
                        ]);
                }
            }
        }
    }
}
