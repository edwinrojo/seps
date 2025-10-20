<?php

namespace App\Filament\Resources\Suppliers\Schemas;

use App\Helpers\DateColor;
use App\Models\Attachment;
use App\Models\Document;
use App\Models\Status;
use Carbon\Carbon;
use Filament\Schemas\Components\Html;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\HtmlString;

class SupplierDocumentsColumns
{
    public static function get($supplier): array
    {
        return [
            TextColumn::make('title')
                ->weight('bold')
                ->tooltip(fn (Document $record): string => $record->description)
                ->description(fn (Document $record): string => substr($record->description, 0, 70) . (strlen($record->description) > 70 ? '...' : ''))
                ->formatStateUsing(function ($record, $state): HtmlString {
                    return new HtmlString($state . ($record->is_required ? ' - <span class="font-normal" style="color: #c10007;">Required</span>' : ''));
                })
                ->searchable(),
            TextColumn::make('validity_date')
                ->state(fn (Document $record) => $supplier->attachments()->where('document_id', $record->id)->first()?->validity_date)
                ->badge()
                ->color(function (Document $record) use ($supplier) {
                    $attachment = $supplier->attachments()->where('document_id', $record->id)->first();
                    return DateColor::getColor($attachment?->validity_date);
                })
                ->label('Valid Until')
                ->date('F d, Y'),
            TextColumn::make('status')
                ->state(function (Document $record) use ($supplier) {
                    $attachment = $supplier->attachments()->where('document_id', $record->id)->first();
                    return Status::where('statusable_type', Attachment::class)
                        ->where('statusable_id', $attachment?->id)
                        ->latest()
                        ->first();
                })
                ->html()
                ->formatStateUsing(function ($state): HtmlString {
                    $formatted_datetime = $state?->status_date ? Carbon::parse($state->status_date)->format('F d, Y h:i A') : '';
                    return new HtmlString($state ? $state->status->getLabel() . '<br>' . $formatted_datetime : null);
                })
                ->tooltip(function (Document $record) use ($supplier) {
                    $attachment = $supplier->attachments()->where('document_id', $record->id)->first();
                    $latest_status = Status::where('statusable_type', Attachment::class)
                        ->where('statusable_id', $attachment?->id)
                        ->latest()
                        ->first();
                    return $latest_status ? $latest_status->remarks : 'No status available';
                })
                ->badge()
                ->color(fn ($state) => $state->status->getColor())
        ];
    }
}
