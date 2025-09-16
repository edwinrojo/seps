<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\ProcType;
use App\Enums\UserRole;
use App\Models\Office;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Support\Icons\Heroicon;

class UserWizard
{
    public static function getSteps(): array
    {
        return [
            Step::make('Account Information')
                ->columns(2)
                ->icon(Heroicon::UserCircle)
                ->description('Provide account information below')
                ->schema([
                    TextInput::make('first_name')
                        ->required(),
                    TextInput::make('last_name')
                        ->required(),
                    TextInput::make('middle_name'),
                    TextInput::make('suffix'),
                    Select::make('role')
                        ->searchable()
                        ->required()
                        ->native(false)
                        ->options(UserRole::class)
                        ->hiddenOn('edit')
                        ->live(),
                    TextInput::make('email')
                        ->label('Email address')
                        ->email()
                        ->unique(table: 'users', column: 'email')
                        ->required(),
                    TextInput::make('contact_number')
                        ->prefix('+63')
                        ->mask('999-999-9999')
                        ->placeholder('912-345-6789')
                        ->required(),
                    ToggleButtons::make('status')
                        ->label('Account Status')
                        ->grouped()
                        ->default('active')
                        ->options([
                            'active' => 'Active',
                            'inactive' => 'Inactive'
                        ])
                        ->colors([
                            'active' => 'success',
                            'inactive' => 'danger'
                        ])
                        ->icons([
                            'active' => Heroicon::CheckCircle,
                            'inactive' => Heroicon::XCircle
                        ])
                ]),
            Step::make('Other Information')
                ->columns(2)
                ->icon(Heroicon::InformationCircle)
                ->description('Provide additional information below')
                ->visible(function (Get $get) {
                    return $get('role') ? $get('role')->value === UserRole::Twg->value : false;
                })
                ->schema([
                    Select::make('twg.office_id')
                        ->label('Office')
                        ->native(false)
                        ->searchable()
                        ->required()
                        ->options(Office::pluck('title', 'id'))
                        ->preload()
                        ->afterStateHydrated(function ($component, $state, $record) {
                            if ($record && $record->twg) {
                                $component->state($record->twg->office_id);
                            }
                        }),
                    TextInput::make('twg.position_title')
                        ->label('Position Title')
                        ->required()
                        ->afterStateHydrated(function ($component, $state, $record) {
                            if ($record && $record->twg) {
                                $component->state($record->twg->position_title);
                            }
                        }),
                    Select::make('twg.twg_type')
                        ->native(false)
                        ->searchable()
                        ->options(ProcType::class)
                        ->required()
                        ->afterStateHydrated(function ($component, $state, $record) {
                            if ($record && $record->twg) {
                                $component->state($record->twg->twg_type);
                            }
                        }),
                ]),
            Step::make('Other Information')
                ->columns(2)
                ->icon(Heroicon::InformationCircle)
                ->description('Provide additional information below')
                ->visible(function (Get $get) {
                    return $get('role') ? $get('role')->value === UserRole::EndUser->value : false;
                })
                ->schema([
                    Select::make('endUser.office_id')
                        ->label('Office')
                        ->native(false)
                        ->searchable()
                        ->required()
                        ->options(Office::pluck('title', 'id'))
                        ->preload()
                        ->afterStateHydrated(function ($component, $state, $record) {
                            if ($record && $record->endUser) {
                                $component->state($record->endUser->office_id);
                            }
                        }),
                    TextInput::make('endUser.designation')
                        ->label('Designation')
                        ->required()
                        ->afterStateHydrated(function ($component, $state, $record) {
                            if ($record && $record->endUser) {
                                $component->state($record->endUser->designation);
                            }
                        })
                ]),
        ];
    }
}
