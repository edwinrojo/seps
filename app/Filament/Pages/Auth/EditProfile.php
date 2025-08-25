<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Filament\Schemas\Components\Section;

class EditProfile extends BaseEditProfile
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Profile Information')
                    ->description('Update your profile information')
                    ->schema([
                        FileUpload::make('avatar')
                            ->disk('public')
                            ->avatar()
                            ->imageEditor()
                            ->circleCropper()
                            ->imageEditorAspectRatios([
                                null,
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->getUploadedFileNameForStorageUsing(
                                fn (TemporaryUploadedFile $file): string => (string) str($file->getClientOriginalName())
                                    ->prepend((string) now()->format('YmdHis') . '_')
                            )
                            ->directory('avatars')
                            ->openable()
                            ->downloadable()
                            ->moveFiles()
                            ->belowContent('This image will be used as your profile picture')
                            ->maxSize(2048),
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
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                        $this->getSaveFormAction()->formId('form'),
                    ]),
                    Section::make('Multi-Factor Authentication')
                        ->description('Enhance the security of your account')
                        ->schema([
                            $this->getMultiFactorAuthenticationContentComponent()
                        ]),
            ])->columns(2);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getFormContentComponent(),
                // Do NOT include the MFA component here
            ]);
    }

    protected function getFormActions(): array
    {
        return []; // Prevents default form actions from rendering
    }
}
