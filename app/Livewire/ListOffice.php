<?php

namespace App\Livewire;

use App\Models\Office;
use Dom\Text;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;

class ListOffice extends Component implements HasTable, HasForms, HasActions
{
    use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithActions;

    public function render()
    {
        return view('livewire.list-office');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Office::query())
            ->heading('Offices')
            ->description('List of all offices available in the system.')
            ->columns([
                TextColumn::make('acronym')
                    ->label('Title')
                    ->color('primary')
                    ->searchable()
                    ->description(fn (Office $record) => $record->title)
                    ->weight(FontWeight::Bold),
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
                    ->modalHeading('Edit Office')
                    ->modalDescription('Update the details of the office below.')
                    ->modalSubmitAction(fn (Action $action) => $action->label('Save Changes')->icon(Heroicon::Check))
                    ->modalCancelAction(fn (Action $action) => $action->label('Cancel')->icon(Heroicon::XMark))
                    ->slideOver()
                    ->modalWidth(Width::ExtraLarge)
                    ->fillForm(function ($record) {
                        return [
                            'title' => $record->title,
                            'acronym' => $record->acronym,
                        ];
                    })
                    ->schema([
                        TextInput::make('title')
                            ->label('Office Title')
                            ->placeholder('e.g., Provincial Budget Office')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('acronym')
                            ->placeholder('Provide an acronym.')
                            ->required()
                            ->label('Office Acronym')
                    ])
                    ->action(function (Office $record, array $data) {
                        $record->update($data);

                        Notification::make()
                            ->title('Record Updated')
                            ->success()
                            ->body('The office has been updated successfully.')
                            ->send();
                    })
            ])
            ->toolbarActions([
                Action::make('create')
                    ->label('Add Office')
                    ->button()
                    ->icon('heroicon-o-plus')
                    ->modal()
                    ->modalHeading('Create New Office')
                    ->modalDescription('Fill out the form below to add a new office.')
                    ->modalSubmitAction(fn (Action $action) => $action->label('Save Changes')->icon(Heroicon::OutlinedPlusCircle))
                    ->modalCancelAction(fn (Action $action) => $action->label('Cancel')->icon(Heroicon::XMark))
                    ->slideOver()
                    ->modalWidth(Width::ExtraLarge)
                    ->schema([
                        TextInput::make('title')
                            ->label('Office Title')
                            ->placeholder('e.g., Provincial Budget Office')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('acronym')
                            ->placeholder('Provide an acronym.')
                            ->label('Office Acronym')
                    ])
                    ->action(function (array $data) {
                        Office::create($data);

                        Notification::make()
                            ->title('Record Created')
                            ->success()
                            ->body('The office has been created successfully.')
                            ->send();
                    }),
            ]);
    }
}
