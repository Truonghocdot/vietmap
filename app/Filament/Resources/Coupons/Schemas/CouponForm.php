<?php

namespace App\Filament\Resources\Coupons\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CouponForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thong tin ma giam gia')
                    ->schema([
                        TextInput::make('code')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('description')
                            ->maxLength(255),
                        Select::make('discount_type')
                            ->options([
                                'percent' => 'percent',
                                'fixed' => 'fixed',
                            ])
                            ->required(),
                        TextInput::make('discount_value')
                            ->numeric()
                            ->required(),
                        TextInput::make('min_order_amount')
                            ->numeric()
                            ->default(0),
                        TextInput::make('max_discount_amount')
                            ->numeric(),
                        TextInput::make('max_uses')
                            ->numeric(),
                        TextInput::make('used_count')
                            ->numeric()
                            ->default(0),
                        DateTimePicker::make('starts_at'),
                        DateTimePicker::make('ends_at'),
                        Toggle::make('is_active')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }
}
