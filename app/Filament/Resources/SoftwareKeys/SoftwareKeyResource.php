<?php

namespace App\Filament\Resources\SoftwareKeys;

use App\Filament\Resources\SoftwareKeys\Pages\CreateSoftwareKey;
use App\Filament\Resources\SoftwareKeys\Pages\EditSoftwareKey;
use App\Filament\Resources\SoftwareKeys\Pages\ListSoftwareKeys;
use App\Filament\Resources\SoftwareKeys\Schemas\SoftwareKeyForm;
use App\Filament\Resources\SoftwareKeys\Tables\SoftwareKeysTable;
use App\Models\SoftwareKey;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SoftwareKeyResource extends Resource
{
    protected static ?string $model = SoftwareKey::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Kho key';

    protected static ?string $modelLabel = 'Key';

    protected static ?string $pluralModelLabel = 'Kho key';

    protected static string|\UnitEnum|null $navigationGroup = 'Catalog';

    public static function form(Schema $schema): Schema
    {
        return SoftwareKeyForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SoftwareKeysTable::configure($table);
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
            'index' => ListSoftwareKeys::route('/'),
            'create' => CreateSoftwareKey::route('/create'),
            'edit' => EditSoftwareKey::route('/{record}/edit'),
        ];
    }
}
