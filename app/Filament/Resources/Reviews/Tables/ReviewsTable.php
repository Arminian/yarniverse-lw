<?php

namespace App\Filament\Resources\Reviews\Tables;

use App\Filament\Resources\Customers\CustomerResource;
use App\Filament\Resources\Orders\OrderResource;
use App\Filament\Resources\Products\ProductResource;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ReviewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')
                    ->searchable()
                    ->url(fn($record) => $record->product ?
                        ProductResource::getUrl('edit', [$record->product]) : null)
                    ->weight('bold')
                    ->sortable(),
                TextColumn::make('customer.name')
                    ->searchable()
                    ->url(fn($record) => $record->customer ?
                        CustomerResource::getUrl('edit', [$record->customer]) : null)
                    ->weight('bold')
                    ->sortable(),
                TextColumn::make('order.name')
                    ->searchable()
                    ->url(fn($record) => $record->order ?
                        OrderResource::getUrl('edit', [$record->order]) : null)
                    ->weight('bold')
                    ->sortable(),
                TextColumn::make('rating')
                    ->formatStateUsing(fn($state) => str_repeat('⭐', $state))
                    ->color('warning')
                    ->sortable(),
                TextColumn::make('title')
                    ->limit(60)
                    ->searchable(),
                TextColumn::make('comment')
                    ->limit(150)
                    ->wrap()
                    ->searchable(),
                IconColumn::make('is_purchase_verified')
                    ->label('Verified')
                    ->boolean(),
                IconColumn::make('is_approved')
                    ->label('Approved')
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
            ->filters([
                TernaryFilter::make('is_purchase_verified')
                    ->label('Verify Status')
                    ->boolean()
                    ->trueLabel('Verified')
                    ->falseLabel('Unverified')
                    ->native(false),
                TernaryFilter::make('is_approved')
                    ->label('Approvement Status')
                    ->boolean()
                    ->trueLabel('Approved')
                    ->falseLabel('Inapproved')
                    ->native(false),
            ])
            ->recordActions([
                Action::make('approve')
                    ->icon('phosphor-cloud-check-fill')
                    ->color('success')
                    ->action(fn($record) => $record->update(['is_approved' => true]))
                    ->visible(fn($record) => !$record->is_approved)
                    ->requiresConfirmation(),
                Action::make('reject')
                    ->icon('phosphor-cloud-check')
                    ->color('danger')
                    ->action(fn($record) => $record->update(['is_approved' => false]))
                    ->visible(fn($record) => $record->is_approved)
                    ->requiresConfirmation(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('approve')
                        ->icon('phosphor-cloud-check-fill')
                        ->color('success')
                        ->action(fn($record) => $record->update(['is_approved' => true]))
                        ->requiresConfirmation(),
                    BulkAction::make('reject')
                        ->icon('phosphor-cloud-check')
                        ->color('danger')
                        ->action(fn($record) => $record->update(['is_approved' => false]))
                        ->requiresConfirmation(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
