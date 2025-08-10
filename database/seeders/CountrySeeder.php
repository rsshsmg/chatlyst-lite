<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countryJson = __DIR__ . '/../../database/data/countries.json';
        $countryFileExists = file_exists($countryJson);

        if ($countryFileExists) {
            $countries = json_decode(file_get_contents($countryJson), true);
            foreach ($countries as $country) {
                if (
                    !isset($country['timezones']) ||
                    trim($country['timezones']) === '' ||
                    (is_array($country['timezones']) && empty($country['timezones']))
                ) {
                    $country['timezones'] = null;
                } elseif (is_array($country['timezones'])) {
                    $country['timezones'] = json_encode($country['timezones']);
                } elseif (is_string($country['timezones'])) {
                    // Coba decode string JSON (jaga-jaga kalau valid)
                    $decoded = json_decode($country['timezones'], true);
                    $country['timezones'] = $decoded ? json_encode($decoded) : null;
                }
                DB::table('countries')->insert($country);
            }
        }
    }
}
