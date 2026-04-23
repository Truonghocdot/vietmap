<?php

namespace App\Filament\Resources\SoftwareKeys\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SoftwareKeysTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('package.name')
                    ->label('Goi')
                    ->searchable(),
                TextColumn::make('reference')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('label')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('order.order_number')
                    ->label('Don gan')
                    ->toggleable(),
                TextColumn::make('delivered_at')
                    ->dateTime('H:i d/m/Y')
                    ->toggleable(),
                IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'available' => 'available',
                        'reserved' => 'reserved',
                        'delivered' => 'delivered',
                        'disabled' => 'disabled',
                    ]),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
