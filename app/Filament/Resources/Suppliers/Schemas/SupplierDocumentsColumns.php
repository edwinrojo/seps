<?php

namespace App\Filament\Resources\Suppliers\Schemas;

use App\Models\Attachment;
use App\Models\Document;
use App\Models\Status;
use Filament\Tables\Columns\TextColumn;

class SupplierDocumentsColumns
{
    public static function get($supplier): array
    {
        return [
            TextColumn::make('title')
                ->color('primary')
                ->weight('bold')
                ->tooltip(fn (Document $record): string => $record->description)
                ->description(fn (Document $record): string => substr($record->description, 0, 70) . (strlen($record->description) > 70 ? '...' : ''))
                ->searchable(),
            TextColumn::make('validity_date')
                ->state(fn (Document $record) => $supplier->attachments()->where('document_id', $record->id)->first()?->validity_date)
                ->badge()
                ->color(function (Document $record) use ($supplier) {
                    $attachment = $supplier->attachments()->where('document_id', $record->id)->first();
                    if (!$attachment || !$attachment->validity_date) {
                        return 'gray';
                    }
                    $validity = \Carbon\Carbon::parse($attachment->validity_date);
                    if ($validity->isPast()) {
                        return 'danger';
                    } elseif ($validity->isToday() || $validity->isTomorrow() || $validity->isNextWeek()) {
                        return 'warning';
                    } else {
                        return 'success';
                    }
                })
                ->label('Valid Until')
                ->date('F d, Y'),
            TextColumn::make('status')
                ->state(function (Document $record) use ($supplier) {
                    $attachment = $supplier->attachments()->where('document_id', $record->id)->first();
                    $latest_status = Status::where('statusable_type', Attachment::class)
                        ->where('statusable_id', $attachment?->id)
                        ->latest()
                        ->first();
                    return $latest_status ? $latest_status->status->getLabel() : null;
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
                ->color(function ($state) {
                    return match ($state) {
                        'Approved' => 'success',
                        'Pending Review' => 'warning',
                        'Rejected' => 'danger',
                        default => 'secondary',
                    };
                })

        ];
    }
}
