<?php

declare(strict_types=1);

namespace App\Models\Builders;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends Builder<Product>
 */
final class ProductBuilder extends Builder
{
    public function published(): static
    {
        return $this->where('is_published', true);
    }

    public function featured(): static
    {
        return $this->where('is_featured', true);
    }

    public function newArrivals(): static
    {
        return $this->where('is_new', true);
    }

    public function soldOut(): static
    {
        return $this->where('stock', 0);
    }

    public function lowStock(int $below = 5): static
    {
        return $this->whereBetween('stock', [1, $below - 1]);
    }

    public function inCategory(string $slug): static
    {
        return $this->whereRelation('category', 'slug', $slug);
    }

    public function search(string $term): static
    {
        return $this->where(fn (self $match): self => $match
            ->whereLike('name', "%{$term}%")
            ->orWhereLike('description', "%{$term}%"));
    }

    public function ordered(): static
    {
        return $this->orderBy('position')->orderBy('id');
    }
}
