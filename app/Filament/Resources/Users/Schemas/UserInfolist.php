<?php

namespace App\Filament\Resources\Users\Schemas;

use Dom\Text;
use Filament\Infolists\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Icon;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\HtmlString;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('User Information')
                    ->description('Detailed information about the user.')
                    ->icon(Heroicon::UserCircle)
                    ->inlineLabel()
                    ->columnSpan(fn ($record) => $record->role->value === 'supplier' ? 1 : 2)
                    ->schema([
                        ImageEntry::make('avatar')
                            ->circular()
                            ->visibility('public')
                            ->imageHeight(100)
                            ->defaultImageUrl(fn ($record) => $record->getFilamentAvatarUrl())
                            ->disk('public')
                            ->hiddenLabel(),
                        TextEntry::make('name')
                            ->beforeLabel(Icon::make(Heroicon::OutlinedUser)->color('gray'))
                            ->formatStateUsing(function ($record): HtmlString {
                                $name = $record->name;
                                if ($record->role->value === 'twg' && $record->twg) {
                                    $name .= '<span style="display: block;" class="text-sm font-normal text-gray-500">'.$record->twg->position_title.'</span>';
                                    $name .= '<span style="display: block;" class="text-sm font-normal text-gray-500">'.$record->twg->office->title.'</span>';
                                }
                                if ($record->role->value === 'end-user' && $record->endUser) {
                                    $name .= '<span style="display: block;" class="text-sm font-normal text-gray-500">'.$record->endUser->designation.'</span>';
                                    $name .= '<span style="display: block;" class="text-sm font-normal text-gray-500">'.$record->endUser->office->title.'</span>';
                                }
                                return new HtmlString("<span class='font-bold text-lg'>{$name}</span>");
                            })
                            ->color('primary'),
                        TextEntry::make('role')
                            ->beforeLabel(Icon::make(Heroicon::OutlinedShieldCheck)->color('gray'))
                            ->color('primary'),
                        TextEntry::make('email')
                            ->beforeLabel(Icon::make(Heroicon::OutlinedEnvelope)->color('gray'))
                            ->color('primary'),
                        TextEntry::make('contact_number')
                            ->beforeLabel(Icon::make(Heroicon::OutlinedDevicePhoneMobile)->color('gray'))
                            ->prefix('+63 ')
                            ->color('primary'),
                    ]),
                Section::make('Business Information')
                    ->description('Details about the supplier business.')
                    ->icon(Heroicon::BuildingStorefront)
                    ->inlineLabel()
                    ->hidden(function ($record) {
                        if ($record->role->value !== 'supplier') return true;

                        return $record->supplier === null;
                    })
                    ->schema([
                        TextEntry::make('supplier.business_name')
                            ->beforeLabel(Icon::make(Heroicon::OutlinedBriefcase)->color('gray'))
                            ->color('primary'),
                        TextEntry::make('supplier.supplier_type')
                            ->hiddenLabel()
                            ->formatStateUsing(fn ($state) => 'Supplier for ' . $state->getLabel())
                            ->badge()
                            ->icon(Heroicon::OutlinedTag)
                            ->color('info'),
                        TextEntry::make('supplier.owner_name')
                            ->label('Owner')
                            ->beforeLabel(Icon::make(Heroicon::OutlinedUserCircle)->color('gray'))
                            ->color('primary'),
                        TextEntry::make('supplier.email')
                            ->beforeLabel(Icon::make(Heroicon::OutlinedEnvelope)->color('gray'))
                            ->color('primary'),
                        TextEntry::make('supplier.mobile_number')
                            ->beforeLabel(Icon::make(Heroicon::OutlinedDevicePhoneMobile)->color('gray'))
                            ->prefix('+63 ')
                            ->color('primary'),
                        TextEntry::make('supplier.landline_number')
                            ->beforeLabel(Icon::make(Heroicon::OutlinedPhone)->color('gray'))
                            ->color('primary'),
                        TextEntry::make('supplier.website')
                            ->beforeLabel(Icon::make(Heroicon::OutlinedGlobeAlt)->color('gray'))
                            ->color('primary'),
                    ])
            ]);
    }
}
