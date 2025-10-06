<?php

namespace App\Filament\Resources\SiteValidations\Schemas;

use App\Models\Address;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class SiteValidationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('supplier_id')
                    ->relationship('supplier', 'business_name')
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(fn (callable $set) => $set('address_id', null))
                    ->preload()
                    ->required(),
                Select::make('address_id')
                    ->label('Address')
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
                    ->seconds(false)
                    ->required(),
                TextInput::make('purpose')
                    ->maxLength(500)
                    ->required(),
                Textarea::make('remarks')
                    ->columnSpanFull(),
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
                    ->directory('site_images')
                    ->maxSize(2048)
                    ->moveFiles()
                    ->imagePreviewHeight('200'),
            ]);
    }
}
