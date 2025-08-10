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
        $gender = $this->faker->randomElement(Gender::cases());

        return [
            'full_name' => $this->faker->name($gender->name),
            'nickname' => $this->faker->firstName($gender->name),
            'gender' => $gender->value,
            'place_of_birth' => $this->faker->city(),
            'date_of_birth' => $this->faker->dateTimeThisCentury->format('Y-m-d'),
            'mother_name' => $this->faker->boolean(60) ? $this->faker->name('female') : null,
            'blood_type' => $this->faker->randomElement(BloodType::cases()) ?? BloodType::BPositive,
            'religion' => $this->faker->randomElement(ReligionType::cases()) ?? ReligionType::Islam,
            'marital_status' => $this->faker->randomElement(MaritalStatus::cases()) ?? MaritalStatus::Single,
            'education_id' => $this->faker->boolean(30) ? Education::inRandomOrder()->first()->id : null,
            'job_title_id' => $this->faker->boolean(30) ? JobTitle::inRandomOrder()->first()->id : null,
            'lang_code' => $this->faker->languageCode(),
            'is_foreigner' => $this->faker->boolean(30),
        ];
    }

    public function asGuardian(): static
    {
        return $this->state(function () {
            return [
                'full_name' => $this->faker->name(),
                'gender' => $this->faker->randomElement(Gender::cases())->value,
            ];
        });
    }

    public function asPatient(): static
    {
        return $this->state(function () {
            $gender = $this->faker->randomElement(Gender::cases());

            return [
                'full_name' => $this->faker->name($gender->name),
                'nickname' => $this->faker->firstName($gender->name),
                'gender' => $gender->value,
                'place_of_birth' => $this->faker->city(),
                'date_of_birth' => $this->faker->dateTimeThisCentury->format('Y-m-d'),
                'mother_name' => $this->faker->boolean(60) ? $this->faker->name('female') : null,
                'blood_type' => $this->faker->randomElement(BloodType::cases()) ?? BloodType::BPositive,
                'religion' => $this->faker->randomElement(ReligionType::cases()) ?? ReligionType::Islam,
                'marital_status' => $this->faker->randomElement(MaritalStatus::cases()) ?? MaritalStatus::Single,
                'education_id' => $this->faker->boolean(30) ? Education::inRandomOrder()->first()->id : null,
                'job_title_id' => $this->faker->boolean(30) ? JobTitle::inRandomOrder()->first()->id : null,
                'lang_code' => $this->faker->languageCode(),
                'is_foreigner' => $this->faker->boolean(30),
            ];
        });
    }
}
