<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->firstName().' '.$this->faker->lastName();

        return [
            'number' => config('store.order_prefix').'-'.Str::upper(Str::random(6)),
            'status' => OrderStatus::Pending,
            'customer_name' => $name,
            'customer_email' => Str::slug($name, '.').'@example.com',
            'customer_phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->streetAddress(),
            'city' => $this->faker->randomElement(['Bristol', 'Bath', 'Cardiff', 'London', 'Manchester', 'Leeds', 'Exeter', 'Glasgow']),
            'postal' => $this->faker->postcode(),
            'country' => 'United Kingdom',
            'subtotal' => 0,
            'shipping' => 0,
            'total' => 0,
            'placed_at' => now(),
        ];
    }

    public function status(OrderStatus $status): static
    {
        return $this->state(['status' => $status]);
    }
}
