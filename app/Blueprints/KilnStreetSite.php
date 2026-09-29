<?php

declare(strict_types=1);

namespace App\Blueprints;

use App\Blueprints\Concerns\BuildsSections;
use App\Blueprints\Pages\CarePage;
use App\Blueprints\Pages\CartPage;
use App\Blueprints\Pages\CheckoutPage;
use App\Blueprints\Pages\HomePage;
use App\Blueprints\Pages\ProductPage;
use App\Blueprints\Pages\ShopPage;
use App\Blueprints\Pages\StudioPage;
use App\Support\SiteImages;
use FilamentCraft\Blueprints\BlueprintRegion;
use FilamentCraft\Blueprints\SiteBlueprint;
use FilamentCraft\Enums\RegionName;
use FilamentCraft\Sections\Builtin\BannerSection;
use FilamentCraft\Sections\Builtin\FooterSection;
use FilamentCraft\Sections\Builtin\HeaderSection;

final class KilnStreetSite extends SiteBlueprint
{
    use BuildsSections;

    public const SCHEME = 'kiln';

    public const CELADON = 'kiln-celadon';

    public const INK = 'kiln-ink';

    public const SHIPPING = '5';

    public const FREE_OVER = '60';

    public function name(): string
    {
        return 'Kiln Street';
    }

    public function pages(): array
    {
        return [
            HomePage::class,
            ShopPage::class,
            ProductPage::class,
            CartPage::class,
            CheckoutPage::class,
            StudioPage::class,
            CarePage::class,
        ];
    }

    public function regions(): array
    {
        return [
            BlueprintRegion::make(RegionName::Announcement)->sections([
                $this->section(BannerSection::class, [
                    'icon' => '',
                    'text' => 'Free UK delivery on orders over £'.self::FREE_OVER.'. Pay when it arrives.',
                    'cta_label' => 'How delivery works',
                    'cta_url' => '/care',
                    'style' => 'bar',
                    'tone' => 'brand',
                    'dismissible' => true,
                ], scheme: self::CELADON),
            ]),
            BlueprintRegion::make(RegionName::Header)->sections([
                $this->section(HeaderSection::class, [
                    'brand_text' => 'Kiln Street',
                    'cta_enabled' => true,
                    'cta_label' => 'Your basket',
                    'cta_url' => '/cart',
                    'show_locale_switcher' => false,
                    'sticky' => true,
                    'show_border' => true,
                ], $this->blocks('nav-link', [
                    ['label' => 'Shop all', 'url' => '/shop'],
                    ['label' => 'Tea & coffee', 'url' => '/shop?category=tea-and-coffee'],
                    ['label' => 'Bowls', 'url' => '/shop?category=bowls'],
                    ['label' => 'The studio', 'url' => '/studio'],
                    ['label' => 'Care & delivery', 'url' => '/care'],
                ])),
            ]),
            BlueprintRegion::make(RegionName::Footer)->sections([
                $this->section(FooterSection::class, [
                    'brand' => 'Kiln Street',
                    'description' => 'Stoneware thrown, glazed and fired in a small studio in Stokes Croft, Bristol.',
                    'copyright' => '© '.now()->year.' Kiln Street Pottery. 14 Picton Street, Bristol BS6 5QA.',
                ], $this->blocks('link', [
                    ['column' => 'Shop', 'label' => 'Tea & coffee', 'url' => '/shop?category=tea-and-coffee'],
                    ['column' => 'Shop', 'label' => 'Bowls', 'url' => '/shop?category=bowls'],
                    ['column' => 'Shop', 'label' => 'Plates', 'url' => '/shop?category=plates'],
                    ['column' => 'Shop', 'label' => 'Vases & bottles', 'url' => '/shop?category=vases'],
                    ['column' => 'Help', 'label' => 'Care & delivery', 'url' => '/care'],
                    ['column' => 'Help', 'label' => 'Visit the studio', 'url' => '/studio'],
                    ['column' => 'Help', 'label' => 'Your basket', 'url' => '/cart'],
                ]), scheme: self::INK),
            ]),
        ];
    }

    public function siteSettings(): array
    {
        return [
            'color_scheme' => self::SCHEME,
            'heading_font' => 'young-serif',
            'default_font' => 'karla',
            'button_radius' => 24,
            'seo' => [
                'description' => 'Handmade stoneware mugs, bowls, plates and vases from a small pottery in Bristol. Free UK delivery over £60.',
                'og_image' => SiteImages::path('green-set'),
            ],
            'schemes' => [
                self::SCHEME => $this->scheme([
                    'background' => '#f5f7f6',
                    'on-background' => '#1b201e',
                    'surface' => '#ffffff',
                    'on-surface' => '#1b201e',
                    'surface-alt' => '#e7edea',
                    'on-surface-alt' => '#34403b',
                    'primary' => '#2f5d50',
                    'on-primary' => '#ffffff',
                    'secondary' => '#1b201e',
                    'on-secondary' => '#ffffff',
                    'accent' => '#b64d24',
                    'on-accent' => '#ffffff',
                    'neutral' => '#5a6560',
                ]),
                self::CELADON => $this->scheme([
                    'background' => '#2f5d50',
                    'on-background' => '#ffffff',
                    'surface' => '#295247',
                    'on-surface' => '#ffffff',
                    'surface-alt' => '#23473d',
                    'on-surface-alt' => '#dcebe5',
                    'primary' => '#ffffff',
                    'on-primary' => '#2f5d50',
                    'secondary' => '#1b201e',
                    'on-secondary' => '#ffffff',
                    'accent' => '#f2b391',
                    'on-accent' => '#1b201e',
                    'neutral' => '#bcd3ca',
                ]),
                self::INK => $this->scheme([
                    'background' => '#151917',
                    'on-background' => '#eef2f0',
                    'surface' => '#1d2220',
                    'on-surface' => '#eef2f0',
                    'surface-alt' => '#262c29',
                    'on-surface-alt' => '#cfd8d4',
                    'primary' => '#9cc9b9',
                    'on-primary' => '#151917',
                    'secondary' => '#eef2f0',
                    'on-secondary' => '#151917',
                    'accent' => '#e98a5f',
                    'on-accent' => '#151917',
                    'neutral' => '#9aa6a1',
                ]),
            ],
        ];
    }

    /**
     * @param  array<string, string>  $colors
     * @return array<string, string>
     */
    private function scheme(array $colors): array
    {
        return [
            ...$colors,
            'on-neutral' => '#ffffff',
            'info' => '#2d5f8a',
            'on-info' => '#ffffff',
            'success' => '#2f6b3f',
            'on-success' => '#ffffff',
            'warning' => '#9a5a00',
            'on-warning' => '#ffffff',
            'danger' => '#b42318',
            'on-danger' => '#ffffff',
        ];
    }
}
