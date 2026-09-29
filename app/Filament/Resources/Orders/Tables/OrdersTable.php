<?php

declare(strict_types=1);

namespace App\Filament\Resources\Orders\Tables;

use App\Enums\OrderStatus;
use App\Models\Order;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('placed_at', 'desc')
            ->columns([
                TextColumn::make('number')->searchable()->weight('medium'),
                TextColumn::make('customer_name')
                    ->label('Customer')
                    ->searchable(['customer_name', 'customer_email'])
                    ->description(fn (Order $record): string => $record->city),
                TextColumn::make('status')->badge(),
                TextColumn::make('total')
                    ->money(config('store.currency.code'), divideBy: 100)
                    ->sortable(),
                TextColumn::make('placed_at')->label('Placed')->since()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options(OrderStatus::class),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
