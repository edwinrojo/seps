<?php

namespace App\Livewire;

use App\Enums\UserRole;
use App\Filament\Resources\Suppliers\Actions\AttachDocument;
use App\Filament\Resources\Suppliers\Schemas\SupplierDocumentsColumns;
use App\Models\Document;
use App\Models\Supplier;
use Asmit\FilamentUpload\Enums\PdfViewFit;
use Asmit\FilamentUpload\Forms\Components\AdvancedFileUpload;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Components\Grid;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Hugomyb\FilamentMediaAction\Actions\MediaAction;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class SupplierDocumentsTable extends Component implements HasTable, HasForms, HasActions
{
    use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithActions;

    public Supplier $supplier;

    public function render()
    {
        return view('livewire.supplier-documents-table');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                // display all documents instead of attachments
                $procurement_type = $this->supplier->supplier_type->value;
                return Document::where('procurement_type', 'LIKE', '%' . $procurement_type . '%');
            })
            ->columns(SupplierDocumentsColumns::get($this->supplier))
            ->filters([
                // ...
            ])
            ->recordActions([
                MediaAction::make('file_path')
                    ->hidden(fn (Document $record) => !$this->supplier->attachments()->where('document_id', $record->id)->exists())
                    ->icon(Heroicon::OutlinedPaperClip)
                    ->color('info')
                    ->label('View Document')
                    ->modalWidth(Width::SevenExtraLarge)
                    ->mediaType('pdf')
                    ->media(function (Document $record) {
                        $attachment = $this->supplier->attachments()->where('document_id', $record->id)->first();
                        return $attachment ? '/' . $attachment->file_path : null;
                    }),
                Action::make('attach')
                    ->label(fn (Document $record): string => $this->supplier->attachments()->where('document_id', $record->id)->exists() ? 'Update Document' : 'Attach Document')
                    ->modal()
                    ->modalWidth(Width::SevenExtraLarge)
                    ->modalHeading(fn (Document $record): string => "Attach " . $record->title)
                    ->modalDescription('Select a scanned PDF document')
                    ->modalSubmitAction(fn (Action $action) => $action->label('Save Document')->icon(Heroicon::OutlinedPlusCircle))
                    ->modalCancelAction(fn (Action $action) => $action->label('Cancel')->icon(Heroicon::XMark))
                    ->modalIcon(Heroicon::Link)
                    ->icon(Heroicon::Link)
                    ->modalAlignment(Alignment::Center)
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                AdvancedFileUpload::make('file_path')
                                    ->getUploadedFileNameForStorageUsing(
                                        fn (TemporaryUploadedFile $file): string => (string) str($file->getClientOriginalName())
                                            ->prepend((string) now()->format('YmdHis') . '_')
                                    )
                                    ->afterStateUpdated(function (callable $set, $state) {
                                        if ($state instanceof TemporaryUploadedFile) {
                                            $set('file_size', $state->getSize());
                                        }
                                    })
                                    ->required()
                                    ->columnSpan(2)
                                    ->acceptedFileTypes(['application/pdf'])
                                    ->directory('attachments')
                                    ->disk('public')
                                    ->hiddenLabel()
                                    ->openable()
                                    ->downloadable()
                                    ->moveFiles()
                                    ->minSize(1)
                                    ->maxSize(5120)
                                    ->validationMessages([
                                        'max' => 'File size must not exceed 5MB.'
                                    ])
                                    ->pdfDisplayPage(1)
                                    ->pdfToolbar(true)
                                    ->pdfZoomLevel(150)
                                    ->pdfPreviewHeight(600)
                                    ->pdfFitType(PdfViewFit::FITV)
                                    ->pdfNavPanes(true),
                                DatePicker::make('validity_date')
                                    ->label('Validity Date')
                                    ->belowLabel('Leave blank if not applicable')
                                    ->placeholder('Select validity date')
                                    ->native(false)
                                    ->displayFormat('F d, Y')
                                    ->firstDayOfWeek(1),
                                Hidden::make('file_size')
                            ])
                    ])
                    ->hidden(request()->user()->role !== UserRole::Supplier)
                    ->action(fn (Document $record, $data) => AttachDocument::handle($this->supplier, $record, $data))
                    ->color('primary'),
            ])
            ->toolbarActions([
                // ...
            ]);
    }
}
