<?php

namespace Database\Factories;

use App\Enums\RelationType;
use App\Models\Contact;
use App\Models\Patient;
use App\Models\Person;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PatientContact>
 */
class PatientGuardianFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'relation_type' => $this->faker->randomElement(RelationType::cases()),
            'person_id' => Person::factory(),
            'patient_id' => Patient::factory(),
        ];
    }
}
