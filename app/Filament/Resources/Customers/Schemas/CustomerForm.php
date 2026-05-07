<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Customer Info')
                    ->schema([
                        TextInput::make('name')
                            ->placeholder('Enter full name')
                            ->required(),
                        TextInput::make('email')
                            ->label('Email address')
                            ->unique(ignoreRecord: true)
                            ->email()
                            ->placeholder('Enter email')
                            ->required(),
                        DateTimePicker::make('email_verified_at')
                            ->default(Carbon::now()->format('m/d/Y H:i:s')),
                        TextInput::make('phone')
                            ->tel()
                            ->placeholder('Enter phone number'),
                        DatePicker::make('date_of_birth')
                            ->native(false)
                            ->firstDayOfWeek('1')
                            ->displayFormat('d/m/Y')
                            ->placeholder('dd/mm/YYYY'),
                        Select::make(name: 'gender')
                            ->options([
                                'male' => 'Male',
                                'female' => 'Female',
                                'non-binary' => 'Non-binary'
                            ])
                            ->native(false),
                        Toggle::make('is_active')
                            ->label('Active')
                            ->required(),
                    ]),

                Section::make('Password Info')
                    ->schema([
                        TextInput::make('password')
                            ->password()
                            ->dehydrateStateUsing(fn($state) =>
                                filled($state) ? Hash::make($state) : null)
                            ->dehydrated(fn($state) => filled($state))
                            ->placeholder('Enter password')
                            ->revealable()
                            ->required(fn(string $operation) => $operation === 'create')
                            ->required(),
                        TextInput::make('password_confirmation')
                            ->password()
                            ->same('password')
                            ->dehydrated(false)
                            ->placeholder('Confirm password')
                            ->revealable()
                            ->required(fn(string $operation) => $operation === 'create'),
                    ])
            ]);
    }
}
