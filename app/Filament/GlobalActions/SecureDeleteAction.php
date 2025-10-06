<?php

namespace App\Filament\GlobalActions;

use Closure;
use Dotenv\Exception\ValidationException;
use Filament\Actions\DeleteAction;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\TextInput;

class SecureDeleteAction extends DeleteAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->schema([
                TextInput::make('password')
                    ->password()
                    ->required()
                    ->rules([
                        fn (): Closure => function (string $attribute, $value, Closure $fail) {
                            if (! Hash::check($value, request()->user()->getAuthPassword())) {
                                $fail('The password you entered is incorrect.');
                            }
                        }
                    ])
                    ->label('Enter your password to confirm deletion')
            ]);
    }
}
