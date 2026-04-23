<?php

namespace App\Filament\Resources\PaymentWebhooks\Pages;

use App\Filament\Resources\PaymentWebhooks\PaymentWebhookResource;
use Filament\Resources\Pages\EditRecord;

class EditPaymentWebhook extends EditRecord
{
    protected static string $resource = PaymentWebhookResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
