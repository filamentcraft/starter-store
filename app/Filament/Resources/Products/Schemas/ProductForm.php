<?php

declare(strict_types=1);

namespace App\Filament\Resources\Products\Schemas;

use App\Filament\Forms\MoneyInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Group::make([
                    Section::make()
                        ->columns(2)
                        ->schema([
                            TextInput::make('name')
                                ->required()
                                ->maxLength(160)
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (string $operation, ?string $state, Set $set) => $operation === 'create' ? $set('slug', Str::slug((string) $state)) : null),
                            TextInput::make('slug')
                                ->required()
                                ->alphaDash()
                                ->unique(ignoreRecord: true)
                                ->helperText('The product lives at /products/{slug}.'),
                            Textarea::make('description')
                                ->rows(5)
                                ->columnSpanFull(),
                        ]),
                    Section::make('Photos')
                        ->schema([
                            FileUpload::make('images')
                                ->hiddenLabel()
                                ->image()
                                ->multiple()
                                ->reorderable()
                                ->panelLayout('grid')
                                ->disk('public')
                                ->directory('products')
                                ->maxFiles(6)
                                ->helperText('The first photo is the one on product cards.'),
                        ]),
                    Section::make('Options')
                        ->schema([
                            TagsInput::make('variants')
                                ->label('Choices')
                                ->placeholder('Add a glaze or size')
                                ->helperText('Customers pick one of these on the product page.'),
                            KeyValue::make('details')
                                ->keyLabel('Detail')
                                ->valueLabel('Value')
                                ->addActionLabel('Add detail'),
                        ]),
                ])->columnSpan(['lg' => 2]),

                Group::make([
                    Section::make('Price and stock')
                        ->schema([
                            MoneyInput::make('price')->required(),
                            MoneyInput::make('compare_at_price')
                                ->label('Was')
                                ->helperText('Shows a sale badge when higher than the price.'),
                            TextInput::make('stock')->numeric()->integer()->minValue(0)->default(0)->required(),
                            TextInput::make('sku')->label('SKU')->unique(ignoreRecord: true),
                        ]),
                    Section::make('Placement')
                        ->schema([
                            Select::make('category_id')
                                ->relationship('category', 'name')
                                ->preload()
                                ->searchable(),
                            Toggle::make('is_published')->label('On sale')->default(true),
                            Toggle::make('is_featured')->label('Featured on the homepage'),
                            Toggle::make('is_new')->label('New arrival'),
                        ]),
                ])->columnSpan(['lg' => 1]),
            ]);
    }
}
