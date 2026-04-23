<?php

namespace App\Filament\Resources\SoftwareKeys\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SoftwareKeyForm
{
    private const array SENSITIVE_FIELDS = [
        'username',
        'password',
        'license_key',
    ];

    private const string SENSITIVE_FIELD_HELPER_TEXT = 'Gia tri hien tai duoc an. De trong de giu nguyen, nhap moi de ma hoa va ghi de.';

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thong tin key')
                    ->schema([
                        Select::make('package_id')
                            ->relationship('package', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        TextInput::make('reference')
                            ->maxLength(255)
                            ->helperText('Ma noi bo de tim key nhanh'),
                        TextInput::make('label')
                            ->maxLength(255),
                        Select::make('status')
                            ->options([
                                'available' => 'available',
                                'reserved' => 'reserved',
                                'delivered' => 'delivered',
                                'disabled' => 'disabled',
                            ])
                            ->required(),
                        Toggle::make('is_active')
                            ->default(true),
                    ])
                    ->columns(2),
                Section::make('Thong tin giao khach')
                    ->schema([
                        TextInput::make('username')
                            ->maxLength(255)
                            ->autocomplete('off')
                            ->helperText(self::SENSITIVE_FIELD_HELPER_TEXT),
                        TextInput::make('password')
                            ->maxLength(255)
                            ->password()
                            ->revealable(false)
                            ->autocomplete('new-password')
                            ->helperText(self::SENSITIVE_FIELD_HELPER_TEXT),
                        Textarea::make('license_key')
                            ->rows(3)
                            ->helperText(self::SENSITIVE_FIELD_HELPER_TEXT),
                        Textarea::make('notes')
                            ->rows(3),
                        KeyValue::make('extra_data')
                            ->keyLabel('Ten truong')
                            ->valueLabel('Gia tri'),
                    ]),
            ]);
    }

    public static function clearSensitiveData(array $data): array
    {
        foreach (self::SENSITIVE_FIELDS as $field) {
            if (array_key_exists($field, $data)) {
                $data[$field] = null;
            }
        }

        return $data;
    }

    public static function sanitizeSensitiveData(array $data): array
    {
        foreach (self::SENSITIVE_FIELDS as $field) {
            if (! array_key_exists($field, $data)) {
                continue;
            }

            if (blank($data[$field])) {
                unset($data[$field]);

                continue;
            }

            if (is_string($data[$field])) {
                $data[$field] = trim($data[$field]);
            }
        }

        return $data;
    }
}
