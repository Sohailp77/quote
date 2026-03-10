<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Quote>
 */
class QuoteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_name' => $this->faker->name(),
            'customer_email' => $this->faker->safeEmail(),
            'status' => 'draft',
            'tax_mode' => 'global',
            'total_amount' => $this->faker->randomFloat(2, 100, 1000),
            'reference_id' => 'Q-' . strtoupper(\Illuminate\Support\Str::random(8)),
        ];
    }
}
