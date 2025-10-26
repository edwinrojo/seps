<?php

namespace App\Filament\Resources\SiteValidations\Schemas;

use App\Models\Address;
use App\Models\ValidationPurpose;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class SiteValidationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Site Validation Form')
                    ->description('Please provide the necessary information for the site validation.')
                    ->icon(Heroicon::DocumentText)
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Select::make('supplier_id')
                            ->relationship('supplier', 'business_name')
                            ->helperText('Select the supplier for whom the site validation is being conducted.')
                            ->searchable()
                            ->reactive()
                            ->afterStateUpdated(fn (callable $set) => $set('address_id', null))
                            ->preload()
                            ->required(),
                        Select::make('address_id')
                            ->label('Address')
                            ->helperText('Select the address of the supplier being validated.')
                            ->options(function (callable $get) {
                                $supplierId = $get('supplier_id');
                                if (!$supplierId) {
                                    return [];
                                }

                                $addresses = Address::where('supplier_id', $supplierId)->get();
                                $options = [];
                                foreach ($addresses as $address) {
                                    $options[$address->id] = $address->getFullAddressAttribute();
                                }
                                return $options;
                            })
                            ->native(false)
                            ->required(),
                        FileUpload::make('site_images')
                            ->disk('public')
                            ->label('Site Images')
                            ->imageEditor()
                            ->multiple()
                            ->image()
                            ->getUploadedFileNameForStorageUsing(
                                fn (TemporaryUploadedFile $file): string => (string) str($file->getClientOriginalName())
                                    ->prepend((string) now()->format('YmdHis') . '_')
                            )
                            ->helperText('Upload images taken during the site validation. You can upload multiple images.')
                            ->directory('site_images')
                            ->maxSize(2048)
                            ->moveFiles()
                            ->imagePreviewHeight('200'),
                        Textarea::make('remarks')
                            ->label('Remarks')
                            ->helperText('Provide any additional remarks or observations from the site validation.')
                            ->rows(3)
                            ->maxLength(1000),
                        DateTimePicker::make('validation_date')
                            ->label('Validation Date & Time')
                            ->helperText('Select the date and time when the site validation took place.')
                            ->seconds(false)
                            ->required(),
                    ]),
                Section::make('Validation Purposes')
                    ->description('Please provide the purpose of the site validation.')
                    ->icon(Heroicon::DocumentText)
                    ->columnSpanFull()
                    ->schema([
                        Repeater::make('validation_purposes')
                            ->hiddenLabel()
                            ->addActionLabel('Add Purpose')
                            ->grid(2)
                            ->reorderable(false)
                            ->itemLabel(fn (array $state): ?string => $state['validation_purpose_id'] ? ValidationPurpose::find($state['validation_purpose_id'])->purpose : null)
                            ->schema([
                                Select::make('validation_purpose_id')
                                    ->label('Purpose')
                                    ->options(function (callable $get, callable $set, $state) {
                                        $validationPurposes = ValidationPurpose::all();

                                        // Get all currently selected validation purpose IDs from other repeater items
                                        $allValidationPurposes = $get('../../validation_purposes') ?? [];
                                        $selectedIds = collect($allValidationPurposes)
                                            ->pluck('validation_purpose_id')
                                            ->filter()
                                            ->toArray();

                                        // Remove current item's selection from the exclusion list
                                        if ($state) {
                                            $selectedIds = array_filter($selectedIds, fn($id) => $id !== $state);
                                        }

                                        return $validationPurposes->pluck('purpose', 'id')->mapWithKeys(function ($title, $id) use ($validationPurposes, $selectedIds) {
                                            // Skip if this option is already selected in another repeater item
                                            if (in_array($id, $selectedIds)) {
                                                return [];
                                            }

                                            $description = $validationPurposes->where('id', $id)->first()->description;
                                            return [$id => '<b class="text-primary-600">'.$title.'</b>' . ($description ? "<span style='display: block;' class='text-sm text-gray-500'>$description</span>" : '')];
                                        });
                                    })
                                    ->allowHtml()
                                    ->searchable()
                                    ->preload()
                                    ->reactive()
                                    ->required(),
                                Radio::make('status')
                                    ->label('Address Status')
                                    ->hidden(function (callable $get){
                                        $validation_purpose = ValidationPurpose::find($get('validation_purpose_id'));
                                        return $validation_purpose ? !$validation_purpose->is_iv : false;
                                    })
                                    ->belowLabel('The purpose you selected is included in the criteria to determine supplier eligibility. Choose approve if the address meets the criteria, or reject if it does not.')
                                    ->options([
                                        'approve' => 'Approve',
                                        'reject' => 'Reject'
                                    ])
                                    ->required()
                                    ->inline(),
                                Textarea::make('status_remarks')
                                    ->label('Remarks')
                                    ->hidden(function (callable $get){
                                        $validation_purpose = ValidationPurpose::find($get('validation_purpose_id'));
                                        return $validation_purpose ? !$validation_purpose->is_iv : false;
                                    })
                                    ->helperText('Provide remarks regarding the address status.')
                                    ->rows(3)
                                    ->maxLength(1000),
                            ]),
                    ]),
            ]);
    }
}
