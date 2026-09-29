<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Builders\ProductBuilder;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseEloquentBuilder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'category_id', 'name', 'slug', 'sku', 'description', 'price', 'compare_at_price', 'stock',
    'images', 'variants', 'details', 'is_published', 'is_featured', 'is_new', 'position',
])]
#[UseEloquentBuilder(ProductBuilder::class)]
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return Attribute<bool, never>
     */
    protected function inStock(): Attribute
    {
        return Attribute::get(fn (): bool => $this->stock > 0);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'compare_at_price' => 'integer',
            'stock' => 'integer',
            'images' => 'array',
            'variants' => 'array',
            'details' => 'array',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'is_new' => 'boolean',
        ];
    }
}
