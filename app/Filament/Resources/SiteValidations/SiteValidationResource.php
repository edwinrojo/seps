<?php

namespace App\Filament\Resources\SiteValidations;

use App\Enums\NavigationGroup;
use App\Filament\Resources\SiteValidations\Pages\CreateSiteValidation;
use App\Filament\Resources\SiteValidations\Pages\EditSiteValidation;
use App\Filament\Resources\SiteValidations\Pages\ListSiteValidations;
use App\Filament\Resources\SiteValidations\Pages\ViewSiteValidation;
use App\Filament\Resources\SiteValidations\Schemas\SiteValidationForm;
use App\Filament\Resources\SiteValidations\Schemas\SiteValidationInfolist;
use App\Filament\Resources\SiteValidations\Tables\SiteValidationsTable;
use App\Models\SiteValidation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SiteValidationResource extends Resource
{
    protected static ?string $model = SiteValidation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRocketLaunch;

    protected static string | UnitEnum | null $navigationGroup = NavigationGroup::Suppliers;

    protected static ?string $recordTitleAttribute = 'purpose';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return SiteValidationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SiteValidationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SiteValidationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSiteValidations::route('/'),
            'create' => CreateSiteValidation::route('/create'),
            'edit' => EditSiteValidation::route('/{record}/edit'),
        ];
    }
}
