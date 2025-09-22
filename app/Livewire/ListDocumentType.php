<?php

namespace App\Livewire;

use App\Models\DocumentType;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Textarea;
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
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ListDocumentType extends Component implements HasTable, HasForms, HasActions
{
    use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithActions;

    public function render(): View
    {
        return view('livewire.list-document-type');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(DocumentType::query())
            ->heading('Document Types')
            ->description('List of all document types available in the system.')
            ->columns([
                TextColumn::make('title')
                    ->label('Title')
                    ->color('primary')
                    ->searchable()
                    ->weight(FontWeight::Bold),
                TextColumn::make('description')
                    ->label('Description')
                    ->wrap()
                    ->color('secondary'),
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
                    ->modalHeading('Edit Document Type')
                    ->modalDescription('Update the details of the document type below.')
                    ->modalSubmitAction(fn (Action $action) => $action->label('Save Changes')->icon(Heroicon::Check))
                    ->modalCancelAction(fn (Action $action) => $action->label('Cancel')->icon(Heroicon::XMark))
                    ->slideOver()
                    ->modalWidth(Width::ExtraLarge)
                    ->fillForm(function ($record) {
                        return [
                            'title' => $record->title,
                            'description' => $record->description,
                        ];
                    })
                    ->schema([
                        TextInput::make('title')
                            ->label('Document Type Title')
                            ->placeholder('e.g., Legal Document, Financial Document')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('description')
                            ->placeholder('Provide a brief description of the document type.')
                            ->label('Document Type Description')
                    ])
                    ->action(function (DocumentType $record, array $data) {
                        $record->update($data);

                        Notification::make()
                            ->title('Record Updated')
                            ->success()
                            ->body('The document type has been updated successfully.')
                            ->send();
                    })
            ])
            ->toolbarActions([
                Action::make('create')
                    ->label('Add Document Type')
                    ->button()
                    ->icon('heroicon-o-plus')
                    ->modal()
                    ->modalHeading('Create New Document Type')
                    ->modalDescription('Fill out the form below to add a new document type.')
                    ->modalSubmitAction(fn (Action $action) => $action->label('Save Changes')->icon(Heroicon::OutlinedPlusCircle))
                    ->modalCancelAction(fn (Action $action) => $action->label('Cancel')->icon(Heroicon::XMark))
                    ->slideOver()
                    ->modalWidth(Width::ExtraLarge)
                    ->schema([
                        TextInput::make('title')
                            ->label('Document Type Title')
                            ->placeholder('e.g., Legal Document, Financial Document')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('description')
                            ->placeholder('Provide a brief description of the document type.')
                            ->label('Document Type Description')
                    ])
                    ->action(function (array $data) {
                        DocumentType::create($data);

                        Notification::make()
                            ->title('Record Created')
                            ->success()
                            ->body('The document type has been created successfully.')
                            ->send();
                    }),
            ]);
    }
}
