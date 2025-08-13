<?php

namespace Database\Factories;

use App\Enums\GuarantorType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Team>
 */
class GuarantorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->company(),
            'code' => fake()->unique()->bothify('??##?-##?##-####'),
            'guarantor_type' => fake()->randomElement(GuarantorType::class),
            'description' => fake()->sentence(),
        ];
    }
}
