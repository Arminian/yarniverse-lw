<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\ToolbarButtonGroup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use function is_array;


class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Product info')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('General info')
                            ->icon('phosphor-info-bold')
                            ->schema([
                                Section::make('Product details')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('name')
                                            ->placeholder('Enter name')
                                            ->required(),
                                        TextInput::make('slug')
                                            ->visibleOn('edit')
                                            ->unique(ignoreRecord: true)
                                            ->readOnly()
                                            ->required(),
                                        TextInput::make('weight')
                                            ->placeholder('Enter weight (g)')
                                            ->minValue(1)
                                            ->numeric(),
                                        Select::make('category_id')
                                            ->relationship('category', 'name')
                                            ->preload()
                                            ->searchable()
                                            ->required()
                                            ->createOptionModalHeading('Create Category')
                                            ->createOptionForm([
                                                TextInput::make('name')
                                                    ->placeholder('Enter name')
                                                    ->required(),
                                                TextInput::make('slug')
                                                    ->visibleOn('edit')
                                                    ->unique(ignoreRecord: true)
                                                    ->readOnly(),
                                            ]),
                                        Select::make('brand_id')
                                            ->relationship('brand', 'name')
                                            ->preload()
                                            ->searchable()
                                            ->default(null)
                                            ->createOptionModalHeading('Create Brand')
                                            ->createOptionForm([
                                                TextInput::make('name')
                                                    ->placeholder('Enter name')
                                                    ->required(),
                                                TextInput::make('slug')
                                                    ->visibleOn('edit')
                                                    ->unique(ignoreRecord: true)
                                                    ->readOnly(),
                                            ]),
                                    ]),
                                Section::make('Product description')
                                    ->schema([
                                        Textarea::make('short_description')
                                            ->placeholder('Enter short description')
                                            ->columnSpanFull(),
                                        RichEditor::make('description')
                                            ->toolbarButtons([
                                                ['bold', 'italic', 'underline', 'strike'],
                                                [ToolbarButtonGroup::make('Heading', ['h1', 'h2', 'h3'])->icon('fi-o-heading')],
                                                [ToolbarButtonGroup::make('Alignment', ['alignStart', 'alignCenter', 'alignEnd', 'alignJustify'])],
                                                ['blockquote', 'bulletList', 'orderedList'],
                                                ['undo', 'redo'],
                                            ])
                                            ->extraInputAttributes(['style' => 'min-height: 5rem; max-height: 40rem; overflow-y: auto;'])
                                            ->placeholder('Enter detailed description')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tab::make(label: 'Price & Stock')
                            ->icon('phosphor-money')
                            ->schema([
                                Section::make('Pricing')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('sku')
                                            ->label('SKU')
                                            ->placeholder('Enter SKU')
                                            ->default(fn() => 'SKU-' . strtoupper(Str::random(10)))
                                            ->helperText('Stock Keeping Unit')
                                            ->unique(ignoreRecord: true)
                                            ->required(),
                                        TextInput::make('price')
                                            ->required()
                                            ->numeric()
                                            ->minValue(0.01)
                                            ->step(0.01)
                                            ->placeholder('...')
                                            ->helperText('Price for selling')
                                            ->prefix('$'),
                                        TextInput::make('compare_price')
                                            ->numeric()
                                            ->minValue(0.01)
                                            ->step(0.01)
                                            ->placeholder('...')
                                            ->helperText('Price before discounts')
                                            ->prefix('$'),
                                        TextInput::make('cost_price')
                                            ->numeric()
                                            ->minValue(0.01)
                                            ->step(0.01)
                                            ->placeholder('...')
                                            ->helperText('Price from the vendor')
                                            ->prefix('$'),
                                    ]),
                                Section::make('Stock')
                                    ->columns(2)
                                    ->schema([
                                        Toggle::make('manage_stock')
                                            ->default(true)
                                            ->helperText('Enable stock management')
                                            ->live(),
                                        ToggleButtons::make('stock_status')
                                            ->options([
                                                'in_stock' => 'In Stock',
                                                'out_of_stock' => 'Out of Stock',
                                                'on_backorder' => 'On Backorder',
                                            ])
                                            ->grouped()
                                            ->required()
                                            ->default('in_stock'),
                                        TextInput::make('stock_quantity')
                                            ->required(fn(callable $get) => $get('manage_stock'))
                                            ->disabled(fn(callable $get) => !$get('manage_stock'))
                                            ->numeric()
                                            ->minValue(0)
                                            ->default(0),
                                        TextInput::make('low_stock_threshold')
                                            ->required()
                                            ->numeric()
                                            ->minValue(0)
                                            ->helperText('Get notified about low stock')
                                            ->default(10),
                                    ]),
                            ]),

                        Tab::make('Variants')
                            ->icon('phosphor-exclude-square')
                            ->schema([
                                Toggle::make('has_variants')
                                    ->label('Enable Variants')
                                    ->live()
                                    ->required(),
                                Section::make('Product Variants')
                                    ->description('Product variation, e.g. different size or color')
                                    ->schema([
                                        Repeater::make('variants')
                                            ->relationship('variants')
                                            ->schema([
                                                TextInput::make('name')
                                                    ->required()
                                                    ->placeholder('Enter e.g. red, blue, XL'),
                                                KeyValue::make('options'),
                                                TextInput::make('sku')
                                                    ->label('SKU')
                                                    ->placeholder('Enter SKU')
                                                    ->helperText('Stock Keeping Unit')
                                                    ->unique(ignoreRecord: true)
                                                    ->default(fn()
                                                        => 'VAR-' . strtoupper(Str::random(10)))
                                                    ->required(),

                                                TextInput::make('price')
                                                    ->required()
                                                    ->numeric()
                                                    ->minValue(0.01)
                                                    ->step(0.01)
                                                    ->placeholder('...')
                                                    ->helperText('Price for selling')
                                                    ->prefix('$'),
                                                TextInput::make('compare_price')
                                                    ->numeric()
                                                    ->minValue(0.01)
                                                    ->step(0.01)
                                                    ->placeholder('...')
                                                    ->helperText('Price before discounts')
                                                    ->prefix('$'),
                                                TextInput::make('cost_price')
                                                    ->numeric()
                                                    ->minValue(0.01)
                                                    ->step(0.01)
                                                    ->placeholder('...')
                                                    ->helperText('Price from the vendor')
                                                    ->prefix('$'),

                                                TextInput::make('stock_quantity')
                                                    ->numeric()
                                                    ->minValue(0)
                                                    ->default(0),
                                                ToggleButtons::make('stock_status')
                                                    ->options([
                                                        'in_stock' => 'In Stock',
                                                        'out_of_stock' => 'Out of Stock',
                                                        'on_backorder' => 'On Backorder',
                                                    ])
                                                    ->grouped()
                                                    ->required()
                                                    ->default('in_stock'),
                                                Toggle::make('is_active')
                                                    ->required()
                                                    ->default(true),
                                            ])
                                            ->columns(2)
                                            ->defaultItems(0)
                                            ->collapsible()
                                            ->itemLabel(fn(array $state): ?string => $state['name'] ?? null)
                                            ->addActionLabel('Add Variant'),
                                    ])
                                    ->visible(fn(callable $get) => $get('has_variants'))
                            ]),
                        Tab::make('Images')
                            ->icon('phosphor-image-square')
                            ->schema([
                                Section::make('Product Images')
                                    ->description('Upload product images; note that the first will be primary display image')
                                    ->schema([
                                        FileUpload::make('images')
                                            ->label('Product Images')
                                            ->multiple()
                                            ->image()
                                            ->directory('products')
                                            ->imageEditor()
                                            ->maxSize(2048)
                                            ->reorderable()
                                            ->columnSpanFull()
                                            ->helperText('Drag to reorder')
                                            ->saveRelationshipsUsing(function ($state, $record) {
                                                $record->images()->delete();

                                                if (is_array($state)) {
                                                    foreach ($state as $index => $imagePath) {
                                                        $record->images()->create([
                                                            'image_path' => $imagePath,
                                                            'is_primary' => $index === 0,
                                                            'sort_order' => $index
                                                        ]);
                                                    }
                                                }
                                            })
                                            ->dehydrated(false),
                                    ]),
                            ]),
                        Tab::make('Settings')
                            ->icon('phosphor-gear')
                            ->schema([
                                Section::make('Product status')
                                    ->schema([
                                        Toggle::make('is_active')
                                            ->label('Active')
                                            ->required()
                                            ->default(true),
                                        Toggle::make('is_featured')
                                            ->label('Featured')
                                            ->required(),
                                    ])
                            ]),
                        Tab::make(label: 'Meta Data')
                            ->icon('phosphor-magnifying-glass')
                            ->schema([
                                TextInput::make('meta_title')
                                    ->placeholder('Enter keywords'),
                                Textarea::make('meta_description')
                                    ->placeholder('Enter short description')
                                    ->columnSpanFull(),
                                TextEntry::make('views_count')
                                    ->label('Views: ')
                                    ->state(fn($record) => $record?->views_count ?? 0),
                                TextEntry::make('created_at') // diffForHumans will show text date, e.g. "an hour ago"
                                    ->label('Created: ')
                                    ->state(fn($record) => $record?->created_at?->diffForHumans() ?? '-'),
                            ])
                    ]),
            ]);
    }
}
