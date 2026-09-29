<?php

declare(strict_types=1);

use App\Actions\ChangeOrderStatus;
use App\Enums\OrderStatus;
use App\Filament\Resources\Orders\Pages\ViewOrder;
use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Filament\Widgets\LatestOrders;
use App\Filament\Widgets\StoreStats;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function (): void {
    Storage::fake('public');

    $this->seed();
    $this->actingAs(User::query()->where('email', 'admin@example.com')->sole());
});

it('shows the store at a glance on the dashboard', function (): void {
    $this->get('/admin')->assertOk();

    Livewire::test(StoreStats::class)->assertSee(['Orders to handle', 'Sold out']);
    Livewire::test(LatestOrders::class)->assertCanSeeTableRecords(Order::query()->latest('placed_at')->limit(6)->get());
});

it('saves prices entered in pounds as pence', function (): void {
    Livewire::test(CreateProduct::class)
        ->fillForm([
            'name' => 'Lidded jar',
            'slug' => 'lidded-jar',
            'price' => '38.50',
            'stock' => 6,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Product::query()->where('slug', 'lidded-jar')->value('price'))->toBe(3850);
});

it('filters products that are sold out', function (): void {
    Livewire::test(ListProducts::class)
        ->filterTable('sold_out')
        ->assertCanSeeTableRecords(Product::query()->soldOut()->get())
        ->assertCanNotSeeTableRecords(Product::query()->where('stock', '>', 0)->get());
});

it('moves an order along and puts stock back when it is cancelled', function (): void {
    $order = Order::query()->open()->inStatus(OrderStatus::Pending)->with('items.product')->firstOrFail();
    $item = $order->items->first();
    $before = $item->product->stock;

    Livewire::test(ViewOrder::class, ['record' => $order->getRouteKey()])
        ->callAction('confirmed')
        ->assertHasNoActionErrors();

    expect($order->refresh()->status)->toBe(OrderStatus::Confirmed);

    app(ChangeOrderStatus::class)->handle($order, OrderStatus::Cancelled);

    expect($order->refresh()->status)->toBe(OrderStatus::Cancelled)
        ->and($item->product->refresh()->stock)->toBe($before + $item->quantity);

    app(ChangeOrderStatus::class)->handle($order, OrderStatus::Shipped);

    expect($order->refresh()->status)->toBe(OrderStatus::Cancelled);
});
