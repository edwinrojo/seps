<?php

namespace App\Filament\Resources\SiteValidations\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SiteValidationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('ID'),
                TextEntry::make('supplier_id'),
                TextEntry::make('address_id'),
                TextEntry::make('twg_id'),
                TextEntry::make('validation_date')
                    ->dateTime(),
                TextEntry::make('remarks')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
