<?php

namespace App\Filament\Resources\Suppliers\RelationManagers;

use App\Filament\Actions\SecureDeleteAction;
use App\Filament\Resources\Suppliers\Schemas\SupplierInfolist;
use App\Models\Barangay;
use App\Models\Municipality;
use App\Models\Province;
use Filament\Actions\Action;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class AddressesRelationManager extends RelationManager
{
    protected static string $relationship = 'addresses';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('line_1')
                    ->label('Address Line 1')
                    ->placeholder('e.g., 123 Main St')
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(2),
                TextInput::make('line_2')
                    ->label('Address Line 2')
                    ->placeholder('e.g., Suite 100')
                    ->maxLength(255)
                    ->columnSpan(2),
                Select::make('province_id')
                    ->searchable()
                    ->required()
                    ->options(fn () => Province::pluck('name', 'id')->toArray())
                    ->reactive()
                    ->afterStateUpdated(fn ($state, callable $set) => $set('municipality', null)),
                Select::make('municipality_id')
                    ->label('Municipality/City')
                    ->searchable()
                    ->required()
                    ->options(fn (callable $get) => $get('province_id') ? Municipality::where('province_id', $get('province_id'))->pluck('name', 'id')->toArray() : [])
                    ->reactive()
                    ->afterStateUpdated(fn ($state, callable $set) => $set('barangay_id', null)),
                Select::make('barangay_id')
                    ->label('Barangay')
                    ->searchable()
                    ->required()
                    ->options(fn (callable $get) => $get('municipality_id') ? Barangay::where('municipality_id', $get('municipality_id'))->pluck('name', 'id')->toArray() : []),
                TextInput::make('country')
                    ->default('Philippines')
                    ->label('Country')
                    ->placeholder('e.g., Philippines')
                    ->required()
                    ->maxLength(255),
                TextInput::make('zip_code')
                    ->label('ZIP Code')
                    ->placeholder('e.g., 8000')
                    ->required()
                    ->maxLength(10),
            ]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('ID'),
                TextEntry::make('province.name'),
                TextEntry::make('municipality.name'),
                TextEntry::make('barangay.name'),
                TextEntry::make('line_1'),
                TextEntry::make('line_2'),
                TextEntry::make('country'),
                TextEntry::make('zip_code'),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('line_1')
            ->description('This table displays the registered addresses of the selected supplier, including main office, branch, and other relevant locations. You may also add or edit addresses as needed.')
            ->columns([
                TextColumn::make('line_1')
                    ->label('Address')
                    ->formatStateUsing(function ($state, $record) {
                        $parts = [$state];
                        if (!empty($record->line_2)) {
                            $parts[] = $record->line_2;
                        }
                        $parts[] = $record->barangay->name;
                        $parts[] = $record->municipality->name;
                        $parts[] = $record->province->name;
                        $parts[] = $record->zip_code;
                        $parts[] = $record->country;
                        return implode(', ', $parts);
                    })
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->modalSubmitAction(fn (Action $action) => $action->label('Create Address')->icon(Heroicon::OutlinedPlusCircle))
                    ->modalCancelAction(fn (Action $action) => $action->label('Cancel')->icon(Heroicon::XMark))
                    ->slideOver()
                    ->createAnother(false)
                    ->modalHeading('Create new address')
                    ->modalDescription('Fill out the form below to create a new address.')
                    ->label('Add Address')
                    ->icon(Heroicon::OutlinedPlusCircle),
                // AssociateAction::make(),
            ])
            ->recordActions([
                // ViewAction::make(),
                EditAction::make()
                    ->modalSubmitAction(fn (Action $action) => $action->label('Update Address')->icon(Heroicon::OutlinedPlusCircle))
                    ->modalCancelAction(fn (Action $action) => $action->label('Cancel')->icon(Heroicon::XMark))
                    ->slideOver()
                    ->modalHeading('Edit Address')
                    ->modalDescription('Fill out the form below to edit this address.'),
                // DissociateAction::make(),
                // DeleteAction::make(),
                SecureDeleteAction::make()
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
