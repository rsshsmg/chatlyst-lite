<?php

namespace Database\Factories;

use App\Enums\IdentityType;
use App\Models\Person;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Patient>
 */
class PatientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'patient_code' => $this->faker->numerify('PSH-######'),
            'ref_patient_code' => ($this->faker->boolean(30)) ? $this->faker->numerify('TSH-######') : null,
            'person_id' => Person::factory()
                ->hasIdentities(
                    $this->faker->boolean(80) ? 1 : rand(0, 2),
                    function (array $attributes, Person $person) {
                        // Cari identity_type yang belum digunakan
                        $availableTypes = collect(IdentityType::cases())
                            ->reject(
                                fn($type) =>
                                $person->identities()
                                    ->where('identity_type', $type->value)
                                    ->exists()
                            );

                        // Kalau tidak ada lagi yang tersedia, skip pembuatan
                        if ($availableTypes->isEmpty()) {
                            return []; // ini akan membuat factory tidak membuat identity
                        }

                        return [
                            'identity_type' => $availableTypes->random(),
                        ];
                    }
                )
                ->hasPhones($this->faker->boolean(80) ? 1 : rand(0, 2))
                ->hasEmails($this->faker->boolean(80) ? 1 : 0)
                ->hasAddresses($this->faker->boolean(90) ? 1 : rand(0, 3))
                ->create(),
        ];
    }

    public function withPatientPerson(): static
    {
        return $this->has(Person::factory()->asPatient(), 'person');
    }
}
