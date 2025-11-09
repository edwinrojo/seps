<?php

namespace App\Filament\Resources\Suppliers\Schemas;

use App\Enums\UserRole;
use App\Filament\GlobalActions\SiteImageAction;
use App\Filament\Supplier\Actions\SupplierLOB;
use App\Filament\Supplier\Schemas\AddressInformation;
use App\Filament\Supplier\Schemas\BusinessInformation;
use App\Filament\Supplier\Schemas\LineOfBusiness;
use App\Livewire\StatusView;
use App\Livewire\SupplierDocumentsTable;
use App\Models\Status;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
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
use Illuminate\Support\HtmlString;
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
                    ->collapsed()
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
                    ->afterHeader([
                        Action::make('manage_business_information')
                            ->label('Update')
                            ->icon(Heroicon::PencilSquare)
                            ->color('primary')
                            ->modal()
                            ->modalHeading('Manage Business Information')
                            ->modalDescription('Update your business information in the form below.')
                            ->modalSubmitAction(fn (Action $action) => $action->label('Save Changes')->icon(Heroicon::OutlinedPlusCircle))
                            ->modalCancelAction(fn (Action $action) => $action->label('Cancel')->icon(Heroicon::XMark))
                            ->modalIcon(Heroicon::PencilSquare)
                            ->icon(Heroicon::PencilSquare)
                            ->modalWidth(Width::FiveExtraLarge)
                            ->closeModalByClickingAway(false)
                            ->closeModalByEscaping(false)
                            ->modalAutofocus(false)
                            ->hidden(request()->user()->role !== UserRole::Supplier)
                            ->fillForm(function ($record) {
                                return $record ? [
                                    'business_name' => $record->business_name,
                                    'owner_name' => $record->owner_name,
                                    'email' => $record->email,
                                    'website' => $record->website,
                                    'mobile_number' => $record->mobile_number,
                                    'landline_number' => $record->landline_number,
                                    'supplier_type' => $record->supplier_type,
                                ] : [];
                            })
                            ->schema([
                                Grid::make(2)
                                    ->schema(BusinessInformation::getSchema())
                            ])
                            ->after(fn ($livewire) => $livewire->dispatch('refreshInfolist'))
                            ->action(function (array $data, $record): void {
                                $supplier = $record;
                                if ($supplier) {
                                    $supplier->update($data);
                                } else {
                                    $supplier()->create($data);
                                }

                                Notification::make()
                                    ->title('Saved successfully')
                                    ->success()
                                    ->body('Changes to the record have been saved.')
                                    ->send();
                            })
                            ->link(),
                    ])
                    ->collapsible()
                    ->columns(2),
                Section::make('Line of Business')
                    ->heading(function ($record) {
                        $status = $record->lob_statuses()->latest()->first();
                        if (! $status) {
                            return new HtmlString('Line of Business');
                        }
                        return new HtmlString('Line of Business <span class="text-sm font-normal text-custom-600 fi-color-'.$status->status->getColor().'">(' . ($status->status->getLabel() ?? 'Pending for Validation') . ')</span>');
                    })
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
                            ]),
                        Action::make('status_history')
                            ->hidden(fn ($record) => $record->lob_statuses->isEmpty())
                            ->button()
                            ->icon(Heroicon::OutlinedClock)
                            ->color('primary')
                            ->label('View status history')
                            ->modalWidth(Width::ExtraLarge)
                            ->modalHeading('Line of Business Status History')
                            ->modalDescription('View the status history of your line of business.')
                            ->modalCancelAction(fn (Action $action) => $action->label('Close')->icon(Heroicon::XMark))
                            ->slideOver()
                            ->schema([
                                Livewire::make(StatusView::class, function ($record) {
                                    return [
                                        'statuses' => $record->lob_statuses,
                                    ];
                                })
                                ->key('lob-status-view-')
                                ->extraAttributes(['class' => 'ms-5'])
                            ]),
                    ])
                    ->afterHeader([
                        Action::make('manage_line_of_business')
                            ->label('Update')
                            ->icon(Heroicon::PencilSquare)
                            ->color('primary')
                            ->modal()
                            ->modalHeading('Manage Line of Business')
                            ->modalDescription('Update your line of business information in the form below.')
                            ->modalSubmitAction(fn (Action $action) => $action->label('Save Changes')->icon(Heroicon::OutlinedPlusCircle))
                            ->modalCancelAction(fn (Action $action) => $action->label('Cancel')->icon(Heroicon::XMark))
                            ->modalIcon(Heroicon::PencilSquare)
                            ->icon(Heroicon::PencilSquare)
                            ->modalWidth(Width::FiveExtraLarge)
                            ->closeModalByClickingAway(false)
                            ->closeModalByEscaping(false)
                            ->modalAutofocus(false)
                            ->hidden(request()->user()->role !== UserRole::Supplier)
                            ->fillForm(function ($record) {
                                $data = $record->supplierLobs
                                    ->groupBy('lob_category_id')
                                    ->map(function ($items, $categoryId) {
                                        return [
                                            'lob_category_id' => $categoryId,
                                            'lob_subcategory_id' => $items->pluck('lob_subcategory_id')->toArray(),
                                        ];
                                    })
                                    ->values()
                                    ->toArray();

                                return $record ? [
                                    'supplierLobs' => $data,
                                ] : [];
                            })
                            ->schema([
                                Grid::make(2)
                                    ->schema(LineOfBusiness::getSchema())
                            ])
                            ->after(fn ($livewire) => $livewire->dispatch('refreshInfolist'))
                            ->action(function (array $data, $record): void {
                                SupplierLOB::save($record, $data);

                                Notification::make()
                                    ->title('Saved successfully')
                                    ->success()
                                    ->body('Changes to the record have been saved.')
                                    ->send();
                            })
                            ->link(),
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
                                TextEntry::make('label')
                                    ->hiddenLabel()
                                    ->icon(Heroicon::MapPin)
                                    ->size(TextSize::Large)
                                    ->weight(FontWeight::Bold)
                                    ->color('primary'),
                                TextEntry::make('line_1')
                                    ->label('Full Address')
                                    ->formatStateUsing(fn ($state, $record) => $record->getFullAddressAttribute())
                                    ->icon(Heroicon::MapPin)
                                    ->color('primary'),
                                TextEntry::make('label')
                                    ->label('Status')
                                    ->badge()
                                    ->state(function ($record) {
                                        return $record->statuses()->latest()->first()->status->getLabel();
                                    })
                                    ->color(function ($record) {
                                        return $record->statuses()->latest()->first()->status->getColor();
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
                                    ->belowContent(function ($record) {
                                        return [
                                            Action::make('status_history')
                                                ->button()
                                                ->icon(Heroicon::OutlinedClock)
                                                ->color('primary')
                                                ->label('View status history')
                                                ->modalWidth(Width::ExtraLarge)
                                                ->modalHeading($record->label)
                                                ->modalDescription('View the status history of this address.')
                                                ->modalCancelAction(fn (Action $action) => $action->label('Close')->icon(Heroicon::XMark))
                                                ->slideOver()
                                                ->schema([
                                                    Livewire::make(StatusView::class, function ($record) {
                                                        return [
                                                            'statuses' => $record->statuses,
                                                        ];
                                                    })
                                                    ->key('address-status-view-'.$record->id)
                                                    ->extraAttributes(['class' => 'ms-5'])
                                                ]),
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
                    ->afterHeader([
                        Action::make('manage_address_information')
                            ->label('Update')
                            ->icon(Heroicon::PencilSquare)
                            ->color('primary')
                            ->modal()
                            ->modalHeading('Manage Address Information')
                            ->modalDescription('Update your address information in the form below.')
                            ->modalSubmitAction(fn (Action $action) => $action->label('Save Changes')->icon(Heroicon::OutlinedPlusCircle))
                            ->modalCancelAction(fn (Action $action) => $action->label('Cancel')->icon(Heroicon::XMark))
                            ->modalIcon(Heroicon::PencilSquare)
                            ->icon(Heroicon::PencilSquare)
                            ->modalWidth(Width::FiveExtraLarge)
                            ->closeModalByClickingAway(false)
                            ->closeModalByEscaping(false)
                            ->modalAutofocus(false)
                            ->hidden(request()->user()->role !== UserRole::Supplier)
                            ->fillForm(function ($record) {
                                return $record ? [
                                    'addresses' => $record->addresses()->get(),
                                ] : [];
                            })
                            ->schema(AddressInformation::getSchema())
                            ->after(fn ($livewire) => $livewire->dispatch('refreshInfolist'))
                            ->action(function (array $data, $record): void {
                                $addresses = $data['addresses'] ?? [];
                                foreach ($addresses as $addressData) {
                                    if (isset($addressData['id'])) {
                                        $address = $record->addresses()->find($addressData['id']);
                                        if ($address) {
                                            $address->update($addressData);
                                            SiteImageAction::save($address, $addressData['site_image']['file_path']);

                                            // Create new status
                                            $address->statuses()->create([
                                                'user_id' => request()->user()->id,
                                                'status' => \App\Enums\Status::PendingReview,
                                                'remarks' => '<p>Address information updated, pending re-validation.</p>',
                                                'status_date' => now(),
                                            ]);

                                            continue;
                                        }
                                    }
                                    $address = $record->addresses()->create($addressData);
                                    SiteImageAction::save($address, $addressData['site_image']['file_path']);

                                    // Create new status
                                    $address->statuses()->create([
                                        'user_id' => request()->user()->id,
                                        'status' => \App\Enums\Status::PendingReview,
                                        'remarks' => '<p>Address information added, pending validation.</p>',
                                        'status_date' => now(),
                                    ]);
                                }

                                Notification::make()
                                    ->title('Saved successfully')
                                    ->success()
                                    ->body('Changes to the record have been saved.')
                                    ->send();
                            })
                            ->link(),
                    ])
                    ->collapsible(),
                Section::make('Documents')
                    ->description('Below are the documents you have uploaded for your business. You can add or update documents as needed.')
                    ->icon(Heroicon::DocumentText)
                    ->collapsed()
                    ->schema([
                        Livewire::make(SupplierDocumentsTable::class, function ($record) {
                            return ['supplier' => $record];
                        }),
                    ])
                    ->collapsible(),
            ])->columns(1);
    }
}
