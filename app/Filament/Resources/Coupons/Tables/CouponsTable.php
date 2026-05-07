<?php

namespace App\Filament\Resources\Coupons\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class CouponsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->copyable()
                    ->weight('bold')
                    ->searchable(),
                TextColumn::make('type')
                    ->sortable()
                    ->badge()
                    ->colors([
                        'percentage' => 'info',
                        'fixed' => 'primary',
                    ])
                    ->searchable(),
                TextColumn::make('value')
                    ->sortable()
                    ->numeric()
                    ->weight('bold')
                    ->formatStateUsing(fn($record) =>
                        $record->type === 'percentage'
                        ? "{$record->value}%"
                        : '$' . number_format($record->value, 2)),
                TextColumn::make('minimum_order_value')
                    ->numeric()
                    ->badge()
                    ->color('success')
                    ->sortable(),
                TextColumn::make('maximum_discount')
                    ->numeric()
                    ->badge()
                    ->color('warning')
                    ->sortable(),
                TextColumn::make('usage_count')
                    ->numeric()
                    ->badge()
                    ->default(0)
                    ->color('warning'),
                TextColumn::make('usage_limit')
                    ->numeric()
                    ->badge()
                    ->color('danger')
                    ->sortable(),
                TextColumn::make('usage_limit_per_customer')
                    ->numeric()
                    ->badge()
                    ->color('danger')
                    ->sortable(),
                TextColumn::make('starts_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('ends_at')
                    ->dateTime()
                    ->color(fn($state) => $state?->isPast() ? 'danger' : 'gray')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('starts_at', 'asc')
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'fixed' => 'Fixed',
                        'percentage' => 'Percentage',
                    ])
                    ->multiple()
                    ->native(false),
                TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->boolean()
                    ->trueLabel('Active')
                    ->falseLabel('Inactive')
                    ->native(false),
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
