<?php

namespace App\Filament\Resources\Brands\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BrandForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Brand Info')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->placeholder('Enter name')
                            ->required(),
                        TextInput::make('slug')
                            ->visibleOn('edit')
                            ->unique(ignoreRecord: true)
                            ->readOnly(),
                        Textarea::make('description')
                            ->placeholder('Business description')
                            ->rows(3)
                            ->columnSpanFull(),
                        FileUpload::make('logo')
                            ->disk('public')
                            ->directory('brands')
                            ->maxSize(2048)
                            ->image(),
                        TextInput::make('website')
                            ->placeholder('https://mywebsite.com')
                            ->url(),
                    ]),
                Section::make('Display Settings')
                    ->schema([
                        Toggle::make('is_active')
                            ->required(),
                        TextInput::make('sort_order')
                            ->required()
                            ->numeric()
                            ->default(0),
                    ]),
            ]);
    }
}
