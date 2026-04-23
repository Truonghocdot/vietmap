<?php

namespace App\Filament\Resources\Packages\Schemas;

use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PackageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thong tin goi')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('short_name')
                            ->maxLength(255),
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('service_code')
                            ->default('vietmap')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('duration_label')
                            ->helperText('Vi du: 1 ngay, 30 ngay, 90 ngay')
                            ->maxLength(255),
                        TextInput::make('duration_hours')
                            ->numeric()
                            ->required()
                            ->minValue(1),
                        TextInput::make('sort_order')
                            ->numeric()
                            ->default(0),
                        Toggle::make('is_active')
                            ->default(true),
                    ])
                    ->columns(2),
                Section::make('Gia ban')
                    ->schema([
                        TextInput::make('price')
                            ->numeric()
                            ->required()
                            ->prefix('VND'),
                        TextInput::make('compare_at_price')
                            ->numeric()
                            ->prefix('VND'),
                        TextInput::make('badge')
                            ->maxLength(255)
                            ->helperText('Vi du: best-value, hot, khuyen mai'),
                        TextInput::make('badge_color')
                            ->maxLength(255)
                            ->helperText('Chi de ghi chu cho admin'),
                    ])
                    ->columns(2),
                Section::make('Noi dung hien thi')
                    ->schema([
                        Textarea::make('description')
                            ->rows(3),
                        Textarea::make('checkout_notes')
                            ->rows(3)
                            ->helperText('Noi dung can hien thi them o trang xac nhan/thanh toan'),
                        TagsInput::make('features')
                            ->separator(',')
                            ->helperText('Nhap cac tinh nang ngan gon cho goi nay'),
                    ]),
            ]);
    }
}
