<?php

namespace App\Filament\Resources\Suppliers\Schemas;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Support\Icons\Heroicon;

class AssignForm
{
    public static function configure(): array
    {
        return [
            Select::make('user_id')
                ->label('User Account')
                ->placeholder('Select a user account')
                ->native(false)
                ->searchable()
                ->searchPrompt('Search for a user account...')
                ->prefixIcon(Heroicon::User)
                ->preload()
                ->options(fn ($record) => User::doesntHave('supplier')->where('role', 'supplier')->orWhere('id', $record->user_id)->pluck('name', 'id'))
                ->afterLabel('Options are limited to supplier-role accounts.')
                ->required(),
        ];
    }
}
