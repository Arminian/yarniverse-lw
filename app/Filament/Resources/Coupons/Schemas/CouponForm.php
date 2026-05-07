<?php

namespace App\Filament\Resources\Coupons\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;

class CouponForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Coupon info')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('code')
                            ->unique(ignoreRecord: true)
                            ->placeholder('Enter unique code')
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn($state, callable $set) =>
                                $set('code', strtoupper($state)))
                            ->required(),
                        Select::make('type')
                            ->options([
                                'percentage' => 'Percentage',
                                'fixed' => 'Fixed',
                            ])
                            ->required()
                            ->live()
                            ->default('percentage'),
                        TextInput::make('value')
                            ->required()
                            ->placeholder('Enter discount value')
                            ->minValue(0)
                            ->prefix(fn(callable $get) =>
                                $get('type') === 'fixed' ? '$' : null)
                            ->suffix(fn(callable $get) =>
                                $get('type') === 'percentage' ? '%' : null)
                            ->numeric(),
                        TextEntry::make('usage_count')
                            ->label('Usage: ')
                            ->badge()
                            ->color('warning')
                            ->size('medium')
                            ->icon('phosphor-warning-fill')
                            ->state(fn($record) => $record?->usage_count ?? 0),
                        Toggle::make('is_active')
                            ->label('Active')
                            ->required(),
                    ]),
                Section::make('Coupon Conditions')
                    ->columns(2)
                    ->schema([
                        TextInput::make('minimum_order_value')
                            ->prefix('$')
                            ->placeholder('...')
                            ->minValue(0.01)
                            ->numeric(),
                        TextInput::make('maximum_discount')
                            ->minValue(0)
                            ->maxValue(99)
                            ->postfix('%')
                            ->placeholder('...')
                            ->visible(fn($state, callable $get) =>
                                $get('type') === 'percentage')
                            ->numeric(),
                        TextInput::make('usage_limit')
                            ->minValue(1)
                            ->placeholder('...')
                            ->numeric(),
                        TextInput::make('usage_limit_per_customer')
                            ->minValue(1)
                            ->placeholder('...')
                            ->numeric(),
                    ]),
                Section::make('Discount Duration')
                    ->schema([
                        DateTimePicker::make('starts_at')
                            ->default(Carbon::now()->format('m/d/Y H:i:s')),
                        DateTimePicker::make('ends_at')
                            ->default(Carbon::now()->addMonths(2)->
                                format('m/d/Y H:i:s')),
                    ]),
            ]);
    }
}
