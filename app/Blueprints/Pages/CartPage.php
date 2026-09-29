<?php

declare(strict_types=1);

namespace App\Blueprints\Pages;

use App\Blueprints\KilnStreetSite;
use FilamentCraft\Sections\Builtin\CartSection;

final class CartPage extends Page
{
    public function name(): string
    {
        return 'Basket';
    }

    public function slug(): string
    {
        return 'cart';
    }

    public function description(): string
    {
        return 'Your Kiln Street basket.';
    }

    public function sections(): array
    {
        return [
            $this->section(CartSection::class, [
                'heading' => 'Your basket',
                'continue_label' => 'Keep looking',
                'checkout_label' => 'Checkout',
                'empty_title' => 'Nothing in here yet',
                'empty_body' => 'Mugs are a good place to start.',
                'shipping_flat' => KilnStreetSite::SHIPPING,
                'free_threshold' => KilnStreetSite::FREE_OVER,
            ]),
        ];
    }
}
