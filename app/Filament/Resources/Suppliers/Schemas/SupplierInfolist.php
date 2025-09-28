<?php

namespace App\Filament\Resources\Suppliers\Schemas;

use App\Livewire\SupplierDocumentsTable;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Hugomyb\FilamentMediaAction\Actions\MediaAction;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class SupplierInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Business Information')
                    ->description('Below is the information about your business profile. To make changes, click the "Manage Profile" button above.')
                    ->icon(Heroicon::InformationCircle)
                    ->schema([
                        TextEntry::make('business_name')
                            ->extraAttributes(['style' => 'font-size: 1.5rem;'])
                            ->icon(Heroicon::BuildingOffice2)
                            ->color('primary')
                            ->size(TextSize::Large)
                            ->weight(FontWeight::Black)
                            ->columnSpan(2),
                        TextEntry::make('owner_name')
                            ->color('info')
                            ->label('Owner Name')
                            ->icon(Heroicon::User)
                            ->weight(FontWeight::Bold),
                        TextEntry::make('supplier_type')
                            ->color('info')
                            ->label('Supplier Type')
                            ->icon(Heroicon::Briefcase)
                            ->weight(FontWeight::Bold)
                            ->formatStateUsing(fn ($record) => $record->supplier_type->getLabel()),
                        TextEntry::make('email')
                            ->icon(Heroicon::Envelope)
                            ->label('Email Address')
                            ->color('primary'),
                        TextEntry::make('website')
                            ->icon(Heroicon::GlobeAlt)
                            ->label('Website')
                            ->color('primary'),
                        TextEntry::make('mobile_number')
                            ->icon(Heroicon::DevicePhoneMobile)
                            ->prefix('+63 ')
                            ->label('Mobile Number')
                            ->color('primary'),
                        TextEntry::make('landline_number')
                            ->icon(Heroicon::Phone)
                            ->label('Landline Number')
                            ->color('primary'),
                    ])
                    ->collapsible()
                    ->columns(2),
                Section::make('Line of Business')
                    ->description('Below are the line of business details you have provided. To make changes, click the "Manage Profile" button above.')
                    ->icon(Heroicon::Briefcase)
                    ->collapsed()
                    ->schema([
                        TextEntry::make('business_name')
                            ->formatStateUsing(function ($state): string {
                                return 'No records available';
                            })
                            ->size(TextSize::Medium)
                            ->alignCenter()
                            ->hidden(fn ($record) => count($record->supplierLobs) > 0)
                            ->hiddenLabel()
                            ->icon(Heroicon::OutlinedXCircle)
                            ->color('gray')
                            ->columnSpan(2),
                        RepeatableEntry::make('supplierLobs')
                            ->hiddenLabel()
                            ->grid(2)
                            ->columnSpanFull()
                            ->hidden(fn ($record) => count($record->supplierLobs) === 0)
                            ->state(function ($record) {
                                $data['supplierLobs'] = $record->supplierLobs()
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
                                return $data['supplierLobs'];
                            })
                            ->schema([
                                TextEntry::make('lobCategory')
                                    ->belowContent(fn ($state) => $state->description)
                                    ->hiddenLabel()
                                    ->formatStateUsing(fn ($state) => $state->title)
                                    ->weight(FontWeight::Bold)
                                    ->icon(Heroicon::OutlinedTag)
                                    ->color('primary')
                                    ->size(TextSize::Medium),
                                TextEntry::make('lob_subcategories_list')
                                    ->listWithLineBreaks()
                                    ->bulleted()
                                    ->badge()
                                    ->hiddenLabel()
                                    ->color('info'),
                            ])
                    ])
                    ->collapsible(),
                Section::make('Address Information')
                    ->description('Below is the address information for your business. You can add multiple addresses if needed.')
                    ->icon(Heroicon::Map)
                    ->collapsed()
                    ->schema([
                        TextEntry::make('business_name')
                            ->formatStateUsing(function ($state): string {
                                return 'No records available';
                            })
                            ->size(TextSize::Medium)
                            ->alignCenter()
                            ->hidden(fn ($record) => count($record->addresses) > 0)
                            ->hiddenLabel()
                            ->icon(Heroicon::OutlinedXCircle)
                            ->color('gray')
                            ->columnSpan(2),
                        RepeatableEntry::make('addresses')
                            ->hiddenLabel()
                            ->hidden(fn ($record) => count($record->addresses) === 0)
                            ->grid(2)
                            ->schema([
                                TextEntry::make('line_1')
                                    ->hiddenLabel()
                                    ->formatStateUsing(function ($state, $record) {
                                        $parts = [$state];
                                        if (!empty($record->line_2)) {
                                            $parts[] = $record->line_2;
                                        }
                                        $parts[] = $record->barangay->name;
                                        $parts[] = $record->municipality->name;
                                        $parts[] = $record->province->name;
                                        $parts[] = $record->zip_code;
                                        $parts[] = $record->country;
                                        return implode(', ', $parts);
                                    })
                                    ->icon(Heroicon::MapPin)
                                    // ->size(TextSize::Large)
                                    ->weight(FontWeight::Bold)
                                    ->color('primary'),
                                TextEntry::make('line_1')
                                    ->label('Status')
                                    ->badge()
                                    ->state(function ($record) {
                                        return $record->statuses()->latest()->first()->status ?? 'Pending for Validation';
                                    })
                                    ->color(function ($record) {
                                        $status = $record->statuses()->latest()->first()->status ?? 'Pending for Validation';
                                        return match ($status) {
                                            'Validated' => 'success',
                                            'Rejected' => 'danger',
                                            'Pending for Validation' => 'warning',
                                            default => 'secondary',
                                        };
                                    })
                                    ->icon(Heroicon::Clock),
                                TextEntry::make('line_1')
                                    ->label('Last Status Updated')
                                    ->icon(Heroicon::CalendarDays)
                                    ->iconColor('primary')
                                    ->formatStateUsing(function ($record) {
                                        $status = $record->statuses()->latest()->first();
                                        return $status ? $status->status_date->format('F j, Y, g:i a') : $record->created_at->format('F j, Y, g:i a');
                                    })
                                    ->color('primary'),
                                TextEntry::make('site_image.file_path')
                                    ->hiddenLabel()
                                    ->formatStateUsing(fn () => '')
                                    ->placeholder('No image available')
                                    ->belowContent(function () {
                                        return [
                                            MediaAction::make('storefront')
                                                ->hidden(fn ($state) => !$state)
                                                ->icon(Heroicon::OutlinedPhoto)
                                                ->color('primary')
                                                ->modalAlignment(Alignment::Center)
                                                ->label('View storefront image')
                                                ->button()
                                                ->modalWidth(Width::FourExtraLarge)
                                                ->media(function ($state) {
                                                    return '/' . $state;
                                                }),
                                        ];
                                    })
                            ])
                    ])
                    ->collapsible(),
                Section::make('Documents')
                    ->description('Below are the documents you have uploaded for your business. You can add or update documents as needed.')
                    ->icon(Heroicon::DocumentText)
                    ->collapsed()
                    ->schema([
                        TextEntry::make('business_name')
                            ->formatStateUsing(function ($state): string {
                                return 'No records available';
                            })
                            ->size(TextSize::Medium)
                            ->alignCenter()
                            ->hidden(fn ($record) => count($record->attachments) > 0)
                            ->hiddenLabel()
                            ->icon(Heroicon::OutlinedXCircle)
                            ->color('gray')
                            ->columnSpan(2),
                        Livewire::make(SupplierDocumentsTable::class, [
                            'supplier' => $schema->getRecord(),
                        ]),
                    ])
                    ->collapsible(),
            ])->columns(1);
    }
}
