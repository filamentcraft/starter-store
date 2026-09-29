<?php

declare(strict_types=1);

namespace App\Filament\Resources\Orders\Pages;

use App\Enums\OrderStatus;
use App\Filament\Resources\Orders\OrderResource;
use App\Models\Builders\OrderBuilder;
use App\Models\Order;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    public function getTabs(): array
    {
        return [
            'open' => Tab::make('To do')
                ->modifyQueryUsing(fn (OrderBuilder $query): OrderBuilder => $query->open())
                ->badge(Order::query()->open()->count()),
            'shipped' => Tab::make(OrderStatus::Shipped->getLabel())
                ->modifyQueryUsing(fn (OrderBuilder $query): OrderBuilder => $query->inStatus(OrderStatus::Shipped)),
            'all' => Tab::make('All orders'),
        ];
    }
}
