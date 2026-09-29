<?php

declare(strict_types=1);

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Notifications\OrderReceived;
use Filament\Notifications\DatabaseNotification;
use FilamentCraft\Commerce\ShippingSignature;
use FilamentCraft\Models\Site;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

beforeEach(function (): void {
    Storage::fake('public');
    Notification::fake();

    $this->seed();
    $this->store = Site::query()->sole()->slug;
});

function checkout(string $store, int $flat = 500, int $threshold = 6000): array
{
    return [
        'name' => 'Owen Pritchard',
        'email' => 'owen@example.com',
        'phone' => '07700 900123',
        'address' => '12 Clive Street',
        'city' => 'Cardiff',
        'postal' => 'CF11 7JA',
        'country' => 'United Kingdom',
        'shipping_flat' => $flat,
        'free_threshold' => $threshold,
        'shipping_sig' => ShippingSignature::sign($flat, $threshold, $store),
        'store' => $store,
        'return' => '/checkout',
    ];
}

it('turns a basket into an order and takes the items out of stock', function (): void {
    $this->post(route('filamentcraft.cart.add'), ['slug' => 'tide-mug', 'qty' => 2, 'variant' => 'Oat', 'store' => $this->store, 'return' => '/products/tide-mug'])
        ->assertRedirect('/products/tide-mug');

    $this->get('/checkout')->assertOk()->assertSee('You pay the courier when it arrives')->assertSee('Tide mug');

    $placed = $this->post(route('filamentcraft.cart.checkout'), checkout($this->store))
        ->assertRedirectContains('/checkout?placed=');

    $this->get($placed->headers->get('Location'))->assertSee('Thank you. Your order is in.');

    $order = Order::query()->with('items')->latest('id')->firstOrFail();

    expect($order->status)->toBe(OrderStatus::Pending)
        ->and($order->customer_name)->toBe('Owen Pritchard')
        ->and($order->subtotal)->toBe(6400)
        ->and($order->shipping)->toBe(0)
        ->and($order->total)->toBe(6400)
        ->and($order->items->sole()->variant)->toBe('Oat')
        ->and(Product::query()->where('slug', 'tide-mug')->value('stock'))->toBe(12);

    Notification::assertSentTo($order, OrderReceived::class);
    Notification::assertSentTo(User::query()->sole(), DatabaseNotification::class);
});

it('charges flat shipping under the free delivery threshold', function (): void {
    $this->post(route('filamentcraft.cart.add'), ['slug' => 'speckle-tumbler', 'store' => $this->store]);
    $this->post(route('filamentcraft.cart.checkout'), checkout($this->store));

    $order = Order::query()->latest('id')->firstOrFail();

    expect($order->shipping)->toBe(500)->and($order->total)->toBe(2900);
});

it('rejects a checkout whose shipping was tampered with', function (): void {
    $this->post(route('filamentcraft.cart.add'), ['slug' => 'speckle-tumbler', 'store' => $this->store]);

    $this->post(route('filamentcraft.cart.checkout'), [...checkout($this->store), 'shipping_flat' => 0]);

    expect(Order::query()->where('customer_email', 'owen@example.com')->exists())->toBeFalse();
});

it('will not sell more than is in stock', function (): void {
    $this->post(route('filamentcraft.cart.add'), ['slug' => 'handled-vase', 'qty' => 5, 'store' => $this->store]);
    $this->post(route('filamentcraft.cart.checkout'), checkout($this->store));

    expect(Order::query()->where('customer_email', 'owen@example.com')->exists())->toBeFalse()
        ->and(Product::query()->where('slug', 'handled-vase')->value('stock'))->toBe(2);
});

it('refuses sold-out products at the basket', function (): void {
    $this->post(route('filamentcraft.cart.add'), ['slug' => 'night-mug', 'store' => $this->store])
        ->assertSessionHas('fc_cart_error');
});
