<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('first_name')
                    ->required(),
                TextInput::make('last_name')
                    ->required(),
                TextInput::make('middle_name'),
                TextInput::make('suffix'),
                Select::make('role')
                    ->searchable()
                    ->required()
                    ->native(false)
                    ->options([
                        'administrator' => 'Admin',
                        'user' => 'User',
                        'supplier' => 'Supplier',
                        'twg' => 'TWG'
                    ]),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('contact_number')
                    ->prefix('+63')
                    ->mask('999-999-9999')
                    ->placeholder('912-345-6789')
                    ->required(),
                ToggleButtons::make('status')
                    ->label('Account Status')
                    ->grouped()
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive'
                    ])
                    ->colors([
                        'active' => 'success',
                        'inactive' => 'danger'
                    ])
                    ->icons([
                        'active' => Heroicon::CheckCircle,
                        'inactive' => Heroicon::XCircle
                    ])
            ]);
    }
}
