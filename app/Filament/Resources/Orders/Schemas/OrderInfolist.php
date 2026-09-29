<?php

declare(strict_types=1);

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\Order;
use App\Models\OrderItem;
use App\Support\Money;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        $currency = (string) config('store.currency.code');

        return $schema
            ->columns(3)
            ->components([
                Group::make([
                    Section::make('Items')
                        ->schema([
                            RepeatableEntry::make('items')
                                ->hiddenLabel()
                                ->table([
                                    RepeatableEntry\TableColumn::make('Item'),
                                    RepeatableEntry\TableColumn::make('Qty')->alignEnd(),
                                    RepeatableEntry\TableColumn::make('Price')->alignEnd(),
                                    RepeatableEntry\TableColumn::make('Total')->alignEnd(),
                                ])
                                ->schema([
                                    TextEntry::make('name')
                                        ->formatStateUsing(fn (string $state, OrderItem $record): string => filled($record->variant) ? "{$state} · {$record->variant}" : $state),
                                    TextEntry::make('quantity')->alignEnd(),
                                    TextEntry::make('unit_price')->money($currency, divideBy: 100)->alignEnd(),
                                    TextEntry::make('line_total')->money($currency, divideBy: 100)->alignEnd(),
                                ]),
                            TextEntry::make('subtotal')->money($currency, divideBy: 100)->inlineLabel(),
                            TextEntry::make('shipping')
                                ->formatStateUsing(fn (int $state): string => $state === 0 ? 'Free' : Money::format($state))
                                ->inlineLabel(),
                            TextEntry::make('total')
                                ->money($currency, divideBy: 100)
                                ->weight('bold')
                                ->helperText('Paid in cash on delivery.')
                                ->inlineLabel(),
                        ]),
                    Section::make('Note from the customer')
                        ->visible(fn (Order $record): bool => filled($record->note))
                        ->schema([
                            TextEntry::make('note')->hiddenLabel(),
                        ]),
                ])->columnSpan(['lg' => 2]),

                Group::make([
                    Section::make('Order')
                        ->schema([
                            TextEntry::make('status')->badge(),
                            TextEntry::make('placed_at')->dateTime('j M Y, H:i'),
                        ]),
                    Section::make('Customer')
                        ->schema([
                            TextEntry::make('customer_name')->label('Name'),
                            TextEntry::make('customer_email')->label('Email')->copyable(),
                            TextEntry::make('customer_phone')->label('Phone')->copyable(),
                            TextEntry::make('address')
                                ->label('Deliver to')
                                ->formatStateUsing(fn (Order $record): string => collect([$record->address, trim("{$record->postal} {$record->city}"), $record->country])->filter()->implode("\n"))
                                ->extraAttributes(['class' => 'whitespace-pre-line']),
                        ]),
                ])->columnSpan(['lg' => 1]),
            ]);
    }
}
