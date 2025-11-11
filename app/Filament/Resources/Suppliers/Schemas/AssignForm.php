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
                ->helperText('Assign a user account to this supplier. Only user accounts without an existing supplier assignment are shown above. You may create a new user account if needed.')
                ->prefixIcon(Heroicon::User)
                ->preload()
                ->options(fn ($record) => User::doesntHave('supplier')->where('role', 'supplier')->orWhere('id', $record->user_id)->pluck('name', 'id'))
                ->options(function ($record) {
                        $users = User::doesntHave('supplier')->where('role', 'supplier')->orWhere('id', $record->user_id)->get();
                        // add description to next line via HtmlString
                        return $users->pluck('name', 'id')->mapWithKeys(function ($name, $id) use ($users) {
                            $email = $users->where('id', $id)->first()->email;
                            return [$id => '<b>'.$name.'</b>' . ($email ? "<span style='display: block;' class='text-sm text-gray-500'>$email</span>" : '')];
                        });
                    })
                ->allowHtml()
                ->afterLabel('Options are limited to supplier-role accounts.')
                ->required(),
        ];
    }
}
