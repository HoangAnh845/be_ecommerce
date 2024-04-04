<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => 169,
            'name' => $this->faker->word,
            'avatar' => $this->faker->imageUrl(),
            'note' => $this->faker->sentence,
            'describe' => $this->faker->paragraph,
            'image_other' => json_encode([$this->faker->imageUrl(), $this->faker->imageUrl()]),
            'outstan' => $this->faker->boolean,
            'tiki_best' => $this->faker->boolean,
            'genuine' => $this->faker->boolean,
            'support' => $this->faker->boolean,
            'amount' => $this->faker->randomNumber(),
            'price' => $this->faker->randomNumber(),
        ];
    }
}
