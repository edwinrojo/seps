<?php

namespace App\Filament\Supplier\Schemas;

use App\Enums\ProcType;
use App\Models\Barangay;
use App\Models\Municipality;
use App\Models\Province;
use Dom\Text;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\Alignment;
use Filament\Support\Icons\Heroicon;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class AddressInformation
{
    public static function getSchema(): array
    {
        return [
            Repeater::make('addresses')
                ->label('Business Addresses')
                ->schema([
                    Hidden::make('id'),
                    TextInput::make('label')
                        ->label('Address Label')
                        ->placeholder('e.g., Head Office, Warehouse')
                        ->required()
                        ->maxLength(100),
                    TextInput::make('line_1')
                        ->label('Address Line 1')
                        ->placeholder('e.g., 123 Main St')
                        ->required()
                        ->maxLength(255)
                        ->columnSpan(2),
                    TextInput::make('line_2')
                        ->label('Address Line 2')
                        ->placeholder('e.g., Suite 100')
                        ->maxLength(255)
                        ->columnSpan(2),
                    Select::make('province_id')
                        ->label('Province')
                        ->searchable()
                        ->required()
                        ->options(fn () => Province::pluck('name', 'id')->toArray())
                        ->reactive()
                        ->afterStateUpdated(fn ($state, callable $set) => $set('municipality', null)),
                    Select::make('municipality_id')
                        ->label('Municipality/City')
                        ->searchable()
                        ->required()
                        ->options(fn (callable $get) => $get('province_id') ? Municipality::where('province_id', $get('province_id'))->pluck('name', 'id')->toArray() : [])
                        ->reactive()
                        ->afterStateUpdated(fn ($state, callable $set) => $set('barangay_id', null)),
                    Select::make('barangay_id')
                        ->label('Barangay')
                        ->searchable()
                        ->required()
                        ->options(fn (callable $get) => $get('municipality_id') ? Barangay::where('municipality_id', $get('municipality_id'))->pluck('name', 'id')->toArray() : []),
                    TextInput::make('country')
                        ->default('Philippines')
                        ->label('Country')
                        ->placeholder('e.g., Philippines')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('zip_code')
                        ->label('ZIP Code')
                        ->placeholder('e.g., 8000')
                        ->required()
                        ->maxLength(10),
                    FileUpload::make('site_image.file_path')
                        ->disk('public')
                        ->label('Site Image')
                        ->imageEditor()
                        ->image()
                        ->getUploadedFileNameForStorageUsing(
                            fn (TemporaryUploadedFile $file): string => (string) str($file->getClientOriginalName())
                                ->prepend((string) now()->format('YmdHis') . '_')
                        )
                        ->directory('site_images')
                        ->maxSize(2048)
                        ->moveFiles()
                        ->imagePreviewHeight('200'),
                ])
                ->collapsed()
                ->columns(2)
                ->collapsible()
                ->deletable(false)
                ->addActionAlignment(Alignment::Start)
                ->addActionLabel('Add Address')
                ->itemLabel(function ($uuid, $component) {
                    $keys = array_keys($component->getState());
                    $index = array_search($uuid, $keys);
                    return 'Address ' . ($index + 1); // Returns 1-based index
                })

        ];
    }
}
