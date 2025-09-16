<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
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
            ->modifyQueryUsing(function (Builder $query) {
                $currentUserId = Filament::auth()->user()?->id;
                if ($currentUserId) {
                    $query->where('id', '!=', $currentUserId);
                }
                return $query;
            })
            ->columns([
                ImageColumn::make('avatar')
                    ->defaultImageUrl(function ($record) {
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
                    ->description(function ($record, $livewire) {
                        switch ($livewire->activeTab) {
                            case 'suppliers':
                                return $record->supplier?->business_name ?? 'No business profile';
                            case 'twgs':
                                return 'TWG for ' . $record->twg?->twg_type->getLabel();
                            case 'end-users':
                                return $record->endUser?->designation;
                            case 'all':
                                return $record->role->getLabel();
                            default:
                                return null;
                        }
                    })
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Contact Information')
                    ->getStateUsing(fn ($record, $table): array => [
                        TextColumn::make('email')->record($record)->icon(Heroicon::Envelope)->table($table)->inline(),
                        TextColumn::make('contact_number')->record($record)->icon(Heroicon::Phone)->table($table)->inline()->prefix('+63 '),
                    ])
                    ->listWithLineBreaks(),
                TextColumn::make('endUser.office.acronym')
                    ->label('Office')
                    ->searchable()
                    ->icon(Heroicon::BuildingLibrary)
                    ->hidden(fn ($livewire) => $livewire->activeTab !== 'end-users')
                    ->formatStateUsing(fn ($state) => $state ?? 'No office assigned'),
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
