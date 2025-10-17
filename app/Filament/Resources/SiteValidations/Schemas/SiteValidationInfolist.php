<?php

namespace App\Filament\Resources\SiteValidations\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class SiteValidationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Validation Information')
                    ->description('Detailed information about the site validation.')
                    ->icon(Heroicon::Map)
                    ->columnSpanFull()
                    ->inlineLabel()
                    ->schema([
                        TextEntry::make('supplier.business_name')
                            ->icon(Heroicon::BuildingOffice2)
                            ->label('Supplier')
                            ->color('primary')
                            ->weight('bold'),
                        TextEntry::make('address.full_address')
                            ->icon(Heroicon::MapPin)
                            ->label('Address')
                            ->color('gray')
                            ->weight('bold'),
                        TextEntry::make('validation_date')
                            ->icon(Heroicon::CalendarDays)
                            ->label('Validation Date')
                            ->dateTime('F d, Y h:i A'),
                        TextEntry::make('validation_purpose.purpose')
                            ->color('info')
                            ->weight('bold')
                            ->belowContent(fn ($record) => $record->validation_purpose?->description)
                            ->label('Purpose'),
                        TextEntry::make('remarks')
                            ->label('Remarks'),
                        TextEntry::make('twg.user.name')
                            ->icon(Heroicon::User)
                            ->color('info')
                            ->weight('bold')
                            ->label('Validated By'),
                    ]),
                Section::make('Site Images')
                    ->description('Images captured during the site validation.')
                    ->icon(Heroicon::Photo)
                    ->columnSpanFull()
                    ->schema([
                        RepeatableEntry::make('site_images')
                            ->hiddenLabel()
                            ->contained(false)
                            ->grid(2)
                            ->schema([
                                ImageEntry::make('file_path')
                                    ->imageWidth('100%')
                                    ->imageHeight('auto')
                                    ->hiddenLabel()
                                    ->disk('public'),
                            ]),
                    ]),
            ]);
    }
}
