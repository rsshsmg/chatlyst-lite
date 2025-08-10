<?php

namespace Database\Factories;

use App\Enums\IdentityType;
use App\Models\Identity;
use App\Models\Person;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Identity>
 */
class IdentityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Randomly select from available types
        $type = $this->faker->randomElement(IdentityType::cases());

        return [
            'person_id' => Person::factory(),
            'identity_type' => $type,
            'number' => ($type == IdentityType::KTP) ?
                $this->faker->unique()->nik() :
                $this->faker->unique()->numerify('################'),
            'issued_at' => $this->faker->date(),
            'is_primary' => $this->faker->boolean(50),
        ];
    }
}
