<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PatientGuarantor>
 */
class PatientGuarantorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $hasValidDate = fake()->boolean(0.5);

        return [
            'patient_id' => \App\Models\Patient::factory(),
            'guarantor_id' => \App\Models\Guarantor::factory(),
            'member_number' => fake()->unique()->bothify('???-#######'),
            'is_primary' => fake()->boolean(80),
            'valid_from' => ($hasValidDate) ? fake()->date() : null,
            'valid_to' => ($hasValidDate) ? fake()->date() : null,
        ];
    }
}
