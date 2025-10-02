<?php

namespace App\Filament\Resources\Attachments\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class AttachmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('supplier_id')
                    ->relationship('supplier', 'id')
                    ->required(),
                Select::make('document_id')
                    ->relationship('document', 'title'),
                Textarea::make('file_path')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('file_size')
                    ->required()
                    ->numeric(),
                DatePicker::make('validity_date')
                    ->required(),
            ]);
    }
}
