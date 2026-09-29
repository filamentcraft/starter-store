<?php

declare(strict_types=1);

namespace App\Blueprints\Pages;

use App\Blueprints\KilnStreetSite;
use FilamentCraft\Sections\Builtin\CheckoutSection;

final class CheckoutPage extends Page
{
    public function name(): string
    {
        return 'Checkout';
    }

    public function slug(): string
    {
        return 'checkout';
    }

    public function description(): string
    {
        return 'Check out and pay on delivery.';
    }

    public function sections(): array
    {
        return [
            $this->section(CheckoutSection::class, [
                'heading' => 'Checkout',
                'subheading' => 'Tell us where to send it. You pay the courier when it arrives, so there is no card to enter.',
                'place_label' => 'Place order',
                'cod_label' => 'Pay on delivery',
                'cod_help' => 'Cash or card to the courier at your door.',
                'success_title' => 'Thank you. Your order is in.',
                'success_body' => 'A confirmation is on its way to your inbox. We pack on Tuesdays and Fridays and will email again when it leaves the studio.',
                'shipping_flat' => KilnStreetSite::SHIPPING,
                'free_threshold' => KilnStreetSite::FREE_OVER,
            ]),
        ];
    }
}
