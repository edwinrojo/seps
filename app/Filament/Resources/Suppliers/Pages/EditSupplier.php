<?php

namespace App\Filament\Resources\Suppliers\Pages;

use App\Filament\Actions\SecureDeleteAction;
use App\Filament\Resources\Suppliers\SupplierResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditSupplier extends EditRecord
{
    protected static string $resource = SupplierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->icon(Heroicon::OutlinedEye)
                ->label('View Supplier'),
            SecureDeleteAction::make()
                ->icon(Heroicon::OutlinedTrash),
            ForceDeleteAction::make()
                ->icon(Heroicon::OutlinedTrash),
            RestoreAction::make()
                ->icon(Heroicon::OutlinedArrowUturnLeft),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()
                ->icon(Heroicon::OutlinedCheckCircle)
                ->label('Save Supplier'),
            $this->getCancelFormAction()
                ->icon(Heroicon::OutlinedXMark)
                ->label('Cancel'),
        ];
    }
}
