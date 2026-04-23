<?php

namespace App\Filament\Resources\PaymentWebhooks\Pages;

use App\Filament\Resources\PaymentWebhooks\PaymentWebhookResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePaymentWebhook extends CreateRecord
{
    protected static string $resource = PaymentWebhookResource::class;
}
