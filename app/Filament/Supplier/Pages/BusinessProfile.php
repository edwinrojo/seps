<?php

namespace App\Filament\Supplier\Pages;

use Filament\Pages\Page;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Support\Enums\Width;
use Filament\Schemas\Components\Section;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Enums\TextSize;
use Filament\Support\Enums\FontWeight;
use Filament\Forms\Components\Repeater;
use Filament\Support\Enums\Alignment;
use app\Models\Supplier;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Http;
use Filament\Infolists\Components\RepeatableEntry;
use App\Filament\Services\PSGC;

class BusinessProfile extends Page
{
    protected string $view = 'filament.supplier.pages.business-profile';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Briefcase;

    protected static ?string $title = 'My Business Profile';

    protected ?string $subheading = 'Manage your business profile information and upload necessary documents to verify your business\' identity and eligibility.';

    public ?Supplier $record = null;

    public $defaultAction = 'onboarding';

    public function mount(): void
    {
        $this->record = auth()->user()->supplier;
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
                            ->icon(Heroicon::BuildingOffice2)
                            ->iconColor('primary')
                            ->color('primary')
                            ->size(TextSize::Large)
                            ->weight(FontWeight::Black)
                            ->columnSpan(2),
                        TextEntry::make('email')
                            ->label('Email Address')
                            ->iconColor('primary')
                            ->color('primary')
                            ->weight(FontWeight::Bold),
                        TextEntry::make('website')
                            ->label('Website')
                            ->color('primary')
                            ->weight(FontWeight::Bold),
                        TextEntry::make('mobile_number')
                            ->prefix('+63 ')
                            ->label('Mobile Number')
                            ->color('primary')
                            ->weight(FontWeight::Bold),
                        TextEntry::make('landline_number')
                            ->label('Landline Number')
                            ->color('primary')
                            ->weight(FontWeight::Bold),
                    ])
                    ->columns(2),
                Section::make('Address Information')
                    ->description('Below is the address information for your business. You can add multiple addresses if needed.')
                    ->icon(Heroicon::Map)
                    ->schema([
                        RepeatableEntry::make('addresses')
                            ->label('Business Addresses')
                            ->schema([
                                TextEntry::make('line_1')
                                    ->label('Address Line 1')
                                    ->color('primary')
                                    ->weight(FontWeight::Bold)
                                    ->columnSpan(2),
                            ])
                            ->columns(2)
                    ]),
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

                    $this->record = auth()->user()->supplier;

                    Notification::make()
                        ->title('Saved successfully')
                        ->success()
                        ->body('Changes to the record have been saved.')
                        ->send();
                })
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
                auth()->user()->supplier()->create($data);

                $this->record = auth()->user()->supplier;

                Notification::make()
                    ->title('Saved successfully')
                    ->success()
                    ->body('Changes to the record have been saved.')
                    ->send();
            })
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
                            Select::make('province')
                                ->searchable()
                                ->required()
                                ->options(fn () => PSGC::getProvinces())
                                ->reactive()
                                ->afterStateUpdated(fn ($state, callable $set) => $set('municipality_city', null)),
                            Select::make('municipality_city')
                                ->label('Municipality/City')
                                ->searchable()
                                ->required()
                                ->options(fn (callable $get) => $get('province') ? PSGC::getMunicipalities($get('province')) : [])
                                ->reactive()
                                ->afterStateUpdated(fn ($state, callable $set) => $set('barangay', null)),
                            Select::make('barangay')
                                ->label('Barangay')
                                ->searchable()
                                ->required()
                                ->options(fn (callable $get) => $get('municipality_city') ? PSGC::getBarangays($get('municipality_city')) : []),
                            TextInput::make('country')
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
