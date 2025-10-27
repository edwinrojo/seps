<?php

namespace App\Filament\Pages;

use App\Enums\NavigationGroup;
use App\Models\Document;
use App\Settings\SiteSettings;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class SystemSettings extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

    protected static string $settings = SiteSettings::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Administration;

    protected static ?int $navigationSort = 4;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('lob_reference_document')
                    ->label('Line of Business Reference Document')
                    ->options(function () {
                        $documents = Document::all();
                        return $documents->pluck('title', 'id')->mapWithKeys(function ($title, $id) use ($documents) {
                            $description = $documents->where('id', $id)->first()->description;
                            return [$id => '<b class="text-primary-600">'.$title.'</b>' . ($description ? "<span style='display: block;' class='text-sm text-gray-500'>$description</span>" : '')];
                        });
                    })
                    ->allowHtml()
                    ->searchable()
                    ->required(),
            ]);
    }

    public static function canAccess(): bool
    {
        return request()->user()->role === \App\Enums\UserRole::Administrator;
    }

    public function canEdit(): bool
    {
        return request()->user()->role === \App\Enums\UserRole::Administrator;
    }
}
