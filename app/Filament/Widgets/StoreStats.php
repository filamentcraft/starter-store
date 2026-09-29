<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Filament\Resources\Orders\OrderResource;
use App\Filament\Resources\Products\ProductResource;
use App\Models\Order;
use App\Models\Product;
use App\Support\Money;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StoreStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $recent = Order::query()->counted()->placedSince(now()->subDays(30));

        return [
            Stat::make('Orders to handle', Order::query()->open()->count())
                ->description('Waiting to be confirmed or shipped')
                ->url(OrderResource::getUrl()),
            Stat::make('Sales, last 30 days', Money::format((int) (clone $recent)->sum('total')))
                ->description($recent->count().' orders, cancellations excluded'),
            Stat::make('Sold out', Product::query()->published()->soldOut()->count())
                ->description(Product::query()->published()->lowStock()->count().' more with fewer than 5 left')
                ->url(ProductResource::getUrl()),
        ];
    }
}
