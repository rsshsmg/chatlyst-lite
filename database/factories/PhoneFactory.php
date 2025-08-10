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
            'number' => $this->faker->e164PhoneNumber(),
            'country_code' => $this->faker->countryCode(),
            'is_whatsapp' => $this->faker->boolean(80),
            'is_primary' => $this->faker->boolean(70),
            'verified_at' => $this->faker->boolean(80) ? $this->faker->dateTime() : null,
        ];
    }
}
