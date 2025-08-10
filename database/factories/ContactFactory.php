<?php

namespace Database\Factories;

use App\Enums\ContactType;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contact>
 */
class ContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(ContactType::cases());
        $value = match ($type) {
            ContactType::Phone => $this->faker->e164PhoneNumber(),
            ContactType::Email => $this->faker->email(),
            ContactType::Whatsapp => $this->faker->e164PhoneNumber(),
        };
        $hasOwner = $this->faker->boolean(80);

        return [
            'contact_type' => $type,
            'value' => $value,
            'ownerable_id' => $hasOwner ? Patient::factory() : null,
            'ownerable_type' => $hasOwner ? Patient::class : null,
            'verified_at' => ($this->faker->boolean(60)) ? $this->faker->date() : null,
        ];
    }
}
