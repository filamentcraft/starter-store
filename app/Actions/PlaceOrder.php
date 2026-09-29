<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\OrderStatus;
use App\Events\OrderPlaced;
use App\Models\Order;
use App\Models\Product;
use FilamentCraft\Commerce\Data\CustomerDetails;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class PlaceOrder
{
    /**
     * @param  list<array{slug: string, variant: string|null, qty: int, catalog?: string}>  $lines
     */
    public function handle(array $lines, CustomerDetails $customer): ?Order
    {
        $order = DB::transaction(function () use ($lines, $customer): ?Order {
            $items = [];

            foreach ($lines as $line) {
                $product = Product::query()->published()->where('slug', $line['slug'])->lockForUpdate()->first();
                $quantity = max(1, $line['qty']);

                if (! $product instanceof Product || $product->stock < $quantity) {
                    return null;
                }

                $product->decrement('stock', $quantity);

                $items[] = [
                    'product_id' => $product->getKey(),
                    'name' => $product->name,
                    'variant' => $line['variant'],
                    'unit_price' => $product->price,
                    'quantity' => $quantity,
                    'line_total' => $product->price * $quantity,
                ];
            }

            if ($items === []) {
                return null;
            }

            $subtotal = array_sum(array_column($items, 'line_total'));

            $order = Order::query()->create([
                'number' => $this->number(),
                'status' => OrderStatus::Pending,
                'customer_name' => $customer->name,
                'customer_email' => $customer->email,
                'customer_phone' => $customer->phone,
                'address' => $customer->address,
                'city' => $customer->city,
                'postal' => $customer->postal,
                'country' => $customer->country,
                'note' => $customer->note,
                'subtotal' => $subtotal,
                'shipping' => $customer->shippingMinor,
                'total' => $subtotal + $customer->shippingMinor,
                'placed_at' => now(),
            ]);

            $order->items()->createMany($items);

            return $order;
        });

        if ($order instanceof Order) {
            OrderPlaced::dispatch($order->load('items'));
        }

        return $order;
    }

    private function number(): string
    {
        do {
            $number = config('store.order_prefix').'-'.Str::upper(Str::random(6));
        } while (Order::query()->where('number', $number)->exists());

        return $number;
    }
}
