<?php

declare(strict_types=1);

namespace App\Models\Builders;

use App\Enums\OrderStatus;
use App\Models\Order;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends Builder<Order>
 */
final class OrderBuilder extends Builder
{
    public function open(): static
    {
        return $this->whereIn('status', [OrderStatus::Pending, OrderStatus::Confirmed]);
    }

    public function inStatus(OrderStatus $status): static
    {
        return $this->where('status', $status);
    }

    public function placedSince(CarbonInterface $since): static
    {
        return $this->where('placed_at', '>=', $since);
    }

    public function counted(): static
    {
        return $this->whereNot('status', OrderStatus::Cancelled);
    }
}
