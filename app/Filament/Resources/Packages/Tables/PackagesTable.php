<?php

namespace App\Filament\Resources\Packages\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PackagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('duration_label')
                    ->label('Thoi han')
                    ->toggleable(),
                TextColumn::make('price')
                    ->label('Gia')
                    ->formatStateUsing(fn (mixed $state): string => number_format((int) $state, 0, ',', '.') . 'đ')
                    ->sortable(),
                TextColumn::make('compare_at_price')
                    ->label('Gia cu')
                    ->formatStateUsing(fn (mixed $state): string => $state ? number_format((int) $state, 0, ',', '.') . 'đ' : '-')
                    ->toggleable(),
                TextColumn::make('software_keys_count')
                    ->counts('softwareKeys')
                    ->label('So key'),
                IconColumn::make('is_active')
                    ->label('Bat')
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
