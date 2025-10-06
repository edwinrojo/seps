<?php

namespace App\Filament\Resources\LOBCategories;

use App\Enums\NavigationGroup;
use App\Filament\Resources\LOBCategories\Pages\CreateLOBCategory;
use App\Filament\Resources\LOBCategories\Pages\EditLOBCategory;
use App\Filament\Resources\LOBCategories\Pages\ListLOBCategories;
use App\Filament\Resources\LOBCategories\RelationManagers\LobSubcategoriesRelationManager;
use App\Filament\Resources\LOBCategories\Schemas\LOBCategoryForm;
use App\Filament\Resources\LOBCategories\Tables\LOBCategoriesTable;
use App\Models\LOBCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class LOBCategoryResource extends Resource
{
    protected static ?string $model = LOBCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static string | UnitEnum | null $navigationGroup = NavigationGroup::Administration;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $navigationLabel = 'Line of Business';

    protected static ?string $modelLabel = 'Line of Business Category';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return LOBCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LOBCategoriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            LobSubcategoriesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLOBCategories::route('/'),
            'create' => CreateLOBCategory::route('/create'),
            'edit' => EditLOBCategory::route('/{record}/edit'),
        ];
    }
}
