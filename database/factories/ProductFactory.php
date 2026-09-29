<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        $name = Str::title($this->faker->unique()->words(2, true));

        return [
            'category_id' => Category::factory(),
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->sentence(12),
            'price' => $this->faker->numberBetween(1500, 9000),
            'stock' => $this->faker->numberBetween(5, 30),
            'images' => [],
            'variants' => [],
            'details' => [],
            'is_published' => true,
        ];
    }

    public function soldOut(): static
    {
        return $this->state(['stock' => 0]);
    }
}
