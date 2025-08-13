<?php

namespace Database\Factories;

use App\Enums\BloodType;
use App\Enums\Gender;
use App\Enums\MaritalStatus;
use App\Enums\ReligionType;
use App\Models\Address;
use App\Models\Education;
use App\Models\Email;
use App\Models\Identity;
use App\Models\JobTitle;
use App\Models\Phone;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Person>
 */
class PersonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $gender = fake()->randomElement(Gender::cases());

        return [
            'full_name' => fake()->name(strtolower($gender->name)),
            'nickname' => fake()->firstName(strtolower($gender->name)),
            'gender' => $gender->value,
            'place_of_birth' => fake()->city(),
            'date_of_birth' => fake()->dateTimeThisCentury->format('Y-m-d'),
            'mother_name' => fake()->boolean(60) ? fake()->name('female') : null,
            'blood_type' => fake()->randomElement(BloodType::cases()) ?? BloodType::BPositive,
            'religion' => fake()->randomElement(ReligionType::cases()) ?? ReligionType::Islam,
            'marital_status' => fake()->randomElement(MaritalStatus::cases()) ?? MaritalStatus::Single,
            'education_id' => fake()->boolean(30) ? Education::factory()->create() : null,
            'job_title_id' => fake()->boolean(30) ? JobTitle::factory()->create() : null,
            'lang_code' => fake()->languageCode(),
            'is_foreigner' => fake()->boolean(30),
        ];
    }

    public function asGuardian(): static
    {
        return $this->state(function () {
            return [
                'full_name' => fake()->name(),
                'gender' => fake()->randomElement(Gender::cases())->value,
            ];
        });
    }

    public function asPatient(): static
    {
        return $this->state(function () {
            $gender = fake()->randomElement(Gender::cases());

            return [
                'full_name' => fake()->name($gender->label),
                'nickname' => fake()->firstName($gender->label),
                'gender' => $gender->value,
                'place_of_birth' => fake()->city(),
                'date_of_birth' => fake()->dateTimeThisCentury->format('Y-m-d'),
                'mother_name' => fake()->boolean(60) ? fake()->name('female') : null,
                'blood_type' => fake()->randomElement(BloodType::cases()) ?? BloodType::BPositive,
                'religion' => fake()->randomElement(ReligionType::cases()) ?? ReligionType::Islam,
                'marital_status' => fake()->randomElement(MaritalStatus::cases()) ?? MaritalStatus::Single,
                'education_id' => fake()->boolean(30) ? Education::factory()->create() : null,
                'job_title_id' => fake()->boolean(30) ? JobTitle::factory()->create() : null,
                'lang_code' => fake()->languageCode(),
                'is_foreigner' => fake()->boolean(30),
            ];
        });
    }
}
