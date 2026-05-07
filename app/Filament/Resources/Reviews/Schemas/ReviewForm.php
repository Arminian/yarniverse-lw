<?php

namespace App\Filament\Resources\Reviews\Schemas;

use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ReviewForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Review Moderation')
                    ->schema([
                        Toggle::make('is_approved')
                            ->label('Approve Review')
                            ->helperText('Approved reviews are visible on the website')
                            ->required(),
                    ])
            ]);
    }
}
