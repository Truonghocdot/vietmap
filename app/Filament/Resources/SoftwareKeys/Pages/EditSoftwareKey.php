<?php

namespace App\Filament\Resources\SoftwareKeys\Pages;

use App\Filament\Resources\SoftwareKeys\Schemas\SoftwareKeyForm;
use App\Filament\Resources\SoftwareKeys\SoftwareKeyResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSoftwareKey extends EditRecord
{
    protected static string $resource = SoftwareKeyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return SoftwareKeyForm::clearSensitiveData($data);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return SoftwareKeyForm::sanitizeSensitiveData($data);
    }
}
