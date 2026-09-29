<?php

declare(strict_types=1);

namespace App\Filament\Resources\Products\Tables;

use App\Models\Builders\ProductBuilder;
use App\Models\Product;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('position')
            ->defaultSort('position')
            ->defaultPaginationPageOption(25)
            ->columns([
                ImageColumn::make('images')
                    ->label('')
                    ->disk('public')
                    ->limit(1)
                    ->square(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Product $record): ?string => $record->category?->name),
                TextColumn::make('price')
                    ->money(config('store.currency.code'), divideBy: 100)
                    ->sortable(),
                TextColumn::make('stock')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state === 0 => 'danger',
                        $state < 5 => 'warning',
                        default => 'gray',
                    }),
                IconColumn::make('is_published')->label('On sale')->boolean(),
                IconColumn::make('is_featured')->label('Featured')->boolean()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')->relationship('category', 'name'),
                TernaryFilter::make('is_published')->label('On sale'),
                Filter::make('sold_out')
                    ->label('Sold out')
                    ->query(fn (ProductBuilder $query): ProductBuilder => $query->soldOut()),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
