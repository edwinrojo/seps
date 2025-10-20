<?php

namespace App\Filament\Supplier\Actions;

use Illuminate\Validation\ValidationException;

class SupplierLOB
{
    public static function save($record, $data)
    {
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
    }
}
