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
                    ->helperText('Enter your given name as it appears on official documents.')
                    ->required()
                    ->maxLength(255),
                TextInput::make('last_name')
                    ->label('Last Name')
                    ->helperText('Enter your family name as it appears on official documents.')
                    ->required()
                    ->maxLength(255),
                TextInput::make('middle_name')
                    ->label('Middle Name')
                    ->helperText('Enter your middle name as it appears on official documents. This field is optional.'),
                TextInput::make('suffix')
                    ->label('Suffix')
                    ->helperText('Enter any suffix associated with your name, such as Jr., Sr., III, etc. This field is optional.'),
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
                $this->getPasswordFormComponent()->helperText('Create a strong password to secure your account. It should be at least 8 characters long.'),
                $this->getPasswordConfirmationFormComponent()->helperText('Re-enter your password to confirm it matches.'),
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
