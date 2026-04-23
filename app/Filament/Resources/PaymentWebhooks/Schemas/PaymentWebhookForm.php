<?php

namespace App\Filament\Resources\PaymentWebhooks\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PaymentWebhookForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thong tin webhook')
                    ->schema([
                        TextInput::make('provider')
                            ->disabled(),
                        TextInput::make('provider_transaction_id')
                            ->disabled(),
                        TextInput::make('matched_order_number')
                            ->disabled(),
                        TextInput::make('is_valid')
                            ->disabled(),
                        TextInput::make('processed_at')
                            ->disabled()
                            ->formatStateUsing(fn (mixed $state): string => $state ? (string) $state : ''),
                    ])
                    ->columns(2),
                Section::make('Payload')
                    ->schema([
                        Placeholder::make('payload_preview')
                            ->label('Payload')
                            ->content(fn ($record): string => json_encode($record?->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?: '{}'),
                        Placeholder::make('headers_preview')
                            ->label('Headers')
                            ->content(fn ($record): string => json_encode($record?->headers, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?: '{}'),
                    ]),
            ]);
    }
}
