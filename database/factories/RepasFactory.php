<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Repas>
 */
class RepasFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nom' => $this->faker->word,
            'type_repas' => $this->faker->randomElement(['petit-déjeuner', 'déjeuner', 'dîner']),
            'date_consommation' => $this->faker->dateTimeThisMonth,
            'user_id' => \App\Models\User::factory(),
            'calories_total' => $this->faker->randomFloat(2, 100, 1000),
            'proteines_total' => $this->faker->randomFloat(2, 10, 100),
            'glucides_total' => $this->faker->randomFloat(2, 10, 100),
            'lipides_total' => $this->faker->randomFloat(2, 10, 100),
        ];
    }
}
