<?php

namespace App\Livewire;

use App\Models\ValidationPurpose;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;

class ListValidationPurpose extends Component implements HasTable, HasForms, HasActions
{
    use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithActions;

    public function render()
    {
        return view('livewire.list-validation-purpose');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(ValidationPurpose::query())
            ->heading('Validation Purposes')
            ->description('List of all validation purposes available in the system.')
            ->columns([
                TextColumn::make('purpose')
                    ->label('Purpose')
                    ->color('primary')
                    ->searchable()
                    ->description(fn (ValidationPurpose $record) => $record->description)
                    ->weight(FontWeight::Bold),
                IconColumn::make('is_iv')
                    ->label('Supplier Status Validation')
                    ->boolean()
                    ->trueIcon(Heroicon::CheckCircle)
                    ->falseIcon(Heroicon::XCircle)
                    ->trueColor('success')
                    ->falseColor('danger'),
            ])
            ->filters([
                // ...
            ])
            ->recordActions([
                Action::make('edit')
                    ->label('Edit')
                    ->icon(Heroicon::PencilSquare)
                    ->button()
                    ->modal()
                    ->modalHeading('Edit Validation Purpose')
                    ->modalDescription('Update the details of the validation purpose below.')
                    ->modalSubmitAction(fn (Action $action) => $action->label('Save Changes')->icon(Heroicon::Check))
                    ->modalCancelAction(fn (Action $action) => $action->label('Cancel')->icon(Heroicon::XMark))
                    ->slideOver()
                    ->modalWidth(Width::ExtraLarge)
                    ->fillForm(function ($record) {
                        return [
                            'purpose' => $record->purpose,
                            'description' => $record->description,
                            'is_iv' => $record->is_iv,
                        ];
                    })
                    ->schema([
                        TextInput::make('purpose')
                            ->label('Validation Purpose')
                            ->placeholder('e.g., Document Verification, Physical Office Visit')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('description')
                            ->placeholder('Provide a description.')
                            ->label('Description'),
                        Checkbox::make('is_iv')
                            ->label('Supplier Status Validation')
                            ->helperText('Please check if this purpose is included in the supplier status validation for the list of eligible suppliers.')
                    ])
                    ->action(function (ValidationPurpose $record, array $data) {
                        $record->update($data);

                        Notification::make()
                            ->title('Record Updated')
                            ->success()
                            ->body('The validation purpose has been updated successfully.')
                            ->send();
                    })
            ])
            ->toolbarActions([
                Action::make('create')
                    ->label('Add Validation Purpose')
                    ->button()
                    ->icon('heroicon-o-plus')
                    ->modal()
                    ->modalHeading('Create New Validation Purpose')
                    ->modalDescription('Fill out the form below to add a new validation purpose.')
                    ->modalSubmitAction(fn (Action $action) => $action->label('Save Changes')->icon(Heroicon::OutlinedPlusCircle))
                    ->modalCancelAction(fn (Action $action) => $action->label('Cancel')->icon(Heroicon::XMark))
                    ->slideOver()
                    ->modalWidth(Width::ExtraLarge)
                    ->schema([
                        TextInput::make('purpose')
                            ->label('Validation Purpose')
                            ->placeholder('e.g., Document Verification, Physical Office Visit')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('description')
                            ->placeholder('Provide a description.')
                            ->label('Description'),
                        Checkbox::make('is_iv')
                            ->label('Supplier Status Validation')
                            ->helperText('Please check if this purpose is included in the supplier status validation for the list of eligible suppliers.')
                    ])
                    ->action(function (array $data) {
                        ValidationPurpose::create($data);

                        Notification::make()
                            ->title('Record Created')
                            ->success()
                            ->body('The validation purpose has been created successfully.')
                            ->send();
                    }),
            ]);
    }
}
