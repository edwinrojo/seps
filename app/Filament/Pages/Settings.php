<?php

namespace App\Filament\Pages;

use App\Enums\UserRole;
use App\Livewire\ListDocumentType;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Icons\Heroicon;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;

class Settings extends Page
{
    protected string $view = 'filament.pages.settings';

    protected static ?string $title = 'System Settings';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

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
                    ])
            ]);
    }
}
