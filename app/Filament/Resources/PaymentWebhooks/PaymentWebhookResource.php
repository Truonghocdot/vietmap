<?php

namespace App\Filament\Resources\PaymentWebhooks;

use App\Filament\Resources\PaymentWebhooks\Pages\CreatePaymentWebhook;
use App\Filament\Resources\PaymentWebhooks\Pages\EditPaymentWebhook;
use App\Filament\Resources\PaymentWebhooks\Pages\ListPaymentWebhooks;
use App\Filament\Resources\PaymentWebhooks\Schemas\PaymentWebhookForm;
use App\Filament\Resources\PaymentWebhooks\Tables\PaymentWebhooksTable;
use App\Models\PaymentWebhook;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PaymentWebhookResource extends Resource
{
    protected static ?string $model = PaymentWebhook::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Webhook logs';

    protected static string|\UnitEnum|null $navigationGroup = 'System';

    public static function form(Schema $schema): Schema
    {
        return PaymentWebhookForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PaymentWebhooksTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPaymentWebhooks::route('/'),
            'edit' => EditPaymentWebhook::route('/{record}/edit'),
        ];
    }
}
