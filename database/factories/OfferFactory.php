<?php

namespace Database\Factories;

use App\Models\Offer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Offer>
 */
class OfferFactory extends Factory
{
    protected $model = Offer::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->words(3, true);

        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1, 9999),
            'description' => fake()->optional(0.7)->sentence(),
            'image' => 'offers/placeholder.jpg',
            'state' => fake()->randomElement(array_keys(Offer::$states)),
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

    public function hidden(): static
    {
        return $this->state(fn (array $attributes) => ['state' => 'hidden']);
    }
}
