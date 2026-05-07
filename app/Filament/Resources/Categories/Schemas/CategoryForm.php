<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Category Info')
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
                            ->placeholder('Detailed summary')
                            ->rows(3),
                        FileUpload::make('image')
                            ->disk('public')
                            ->directory('categories')
                            ->downloadable(false)
                            ->imageEditor()
                            ->maxSize(2048)
                            ->image(),
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
                    
                Section::make('Meta Data')
                    ->schema([
                        TextInput::make('meta_title')
                            ->placeholder('Enter keywords'),
                        Textarea::make('meta_description')
                            ->placeholder('One sentence summary')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
