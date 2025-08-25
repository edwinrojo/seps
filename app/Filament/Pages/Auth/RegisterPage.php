<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Register as BaseRegister;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Support\Enums\Width;

class RegisterPage extends BaseRegister
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('first_name')
                    ->label('First Name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('last_name')
                    ->label('Last Name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('middle_name')
                    ->label('Middle Name'),
                TextInput::make('suffix')
                    ->label('Suffix'),
                TextInput::make('contact_number')
                    ->label('Contact Number')
                    ->prefix('+63')
                    ->mask('999-999-9999')
                    ->placeholder('912-345-6789')
                    ->required()
                    ->belowContent('This will be used to contact you for any important inquiries')
                    ->maxLength(255),
                $this->getEmailFormComponent()
                    ->belowContent('Use your official email address for verification purposes.'),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ])->columns(2);
    }

    protected function mutateFormDataBeforeRegister(array $data): array
    {
        $data['role'] = 'supplier';
        return $data;
    }

    public function getMaxContentWidth(): Width
    {
        return Width::ScreenLarge;
    }
}
