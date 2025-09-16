<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Icons\Heroicon;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Tabs;
use Filament\Tables\Columns\TextColumn;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Add new User')
                ->icon(Heroicon::OutlinedPlus)
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Users')
                ->icon(Heroicon::Users)
                ->badgeColor('info')
                ->badge(User::query()->count()),
            'suppliers' => Tab::make('Suppliers')
                ->icon(Heroicon::BuildingStorefront)
                ->badge(User::query()->where('role', 'supplier')->count())
                ->badgeColor('info')
                ->query(fn ($query) => $query->where('role', 'supplier')),
            'twgs' => Tab::make('TWG Members')
                ->icon(Heroicon::Calculator)
                ->badge(User::query()->where('role', 'twg')->count())
                ->badgeColor('info')
                ->query(fn ($query) => $query->where('role', 'twg')),
            'end-users' => Tab::make('End Users')
                ->icon(Heroicon::UserCircle)
                ->badge(User::query()->where('role', 'end-user')->count())
                ->badgeColor('info')
                ->query(fn ($query) => $query->where('role', 'end-user')),
        ];
    }

    public function getTabsContentComponent(): Component
    {
        $tabs = $this->getCachedTabs();

        return Tabs::make()
            ->livewireProperty('activeTab')
            ->contained(false)
            ->tabs($tabs)
            ->extraAttributes(['style' => 'display: block;'])
            ->hidden(empty($tabs));
    }
}
