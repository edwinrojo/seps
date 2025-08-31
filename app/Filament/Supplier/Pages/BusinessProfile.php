<?php

namespace App\Filament\Supplier\Pages;

use Filament\Pages\Page;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Support\Enums\Width;
use Filament\Schemas\Components\Section;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Enums\TextSize;
use Filament\Support\Enums\FontWeight;
use Filament\Forms\Components\Repeater;
use Filament\Support\Enums\Alignment;
use App\Models\Supplier;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use App\Models\Province;
use App\Models\Municipality;
use App\Models\Barangay;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Support\Facades\Storage;
use Filament\Schemas\Components\Utilities\Set;

class BusinessProfile extends Page
{
    protected string $view = 'filament.supplier.pages.business-profile';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Briefcase;

    protected static ?string $title = 'My Business Profile';

    protected ?string $subheading = 'Manage your business profile information and upload necessary documents to verify your business\' identity and eligibility.';

    protected $listeners = ['refreshInfolist' => '$refresh'];

    public ?Supplier $record = null;

    public $defaultAction = 'onboarding';

    public function mount(): void
    {
        $this->record = request()->user()?->supplier;
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->record($this->record)
            ->schema([
                Section::make('Business Information')
                    ->description('Below is the information about your business profile. To make changes, click the "Manage Profile" button above.')
                    ->icon(Heroicon::InformationCircle)
                    ->schema([
                        TextEntry::make('business_name')
                            ->extraAttributes(['style' => 'font-size: 1.5rem;'])
                            ->icon(Heroicon::BuildingOffice2)
                            ->color('info')
                            ->size(TextSize::Large)
                            ->weight(FontWeight::Black)
                            ->columnSpan(2),
                        TextEntry::make('email')
                            ->icon(Heroicon::Envelope)
                            ->label('Email Address')
                            ->color('primary')
                            ->weight(FontWeight::Bold),
                        TextEntry::make('website')
                            ->icon(Heroicon::GlobeAlt)
                            ->label('Website')
                            ->color('primary')
                            ->weight(FontWeight::Bold),
                        TextEntry::make('mobile_number')
                            ->icon(Heroicon::DevicePhoneMobile)
                            ->prefix('+63 ')
                            ->label('Mobile Number')
                            ->color('primary')
                            ->weight(FontWeight::Bold),
                        TextEntry::make('landline_number')
                            ->icon(Heroicon::Phone)
                            ->label('Landline Number')
                            ->color('primary')
                            ->weight(FontWeight::Bold),
                    ])
                    ->collapsible()
                    ->columns(2),
                Section::make('Address Information')
                    ->description('Below is the address information for your business. You can add multiple addresses if needed.')
                    ->icon(Heroicon::Map)
                    ->schema([
                        RepeatableEntry::make('addresses')
                            ->hiddenLabel()
                            ->schema([
                                TextEntry::make('line_1')
                                    ->label('Address')
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
                                    ->icon(Heroicon::BuildingStorefront)
                                    ->size(TextSize::Large)
                                    ->weight(FontWeight::Bold)
                                    ->color('info')
                                    ->columnSpan(3),
                                Section::make('Storefront Image')
                                    ->schema([
                                        ImageEntry::make('site_image.file_path')
                                            ->hiddenLabel()
                                            ->state(function ($record) {
                                                return $record->site_image ? $record->site_image->file_path : 'storage/site_images/placeholder.png';
                                            })
                                            ->defaultImageUrl(url('storage/site_images/placeholder.png'))
                                            ->imageWidth(300)
                                            ->imageHeight(200)
                                            ->url(fn ($state) => $state ? asset('storage/' . ltrim($state, '/')) : null)
                                            ->openUrlInNewTab()
                                            ->disk('public'),
                                        Action::make('add_site_image')
                                            ->label('Add / Update Storefront Photo')
                                            ->modal()
                                            ->modalHeading('Add / Update Storefront Image')
                                            ->modalDescription('Upload a new storefront image or update the existing one.')
                                            ->action(function ($record, array $data, Set $set, $get): void {
                                                // Action logic for adding/updating site image
                                                $record->site_image?->delete();
                                                if ($record->site_image?->file_path) {
                                                    Storage::disk('public')->delete($record->site_image?->file_path);
                                                }

                                                $sizeInBytes = Storage::disk('public')->size($data['site_image']);

                                                $record->site_image()->create([
                                                    'file_path' => $data['site_image'],
                                                    'file_size' => $sizeInBytes,
                                                ]);
                                                $record->save();

                                                Notification::make()
                                                    ->title('Site Image Saved')
                                                    ->success()
                                                    ->body('The site image has been successfully saved.')
                                                    ->send();
                                            })
                                            ->schema([
                                                FileUpload::make('site_image')
                                                    ->disk('public')
                                                    ->label('Site Image')
                                                    ->imageEditor()
                                                    ->image()
                                                    ->getUploadedFileNameForStorageUsing(
                                                        fn (TemporaryUploadedFile $file): string => (string) str($file->getClientOriginalName())
                                                            ->prepend((string) now()->format('YmdHis') . '_')
                                                    )
                                                    ->directory('site_images')
                                                    ->maxSize(2048) // 2MB
                                                    ->moveFiles()
                                                    ->required()
                                                    ->imagePreviewHeight('200')
                                                    ->columnSpan(2),
                                            ])
                                            ->after(fn ($livewire) => $livewire->dispatch('refreshInfolist'))
                                            ->modalWidth(Width::Medium)
                                            ->modalAlignment(Alignment::Center)
                                            ->modalIcon(Heroicon::Camera)
                                            ->modalIconColor('primary')
                                            ->closeModalByClickingAway(false)
                                            ->closeModalByEscaping(false)
                                            ->modalAutofocus(false)
                                            ->modalSubmitActionLabel('Save Image')
                                            ->icon(Heroicon::Camera)
                                            ->color('gray'),
                                    ])
                                    ->icon(Heroicon::Photo)
                                    ->collapsible()
                                    ->collapsed(),
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
                            ])
                            ->columns(3)
                    ])
                    ->collapsible(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Manage Profile')
                ->modal()
                ->modalHeading('Manage Your Business Profile')
                ->fillForm(function (): array {
                    $supplier = $this->record;
                    return $supplier ? [
                        'business_name' => $supplier->business_name,
                        'email' => $supplier->email,
                        'website' => $supplier->website,
                        'mobile_number' => $supplier->mobile_number,
                        'landline_number' => $supplier->landline_number,
                        'addresses' => $supplier->addresses,
                    ] : [];
                })
                ->schema(array_merge($this->businessInformationSchema(), $this->addressSchema()))
                ->action(function (array $data): void {
                    $supplier = $this->record;
                    if ($supplier) {
                        $supplier->update($data);
                    } else {
                        $supplier()->create($data);
                    }

                    $addresses = $data['addresses'] ?? [];
                    foreach ($addresses as $addressData) {
                        if (isset($addressData['id'])) {
                            $address = $supplier->addresses()->find($addressData['id']);
                            if ($address) {
                                $address->update($addressData);
                                continue;
                            }
                        }
                        $supplier->addresses()->create($addressData);
                    }

                    Notification::make()
                        ->title('Saved successfully')
                        ->success()
                        ->body('Changes to the record have been saved.')
                        ->send();
                })
                ->after(fn ($livewire) => $livewire->dispatch('refreshInfolist'))
                ->slideOver()
                ->stickyModalHeader()
                ->stickyModalFooter()
                ->icon(Heroicon::PencilSquare)
                ->modalWidth(Width::FiveExtraLarge)
                ->closeModalByClickingAway(false)
                ->closeModalByEscaping(false)
                ->modalAutofocus(false)
                ->modalSubmitActionLabel('Save Changes')
                ->visible(fn (): bool => $this->record != null),
        ];
    }

    public function onboardingAction(): Action
    {
        return Action::make('onboarding')
            ->modal()
            ->modalHeading('Setup Your Business Profile')
            ->fillForm(function (): array {
                $supplier = $this->record;
                return $supplier ? [
                    'business_name' => $supplier->business_name,
                    'email' => $supplier->email,
                    'website' => $supplier->website,
                    'mobile_number' => $supplier->mobile_number,
                    'landline_number' => $supplier->landline_number,
                    'addresses' => $supplier->addresses,
                ] : [];
            })
            ->schema($this->businessInformationSchema())
            ->action(function (array $data): void {
                request()->user()?->supplier()->create($data);

                Notification::make()
                    ->title('Saved successfully')
                    ->success()
                    ->body('Changes to the record have been saved.')
                    ->send();
            })
            ->after(fn ($livewire) => $livewire->dispatch('refreshInfolist'))
            ->modalWidth(Width::FiveExtraLarge)
            ->closeModalByClickingAway(false)
            ->closeModalByEscaping(false)
            ->modalAutofocus(false)
            ->modalSubmitActionLabel('Save Changes')
            ->visible(fn (): bool => $this->record === null);
    }

    public function businessInformationSchema(): array
    {
        return [
            Section::make('Business Information')
                ->description('Please provide accurate and up-to-date information about your business.')
                ->icon(Heroicon::InformationCircle)
                ->schema([
                    TextInput::make('business_name')
                        ->label('Business Name')
                        ->placeholder('e.g., ABC Enterprises')
                        ->required()
                        ->maxLength(255)
                        ->columnSpan(2),
                    TextInput::make('email')
                        ->email()
                        ->label('Email Address')
                        ->placeholder('e.g., abc-enterprises@example.com')
                        ->belowContent('This email address will be used to contact your company for any important inquiries and updates.')
                        ->required()
                        ->maxLength(500)
                        ->columnSpan(2),
                    TextInput::make('website')
                        ->prefix('https://')
                        ->label('Website')
                        ->placeholder('e.g., www.abc-enterprises.com')
                        ->maxLength(500)
                        ->columnSpan(2),
                    TextInput::make('mobile_number')
                        ->label('Mobile Number')
                        ->prefix('+63')
                        ->mask('999-999-9999')
                        ->placeholder('912-345-6789')
                        ->required()
                        ->belowContent('This number will be used to contact your company for any important inquiries and updates.')
                        ->maxLength(255),
                    TextInput::make('landline_number')
                        ->label('Landline Number')
                        ->placeholder('e.g., (082) 123-4567')
                        ->maxLength(255),
                ])
                ->columns(2),
        ];
    }

    public function addressSchema(): array
    {
        return [
            Section::make('Address Information')
                ->description('Please provide the address information for your business.')
                ->icon(Heroicon::Map)
                ->schema([
                    Repeater::make('addresses')
                        ->label('Business Addresses')
                        ->schema([
                            Hidden::make('id'),
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
                ]),
        ];
    }
}
