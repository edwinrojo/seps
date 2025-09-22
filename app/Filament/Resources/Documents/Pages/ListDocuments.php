<?php

namespace App\Filament\Resources\Documents\Pages;

use App\Filament\Resources\Documents\DocumentResource;
use App\Models\Document;
use App\Models\DocumentType;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Icons\Heroicon;

class ListDocuments extends ListRecords
{
    protected static string $resource = DocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon(Heroicon::OutlinedPlus),
        ];
    }

    public function getTabs(): array
    {
        $tabs = [
            'all' => Tab::make('All Documents')
                ->icon(Heroicon::DocumentText)
                ->badgeColor('info')
                ->badge(Document::query()->count()),
        ];

        // Document types
        $documentTypes = DocumentType::all();
        foreach ($documentTypes as $documentType) {
            $tabs[$documentType->id] = Tab::make($documentType->title)
                ->icon(Heroicon::DocumentText)
                ->badge(Document::query()->where('document_type_id', $documentType->id)->count())
                ->badgeColor('info')
                ->query(fn ($query) => $query->where('document_type_id', $documentType->id));
        }
        return $tabs;
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
