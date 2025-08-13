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
        $type = fake()->randomElement(ContactType::cases());
        $value = match ($type) {
            ContactType::Phone => fake()->e164PhoneNumber(),
            ContactType::Email => fake()->email(),
            ContactType::Whatsapp => fake()->e164PhoneNumber(),
        };
        $hasOwner = fake()->boolean(80);

        return [
            'contact_type' => $type,
            'value' => $value,
            'ownerable_id' => $hasOwner ? Patient::factory() : null,
            'ownerable_type' => $hasOwner ? Patient::class : null,
            'verified_at' => (fake()->boolean(60)) ? fake()->date() : null,
        ];
    }
}
