<?php

declare(strict_types=1);

namespace App\Blueprints\Pages;

use FilamentCraft\Sections\Builtin\FaqSection;
use FilamentCraft\Sections\Builtin\ProductDetailSection;

final class ProductPage extends Page
{
    public function name(): string
    {
        return 'Product';
    }

    public function slug(): string
    {
        return 'product';
    }

    public function description(): string
    {
        return 'Handmade stoneware from Kiln Street, Bristol.';
    }

    public function sections(): array
    {
        return [
            $this->section(ProductDetailSection::class, [
                'back_label' => 'Back to the shop',
                'add_label' => 'Add to basket',
                'show_related' => true,
                'related_heading' => 'Goes well with',
                'preview_slug' => 'tide-mug',
            ]),

            $this->section(FaqSection::class, [
                'eyebrow' => '',
                'title' => 'Good to know',
                'intro' => '',
                'align' => 'start',
            ], $this->blocks('item', [
                ['question' => 'Will it look exactly like the photo?', 'answer' => 'Close, not identical. Glazes pool and break differently on every piece, so yours will have its own markings.'],
                ['question' => 'Dishwasher and microwave?', 'answer' => 'Both are fine for everything except the vases. Hand-washing will keep the glaze glossy for longer.'],
                ['question' => 'When will it arrive?', 'answer' => 'We pack on Tuesdays and Fridays. Most UK orders arrive two to three working days later.'],
            ])),
        ];
    }
}
