<?php

namespace Database\Factories;

use App\Models\Offer;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'offer_id' => Offer::factory(),
            'name' => fake()->words(2, true),
            'sku' => strtoupper(fake()->unique()->bothify('SKU-####-????')),
            'image' => fake()->optional(0.8)->passthrough('products/placeholder.jpg'),
            'price' => fake()->randomFloat(2, 5, 500),
            'state' => fake()->randomElement(array_keys(Product::$states)),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => ['state' => 'draft']);
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => ['state' => 'published']);
    }

    public function invisible(): static
    {
        return $this->state(fn (array $attributes) => ['state' => 'invisible']);
    }
}
