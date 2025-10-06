<?php

namespace App\Filament\Resources\Suppliers\Pages;

use App\Filament\GlobalActions\SecureDeleteAction;
use App\Filament\Resources\Suppliers\SupplierResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Tabs;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;

class EditSupplier extends EditRecord
{
    protected static string $resource = SupplierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // ViewAction::make()
            //     ->icon(Heroicon::OutlinedEye)
            //     ->label('View Supplier'),
            $this->getSaveFormAction()
                ->formId('form')
                ->icon(Heroicon::OutlinedCheckCircle)
                ->label('Save Supplier'),
            SecureDeleteAction::make()
                ->icon(Heroicon::OutlinedTrash),
            ForceDeleteAction::make()
                ->icon(Heroicon::OutlinedTrash),
            RestoreAction::make()
                ->icon(Heroicon::OutlinedArrowUturnLeft),
            $this->getCancelFormAction()
                ->icon(Heroicon::OutlinedXMark)
                ->label('Cancel'),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            // $this->getSaveFormAction()
            //     ->icon(Heroicon::OutlinedCheckCircle)
            //     ->label('Save Supplier'),
            // $this->getCancelFormAction()
            //     ->icon(Heroicon::OutlinedXMark)
            //     ->label('Cancel'),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);

        // Save supplier LOBs
        if (isset($data['supplierLobs'])) {
            $record->supplierLobs()->delete(); // Remove existing LOBs
            foreach ($data['supplierLobs'] as $lobData) {
                if (isset($lobData['lob_subcategory_id'])) {
                    foreach ($lobData['lob_subcategory_id'] as $subcategoryId) {
                        $record->supplierLobs()->create([
                            'supplier_id' => $record->id,
                            'lob_category_id' => $lobData['lob_category_id'],
                            'lob_subcategory_id' => $subcategoryId,
                        ]);
                    }
                } else {
                    $record->supplierLobs()->create([
                        'supplier_id' => $record->id,
                        'lob_category_id' => $lobData['lob_category_id'],
                    ]);
                }
            }
        }

        return $record;
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['supplierLobs'] = $this->record->supplierLobs()
            ->get()
            ->groupBy('lob_category_id')
            ->map(function ($items, $categoryId) {
                return [
                    'lob_category_id' => $categoryId,
                    'lob_subcategory_id' => $items->pluck('lob_subcategory_id')->toArray(),
                ];
            })
            ->values()
            ->toArray();
        return $data;
    }
}
