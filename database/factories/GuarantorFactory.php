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
            'name' => $this->faker->unique()->company(),
            'code' => $this->faker->unique()->bothify('??##?-##?##-####'),
            'guarantor_type' => $this->faker->randomElement(GuarantorType::class),
            'description' => $this->faker->sentence(),
        ];
    }
}
