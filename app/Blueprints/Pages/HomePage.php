<?php

declare(strict_types=1);

namespace App\Blueprints\Pages;

use App\Blueprints\KilnStreetSite;
use FilamentCraft\Sections\Builtin\CategoryGridSection;
use FilamentCraft\Sections\Builtin\ImageTextSection;
use FilamentCraft\Sections\Builtin\NewsletterSection;
use FilamentCraft\Sections\Builtin\ProductCarouselSection;
use FilamentCraft\Sections\Builtin\StoreHeroSection;
use FilamentCraft\Sections\Builtin\TestimonialsSection;

final class HomePage extends Page
{
    public function name(): string
    {
        return 'Home';
    }

    public function slug(): string
    {
        return 'home';
    }

    public function description(): string
    {
        return 'Handmade stoneware mugs, bowls, plates and vases, thrown and fired in a small Bristol pottery.';
    }

    public function isHomepage(): bool
    {
        return true;
    }

    public function sections(): array
    {
        return [
            $this->section(StoreHeroSection::class, [
                'eyebrow' => 'Thrown in Bristol',
                'heading' => 'Stoneware for the table you already have.',
                'subheading' => 'Mugs, bowls and plates made on the wheel in our Stokes Croft studio, glazed in small batches and fired twice. No two come out quite the same.',
                'primary_label' => 'Shop the pottery',
                'primary_url' => '/shop',
                'secondary_label' => 'Visit the studio',
                'secondary_url' => '/studio',
                'image' => $this->image('throwing'),
                'image_2' => 'products/tide-mug.jpg',
                'image_3' => 'products/tenmoku-bowl.jpg',
                'layout' => 'collage',
            ], $this->blocks('perk', [
                ['icon' => 'heroicon-o-truck', 'label' => 'Free UK delivery over £'.KilnStreetSite::FREE_OVER],
                ['icon' => 'heroicon-o-banknotes', 'label' => 'Pay when it arrives'],
                ['icon' => 'heroicon-o-arrow-path', 'label' => '30 days to change your mind'],
            ])),

            $this->section(CategoryGridSection::class, [
                'eyebrow' => '',
                'heading' => 'Shop by shape',
                'intro' => '',
                'columns' => '4',
                'limit' => 4,
            ]),

            $this->section(ProductCarouselSection::class, [
                'eyebrow' => '',
                'heading' => 'The ones people come back for',
                'intro' => 'Our steadiest shapes. We throw them every week, so they are rarely out for long.',
                'view_all_label' => 'Shop everything',
                'collection' => 'featured',
                'limit' => 8,
            ]),

            $this->section(ImageTextSection::class, [
                'eyebrow' => 'The studio',
                'title' => 'Every piece goes through the kiln twice.',
                'body' => 'Once to around 1000 °C to harden the clay, then again at 1280 °C once it is glazed. The second firing takes a day to climb and two days to cool, and we only open the kiln when it drops below 100 °C. Rush it and the glaze cracks.',
                'url' => '/studio',
                'label' => 'How a mug is made',
                'image' => $this->image('kiln-glow'),
                'media_placement' => 'start',
                'align' => 'start',
            ], scheme: KilnStreetSite::CELADON),

            $this->section(ProductCarouselSection::class, [
                'eyebrow' => '',
                'heading' => 'Out of the last firing',
                'intro' => 'New glazes and shapes from the kiln we opened this month.',
                'view_all_label' => 'See what is new',
                'collection' => 'new',
                'limit' => 8,
            ]),

            $this->section(TestimonialsSection::class, [
                'eyebrow' => '',
                'title' => 'From people who use them every day.',
                'intro' => '',
                'layout' => 'cards',
                'align' => 'start',
            ], $this->blocks('quote', [
                ['quote' => 'I bought one Tide mug three years ago. There are now six in the cupboard and nobody in the house will drink from anything else.', 'name' => 'Owen Pritchard', 'role' => 'Cardiff'],
                ['quote' => 'The pasta bowls are exactly the right depth. Wide enough for a proper portion, deep enough that nothing slides off.', 'name' => 'Arjun Mehta', 'role' => 'Bath'],
                ['quote' => 'One bowl arrived chipped. They had a new one in the post the next morning and told me to keep the old one for the garden.', 'name' => 'Samuel Okafor', 'role' => 'Leeds'],
            ])),

            $this->section(NewsletterSection::class, [
                'heading' => 'Hear when the kiln opens.',
                'subheading' => 'We fire every six weeks. One short email when a firing comes out, a day before it goes on the shop.',
                'placeholder' => 'you@example.com',
                'button_label' => 'Tell me',
                'success_message' => 'You are on the list. The next firing is due in a few weeks.',
            ]),
        ];
    }
}
