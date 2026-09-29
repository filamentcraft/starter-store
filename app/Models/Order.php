<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\OrderStatus;
use App\Models\Builders\OrderBuilder;
use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseEloquentBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'number', 'status', 'customer_name', 'customer_email', 'customer_phone', 'address', 'city',
    'postal', 'country', 'note', 'subtotal', 'shipping', 'total', 'placed_at',
])]
#[UseEloquentBuilder(OrderBuilder::class)]
class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory, Notifiable;

    /**
     * @return HasMany<OrderItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function routeNotificationForMail(): string
    {
        return $this->customer_email;
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'subtotal' => 'integer',
            'shipping' => 'integer',
            'total' => 'integer',
            'placed_at' => 'datetime',
        ];
    }
}
