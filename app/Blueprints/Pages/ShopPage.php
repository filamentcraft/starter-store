<?php

declare(strict_types=1);

namespace App\Blueprints\Pages;

use FilamentCraft\Sections\Builtin\ProductListingSection;

final class ShopPage extends Page
{
    public function name(): string
    {
        return 'Shop';
    }

    public function slug(): string
    {
        return 'shop';
    }

    public function description(): string
    {
        return 'Every mug, bowl, plate and vase we have in stock, with prices and glazes.';
    }

    public function sections(): array
    {
        return [
            $this->section(ProductListingSection::class, [
                'eyebrow' => '',
                'heading' => 'The pottery',
                'show_filters' => true,
                'show_price_filter' => true,
                'columns' => '3',
            ]),
        ];
    }
}
