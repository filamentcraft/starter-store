<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Blueprints\KilnStreetSite;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    private const ORDERS = [
        [OrderStatus::Pending, 0, ['tide-mug' => 2, 'everyday-bowl' => 2]],
        [OrderStatus::Pending, 1, ['moon-bottle' => 1]],
        [OrderStatus::Confirmed, 2, ['dinner-plate' => 4, 'side-plate' => 4]],
        [OrderStatus::Confirmed, 3, ['brushed-teapot' => 1, 'ash-espresso-cups' => 1]],
        [OrderStatus::Shipped, 6, ['pasta-bowl' => 2]],
        [OrderStatus::Shipped, 8, ['dune-mugs' => 1, 'speckle-tumbler' => 2]],
        [OrderStatus::Delivered, 13, ['tenmoku-bowl' => 1, 'charcoal-rice-bowl' => 2]],
        [OrderStatus::Delivered, 19, ['nesting-bowls' => 1]],
        [OrderStatus::Delivered, 24, ['ring-vase' => 1, 'tide-mug' => 1]],
        [OrderStatus::Cancelled, 27, ['handled-vase' => 1]],
    ];

    private const CUSTOMERS = [
        ['Priya Shah', 'priya.shah@example.com', '07700 900418', '41 Cotham Brow', 'Bristol', 'BS6 6AS'],
        ['Callum Reid', 'callum.reid@example.com', '07700 900233', '8 Marchmont Crescent', 'Edinburgh', 'EH9 1HN'],
        ['Hannah Doyle', 'hannah.doyle@example.com', '07700 900871', '17 Pontcanna Street', 'Cardiff', 'CF11 9HQ'],
        ['Tom Okafor', 'tom.okafor@example.com', '07700 900156', '3 Albert Road', 'Manchester', 'M19 2EQ'],
        ['Megan Price', 'megan.price@example.com', '07700 900642', '22 Walcot Street', 'Bath', 'BA1 5BG'],
        ['Daniel Kowalski', 'daniel.kowalski@example.com', '07700 900389', '56 Chapel Allerton Road', 'Leeds', 'LS7 3HL'],
        ['Aisha Rahman', 'aisha.rahman@example.com', '07700 900727', '9 Stoke Newington Church Street', 'London', 'N16 0AR'],
        ['Owen Pritchard', 'owen.pritchard@example.com', '07700 900514', '14 St David\'s Hill', 'Exeter', 'EX4 4DR'],
        ['Fiona MacLeod', 'fiona.macleod@example.com', '07700 900905', '30 Hyndland Road', 'Glasgow', 'G12 9UP'],
        ['Sam Whitfield', 'sam.whitfield@example.com', '07700 900067', '5 Kingsdown Parade', 'Bristol', 'BS6 5UD'],
    ];

    public function run(): void
    {
        if (Order::query()->exists()) {
            return;
        }

        $products = Product::query()->get()->keyBy('slug');
        $freeOver = (int) KilnStreetSite::FREE_OVER * 100;
        $flat = (int) KilnStreetSite::SHIPPING * 100;

        foreach (self::ORDERS as $index => [$status, $daysAgo, $lines]) {
            [$name, $email, $phone, $address, $city, $postal] = self::CUSTOMERS[$index];

            $items = collect($lines)->map(fn (int $quantity, string $slug): array => [
                'product_id' => $products[$slug]->getKey(),
                'name' => $products[$slug]->name,
                'unit_price' => $products[$slug]->price,
                'quantity' => $quantity,
                'line_total' => $products[$slug]->price * $quantity,
            ])->values();

            $subtotal = (int) $items->sum('line_total');
            $shipping = $subtotal >= $freeOver ? 0 : $flat;

            Order::factory()
                ->status($status)
                ->create([
                    'customer_name' => $name,
                    'customer_email' => $email,
                    'customer_phone' => $phone,
                    'address' => $address,
                    'city' => $city,
                    'postal' => $postal,
                    'subtotal' => $subtotal,
                    'shipping' => $shipping,
                    'total' => $subtotal + $shipping,
                    'placed_at' => now()->subDays($daysAgo)->subHours(3),
                ])
                ->items()
                ->createMany($items->all());
        }
    }
}
