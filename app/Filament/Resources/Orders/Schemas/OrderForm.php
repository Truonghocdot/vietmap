<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Chi tiet don hang')
                    ->schema([
                        TextInput::make('order_number')
                            ->disabled(),
                        TextInput::make('package.name')
                            ->label('Goi')
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('customer_email')
                            ->disabled(),
                        TextInput::make('amount')
                            ->disabled(),
                        TextInput::make('payment_status')
                            ->disabled(),
                        TextInput::make('fulfillment_status')
                            ->disabled(),
                        TextInput::make('payment_gateway')
                            ->disabled(),
                        TextInput::make('provider_transaction_id')
                            ->disabled(),
                        TextInput::make('paid_at')
                            ->disabled()
                            ->formatStateUsing(fn (mixed $state): string => $state ? (string) $state : ''),
                        TextInput::make('fulfilled_at')
                            ->disabled()
                            ->formatStateUsing(fn (mixed $state): string => $state ? (string) $state : ''),
                    ])
                    ->columns(2),
                Section::make('Ghi chu')
                    ->schema([
                        Textarea::make('notes')
                            ->rows(4),
                    ]),
            ]);
    }
}
