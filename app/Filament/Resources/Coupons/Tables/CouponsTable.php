<?php

namespace App\Filament\Resources\Coupons\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CouponsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('discount_type')
                    ->badge(),
                TextColumn::make('discount_value')
                    ->label('Gia tri'),
                TextColumn::make('used_count')
                    ->label('Da dung'),
                TextColumn::make('max_uses')
                    ->label('Gioi han'),
                TextColumn::make('starts_at')
                    ->dateTime('H:i d/m/Y')
                    ->toggleable(),
                TextColumn::make('ends_at')
                    ->dateTime('H:i d/m/Y')
                    ->toggleable(),
                IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->filters([
                //
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
