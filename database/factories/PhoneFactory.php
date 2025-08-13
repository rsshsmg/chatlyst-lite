<?php

namespace Database\Factories;

use App\Models\Person;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Phone>
 */
class PhoneFactory extends Factory
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
            'number' => fake()->e164PhoneNumber(),
            'country_code' => fake()->countryCode(),
            'is_whatsapp' => fake()->boolean(80),
            'is_primary' => fake()->boolean(70),
            'verified_at' => fake()->boolean(80) ? fake()->dateTime() : null,
        ];
    }
}
