<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\AvatarProviders\UiAvatarsProvider;
use Filament\Facades\Filament;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar')
                    ->defaultImageUrl(function ($record) {
                        return $record->getFilamentAvatarUrl();
                    })
                    ->state(function ($record) {
                        return $record->getFilamentAvatarUrl();
                    })
                    ->grow(false)
                    ->label('')
                    ->disk('public')
                    ->visibility('public')
                    ->imageSize(50)
                    ->width('1px')
                    ->circular(),
                TextColumn::make('name')
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query
                            ->orderBy('last_name', $direction)
                            ->orderBy('first_name', $direction);
                    })
                    ->weight(FontWeight::Bold)
                    ->description(fn ($record) => ucfirst($record->role))
                    ->searchable(),
                TextColumn::make('supplier.business_name')
                    ->icon(Heroicon::BuildingStorefront)
                    ->label('Business Name')
                    ->placeholder(function ($record) {
                        return $record->role === 'supplier' ? 'Not available' : 'This user is not a supplier';
                    })
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Contact Information')
                    ->getStateUsing(fn ($record, $table): array => [
                        TextColumn::make('email')->record($record)->icon(Heroicon::Envelope)->table($table)->inline(),
                        TextColumn::make('contact_number')->record($record)->icon(Heroicon::Phone)->table($table)->inline()->prefix('+63 '),
                    ])
                    ->listWithLineBreaks(),
                TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->icon(function ($record) {
                        return match ($record->status) {
                            'active' => Heroicon::OutlinedCheckCircle,
                            'inactive' => Heroicon::OutlinedXCircle,
                            'pending' => Heroicon::OutlinedExclamationCircle,
                        };
                    })
                    ->colors([
                        'success' => 'active',
                        'danger' => 'inactive',
                        'warning' => 'pending',
                    ])
                    ->searchable(),
                IconColumn::make('has_email_authentication')
                    ->label('2FA')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make()->extraAttributes(['style' => 'visibility: hidden;']),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
