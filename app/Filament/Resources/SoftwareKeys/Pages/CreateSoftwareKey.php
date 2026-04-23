<?php

namespace App\Filament\Resources\SoftwareKeys\Pages;

use App\Filament\Resources\SoftwareKeys\Schemas\SoftwareKeyForm;
use App\Filament\Resources\SoftwareKeys\SoftwareKeyResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSoftwareKey extends CreateRecord
{
    protected static string $resource = SoftwareKeyResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return SoftwareKeyForm::sanitizeSensitiveData($data);
    }
}
