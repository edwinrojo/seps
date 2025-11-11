<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\ProcType;
use App\Enums\UserRole;
use App\Models\Office;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([
                    Step::make('Account Information')
                        ->columns(2)
                        ->icon(Heroicon::UserCircle)
                        ->description('Provide account information below')
                        ->schema([
                            TextInput::make('first_name')
                                ->helperText('Enter the first name of the user.')
                                ->required(),
                            TextInput::make('last_name')
                                ->helperText('Enter the last name of the user.')
                                ->required(),
                            TextInput::make('middle_name')
                                ->helperText('Enter the middle name of the user (optional).'),
                            TextInput::make('suffix')
                                ->helperText('Enter the suffix of the user (optional).'),
                            Select::make('role')
                                ->label('User Role')
                                ->helperText('Select the role assigned to the user.')
                                ->searchable()
                                ->required()
                                ->native(false)
                                ->options(UserRole::class)
                                ->hiddenOn('edit')
                                ->live(),
                            TextInput::make('email')
                                ->label('Email address')
                                ->helperText('Enter a valid email address for the user.')
                                ->email()
                                ->unique(table: 'users', column: 'email')
                                ->required(),
                            TextInput::make('contact_number')
                                ->label('Contact Number')
                                ->helperText('Enter the contact number of the user.')
                                ->prefix('+63')
                                ->mask('999-999-9999')
                                ->placeholder('912-345-6789')
                                ->required(),
                            ToggleButtons::make('status')
                                ->helperText('Set the account status of the user. Setting this to inactive will prevent the user from logging in.')
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
                                ->helperText('Select the office where the user is assigned.')
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
                                ->helperText('Enter the position title of the user.')
                                ->label('Position Title')
                                ->required()
                                ->afterStateHydrated(function ($component, $state, $record) {
                                    if ($record && $record->twg) {
                                        $component->state($record->twg->position_title);
                                    }
                                }),
                            Select::make('twg.twg_type')
                                ->helperText('Select the TWG type for the user.')
                                ->label('TWG Type')
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
                                ->helperText('Select the office where the user is assigned.')
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
                                ->helperText('Enter the designation of the user.')
                                ->label('Designation')
                                ->required()
                                ->afterStateHydrated(function ($component, $state, $record) {
                                    if ($record && $record->endUser) {
                                        $component->state($record->endUser->designation);
                                    }
                                })
                        ]),
                ])->columnSpanFull()
            ]);
    }
}
