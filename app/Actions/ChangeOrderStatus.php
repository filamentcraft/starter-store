<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

final class ChangeOrderStatus
{
    public function handle(Order $order, OrderStatus $status): void
    {
        if ($order->status === OrderStatus::Cancelled || $order->status === $status) {
            return;
        }

        DB::transaction(function () use ($order, $status): void {
            if ($status === OrderStatus::Cancelled) {
                $order->items()->with('product')->get()
                    ->each(fn (OrderItem $item) => $item->product?->increment('stock', $item->quantity));
            }

            $order->update(['status' => $status]);
        });
    }
}
