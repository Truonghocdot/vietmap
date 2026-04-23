<?php

namespace App\Filament\Resources\Orders\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('package.name')
                    ->label('Goi')
                    ->searchable(),
                TextColumn::make('customer_email')
                    ->label('Email')
                    ->toggleable(),
                TextColumn::make('payment_status')
                    ->badge(),
                TextColumn::make('fulfillment_status')
                    ->badge(),
                TextColumn::make('amount')
                    ->label('So tien')
                    ->formatStateUsing(fn (mixed $state): string => number_format((int) $state, 0, ',', '.') . 'đ')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime('H:i d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('payment_status')
                    ->options([
                        'pending' => 'pending',
                        'paid' => 'paid',
                        'expired' => 'expired',
                        'failed' => 'failed',
                    ]),
                SelectFilter::make('fulfillment_status')
                    ->options([
                        'pending' => 'pending',
                        'fulfilled' => 'fulfilled',
                        'failed' => 'failed',
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
