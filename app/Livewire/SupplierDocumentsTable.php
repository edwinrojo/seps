<?php

namespace App\Livewire;

use App\Filament\Resources\Suppliers\Schemas\SupplierDocumentsColumns;
use App\Models\Document;
use App\Models\Supplier;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Hugomyb\FilamentMediaAction\Actions\MediaAction;
use Livewire\Component;

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
            ])
            ->toolbarActions([
                // ...
            ]);
    }
}
