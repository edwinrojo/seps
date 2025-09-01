<?php

namespace App\Filament\Resources\Suppliers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class SupplierForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Business Information')
                    ->description('Please provide accurate and up-to-date information about the business.')
                    ->icon(Heroicon::InformationCircle)
                    ->schema([
                        TextInput::make('business_name')
                            ->label('Business Name')
                            ->placeholder('e.g., ABC Enterprises')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),
                        TextInput::make('email')
                            ->email()
                            ->label('Email Address')
                            ->placeholder('e.g., abc-enterprises@example.com')
                            ->belowContent('This email address will be used to contact the business for any important inquiries and updates.')
                            ->required()
                            ->maxLength(500)
                            ->columnSpan(2),
                        TextInput::make('website')
                            ->prefix('https://')
                            ->label('Website')
                            ->placeholder('e.g., www.abc-enterprises.com')
                            ->maxLength(500)
                            ->columnSpan(2),
                        TextInput::make('mobile_number')
                            ->label('Mobile Number')
                            ->prefix('+63')
                            ->mask('999-999-9999')
                            ->placeholder('912-345-6789')
                            ->required()
                            ->belowContent('This number will be used to contact the business for any important inquiries and updates.')
                            ->maxLength(255),
                        TextInput::make('landline_number')
                            ->label('Landline Number')
                            ->placeholder('e.g., (082) 123-4567')
                            ->maxLength(255),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
