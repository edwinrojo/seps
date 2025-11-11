<?php

namespace App\Filament\Pages\Schemas;

use App\Models\Attachment;
use App\Settings\SiteSettings;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\ViewField;
use App\Enums\Status as EnumsStatus;
use Filament\Forms\Components\RichEditor;

class LOBValidationsModalForm
{
    public static function configure(): array
    {
        return [
                ViewField::make('lob_listings')
                    ->view('filament.forms.components.supplier-lob-listings', function ($record) {
                        // create array of titles
                        $array = $record->supplierLobs()
                            ->get()
                            ->groupBy('lob_category_id')
                            ->map(function ($items, $categoryId) {
                                return [
                                    'lobCategory' => $items->first()->lobCategory,
                                    'lob_subcategories_list' => $items->pluck('lobSubcategory.title')->filter(),
                                ];
                            })
                            ->values()
                            ->toArray();

                        $lob_reference_document = app(SiteSettings::class)->lob_reference_document;
                        if ($lob_reference_document) {
                            $document = Attachment::where('supplier_id', $record->id)
                                ->where('document_id', $lob_reference_document)
                                ->first();
                        } else {
                            return;
                        }

                        return [
                            'lob_listings' => $array,
                            'reference_document' => $document ? ($document->is_validated ? $document : null) : null,
                        ];
                    })
                    ->label('Instructions'),
                ToggleButtons::make('status')
                    ->label('Status')
                    ->helperText('Choose the appropriate status for the Line of Business (LOB) validation. Approving the LOBs will mark the supplier\'s Line of Business as verified, while rejecting will require further action from the supplier.')
                    ->options(EnumsStatus::class)
                    ->inline()
                    ->required(),
                RichEditor::make('remarks')
                    ->label('Remarks')
                    ->helperText('Provide additional remarks or comments regarding the LOB validation decision. This information will be communicated to the supplier to inform them of the outcome and any necessary next steps.')
                    ->required()
                    ->toolbarButtons([
                        'italic',
                        'underline',
                        'strike',
                    ])
            ];
    }
}
