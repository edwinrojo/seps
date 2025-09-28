<?php

namespace App\Filament\Supplier\Pages;

use App\Enums\ProcType;
use App\Filament\Resources\Suppliers\Actions\SiteImageAction;
use App\Filament\Resources\Suppliers\Schemas\SupplierInfolist;
use Filament\Pages\Page;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Support\Enums\Width;
use Filament\Schemas\Components\Section;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Repeater;
use Filament\Support\Enums\Alignment;
use App\Models\Supplier;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use App\Models\Province;
use App\Models\Municipality;
use App\Models\Barangay;
use App\Models\LobCategory;
use App\Models\LobSubcategory;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater\TableColumn;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

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
        return SupplierInfolist::configure($schema)->record($this->record);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Manage Profile')
                ->modal()
                ->modalHeading('Manage Your Business Profile')
                ->modalDescription('Update your business profile information.')
                ->modalSubmitAction(fn (Action $action) => $action->label('Save Changes')->icon(Heroicon::OutlinedPlusCircle))
                ->modalCancelAction(fn (Action $action) => $action->label('Cancel')->icon(Heroicon::XMark))
                ->fillForm(function (): array {
                    $supplier = $this->record;
                    return $supplier ? [
                        'business_name' => $supplier->business_name,
                        'owner_name' => $supplier->owner_name,
                        'email' => $supplier->email,
                        'website' => $supplier->website,
                        'mobile_number' => $supplier->mobile_number,
                        'landline_number' => $supplier->landline_number,
                        'addresses' => $supplier->addresses()->with('site_image')->get(),
                        'supplier_type' => $supplier->supplier_type,
                        'supplierLobs' => $this->formatSupplierLobsBeforeFill($supplier->supplierLobs),
                    ] : [];
                })
                ->schema(array_merge($this->businessInformationSchema(), $this->lineOfBusinessSchema(), $this->addressSchema()))
                ->action(function (array $data): void {
                    $supplier = $this->record;
                    if ($supplier) {
                        $supplier->update($data);
                    } else {
                        $supplier()->create($data);
                    }

                    $this->saveSupplierLobs($supplier, $data);

                    $addresses = $data['addresses'] ?? [];
                    foreach ($addresses as $addressData) {
                        if (isset($addressData['id'])) {
                            $address = $supplier->addresses()->find($addressData['id']);
                            if ($address) {
                                $address->update($addressData);
                                SiteImageAction::save($address, $addressData['site_image']['file_path']);
                                continue;
                            }
                        }
                        $supplier->addresses()->create($addressData);
                        SiteImageAction::save($address, $addressData['site_image']['file_path']);
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
                ->modalIcon(Heroicon::PencilSquare)
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
                    'owner_name' => $supplier->owner_name,
                    'email' => $supplier->email,
                    'website' => $supplier->website,
                    'mobile_number' => $supplier->mobile_number,
                    'landline_number' => $supplier->landline_number,
                    'addresses' => $supplier->addresses,
                    'supplier_type' => $supplier->supplier_type,
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
                        ->maxLength(255),
                    TextInput::make('owner_name')
                        ->label('Name of Owner')
                        ->required()
                        ->placeholder('e.g., Juan Dela Cruz'),
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
                    Select::make('supplier_type')
                        ->native(false)
                        ->searchable()
                        ->options(ProcType::class)
                        ->required()
                ])
                ->columns(2),
        ];
    }

    public function lineOfBusinessSchema(): array
    {
        return [
            Section::make('Line of Business')
                ->description('Please provide the line of business details.')
                ->icon(Heroicon::Briefcase)
                ->schema([
                    Repeater::make('supplierLobs')
                        ->hiddenLabel()
                        ->columns(2)
                        ->grid(2)
                        ->addActionAlignment(Alignment::Start)
                        ->addActionLabel('Add Line of Business')
                        ->addAction(fn ($action) => $action->icon(Heroicon::OutlinedPlusCircle))
                        ->columnSpanFull()
                        ->table([
                            TableColumn::make('Category'),
                            TableColumn::make('Subcategory'),
                        ])
                        ->schema([
                            Select::make('lob_category_id')
                                ->label('Category')
                                ->options(fn () => LobCategory::pluck('title', 'id')->toArray())
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(fn (callable $set) => $set('lob_subcategory_id', null))
                                ->searchable()
                                ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                ->placeholder('Select Category'),
                            Select::make('lob_subcategory_id')
                                ->label('Subcategory')
                                ->multiple()
                                ->required()
                                ->searchable()
                                ->hidden(function (callable $get) {
                                    $categoryId = $get('lob_category_id');
                                    $subcategories = LobSubcategory::where('lob_category_id', $categoryId)
                                        ->pluck('title', 'id')
                                        ->toArray();
                                    return $subcategories === [] || !$categoryId;
                                })
                                ->options(function (callable $get) {
                                    $categoryId = $get('lob_category_id');
                                    if (!$categoryId) {
                                        return [];
                                    }
                                    return LobSubcategory::where('lob_category_id', $categoryId)
                                        ->pluck('title', 'id')
                                        ->toArray();
                                })
                                ->placeholder('Select Subcategory'),
                        ]),
                ])
                ->columns(2)
                ->columnSpanFull(),
        ];
    }

    protected function formatSupplierLobsBeforeFill($supplierLobs): array
    {
        $data = $supplierLobs
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

    protected function saveSupplierLobs($record, $data) {
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
                                ->required()
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
                ]),
        ];
    }
}
