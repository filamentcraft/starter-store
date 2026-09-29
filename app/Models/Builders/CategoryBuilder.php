<?php

declare(strict_types=1);

namespace App\Models\Builders;

use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends Builder<Category>
 */
final class CategoryBuilder extends Builder
{
    public function ordered(): static
    {
        return $this->orderBy('position')->orderBy('id');
    }
}
