<?php

namespace App\Filament\Resources\SiteValidations\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SiteValidationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('supplier_id')
                    ->required(),
                TextInput::make('address_id')
                    ->required(),
                TextInput::make('twg_id')
                    ->required(),
                DateTimePicker::make('validation_date')
                    ->required(),
                Textarea::make('remarks')
                    ->columnSpanFull(),
            ]);
    }
}
