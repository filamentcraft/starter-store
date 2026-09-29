<?php

declare(strict_types=1);

use App\Models\Product;
use FilamentCraft\Models\Site;
use FilamentCraft\Models\Template;
use Illuminate\Support\Facades\Storage;

beforeEach(function (): void {
    Storage::fake('public');

    $this->seed();
});

it('serves every storefront page', function (string $path, string $text): void {
    $this->get($path)->assertOk()->assertSee($text);
})->with([
    'home' => ['/', 'Stoneware for the table you already have.'],
    'shop' => ['/shop', 'Pasta bowl'],
    'product' => ['/products/brushed-teapot', 'A four-cup teapot'],
    'basket' => ['/cart', 'Your basket'],
    'studio' => ['/studio', 'How a mug is made.'],
    'care' => ['/care', 'Can it go in the dishwasher?'],
]);

it('filters the shop by category', function (): void {
    $this->get('/shop?category=plates')
        ->assertOk()
        ->assertSee('Dinner plate')
        ->assertDontSee('Brushed teapot');
});

it('returns 404 for products that do not exist or are hidden', function (): void {
    $this->get('/products/no-such-pot')->assertNotFound();

    Product::query()->where('slug', 'pasta-bowl')->update(['is_published' => false]);

    $this->get('/products/pasta-bowl')->assertNotFound();
});

it('marks sold-out products', function (): void {
    $this->get('/products/night-mug')->assertOk()->assertSee('Sold out');
});

it('seeds the catalog and the site once', function (): void {
    $this->seed();

    expect(Product::query()->count())->toBe(19)
        ->and(Site::query()->count())->toBe(1)
        ->and(Template::query()->count())->toBe(7);
});
