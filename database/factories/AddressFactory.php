<?php

namespace Database\Factories;

use App\Enums\AddressType;
use App\Models\Country;
use App\Models\Person;
use App\Models\Subdistrict;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PatientAddress>
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'person_id' => Person::factory(),
            'address_type' => fake()->randomElement(AddressType::cases()),
            'address' => fake()->streetAddress,
            'subdistrict_id' => Subdistrict::factory(),
            'country_id' => 103,
            'country_code' => fake()->countryCode(),
            'postal_code' => fake()->postcode,
            'is_primary' => fake()->boolean,
        ];
    }
}
