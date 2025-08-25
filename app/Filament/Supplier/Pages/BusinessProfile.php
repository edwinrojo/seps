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

class BusinessProfile extends Page
{
    protected string $view = 'filament.supplier.pages.business-profile';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Briefcase;

    protected static ?string $title = 'My Business Profile';

    protected ?string $subheading = 'Manage your business profile information and upload necessary documents to verify your business\' identity and eligibility.';

    public $defaultAction = 'onboarding';

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->record(auth()->user()->supplier)
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
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Manage Profile')
                ->modal()
                ->modalHeading('Manage Your Business Profile')
                ->fillForm(function (): array {
                    $supplier = auth()->user()->supplier;
                    return $supplier ? [
                        'business_name' => $supplier->business_name,
                        'email' => $supplier->email,
                        'website' => $supplier->website,
                        'mobile_number' => $supplier->mobile_number,
                        'landline_number' => $supplier->landline_number,
                    ] : [];
                })
                ->schema($this->modalSchema())
                ->action(function (array $data): void {
                    $user = auth()->user();
                    if ($user->supplier) {
                        $user->supplier->update($data);
                    } else {
                        $user->supplier()->create($data);
                    }

                    Notification::make()
                        ->title('Saved successfully')
                        ->success()
                        ->body('Changes to the record have been saved.')
                        ->send();
                })
                ->stickyModalHeader()
                ->modalWidth(Width::FiveExtraLarge)
                ->closeModalByClickingAway(false)
                ->closeModalByEscaping(false)
                ->modalAutofocus(false)
                ->modalSubmitActionLabel('Save Changes'),
        ];
    }

    public function onboardingAction(): Action
    {
        return Action::make('onboarding')
            ->modal()
            ->modalHeading('Setup Your Business Profile')
            ->fillForm(function (): array {
                $supplier = auth()->user()->supplier;
                return $supplier ? [
                    'business_name' => $supplier->business_name,
                    'email' => $supplier->email,
                    'website' => $supplier->website,
                    'mobile_number' => $supplier->mobile_number,
                    'landline_number' => $supplier->landline_number,
                ] : [];
            })
            ->schema($this->modalSchema())
            ->action(function (array $data): void {
                $user = auth()->user();
                if ($user->supplier) {
                    $user->supplier->update($data);
                } else {
                    $user->supplier()->create($data);
                }

                Notification::make()
                    ->title('Saved successfully')
                    ->success()
                    ->body('Changes to the record have been saved.')
                    ->send();
            })
            ->stickyModalHeader()
            ->modalWidth(Width::FiveExtraLarge)
            ->closeModalByClickingAway(false)
            ->closeModalByEscaping(false)
            ->modalAutofocus(false)
            ->modalSubmitActionLabel('Save Changes')
            ->visible(fn (): bool => auth()->user()?->supplier === null);
    }

    public function modalSchema(): array
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
                ->columns(2)
        ];
    }
}
