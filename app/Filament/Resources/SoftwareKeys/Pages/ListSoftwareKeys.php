<?php

namespace App\Filament\Resources\SoftwareKeys\Pages;

use App\Filament\Resources\SoftwareKeys\SoftwareKeyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSoftwareKeys extends ListRecords
{
    protected static string $resource = SoftwareKeyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
