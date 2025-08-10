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
            'address_type' => $this->faker->randomElement(AddressType::cases()),
            'address' => $this->faker->streetAddress,
            'subdistrict_id' => Subdistrict::inRandomOrder()->first()->id,
            'country_id' => 103,
            'country_code' => $this->faker->countryCode(),
            'postal_code' => $this->faker->postcode,
            'is_primary' => $this->faker->boolean,
        ];
    }
}
