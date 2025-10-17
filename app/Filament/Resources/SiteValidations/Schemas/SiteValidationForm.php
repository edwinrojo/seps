<?php

namespace App\Filament\Resources\SiteValidations\Schemas;

use App\Models\Address;
use App\Models\ValidationPurpose;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
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
                        DateTimePicker::make('validation_date')
                            ->label('Validation Date & Time')
                            ->helperText('Select the date and time when the site validation took place.')
                            ->seconds(false)
                            ->required(),
                        Select::make('validation_purpose_id')
                            ->options(function () {
                                $validationPurposes = ValidationPurpose::all();
                                // add description to next line via HtmlString
                                return $validationPurposes->pluck('purpose', 'id')->mapWithKeys(function ($title, $id) use ($validationPurposes) {
                                    $description = $validationPurposes->where('id', $id)->first()->description;
                                    return [$id => '<b>'.$title.'</b>' . ($description ? "<span style='display: block;' class='text-sm text-gray-500'>$description</span>" : '')];
                                });
                            })
                            ->helperText('Select the purpose of the site validation.')
                            ->multiple()
                            ->allowHtml()
                            ->searchable()
                            ->preload()
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
                    ])
            ]);
    }
}
