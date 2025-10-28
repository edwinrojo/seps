<?php

namespace App\Filament\Resources\Attachments\Pages;

use App\Enums\Status;
use App\Filament\Resources\Attachments\AttachmentResource;
use App\Models\Attachment;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Icons\Heroicon;

class ListAttachments extends ListRecords
{
    protected static string $resource = AttachmentResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }

    public function getTabs(): array
    {
        $tabs = [
            'all' => Tab::make('All Attachments')
                ->icon(Heroicon::PaperClip)
                ->badgeColor('info')
                ->badge(Attachment::query()->count()),
        ];

        // Document types
        $statuses = Status::cases();
        foreach ($statuses as $status) {
            $tabs[$status->value] = Tab::make($status->getLabel())
                ->icon(fn () => $status->getIcon())
                ->badge(function () use ($status) {
                    return Attachment::query()->whereHas('statuses', function ($query) use ($status) {
                        $query->where('status', $status->value)
                            ->whereRaw('statuses.id = (SELECT MAX(id) FROM statuses WHERE statuses.statusable_id = attachments.id)');
                    })->count();
                })
                ->badgeColor($status->getColor())
                ->query(fn ($query) => $query
                    ->whereHas('statuses', function ($query) use ($status) {
                        $query->where('status', $status->value)
                            ->whereRaw('statuses.id = (SELECT MAX(id) FROM statuses WHERE statuses.statusable_id = attachments.id)');
                    })
                );
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
