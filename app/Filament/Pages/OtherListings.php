<?php

namespace App\Filament\Pages;

use App\Enums\NavigationGroup;
use App\Enums\UserRole;
use App\Livewire\ListDocumentType;
use App\Livewire\ListOffice;
use App\Livewire\ListValidationPurpose;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Icons\Heroicon;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use UnitEnum;

class OtherListings extends Page
{
    protected string $view = 'filament.pages.settings';

    protected static ?string $title = 'Other Listings';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedListBullet;

    protected static string | UnitEnum | null $navigationGroup = NavigationGroup::Administration;

    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        return request()->user()->role === UserRole::Administrator;
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Tabs')->contained(false)
                    ->contained(true)
                    ->vertical()
                    ->tabs([
                        Tab::make('document-types')->label('Document Types')
                            ->icon(Heroicon::OutlinedDocumentText)
                            ->schema([
                                Livewire::make(ListDocumentType::class)->key('list-document-type'),
                            ]),
                        Tab::make('offices')->label('Offices')
                            ->icon(Heroicon::OutlinedBuildingOffice2)
                            ->schema([
                                Livewire::make(ListOffice::class)->key('list-office'),
                            ]),
                        Tab::make('validation-purposes')->label('Validation Purposes')
                            ->icon(Heroicon::OutlinedCheckBadge)
                            ->schema([
                                Livewire::make(ListValidationPurpose::class)->key('list-validation-purpose'),
                            ]),
                    ])
            ]);
    }
}
