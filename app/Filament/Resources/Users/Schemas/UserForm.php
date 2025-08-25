<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

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
                TextInput::make('role')
                    ->required(),
                TextInput::make('name'),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                DateTimePicker::make('email_verified_at'),
                TextInput::make('contact_number')
                    ->required(),
                TextInput::make('password')
                    ->password()
                    ->required(),
                TextInput::make('avatar'),
                TextInput::make('status')
                    ->required()
                    ->default('active'),
                Textarea::make('app_authentication_secret')
                    ->columnSpanFull(),
                Textarea::make('app_authentication_recovery_codes')
                    ->columnSpanFull(),
                Toggle::make('has_email_authentication')
                    ->required(),
            ]);
    }
}
